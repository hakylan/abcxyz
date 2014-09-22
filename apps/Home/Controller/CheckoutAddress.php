<?php
namespace Home\Controller;
use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use Flywheel\Storage;
use Flywheel\Session;
use SeuDo\Main;
use SeuDo\Queue;

class CheckoutAddress extends HomeBase {
    private $authen;
    private $user;


    public function beforeExecute(){
        parent::beforeExecute();
        $this->authen = \HomeAuth::getInstance();
        $this->user = $this->authen->getUser();

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('onSuccessCartAdd', array(new \HomeEvent(), 'onSuccessCartAdd'));
    }

    public function executeDefault() {

        exit;
        $order_id = ['H995_0170901'];

        $anhken = \Users::retrieveByUsername('anhken');
//        $giangkt = \Users::retrieveByUsername('giangkt');
        foreach ($order_id as $id) {
            $order = \Order::retrieveById($id);
            if(!$order instanceof \Order){
                $order = \Order::retrieveByCode($id);
            }

            if($order instanceof \Order){
                $this->_divisionOrder($anhken,$order);
            }
        }
        exit;

//        $order_list = [177,237,243,1115,2008,2404,3441,4104,4541,4796,4797,5100,5154,5175,5311,5312
//            ,5313,5357,5358,5362,5387,5461,5464,5477,5478,5509,5512,5518,5520,5521,5524,5526,5527
//            ,5531,5533,5564,5584,5589,5613,5617,5625,5626,5637,5689,5698,5704,5705,5708,5716,5717
//            ,5720,5736,5738,5739,5740,5748,5751,5775,5776,5777,5782,5783,5792,5796,5797,5817,5828,
//            5830,5864,5871,5880,5952,5975,6111];
//        $weight =  [500 , 200 , 400 , 200 , 1000 , 2500 , 200 , 600 , 200 , 200 , 800 , 2500 , 4500
//            , 1000 , 300 , 1800 , 2000 , 2500 , 1000 , 500 , 600 , 2300 , 4500 , 7500 , 500 , 200 ,
//            300 , 7000 , 600 , 300 , 3600 , 300 , 1000 , 6000 , 200 , 500 , 2000 , 300 , 200 , 200 ,
//            900 , 500 , 700 , 4000 , 3400 , 1000 , 400 , 5000 , 3600 , 1000 , 1500 , 200 , 4800 , 1000
//            , 300 , 1000 , 1800 , 16000 , 9500 , 4000 , 200 , 200 , 4000 , 3500 , 2800 , 7200 , 3400 ,
//            1600 , 2600 , 4500 , 5600 , 600 , 100 , 300];
//        foreach ($order_list as $key=>$order_id) {
//            try{
//                $this->_chargeFeeShipping($weight[$key]/1000,$order_id);
//
//            }catch (\Exception $e){
//                print_r('<pre>');
//                print_r($e->getMessage());
//                print_r('</pre>');
//                continue;
//            }
//        }


        exit;
        $order = \Order::retrieveById(8209);
        $order->updateInfo();
        $img = "http%3A%2F%2Fgi3.md.alicdn.com%2Fimgextra%2Fi3%2F94153930%2FTB2OTZvXVXXXXXjXXXXXXXXXXXX_!!94153930.jpg_430x430q90.jpg";
        print_r('<pre>');
        print_r(urldecode(urldecode($img)));
        print_r('</pre>');
        exit();
    }

    public function executeChooseCpn(){
        if($this->user instanceof \Users && $this->user->getUsername() == "quyenminh"){
            $order_id = $this->request()->get("order_id");
            $order = \Order::retrieveById($order_id);
            if($order instanceof \Order){
                $result = $order->addService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
                if($result){
                    $order->removeService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    $order->updateInfo();
                }
            }
            return $this->renderText("OK");
        }

        return $this->renderText(1);
    }

    public function executeRefundOrder(){
        if($this->user instanceof \Users && $this->user->getUsername() == "quyenminh"){
            $type = $this->request()->get("type");
            if($type == "refund"){
                $order_id = $this->request()->get("id");
                $refund_amount = $this->request()->get("refund_amount");
                $order = \Order::retrieveById($order_id);
                if($order instanceof \Order){
                    $user = $order->getBuyer();
                    $detail = array(
                        'order_code' => $order->getCode(),
                        'type' => 'REFUND',
                        'message' => "Trả lại tiền đặt cọc khi đơn hết hàng."
                    );
                    try {
                        if($refund_amount > 0){
                            $user_transaction = \OrderPeer::refundOrder($order,$refund_amount,"Trả lại tiền khi tất toán đơn hàng xảy ra lỗi.");
                            print_r('<pre>');
                            print_r($user_transaction);
                            print_r('</pre>');
                            exit();
                        }

                    } catch(\Exception $e) {//accountant's transaction success need rollback and recharge
                        $user->rollBack();
                        print_r('<pre>');
                        print_r(4);
                        print_r('</pre>');
                        \SeuDo\Logger::factory('system')->info($e->getMessage() .".\nTrances:\n" .$e->getTraceAsString());
                        throw $e;
                    }
                }

            }else{
                $user_id = $this->request()->get("uid");
                $order_code = $this->request()->get("order");
                $money = $this->request()->get("money");

                $user = \Users::retrieveById($user_id);

                $histories =  \SeuDo\Accountant\Util::getUserTransactionHistory($user, "2014-04-15 00:00:00", date("Y-m-d H:i:s"), 0, 99999);

                print_r('<pre>');
                print_r($histories);
                print_r('</pre>');
                exit();
            }
        }
        exit;
    }


    public function _divisionOrder($user,$order){
        if($user instanceof \Users && $order instanceof \Order){
            $order->setTellersId($user->getId());
            $order->setTellersAssignedTime(new \DateTime());
            $order->setStatus(\Order::STATUS_BUYING);
            $order->save();
        }

    }

    public function _exportTransaction($user){
        if(is_numeric($user)){
            $user_id = $user;
        }else if($user instanceof \Users){
            $user_id = $user->getId();
        }
        $user_transaction = \UserTransaction::read()->andWhere("user_id = {$user_id}")->orderBy("closed_time","ASC")->execute()
            ->fetchAll(\PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(null,false));

        foreach ($user_transaction as $transaction) {
            if($transaction instanceof \UserTransaction){
                $time = new DateTime($transaction->getClosedTime());
                echo $time->format("H:i d/m/Y")."<br/>";
            }
        }
    }

    public function _sysTransaction(){
        $users = \Users::findByStatus("ACTIVE");
        $from_time =  date('2014-01-01 00:00:00');

        foreach ($users as $user) {
            try{
                \UserTransaction::syncTransactionHistory($user,$from_time,date("Y-m-d H:i:s"));
            }catch(\Exception $e){
                echo $e->getMessage();
                continue;
            }
        }
    }

    public function _statisticOrderServicesBuying(){
        $this->setLayout("default");
        $this->setView("Home/default");


//        $order_list = \Order::read()->andWhere("DATE(created_time) > (CURDATE() - INTERVAL 9 DAY)
//                                OR DATE(deposit_time) > (CURDATE() - INTERVAL 9 DAY)")->execute()
//                    ->fetchAll(\PDO::FETCH_CLASS,\Order::getPhpName(),array(false,null));

//        $array_error = \OrderService::getOrderFailServices(\Services::TYPE_BUYING,'2014-05-01 00:00:00',date('Y-m-d H:i:s',time()));
//
//        $order_services = \OrderService::read()->andWhere("created_time>'2014-05-09 11:50:23'")->execute()->fetchAll(\PDO::FETCH_CLASS,\OrderService::getPhpName(),array(false,null));
//        foreach ($order_services as $services) {
//            if($services instanceof \OrderService){
//                $order = \Order::retrieveById($services->getOrderId());
//                if($order instanceof \Order){
//                    $total_money = \OrderService::getOrderServicesAmount($order);
//                    $order->setServiceFee($total_money);
//                    $order->save();
//                }
//            }
//        }

        print_r('<pre>');
//        print_r($array_error);
        print_r('</pre>');
        exit();

        $array_user = array();

        foreach ($order_list as $order) {
            if($order instanceof \Order){
                if($order instanceof \Order){
                    $order_services = \OrderService::findOneByOrderIdAndServiceCode($order->getId(),"BUYING");
                    if(empty($order_services) || !($order_services instanceof \OrderService)){
                        $array = array();
                        $array["id"] = $order->getId();
                        $array["code"] = $order->getCode();
                        $date = new \DateTime($order->getDepositTime());
                        $array["deposit_time"] = $date->format("H:i d-m-Y");
                        $user = \Users::retrieveById($order->buyer_id);
                        if($user instanceof \Users){

                            $array["user"] = $user->getUsername();
                            $array["buying_fee"] = \OrderService::getMissingServiceFee($order,\Services::TYPE_BUYING);
                            $array_user[$order->buyer_id][] = $array;
                        }
                    }
                }
            }
        }

        foreach ($array_user as $key=>$list) {
            $total_money = 0;
            foreach ($list as $value) {
                if(!isset($array_error[$key])){
                    echo " **** {$value["user"]}<br/>";
                }
                $fee = $value["buying_fee"]["fee_origin"];
                echo $value["deposit_time"]."<br/>";
//                 - <span>{$fee}</span>    -    <span>{$value["deposit_time"]}</span><br/>";
                $total_money += $value["buying_fee"]["fee_origin"];
                $array_error[$key][] = $value;
            }
            echo "Tong: ".$total_money ."<br>";
        }

        print_r('<pre>');
        print_r($array_error);
        print_r('</pre>');
        exit;
    }


    /**
     * Truy thu với những đơn hàng đã nhận hàng mà không có cân nặng
     * @param $weight
     * @param $order_id
     * @return bool|\UserTransaction
     * @throws \Exception
     * @throws \SeuDo\Accountant\Exception
     * @throws \Exception
     */
    public function _chargeFeeShipping($weight,$order_id)
    {
        $order = \Order::retrieveById($order_id);
        if($order instanceof \Order){
            $order->changeWeight($weight);

            print_r('<pre>');
            print_r($order_id . " -------- " . $weight);
            print_r('</pre>');
            $amount = $order->requestDeliveryMoney();

            print_r('<pre>');
            print_r($amount);
            print_r('</pre>');

            if($amount <= 0){
                return true;
            }
            /**
             * @TODO allow amount lower than zero, in this case we refund customer
             */

            $message = "Truy thu tiền phí Vận Chuyển TQ-VN khi cập nhật cân nặng cho đơn hàng";


            //charge fee first
            $detail = json_encode(array(
                'order_code' => $order->getCode(),
                'type' => 'PAYMENT',
                'detail' => $message
            ));

            $buyer = $order->getBuyer();

//        $order->beginTransaction();
            $conn = \Flywheel\Db\Manager::getConnection();
            $conn->beginTransaction();
            try {
                $transfer = \SeuDo\Accountant\Util::charge($buyer, $amount, $detail, $message);

                try {
                    $balance = $transfer['from_account']['balance'];
                    \UsersPeer::changeAccountBalance($buyer, $balance);//change buyer account balance

                    //save user transaction
                    $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                        $amount,
                        $balance,
                        \UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                        $transfer['transfer_transaction'],
                        $detail);

                    //change order status late
                    $order->setRealPaymentAmount($order->getRealPaymentAmount() + $amount);

                    $result = $order->save();

                    if($result){
                        $conn->commit();

                        //logging
                        \SeuDo\Logger::factory('business')->info('Completed payment orders', array(
                            'order_code' => $order->getCode(),
                            'user' => $buyer->getUsername(),
                            'accountant_transaction' => $userTransaction->getTransactionCode(),
                            'user_transaction' => $userTransaction->getId()
                        ));

                        return $userTransaction;
                    }else{
                        $conn->rollBack();
                        throw new \Flywheel\Exception("Can't save order");
                    }


                } catch(\Exception $e) {
                    $conn->rollBack();
                        \SeuDo\Accountant\Util::refund($buyer, $amount, json_encode(array(
                                'order_code' => $order->getCode(),
                                'type' => 'REFUND',
                                'detail' => 'Giao dịch Truy thu tiền phí Vận Chuyển TQ-VN khi cập nhật cân nặng cho đơn hàng
                                 ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.'
                            )),
                            'Giao dịch Truy thu tiền phí Vận Chuyển TQ-VN khi cập nhật cân nặng cho đơn hàng
                                 ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.');

                        throw $e;
                }
            } catch(\SeuDo\Accountant\Exception $sae) {//transfer not success not need refund
                $conn->rollBack();

                //log error
                \SeuDo\Logger::factory('system')->error($sae->getMessage() ."\nTraces:\n" .$sae->getTraceAsString());
                throw $sae;
            }
        }
    }



    // Address

    public function executeLoadAddress(){

        if($this->user){
            $user_id = $this->user->id;
        }else{
            return $this->renderText('');
        }

        $is_user = $this->request()->get("is_user","INT",0);

        $province_list = \Locations::findByType(\Locations::LOCATION_STATE);

        $this->view()->assign('user_id',$user_id);

        $this->setView('CheckoutAddress/default');

        $user_address = \UserAddress::getUserAddresses($user_id);

        $this->view()->assign('user_address',$user_address);
        $this->view()->assign('province_list',$province_list);
        $this->view()->assign('is_user',$is_user);

        return $this->renderPartial();
    }

    public function executeChooseAddress(){

        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();


        if($this->user){
            $user_id = $this->user->id;
        }else{
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Bạn chưa đăng nhập';
            return $this->renderText($ajax->toString());
        }

        $user_address_id = $this->request()->get('id');

        if($user_address_id > 0){
            $query = \UserAddress::read();

            $list_address = $query->where("is_delete = 0 and user_id='{$user_id}")
                ->setFirstResult(0)->setMaxResults(10)->orderBy('id','desc')
                ->execute()->fetchAll(\PDO::FETCH_CLASS, 'UserAddress', array(null, false));

            foreach ($list_address as $ad) {
                if(sizeof($ad) == 0){
                    continue;
                }
            }

            $user_address = \UserAddress::retrieveById($user_address_id);
            if($user_address->getUserId() != $user_id || !$user_address instanceof \UserAddress){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'Địa chỉ nhận hàng này không phải của bạn';
                return $this->renderText($ajax->toString());
            }else{
                $user_address->setIsDefault(1);
                $user_address->setUpdatedTime(date('Y:m:d H:i:s'));
                $user_address->setNew(false);
                $user_address->save();
            }
        }
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = 'Cập nhật địa chỉ nhận hàng thành công';
        return $this->renderText($ajax->toString());
    }

    public function executeDeleteAddress(){

        $ajax = new \AjaxResponse();

        $this->validAjaxRequest();

        $addressId = $this->request()->post('address_id');

        $user_address = \UserAddress::retrieveById($addressId);
        $user_address->setIsDelete(1);
        $user_address->save();

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = 'Cập nhật địa chỉ nhận hàng thành công';
        return $this->renderText($ajax->toString());
    }

    public function executeSelectCity(){
        $id = $this->request()->get('id','INT',0);

        if($id <= 0){
            $streets = '<option value="-1">Quận/Huyện</option>';
            return $this->renderText($streets);
        }

        $streets = '<option value="-1">Quận/Huyện</option>';
        $district_list = \Locations::findByParentId($id);

        if($district_list){
            foreach($district_list as $district){
                $streets.= "<option value='".$district->id."'>".$district->label."</option>";
            }
        }

        return $this->renderText($streets);
    }

    public function executeSaveAddress(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $data = $this->request()->post('data','STRING','');

        if($data == ''){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Không có dữ liệu';
            return $this->renderText($ajax->toString());
        }

        $address = json_decode($data,true);


        if(!isset($address['id']) || $address['id'] <= 0){
            $address['id'] = '';
        }

        if(!$this->user){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Bạn chưa đang nhập';
            return $this->renderText($ajax->toString());
        }
        if(!$address['name']){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Chưa điền tên liên hệ';
            $ajax->element = '._error_name';
            return $this->renderText($ajax->toString());
        }
        if(!$address['phone']){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Chưa điền số điện thoại liên hệ';
            $ajax->element = '._error_phone';
            return $this->renderText($ajax->toString());
        }
        if(!$address['home']){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Chưa điền số nhà, địa chỉ chi tiết';
            $ajax->element = '._error_home';
            return $this->renderText($ajax->toString());
        }

        if(!$address['province'] || $address['province'] <= 0){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Bạn chưa chọn Tỉnh / Thành phố';
            $ajax->element = '._error_province';
            return $this->renderText($ajax->toString());
        }

        if(!$address['district'] || $address['district'] <= 0){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Bạn chưa chọn Quận / huyện';
            $ajax->element = '._error_district';
            return $this->renderText($ajax->toString());
        }

        if(!isset($address['id']) || $address['id'] <= 0){
            $user_address = new \UserAddress();
            $user_address->setNew(true);
            $user_address->setCreatedTime(date('Y-m-d H:i:s'),time());
        }else{
            $user_address = \UserAddress::retrieveById($address['id']);
        }

        $user_address->setDetail($address['home']);
        $user_address->setUserId($this->user->id);
        $user_address->setNote($address['note']);
        $user_address->setReciverName($address['name']);
        $user_address->setReciverPhone($address['phone']);
        $user_address->setProvinceId($address['province']);
        $user_address->setDistrictId($address['district']);
        if(!isset($address['id']) || $address['id'] <= 0){
            $query = \UserAddress::read();
            $query
                ->where("user_id={$this->user->id}")
                ->set('`is_default`',0)
                ->update(\UserAddress::getTableName())->execute();
            $user_address->setIsDefault(1);
        }else{
            $user_address->setIsDefault(0);
        }
        $user_address->save();

        if($user_address->getId() > 0){

            $ajax->type = \AjaxResponse::SUCCESS;
            if(!isset($address['id']) || $address['id'] <= 0){

                $ajax->message  = 'Thêm địa chỉ thành công';
            }else{
                $ajax->message  = 'Sửa địa chỉ thành công';
            }
            return $this->renderText($ajax->toString());
        }
        $ajax->type = \AjaxResponse::ERROR;
        if(!isset($address['id']) || $address['id'] <= 0){
            $ajax->message  = 'Thêm địa chỉ không thành công';
        }else{
            $ajax->message  = 'Sửa địa chỉ không thành công';
        }
        return $this->renderText($ajax->toString());
    }

    public function executeSelectAddress(){
        $this->validAjaxRequest();
        $address_id = $this->request()->post('address_id',"INT",0);

        $ajax = new \AjaxResponse();

        if(!$this->user){

            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Bạn chưa đăng nhập';
            return $this->renderText($ajax->toString());
        }else{
            $user_id = $this->user->id;
        }

        if($address_id == 0){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Bạn chưa chọn địa chỉ';
            return $this->renderText($ajax->toString());
        }

        $query = \UserAddress::read();
        $query
            ->where("user_id={$user_id}")
            ->set('`is_default`',0)
            ->update(\UserAddress::getTableName())->execute();

        $user_address = \UserAddress::retrieveById($address_id);

        if($user_address->getUserId() != $user_id ){ //        if($user_address->getUserId() != 1 ){ //

            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Đây không phải địa chỉ của bạn';
            return $this->renderText($ajax->toString());
        }

        $user_address->setIsDefault(1);
        $user_address->setUpdatedTime(date('Y:m:d H:i:s'));
        $result = $user_address->save();

        if($result){
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message  = 'Chọn địa chỉ thành công';
            return $this->renderText($ajax->toString());
        }else{
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message  = 'Chọn địa chỉ không thành công';
            return $this->renderText($ajax->toString());
        }

    }
}