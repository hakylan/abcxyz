<?php 
/**
 * OrderRequestDelivery
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/OrderRequestDeliveryBase.php';
class OrderRequestDelivery extends \OrderRequestDeliveryBase {
    public static function getOrderRequestDelivery(\Flywheel\Db\Query $query = null){
        if($query == null){
            $query = self::read();
        }
        return $query->execute()->fetchAll(PDO::FETCH_CLASS,self::getPhpName(),array(null,false));
    }
}