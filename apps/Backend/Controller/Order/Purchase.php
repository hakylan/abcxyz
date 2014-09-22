<?php
namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use Flywheel\Event\Event;
use SeuDo\Main;
use SeuDo\Order\PurchaseAllocation;
use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Factory;
use Flywheel\Redis\Client;
use \SeuDo\Logger;
use mongodb\NotificationUser;

class Purchase extends BackendBase {
    const TIME_WAIT = 300;
    public $canChangeOrder = array(\Order::STATUS_BUYING, \Order::STATUS_NEGOTIATING);
    public $rounding_config = null;

    /**
     * @var \Users
     */
    public $user;
    public $condition_staff = "";
    /**
     * @var \SeuDo\Logger::factory("");
     */
    public $logger = null;

    public $logger_choose_services = null;

    public $is_public_profile = false;
    public $is_external = false;
//    public $permission = "";

    public function beforeExecute() {
        parent::beforeExecute();
        $this->rounding_config = ConfigHandler::get('money_rounding');
        $this->user = \BackendAuth::getInstance()->getUser();
        $this->condition_staff = "(tellers_id={$this->user->getId()} OR paid_staff_id={$this->user->getId()})";
        $this->logger = Logger::factory("Purchase_Payment_Backend");
        $this->logger_choose_services = Logger::factory("Purchase_Payment_Choose_Services");


        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $this->is_public_profile = true;
        }

        if($this->isAllowed(PERMISSION_COMMUNICATE_CUSTOMER)){
            $this->is_external = true;
        }

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('logOrderComment', array(new \BackendEvent(), 'logOrderComment'));
    }

    public function executeDefault()
    {
//        echo 'hello';exit;
        if(!$this->isAllowed(PERMISSION_ORDER_PAYMENT) && !$this->isAllowed(PERMISSION_PURCHASE_ORDER)){
            $this->raise403();
            exit();
        }

        $this->setView('OrderPurchasePayment/default');
        $this->setLayout('order_paid');
        $this->document()->title = "Mua hàng & thanh toán";
        $status = $this->request()->get("status","STRING",\Order::STATUS_BUYING);
        if($this->isAllowed(PERMISSION_ORDER_PAYMENT) && !$this->isAllowed(PERMISSION_PURCHASE_ORDER)){
            $status = \Order::STATUS_NEGOTIATED;
        }
        $site = $this->request()->get("site_origin","STRING","all");
        $page = $this->request()->get("page","INT",1);
        $customer_confirm = $this->request()->get("customer_confirm","STRING",\Order::CUSTOMER_CONFIRM_NONE);

        //$rounding_config = \Flywheel\Config\ConfigHandler::get('money_rounding');
        $this->document()->addJsVar('rounding_config', json_encode($this->rounding_config));

        $this->document()->addJsVar("LinkLoadOrder",$this->createUrl("Order/Purchase/LoadOrderList"));
        $this->document()->addJsVar("LinkOrderPaid",$this->createUrl("order/Purchase"));
        $this->document()->addJsVar("LinkAddComment",$this->createUrl("Order/Purchase/add_comment"));
        $this->document()->addJsVar("LinkChangeQuantity",$this->createUrl("Order/Purchase/ChangeQuantity"));
        $this->document()->addJsVar("LinkChangeDomesticFee",$this->createUrl("Order/Purchase/ChangeDomesticFee"));
        $this->document()->addJsVar("UpdateOrderMoney",$this->createUrl("Order/Purchase/UpdateOrderMoney"));
        $this->document()->addJsVar("ChangeInvoice",$this->createUrl("Order/Purchase/ChangeInvoice"));
        $this->document()->addJsVar("ChangeAlipay",$this->createUrl("Order/Purchase/ChangeAlipay"));
        $this->document()->addJsVar("AutopaiLink",$this->createUrl("Order/Purchase/AutoPai"));
        $this->document()->addJsVar("ChangeStatusLink",$this->createUrl("Order/Purchase/ChangeStatus"));
        $this->document()->addJsVar("SelectAccountPurchase",$this->createUrl("Order/Purchase/SelectAccountPurchase"));
        $this->document()->addJsVar("LinkAddCommentItem",$this->createUrl("OrderCommon/CommentItemOrder"));
        $this->document()->addJsVar("LinkEditPrice",$this->createUrl("OrderCommon/EditPriceItem"));
        $this->document()->addJsVar("out_of_stock_url",$this->createUrl('OrderCommon/out_of_stock'));
        $this->document()->addJsVar("ChooseServicesLink",$this->createUrl('order_common/choose_services'));
        // dau's
        $this->document()->addJsVar("linkAddOrderItemComment", $this->createUrl('Order/OrderItemComment/AddMessage'));
        $this->document()->addJsVar("LoadOrderCommentUrl",$this->createUrl('Order/OrderComment/ListOrderComments'));
        $this->document()->addJsVar("linkAddOrderComment",$this->createUrl('Order/OrderComment/AddMessage'));
        $this->document()->addJsVar("buildArrayOrderPurchase", $this->createUrl('Order/OrderItemComment/AddMessage'));
        $this->document()->addJsVar("linkMoreOrderItemComment", $this->createUrl('Order/OrderItemComment/ListMoreComments'));

        //info curren user
        $this->document()->addJsVar('first_name', $this->user->getFirstName());
        $this->document()->addJsVar('current_username', $this->user->getFullName());
        $this->document()->addJsVar('current_user_id', $this->user->getId());
        $this->document()->addJsVar('current_img_path', \Users::getAvatar32x($this->user));

        $orderWaiting = \Order::read()->select('COUNT(*) AS `cnt`')->andWhere("`status`='" . \Order::STATUS_DEPOSITED . "'")->execute()->fetch();

        $queryCountBuying =  \Order::read()->andWhere("{$this->condition_staff} AND status='". \Order::STATUS_BUYING ."' and customer_confirm='". \Order::CUSTOMER_CONFIRM_NONE ."'");
        $queryCountNegotiating =  \Order::read()->andWhere("{$this->condition_staff} AND status='". \Order::STATUS_NEGOTIATING ."' and customer_confirm='". \Order::CUSTOMER_CONFIRM_NONE ."'");
        if($this->isAllowed(PERMISSION_PURCHASE_ORDER) && !$this->isAllowed(PERMISSION_ORDER_PAYMENT)){
            $queryCountNegotiated =  \Order::read()->andWhere("tellers_id={$this->user->getId()} AND status='". \Order::STATUS_NEGOTIATED ."' and customer_confirm='". \Order::CUSTOMER_CONFIRM_NONE ."'");
        }else if($this->isAllowed(PERMISSION_ORDER_PAYMENT)){
            $queryCountNegotiated = \Order::read()->andWhere("status='". \Order::STATUS_NEGOTIATED ."' and customer_confirm='". \Order::CUSTOMER_CONFIRM_NONE ."'");
        }else{
            $queryCountNegotiated = array();
        }
        $status_negotiated = \Order::STATUS_NEGOTIATED;

        if(!$this->isAllowed(PERMISSION_ORDER_PAYMENT)){
            $queryCountConfirm = \Order::read()->andWhere("{$this->condition_staff} and customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."'");
        }else if(!$this->isAllowed(PERMISSION_PURCHASE_ORDER)){
            $queryCountConfirm = \Order::read()->andWhere("(paid_staff_id={$this->user->getId()} AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."')
                         OR (status='{$status_negotiated}' AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."')");
        }else{
            $queryCountConfirm = \Order::read(); //
            $queryCountConfirm->andWhere("{$this->condition_staff} AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."'");
            $queryCountConfirm->orWhere("status='{$status_negotiated}' AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."'");
        }

        if(!$this->isAllowed(PERMISSION_ORDER_PAYMENT)){
            $queryCountConfirmed = \Order::read()->andWhere("{$this->condition_staff} and customer_confirm='". \Order::CUSTOMER_CONFIRM_CONFIRMED ."'");
        }else if(!$this->isAllowed(PERMISSION_PURCHASE_ORDER)){
            $queryCountConfirmed = \Order::read()->andWhere("(paid_staff_id={$this->user->getId()} AND customer_confirm='". \Order::CUSTOMER_CONFIRM_CONFIRMED ."')
                         OR (status='{$status_negotiated}' AND customer_confirm='". \Order::CUSTOMER_CONFIRM_CONFIRMED ."')");
        }else{
            $queryCountConfirmed = \Order::read(); //
            $queryCountConfirmed->andWhere("{$this->condition_staff} AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."'");
            $queryCountConfirmed->orWhere("status='{$status_negotiated}' AND customer_confirm='". \Order::CUSTOMER_CONFIRM_WAIT ."'");
        }

        $queryCountBought =  \Order::read()->andWhere("{$this->condition_staff} and status='". \Order::STATUS_BOUGHT ."'");
        $queryCountOutOf =  \Order::read()->andWhere("{$this->condition_staff} and (status='". \Order::STATUS_OUT_OF_STOCK ."' OR is_deleted=1)");

        $countBuying = \OrderPeer::countOrder($queryCountBuying);
        $countNegotiating = \OrderPeer::countOrder($queryCountNegotiating);
        $countNegotiated = \OrderPeer::countOrder($queryCountNegotiated);
        $countBought = \OrderPeer::countOrder($queryCountBought);
        $countOutOf = \OrderPeer::countOrder($queryCountOutOf,true);
        $countConfirm = \OrderPeer::countOrder($queryCountConfirm);
        $countConfirmed = \OrderPeer::countOrder($queryCountConfirmed,true);

        $this->view()->assign("user",$this->user);
        $this->view()->assign("canChangeOrder",$this->canChangeOrder);
        $this->view()->assign("status",$status);
        $this->view()->assign("site",$site);
        $this->view()->assign("page",$page);
        $this->view()->assign("customer_confirm",$customer_confirm);

        $this->view()->assign(
            array(
                "orderWaiting" => $orderWaiting['cnt'],
                "countBuying" => $countBuying,
                "countNegotiating" => $countNegotiating,
                "countNegotiated" => $countNegotiated,
                "countBought" => $countBought,
                "countOutOf" => $countOutOf,
                "countConfirm" => $countConfirm,
                "countConfirmed" => $countConfirmed,
            )
        );

        return $this->renderComponent();
    }

    public function executeLoadOrderList(){

        $this->validAjaxRequest();

        $query = \Order::read();

        // Request state of order, default: buying, negotiating
        $status = $this->request()->get('status');
        $site_origin = $this->request()->get('site_origin');
        $page = $this->request()->get("page","INT",1);
        $customer_confirm = $this->request()->get("customer_confirm","STRING",\Order::CUSTOMER_CONFIRM_NONE);

        $confirm_condition = $customer_confirm == \Order::CUSTOMER_CONFIRM_NONE
            ? "(customer_confirm='{$customer_confirm}' || customer_confirm = '')"
            : "customer_confirm='{$customer_confirm}'";

        $num_show = 15;
        $offset = ($page - 1) * $num_show;

        if($site_origin != 'all'){
            $site_origin = strtoupper($site_origin);
            $query->andWhere("seller_homeland='{$site_origin}'");
        }
        if($status == \Order::STATUS_NEGOTIATED){
            if($this->isAllowed(PERMISSION_ORDER_PAYMENT)){
                $query->andWhere("status='{$status}' AND $confirm_condition");
            }else{
                $query->andWhere("{$this->condition_staff} AND status='{$status}' AND $confirm_condition");
            }
        }else if($status == 'all'){

            $status_negotiated = \Order::STATUS_NEGOTIATED;
            if(!$this->isAllowed(PERMISSION_ORDER_PAYMENT)){
                $query->andWhere("{$this->condition_staff} AND $confirm_condition");
            }else if(!$this->isAllowed(PERMISSION_PURCHASE_ORDER)){
                $query = \Order::read()->andWhere("(paid_staff_id={$this->user->getId()} AND {$confirm_condition})
                         OR (status='{$status_negotiated}' AND {$confirm_condition})");
            }else{
                $query = \Order::read(); //
                $query->andWhere("{$this->condition_staff} AND {$confirm_condition}");
                $query->orWhere("status='{$status_negotiated}' AND {$confirm_condition}");
            }

        }else{
            $query->andWhere("{$this->condition_staff} and status='{$status}' AND $confirm_condition");
        }

        switch ($status){
            case \Order::STATUS_BUYING:
                $query->orderBy("deposit_time","ASC");
                break;
            case \Order::STATUS_NEGOTIATING:
                $query->orderBy("buying_time","ASC");
                break;
            case \Order::STATUS_NEGOTIATED:
                $query->orderBy("negotiating_time","ASC");
                break;
            case \Order::STATUS_BOUGHT:
                $query->orderBy("negotiated_time","DESC");
                break;
            case \Order::STATUS_OUT_OF_STOCK:
                $query->orderBy("id","ASC");
                break;
        }

        if($customer_confirm == \Order::CUSTOMER_CONFIRM_WAIT){
            $query->orderBy("confirm_created_time","ASC");
        }else if($customer_confirm == \Order::CUSTOMER_CONFIRM_CONFIRMED){
            $query->orderBy("confirm_approval_time","ASC");
        }

        $query->setFirstResult($offset)->setMaxResults($num_show);

        $orders = \OrderPeer::getOrder($query);

        $orderDatas = \OrderPeer::buildArrayOrderPurchase($orders);

        $ajax = new \AjaxResponse();

        $ajax->type = \AjaxResponse::SUCCESS;

        $ajax->orders_list = $orderDatas;

        // Count order in status "DEPOSITED"

        return $this->renderText($ajax->toString());
    }

    public function executeSelectAccountPurchase(){
        $account = $this->request()->post("username");
        $order_id = $this->request()->post("order_id");
        $order = \Order::retrieveById($order_id);
        if($order instanceof \Order){
            $order_old = $order;
            $order->setAccountPurchaseOrigin($account);
            if($order->save()){
                //dispatch Event - quyen
                $this->dispatch('afterSelectAccountPurchase', new Event($this, array(
                    'order' => $order,
                    'user' => $this->user,
                    'order_old' => $order_old
                )));

                $response = array(
                    "type" => \AjaxResponse::SUCCESS,
                    "message" => "Thành công"
                );
                return $this->renderText(json_encode($response));
            }else{
                $response = array(
                    "type" => \AjaxResponse::ERROR,
                    "message" => "Thất bại"
                );
                return $this->renderText(json_encode($response));
            }
        }else{
            $response = array(
                "type" => \AjaxResponse::ERROR,
                "message" => "Không tòn tại đơn hàng"
            );
            return $this->renderText(json_encode($response));
        }
    }

    public function executeAddComment()
    {
        $this->validAjaxRequest();
        $user = \BackendAuth::getInstance()->getUser();
        $content = $this->post('content');
        $orderId = $this->post('orderId');

        $order = \Order::retrieveById($orderId);
        if ($content == '') {
            $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Chưa điền nội dung comment');
            return $this->renderText($ajax->toString());
        }
        if (!$order) {
            $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Đơn hàng không tồn tại');
            return $this->renderText($ajax->toString());
        }
        $check = \OrderPeer::addOrderComment($order, $user, $content, \OrderComment::TYPE_EXTERNAL);
        if ($check === true) {
            $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'SUCCESS');
            return $this->renderText($ajax->toString());
        }
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'FAIL');
        return $this->renderText($ajax->toString());
    }

    public function executeRequestOrder()
    {
        $this->validAjaxRequest();
        $user = \BackendAuth::getInstance()->getUser();
        $res = new \AjaxResponse(\AjaxResponse::SUCCESS);

        //check permission first
        if(!$this->isAllowed(PERMISSION_PURCHASE_ORDER)) {
            $res->type = \AjaxResponse::ERROR;
            $res->message = t("Bạn không có quyền giao dịch và mua hàng");

            return $this->renderText($res->toString());
        }

        //check last request
        $cookie = Factory::getCookie();
        $lastRequest = $cookie->read('purchase_staff_last_request');
        $now = time();
        if ($lastRequest) {
            $diff = $now - $lastRequest;
            if ($diff < self::TIME_WAIT) {
                $wait = self::TIME_WAIT - $diff;
                $res->type = \AjaxResponse::WARNING;
                $res->message = self::t("Bạn đặt hàng rất nhanh. Hãy đợi {$wait} giây để yêu cầu tiếp",
                    array('%wait%' => $wait));

                return $this->renderText($res->toString());
            }
        }
        $cookie->write('purchase_staff_last_request', $now, 1500);

        //check current orders received
        $currentOrders = \Order::read()->count('id')
            ->where('`status` = :status AND `tellers_id` = :teller')
            ->setParameter(':status', \Order::STATUS_BUYING, \PDO::PARAM_STR)
            ->setParameter(':teller', $user->getId(), \PDO::PARAM_INT)
            ->execute();
        $maxOrderAllocated = \SystemConfig::retrieveByKey(\SystemConfig::MAX_PURCHASE_ORDERS_ALLOCATED);
        if ($currentOrders > (int)$maxOrderAllocated->getConfigValue()) {
            $res->type = \AjaxResponse::WARNING;
            $res->message = t("Đừng vội vàng thế. Bạn chưa xử lý xong %current_order% đơn hàng.",
                array("%current_order%" => $currentOrders));

            return $this->renderText($res->toString());
        }

        //orders received during day
        $redisConn = Client::getConnection('purchase_allocation');
        $today = (int)$redisConn->get(REDIS_ORDER_PURCHASE_ALLOCATED_BY_DAY . $user->getUsername() . date('Y-m-d'));
        $maxOrderAllocatedByDay = \SystemConfig::retrieveByKey(\SystemConfig::ORDER_MAXIMUM_RECEIVED_A_DAY);
        $maxOrderAllocatedByDay = ($maxOrderAllocatedByDay) ? (int)$maxOrderAllocatedByDay->getConfigValue() : 9999;

        if ($today > $maxOrderAllocatedByDay) {
            $res->type = \AjaxResponse::WARNING;
            $res->message = t("Good job! Hôm nay bạn đã nhận mua %order_finished%. Hãy nghỉ ngơi đi!",
                array("%order_finished%" => $today));

            return $this->renderText($res->toString());
        }

        //allocate and assign teller
        $numberOrdersPerRequest = \SystemConfig::retrieveByKey(\SystemConfig::ORDER_MAXIMUM_RECEIVED_ONCE);
        $numberOrdersPerRequest = ($numberOrdersPerRequest) ? (int)$numberOrdersPerRequest->getConfigValue() : 10;

        $allocator = new PurchaseAllocation($user, $numberOrdersPerRequest);
        $orders = $allocator->allocate();
        $res->orders = array();
        foreach ($orders as &$order) {
            if($order instanceof \Order){
                $order->createRecipientName();
                $res->message = "OK";
                $res->type = \AjaxResponse::SUCCESS;
                $res->orders[] = $order->getCode();
            }
        }
        $res->total = count($res->orders);

        if (!empty($orders)) {
            //incr received orders number
            $redisConn->incrBy(REDIS_ORDER_PURCHASE_ALLOCATED_BY_DAY . $user->getUsername() . date('Y-m-d'), sizeof($orders));
        }

        $this->dispatch('onAssignPurchaseOrder', new Event($this, array(
            'teller' => $user,
            'orders' => $res->orders
        )));

        // Return if error or warning
        return $this->renderText($res->toString());
    }

    /**
     * Change item quantity
     * Update pending quantity and update order info
     * @return string
     * @author binhnt
     */
    public function executeChangeQuantity()
    {
        $this->validAjaxRequest();

        $iid = $this->request()->post('iid');
        $quantity = $this->request()->post('quantity', 'INT', 0);
        $quantity_old = $this->request()->post('quantity_old', 'INT', 0);

        $logger = Logger::factory("order_change_quantity");

        $ajax = new \AjaxResponse();

        $item = \OrderItem::retrieveById($iid);
        if (empty($item)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không tồn tại sản phẩm.";

            return $this->renderText($ajax->toString());
        }
        $order = \Order::retrieveById($item->getOrderId());

        if (empty($order) || !($order instanceof \Order)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không tồn tại đơn hàng của sản phẩm.";

            return $this->renderText($ajax->toString());
        }
        $item->beginTransaction();
        try {

            //Customer Confirm
            if(($quantity_old - $quantity) < 0){
                if($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)){
                    $note = "Nhân viên {$this->user->getFullname()} - @{$this->user->getUsername()} thay đổi số lượng từ {$item->getPendingQuantity()} thành {$quantity}";
                    $note_order = "Nhân viên {$this->user->getFullname()} - @{$this->user->getUsername()} thay đổi đơn giá hoặc số lượng của sản phẩm trong đơn hàng, kiểm tra mục chat của đơn hàng để biết chi tiết";
                }else{
                    $note = "Hệ thống vừa thay đổi số lượng từ {$item->getPendingQuantity()} thành {$quantity}";
                    $note_order = "Hệ thống vừa thay đổi đơn giá hoặc số lượng của sản phẩm trong đơn hàng, kiểm tra mục chat của đơn hàng để biết chi tiết";
                }

                $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_WAIT);

                $order->setNoteCustomerConfirm($note_order);

                $order->setConfirmCreatedTime(new DateTime());

                $item->setNoteSystem($note);

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Đơn hàng cần xác nhận: Điều chỉnh số lượng mua của sản phẩm với mã
                        {$item->getId()} từ {$quantity_old} thành {$quantity}đ.",
                    "is_external" => true,
                    'is_log' => true
                )));
            }
            $content = "Sửa số lượng sản phẩm với mã {$item->getId()} từ {$quantity_old} thành {$quantity}";

            if ($item->updateOrderItemQuantity($quantity)) {

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => $content,
                    "is_public" => $this->is_public_profile,
                    "is_external" => true,
                    "is_log" => true,
                )));

                $this->dispatch('afterEditQuantityItem', new Event($this, array(
                    'order_item' => $item,
                    'order' => $order,
                    "message" => "Điều chỉnh số lượng sản phẩm từ {$quantity_old}đ thành {$quantity}đ.",
                    "staff" => $this->user
                )));

                $item->commit(); // Commit transaction
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Lưu thành công.";
                $price_cny = $item->getPricePromotion() > 0 ? $item->getPricePromotion() :
                    $item->getPriceOrigin();
                if(floatval($price_cny) <= 0){
                    $price_cny = floatval($item->getPricePromotion()) > 0 ? $item->getPricePromotion() : floatval($item->getPriceOrigin());
                    $price_cny = $price_cny > 0 ? $price_cny : $item->getPrice() / \ExchangeRate::getExchange();
                }
                $ajax->total_item_price_ndt = \Common::numberFormat($price_cny*$item->getPendingQuantity());
                $ajax->total_item_price_vnd = \Common::numberFormat($item->getPrice()*$item->getPendingQuantity());
                $ajax->total_order_price_ndt = \Common::numberFormat($order->getRealAmountNdt());
                if($ajax->total_order_price_ndt == 0){
                    $ajax->total_order_price_ndt = \Common::numberFormat($order->getRealAmount() / \ExchangeRate::getExchange());
                }
                $ajax->total_order_price_vnd = \Common::numberFormat($order->getRealAmount());
                $ajax->total_item_quantity = $order->getTotalPendingQuantity();

                $logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} change quantity from {$quantity_old} to {$quantity} with order code {$order->getCode()}. Status : {$order->getStatus()}");

                return $this->renderText($ajax->toString());
            }else{
                $logger->addWarning("User {$this->user->getUsername()} - {$this->user->getFullName()} can't change quantity from {$quantity_old} to {$quantity} with order code {$order->getCode()}. Status : {$order->getStatus()}");
                $item->rollBack();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Trạng thái này không thể thay đổi số lượng.";
                return $this->renderText($ajax->toString());
            }
        } catch (Exception $e) {
            $logger->addWarning("User {$this->user->getUsername()} - {$this->user->getFullName()} can't change quantity from {$quantity_old} to {$quantity} with order code {$order->getCode()}. Status : {$order->getStatus()} ".$e->getMessage());
            $item->rollBack();
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không thành công, xin thử lại.";
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Update total order money
     * @status: pending, đang đ?i Trung thêm d? li?u update thông tin này
     * @author binhnt
     */
    public function executeUpdateOrderMoney()
    {
        $this->validAjaxRequest();
        $oid = $this->request()->post('oid');
        $amount = $this->request()->post('amount');
        $order = \Order::retrieveById($oid);

        $ajax = new \AjaxResponse();
        if (empty($order)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Trên hệ thống không tồn tại đơn hàng.";
            return $this->renderText($ajax->toString());
        }
        $order_old = $order;

        /*if(!in_array($order->getStatus(), $this->canChangeOrder)) {
            return $this->renderText(json_encode(array(
                'status' => false,
                'msg'   => 'Không th? thay đ?i ti?n hàng c?a đơn hàng ? tr?ng thái "' . $order->getStatusTitle() . '"'
            )));
        }*/
        $order->setDirectFillAmountCny($amount);
        $order->setDirectFillAmountVnd($amount * \ExchangeRate::getExchange());
        $direct_vnd = $amount * \ExchangeRate::getExchange();
        if ($order->save()) {

            $content = "Cập nhật tổng giá trị đơn thành {$direct_vnd} VNĐ";

            $this->dispatch('logOrderComment', new Event($this, array(
                'order' => $order,
                'message' => $content,
                "is_activity" => true,
                "is_external" => false,
            )));
            //dispatch Event - quyen
            $this->dispatch('afterChangeDirectFillAmount', new Event($this, array(
                'order' => $order,
                'order_old' => $order_old
            )));
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Lưu thành công.";
            return $this->renderText($ajax->toString());
        }
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->message = "Lỗi hệ thống, liên hệ kỹ thuật để được hỗ trợ";
        return $this->renderText($ajax->toString());
    }

    /**
     * Update domestic shipping fee
     * @return string
     * @author quyenminh
     */
    public function executeChangeDomesticFee()
    {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        try{
            $oid = $this->request()->post('oid');
            $amount = $this->request()->post('amount');
            $order = \Order::retrieveById($oid);
            if (empty($order)) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tồn tại đơn hàng.";
                return $this->renderText($ajax->toString());
            }
            $domestic_vnd = $amount * \ExchangeRate::getExchange();

            $order_old = $order;
            $order->setDomesticShippingFee($amount);
            $order->setDomesticShippingFeeVnd(\Common::roundingMoney($domestic_vnd));
            if ($order->updateInfo()) {

                //dispatch Event - quyen
                $this->dispatch('afterChangeDomesticShippingFee', new Event($this, array(
                    'order' => $order,
                    'order_old' => $order_old,
                    'staff' => $this->user
                )));
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Thành công.";
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Thất bại, liên hệ Kỹ thuật để được hỗ trợ";
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi kĩ thuật, liên hệ Kỹ thuật để được hỗ trợ ".$e->getMessage();
            return $this->renderText($ajax->toString());
        }

    }

    /**
     * Thay doi mã đơn trên Site Gốc
     * @return string
     * @author quyenminh
     */
    public function executeChangeInvoice()
    {
        $this->validAjaxRequest();
        $oid = $this->request()->post('oid');
        $data = $this->request()->post('data');
        $order = \Order::retrieveById($oid);
        $ajax = new \AjaxResponse();
        if (empty($order)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không tồn tại đơn hàng này.";
            return $this->renderText($ajax->toString());
        }
        $order_old = $order;
        $order->setInvoice($data);
        if ($order->updateInfo()) {
            //dispatch Event - quyen
            $this->dispatch('afterChangeInvoice', new Event($this, array(
                'order' => $order,
                'order_old' => $order_old,
                'staff' => $this->user
            )));
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Thành công.";
            return $this->renderText($ajax->toString());
        }
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->message = "Thất bại.";
        return $this->renderText($ajax->toString());
    }

    /**
     * Change status order
     */
    public function executeChangeStatus()
    {
        $oid = $this->request()->post('oid');
        $status = $this->request()->post('status');
        $ajax = new \AjaxResponse();
        try{
            $status_before = $status;
            $pvc_tq = $this->request()->post('pvc_tq',"FLOAT",0);
            $total_money = $this->request()->post('total_money',"FLOAT",0);
            $order_code_origin = $this->request()->post('order_code_origin',"STRING","");
            $account = $this->request()->post('account',"STRING","");

            $order = \Order::retrieveById($oid);

            if(empty($order)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tồn tại đơn hàng";
                return $this->renderText($ajax->toString());
            }

            if ($status != "") {
                switch (strtoupper($status)) {
                    case \Order::STATUS_BUYING:
                        if($order_code_origin == ''){
                            $ajax->type = \AjaxResponse::ERROR;
                            $ajax->message = "Không được bỏ trống đơn trên Site gốc";
                            return $this->renderText($ajax->toString());
                        }
                        $status = \Order::STATUS_NEGOTIATING;
                        $order->setNegotiatingTime(new \DateTime());
                        break;
                    case \Order::STATUS_NEGOTIATING:
                        $status = \Order::STATUS_NEGOTIATED;
                        $order->setPaymentLink($this->request()->post('payment_link'));
                        $order->setNegotiatedTime(new \DateTime());
                        break;
                    case \Order::STATUS_NEGOTIATED:
                        $payment_link = $this->request()->post('payment_link');
                        if($payment_link != ""){
                            $order->setPaymentLink($payment_link);
                        }
                        $status = \Order::STATUS_BOUGHT;
                        if($order_code_origin == '' || $total_money == 0){
                            $ajax->type = \AjaxResponse::ERROR;
                            $ajax->message = "Thanh toán đơn cần đầy đủ các thông tin : Tổng GT đơn, Mã đơn trên Site gốc.";
                            return $this->renderText($ajax->toString());
                        }

                        $order->setPaidStaffId($this->user->getId());

                        $order->setPaidStaffAssignedTime(new \DateTime());

                        $order->setBoughtAmount($order->getRealAmount());

                        $order->setBoughtTime(new \DateTime());

                        break;
                    case \Order::STATUS_BOUGHT:
                        $status = \Order::STATUS_CHECKING;
                        $order->setCheckingTime(new \DateTime());
                        break;
                    case \Order::CUSTOMER_CONFIRM_WAIT:
                        $exchange = \ExchangeRate::getExchange();
                        $order->setDomesticShippingFee($pvc_tq);
                        $order->setDomesticShippingFeeVnd(\Common::roundingMoney($pvc_tq * $exchange));
                        $order->setDirectFillAmountCny($total_money);
                        $order->setDirectFillAmountVnd($total_money * $exchange);
                        $order->setInvoice($order_code_origin);

                        if($account != 0 || $account != ""){
                            $order->setAccountPurchaseOrigin($account);
                        }
                        $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_WAIT);
                        $order->setConfirmCreatedTime(new DateTime());
                        if($order->updateInfo()){
                            $ajax->type = \AjaxResponse::SUCCESS;
                            $ajax->message = "Thay đổi thành công.";
                            return $this->renderText($ajax->toString());
                        }
                        break;

                    default:
                        break;
                }
            }

            if($order->getCustomerConfirm() == \Order::CUSTOMER_CONFIRM_CONFIRMED){
                $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_NONE);
            }

            $exchange = \ExchangeRate::getExchange();
            $order->setDomesticShippingFee($pvc_tq);
            $order->setDomesticShippingFeeVnd(\Common::roundingMoney($pvc_tq * $exchange));
            $order->setDirectFillAmountCny($total_money);
            $order->setDirectFillAmountVnd($total_money * $exchange);
            $order->setInvoice($order_code_origin);

            if($account != 0 || $account != ""){
                $order->setAccountPurchaseOrigin($account);
            }

            // Set order with new status
            $result = $order->changeStatus($status);

            if ($result) {

                //dispatch Event - quyen

                $this->dispatch( ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event( $this, array(
                    'order' => $order,
                    'staff' => $this->user
                )));
                $this->logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} change status from {$status_before} to {$order->getStatus()} success. Order_id :{$order->getId()}");
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Thay đổi thành công.";
                return $this->renderText($ajax->toString());
            }else{
                $this->logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} change status from {$status_before} to {$order->getStatus()} not success. Order_id :{$order->getId()}");
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Lỗi hệ thống , liên hệ kỹ thuật để được hỗ trợ.";
                return $this->renderText($ajax->toString());
            }
        }catch (\Exception $e){
            $this->logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()}
                    change status not success. Order_id :{$oid} ".$e->getMessage());
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi hệ thống , liên hệ kỹ thuật để được hỗ trợ.";
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Set autopai for item
     */
    public function executeAutoPai()
    {
        $iid = $this->request()->post('iid');
        $item = \OrderItem::retrieveById($iid);
        if(empty($item)) {
            return $this->renderText(json_encode(array(
                'status' => false,
                'msg'   => 'Không tồn tại dữ liệu'
            )));
        }

        $item->setIsPaied(1);
        $item->setModifyTime(new DateTime());
//        $ct =
//        $this->setView()
        if($item->save()) {
            //dispatch Event - quyen
            $this->dispatch('afterAutoPai', new Event($this, array(
                'item' => $item,
                'user' => $this->user,
            )));
            return $this->renderText(json_encode(array(
                'status' => true,
                'msg'   => 'Auto pai success'
            )));
        }
        return $this->renderText(json_encode(array(
            'status' => false,
            'msg'   => 'Auto pai false'
        )));
    }

    public function executeGetCookie(){
        $this->setView("Order/Purchase/get_cookie");
        return $this->renderPartial();
    }

    public function executeResponseCookie(){
//        $ct = $_COOKIE["ct"];
        $ct = $this->request()->get("ct");
        $cookie = Factory::getCookie();
        if($ct != ''){
            $r = $cookie->write("ct_auto_pai",$ct,60*60*24*90);
        }
        if($cookie->read("ct_auto_pai") == $ct){
            return $this->renderText("OK");
        }else{
            return $this->renderText("Không thành công, xin thử lại");
        }
    }

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

        if (!in_array($order->getStatus(), array(
            \Order::STATUS_BUYING,
            \Order::STATUS_NEGOTIATING,
            \Order::STATUS_NEGOTIATED))) {
            $ajax->message = self::t('Đơn hàng không ở trạng thái cho phép hủy');
            return $this->renderText($ajax->toString());
        }

        try {
            $transaction = \OrderPeer::transitOutOfStock($order, "Trả lại đơn hàng {$order->getCode()} hết hàng khi mua");
            if ($transaction) {
                //dispatch Event
                $this->dispatch('onOrderTransitOutOfStock', new Event($this, array(
                    'order' => $order,
                    'transaction' => $transaction
                )));

                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "cho hết hàng của đơn với mã {$order->getCode()}",
                    "is_activity" => true,
                    "is_chat" => true
                )));

                $ajax->message = self::t('Thành công');
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->order = $order->toArray();
                $ajax->transacion = $transaction->getId();
            } else {
                $ajax->message = self::t('Lỗi kỹ thuật! Không thể thực hiện hết hàng, vui lòng thông báo kiểm tra!');
            }

            return $this->renderText($ajax->toString());
        } catch (\Exception $e) {
            throw $e;
        }
    }
} 