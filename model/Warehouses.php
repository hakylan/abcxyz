<?php 
/**
 * Warehouses
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/WarehousesBase.php';
class Warehouses extends \WarehousesBase {
    /**
     * Get all warehouse retrieve from static pool
     * @return Warehouses[]
     */
    public static function getAllWarehouses() {
        static $warehouse;
        if (null == $warehouse) {
            $warehouse = self::select()
                ->orderBy('ordering')
                ->execute();
        }

        return $warehouse;
    }
}