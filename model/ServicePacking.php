<?php 
/**
 * ServicePacking
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServicePackingBase.php';
class ServicePacking extends \ServicePackingBase {
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLE = 'DISABLE';


    public static function getCorrespond() {

        $query = \ServicePacking::read();
        $query->andWhere('status="'.self::STATUS_ACTIVE.'"');
        $result = $query->execute()->fetch();
        return $result;
    }

    public static function getTotalFee ($totalWeight = 0,$level_id = 1) {
        /* Phí đóng gói hiện tại được gộp trong phí vận chuyển TQ-VN */
        return 0;

        /*$result = self::getCorrespond();

        $fee = 0;
        if($result && isset($result['fee'])) {
            $fee = $result['fee'];
        }
        return GlobalHelper::rounding( $fee * $totalWeight, 500) ;*/
    }
}