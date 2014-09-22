<?php 
/**
 * DomesticShipping
 * @version		$Id$
 * @package		Model

 */
use Flywheel\Redis\Client;

require_once dirname(__FILE__) .'/Base/DomesticShippingBase.php';
class DomesticShipping extends \DomesticShippingBase {

    const STATUS_ACTIVE = "ACTIVE";
    const STATUS_INACTIVE = "INACTIVE";
    const REDIS_COD = "order_cod";

    protected function _beforeSave() {
        parent::_beforeSave();

        if ($this->isNew()) {
            //make order code
            if($this->getCode() == ""){
                $code = self::createCode($this->getBuyer());
                $this->setCode($code);
            }
        }
    }

    /**
     * Get Domestic shipping barcode
     * @return string
     */
    public function getDomesticBarcode(){
        $code = $this->getCode();
        if($code == ""){
            $code = self::createCode($this->getBuyer());
            $this->setCode($code);
            $this->save();
        }

        return $code;
    }

    /**
     * get order's buyer
     * @return bool|Users
     */
    public function getBuyer() {
        return \Users::retrieveById($this->getUserId());
    }

    /**
     * create domestic shipping code
     * @param Users $user
     * @return string
     * @throws InvalidArgumentException
     */
    public static function createCode(\Users $user){
        if ($user->isNew()) {
            throw new \InvalidArgumentException("Could not create order code with new user");
        }

        $current_bill_no = self::read()
            ->count('id')
            ->andWhere('DATE(`created_time`) = :today')
            ->setParameter(':today', date('Y-m-d'), \PDO::PARAM_STR)
            ->execute();
        $serial_part = str_pad($current_bill_no + 1, 2, '0', STR_PAD_LEFT);
        $working_year_sequence = Common::getWorkingYearSequence();
        return $working_year_sequence.date("md").'_'.$serial_part;
    }

    /**
     * Create new Domestic shipping
     * @param $address_id
     * @param $order_id_list
     * @param Users $created_by
     * @return bool
     * @throws Flywheel\Exception
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function createNewDomesticShipping($address_id , $order_id_list ,\Users $created_by){
        $address = \UserAddress::retrieveById($address_id);

        if(!$address instanceof \UserAddress){
            throw new \Flywheel\Exception("Không tồn tại địa chỉ. Xin thử lại");
        }

        $buyer = \Users::retrieveById($address->getUserId());

        if(!$buyer instanceof \Users){
            throw new \Flywheel\Exception("Không tồn tại khách hàng. Xin thử lại");
        }

        if(!empty($order_id_list)){

            $conn = \Flywheel\Db\Manager::getConnection();
            $conn->beginTransaction();

            try{
                $order_list = self::validateDataCreate($address_id , $order_id_list,$warehouse);

                $weight = \OrderPeer::getWeightByOrderList($order_list);

                if(empty($order_list)){
                    throw new \Flywheel\Exception("Không tồn tại đơn hàng nào.");
                }

                $purpose_charge_fee = self::getPurposeChargeFeeFromRedis($address_id);

                $domestic_shipping_fee = self::getDomesticShippingFeeFromRedis($address_id);

                $cod = OrderCod::getCod($address_id);

                $transaction_code = "";

                if($domestic_shipping_fee > 0){
                    $transfer = self::chargeDomesticShippingFee($buyer,$domestic_shipping_fee,$purpose_charge_fee);
                    $transaction_code = $transfer['transfer_transaction']["uid"];
                }

                $domestic_shipping = new self();
                $domestic_shipping->setUserAddressId($address_id);
                $domestic_shipping->setUserId($buyer->getId());
                $domestic_shipping->setWarehouse($warehouse);
                $domestic_shipping->setDomesticShippingFee($domestic_shipping_fee);
                $domestic_shipping->setPurposeChargeFee($purpose_charge_fee);
                $domestic_shipping->setCod($cod);
                $payment_amount = $transaction_code != "" ? $domestic_shipping_fee : 0;
                $domestic_shipping->setPaymentAmount($payment_amount);
                $domestic_shipping->setTransactionCode($transaction_code);
                $domestic_shipping->setCreatedBy($created_by->getId());
                $domestic_shipping->setWeight($weight);
                $domestic_shipping->setCreatedTime(new \Flywheel\Db\Type\DateTime());
                $domestic_shipping->setStatus(self::STATUS_ACTIVE);
                $result = $domestic_shipping->save();
                $domestic_shipping->getDomesticBarcode();
                if($result){
                    $flag = self::createDomesticShippingOrder($domestic_shipping,$order_list);

                    if($flag){
                        \OrderCod::delCod($address);
                        \DomesticShipping::delDomesticShippingFee($address);
                        $conn->commit();
                        return $domestic_shipping;
                    }else{
                        $conn->rollBack();
                        return false;
                    }
                }else{
                    $conn->rollBack();
                    return false;
                }
            }catch (\Exception $e){
                $conn->rollBack();
                throw $e;
            }
        }
        throw new \Flywheel\Exception("Không tồn tại đơn hàng nào. Vui lòng kiểm tra lại");
    }


    /**
     * @param $buyer
     * @param $domestic_shipping_fee
     * @param $purpose_charge_fee
     * @return mixed
     * @throws Exception
     */
    public static function chargeDomesticShippingFee($buyer,$domestic_shipping_fee,$purpose_charge_fee){
        try{
            $transfer = \SeuDo\Accountant\Util::charge($buyer, $domestic_shipping_fee, "Trừ tiền phí vận chuyển nội địa Việt Nam khi giao hàng", $purpose_charge_fee);
            try{
                $balance = $transfer['from_account']['balance'];
                \UsersPeer::changeAccountBalance($buyer, $balance);//change buyer account balance
                \UserTransaction::createTransactionChargeFee($buyer,$balance,$transfer['transfer_transaction'],"Trừ tiền phí vận chuyển nội địa Việt Nam khi giao hàng",$purpose_charge_fee);
                return $transfer;
            }catch (\Exception $ex){
                \SeuDo\Accountant\Util::refund($buyer, $domestic_shipping_fee, json_encode(array(
                        'detail' => 'Giao dịch trừ tiền phí vận chuyển nội địa Việt Nam khi giao hàng không thành công. Hoàn lại tiền.'
                    )),
                    'Giao dịch trừ tiền phí vận chuyển nội địa Việt Nam khi giao hàng không thành công');
                throw $ex;
            }
        }catch (\Exception $e){
            throw $e;
        }
    }


    /**
     * Create domestic shipping order
     * @param DomesticShipping $domestic_shipping
     * @param $order_list
     * @return bool
     * @throws Exception
     */
    public static function createDomesticShippingOrder(DomesticShipping $domestic_shipping,$order_list){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try{
            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    if($order->isLeftStatus(\Order::STATUS_DELIVERING)){
                        throw new \Flywheel\Exception("Order not left status Delivering. Not create bill domestic shipping ".$order->getCode());
                    }
                    $domestic_shipping_order = new DomesticShippingOrder();
                    $domestic_shipping_order->setOrderCode($order->getCode());
                    $domestic_shipping_order->setDomesticShippingId($domestic_shipping->getId());
                    $domestic_shipping_order->setCreatedTime(new \Flywheel\Db\Type\DateTime());
                    $result = $domestic_shipping_order->save();
                    if($result){
                        $order->changeStatus(\Order::STATUS_DELIVERING);
                        //update packages status of order
                        $package_list = \Packages::retrieveByOrderId($order->getId());
                        if ($package_list) {
                            foreach ($package_list as $package) {
                                if ($package instanceof \Packages) {
                                    if ($package->isStatus(\Packages::STATUS_CUSTOMER_CONFIRM_DELIVERY)) {
                                        $package->changeStatus(\Packages::STATUS_DELIVERING);
                                    }
                                }
                            }
                        }
                    }else{
                        $conn->rollBack();
                        return false;
                    }
                }
            }
            $conn->commit();
            return true;
        }catch (\Exception $e){
            $conn->rollBack();
            throw $e;
        }

    }

    /**
     * Check validate data create domestic shipping
     * @param $address_id
     * @param $order_id_list
     * @param $warehouse
     * @return array
     * @throws Flywheel\Exception
     * @throws InvalidArgumentException
     */
    public static function validateDataCreate($address_id , $order_id_list,&$warehouse){

        $warehouse = "";

        /**
         * @var \Order[]
         */
        $order_list = array();

        if(is_array($order_id_list) && !empty($order_id_list)){
            foreach ($order_id_list as $id) {
                $order = \Order::retrieveById($id);
                if(!$order instanceof \Order){
                    throw new \Flywheel\Exception("Tồn tại mã đơn hàng không có trên hệ thống, xin kiểm tra và thử lại.");
                }
                if($order->getUserAddressId() != $address_id){
                    throw new InvalidArgumentException("Tồn tại đơn hàng không thuộc địa chỉ này, vui lòng Refresh trình duyệt và thử lại.");
                }

                $warehouse = $order->getDestinationWarehouse();

                $order_list[] = $order;
            }

            return $order_list;
        }else{
            throw new \Flywheel\Exception("Không tồn tại mã đơn hàng nào. Xin thử lại.");
        }
    }


    /**
     * Set phí vận chuyển nội địa việt nam vào redis
     * @param $address
     * @param $fee
     * @param string $purpose_charge_fee
     * @return bool
     * @throws Exception
     */
    public static function setDomesticShippingFeeToRedis($address,$fee,$purpose_charge_fee = ""){
        try{
            $redis = Client::getConnection(OrderCod::CONFIG_REDIS);

            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }

            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::REDIS_COD);
                $key = REDIS_DOMESTIC_SHIPPING_FEE."{$address->getUserId()}";
                $result = $redis->hSet($key,$address->getId(),$fee);
                $redis->hSet($key,$address->getId()."_PURPOSE",$fee);
                $redis->expire($key,60*60*24*30);
                return true;
            }else{
                throw new InvalidArgumentException("Variable address not instanceof UserAddress");
            }
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * Hàm lấy Mục đích thu phí đối với địa chỉ từ redis
     * @param $address
     * @return string
     * @throws Exception
     */
    public static function delDomesticShippingFee($address){
        try{
            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }
            $result = false;
            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::REDIS_COD);
                $key = REDIS_DOMESTIC_SHIPPING_FEE."{$address->getUserId()}";
                $result = $redis->del($key);
            }
            return $result;

        }catch (\Exception $e){
            throw $e;
        }
    }


    /**
     * Hàm lấy Mục đích thu phí đối với địa chỉ từ redis
     * @param $address
     * @return string
     * @throws Exception
     */
    public static function getPurposeChargeFeeFromRedis($address){
        try{
            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }
            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::REDIS_COD);
                $key = REDIS_DOMESTIC_SHIPPING_FEE."{$address->getUserId()}";
                $purpose = $redis->hGet($key,$address->getId()."_PURPOSE");
                return $purpose;
            }

            return "";

        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * get domestic shipping Viet Nam fee to redis
     * @param $address
     * @return int|string
     * @throws Exception
     */
    public static function getDomesticShippingFeeFromRedis($address){
        try{
            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }
            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::REDIS_COD);
                $key = REDIS_DOMESTIC_SHIPPING_FEE."{$address->getUserId()}";
                $fee = $redis->hGet($key,$address->getId());
                $fee = floatval($fee) > 0 ? $fee : 0;
                return $fee;
            }

            return 0;

        }catch (\Exception $e){
            throw $e;
        }
    }


}