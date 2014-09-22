<?php 
/**
 * ComplaintsFiles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ComplaintsFilesBase.php';
class ComplaintsFiles extends \ComplaintsFilesBase {
    public static function getAllFilesByComplaint($complaint_id, $delete = "NONE"){
        $query = \ComplaintsFiles::read();
        $query->andWhere("complaint_id = {$complaint_id} AND invalid = '{$delete}'");
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \ComplaintsFiles::getPhpName(), array(null, false));
    }
}