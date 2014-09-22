<?php 
/**
 * ComplaintsReasons
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ComplaintsReasonsBase.php';
class ComplaintsReasons extends \ComplaintsReasonsBase {

    public static function getAllReasonsByComplaint($complaint_id){
        $query = \ComplaintsReasons::read();
        $query->andWhere("complaint_id = {$complaint_id}");
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \Order::getPhpName(), array(null, false));
    }
}