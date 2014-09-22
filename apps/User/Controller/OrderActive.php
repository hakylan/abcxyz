<?php
namespace User\Controller;

use Backend\Controller\Error;
use Flywheel\Session\Session;
use SeuDo\Main;
use SeuDo\Queue;
use \SeuDo\Logger;

class OrderActive extends UserBase
{
    /**
     * @var \Users
     */
    public $user = null;

    /**
     * @var \SeuDo\Logger::factory("")
     */
    public $logger_order = null;

    private $number_show = 25;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
        $this->logger_order = Logger::factory("order_delivery");
    }

    public function executeDefault(){
        $keyword = $this->request()->request("keyword","STRING",'');
        $order_status = $this->request()->request("status","STRING","OrderActive");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);

        $query = \Order::read();
        $query->andWhere("buyer_id={$this->user->getId()}");

        if($from_time != '' && $to_time != ''){
            $from_time = new \DateTime($from_time); //($from_time);
            $from_time = $from_time->format("Y:m:d H:i:s");
            $to_time = new \DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("created_time >= '{$from_time}' and created_time<='{$to_time}'");
        }
        if($keyword != ''){
            $query->andWhere("seller_name like '%{$keyword}%' or `code` like '%{$keyword}%'");
        }

        $status_out_of = \Order::STATUS_OUT_OF_STOCK;
        switch ($order_status){
            case 'OrderActive':
                $status_init = \Order::STATUS_INIT;
                $status_received = \Order::STATUS_RECEIVED;
                $query->andWhere("status != '{$status_init}' && status != '{$status_received}' AND is_deleted=0");
                $query->andWhere("status != '{$status_out_of}'");
                break;
            case 'DeletedOut':
                $is_delete = 1;
                $query->andWhere("status = '{$status_out_of}' OR is_deleted=1");
                break;
            case \Order::STATUS_OUT_OF_STOCK:
                $query->andWhere("status = '{$order_status}' AND is_deleted=0");
                break;
            case 'BEFORE_BOUGHT':
                $status_before_bought = \OrderPeer::getBetweenStatus(\Order::STATUS_DEPOSITED,\Order::STATUS_BOUGHT);
                $status_condition = "";
                foreach ($status_before_bought as $status) {
                    $status_condition .= "status = '{$status}' OR ";
                }
                $status_condition = substr($status_condition,0,strlen($status_condition) - 3);

                $query->andWhere($status_condition);
                $query->andWhere("is_deleted=0");
                break;
            default:
                $query->andWhere("status='{$order_status}'");
                $query->andWhere("is_deleted=0");
                break;
        }

        $query->orderBy("id","DESC");
        $total = $query->count('id')->execute();

        $total_page = ceil($total/$this->number_show);

        $document = $this->document();
        $document->title = "Đơn hàng hoạt động";
        $this->setView("Order/order_active");
        $url = Main::getUserRouter()->createUrl('OrderActive/load_order_active');
        $document->addJsVar('UrlLoadOrderActive',$url);
        $document->addJsVar('UrlSubmitDelivering',$this->createUrl("OrderActive/submit_delivering"),"TOP");
        $document->addJsVar('UrlSubmitConfirmDelivery',$this->createUrl("OrderActive/submit_confirm_delivering"),"TOP");

        $document->addJsVar('urlSynBalance',Main::getHomeRouter()->createUrl("order_deposit/syn_balance"),"TOP");
        $this->assign('UrlLoadOrderActive',$url);
        $this->view()->assign('keyword',$keyword);
        $this->view()->assign('from_time',$from_time);
        $this->view()->assign('to_time',$to_time);
        $this->view()->assign('status',$order_status);
        $this->view()->assign('page',$page);
        $this->view()->assign('total',$total);
        $this->view()->assign('total_page',$total_page);
        $document->addJsVar('OrderActiveUrl',Main::getUserRouter()->createUrl('OrderActive'));

        return $this->renderComponent();
    }


    /**
     * Load order active
     * @return string
     */
    public function executeLoadOrderActive(){

        $this->validAjaxRequest();
        $user_id = $this->user->getId();
        $keyword = $this->request()->request("keyword","STRING",'');
        $order_status = $this->request()->request("status","STRING","OrderActive");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $order_status = trim($order_status);

        $offset = ($page - 1) * $this->number_show;

        $query = \Order::read();
        $query->andWhere("buyer_id={$user_id}");
        $is_delete = 0;
        if($from_time != '' && $to_time != ''){
            $from_time = new \DateTime($from_time); //($from_time);
            $from_time = $from_time->format("Y:m:d H:i:s");
            $to_time = new \DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("created_time >= '{$from_time}' and created_time<='{$to_time}'");
        }
        if($keyword != ''){
            $query->andWhere("seller_name like '%{$keyword}%' or `code` like '%{$keyword}%'");
        }
        $status_out_of = \Order::STATUS_OUT_OF_STOCK;
        switch ($order_status){
            case 'OrderActive':
                $is_delete = 0;
                $status_init = \Order::STATUS_INIT;
                $status_received = \Order::STATUS_RECEIVED;
                $query->andWhere("status != '{$status_init}' && status != '{$status_received}'");
                $query->andWhere("status != '{$status_out_of}'");
                break;
            case 'DeletedOut':
                $is_delete = 1;
                $query->andWhere("status = '{$status_out_of}' OR is_deleted=1");
                break;
            case \Order::STATUS_OUT_OF_STOCK:
                $query->andWhere("status = '{$order_status}'");
                break;
            case 'BEFORE_BOUGHT':
                $status_before_bought = \OrderPeer::getBetweenStatus(\Order::STATUS_DEPOSITED,\Order::STATUS_BOUGHT);
                $status_condition = "";
                foreach ($status_before_bought as $status) {
                    $status_condition .= "status = '{$status}' OR ";
                }
                $status_condition = substr($status_condition,0,strlen($status_condition) - 3);

                $query->andWhere($status_condition);
                break;
            default:
                $query->andWhere("status='{$order_status}'");
                break;
        }

        $query->orderBy("id","DESC");
//        echo $query->getSQL();
        $query_count = clone $query;

        $query->setFirstResult($offset)->setMaxResults($this->number_show);

        $order_list = \OrderPeer::getOrder($query,$is_delete);
        $total = $query_count->count("id")->execute();

        $this->view()->assign('order_list',$order_list);
        $this->setView("Order/order_one");

        $ajax = new \AjaxResponse();
        if($order_status == \Order::STATUS_WAITING_FOR_DELIVERY){
            $order_list = $this->_buildOrderWaitingDelivery($order_list);
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->order_list = $order_list;
            $ajax->total = $total;
            return $this->renderText($ajax->toString());
        }

        $this->view()->assign('status',$order_status);
        $this->view()->assign('page',$page);

        $response = array(
            "total" => intval($total),
            "html_result" =>$this->renderPartial()
        );

        return $this->renderText(json_encode($response));
    }

    public function executeCountOrder(){
        $this->validAjaxRequest();
        $status = $this->request()->request('status');

        $sta = $status;

        $query = \Order::read()->andWhere('buyer_id='.$this->user->getId());

        if($status == 'OrderActive'){
            $status_init = \Order::STATUS_INIT;
            $status_received = \Order::STATUS_RECEIVED;
            $query->andWhere("status != '{$status_init}' && status != '{$status_received}'");
            $quantity = \OrderPeer::countOrderByQuery($query);
            $quantity =  intval($quantity) >=0 ? intval($quantity) : 0;
            $ajax = new \AjaxResponse();
            $ajax->data = $quantity;
            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }
        if($status == 'DeletedOut'){
            $status_out_of = \Order::STATUS_OUT_OF_STOCK;
            $query->andWhere("status = '{$status_out_of}' OR is_deleted=1");
            $quantity = \OrderPeer::countOrderByQuery($query,1);
            $quantity =  intval($quantity) >=0 ? intval($quantity) : 0;
            $ajax = new \AjaxResponse();
            $ajax->data = $quantity;
            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }
        if($status == \Order::CUSTOMER_CONFIRM_WAIT){
            $confirm_wait = \Order::CUSTOMER_CONFIRM_WAIT;
            $confirm_confirmed = \Order::CUSTOMER_CONFIRM_CONFIRMED;
            $query->andWhere("customer_confirm = '{$confirm_wait}' OR customer_confirm = '{$confirm_confirmed}'");
            $quantity = \OrderPeer::countOrderByQuery($query);
            $quantity =  intval($quantity) >=0 ? intval($quantity) : 0;
            $ajax = new \AjaxResponse();
            $ajax->data = $quantity;
            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }else if($status == "BEFORE_BOUGHT"){
            $status_before_bought = \OrderPeer::getBetweenStatus(\Order::STATUS_DEPOSITED,\Order::STATUS_BOUGHT);
            $status_condition = "";
            foreach ($status_before_bought as $status) {
                $status_condition .= "status = '{$status}' OR ";
            }
            $status_condition = substr($status_condition,0,strlen($status_condition) - 3);

            $query->andWhere($status_condition);

            $quantity = \OrderPeer::countOrderByQuery($query);

            $quantity =  intval($quantity) >=0 ? intval($quantity) : 0;
            $ajax = new \AjaxResponse();
            $ajax->data = $quantity;
            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());

        }

        $status = array($status);

        $quantity = \OrderPeer::countOrderByStatus($status,$query);

        if($quantity && !empty($quantity)) {
            $quantity =  intval($quantity[$sta]) >=0 ? intval($quantity[$sta]) : 0;
            $ajax = new \AjaxResponse();
            $ajax->data = $quantity;
            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::ERROR;
        return $this->renderText($ajax->toString());
    }

    public function executeSubmitConfirmDelivering(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $order_id = $this->request()->post("order_id","STRING",0);

        try{
            if($order_id == 0){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tồn tại đơn hàng!";
                return $this->renderText($ajax->toString());
            }

            $order = \Order::retrieveById($order_id);
            if($order->getStatus() != \Order::STATUS_DELIVERING || $this->user->getId() != $order->getBuyerId()){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Có lỗi xảy ra trong quá trình lưu dữ liệu!";
                return $this->renderText($ajax->toString());
            }

            if(!$order->changeStatus(\Order::STATUS_RECEIVED)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Có lỗi xảy ra trong quá trình lưu dữ liệu!";
                return $this->renderText($ajax->toString());
            }

            //tinh diem tich luy cho khach hang
            $user = \Users::retrieveById($order->getBuyerId());
            if($user instanceof \Users){
                $result_calculate_point_member = \OrderService::CalculatePointMemberByOrderService($order,$user);
            }else{
                \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('$user not instanceof \User at OrderActive.php');
            }

            //update packages status of order
            $package_list = \Packages::retrieveByOrderId($order->getId());
            if ($package_list) {
                foreach ($package_list as $package) {
                    if ($package instanceof \Packages) {
                        if ($package->isStatus(\Packages::STATUS_DELIVERING)) {
                            $package->changeStatus(\Packages::STATUS_RECEIVED);
                        }
                    }
                }
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Thành công";
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $this->logger_order->info("User {$this->user->getUsername()} can not update status confirm delivery with error:".$e->getMessage());
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Yêu cầu không thành công , xin thử lại";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeSubmitDelivering(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $order_list_id = $this->request()->post("order_id_list","STRING","");
        $cod = $this->request()->post("money_settle","FLOAT",0);

        try{
            $password = $this->request()->post("password");
            $is_confirm = $this->request()->post("is_confirm");
            $balance = $this->request()->post("balance","FLOAT",0);
            $order_list_id = explode(",",$order_list_id);
            if($is_confirm == 1){
                $user_auth = \UserAuth::getInstance();
                $result =  $user_auth->authenticate($this->user->getUsername(), $password);
                if($result <= 0){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = "Mật khẩu không chính xác, xin thử lại";
                    $ajax->element = "password";
                    return $this->renderText($ajax->toString());
                }

                $is_cod = $this->_isCod($order_list_id);
                if($balance >= 0 || !$is_cod){
                    $data = $this->_processOrder($order_list_id,$cod);
                    $this->_sendMail($data);
                    $this->logger_order->info("User {$this->user->getUsername()} request Delivery Order Successful",$data);
                    $ajax->account_balance = $this->user->getAccountBalance();
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = "Thành công";
                    return $this->renderText($ajax->toString());
                }

                if($is_confirm == 1){
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = "Xác nhận mật khẩu thành công";
                    $ajax->element = "password";
                    return $this->renderText($ajax->toString());
                }
            }

            $data = $this->_processOrder($order_list_id,$cod);

            $this->_sendMail($data);

            $this->logger_order->info("User {$this->user->getUsername()} request Delivery Order Successful with address : {$data["address_full"]}",
                array(
                    "order_code_list"=>$data["order_code_list"],
                    "missing_amount"=>$data["missing_amount"],
                    "total_amount" => $data["total_amount"],
                    "cod" => $data["cod"],
                    "account_balance" => $this->user->getAccountBalance()
                ));
            $this->user = \Users::retrieveById($this->user->getId());
            $ajax->account_balance = $this->user->getAccountBalance();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Thành công";
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $this->logger_order->info("User {$this->user->getUsername()} request Delivery Order not success - COD : $cod ".$e->getMessage(),$order_list_id);
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Yêu cầu không thành công , xin thử lại";
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Xử lý đơn hàng yêu cầu giao
     * @param $order_list_id
     * @param $cod
     * @return array
     * @throws \Exception
     */
    public function _processOrder($order_list_id,$cod){
        try{
            $missing_amount = 0;
            $order_list = array();
            $total_amount = 0;
            $address = array();
            $order_code_list = array();
            if(sizeof($order_list_id) > 0){
                foreach ($order_list_id as $order_id) {
                    if(intval($order_id) == 0){
                        continue;
                    }
                    $order = \Order::retrieveById($order_id);
                    if($order instanceof \Order){
                        if(!$address){
                            $address = $order->getAddressFull();
                        }
                        $order_code_list[] = $order->getCode();
                        $order_list[] = $order;
                        $missing_amount += $order->getMissingMoney();
                        $total_amount += $order->getTotalAmount();
                        \OrderPeer::requestDeliveryOrder($order);

                    }
                }
            }
            $data = array(
                "order_list" => $order_list,
                "missing_amount" =>$missing_amount,
                "address_full" => $address["detail"]." / {$address["district"]["label"]} - {$address["province"]["label"]}",
                "total_amount" => $total_amount,
                "order_code_list"=>$order_code_list,
                "cod" =>$cod,
                "address_id"=>$address["id"],
                "phone_number" => $address["reciver_phone"]
            );
            \OrderCod::updateCod($address["id"],$cod);
            return $data;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * Build order waiting delivery
     * @param $order_list
     * @return array
     */
    public function _buildOrderWaitingDelivery($order_list){
        $address_full = array();
        $order_list_array = array();
        $order_general = array();
        if(!empty($order_list)){
            foreach ($order_list as $order) {

                if($order instanceof \Order){
                    $order_array = \OrderPeer::buildArrayOrderData($order);
                    if(!isset($address_full[$order->getUserAddressId()])){
                        $address_full[$order->getUserAddressId()] = $order->getAddressFull();
                    }
                    if(!isset($order_list_array[$order->getUserAddressId()]["order_general"])){
                        $order_list_array[$order->getUserAddressId()]["order_general"] = array(
                            "total_pending_quantity" => 0,
                            "total_receive_quantity" => 0,
                            "total_weight" => 0,
                            "total_real_amount" => 0,
                            "total_services" => 0,
                            "total_payment" => 0,
                            "total_missing" => 0,
                            "total_real_refund" => 0
                        );
                    }

                    if($address_full[$order->getUserAddressId()]["province_id"] == 52 || $address_full[$order->getUserAddressId()]["province_id"] == 84){
                        $order_list_array[$order->getUserAddressId()]["order_general"]["is_cod"] = 1;
                    }else{
                        $order_list_array[$order->getUserAddressId()]["order_general"]["is_cod"] = 0;
                    }
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_pending_quantity"] += $order->getPendingQuantity();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_receive_quantity"] += $order->getReciveQuantity();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_weight"] += $order->getWeight();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_real_amount"] += $order->getRealAmount();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_services"] += $order_array["total_services_fee"];
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_payment"] += $order->getRealPaymentAmount();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_missing"] += $order_array["missing_amount"];
                    $order_list_array[$order->getUserAddressId()]["order_general"]["total_real_refund"] += $order->getRealRefundAmount();
                    $order_list_array[$order->getUserAddressId()]["order_general"]["id"] = $order->getUserAddressId();
                    $order_list_array[$order->getUserAddressId()]["address"] = $address_full[$order->getUserAddressId()];
                    $order_list_array[$order->getUserAddressId()]["order"][] = $order_array;
                }
            }
        }
        return $order_list_array;
    }

    /**
     * Send Mail to luutronghieu
     * @param $data
     * @return bool
     */
    public function _sendMail($data){
        $template = GLOBAL_TEMPLATES_PATH.'/email/RequestDelivery';
        $email_array =  array(
//            "chuminhquyen@alimama.vn",
//            "nguyenvangiang@alimama.vn",
            "maithigiang@alimama.vn",
            "buiductruong@alimama.vn",
            "nguyenquocthang@alimama.vn",
            "tranthithuong@alimama.vn",
            " hoangthicuc@alimama.vn"
        );// "luutronghieu@alimama.vn";//"chuminhquyen@alimama.vn";
        $subject = "Thông tin khách hàng {$this->user->getUsername()} yêu cầu giao hàng - {$data['address_id']}";
        $params = array(
            'user' => $this->user,
            'order_list' => $data['order_list'],
            'total_amount' => $data['total_amount'],
            'missing_amount' => $data['missing_amount'],
            'address_full' => $data['address_full'],
            'cod' => $data['cod'],
            'phone_number' => $data["phone_number"]
        );
        foreach ($email_array as $email) {
            $sendMail= \MailHelper::mailHelperWithBody($template,$params);
            $sendMail->setReciver($email);
            $sendMail->setSubject($subject);
            $sendMail->sendMail();
        }
        return true;
    }

    /**
     * Check is cod
     * @param $order_id_list
     * @return bool
     */
    public function _isCod($order_id_list){
        if(sizeof($order_id_list) > 0){
            foreach ($order_id_list as $order_id) {
                $order = \Order::retrieveById($order_id);
                if($order instanceof \Order){
                    $address_full = $order->getAddressFull();
                    if(!empty($address_full)){
                        if($address_full["province_id"] == 52 || $address_full["province_id"] == 84){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}