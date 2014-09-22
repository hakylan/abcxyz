<?php 
/**
 * OrderService
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/OrderServiceBase.php';
class OrderService extends \OrderServiceBase {

    const STATUS_INIT = 'INIT';
    const STATUS_CONFIRM = 'CONFIRM';
    const STATUS_SKIP = 'SKIP';

    /**
     * Remove all order's services
     * @param Order $order
     */
    public static function deleteByOrder(\Order $order) {
    }

    public function getOrderService(){
        return OrderService::findByOrderId($this->getOrderId());
    }

    /**
     * Get
     * @return mixed
     */
    public function getServicesCodeTitle(){
        $services = Services::retrieveById($this->getServiceId());
        return $services->getDescription();
    }

    public static function mappingToOrder($data = array(), \Order $order){

        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try {
            $checkFlag = false;
            if(!$data || empty($data)) { return false;}

            foreach ($data as  $serviceCode =>$serviceMoney ) {
                $service = \Services::findOneByCodeAndStatus($serviceCode,\Services::STATUS_ACTIVE);

                if($service && ($service instanceof \Services)) {
                    $orderService = new self;
                    $orderService->setOrderId($order->getId());
                    $orderService->setServiceId($service->getId());
                    $orderService->setServiceCode($service->getCode());
                    $orderService->setMoney($serviceMoney);
                    $orderService->setStatus(self::STATUS_INIT);
                    $orderService->setCreatedTime(new \Flywheel\Db\Type\DateTime());
                    $orderService->setNew(true);
                    $result = $orderService->save();

                    if($result == true){
                        $checkFlag = true;
                    }
                    else {
                        $checkFlag = false;
                        break;
                    }
                }
            }

            if($checkFlag == true) {
                $conn->commit();
                return true;
            }
            $conn->rollBack();
            return false;
        } catch (\Flywheel\Exception $e) {
            $conn->rollBack();
            return false;
        }
    }


    /**
     * @param Order $order
     * @return bool|int
     */
    public static function getOrderServicesAmount(\Order $order) {
        $total_fee = 0;
        $order_services = \OrderPeer::getOrderServices($order);
        if(!empty($order_services)) {
            foreach ($order_services as $os) {
                if($os instanceof \OrderService) {
                    $fee = $os->getOrderServiceAmount($order);
                    /* update lại thông tin của các service */
                    if (is_array($fee) && isset($fee['fee_origin'])) {
                        $feeOnService = $fee['fee_origin'];
                    } else {
                        $feeOnService = $fee;
                    }
                    if (is_array($fee) && isset($fee['fee_discount'])) {
                        $discount_fee = $fee['fee_discount'];
                    } else {
                        $discount_fee = $fee;
                    }
                    $os->setMoney($feeOnService);
                    $os->setDiscountedMoney($discount_fee);
                    $result = $os->save();

                    if ($result == true) {
//                        if (is_array($fee) && isset($fee['fee_discount'])) {
//                            $total_fee += intval($fee['fee_discount']);
//                        } else
                        if(isset($fee['fee_discount'])){
                            $total_fee += intval($discount_fee);
                        }else{
                            $total_fee += intval($fee);
                        }
                    }
                }
            }
        }
        return $total_fee;
    }

    /**
     * Lấy ra thông tin đơn hàng bị thiếu dịch vụ
     * @param $services_code
     * @param $from_time
     * @param string $to_time
     * @param Users $user
     * @return array
     * @throws Exception
     */
    public static function  getOrderFailServices($services_code,$from_time,$to_time = '',\Users $user = null){
        try{
            if($to_time == ''){
                $to_time = date("Y-m-d H:i:s");
            }
            $from_time = Common::validDateTime($from_time);
            $to_time = Common::validDateTime($to_time);

            $query = \Order::read();

            $query->andWhere("created_time > '{$from_time}'");
            $query->andWhere("created_time < '{$to_time}'");
//            $query->andWhere("deposit_time > '0000-00-00'");
            if($user instanceof \Users){
                $query->andWhere("buyer_id = {$user->getId()}");
            }
            $order_list = $query->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Order::getPhpName(),array(false,null));

            $array_user = array();

            $array_order = array();

            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    if($order instanceof \Order){
                        $order_services = \OrderService::findOneByOrderIdAndServiceCode($order->getId(),$services_code);
                        if(empty($order_services) || !($order_services instanceof \OrderService)){
                            $array = array();
                            $array["id"] = $order->getId();
                            $array["code"] = $order->getCode();
                            $date = new \DateTime($order->getDepositTime());
                            $array["deposit_time"] = $date->format("H:i d-m-Y");
                            $array["status"] = $order->getStatus();
                            $array["status_title"] = $order->getStatusTitle();
                            $user = \Users::retrieveById($order->buyer_id);
                            if($user instanceof \Users){

                                $array["user"] = $user->getUsername();
                                $array["buying_fee"] = \OrderService::getMissingServiceFee($order,\Services::TYPE_BUYING);
                                $array_user[$user->getId()][] = $array;
                                if($order->isAfterStatus(\Order::STATUS_WAITING_FOR_DELIVERY)){
                                    $array_order[$user->getId()] = $array;
                                }else{
                                    $service = \Services::retrieveByCode(\Services::TYPE_BUYING);
                                    $order_service = new \OrderService();
                                    $order_service->setOrderId($order->getId());
                                    $order_service->setServiceCode(\Services::TYPE_BUYING);
                                    $order_service->setMoney($array["buying_fee"]);
                                    $order_service->setServiceId($service->getId());
                                    $order_service->setStatus(\Services::STATUS_INIT);
                                    $order_service->setCreatedTime(new DateTime());
                                    $order_service->save();
                                }

                            }
                        }
                    }
                }
            }

            return $array_user;

        }catch (\Exception $e){
            throw $e;
        }

    }

//    public static function checkOrderFailServices(\Order $order,$services_code){
//
//    }

    /**
     * todo lấy ra phí dịch vụ trên từng loại dịch vụ
     * @param Order $order
     * @return array|float|int|mixed
     */
    public function getOrderServiceAmount(\Order $order) {
        $user = $order->getBuyer();

        $fee = 0;
        switch($this->getServiceCode()) {
            case \Services::TYPE_BUYING:
                $fee =  \ServiceBuying::getTotalFee($order->getRealAmount(),$user->getLevelId());
                break;
            case \Services::TYPE_CHECKING:
                $fee = \ServiceChecking::getTotalFee($order->getPendingQuantity(), $order->getItemNormalQuantity(),
                    $order->getItemAssessQuantity(),$order->getDepositTime(),$user->getLevelId());
                break;
            case \Services::TYPE_PACKING:
                $fee = \ServicePacking::getTotalFee($order->getWeight());
                break;
            case \Services::TYPE_SHIPPING_CHINA_VIETNAM:
                $warehouse = $order->getDestinationWarehouse();

                if ($warehouse == WarehouseMapping::WAREHOUSE_CODE_HANOI){
                    $target = '01';
                } elseif($warehouse == WarehouseMapping::WAREHOUSE_CODE_SAIGON) {
                    $target = '02';
                }
                if(!isset($target)) {
                    $target = '01';
                }
                $fee = \ServiceShipping::getChinaVietnamFee($order->getWeight(), $target,$order->getDepositTime(),$user->getLevelId());
                break;
            case \Services::TYPE_EXPRESS_CHINA_VIETNAM:
                $fee = \OrderService::getServicesCpnAmount($order,$user->getLevelId());
                break;
            default:
                $fee = $this->getMoney();
            break;

        }
        return $fee;
    }

    /**
     * Get Services Cpn Amount
     * @param Order $order
     * @param int $level_id
     * @return mixed
     */
    public static function getServicesCpnAmount(\Order $order,$level_id = 1){
        $warehouse = $order->getDestinationWarehouse();

        $time_stamp_deposit = Common::getTimeStamp($order->getDepositTime());

        if ($warehouse == WarehouseMapping::WAREHOUSE_CODE_HANOI){
            if($time_stamp_deposit >= strtotime(date('2014-06-16 00:00:00'))){
                $target = '01';
            }else{
                $target = '03';
            }
        } elseif($warehouse == WarehouseMapping::WAREHOUSE_CODE_SAIGON) {
            if($time_stamp_deposit >= strtotime(date('2014-06-16 00:00:00'))){
                $target = '02';
            }else{
                $target = '04';
            }
        }
        if(!isset($target)) {
            if($time_stamp_deposit >= strtotime(date('2014-06-16 00:00:00'))){
                $target = '01';
            }else{
                $target = '03';
            }
        }
        $fee = \ServiceShipping::getExpressChinaVietnamFee($order->getWeight(),
            $target,$order->getDepositTime(),$level_id);

        return $fee;
    }

    /**
     * Build order services array  -- Create By Quyen
     * @param Order $order
     * @return array
     */
    public static function buildOrderServicesArray(\Order $order){
        $services_order_list = array();
        if($order instanceof \Order){
            $domestic_shipping = array();
            $domestic_shipping["money"] = $domestic_shipping["discounted_money"] = $order->getDomesticShippingFeeVnd();
            $domestic_shipping["description"] = "VC nội địa TQ";
            $services_list = \Services::findAll();
            if(!empty($services_list)){
                foreach ($services_list as $services) {
                    if($services instanceof \Services){
                        if($services->getCode() == \Services::TYPE_SHIPPING){
                            continue;
                        }
                        $order_services = self::findOneByOrderIdAndServiceId($order->getId(),$services->getId());
                        if($order_services instanceof self){
                            $array = $order_services->toArray();
                            $services_list[] = $array;
                        }else{
                            $array["money"] = 0;
                            $array["discounted_money"] = 0;
                        }
                        if($array["discounted_money"] == 0){
                            $array["discounted_money"] = $array["money"];
                        }
                        $array['service_code'] = $services->getCode();
                        $array["description"] = str_replace("Vận chuyển","VC",$services->getTitle());
                        $services_order_list[] = $array;
                    }
                }
            }
            $services_order_list[] = $domestic_shipping;
        }
        return $services_order_list;
    }

    /**
     * Tính thử phí dịch vụ khi chưa được chọn trên đơn hàng
     * @param Order $order
     * @param string $code
     * @return array|int|mixed
     */
    public static function getMissingServiceFee(\Order $order,$code = "BUYING"){
        $user = $order->getBuyer();

        $fee = 0;
        switch($code) {
            case \Services::TYPE_BUYING:
                $fee =  \ServiceBuying::getTotalFee($order->getRealAmount(),$user);
                break;
            case \Services::TYPE_CHECKING:
                $fee = \ServiceChecking::getTotalFee($order->getPendingQuantity(), $order->getItemNormalQuantity(),
                    $order->getItemAssessQuantity(),$order->getDepositTime(),$user);
                break;
            case \Services::TYPE_PACKING:
                $fee = \ServicePacking::getTotalFee($order->getWeight());
                break;
            case \Services::TYPE_SHIPPING_CHINA_VIETNAM:
                $warehouse = $order->getDestinationWarehouse();

                if ($warehouse == WarehouseMapping::WAREHOUSE_CODE_HANOI){
                    $target = '01';
                } elseif($warehouse == WarehouseMapping::WAREHOUSE_CODE_SAIGON) {
                    $target = '02';
                }
                if(!isset($target)) {
                    $target = '01';
                }
                $fee = \ServiceShipping::getChinaVietnamFee($order->getWeight(), $target,$order->getDepositTime());
                break;
            case \Services::TYPE_EXPRESS_CHINA_VIETNAM:
                $fee = \OrderService::getServicesCpnAmount($order);
                break;
            default:
                break;

        }
        return $fee;
    }

    /**
     * Get Services Fee - Create By Quyen
     * @param Order $order
     * @param $code
     * @return int|number
     */
    public static function getServiceFee(\Order $order,$code){
        $fee = 0;
        if($order instanceof \Order){
            $order_services = \OrderService::findOneByOrderIdAndServiceCode($order->getId(),$code);
            if($order_services instanceof \OrderService){
                $fee = $order_services->getMoney();
            }
        }
        return $fee;
    }

    /**
     * Get Services Fee - Create By Quyen
     * @param Order $order
     * @param $code
     * @return int|number
     */
    public static function getServiceDiscountFee(\Order $order,$code){
        $fee = 0;
        if($order instanceof \Order){
            $order_services = \OrderService::findOneByOrderIdAndServiceCode($order->getId(),$code);
            if($order_services instanceof \OrderService){
                $fee = $order_services->getDiscountedMoney();
            }
        }
        return $fee > 0 ? $fee : 0;
    }


    /**
     * @todo trả lại tiền khi tính phí CPN không làm tròn theo 100g mà làm tròn theo 1kg
     */
    public static function refundServicesCpn(){
        $order_list = \Order::read()
            ->andWhere("confirm_delivery_time >= '2014-06-13 00:00:00'")->execute()
//            ->andWhere("id={$order_id}")->execute() //2153
            ->fetchAll(\PDO::FETCH_CLASS,\Order::getPhpName(),array(null,false));

        foreach ($order_list as $key=>$order) {
            if($order instanceof \Order){
                try{
                    $is_cpn =  $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
                    if($is_cpn){
                        $fee = \OrderService::getServicesCpnAmount($order);
                        $cpn_fee = \OrderService::getServiceFee($order,\Services::TYPE_EXPRESS_CHINA_VIETNAM);
                        if($cpn_fee > $fee["fee_origin"]){

                            $refund_amount = $cpn_fee - $fee["fee_origin"];

                        }
                    }
                }catch (\Exception $e){
                    throw $e;
                }
            }
        }
    }

    private static function _refundServiceFeeCpn(\Order $order,$refund_amount){
        $detail = array(
            'order_code' => $order->getCode(),
            'type' => 'REFUND',
            'message' => "Trả lại tiền phí CPN do thay đổi công thức tính phí trên trọng lượng thực"
        );
        $user = \Users::retrieveById($order->getBuyerId());

        if (!($user instanceof \Users)) {
            throw new \InvalidArgumentException('$user parameter must be instance of \Users or user\'s id');
        }

        $user->beginTransaction();
        try {
            $transfer = \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail), $detail['message']);

            try {
                $detail = json_encode($detail);
                $balance = $transfer['to_account']['balance'];
                \UsersPeer::changeAccountBalance($user, $balance);
                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $refund_amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_REFUND,
                    $transfer['receiving_transaction'],
                    $detail);

                $user->commit();
                \SeuDo\Logger::factory('refund_service_cpn')->info('refund order completed', array(
                    'order_code' => $order->getCode(),
                    'user' => $user->getUsername(),
                    'accountant_transaction' => $userTransaction->getTransactionCode(),
                    'user_transaction' => $userTransaction->getId()
                ));
                return $userTransaction;
            } catch (\Exception $e) {
                //charging back
                \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
                    'order_code' => $order->getCode(),
                    'type' => 'ROLLBACK',
                    'detail' => 'Giao dịch hoàn tiền cho đơn  ' . $order->getCode() . ' không thành công. Trả lại tiền cho Sếu đỏ.'
                )));
                throw $e;
            }
        } catch(\Exception $e) {//accountant's transaction success need rollback and recharge
            $user->rollBack();
            \SeuDo\Logger::factory('system')->error($e->getMessage() .".\nTrances:\n" .$e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * @param array $arrServiceCode
     * @return array
     * @throws Flywheel\Exception
     */
    public static function getOrdersByOrderServiceCode( $arrServiceCode = array() ) {
        $arrOrders = array();
        if( sizeof($arrServiceCode) == 0 ) {
            return $arrOrders;
        }

        try{
            $query = \OrderService::select();
            $query->select("order_id");
            $query->andWhere(" `service_code` IN (" . implode(",", $arrServiceCode) . ") ");
            $data = $query->execute();

            if( sizeof($data) > 0 ) {
                foreach ( $data as $item ) {
                    if( $item instanceof \OrderService ) {
                        $arrOrders[] = $item->getOrderId();
                    }
                }
            }

            return $arrOrders;
        } catch ( \Flywheel\Exception $e ) {
            \SeuDo\Logger::factory('get_orders_by_order_service_code')->addError('has error when try get all orders by service code', array($e->getMessage()));
            throw new \Flywheel\Exception('has error when try get all orders by service code');
        }
    }

    public static function CalculatePointMemberByOrderService($order,$user){

        if ($order instanceof \Order) {
            if ($user instanceof \Users) {
                $query = \MemberScoreHistory::select();
                $query->where("user_id={$user->getId()}");
                $query->andWhere("object_id={$order->getId()}");
                $query->andWhere("object_type='ORDER'");
                $check_calculated = $query->execute();
                if(count($check_calculated)>0){
                    //this order of user was calculated point, do nothing: receiver order only one, after receive greater 1 then remove this code
                    return true;
                }

                // $list_order_service
                $list_order_service = \OrderService::findByOrderId($order->getId());
                if ($list_order_service) {
                    $point_member = 0;
                    $note = array();
                    foreach ($list_order_service as $order_service) {
                        //tinh diem tich luy theo tung dich vu
                        if ($order_service instanceof \OrderService) {
                            $discounted_money = $order_service->getDiscountedMoney();
                            $rate_exchange = \PointMemberExchangeSetting::retrieveByServiceCode($order_service->getServiceCode());
                            if ($rate_exchange instanceof \PointMemberExchangeSetting) {
                                $rate = $rate_exchange->getValue();
                                $point = 0;
                                if ($rate_exchange->getType() == \PointMemberExchangeSetting::TYPE_RATE) {
                                    $point = round($discounted_money / $rate,2);

                                    $point_member = $point_member + $point;
                                }

                                //ghi log tich luy diem cho user
                                if ($point > 0) {
                                    switch ($order_service->getServiceCode()) {
                                        case \Services::TYPE_BUYING:
                                            $note[] = "Tích lũy từ phí mua hàng {$point} điểm";
                                            break;
                                        case \Services::TYPE_CHECKING:
                                            $note[] = "Tích lũy từ phí kiểm hàng {$point} điểm";
                                            break;
                                        case \Services::TYPE_SHIPPING_CHINA_VIETNAM:
                                            $note[] = "Tích lũy từ phí vận chuyển quốc tế {$point} điểm";
                                            break;
                                        case \Services::TYPE_EXPRESS_CHINA_VIETNAM:
                                            $note[] = "Tích lũy từ phí vận chuyển quốc tế {$point} điểm";
                                            break;

                                    }
                                }

                            } else {
                                //DO NOTHING IF NOT FOUND SERVICE IN TABLE POINT_MEMBER_EXCHANGE_SETTING
                            }

                        }

                    }

                    if ($point_member > 0) {
                        //get level by point
                        $total_point = $user->getPointMember() + $point_member;
                        $query_level = \LevelSetting::select();
                        $query_level->where("{$total_point} >= from_score");
                        $query_level->andWhere("{$total_point} < to_score");
                        $query_level->setMaxResults(1);
                        $level_setting = $query_level->execute();
                        $new_level = $user->getLevelId(); //level current
                        if (count($level_setting) > 0) {
                            foreach ($level_setting as $level) {
                                if ($level instanceof \LevelSetting) {
                                    $new_level = $level->getId();
                                }
                            }
                        }
                        //save point for user
                        try {
                            $user->setPointMember($total_point);
                            $user->setLevelId($new_level);
                            $user->save();
                        } catch (\Exception $e) {
                            \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('SAVE point and level for customer with error:' . $e->getMessage()." at order id=".$order->getId());
                            return false;
                        }
                        //save log
                        try {
                            $member_score_history = new \MemberScoreHistory();
                            $member_score_history->setUserId($user->getId());
                            $member_score_history->setObjectId($order->getId());
                            $member_score_history->setObjectType("ORDER");
                            $member_score_history->setLevelId($new_level);
                            $member_score_history->setPoint($point_member);
                            $member_score_history->setTotalPoint($total_point);
                            if (count($note) > 0) {
                                $note_history_string = implode(", ", $note);
                            } else {
                                $note_history_string = '';
                            }

                            $member_score_history->setNote($note_history_string);
                            $member_score_history->setCreatedTime(new \DateTime());
                            $member_score_history->save();
                        }catch (\Exception $e) {
                            \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('SAVE log member score with error:' . $e->getMessage()." at order id=".$order->getId());
                            return false;
                        }
                    }

                    return true;

                }
            } else {
                \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('$user not instanceof \User');
                return false;
            }
        } else {
            \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('$order not instanceof \Order');
            return false;
        }
    }
}