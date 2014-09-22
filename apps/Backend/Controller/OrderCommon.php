<?php

namespace Backend\Controller;

use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Db\Type\DateTime;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use Flywheel\Event\Event;
use Flywheel\Exception;
use SeuDo\Event\Order;
use \SeuDo\SFS\Client;
use \SeuDo\Logger;

class OrderCommon extends BackendBase
{
    /**
     * @var \Users
     */
    private $user;

    /**
     * @var \SeuDo\Logger::factory("order_change_price")
     */
    private $logger = null;

    /**
     * @var \SeuDo\Logger::factory("");
     */
    public $logger_choose_services = null;

    public $is_public_profile = false;
    public $is_external = false;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
        $this->logger = Logger::factory("order_change_price");

        $this->logger_choose_services = Logger::factory("choose_services_backend");
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('logOrderComment', array(new \BackendEvent(), 'logOrderComment'));

        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $this->is_public_profile = true;
        }

        if($this->isAllowed(PERMISSION_COMMUNICATE_CUSTOMER)){
            $this->is_external = true;
        }
    }

    public function executeDefault()
    {
        return $this->renderText(0);
    }

    public function executeEditPriceItem(){
        $this->validAjaxRequest();
        $price_item = $this->request()->post("price_item","FLOAT","0");
        $order_item_id = $this->request()->post("order_item_id","INT",0);
        $is_promotion = $this->request()->post("is_price_promotion");
        $price_old = $this->request()->post("price_old","FLOAT","0");
        $message = $this->request()->post("reason_edit");
        $exchange_rate = \ExchangeRate::getExchange();
        $price_new = \Common::roundingMoney($price_item*$exchange_rate);

        $price_old_log = \Common::roundingMoney($price_old*$exchange_rate);

        $array_log = array(
            "reason_edit" => $message,
            "price_cny_new" => $price_item,
            "price_vnd_new" => $price_new,
            "price_cny_old" => $price_old,
            "price_vnd_old" => $price_old_log,
            "is_promotion" => $is_promotion
        );

        $price_old = $price_old_log;
        $ajax = new \AjaxResponse();

        try{
            if(!$this->isAllowed(PERMISSION_ORDER_EDIT_PRICE)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Bạn không có quyền sửa giá sản phẩm";

                return $this->renderText($ajax->toString());
            }

            $user = \BaseAuth::getInstance()->getUser();

            if($price_item <= 0 || $message == "" || $order_item_id == 0){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Thiếu thông tin cần thiết để sửa giá";

                return $this->renderText($ajax->toString());
            }

            $order_item = \OrderItem::retrieveById($order_item_id);
            if($order_item instanceof \OrderItem){
                $order = $order_item->getOrder();
                $order->beginTransaction();
                if(!($order instanceof \Order)){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = "Không tồn tại đơn hàng của sản phẩm này";

                    return $this->renderText($ajax->toString());
                }

                //Customer Confirm
                if(($price_old - $price_new) < 0){

                    if($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)){
                        $note = "Nhân viên {$this->user->getFullname()} - @{$this->user->getUsername()} thay đổi đơn giá từ {$order_item->getPrice()} thành {$price_new}";
                        $note_order = "Nhân viên {$this->user->getFullname()} - @{$this->user->getUsername()} thay đổi đơn giá hoặc số lượng của sản phẩm trong đơn hàng, kiểm tra
                                mục chat của đơn hàng để biết chi tiết";
                    }else{
                        $note = "Hệ thống vừa thay đổi đơn giá từ {$order_item->getPrice()} thành {$price_new}";
                        $note_order = "Hệ thống vừa thay đổi đơn giá hoặc số lượng của sản phẩm trong đơn hàng, kiểm tra
                                mục chat của đơn hàng để biết chi tiết";
                    }

                    $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_WAIT);

                    $order->setNoteCustomerConfirm($note_order);

                    $order->setConfirmCreatedTime(new \DateTime());

                    $order_item->setNoteSystem($note);
                    $this->dispatch( ON_CONFIRM_ORDER_BACKEND, new Event( $this, array(
                        'order' => $order,
                        'type_confirm' => 'price'
                    ) ) );

                    $this->dispatch('logOrderComment', new Event($this, array(
                        'order' => $order,
                        'message' => "Đơn hàng cần xác nhận: Điều chỉnh giá sản phẩm với mã
                        {$order_item->getId()} từ {$price_old}đ thành {$price_new}đ. Lý do: {$message}",
                        "is_external" => true,
                        'is_log' => true
                    )));
                }

                $price_old = \Common::numberFormat($price_old);
                $price_new = \Common::numberFormat($price_new);

                $result = $order_item->updatePrice($price_item,$is_promotion);

                if($result){
                    $order->save();
                    $order->commit();
                    $this->logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} change price of item : #{$order_item->getId()} in Order with code :{$order->getCode()} success",$array_log);

                    $this->dispatch('logOrderComment', new Event($this, array(
                        'order' => $order,
                        'message' => "Điều chỉnh giá sản phẩm với mã {$order_item->getId()} từ {$price_old}đ thành {$price_new}đ
                        do :{$message}",
                        "is_external" => true,
                        'is_log' => true
                    )));


                    $this->dispatch('logOrderComment', new Event($this, array(
                        'order' => $order,
                        'message' => "Điều chỉnh giá sản phẩm với mã {$order_item->getId()} từ {$price_old}đ thành {$price_new}đ.
                                Lý do: {$message}",
                        "is_activity" => true,
                        "is_public" => true,
                        "is_external" => false,
                    )));

                    $this->dispatch('afterEditUnitPriceItem', new Event($this, array(
                        'order_item' => $order_item,
                        'order' => $order,
                        "message" => "Điều chỉnh giá sản phẩm từ {$price_old}đ thành {$price_new}đ.",
                        "staff" => $this->user
                    )));

//                /
//                \OrderComment::addComment($user->getId(),$order->getId(),\OrderComment::TYPE_EXTERNAL,$content,);

//                \OrderPeer::addOrderComment($order, $user, $content, \OrderComment::TYPE_EXTERNAL);
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $price_cny = $order_item->getPricePromotion() > 0 ? $order_item->getPricePromotion() :
                        $order_item->getPriceOrigin();
                    if(floatval($price_cny) <= 0){
                        $price_cny = floatval($order_item->getPricePromotion()) > 0 ? $order_item->getPricePromotion() :
                            floatval($order_item->getPriceOrigin());
                        $price_cny = $price_cny > 0 ? $price_cny : $order_item->getPrice() / \ExchangeRate::getExchange();
                    }
                    $ajax->total_item_price_ndt = number_format($price_cny*$order_item->getPendingQuantity(),2,',','.');
                    $ajax->total_item_price_vnd = \Common::numberFormat($order_item->getPrice()*$order_item->getPendingQuantity());
                    $ajax->total_order_price_ndt = number_format($order->getRealAmountNdt(),2,',','.');
                    $ajax->total_order_price_vnd = \Common::numberFormat($order->getRealAmount());
                    $ajax->total_item_quantity = $order->calcOrderQuantity();
                    $ajax->price_vnd = $order_item->getPrice();
                    $ajax->price_vnd_format = \Common::numberFormat($order_item->getPrice());
                    $ajax->message = "Sửa thành công";
                }else{
                    $order->rollBack();
                    $this->logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} change price of item : #{$order_item->getId()} in Order with code :{$order->getCode()} not success",$array_log);
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = "Sửa không thành công. Liên hệ kĩ thuật để được hỗ trợ";
                }
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không tồn tại sản phẩm";
            return $this->renderText($ajax->toString());
        }catch (Exception $e){
            $this->logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} change price of item : #{$order_item_id} not success ".$e->getMessage(),$array_log);
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật, liên hệ bộ phận kỹ thuật để được hỗ trợ";
            return $this->renderText($ajax->toString());
        }

    }

    /**
     * @return string
     */
    public function executeChooseServices(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        if(!$this->isAllowed(PERMISSION_ORDER_EDIT_SERVICES_REQUEST)){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Bạn không có quyền sửa yêu cầu dịch vụ của đơn hàng";
            return $this->renderText($ajax->toString());
        }
        $order_id = $this->request()->post("order_id","INT",0);
        $services_type = $this->request()->post("services_type");
        $order = \Order::retrieveById($order_id);

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này.";
            return $this->renderText($ajax->toString());
        }

        $this->user = \BaseAuth::getInstance()->getUser();
        try{
            if($order instanceof \Order){
                $result = false;
                $check_mapping = $order->mappingToService($services_type);
                $action_check = "Chọn";
                if(!$check_mapping){
                    $action_check = "Chọn";
                    $result = $order->addService($services_type);
                    if($services_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM && $order->mappingToService(\Services::TYPE_SHIPPING_CHINA_VIETNAM)){
                        $order->removeService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    }elseif($services_type == \Services::TYPE_SHIPPING_CHINA_VIETNAM && $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
                        $order->removeService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
                    }
                    if($result){
                        $this->logger_choose_services->info($this->user->getUsername()." - {$this->user->getFullName()} Choose services {$services_type} for order with order code : ".$order->getCode());
                    }
                }
                if($check_mapping){
                    $action_check = "Bỏ chọn";
                    $result = $order->removeService($services_type);

                    if($services_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM && !$order->mappingToService(\Services::TYPE_SHIPPING_CHINA_VIETNAM)){
                        $order->addService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    }

                    if($result){
                        $this->logger_choose_services->info($this->user->getUsername()." - {$this->user->getFullName()} Remove services {$services_type} for order with order code : ".$order->getCode());
                    }
                }

                $order->changeRecipientName();

                $services = \Services::retrieveByCode($services_type);

                if($result){

                    $this->dispatch('onAfterChooseService', new Event($this, array(
                        'order' => $order,
                        "service_type" => $services_type,
                        "staff" => $this->user
                    )));

                    if($services_type == \Services::TYPE_WOOD_CRATING){

                        $this->dispatch( ON_CHAT_ORDER_BACKEND, new Event( $this, array(
                            'order' => $order,
                            'sender_id' => $this->user->getId(),
                            'message_content' => $check_mapping ? "Sếu Đỏ đã huỷ dịch vụ đóng gỗ " : "Sếu Đỏ đã thêm dịch vụ đóng gỗ ",
                            'type_chat' => 'activity'
                        ) ) );
                        $ajax->permission_order_edit_services = $this->isAllowed( PERMISSION_ORDER_EDIT_SERVICES_REQUEST );
                    }

                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = "Thành công";
                    $ajax->name_origin = $order->getNameRecipientOrigin();
                    $ajax->address_receive = $order->createAddressReceive();
                    $ajax->fee = \OrderService::getServiceFee($order,\Services::TYPE_EXPRESS_CHINA_VIETNAM);

                }else{
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = "Thất bại, liên hệ kỹ thuật để được hỗ trợ";
                }
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tồn tại đơn hàng";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Thất bại, liên hệ kỹ thuật để được hỗ trợ " .$e->getMessage();
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateFeeWoodCrating()
    {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $order_id = $this->request()->post( "order_id", "INT", 0 );
        $money = $this->request()->post( "money" );
        $money = preg_replace( '/\D/', '', $money );
        $order_service = \OrderService::findOneByOrderIdAndServiceCode( $order_id, \Services::TYPE_WOOD_CRATING );
        if ( $order_service ) {
            try {
                $order_service->setMoney( $money );
                $order_service->save();
                $order = \Order::retrieveById( $order_id );
                $order->updateInfo();

                //log internal
                $this->dispatch( 'logOrderComment', new Event( $this, array(
                    'order' => $order,
                    'message' => "Thêm phí đóng gỗ: " . \Common::numberFormat( $money ) . " VNĐ",
                    "is_activity" => true,
                    "is_public" => $this->is_public_profile,
                    "is_external" => false,
                    "is_chat" => false
                ) ) );
                //log external
                $this->dispatch( 'logOrderComment', new Event( $this, array(
                    'order' => $order,
                    'message' => "Đã thêm " . \Common::numberFormat( $money ) . " VNĐ phí đóng gỗ",
                    "is_activity" => true,
                    "is_external" => true,
                    "is_log" => false
                ) ) );
                //notification to customer
                $this->dispatch( ON_CHAT_ORDER_BACKEND, new Event( $this, array(
                    'order' => $order,
                    'sender_id' => $this->user->getId(),
                    'message_content' => "Sếu Đỏ đã thêm " . \Common::numberFormat( $money ) . " VNĐ phí đóng gỗ ",
                    'type_chat' => 'activity'
                ) ) );
                $data = \Order::getListFeeOrder( $order_id );
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Thành công";
                $ajax->data = $data;
                return $this->renderText( $ajax->toString() );
            } catch ( \Exception $e ) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Thất bại, liên hệ kỹ thuật để được hỗ trợ " . $e->getMessage();
                return $this->renderText( $ajax->toString() );
            }
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Có lỗi xảy ra, vui lòng thử lại!";
            return $this->renderText( $ajax->toString() );
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function executeOutOfStock() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        //check permission first
        if (!$this->isAllowed(PERMISSION_ORDER_TRANSITION_OUT_OF_STOCK)) {
            $ajax->message = self::t('Bạn không có quyền thực hiện thao tác này');
            return $this->renderText($ajax->toString());
        }

        $order_id = $this->post('id', 'INT');
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            $ajax->message = self::t('Đơn hàng không tồn tại');
            return $this->renderText($ajax->toString());
        }

        try {
            $amount = $order->getRealPaymentAmount() - $order->getRealRefundAmount();
            if($order->getRealPaymentAmount() == 0
                || $order->getRealPaymentAmount() == $order->getRealRefundAmount()){
                //Khách chưa thanh toán cho đơn hàng
                if($order->getRealPaymentAmount() == 0){
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->flag = 0;
                    $ajax->message = "Đơn hàng này khách chưa thanh toán.";
                }

                //Khách đã được hoàn lại đủ số tiền khách đã thanh toán
                if($order->getRealPaymentAmount() == $order->getRealRefundAmount()){
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->flag = 0;
                    $ajax->message = "Khách hàng đã nhận lại đủ số tiền đã thanh toán (" . \Common::numberFormat($order->getRealPaymentAmount()) . ") trong đơn hàng này.";
                }

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Đơn hàng chuyển sang trạng thái hết hàng.",
                    "is_activity" => false,
                    "is_chat" => false,
                    "is_log" => true,
                    "is_external" => true,
                )));

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => " Chuyển trạng thái đơn hàng sang hết hàng.",
                    "is_activity" => true,
                    "is_chat" => false,
                    "is_external" => false,
                )));

                $order->setStatus(\Order::STATUS_OUT_OF_STOCK);
                $order->setOutOfStockTime(new DateTime());
                if (!$order->save()) {//quick save
                    throw new \RuntimeException('Could not save order after change out of stock');
                }

                return $this->renderText($ajax->toString());
            }

            if($amount < 0){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Số tiền trả lại cho khách đã vượt quá số tiền khách thanh toán.";
                return $this->renderText($ajax->toString());
            }

            $transaction = \OrderPeer::transitOutOfStock($order, "Trả lại đơn hàng {$order->getCode()} khi hết hàng");
            if ($transaction) {
                //dispatch Event
                $this->dispatch('onOrderTransitOutOfStock', new Event($this, array(
                    'order' => $order,
                    'transaction' => $transaction
                )));

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Đơn hàng chuyển sang trạng thái hết hàng. Đơn hàng được trả lại số tiền " . \Common::numberFormat($amount) . " VNĐ",
                    "is_activity" => true,
                    "is_chat" => false,
                    "is_log" => false,
                    "is_external" => true,
                )));

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => " Chuyển trạng thái đơn hàng sang hết hàng. Hoàn lại cho khách số tiền " . \Common::numberFormat($amount) . " VNĐ với mã giao dịch là " . $transaction->getId(),
                    "is_activity" => true,
                    "is_chat" => false,
                    "is_external" => false,
                )));

                $ajax->message = "";
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->order = $order->toArray();
                $ajax->transacion = $transaction->getId();
                $ajax->amount = $amount;
                $ajax->amount_format = \Common::numberFormat($amount);
                $ajax->flag = 1;
            } else {
                $ajax->message = self::t('Lỗi kỹ thuật! Không thể thực hiện hết hàng, vui lòng thông báo kiểm tra!');
            }

            return $this->renderText($ajax->toString());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
