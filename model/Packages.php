<?php
use Flywheel\Redis\Client as RedisClient;

/**
 * Packages
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/PackagesBase.php';
class Packages extends \PackagesBase {

    //Define by ha nguyen
    const WAREHOUSE_CNGZ = "CNGZ";
    const WAREHOUSE_VNHN = "VNHN";
    const WAREHOUSE_VNSG = "VNSG";

    const CHECKING_STATUS_CHECKED = "CHECKED";
    const CHECKING_STATUS_NOT_YET_CHECKED = "NOT_YET_CHECKED";
    const CHECKING_STATUS_NOT_CHECKED = "NOT_CHECKED";

    const WAREHOUSE_STATUS_IN = "IN";
    const WAREHOUSE_STATUS_OUT = "OUT";

    const STATUS_SELLER_DELIVERY = 'SELLER_DELIVERY';
    const STATUS_RECEIVED_FROM_SELLER = 'RECEIVED_FROM_SELLER';
    const STATUS_TRANSPORTING = 'TRANSPORTING'; //package being transporting
    const STATUS_WAITING_FOR_DELIVERY = 'WAITING_DELIVERY'; //
    const STATUS_CUSTOMER_CONFIRM_DELIVERY = 'CONFIRM_DELIVERY'; //
    const STATUS_DELIVERING = 'DELIVERING'; //dang giao hàng
    const STATUS_RECEIVED = 'RECEIVED'; //buyer received order



    public static $statusLevel = array(
        self::STATUS_SELLER_DELIVERY,
        self::STATUS_RECEIVED_FROM_SELLER ,
        self::STATUS_TRANSPORTING,
        self::STATUS_WAITING_FOR_DELIVERY,
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY ,
        self::STATUS_DELIVERING ,
        self::STATUS_RECEIVED,
    );

    public static $statusTitle = array(
        self::STATUS_SELLER_DELIVERY => 'Người bán giao',
        self::STATUS_RECEIVED_FROM_SELLER => 'Nhận hàng',
        self::STATUS_TRANSPORTING => 'Vận chuyển',
        self::STATUS_WAITING_FOR_DELIVERY => 'Chờ giao hàng',
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY => 'Yêu cầu giao',
        self::STATUS_DELIVERING => 'Đang giao hàng',
        self::STATUS_RECEIVED => 'Khách nhận hàng'
    );

    /**
     * Get Order
     * @return bool|Order
     */
    public function getOrder(){
        $order = \Order::retrieveById($this->getOrderId());
        return $order;
    }

    /**
     * Remove all freight bill by order
     * @param Order $order
     * @return PDOStatement
     */
    public static function deleteByOrder(\Order $order) {
        return self::write()->delete('packages')
            ->where('order_id = :order_id')
            ->setParameter(':order_id', $order->getId(), \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * @param $order_id
     * @param $package_id
     * @param $weight
     * @throws Flywheel\Exception
     */
    public function updateWeight( $weight, $order_id ) {
        self::beginTransaction();
        try{
            self::setWeight( $weight );
            self::save();

            //Cập nhật lại tổng cân nặng trên đơn hàng
            $query = self::select();
            $query->andWhere(" `order_id` = {$order_id} ");
            $order_total_weight = $query->sum('weight')->execute();

            $order = \Order::retrieveById($order_id);
            $order->changeWeight( $order_total_weight );

            self::commit();

            return $order_total_weight;
        } catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('update_package_weight')->addError('has error when try to update package weight',array($e->getMessage()));
            self::rollBack();
            throw new \Flywheel\Exception('has error when try to update package weight');
        }
    }

    public function updateWeightToOrder( $order_id, $weight ) {
        $query = self::select();
        $query->andWhere(" `order_id` = {$order_id} ");
        $package = $query->sum('weight')->execute();
        $order = \Order::retrieveById($order_id);
        return $order->changeWeight( $weight );
    }

    public static function createPackageCode($order){
        if($order instanceof \Order){
            $order_code = $order->getCode();
            $packages_quantity = $order->getPackagesQuantity();
            $num = $packages_quantity +1;
            return $order_code."_".$num;
        }else{
            return false;
        }
    }

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'created_time',
            )
        );
    }

    /**
     * @param $fb
     * @return \Order[]
     */
    public static function getOrdersByFreightBill($fb) {
        $redis = RedisClient::getConnection(\Order::REDIS_CONFIG);
        $keys = $redis->keys(REDIS_FREIGHT_BILL.'*_'.$fb);
        $result = array();
        $orderIds = array();

        if (!empty($keys)) {
            foreach($keys as $key) {
                if (($data = $redis->hGetAll($key))) {
                    $obj = new self($data, false);
                    $orderIds[] = $obj->getOrderId();
                }
            }
        } else {
            /** @var \Packages[] $data */
            $data = self::select()->where('`freight_bill` = :bill')
                ->setParameter(':bill', $fb, \PDO::PARAM_STR)
                ->execute();
            if (is_array($data)) {
                foreach($data as $d) {
                    $orderIds[] = $d->getOrderId();
                }
            }
        }

        $orderIds = array_unique($orderIds);
        for($i = 0, $size = sizeof($orderIds); $i < $size; ++$i) {
            if ($order = \Order::retrieveById($orderIds[$i])) {
                $result[] = $order;
            }
        }

        return $result;
    }

    /**
     * @param $fb
     * @param \Order $order
     * @return bool
     */
    public static function checkIsDuplicateFreightBill($fb, $order) {
        return sizeof(self::getDuplicateFreightBill($fb, $order)) > 0;
    }

    /**
     * @param $fb
     * @param $order
     * @return array
     */
    public static function getDuplicateFreightBill($fb, $order) {
        if ($order instanceof \Order) {
            $order = $order->getId();
        }

        $redis = RedisClient::getConnection(\Order::REDIS_CONFIG);
        $keys = $redis->keys(REDIS_FREIGHT_BILL.'*_'.$fb);
        $result = array();

        if (!empty($keys)) {
            foreach($keys as $key) {
                if (($data = $redis->hGetAll($key))) {
                    $obj = new self($data, false);
                    if ($obj->getOrderId() != $order) {
                        $result[] = $obj->getOrderId();
                    }
                }
            }
        } else {
            /** @var \Packages[] $data */
            $data = self::select()->where('`freight_bill` = :bill AND `order_id` != :order_id')
                ->setParameter(':bill', $fb, \PDO::PARAM_STR)
                ->setParameter(':order_id', $order, \PDO::PARAM_INT)
                ->execute();
            if (is_array($data)) {
                foreach($data as $d) {
                    $result[] = $d->getOrderId();
                }
            }
        }

        return array_unique($result);
    }

    protected function _afterSave() {
        parent::_afterSave();
        self::pushToRedis($this);
    }

    protected function _afterDelete() {
        parent::_afterDelete();
        $redis = RedisClient::getConnection(\Order::REDIS_CONFIG);
        $redis->delete(REDIS_FREIGHT_BILL .$this->getOrderId() .'_' .$this->getFreightBill());
    }

    /**
     * Find by orderId
     * fix problem with Order is keyword
     * @param int $order_id
     * @return Packages[]
     */
    public static function findByOrderId($order_id) {
        return self::select()->where('`order_id` = :order_id')
            ->setParameter(':order_id', $order_id, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * search packages by freight bill
     * @param $bill
     * @return \Packages[]
     */
    public static function searchByFreightBill($bill) {
        if (!$bill) {
            return array();
        }

        $q = self::select()
            ->where('`freight_bill` LIKE :keyword')
            ->setParameter(':keyword', "%{$bill}%", \PDO::PARAM_STR);

        $packages = $q->execute();
        return $packages;
    }

    /**
     * @param int $order_id
     * @param bool $assoc if true return associate with freight_bill as key
     * @return \Packages[]
     */
    public static function retrieveByOrderId($order_id, $assoc = false) {
        $redis = RedisClient::getConnection(\Order::REDIS_CONFIG);
        $keys = $redis->keys(REDIS_FREIGHT_BILL.$order_id .'_*');
        $result = array();

        if (empty($keys)) {
            $freightBills = self::select()
                ->where('`order_id` = ?')
                ->setParameter(0, $order_id, \PDO::PARAM_INT)
                ->execute();

            if ($freightBills) {
                foreach($freightBills as $freightBill) {
                    if ($assoc) {
                        $result[$freightBill->getFreightBill()] = $freightBill;
                    } else {
                        $result[] = $freightBill;
                    }
                }
            }
        } else {
            for($i = 0, $size = sizeof($keys); $i < $size; ++$i) {
                if ($data = $redis->hGetAll($keys[$i])) {
                    $obj = new self($data, false);
                    if ($assoc) {
                        $result[$obj->getFreightBill()] = $obj;
                    } else {
                        $result[] = $obj;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param $freight_bill
     * @param $order_id
     * @return \Packages[]
     */
    public static function findOneByFreightBillAndOrderId($freight_bill, $order_id) {
        $data = self::read()->where('`freight_bill` = ? AND `order_id` = ?')
            ->setMaxResults(1)
            ->setParameters(array($freight_bill, $order_id), array(\PDO::PARAM_STR, \PDO::PARAM_INT))
            ->execute()
            ->fetchObject(self::getTableName(), array(null, false));
    }

    /**
     * retrieve object by freight bill and orderid
     * @param string $bill
     * @param \Order|int $order
     * @return bool|Packages
     */
    public static function retrieveByFreightBillAndOrderId($bill, $order) {
        if ($order instanceof \Order) {
            $order = $order->getId();
        }

        //check from pool
        /** @var \Packages[] $objs */
        $objs = static::getInstancesFromPool();
        foreach($objs as $obj) {
            if ($obj->getFreightBill() == $bill && $obj->getOrderId() == $order) {
                return $obj;
            }
        }

        if (($data = RedisClient::getConnection(\Order::REDIS_CONFIG)->hGetAll(REDIS_FREIGHT_BILL ."{$order}_{$bill}"))) {
            $obj = new self($data, false);
            return $obj;
        }

        //from database
        $obj = self::findOneByFreightBillAndOrderId($bill, $order);
        if ($obj) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    public static function pushToRedis(\Packages $obj) {
        $client = RedisClient::getConnection(\Order::REDIS_CONFIG);
        $client->hMset(REDIS_FREIGHT_BILL .$obj->getOrderId() .'_' .$obj->getFreightBill() , $obj->toArray());
    }

    public function isBeforeStatus($status, $includedCurrentStatus = false){

        if ($includedCurrentStatus && $this->getStatus() === $status) {

            return true;
        }

        if($status == ''){
            return false;
        }

        if($this->getStatus()==''){
            return true;
        }
        $before_status = array();

        $key = array_search($status,\Packages::$statusLevel);

        for ($i = $key-1 ; $i >= 0 ;$i--) {
            $before_status[] = \Packages::$statusLevel[$i];
        }
        if(in_array($this->getStatus(),$before_status)){
            return true;
        }
       // \SeuDo\Logger::factory('update_package_weight')->addError('debug status='.$key."//status=".$this->getStatus(),array(json_encode($before_status)));
        return false;
    }

    public function isStatus($status){
        if ($this->getStatus() == $status) {
            return true;
        }else{
            return false;
        }
    }

    public function changeStatus($status) {

        try {
            $status = strtoupper($status);
            $time = new DateTime();
            switch ($status) {
                case self::STATUS_SELLER_DELIVERY:
                    $this->setSellerDeliveredTime($time);
                    break;

                case self::STATUS_RECEIVED_FROM_SELLER:
                    $this->setReceivedFromSellerTime($time);
                    break;
                case self::STATUS_TRANSPORTING:
                    $this->setTransportingTime($time);
                    break;
                case self::STATUS_WAITING_FOR_DELIVERY:
                    $this->setWaitingDeliveryTime($time);
                    break;
                case self::STATUS_CUSTOMER_CONFIRM_DELIVERY:
                    $this->setConfirmDeliveryTime($time);
                    break;
                case self::STATUS_DELIVERING:
                    $this->setDeliveringTime($time);
                    break;
                case self::STATUS_RECEIVED:
                    $this->setReceivedTime($time);
                    break;
                default:
                    break;
            }

            $this->setStatus($status);
            if(!$this->save()) {
                throw new \RuntimeException("Could not change order status. " .$this->getValidationFailuresMessage("\n"));
            }

            return true;
        }catch (\Flywheel\Exception $e) {

            throw $e;
        }
    }

    public function checkUpdateOrderByPackage($order_id){
        //get all package of order
        $list_package =self::retrieveByOrderId($order_id);
        //update order if current package has newest status

        if (count($list_package) == 1) {
           return true;
        } elseif (count($list_package) > 1) {
            foreach ($list_package as $item_package) {
                if ($item_package->getId() != $this->getId()) {
                    $level_item_package = array_search($item_package->getStatus(), self::$statusLevel);
                    $level_current_package = array_search($this->getStatus(), self::$statusLevel);
                    if ($level_current_package <= $level_item_package) {

                        return false;
                    }
                }
            }
            return true;
        }
    }
}