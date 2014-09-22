<?php
use Flywheel\Db\Type\DateTime;
use Flywheel\Redis\Client as RedisClient;
use \mongodb\OrderCommentResource\BaseContext;
use \mongodb\OrderCommentResource\Chat;

/**
 * Order
 * @version        $Id$
 * @package        Model
 */

require_once dirname(__FILE__) . '/Base/OrderBase.php';
class Order extends \OrderBase
{
    //Define by quyen
    const WAREHOUSE_CNGZ = "CNGZ";
    const WAREHOUSE_VNHN = "VNHN";
    const WAREHOUSE_VNSG = "VNSG";

    const CHECKING_STATUS_CHECKED = "CHECKED";
    const CHECKING_STATUS_NOT_YET_CHECKED = "NOT_YET_CHECKED";
    const CHECKING_STATUS_NOT_CHECKED = "NOT_CHECKED";

    const WAREHOUSE_STATUS_IN = "IN";
    const WAREHOUSE_STATUS_OUT = "OUT";

    const CUSTOMER_CONFIRM_NONE = "NONE";
    const CUSTOMER_CONFIRM_WAIT = "WAIT";
    const CUSTOMER_CONFIRM_CONFIRMED = "CONFIRMED";

    const STATUS_INIT = 'INIT'; // Tạo đơn
    const STATUS_DEPOSITED = 'DEPOSITED'; // Đã đặt cọc
    const STATUS_BUYING = 'BUYING'; // phân đơn người mua hàng

    const STATUS_NEGOTIATING = 'NEGOTIATING'; // Đang đàm phán
    const STATUS_WAITING_BUYER_CONFIRM = 'WAITING_BUYER_CONFIRM'; // Đợi khách xác nhận mua hàng
    const STATUS_BUYER_CONFIRMED = 'BUYER_CONFIRMED'; // Khách hàng đã xác nhận
    const STATUS_WAITING_FOR_APPROVAL = "WAITING_FOR_APPROVAL";//Chờ phê duyệt
    const STATUS_NEGOTIATED = 'NEGOTIATED'; // Đã đàm phán xong
    const STATUS_BOUGHT = 'BOUGHT';//Nhận mua

    const STATUS_SELLER_DELIVERY = 'SELLER_DELIVERY';
    const STATUS_RECEIVED_FROM_SELLER = 'RECEIVED_FROM_SELLER';

    const STATUS_CHECKING = 'CHECKING';
    const STATUS_CHECKED = 'CHECKED';

    const STATUS_TRANSPORTING = 'TRANSPORTING'; //order being transporting

    const STATUS_WAITING_FOR_DELIVERY = 'WAITING_DELIVERY'; //
    const STATUS_CUSTOMER_CONFIRM_DELIVERY = 'CONFIRM_DELIVERY'; //
    const STATUS_DELIVERING = 'DELIVERING'; //dang giao hàng

    const STATUS_RECEIVED = 'RECEIVED'; //buyer received order

    const STATUS_COMPLAINT = 'COMPLAINT';//Khiếu nại
    const STATUS_OUT_OF_STOCK = 'OUT_OF_STOCK';

    const STATUS_CANCELLED = 'CANCELLED';//Hủy bỏ

    const ERROR_PARAMS_MISSING = 'ERROR_PARAMS_MISSING';
    const ORDER_CODE_EXIST = 'ORDER_CODE_EXIST';
    const SERVICE_MAPPING_FALSE = 'SERVICE_MAPPING_FALSE';
    const CART_ERROR = 'CART_ERROR';
    const HAS_ERROR_WHEN_CREATE = 'HAS_ERROR_WHEN_CREATE';


    const TYPE_ERROR = 'ERROR';
    const TYPE_SUCCESS = 'SUCCESS';

    const TELLERS_ID = "tellers_id";
    const PAID_STAFF_ID = "paid_staff_id";

    const SEUDO_SERVICES = "SD";

    const REDIS_CONFIG = 'order_active';

    const PER_PAGE = 30;

    public static $statusCustomer = array(
        self::STATUS_INIT,
        self::STATUS_DEPOSITED,
        self::STATUS_BUYING,
        self::STATUS_BOUGHT,
        self::STATUS_CHECKING,
        self::STATUS_CHECKED,
        self::STATUS_DELIVERING,
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY
    );

    public static $notificationCustomer = array(
        self::STATUS_SELLER_DELIVERY,//ng ban giao hang
        self::STATUS_RECEIVED_FROM_SELLER,//nhan hang tu ng ban
        self::STATUS_CHECKED,//da kiem hang
        self::STATUS_BOUGHT,//mua hang tu ng ban
        self::STATUS_WAITING_FOR_DELIVERY,//chờ giao
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY,//yeu cau giao hang: yc tu khach hoặc sau 3 ngay he thong tu chuyen trang thai
        self::STATUS_DELIVERING
    );

    public $itemInOrder = array();

    /**
     * @var array for FreightBill (backend)
     */
    public static $statusFreight = array(
        self::STATUS_BOUGHT => 'Đã mua hàng',
        self::STATUS_SELLER_DELIVERY => "Người bán giao",
        self::STATUS_RECEIVED_FROM_SELLER => "Seudo nhận",
        self::STATUS_TRANSPORTING => "Vận chuyển",
        self::STATUS_CHECKING => 'Đang kiểm hàng',
        self::STATUS_CHECKED => 'Đã kiểm hàng',
        self::STATUS_WAITING_FOR_DELIVERY => "Chờ giao hàng",
        self::STATUS_DELIVERING => "Đang giao hàng",
        self::STATUS_RECEIVED => 'Đã nhận hàng',
        self::STATUS_COMPLAINT => "Khiếu nại"
    );

    public static $statusWarehouse = array(
        self::WAREHOUSE_STATUS_IN => 'Trong kho',
        self::WAREHOUSE_STATUS_OUT => "Xuất kho"
    );

    public static $statusTitle = array(
        self::STATUS_INIT => 'Chưa đặt cọc',
        self::STATUS_DEPOSITED => 'Đã đặt cọc',
        self::STATUS_BUYING => 'Đang mua hàng',
        self::STATUS_NEGOTIATING => 'Đang đàm phán',
        self::STATUS_NEGOTIATED => 'Đã đàm phán',
        self::STATUS_WAITING_BUYER_CONFIRM => 'Chờ khách xác nhận',
        self::STATUS_BUYER_CONFIRMED => 'Khách đã xác nhận',

        self::STATUS_BOUGHT => 'Đã mua hàng',
        self::STATUS_SELLER_DELIVERY => "Người bán giao",
        self::STATUS_RECEIVED_FROM_SELLER => "Nhận hàng",
        self::STATUS_TRANSPORTING => "Vận chuyển",
        self::STATUS_CHECKING => 'Đang kiểm hàng',
        self::STATUS_CHECKED => 'Đã kiểm hàng',
        self::STATUS_WAITING_FOR_DELIVERY => "Chờ giao hàng",
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY => "Yêu cầu giao",
        self::STATUS_DELIVERING => "Đang giao hàng",
        self::STATUS_RECEIVED => 'Khách nhận hàng',
        self::STATUS_COMPLAINT => "Khiếu nại",
        self::STATUS_OUT_OF_STOCK => 'Hết hàng',
        self::STATUS_CANCELLED => "Hủy bỏ"
    );

    public static $statusLevel = array(
        self::STATUS_INIT ,
        self::STATUS_DEPOSITED ,
        self::STATUS_BUYING ,
        self::STATUS_NEGOTIATING ,
        self::STATUS_NEGOTIATED ,
        self::STATUS_BOUGHT ,
        self::STATUS_SELLER_DELIVERY,
        self::STATUS_RECEIVED_FROM_SELLER ,
        self::STATUS_TRANSPORTING,
        self::STATUS_CHECKING,
        self::STATUS_CHECKED,
        self::STATUS_WAITING_FOR_DELIVERY,
        self::STATUS_CUSTOMER_CONFIRM_DELIVERY ,
        self::STATUS_DELIVERING ,
        self::STATUS_RECEIVED,
    );

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'modify_attr' => 'modified_time'
            )
        );
    }

    protected function _beforeSave() {
        parent::_beforeSave();

        if ($this->isNew()) {
            //make order code
            do {
                $code = self::createCode($this->getBuyer());
                $check = self::retrieveByCode($code);
            } while($check);

            $this->setCode($code);

//            $this->createRecipientName();
        }
    }

    protected function _afterSave() {
        parent::_afterSave();
        $this->reload();
        self::pushToRedis($this);
    }

    protected function _afterDelete() {
        $redis = RedisClient::getConnection(self::getTableName());
        $redis->delete(REDIS_ORDER .$this->getId(), REDIS_ORDER_CODE .$this->getCode());
    }

    public function getOrderId(){
        return $this->getId();
    }

    public function delete() {
        $this->beginTransaction();
        try {
            parent::delete();
            $order_id = $this->getId();

            //remove order's item
            \OrderItemPeer::deleteByOrder($this);

            //remove order's services
            \OrderService::deleteByOrder($this);

            //remove freight bill
            \Packages::deleteByOrder($this);


            //delete comment order
            $db_order = \SeuDo\MongoDB::getConnection('order_comment');
            $db_order->where("order_id", $order_id);
            $db_order->deleteAll('order_comment');
            //delete products in order
            \OrderItem::deleteItemsByOrderId($this->getId());
            //delete comments of products
            $db_order_item = \SeuDo\MongoDB::getConnection('order_item_comment');
            $db_order_item->where("order_id", $order_id);
            $db_order_item->deleteAll('order_item_comment');

            $this->commit();
        } catch(\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function afterStatus(){
        $status = $this->getStatus();
        $status_array = array();

        $key = array_search($status,\Order::$statusLevel);

        for ($i = $key+1 ; $i < count(\Order::$statusLevel) ;$i++) {
            $status_array[] = \Order::$statusLevel[$i];
        }

        return $status_array;
    }

    /**
     * @return array
     */
    public function beforeStatus(){
        $status = $this->getStatus();
        $status_array = array();

        $key = array_search($status,\Order::$statusLevel);

        for ($i = $key+1 ; $i < count(\Order::$statusLevel) ;$i++) {
            $status_array[] = \Order::$statusLevel[$i];
        }

        return $status_array;
    }

    /**
     * Check is left status in order
     * @param $status
     * @return bool
     */
    public function isLeftStatus($status){
        $after_status = \OrderPeer::getAfterStatus($status);

        if(empty($after_status)){
            return false;
        }
        if(end($after_status) == $this->getStatus()){
            return true;
        }
        return false;
    }

    /**
     * Is After Status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isAfterStatus($status, $includedCurrentStatus = false){
        if ($includedCurrentStatus && $this->getStatus() == $status) {
            return true;
        }
        $after_status = \OrderPeer::getAfterStatus($status);

        if(empty($after_status)){
            return false;
        }
        if(in_array($this->getStatus(),$after_status)){
            return true;
        }
        return false;
    }

    /**
     * Is before status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isBeforeStatus($status, $includedCurrentStatus = false){
        if ($includedCurrentStatus && $this->getStatus() == $status) {
            return true;
        }

        $before_status = \OrderPeer::getBeforeStatus($status);
        if(empty($before_status)){
            return false;
        }
        if(in_array($this->getStatus(),$before_status)){
            return true;
        }
        return false;
    }

    /**
     * check current status is between start status and end status?
     * @param $start_status
     * @param $end_status
     * @return bool
     */
    public function isBetweenStatus($start_status,$end_status){
        $between_status = \OrderPeer::getBetweenStatus($start_status,$end_status);
        if(in_array($this->getStatus(),$between_status)){
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getColorByOrderStatus(){
        $color = "";
        //Trước khi đặt cọc
        if ( $this->isBeforeStatus( \Order::STATUS_DEPOSITED ) ) {
            $color = "font-black";
        }
        //Từ đặt cọc -> mua hàng
        if ( $this->isAfterStatus( \Order::STATUS_BUYING ) && $this->isBeforeStatus( \Order::STATUS_BOUGHT ) ) {
            $color = "font-yellow";
        }
        //Từ người bán giao hàng -> Trước chờ giao
        if ( $this->isAfterStatus( \Order::STATUS_SELLER_DELIVERY ) && $this->isBeforeStatus( \Order::STATUS_WAITING_FOR_DELIVERY ) ) {
            $color = "font-green";
        }
        //Trước giao hàng (Y/c giao)
        if ( $this->getStatus() == \Order::STATUS_CUSTOMER_CONFIRM_DELIVERY ) {
            $color = "font-blue";
        }
        //Đang giao
        if( $this->getStatus() == \Order::STATUS_DELIVERING ) {
            $color = "font-black";
        }
        //Nhận hàng, Hết hàng, Hủy
        if ( $this->getStatus() == \Order::STATUS_RECEIVED
            || $this->getStatus() == \Order::STATUS_OUT_OF_STOCK
            || $this->getStatus() == \Order::STATUS_CANCELLED ) {
            $color = "font-gray";
        }
        return $color;

    }

    /**
     * get order's buyer
     * @return bool|Users
     */
    public function getBuyer() {
        return \Users::retrieveById($this->getBuyerId());
    }

    public function getStatusTitle()
    {
        return static::$statusTitle[$this->getStatus()];
    }

    public function getWarehouseStatusTitle()
    {
        if($this->getWarehouseStatus() == ""){
            return "";
        }
        return static::$statusWarehouse[trim($this->getWarehouseStatus())];
    }

    /**
     * Get Missing Money
     * @return float
     */
    public function getMissingMoney(){
        // Tiền còn thiếu = realpayment amount - (Tổng tiền hàng + số tiền đã trả lại);
        $missing_amount = $this->getRealPaymentAmount() - (($this->getTotalAmount() + $this->getRealRefundAmount()));
        return Common::roundingMoney($missing_amount);
    }

    /**
     * Checking eligibility
     *
     * @return bool
     */
    public function checkingEligibility() {
        return $this->isBeforeStatus(self::STATUS_CUSTOMER_CONFIRM_DELIVERY);
    }

    /**
     * check order can change weight
     * @return bool
     */
    public function canChangeWeight() {
        return !($this->getStatus() == \Order::STATUS_DELIVERING
                || $this->getStatus() == \Order::STATUS_RECEIVED
                || $this->getStatus() == \Order::STATUS_COMPLAINT);
    }



    /**
     * Retrieve object by id
     * @param int $id
     * @return bool|\Order
     */
    public static function retrieveById($id) {
        if (!$id) {
            return false;
        }

        if (($obj = self::getInstanceFromPool($id))) {
            return $obj;
        }

        if (($data = self::retrieveFromRedis($id))) {
            $obj = new self($data, false);
            return $obj;
        }

        $obj = self::findOneById($id);
        if ($obj) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * Retrieve a object by customer code
     * get from redis if stored
     * @param string $code
     * @return bool|Order
     */
    public static function retrieveByCode($code) {
        //check from pool
        /** @var \Order[] $objs */
        $objs = static::getInstancesFromPool();
        foreach($objs as $obj) {
            if ($obj->code == $code) {
                return $obj;
            }
        }

        if (($id = RedisClient::getConnection(self::getTableName())->get(REDIS_ORDER_CODE .$code))) {
            return self::retrieveById($id);
        }

        if (($obj = self::findOneByCode($code))) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * retrieve data from redis
     * @param $order_id
     * @return array|null
     */
    public static function retrieveFromRedis($order_id) {
        return RedisClient::getConnection(self::getTableName())->hGetAll(REDIS_ORDER .$order_id);
    }

    /**
     * @param \Order $obj
     * @return bool
     */
    public static function pushToRedis($obj) {
        if (!$obj || !($obj instanceof \Order)) {
            return false;
        }

        $data = $obj->toArray();
        foreach ($data as $k => $v) {
            if ($v instanceof DateTime) {
                $data[$k] = $v->toString();
            }
        }

        $timeout = 2592000; //1 months

        $redis = RedisClient::getConnection(self::getTableName());
        $redis->hMset(REDIS_ORDER .$obj->getId(), $data);
        $redis->expire(REDIS_ORDER .$obj->getId(), $timeout);
        $redis->set(REDIS_ORDER_CODE .$obj->getCode(), $obj->getId());
        $redis->expire(REDIS_ORDER_CODE .$obj->getCode(), $timeout);
        return true;
    }

    public static function getLastModifiedTime(){
        try{
            $query = \Order::read();
            $query->addSelect("DATE_FORMAT(modified_time, '%Y-%m-%d %H:%i:%s') AS modified_time");
            $query->orderBy("modified_time", "DESC");
            $query->setMaxResults(1);
            $result = $query->execute()->fetch();
            if(isset($result["modified_time"])){
                return $result["modified_time"];
            }else{
                return "";
            }
        }catch (\Flywheel\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check could cancel order
     *
     * @return bool
     */
    public function cancellingEligibility() {
        return (in_array($this->getStatus(), array(
            \Order::STATUS_CHECKED,
            \Order::STATUS_WAITING_FOR_DELIVERY,
            \Order::STATUS_CUSTOMER_CONFIRM_DELIVERY,
            \Order::STATUS_DELIVERING,
            \Order::STATUS_RECEIVED,
        )));
    }

    /**
     * todo sản phẩm nằm trong order
     * @param array $item
     * @return bool
     */
    public function setItemInOrder($item = array())
    {
        if (!empty($item)) {
            $this->itemInOrder = $item;
        }
        return false;
    }


    /**
     * todo lấy danh sách item trong order
     * @return array
     */
    public function getItemInOrder() {
        if(empty($this->itemInOrder)) {
            return \OrderPeer::getOrderItem($this);
        }
        return $this->itemInOrder;
    }

    public function getCountOrder($conditions = array()) {
        $query = \Order::read();
        if (!empty($conditions)) {
            $query->where(' 1 ');
            foreach ($conditions as $condition) {
                $query->andWhere($condition);
            }
        }

        return $query->count('id')->execute();
    }

    /**
     * Get order avatar
     * @return string
     */
    public function getOrderAvatar(){
        $avatar = $this->getAvatar();
        if($avatar == ""){
            $order_item = \OrderItem::retrieveByOrderId($this->getId());
            if($order_item instanceof \OrderItem){
                $avatar  = $order_item->getImage();
                $this->setAvatar($avatar);
                $this->save();
            }
        }
        return $avatar;
    }

    /**
     * @todo to change instance order's status
     * @param $status
     * @throws Exception
     * @throws Flywheel\Exception
     * @return boolean
     */
    public function changeStatus($status) {
//        if(!$this->isLeftStatus($status)){
//
//        }
        $this->beginTransaction();
        try {
            $status = strtoupper($status);
            $time = new DateTime();
            switch ($status) {
                case self::STATUS_DEPOSITED:
                    $this->setDepositTime($time);
                    break;
                case self::STATUS_BUYING:
                    $this->setBuyingTime($time);
                    $this->setTellersAssignedTime($time);
                    break;
                case self::STATUS_NEGOTIATING:
                    $this->setNegotiatingTime($time);
                    break;
                case self::STATUS_NEGOTIATED:
                    $this->setNegotiatedTime($time);
                    break;
                case self::STATUS_BOUGHT:
                    $this->setBoughtTime($time);
                    /* add queue to push to logistic */
                    break;
                case self::STATUS_TRANSPORTING:
                    $this->setTransportingTime($time);
                    break;
                case self::STATUS_CHECKING:
                    $this->setCheckingTime($time);
                    break;
                case self::STATUS_CHECKED:
                    $this->setCheckingStatus(\Order::CHECKING_STATUS_CHECKED);
                    $this->setCheckedTime($time);
                    break;
                case self::STATUS_WAITING_FOR_DELIVERY:
                    $this->setRealServiceAmount($this->getServiceFee()+$this->getDomesticShippingFeeVnd());
                    $this->setWaitingDeliveryTime($time);
                    break;
                case self::STATUS_CUSTOMER_CONFIRM_DELIVERY:
                    $this->setConfirmDeliveryTime($time);
                    break;
                case self::STATUS_DELIVERING:
                    $this->setDeliveredTime($time);
                    break;
                case self::STATUS_RECEIVED:
                    $this->setReceivedTime($time);
                    break;
                case self::STATUS_OUT_OF_STOCK:
                    $this->setOutOfStockTime($time);
                    break;
                case self::STATUS_SELLER_DELIVERY:
                    $this->setSellerDeliveredTime($time);
                    break;
                case self::STATUS_RECEIVED_FROM_SELLER:
                    $this->setReceivedFromSellerTime($time);
                    break;
                case self::STATUS_COMPLAINT:
                    $this->setComplaintTime($time);
                    break;
                case self::STATUS_CANCELLED:
                    $this->setCancelledTime($time);
                    break;
                default:
                    break;
            }

            if ($this->getStatus() != $status) {
                if($status == self::STATUS_CHECKING || $status == self::STATUS_CHECKED) {
                    if($this->needToChecking() == false) {
                        throw new \Flywheel\Exception($this->getCode().' is not need to check !');
                    }else{
                        $this->setStatus($status);
                    }
                }else{
                    $this->setStatus($status);
                }
            }
            if(!$this->save()) {
                throw new \RuntimeException("Could not change order status. " .$this->getValidationFailuresMessage("\n"));
            }
            //\SeuDo\Logger::factory('order')->addAlert('update order info',array($this->getAttributes()));
            //$this->updateInfo();
            $this->commit();
            return true;
        }catch (\Flywheel\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Get Readl Amount Ndt
     * @return float
     */
    public function getRealAmountNdt(){
        $orderItem = \OrderPeer::getOrderItem($this);

        $total_order_price_ndt = 0;
        foreach ($orderItem as $item) {
            $price_cny = $item->getPricePromotion() > 0 ? $item->getPricePromotion() :
                $item->getPriceOrigin();
            if(floatval($price_cny) <= 0){
                $price_cny = floatval($item->getPricePromotion()) > 0 ? $item->getPricePromotion() : floatval($item->getPriceOrigin());
                $price_cny = $price_cny > 0 ? $price_cny : $item->getPrice() / \ExchangeRate::getExchange();
            }
            $total_order_price_ndt += $price_cny*$item->getReciveQuantity();
        }

        return floatval($total_order_price_ndt);
    }

    /**
     * Save Deposit ratio
     * @throws Flywheel\Exception
     */
    public function saveDepositRatio(){
        try{
            $deposit_ratio = 0.5;
            $this->setDepositRatio($deposit_ratio);
            $this->save();
        }catch (\Exception $e){
            throw new \Flywheel\Exception("Can't save Deposit ratio");
        }
    }

    /**
     * Get Address Full
     * @return array
     */
    public function getAddressFull(){
        $user_address = \UserAddress::retrieveById($this->getUserAddressId());
        if($user_address instanceof \UserAddress){
            $address_full = $user_address->toFullArray();
            return $address_full;
        }
        return array();
    }

    /**
     * Get Address
     * @return \UserAddress
     */
    public function getAddress(){
        $user_address = \UserAddress::retrieveById($this->getUserAddressId());
        return $user_address;
    }


    /**
     * @TODO check cart item is valid
     * @param $cartItem
     * @return bool
     */
    public function validCartItem($cartItem) {
        $flag = false;
        if (!empty ($cartItem)) {
            foreach ($cartItem as $cart) {
                if (($cart instanceof \CartItem)
                        && intval($cart->getPriceVnd()) > 0) {
                    $flag = true;
                } else {
                    \SeuDo\Logger::factory('order')->addWarning("Cart item not save Order",
                        array(
                            "item_site_id" => $cart->item_id,
                            'home_land' => $cart->site,
                            "tool" => $cart->tool,
                            "price" => $cart->price,
                            "price_vnd" => $cart->price_vnd
                        )
                    );
                    $flag = false;
                    break;
                }

            }
        }
        return $flag;
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @param Users $user
     * @param $userAddressId
     * @return \Order[]
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function createOrder($data = array(), \Users $user, $userAddressId) {
        if (empty($data)) {
            throw new \InvalidArgumentException("Could not create new order with empty data");
        }

        if ($user->isNew()) {
            throw new \InvalidArgumentException("Could not create new order with new user");
        }

        $this->beginTransaction();
        $newOrders = array();

        try {
            foreach ($data as $sellerName => $sellerData) {
                $shopInfo = $sellerData['shopInfo'];
                $orderInfo = $sellerData['orderInfo'];
                $cartItems = $sellerData['cartItems'];
                $services = $sellerData['services'];

                if (empty($shopInfo) || empty($orderInfo) || empty($cartItems)) {
                    continue;
                }

                $order = new \Order();
                $order->setSellerName($sellerName);
                $order->setSellerInfo($sellerName);
                $order->setBuyerId($user->getId());
                $order->setExchange(ExchangeRate::getExchange());

                if (isset($shopInfo)) {
                    if (isset($shopInfo['aliwangwang'])) {
                        $order->setSellerAliwang($shopInfo['aliwangwang']);
                    }
                    if (isset($shopInfo['homeland'])) {
                        $order->setSellerHomeland($shopInfo['homeland']);
                    }
                }

                if (isset($orderInfo)) {
                    $orderQuantity = $pendingQuantity = $receiveQuantity = $orderInfo['totalQuantity'];;

                    $order->setOrderQuantity($orderQuantity);
                    $order->setPendingQuantity($pendingQuantity);
                    $order->setReciveQuantity($receiveQuantity);


                    $orderAmount = $realAmount = $orderInfo['totalAmount'];

                    $order->setOrderAmount($orderAmount);
                    $order->setRealAmount($realAmount);

                    if ($orderInfo['totalWeight']) {
                        $order->setWeight($orderInfo['totalWeight']);
                    }
                    $order->setServiceFee($orderInfo['totalServiceFee']);
                    $totalAmount = $order->getRealAmount()  + $order->getServiceFee();
                    $order->setTotalAmount($totalAmount);
                }

                $order->setStatus(self::STATUS_INIT);
                $order->setUserAddressId($userAddressId);
                $order->setCreatedTime(new DateTime('now', null));

//                $order->setExpireTime(new DateTime('+1 day'));

                if(!$order->save()) {
                    throw new \RuntimeException("Could not save order. Validation failure messages:" .$order->getValidationFailuresMessage("\n"));
                }

                //creating order's comments if exsited before
                if(isset($shopInfo['comment']) && !empty($shopInfo['comment'])) {
                    foreach ($shopInfo['comment'] as $comment) {
                        $message = \OrderComment::convertToText($comment->content);
                        $context = new Chat($message);
                        $type = \mongodb\OrderComment::TYPE_EXTERNAL;
                        $type_context = "CHAT";
                        \OrderComment::addComment($user->getId(), $order->getId(), $type, $context, true,
                            $type_context);
                    }
                }

                /* mapping service to order */
                $result = \OrderService::mappingToOrder($services, $order);
                if ($result === false) {
                    \SeuDo\Logger::factory("front_choose_services")->addWarning("User {$user->getUsername()} - {$user->getFullName()} choose services not success .Could not map order's mapping service",$services);
                    throw new \InvalidArgumentException("Could not map order's mapping service");
                }else{
                    \SeuDo\Logger::factory("front_choose_services")->info("User {$user->getUsername()} - {$user->getFullName()} choose services success",$services);
                }

                $total_fee = \OrderService::getOrderServicesAmount($order);
                $order->setServiceFee($total_fee);

                $order->save();
                $order->saveDepositRatio();
                /*if($last_check == true) {
                    $order->updateInfo();
                }*/
                $newOrders[] = $order;
                if (isset($cartItems)) {
                    /* validate */
                    $valid_cart = $this->validCartItem($cartItems);
                    if ($valid_cart == false) {
                        $this->rollBack();
//                        \Flywheel\Log\Logger::getLevels()
                        throw new \InvalidArgumentException("Something went wrong with cart data");
                    }

                    foreach ($cartItems as $cart) {
                        if ($cart instanceof \CartItem) {
                            $order_item = \OrderItem::createFromCartItem($cart, $order);
                            if (!$order_item->getId() || !$order_item->isValid()) { //saving failed
                                $this->rollBack();
                                throw new \RuntimeException("Could not save order item. Context: " .$order_item->getValidationFailuresMessage("\n"));
                            }
                        }
                    }
                }
            }

            $this->commit();
            return $newOrders;

        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Create order's code
     * @param Users $user
     * @throws InvalidArgumentException
     * @internal param $userAddressId
     * @internal param $startPoint
     * @return bool|string
     */
    public static function createCode(\Users $user) {
        if ($user->isNew()) {
            throw new \InvalidArgumentException("Could not create order code with new user");
        }

        $user_code_part = $user->getCode();

        //remove shipping address province's code

        $current_order_no = self::read()
            ->count('id')
            ->where('`buyer_id` = :user_id')
            ->andWhere('DATE(`created_time`) = :today')
            ->setParameter(':user_id', $user->getId(), \PDO::PARAM_INT)
            ->setParameter(':today', date('Y-m-d'), \PDO::PARAM_STR)
            ->execute();

        $serial_part = str_pad($current_order_no + 1, 2, '0', STR_PAD_LEFT);
        $time_part = date('dm');

        $working_year_sequence = Common::getWorkingYearSequence();

        return "{$user_code_part}_{$working_year_sequence}{$time_part}{$serial_part}";
    }

    public function calcTotalAmount() {
        return $this->getRealAmount()+$this->getRealServiceAmount()+$this->getRealSurcharge();
    }

    /**
     * calculate order's total weight
     * @return float|int
     */
    public function calcWeight() {
        $items = $this->getItemInOrder();
        $weight = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $weight += floatval($item->getWeight());
                }
            }
        }
        return $weight;
    }

    /*
     * todo tính tổng sản phẩm order
     * */

    public function calcOrderQuantity() {
        $items = $this->getItemInOrder();
        $quantity = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $quantity += intval($item->getOrderQuantity());
                }
            }
        }
        return $quantity;
    }

    /*
     * todo tính tổng sán phẩm thực nhận
     * */
    public function calcReceiveQuantity() {
        $items = $this->getItemInOrder();
        $quantity = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $quantity += intval($item->getReciveQuantity());
                }
            }
        }
        return $quantity;
    }

    /*
     * todo tính tổng sán phẩm Sếu Đỏ đã mua
     * */
    public function calcPendingQuantity() {
        $items = $this->getItemInOrder();
        $quantity = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $quantity += intval($item->getPendingQuantity());
                }
            }
        }
        return $quantity;
    }


    /**
     * Tính giá trị đơn kia mua hàng xong
     * @return int
     */
    public function calcBoughtAmount(){
        $order_item = $this->getItemInOrder();

        $amount = 0;
        if (!empty($order_item)) {
            foreach ($order_item as $item) {
                if ($item instanceof \OrderItem) {
                    $amount += intval($item->getPendingQuantity()) * floatval($item->getPrice());
                }
            }
        }
        return $amount;
    }

    /*
     * todo tính tổng tiền order lúc bán đầu
     * */
    public function calcOrderAmount() {
        $items = $this->getItemInOrder();
        $amount = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $amount += intval($item->getOrderQuantity()) * floatval($item->getPrice());
                }
            }
        }
        return $amount;
    }
    /**
     * todo tính tổng tiền sản phẩm
     * @quyen Edit from ReciveQuantity to PendingQuantity
     */
    public function calcRealAmount()
    {
        $items = $this->getItemInOrder();
        $amount = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item instanceof \OrderItem) {
                    $amount += intval($item->getPendingQuantity()) * floatval($item->getPrice());
                }
            }
        }
        return $amount;
    }


    /**
     * Change Calc Refund - Edit by Quyen
     * @return number
     */
    public function calcRefund()
    {
        // Quyen Refund
        $amount = $this->getOrderAmount() - $this->getRealAmount();

//        $items = $this->getItemInOrder();
//        $amount = 0;
//        if (!empty($items)) {
//            foreach ($items as $item) {
//                if ($item instanceof \OrderItem) {
//                    $amount +=  ($item->getOrderQuantity() - $item->getReciveQuantity()) * $item->getPrice();
//                }
//            }
//        }
        return $amount;
    }

    /*
     * todo tính tổng dịch vụ trong đơn hàng
     * */
    public function calcFeeService()
    {
        $money = 0;
        $orderServices = \OrderPeer::getOrderServices($this->getId());

        if ($orderServices && !empty ($orderServices)) {
            foreach ($orderServices as $orderService) {
                if ($orderService instanceof \OrderService) {
                    $money += floatval($orderService->getMoney());
                }
            }
        }
        return $money;
    }

    public function getTotalAmount(){
        $total_amount = $this->calcTotalAmount();
        $this->setTotalAmount($total_amount);
        $this->save();

        return $total_amount;
    }

    /**
     *
     * @return bool
     * @throws Exception
     */
    public function updateInfoItemInOrder(){
        $items = $this->getItemInOrder();
        if(!empty($items)){
            $conn = \Flywheel\Db\Manager::getConnection();
            $conn->beginTransaction();
            try{
                $flag = false;
                foreach ($items as $item) {
                    if($item instanceof \OrderItem){
                        $price_cny = $item->getPriceCny();
                        $price_vnd = $price_cny * $this->getExchange();
                        $item->setPrice(Common::roundingMoney($price_vnd));
                        $result = $item->save();
                        if(!$result){
                            $flag = false;
                            break;
                        }else{
                            $flag = true;
                        }
                    }
                }
                if($flag){
                    $conn->commit();
                    return true;
                }else{
                    $conn->rollBack();
                    return false;
                }
            }catch (\Exception $e){
                $conn->rollBack();
                throw $e;
            }
        }
        return false;
    }


    /**
     * @TODO update lại thông tin giá và sản phẩm của đơn hàng
     * @return bool
     * @throws Flywheel\Exception
     * @throws InvalidArgumentException
     */
    public function updateInfo()
    {
        $this->beginTransaction();
        try {
            $this->updateInfoItemInOrder();

            $items = $this->getItemInOrder();//OrderPeer::getOrderItem($this->getId());

            if(empty($items)) {
                throw new InvalidArgumentException("Not item in order");
            }

            $this->setItemInOrder($items);

            if($this->getStatus() == Order::STATUS_INIT) {
                $orderAmount = $this->calcOrderAmount();
                $orderQuantity = $this->calcOrderQuantity();

                $this->setOrderAmount($orderAmount);
                $this->setRealAmount($orderAmount);

                $this->setOrderQuantity($orderQuantity);
                $this->setPendingQuantity($orderQuantity);
                $this->setReciveQuantity($orderQuantity);


            } else {
                $realAmount = $this->calcRealAmount();
                $refund = $this->calcRefund();
                $receiverQuantity = $this->calcReceiveQuantity();

                $pending_quantity = $this->calcPendingQuantity();

                $this->setRealAmount($realAmount);

                if($this->isBeforeStatus(\Order::STATUS_BOUGHT)){
                    $this->setPendingQuantity($pending_quantity);
                }

                if($this->isBeforeStatus(\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY) &&
                    $this->needToChecking() ){
                    $this->setReciveQuantity($receiverQuantity);
                }

                $this->setRefundAmount($refund);
            }

            $totalServiceFee =  \OrderService::getOrderServicesAmount($this);
            $this->setServiceFee($totalServiceFee);
            $this->setRealServiceAmount($this->getServiceFee()+$this->getDomesticShippingFeeVnd() + $this->getRealSurcharge());

            /* set total amount*/
            $totalRealAmount = $this->calcTotalAmount();
            $this->setTotalAmount($totalRealAmount);

            $result = $this->save();
            \SeuDo\Logger::factory('order')->info('save new info',array($this->getAttributes()));
            if($result == false) {
                \SeuDo\Logger::factory('order')->addError('not save new info',array($this->getValidationFailuresMessage()));
                $this->rollBack();
                throw new \Flywheel\Exception('not save new info');
            }
            $this->commit();
            return true;

        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('order')->addError('has error when try to update order_info',array($e->getMessage()));
            $this->rollBack();
            throw new \Flywheel\Exception('has error when try to update order_info');
        }

    }


    /**
     * todo Kiểm tra xem đã có thể giao hàng được cho khách hay chưa(sẵn sàng giao khi khách có yêu cầu giao)
     * @return bool
     */

    public function checkRequestDeliver() {
        //nếu đơn hàng đang ở trạng thái chờ gom
        if($this->getStatus() == self::STATUS_WAITING_FOR_DELIVERY) {
            return true;
        }
        return false;
    }

    /*
     * todo lấy ra số tiền còn thiếu
     * */
    public function requestDeliveryMoney () {

        /* Tổng tiền hàng đã mua */
        $real_amount = $this->getRealAmount();

        /* Tổng tiền phí dịch vụ */
        $service_amount = $this->getRealServiceAmount();


        /* Tổng thực trả */
        $real_payment_amount = $this->getRealPaymentAmount();

        /* Tổng phụ phí cộng thêm */
        $real_surcharge = $this->getRealSurcharge();

        /* Công thức :
        TỔng thanh toán cuối = tổng tiền hàng thực + tổng phí dịch vụ + tổng phụ phí - số tiền đã đặt cọc*/
        $amount = $real_amount + $service_amount + $real_surcharge  - $real_payment_amount;

        return $amount;
    }


    /**
     * Tạo địa chỉ nhận hàng theo dịch vụ
     * @return string
     */
    public function createAddressReceive(){
        \OrderService::findByOrderId($this->getId());
        $feature = "";
        if($this->mappingToService(\Services::TYPE_HIGH_VALUE)){
            $feature .= "V";
        }
        if($this->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
            $feature .= "E";
        }
        if($this->mappingToService(\Services::TYPE_WOOD_CRATING)){
            $feature .= "1";
        }
        if($this->mappingToService(\Services::TYPE_FRAGILE)){
            $feature .= "2";
        }

        if(!$this->mappingToService(\Services::TYPE_CHECKING)){
            $feature .= "-NC";
        }
        if($feature){
            $feature.= "-";
        }
        $feature .= $this->getId();

        $this->updateOrderRecipientName();

        return $feature;
    }

    /**
     *  Change Recipient Name - create by quyen
     */
    public function changeRecipientName(){
        $user = \Users::retrieveById($this->getBuyerId());
        $name_recipient = str_replace($user->getUsername(),$user->getCode(),$this->getNameRecipientOrigin());

        if(preg_match("/_V_/",$name_recipient)){
            $name_recipient = str_replace("V_","",$name_recipient);
        }

        if(preg_match("/_G_/",$name_recipient)){
            $name_recipient = str_replace("G_","",$name_recipient);
        }

        if(preg_match("/_K_/",$name_recipient)){
            $name_recipient = str_replace("K_","",$name_recipient);
        }
        if($name_recipient != ""){
            $this->setNameRecipientOrigin($name_recipient);
            $this->save();
        }
    }

    /**
     * Tao tên người nhận khi đã tồn tại đơn hàng, và dịch vụ
     * @throws RuntimeException
     */
    public function updateOrderRecipientName(){
        $address = UserAddress::retrieveById($this->getUserAddressId());
        if (!$address) {
            throw new \RuntimeException('Order have not had shipping address');
        }

        $warehouse_map = WarehouseMapping::retrieveByCityId($address->getProvinceId());
        $region = !empty($warehouse_map) && $warehouse_map->getWarehouse() == "SAIGON" ? "S" : "";

        $buyer = $this->getBuyer();
        if (!$buyer) {
            throw new \RuntimeException('Order have not had buyer info');
        }

        $recipient_name_origin = self::SEUDO_SERVICES."{$region}-{$buyer->getCode()}-". date('d');
        $this->setNameRecipientOrigin($recipient_name_origin);
        $this->changeRecipientName();
    }


    /**
     * create source site recipient
     */
    public function createRecipientName(){
        $address = UserAddress::retrieveById($this->getUserAddressId());
        if (!$address) {
            throw new \RuntimeException('Order have not had shipping address');
        }

        $warehouse_map = WarehouseMapping::retrieveByCityId($address->getProvinceId());
        $region = !empty($warehouse_map) && $warehouse_map->getWarehouse() == "SAIGON" ? "S" : "";

        $buyer = $this->getBuyer();
        if (!$buyer) {
            throw new \RuntimeException('Order have not had buyer info');
        }

        $recipient_name_origin = self::SEUDO_SERVICES."{$region}-{$buyer->getCode()}-". date('d');
        $this->setNameRecipientOrigin($recipient_name_origin);
        $this->save();
    }

    /*
     * todo check service is mapping with order
     * */

    public function mappingToService($code) {
        $exist = \OrderService::findOneByServiceCodeAndOrderId($code, $this->getId());
        if($exist) {
            return true;
        }
        return false;
    }
    /**
     * todo check xem đơn hàng đó có cần kiểm hay không
     * @return bool
     */
    public function needToChecking () {
        return $this->mappingToService(\Services::TYPE_CHECKING);
    }

    /**
     * todo check xem đơn hàng đó có cần đóng gỗ hay không
     * @return bool
     */
    public function needToWoodCrating() {
        return $this->mappingToService(\Services::TYPE_WOOD_CRATING);
    }

    /**
     * todo check xem đơn hàng đó có dễ vỡ hay không
     * @return bool
     */

    public function needToFragile() {
        return $this->mappingToService(\Services::TYPE_FRAGILE);
    }

    /**
     * todo lấy ra số lượng sản phẩm phụ kiện
     * @return int
     */
    public function getItemAssessQuantity() {
        $total = 0;
        $items = $this->getItemInOrder();

        $exchange_rate = \ExchangeRate::getExchange();

        if(empty($items)) $items = \OrderPeer::getOrderItem($this);

        if(!empty($items) ) {
            foreach ($items as $item) {
                if($item instanceof \OrderItem) {
                    if($item->getPrice() < $exchange_rate*10) $total+=$item->getPendingQuantity();
                }
            }
        }
        return $total;
    }




    /**
     * todo get normal total
     * @return int
     */
    public function getItemNormalQuantity() {
        $total = 0;
        $items = $this->getItemInOrder();

        $exchange_rate = \ExchangeRate::getExchange();

        if(empty($items)) $items = \OrderPeer::getOrderItem($this);

        if(!empty($items) ) {
            foreach ($items as $item) {
                if($item instanceof \OrderItem) {
                    if($item->getPrice() >= $exchange_rate*10) $total+=$item->getPendingQuantity();
                }
            }
        }
        return $total;
    }

    /**
     * @param $weight
     * @return $this
     * @throws Flywheel\Model\Exception
     * @throws \Exception
     */
    public function changeWeight($weight) {
        $this->beginTransaction();
        try {

            $this->setWeight($weight);
            $total_fee = \OrderService::getOrderServicesAmount($this);
            $this->setServiceFee($total_fee);
            $this->setTotalAmount($total_fee + $this->getRealAmount() + $this->getDomesticShippingFeeVnd()); // Loi do ko tinh lai total amount khi sua so luong
            $this->setNew(false);
            $result = $this->save();
            if ($result) {
                if($this->updateInfo()){
                    $this->commit();
                }else{
                    $this->rollBack();
                }
            } elseif (!$this->isValid()) {
                throw new \Flywheel\Model\Exception("Could not save order. Validation failures: " . $this->getValidationFailuresMessage("\n"));
            }
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }


    /**
     * Count Total Item
     * @param \Flywheel\Db\Query $query
     * @return PDOStatement
     */
    public function getCountItem(\Flywheel\Db\Query $query = null){
        if ($query == null) {
            $query = \OrderItem::read();
            $query->andWhere("order_id={$this->getId()}");
        }

        return $query->count('id')->execute();
    }

    /**
     * Get Total Item Pending Quantity
     * @param \Flywheel\Db\Query $query
     * @return int
     */
    public function getTotalPendingQuantity(\Flywheel\Db\Query $query = null){

        $result = 0;
        if ($query == null) {
            $query = \OrderItem::read();
            $query->select("SUM(pending_quantity) as result");
            $query->andWhere("order_id={$this->getId()}");
            $result = $query->execute()->fetch();
            if(isset($result["result"])){
                return $result["result"];
            }else{
                $result = 0;
            }
        }

        return intval($result);
    }


    public function getDestinationWarehouse() {
        if(trim($this->destination_warehouse) == '') {
            $user_address_id = $this->getUserAddressId();
            $user_address = \UserAddress::retrieveById($user_address_id);


            if(!$user_address) {
                return false;
            }
            $warehouse_code = \WarehouseMapping::mappingWareHouse($user_address->getProvinceId());

            if($warehouse_code == false){
                return false;
            }
            $this->setDestinationWarehouse($warehouse_code);
            $this->setNew(false);
            $this->save();

        }
        return $this->destination_warehouse;
    }

    public function addService($code, $money = null) {

        if($code == \Services::TYPE_EXPRESS_CHINA_VIETNAM){
//            && ($this->getCurrentWarehouse() == "CNGZ" && $this->getWarehouseStatus()!='IN')
            if(!$this->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)){
                throw new InvalidArgumentException("Đơn hàng đã quá hạn bỏ dịch vụ CPN");
            }
        }
        $new_service = new \OrderService();

        $new_service->beginTransaction();
        try {

            if(!\Services::isSupport($code)) {
                throw new \Flywheel\Exception($code.' service does not support !');
            }
            $service_info = \Services::retrieveByCode($code);
            if(!$service_info) {
                throw new \Flywheel\Exception($code.' service does not support !');
            }
            $new_service->setServiceId($service_info->getId());
            $new_service->setOrderId($this->getId());
            $new_service->setServiceCode($service_info->getCode());
            $new_service->setStatus('INIT');
            /**/

            if($money != null && intval($money) > 0) {
                $new_service->setMoney($money);
            }else{
                $new_service->setMoney(0);
            }
            $new_service->setCreatedTime(new DateTime());
            $result = $new_service->save();
            if($result == false) {
                $new_service->rollBack();
                throw new \Flywheel\Exception('Service does not save successfully !');
            }

            /* commit all */

            /* tạo xong service thì update lại tất cả order để còn tính các phí*/
            $result_update = $this->updateInfo();
            if($result_update){
                $new_service->commit();
                return true;
            }else{
                $new_service->rollBack();
                return false;
            }

        }catch (\Flywheel\Exception $e) {
            $new_service->rollBack();
            throw $e;
        }
    }

    /**
     * @param $code
     * @return bool
     * @throws Flywheel\Exception
     */
    public function removeService($code) {
//        if(!$this->isBeforeStatus(\Order::STATUS_RECEIVED_FROM_SELLER)&& ($this->getCurrentWarehouse() != "CNGZ" || ($this->getCurrentWarehouse() == "CNGZ" && $this->getWarehouseStatus()!='IN'))){
//        && ($this->getCurrentWarehouse() == "CNGZ" && $this->getWarehouseStatus()!='IN')
        if($code == \Services::TYPE_EXPRESS_CHINA_VIETNAM){
            if(!$this->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)){
                throw new InvalidArgumentException("Đơn hàng đã quá hạn bỏ dịch vụ CPN");
            }
        }
        $order_service = \OrderService::findOneByServiceCodeAndOrderId($code, $this->getId());
        if(!$order_service) {
            throw new \Flywheel\Exception($code.' is not mapping with '.$this->getCode().' before !');
        }
        if(!$order_service instanceof \OrderService ) {
            throw new \Flywheel\Exception($code.' Must be instance of \OrderService ');
        }
        $order_service->beginTransaction();
        $result =  $order_service->delete();
        if($result) {
            $result_update = $this->updateInfo();
            if($result_update){
                $order_service->commit();
                return true;
            }else{
                $order_service->rollBack();
                return false;
            }
        }
        return false;
    }


    public function getPackagesList(){
        return \Packages::findByOrderId($this->getOrderId());
    }

    /**
     * @param $order_id
     * @return array
     */
    public static function getListFeeOrder( $order_id ){
        $order = \Order::retrieveById( $order_id );
        if ( $order instanceof \Order ) {
            $order->updateInfo();
            $data = $order->toArray();
            $data[ "total_services_fee" ] = $order->getServiceFee() + $order->getDomesticShippingFeeVnd();
            $data[ "order_services" ] = \OrderService::buildOrderServicesArray( $order );
            $data[ "missing_amount" ] = $order->requestDeliveryMoney();
            $data[ "total_amount_cal" ] = $order->getTotalAmount();
            $data[ "is_wood_crating" ] = $order->mappingToService(\Services::TYPE_WOOD_CRATING);
            return $data;
        } else {
            return array();
        }
    }
}