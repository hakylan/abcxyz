<?php 
/**
 * ServiceChinaDomestic
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServiceChinaDomesticBase.php';
class ServiceChinaDomestic extends \ServiceChinaDomesticBase {
    const STATUS_ACTIVE = 'ACTIVE';
    public static function getTotalFee($weight) {

        $result = self::findOneByStatus(self::STATUS_ACTIVE);

        $from = $to = 0;

        if($result && ($result instanceof \ServiceChinaDomestic) && ($weight && $weight>0) ) {
            $weight = ceil($weight);
            
            $fistFeeArray = @explode('-',$result->getFirstFee());
            $lastFeeArray = @explode('-',$result->getNextFee());
            if($weight > 1) {
                $from = ($fistFeeArray[0] + ($weight-1)*$lastFeeArray[0]);
                $to = ($fistFeeArray[1] + ($weight-1)*$lastFeeArray[1]);
            }else {
                $from = ($weight*$fistFeeArray[0]);
                $to = ($weight*$fistFeeArray[1]);
            }
        }
        return array(
            'from'=>GlobalHelper::rounding(ceil($from)),
            'to'=>GlobalHelper::rounding(ceil($to))
        );
    }
}