<?php 
/**
 * WarehouseMapping
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/WarehouseMappingBase.php';
class WarehouseMapping extends \WarehouseMappingBase {
    const WAREHOUSE_SAIGON = 'SAIGON';
    const WAREHOUSE_HANOI = 'HANOI';

    const WAREHOUSE_CODE_SAIGON = 'VNSG';
    const WAREHOUSE_CODE_HANOI = 'VNHN';

    public static function mappingWareHouse($provinceId) {
        $warehouse_map = WarehouseMapping::retrieveByCityId($provinceId);
        if($warehouse_map->getWarehouse() == 'SAIGON') {
            return self::WAREHOUSE_CODE_SAIGON;
        }else if($warehouse_map->getWarehouse() == 'HANOI') {
            return self::WAREHOUSE_CODE_HANOI;
        }
        return false;
    }
}