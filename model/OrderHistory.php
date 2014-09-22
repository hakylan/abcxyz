<?php 
/**
 * OrderHistory
 * @version		$Id$
 * @package		Model

 */
use Flywheel\Db\Query;

require_once dirname(__FILE__) .'/Base/OrderHistoryBase.php';
class OrderHistory extends \OrderHistoryBase {


    /**
     * Get list order history
     * @param Query $query
     * @return array
     */
    public static function getOrderHistory(Query $query = null){
        if($query == null){
            $query = OrderHistory::read();
        }

        return $query->execute()->fetchAll(\PDO::FETCH_CLASS,OrderHistory::getPhpName(),array(null,false));

    }
}