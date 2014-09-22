<?php 
/**
 * OrderItem
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/OrderItemBase.php';
class OrderItemPeer {
    /**
     * Remove order's items and its comment (Mongo)
     * @param Order $order
     * @return int number items was removed
     */
    public static function deleteByOrder(\Order $order) {
        /*
        The code below is my recommendation (LUU HIEU)
        $conn = \OrderItem::getWriteConnection();
        $conn->beginTransaction();

        //remove item's comment before

        return $conn->createQuery()
            ->delete('order_item', 'i')
            ->where('i.order_id = :order_id')
            ->setParameter(':order_id', $order->getId(), \PDO::PARAM_INT)
            ->execute();*/
    }
}