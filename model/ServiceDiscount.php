<?php 
/**
 * ServiceDiscount
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServiceDiscountBase.php';
class ServiceDiscount extends \ServiceDiscountBase {
    const SERVICE_BUYING = 'BUYING';
    const SERVICE_CHECKING = 'CHECKING';
    const  SERVICE_SHIPPING_CHINA_VIETNAM = 'SHIPPING_CHINA_VIETNAM';
    const  SERVICE_EXPRESS_CHINA_VIETNAM = 'EXPRESS_CHINA_VIETNAM';
    const  SERVICE_ORDER_FIXED = 'ORDER_FIXED';
    const  SERVICE_DEPOSITED = 'DEPOSITED';
    const TYPE_PERCENT = 'PERCENT';
    const TYPE_FIX = 'FIX';


    /**
     * Hàm tính phí đã được triết khấu - Truyền vào phí chưa chiết khấu
     * @param $level_id
     * @param $services
     * @param $fee
     * @return mixed
     * @throws Flywheel\Exception
     */
    public static function getDiscountFee($level_id,$services, $fee)
    {
        $services_discount = self::findByLevelIdAndService($level_id,$services);

        if(!empty($services_discount) && isset($services_discount[0])){
            $services_discount = $services_discount[0];
            if($services_discount instanceof \ServiceDiscount){
                if($services_discount->getType() == self::TYPE_PERCENT){
                    $fee = $fee - ($fee/100 * $services_discount->getValue());
                }else if($services_discount->getType() == self::TYPE_FIX){
                    $fee = $fee - $services_discount->getValue();
                }
            }
        }

        return $fee;
    }

    /**
     * lấy phí cố định trên đơn hàng theo level id
     * @param $level_id
     * @return int|number
     */
    public static function getOrderFixed($level_id){
        $services_discount = self::findByLevelIdAndService($level_id,self::SERVICE_ORDER_FIXED);

        if(!empty($services_discount) && isset($services_discount[0])){
            $services_discount = $services_discount[0];
            if($services_discount instanceof \ServiceDiscount){
                return $services_discount->getValue();
            }
        }

        return 0;
    }

    /**
     * Lấy phần trăm đặt cọc
     * @param $level_id
     * @return int|number
     */
    public static function getDepositPercent($level_id){
        $services_discount = self::findByLevelIdAndService($level_id,self::SERVICE_DEPOSITED);

        if(!empty($services_discount) && isset($services_discount[0])){
            $services_discount = $services_discount[0];
            if($services_discount instanceof \ServiceDiscount){
                return $services_discount->getValue();
            }
        }

        return SystemConfig::retrieveByKey(SystemConfig::ORDER_DEPOSIT_PERCENT_REQUIRE)->config_value;
    }
}