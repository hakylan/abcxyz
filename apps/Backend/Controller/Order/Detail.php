<?php
namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use Flywheel\Event\Event;
use Flywheel\Factory;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use mongodb\OrderComment;
use SeuDo\Logger;
use Flywheel\Redis\Client as RedisClient;

class Detail extends BackendBase {

    private $user;
    public $is_public_profile = false;
    public $is_external = false;
    public $is_internal = false;

    public function beforeExecute() {
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();

        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $this->is_public_profile = true;
        }

        if($this->isAllowed(PERMISSION_COMMUNICATE_CUSTOMER)){
            $this->is_external = true;
        }

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('logOrderComment', array(new \BackendEvent(), 'logOrderComment'));
    }

    /**
     * Hàm xác nhận lại đơn hàng khi đơn hàng có sự thay đổi
     * @return string
     */
    public function executeManageConfirm(){

        $this->validAjaxRequest();

        $idOrder = $this->request()->post('id');
        $status = $this->request()->post('status');

        $message_public = $this->request()->post('message_public');
        $message_private = $this->request()->post('message_private');

        if (!$this->isAllowed(PERMISSION_ORDER_CUSTOMER_CONFIRM)) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Bạn không có quyền xác nhận đơn hàng này cho khách')));
        }

        if($status!=\Order::CUSTOMER_CONFIRM_WAIT){
            $ajax= \AjaxResponse::responseError('Đơn hàng chỉ có thể xác nhận trong trạng thái chờ xác nhận');
            return $this->renderText($ajax);
        }

        $order = \Order::retrieveById($idOrder);

        if(!$order || (!$order instanceof \Order)){
            $ajax= \AjaxResponse::responseError('Không tìm thấy đơn hàng để xác nhận');
            return $this->renderText($ajax);
        }else{
            if($order->getCustomerConfirm()!=\Order::CUSTOMER_CONFIRM_WAIT){
                $ajax= \AjaxResponse::responseError('Đơn hàng chỉ có thể xác nhận trong trạng thái chờ xác nhận');
                return $this->renderText($ajax);
            }else{
                $order->setNew(false);
                $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_NONE);
                $order->setConfirmApprovalTime(new \DateTime());
                if($order->save()){
                    $this->dispatch("afterCustomerConfirmed",new Event($this,array(
                        "order" => $order
                    )));

                    //LOG PULBLIC AND PRIVATE
                    $this->dispatch('logOrderComment', new Event($this, array(
                        'order' => $order,
                        'message' => $message_public,
                        'is_public' => $this->is_public_profile,
                        'is_external' => true,
                        'is_activity' => true,
                        'is_chat' => false,
                        'is_log' => false
                    )));

                    $this->dispatch('logOrderComment', new Event($this, array(
                        'order' => $order,
                        'message' => $message_private,
                        'is_public' => $this->is_public_profile,
                        'is_external' => false,
                        'is_activity' => true,
                        'is_chat' => false,
                        'is_log' => false
                    )));

                    $ajax= \AjaxResponse::responseSuccess('Success');
                    return $this->renderText($ajax);
                }else{
                    $ajax= \AjaxResponse::responseError('Không thể xác nhận, vui lòng liên hệ chăm sóc khách hàng để được giúp đỡ hoặc sử dụng công cụ chát trực tuyến trên trang!');
                    return $this->renderText($ajax);
                }
            }
        }

        $ajax= \AjaxResponse::responseError('Error');
        $ajax->type = \AjaxResponse::ERROR;
        return $this->renderText($ajax);
    }

    /**
     * Hàm cập nhật cân nặng cho kiện hàng
     * @return string
     */
    public function executeUpdatePackageWeight(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{

            if (!$this->isAllowed(PERMISSION_ORDER_EDIT_WEIGHT)) {
                return $this->renderText(\AjaxResponse::responseError(self::t('Bạn không có quyền sửa trọng lượng')));
            }

            $package_id = $this->request()->post('package_id', 'INT', 0);
            $order_id = $this->request()->post('order_id', 'INT', 0);
            $weight = $this->request()->post('weight', 'FLOAT', 0);

            if (!$order_id || !($order = \Order::retrieveById($order_id))) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = self::t('Không tìm thấy đơn hàng');
                return $this->renderText($ajax->toString());
            }

            if ( !$order->canChangeWeight() ) {
                return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không thể sửa trọng lượng')));
            }

            $package = false;
            $package = \Packages::retrieveById( $package_id );
            if(!$package){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tìm thấy kiện hàng";
                return $this->renderText($ajax->toString());
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->data = $package->updateWeight( $weight, $order_id );
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!";
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Hàm lấy danh sách kiện theo đơn hàng
     * @return string
     */
    public function executeGetListPackagesByOrderId() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $order_id = $this->request()->get('order_id', 'INT', 0);
            $order = false;
            $order = \Order::retrieveById($order_id);
            if(!$order){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng không tồn tại!";
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::SUCCESS;

            $redis = RedisClient::getConnection( \Order::REDIS_CONFIG );
            $keys = $redis->keys( REDIS_FREIGHT_BILL . $order->getId() .'_*' );

            $result = array();
            $idx = 1;
//            if (empty($keys)) {
                $freightBills = \Packages::select()
                    ->where('`order_id` = ?')
                    ->setParameter(0, $order_id, \PDO::PARAM_INT)
                    ->execute();

                if ($freightBills) {
                    foreach($freightBills as $freightBill) {
                        if( $freightBill instanceof \Packages ) {
                            $d = $freightBill->toArray();
                            $d['idx'] = $idx++;
                            $d['status_title'] = isset( $d['status_title'] ) && $d['status_title'] ? \Packages::$statusTitle[ $d['status'] ] : '';
                            $d['package_checking_history'] = \PackageCheckingHistory::retrieveByPackageId( $d['id'] );

                            $is_warehouse_status_time = false;
                            $warehouse_status_title = $warehouse_status_time = '';
                            if( $d['warehouse_status'] == 'IN' ) {
                                $warehouse_status_title = 'Trong kho';
                                if( $d['warehouse_status_in_time'] ) {
                                    $warehouse_status_time = $d['warehouse_status_in_time'];
                                    $is_warehouse_status_time = true;
                                }
                            }

                            if( $d['warehouse_status'] == 'OUT' ) {
                                $warehouse_status_title = 'Xuất kho';
                                if( $d['warehouse_status_out_time'] ) {
                                    $warehouse_status_time = $d['warehouse_status_out_time'];
                                    $is_warehouse_status_time = true;
                                }
                            }

                            $d['warehouse_status_title'] = $warehouse_status_title;
                            $d['warehouse_status_time'] = $warehouse_status_time;

                            $detail_link_logistic_package_barcode = 'javascript:';
                            if( $d['logistic_package_barcode'] ) {
                                $package_code = substr($d['logistic_package_barcode'],2);
                                $detail_link_logistic_package_barcode = \SystemConfig::SITE_ROOT_LOGISTIC . 'package/default#/package-detail/' . $package_code;
                            }
                            $d['detail_link_logistic_package_barcode'] = $detail_link_logistic_package_barcode;

                            $d['show_level'] = $d['level'];
                            $d['is_warehouse_status_time'] = $is_warehouse_status_time;
                            $result[] = $d;
                        }
                    }
                }
//            } else {
//                for($i = 0, $size = sizeof($keys); $i < $size; ++$i) {
//                    if ($data = $redis->hGetAll($keys[$i])) {
//                        $obj = new self($data, false);
//                        $d = $obj->toArray();
//                        $d['idx'] = $idx++;
//                        $d['status_title'] = 'TEST';
//                        $d['package_checking_history'] = \PackageCheckingHistory::retrieveByPackageId( $d['id'] );
//                        $result[] = $d;
//                    }
//                }
//            }

            $ajax->data = $result;
            $ajax->total = sizeof($result);
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!" . $e->getMessage();
            return $this->renderText($ajax->toString());
        }
    }

    public function executeAddPackageChecking(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $this->user = \BaseAuth::getInstance()->getUser();

        try{
            $package_id = $this->request()->post('package_id', 'INT', 0);
            $total_product = $this->request()->post('total_product', 'INT', 0);

            $package = false;
            $package = \Packages::retrieveById( $package_id );
            if(!$package){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Kiện hàng không tồn tại!";
                return $this->renderText($ajax->toString());
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->data = \PackageCheckingHistory::addCheckingHistory( $this->user->getId(), $package_id, $total_product );
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeGetListFreeOrder(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $order_id = $this->request()->get('order_id', 'INT', 0);
            $order = false;
            $order = \Order::retrieveById($order_id);
            if(!$order){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng không tồn tại!";
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->data = \Order::getListFeeOrder($order_id);
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeGetListTransactionOrder(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $order_id = $this->request()->get('order_id', 'INT', 0);
            $order = false;
            $order = \Order::retrieveById($order_id);
            if(!$order){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng không tồn tại!";
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->transaction_order = \UserTransaction::buildTransactionInOrderToArray($order);
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeGetRealPaymentAmount(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $order_id = $this->request()->get('order_id', 'INT', 0);
            $order = false;
            $order = \Order::retrieveById($order_id);
            if(!$order){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng không tồn tại!";
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::SUCCESS;
            $real_payment_amount = $order->getRealPaymentAmount();
            $real_refund_amount = $order->getRealRefundAmount();
            $ajax->real_payment_amount = $real_payment_amount;
            $ajax->real_payment_amount_format = \Common::numberFormat($real_payment_amount);
            $ajax->real_refund_amount = $real_refund_amount;
            $ajax->real_refund_amount_format = \Common::numberFormat($real_refund_amount);
            $ajax->amount = $real_payment_amount - $real_refund_amount;
            $ajax->amount_format = \Common::numberFormat($real_payment_amount - $real_refund_amount);
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kỹ thuật!";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeDefault() {
        if (!$this->isAllowed(PERMISSION_ORDER_VIEW_ORDER_DETAIL)) {
            $this->raise403("Bạn không có quyền xem chi tiết đơn hàng");
        }
        $this->user = \BaseAuth::getInstance()->getUser();
        $this->setView('Order/detail');

        $id = $this->get('id');
        $order = false;

        if (is_numeric($id)) {
            $order = \Order::retrieveById($id);
        } else {
            $order = \Order::retrieveByCode($id);
        }

        if (!$order) {
            //not found
            $this->raise404(self::t('Đơn hàng bạn yêu cầu không tồn tại'));
        }

        //urls;
        $this->document()->addJsVar('get_items_url', $this->createUrl('order/detail/get_items', array(
            'order_id' => $order->getId()
        )));
        $this->document()->addJsVar('get_order_comments_url', $this->createUrl('order/detail/get_order_comments', array(
            'order_id' => $order->getId()
        )));
        $this->document()->addJsVar('confirm_checked_url', $this->createUrl('order/detail/confirm_checked'));
        $this->document()->addJsVar('change_item_quantity', $this->createUrl('order/detail/change_item_quantity'));
        $this->document()->addJsVar('change_item_quantities', $this->createUrl('order/detail/change_item_quantities'));
        $this->document()->addJsVar('change_item_pendding_quantity', $this->createUrl('order/detail/change_item_pendding_quantity'));
        $this->document()->addJsVar('change_item_pendding_quantities', $this->createUrl('order/detail/change_item_pendding_quantities'));
        $this->document()->addJsVar("ChooseServicesLink",$this->createUrl('order_common/choose_services'));
        $this->document()->addJsVar('change_weight_url', $this->createUrl('order/detail/change_weight'));
        $this->document()->addJsVar('change_to_waiting_delivery', $this->createUrl('order/detail/change_waiting_delivery'));
        $this->document()->addJsVar('change_to_request_delivery', $this->createUrl('order/detail/change_request_delivery'));
        $this->document()->addJsVar('change_to_transporting', $this->createUrl('order/detail/change_transporting'));
        $this->document()->addJsVar('change_to_delivery', $this->createUrl('order/detail/change_delivery'));
        $this->document()->addJsVar('first_name', $this->user->getFirstName());
        $this->document()->addJsVar('shorten_fullname', $this->user->getShortenFullName());
        $this->document()->addJsVar('_account', $this->user->getUsername());
        $this->document()->addJsVar('current_user_id', $this->user->getId());
        $this->document()->addJsVar('current_username', $this->user->getFullName());
        $this->document()->addJsVar('current_img_path', \Users::getAvatar32x($this->user));
        $this->document()->addJsVar('order_id', $order->getId());
        $this->document()->addJsVar( 'LinkOrderDetailUrl',  $this->createUrl( 'order/detail', array( 'id' => $order->getId() ) ) );
        $this->document()->addJsVar( 'LinkGetPackages', $this->createUrl( 'order/detail/get_list_packages_by_order_id' ) );

        //manipulation
        $data = $order->toArray();
        $data['destination_warehouse'] = $order->getDestinationWarehouse();

        if ($order->getWeight()) {
            //kilogram to gram
            $data['weight'] = $order->getWeight() * 1000;
        }
        $data['packages'] = array();
        $packages = \Packages::findByOrderId($order->getId());
        if ($packages) {
            foreach($packages as $package) {
                $data['packages'][] = $package->toArray();
            }
        }

        if (empty($data['packages'])) {
            $data['packages'] = new \stdClass();
        }

        //DELIVERY STAFF - delivery_staff_id
        $delivery_staff = \Users::retrieveById($order->getDeliveryStaffId());
        if ($delivery_staff) {
            $data['delivery_staff'] = $delivery_staff->getAttributes('id,username,code,last_name,first_name');
            $data['delivery_staff']['shorten_fullname'] = $delivery_staff->getShortenFullName();
            $data['delivery_staff']['avatar'] = \Users::getAvatar32x($delivery_staff);
            $data['delivery_staff']['detail_link'] = $this->createUrl('user/detail', array('id' => $delivery_staff->getId()));
        }

        //BUYER
        if ($this->isAllowed(PERMISSION_USER_VIEW)) {
            $buyer = \Users::retrieveById($order->getBuyerId());
            if ($buyer) {
                $data['buyer'] = $buyer->getAttributes('id,username,code,last_name,first_name');
                $data['buyer']['shorten_fullname'] = $buyer->getShortenFullName();
                $data['buyer']['avatar'] = \Users::getAvatar32x($buyer);
                $data['buyer']['detail_link'] = $this->createUrl('user/detail', array('id' => $buyer->getId()));
            }
        }

        //USER CHECKED
        $checked = \Users::retrieveById($order->getCheckerId());
        if ($checked) {
            $data['user_checked'] = $checked->getAttributes('id,username,code,last_name,first_name, email');
            $data['user_checked']['shorten_fullname'] = $checked->getShortenFullName();
            $data['user_checked']['avatar'] = \Users::getAvatar32x($checked);
            $data['user_checked']['detail_link'] = $this->createUrl('user/detail', array('id' => $checked->getId()));
        }

        //STAFF
        $teller = \Users::retrieveById($order->getTellersId());
        if ($teller) {
            $data['teller'] = $teller->getAttributes('id,username,code,last_name,first_name, email');
            $data['teller']['shorten_fullname'] = $teller->getShortenFullName();
            $data['teller']['avatar'] = \Users::getAvatar32x($teller);
            $data['teller']['detail_link'] = $this->createUrl('user/detail', array('id' => $teller->getId()));
        }

        $payment = \Users::retrieveById($order->getPaidStaffId());
        if ($payment) {
            $data['payment'] = $payment->getAttributes('id,username,code,last_name,first_name, email');
            $data['payment']['shorten_fullname'] = $payment->getShortenFullName();
            $data['payment']['avatar'] = \Users::getAvatar32x($payment);
            $data['payment']['detail_link'] = $this->createUrl('user/detail', array('id' => $payment->getId()));
        }

        //Trạng thái kiểm hàng
        $data['is_checking_status_checked'] = $data['is_checking_status_not_yet_checked'] = false;

        if ( $order->getCheckingStatus() == \Order::CHECKING_STATUS_CHECKED ) {
            $data['is_checking_status_checked'] = true;
        }

        if( $order->getCheckingStatus() == \Order::CHECKING_STATUS_NOT_YET_CHECKED ) {
            $data['is_checking_status_not_yet_checked'] = true;
        }

        //need checking ?
        $data['need_checking'] = $order->needToChecking();

        //Shipping Address
        if ($this->isAllowed(PERMISSION_ORDER_VIEW_SHIPPING_ADDRESS)) {
            $shippingAddress = \UserAddress::retrieveById($order->getUserAddressId());
            if ($shippingAddress) {
                $data['address'] = $shippingAddress->toFullArray();
            } else {
                $data['address'] = array(
                    'error' => self::t('Đơn hàng chưa có địa chỉ hoặc đã bị xóa')
                );
            }
        }

        //Lấy link site gốc
        $dataSiteRoot = \Common::getLinkSiteRoot($order->getSellerHomeland(), $order->getInvoice());
        $data['show_link_site_root'] = $dataSiteRoot['show_link_site_root'];
        $data['arrLinkSiteRoot'] = $dataSiteRoot['arrLinkSiteRoot'];

        $data['seller_home_land'] = $order->getSellerHomeland();
        $data['seller_favicon_site'] = \Common::getFaviconSite($order->getSellerHomeland());
        $data['seller_name'] = $order->seller_name;
        $data["is_cpn"] = $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);

        $data['checked'] = $order->isAfterStatus(\Order::STATUS_CHECKED, true);
        $data['check_wood_crating'] = $order->needToWoodCrating();
        $data['check_fragile'] = $order->needToFragile();
        $data["real_cny"] = \Common::numberFormat($order->getRealAmountNdt());
        $data["total_vnd_format"] = \Common::numberFormat($order->getRealAmount());
        $data['warehouse_status'] = $order->getWarehouseStatusTitle();

        $data['show_warehouse_in_time'] = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_IN && $order->getWarehouseInTime() != '0000-00-00 00:00:00';
        $data['show_warehouse_out_time'] = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_OUT && $order->getWarehouseOutTime() != '0000-00-00 00:00:00';
        $data['show_customer_confirm'] = $order->getCustomerConfirm() == \Order::CUSTOMER_CONFIRM_WAIT;
        $time_left_customer_confirm = 0;
        if( $order->getConfirmCreatedTime() != '0000-00-00 00:00:00' ) {
            $time_left_customer_confirm = \Common::getDateDiff( $order->getConfirmCreatedTime(), date('Y-m-d H:i:s') );
        }

        $data['time_left_customer_confirm'] = $time_left_customer_confirm;
        $data['show_time_left_customer_confirm'] = $time_left_customer_confirm > 0;

        $data['order_status_color'] = $order->getColorByOrderStatus();

        //check permission change weight
        $edit_weight = $this->isAllowed(PERMISSION_ORDER_EDIT_WEIGHT)
                            && $order->canChangeWeight()
                            && $order->getStatus() != \Order::STATUS_OUT_OF_STOCK;

        $data['edit_weight'] = $edit_weight;
        //check express_delivery
        $data['express_delivery'] = $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM) ? 1 : 0;
        //check fee wood crating
        $data["is_wood_crating"] = $order->mappingToService(\Services::TYPE_WOOD_CRATING);
        //check is show guide
        $data['is_show_note_customer_confirm'] = $order->getNoteCustomerConfirm() != "";
        $cookie = Factory::getCookie();
        $data['is_show_guide'] = $cookie->read("order_detail_guide_" . $this->user->getId()) ? 0 : 1;
        if(!$cookie->read("order_detail_guide_" . $this->user->getId())){
            $cookie->write("order_detail_guide_" . $this->user->getId(), 'viewed', 60*60*24*90);
        }

        //check permission change to WAITING_FOR_DELIVERY
        $permission_change_to_waiting_for_delivery = ( $this->isAllowed(PERMISSION_ORDER_CHANGE_WAIT_DELIVERY) && $order->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY) )
            && ( \Order::STATUS_CHECKED == $order->getStatus() || (!$order->needToChecking() && $order->isAfterStatus(\Order::STATUS_BOUGHT, true)) )
            && $order->getStatus() != \Order::STATUS_OUT_OF_STOCK;

        $data['permission_change_to_waiting_for_delivery'] = $permission_change_to_waiting_for_delivery;
        $this->document()->addJsVar('order', $data);

        $this->document()->title .= self::t('Đơn hàng %code%', array('%code%' => $order->getCode()));

        $this->view()->assign('order', $order);
        $this->view()->assign('edit_weight', $edit_weight);
        $this->view()->assign('permission_change_to_waiting_for_delivery', $permission_change_to_waiting_for_delivery);

        return $this->renderComponent();
    }

    public function executeAddFee() {}

    public function executeChangeItemQuantity() {
        $this->validAjaxRequest();
        $item_id = $this->post('item_id');
        $quantity = $this->post('quantity', 'INT', 0);
        $this->user = \BaseAuth::getInstance()->getUser();

        $orderItem = \OrderItem::retrieveById($item_id);
        $old_recive_quantity = $orderItem->getReciveQuantity();

        if (!$orderItem) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Sản phẩm không tồn tại')));
        }

        $order = \Order::retrieveById($orderItem->getOrderId());
        if (!$order) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không tồn tại')));
        }
        $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'OK');

        if ($quantity != $orderItem->getReciveQuantity()) {

            if (!$orderItem->updateReceiveQuantity($quantity)) {
                return $this->renderText(\AjaxResponse::responseError(self::t('Lỗi kỹ thuật, vui lòng liên hệ với developer xử lý')));
            }
        } else {
            //nothing
        }

        //ACTIVITY
        $content = "Sửa số lượng sản phẩm với mã " . $orderItem->getId() . " từ " . $old_recive_quantity . " thành " . $quantity;
        $this->dispatch('logOrderComment', new Event($this, array(
            'order' => $order,
            'message' => $content,
            'is_public' => $this->is_public_profile,
            'is_external' => false,
            'is_activity' => true,
            'is_chat' => false,
            'is_log' => false
        )));

        $ajax->message = self::t('OK');
        $ajax->old_recive_quantity = $old_recive_quantity;
        $ajax->order_item = $orderItem->toArray();
        $ajax->order = $orderItem->getOrder()->toArray();

        return $this->renderText($ajax->toString());
    }

    public function executeChangeItemPenddingQuantity() {
        $this->validAjaxRequest();
        $item_id = $this->post('item_id');
        $quantity = $this->post('quantity', 'INT', 0);

        $orderItem = \OrderItem::retrieveById($item_id);
        if (!$orderItem) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Sản phẩm không tồn tại')));
        }

        $order = \Order::retrieveById($orderItem->getOrderId());
        if (!$order) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không tồn tại')));
        }

        $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'OK');
        if ($quantity != $orderItem->getPendingQuantity()) {
            if (!$orderItem->updatePendingQuantity($quantity)) {
                return $this->renderText(\AjaxResponse::responseError(self::t('Lỗi kỹ thuật, vui lòng liên hệ với developer xử lý')));
            }
        } else {
            //nothing
        }

        $ajax->message = self::t('OK');
        $ajax->order_item = $orderItem->toArray();
        $ajax->order = $orderItem->getOrder()->toArray();

        return $this->renderText($ajax->toString());
    }

    public function executeConfirmChecked() {
        $this->validAjaxRequest();
        $user = \BackendAuth::getInstance()->getUser();
        $id = $this->post('id', 'INT', 0);
        if (!$id || !($order = \Order::retrieveById($id))) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không tồn tại')));
        }

        if (!($order->checkingEligibility() && $order->needToChecking())) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không nằm trong trạng thái được chuyển sang ĐÃ KIỂM')));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này."));
        }

        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        $order->setCheckerId($user->getId());
        if ($order->changeStatus(\Order::STATUS_CHECKED)) {


            //ACTIVITY EXTERNAL
            $this->dispatch('logOrderComment', new Event($this, array(
                'order' => $order,
                'message' => "Đơn hàng đã được kiểm, tìm thấy tổng {$order->getReciveQuantity()} sản phẩm",
                'is_public' => false,
                'is_external' => true,
                'is_activity' => true,
                'is_chat' => false,
                'is_log' => false
            )));

            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
                'order' => $order,
                'sender_id'=>$user->getId(),
                'message_content'=>"Đơn hàng đã được kiểm, tìm thấy tổng {$order->getReciveQuantity()} sản phẩm",
                'type_chat'=>'activity'
            )));

            //update packages status of order
//            $package_list = \Packages::retrieveByOrderId($order->getId());
//            if ($package_list) {
//                foreach ($package_list as $package) {
//                    if ($package instanceof \Packages) {
//                        if ($package->isStatus(\Packages::STATUS_TRANSPORTING)) {
//                            $package->changeStatus(\Packages::STATUS_WAITING_FOR_DELIVERY);
//                        }
//                    }
//                }
//            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->order = $order->toArray();
        } else {
            $ajax->message = self::t("Có lỗi");
        }

        return $this->renderText($ajax->toString());
    }

    public function executeGetItems() {
        if (!$this->isAllowed(PERMISSION_ORDER_VIEW_ITEMS)) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Bạn không có quyền xem sản phẩm của đơn hàng')));
        }

        $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS);

        $order_id = $this->get('order_id');
        $order = \Order::retrieveById($order_id);
        if (!$order) { // critical error
            return $this->renderText(\AjaxResponse::responseError(self::t('Lỗi kt, đơn hàng không tồn tại')));
        }

        /** @var \OrderItem[] $items */
        $items = (array) \OrderPeer::getOrderItem($order_id);

        //Kiểm tra xem đơn hàng đang ở trong khoảng trạng thái nào
        $type = 3;//1: pendding 2: receive 3: order
        if($order->isBeforeStatus(\Order::STATUS_CHECKED)){
            $type = 1;
        }
        if($order->isAfterStatus(\Order::STATUS_CHECKING)){
            $type = 2;
        }

        $result = array();
        $total_quantity = $count = 0;
        $show_amount_by_pendding_quantity = $show_amount_by_recive_quantity = $show_amount_by_order_quantity = false;
        foreach($items as $item) {
            $count++;
            $data = $item->toArray();
            if ($type == 1) {//pending quantity
                $total_quantity += $item->getPendingQuantity();
                $show_amount_by_pendding_quantity = true;
            } else if($type == 2) {//receive quantity
                $total_quantity += $item->getReciveQuantity();
                $show_amount_by_recive_quantity = true;
            } else if($type == 3) {//order quantity
                $total_quantity += $item->getOrderQuantity();
                $show_amount_by_order_quantity = true;
            }

            $data['show_amount_by_pendding_quantity'] = $show_amount_by_pendding_quantity;
            $data['show_amount_by_recive_quantity'] = $show_amount_by_recive_quantity;
            $data['show_amount_by_order_quantity'] = $show_amount_by_order_quantity;

            $data['show_note'] = $item->getNote();

            //can change quantity
            $data['edit_quantity'] = false;
            if ($order->checkingEligibility()
                && $this->isAllowed(PERMISSION_ORDER_EDIT_RECEIVED_QUANTITY)
                && $order->needToChecking()) {
                $data['edit_quantity'] = 'recive_quantity';
            }

            $data['price_cny'] = $item->getPriceCny() ? $item->getPriceCny() : 0;
            $data['price_vnd'] = $item->getPrice() ? $item->getPrice() : 0;

            $data['total_amount_recive_vnd'] = $item->getPrice() * $item->getReciveQuantity();
            $data['total_amount_pending_vnd'] = $item->getPrice() * $item->getPendingQuantity();
            $data['total_amount_order_vnd'] = $item->getPrice() * $item->getOrderQuantity();

            $data['total_amount_recive_cny'] = $item->getPriceCny() * $item->getReciveQuantity();
            $data['total_amount_pending_cny'] = $item->getPriceCny() * $item->getPendingQuantity();
            $data['total_amount_order_cny'] = $item->getPriceCny() * $item->getOrderQuantity();

            $data['is_odd'] = $count % 2 == 0;

            $result[] = $data;
        }

        $ajax->items = $result;
        $ajax->total_item = sizeof($items);
        $ajax->total_quantity = $total_quantity;
        $ajax->message = 'OK';

        return $this->renderText($ajax->toString());
    }

    public function executeChangeWeight() {
        $this->validAjaxRequest();
        if (!$this->isAllowed(PERMISSION_ORDER_EDIT_WEIGHT)) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Bạn không có quyền sửa trọng lượng')));
        }
        $this->user = \BaseAuth::getInstance()->getUser();

        $ajax = new \AjaxResponse();
        $weight = $this->post('weight', 'STRING', 0);

        $weight = floatval(str_replace(',', '.', str_replace('.', '', $weight)));
        //Gram to kitogram
        $weight = $weight / 1000;

        $order_id = $this->post('id', 'INT', 0);
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t('Không tìm thấy đơn hàng');
            return $this->renderText($ajax->toString());
        }

        if (!$order->canChangeWeight()) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng không thể sửa cân nặng.')));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError(self::t('Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này.')));
        }

        try {
            $old_weight = $order->getWeight();
            $order->changeWeight($weight);
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = 'OK';

            $arrOrder = $order->toArray();
            $arrOrder['weight'] = $arrOrder['weight'] * 1000;

            $ajax->order = $arrOrder;
            $this->dispatch('onChangeOrderWeight', new \BackendEvent($this, array(
                'order' => $order
            )));

            //ACTIVITY EXTERNAL
            $this->dispatch('logOrderComment', new Event($this, array(
                'order' => $order,
                'message' => "Đơn hàng bổ sung {$weight} kg",
                "is_public" => false,
                "is_external" => true,
                "is_activity" => true,
                'is_chat' => false
            )));

            //ACTIVITY INTERNAL
            $this->dispatch('logOrderComment', new Event($this, array(
                'order' => $order,
                'message' => "Đã sửa trọng lượng đơn từ {$old_weight} -> {$weight} kg",
                "is_public" => true,
                "is_external" => false,
                "is_activity" => true,
                'is_chat' => false
            )));

            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
                'order' => $order,
                'sender_id'=>$this->user->getId(),
                'message_content'=>"Bổ sung {$weight} kg vào đơn hàng ",
                'type_chat'=>'activity'
            )));

        } catch (\Exception $e) {
            Logger::factory('system')->error($e->getMessage() .'. \Trances:' .$e->getTraceAsString());
            throw $e;
        }

        return $this->renderText($ajax->toString());
    }

    public function executeGetOrderFrees() {
    }

    public function executeGetOrderComments() {}

    public function executeAddMessage() {
        $this->validAjaxRequest();
        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id');
        $order_id = intval($order_id);
        $type = $this->request()->post('type');

        // Check chat channel
        if ($type==\mongodb\OrderComment::TYPE_EXTERNAL) { // external
            // Check permission
            if (!$this->isAllowed(PERMISSION_COMMUNICATE_CUSTOMER)) {
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->msg = 'Lỗi khi lưu dữ liệu!';
                $ajax->format = 'JSON';

                return $this->renderText($ajax->toString());
            }
        }

        $username = \OrderComment::USER_SYSTEM;
        $is_public_profile = false;

        $img_path = \Users::getAvatar32x($this->user);
        $time = "";
        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $is_public_profile = true;
        }
        $message = \OrderComment::convertToText($message);
        if(strlen($message) > 0 and $order_id > 0) {
            $ok = false;
            if ($this->user instanceof \Users) {
                $user_id = $this->user->getId();
                if ($is_public_profile) {
                    $username = $this->user->getFullName();
                }
                $context = new Chat($message);
                $created_time = new \MongoDate();
                $time = date('h:i:s d/m/Y', $created_time->sec);
                $type_context = BaseContext::TYPE_CHAT;
                $ok = \OrderComment::addComment($user_id, $order_id, $type, $context, $is_public_profile,
                    $type_context);
            }
            if($ok){
                $info = array('username' => $username, 'message' => $message, 'time' => $time,
                    'user_id' => $user_id, 'img_path' => $img_path);
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->type_chat = $type;
                $ajax->info = $info;
                $ajax->format = 'JSON';

                return $this->renderText($ajax->toString());
            } else {
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->msg = 'Lỗi khi lưu dữ liệu!';
                $ajax->format = 'JSON';

                return $this->renderText($ajax->toString());
            }
        }
    }

    public function executeChangeWaitingDelivery() {
        $this->user = \BaseAuth::getInstance()->getUser();
        if (!$this->isAllowed(PERMISSION_ORDER_CHANGE_WAIT_DELIVERY)) {
            return $this->renderText(\AjaxResponse::responseError("Bạn không có quyền thực hiện thao tác này"));
        }

        $order_id = $this->post('order_id', 'INT', 0);
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            return $this->renderText(\AjaxResponse::responseError("Không tìm thấy order"));
        }

        if (!$order->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)
            || !(\Order::STATUS_CHECKED == $order->getStatus()
                || (!$order->needToChecking() && $order->isAfterStatus(\Order::STATUS_BOUGHT, true)))) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đang ở trạng thái {$order->getStatus()}, không thể chuyển trạng thái chờ giao"));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này."));
        }

        if ($order->changeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)) {
            //dispatch Event - quyen
            $this->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event($this, array(
                'order' => $order,
                'message' => 'Chuyển trạng thái đơn hàng sang chờ giao',
                'user_id' => $this->user->getId()
            )));
            return $this->renderText(\AjaxResponse::responseSuccess("Chuyển trạng thái đơn hàng thành công"));
        } else {
            return $this->renderText(\AjaxResponse::responseError("Lỗi kỹ thuật, không thể chuyển trạng thái đơn hàng"));
        }
    }

    public function executeChangeTransporting() {
        $this->user = \BaseAuth::getInstance()->getUser();
        if (!$this->isAllowed(PERMISSION_ORDER_CHANGE_TRANSPORTING)) {
            return $this->renderText(\AjaxResponse::responseError("Bạn không có quyền thực hiện thao tác này"));
        }

        $order_id = $this->post('order_id', 'INT', 0);
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            return $this->renderText(\AjaxResponse::responseError("Không tìm thấy order"));
        }

        if( $order->getStatus() != \Order::STATUS_RECEIVED_FROM_SELLER ) {
            return $this->renderText(\AjaxResponse::responseError("Trạng thái đơn hàng không thể chuyển sang 'Vận Chuyển'"));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này."));
        }

        $order->beginTransaction();
        try {
            $order->changeStatus(\Order::STATUS_TRANSPORTING);
            $order->commit();

            $this->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event($this, array(
                'order' => $order,
                'message' => 'Chuyển trạng thái đơn hàng sang vận chuyển',
                'user_id' => $this->user->getId(),
                'staff' => $this->user
            )));
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Chuyển trạng thái đơn hàng thành công";
            return $this->renderText(\AjaxResponse::responseSuccess("Chuyển trạng thái đơn hàng thành công"));
        } catch(\Exception $e) {

            $order->rollBack();
            return $this->renderText(\AjaxResponse::responseError("Lỗi kỹ thuật, không thể chuyển trạng thái đơn hàng"));
        }
    }

    public function executeChangeRequestDelivery() {
        $this->user = \BaseAuth::getInstance()->getUser();
        if (!$this->isAllowed(PERMISSION_ORDER_CHANGE_REQUEST_DELIVERY)) {
            return $this->renderText(\AjaxResponse::responseError("Bạn không có quyền thực hiện thao tác này"));
        }

        $order_id = $this->post('order_id', 'INT', 0);
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            return $this->renderText(\AjaxResponse::responseError("Không tìm thấy order"));
        }

        if(!$order->checkRequestDeliver()) {//check delivery conditions first
            return $this->renderText(\AjaxResponse::responseError("Trạng thái đơn hàng không thể chuyển sang 'Yêu cầu giao'"));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này."));
        }

        if (\OrderPeer::requestDeliveryOrder($order)) {
            //dispatch Event - quyen
            $this->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event($this, array(
                'order' => $order,
                'message' => 'Chuyển trạng thái đơn hàng sang yêu cầu giao hàng',
                'user_id' => $this->user->getId(),
                'staff' => $this->user
            )));

            //update packages status of order
            $package_list = \Packages::retrieveByOrderId($order_id);
            if ($package_list) {
                foreach ($package_list as $package) {
                    if ($package instanceof \Packages) {
                        if ($package->isStatus(\Packages::STATUS_WAITING_FOR_DELIVERY)) {
                            $package->changeStatus(\Packages::STATUS_CUSTOMER_CONFIRM_DELIVERY);
                        }
                    }
                }
            }
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Chuyển trạng thái đơn hàng thành công";
            $ajax->warehouse_out = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_OUT ? 1 : 0;
            return $this->renderText(\AjaxResponse::responseSuccess("Chuyển trạng thái đơn hàng thành công"));
        } else {
            return $this->renderText(\AjaxResponse::responseError("Lỗi kỹ thuật, không thể chuyển trạng thái đơn hàng"));
        }
    }

    public function executeChangeDelivery() {
        $this->user = \BaseAuth::getInstance()->getUser();
        if (!$this->isAllowed(PERMISSION_ORDER_CHANGE_DELIVERY)) {
            return $this->renderText(\AjaxResponse::responseError("Bạn không có quyền thực hiện thao tác này"));
        }

        $order_id = $this->post('order_id', 'INT', 0);
        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            return $this->renderText(\AjaxResponse::responseError("Không tìm thấy order"));
        }

        if (\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY != $order->getStatus()) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đang ở trạng thái {$order->getStatus()}, không thể chuyển trạng thái đang giao"));
        }

        if( $order->getStatus() == \Order::STATUS_OUT_OF_STOCK ) {
            return $this->renderText(\AjaxResponse::responseError("Đơn hàng đã chuyển sang trạng thái hết hàng. Bạn không thể thực hiện thao tác này."));
        }

        if ($order->changeStatus(\Order::STATUS_DELIVERING)) {

            //dispatch Event - quyen
            $this->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event($this, array(
                'order' => $order,
                'message' => 'Chuyển trạng thái đơn hàng sang đang giao',
                'user_id' => $this->user->getId()
            )));
            //update packages status of order
            $package_list = \Packages::retrieveByOrderId($order_id);
            if ($package_list) {
                foreach ($package_list as $package) {
                    if ($package instanceof \Packages) {
                        if ($package->isStatus(\Packages::STATUS_CUSTOMER_CONFIRM_DELIVERY)) {
                            $package->changeStatus(\Packages::STATUS_DELIVERING);

                        }
                    }
                }
            }
            return $this->renderText(\AjaxResponse::responseSuccess("Chuyển trạng thái đơn hàng thành công"));
        } else {
            return $this->renderText(\AjaxResponse::responseError("Lỗi kỹ thuật, không thể chuyển trạng thái đơn hàng"));
        }
    }
} 