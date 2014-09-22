<?php
/**
 * OrderItem
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/OrderItemBase.php';
class OrderItem extends \OrderItemBase {

    public static $canChangeReceiveQuantity = array(
        \Order::STATUS_INIT,
        \Order::STATUS_DEPOSITED,
        \Order::STATUS_BUYING,
        \Order::STATUS_NEGOTIATING,
        \Order::STATUS_NEGOTIATED,
        \Order::STATUS_BOUGHT,
        \Order::STATUS_CHECKING,
        \Order::STATUS_CHECKED
    );

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'created_time',
                'modify_attr' => 'modified_time'
            )
        );
    }

    /**
     * Get item link
     * @return string
     */
    public function getItemLink(){
        $link = $this->getLink();
        if($link == ""){
            $link = Common::getItemLink($this->getSource(),$this->getItemId());
        }
        return $link;
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitleShow(){
        $title = $this->getTitle() != "" ? $this->getTitle() : "Sản phẩm mã {$this->getId()}";
        return $title;
    }

    public function getPriceCny(){
        return $this->getPricePromotion() > 0 ? $this->getPricePromotion() : $this->getPriceOrigin();
    }


    /**
     * create new order's item from cart item
     *
     * @param CartItem $cart
     * @param Order $order
     * @return OrderItem
     */
    public static function createFromCartItem(\CartItem $cart, \Order $order) {
        $item = new OrderItem();
        $item->setItemId($cart->getItemId());
        $item->setTitle(trim($cart->getTitle()));
        $item->setOrderId($order->getId());

        $item->setImage($cart->getImageShow());
        $item->setLink($cart->getLinkOrigin());
        $item->setProperty($cart->getProperties());
        $item->setPropertyTranslated($cart->getPropertiesTranslate());


        $item->setPrice(Common::roundingMoney($cart->getPriceVnd()));
        $item->setPriceOrigin($cart->getPriceCny());
        $item->setPricePromotion($cart->getPromotionPrice());
        $item->setPriceTable($cart->getPriceTable());
        $item->setRequireMin($cart->getRequireMin());
        $item->setStock($cart->getStock());
        $item->setWeight($cart->getWeight());
        $item->setSource($cart->getSite());
        $item->setOrderQuantity($cart->getAmount());
        $item->setPendingQuantity($cart->getAmount());
        $item->setReciveQuantity($cart->getAmount());
        $item->setOuterId($cart->getOuterId());
        $item->setTool($cart->getTool());
        $item->save();

        if($order->getAvatar() == ""){
            $order->setAvatar($cart->getImageShow());
        }

        return $item;
    }

    /**
     * get items by order id
     * @param int $order_id
     * @return OrderItem[]
     */
    public static function findByOrderId($order_id) {
        return self::select()->where('`order_id` = :order_id')
            ->setParameter(':order_id', $order_id, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Get Order
     * @return Order|false
     */
    public function getOrder(){
        return \Order::retrieveById($this->getOrderId());
    }

    /**
     * Update Pending Quantity To Order Item
     * @param $quantity
     * @throws Exception
     * @throws Flywheel\Exception
     * @return bool
     */
    public function updatePendingQuantity($quantity){
        $order = $this->getOrder();
        if(!$order->isBeforeStatus(\Order::STATUS_BOUGHT)){
            return false;
        }
        $this->beginTransaction();
        try{
            $this->setPendingQuantity($quantity);
            if ($this->save()) {
                if (empty($order) || !($order instanceof \Order)) {
                    $this->rollBack();
                    return false;
                }

                // Update order info
                if ($order->updateInfo()) {
                    $this->commit(); // Commit transaction
                    return true;
                }
            }
        }catch (\Flywheel\Exception $e){
            $this->rollBack();
            throw $e;
        }
        return false;
    }

    /**
     * Update Receive Quantity To Order Item
     * @param $quantity
     * @throws Exception
     * @throws Flywheel\Exception
     * @return bool
     */
    public function updateReceiveQuantity($quantity){
        $order = $this->getOrder();

        if (!$order->isBeforeStatus(\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY)) {
            return false;
        }

        $this->beginTransaction();
        try{
            $this->setReciveQuantity($quantity);
            if ($this->save()) {
                if (empty($order) || !($order instanceof \Order)) {
                    $this->rollBack();
                    return false;
                }

                // Update order info
                if ($order->updateInfo()) {
                    $this->commit(); // Commit transaction
                    return true;
                }
            }
        }catch (\Flywheel\Exception $e){
            $this->rollBack();
            throw $e;
        }
        return false;
    }


    /**
     * Update Order Item Quantity
     * @param $quantity
     * @throws Exception
     * @throws Flywheel\Exception
     * @return bool
     */
    public function updateOrderItemQuantity($quantity){
        $order = $this->getOrder();
        $this->beginTransaction();
        try{
            if($order->isBeforeStatus(\Order::STATUS_BOUGHT)){
                $this->setPendingQuantity($quantity);
            }

            if(in_array($order->getStatus(),self::$canChangeReceiveQuantity)){
                $this->setReciveQuantity($quantity);
            }
            if ($this->save()) {
                if (empty($order) || !($order instanceof \Order)) {
                    $this->rollBack();
                    return false;
                }

                // Update order info
                if ($order->updateInfo()) {
                    $this->commit(); // Commit transaction
                    return true;
                }
            }
        }
        catch (\Flywheel\Exception $e){
            $this->rollBack();
            return false;
        }
        return false;
    }

    /**
     * Update Price Order Item - create by Quyen
     * @param $price
     * @param $is_promotion
     * @return bool
     */
    public function updatePrice($price,$is_promotion=1){
        $this->beginTransaction();
        try{
            $order = $this->getOrder();
            $exchange_rate = $order->getExchange();
            if($is_promotion){
                $this->setPricePromotion($price);
            }else{
                if($this->getPricePromotion() != 0){
                    $this->setPricePromotion($price);
                }
                $this->setPriceOrigin($price);
            }
            $price_vnd = Common::roundingMoney($price*$exchange_rate);
            $this->setPrice($price_vnd);
            $this->setModifyTime(new DateTime());
            if ($this->save()) {
                if (empty($order) || !($order instanceof \Order)) {
                    $this->rollBack();
                    return false;
                }

                // Update order info
                if ($order->updateInfo()) {
                    $this->commit(); // Commit transaction
                    return true;
                }
            }
            $this->rollBack();
            return false;
        }catch (\Flywheel\Exception $e){
            $this->rollBack();
            return false;
        }
    }

    public static function deleteItemsByOrderId($order_id){
        return self::write()->delete('order_item')
            ->where("order_id = {$order_id}")
            ->execute();
    }
}