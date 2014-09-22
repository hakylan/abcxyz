<?php 
/**
 * ServiceChecking
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServiceCheckingBase.php';
class ServiceChecking extends \ServiceCheckingBase {
    const TYPE_NORMAL_ITEM = 'normal_item';
    const TYPE_ACCESS_ITEM = 'accessory_item';

    const STATUS_ACTIVE = 'ACTIVE';

    public static function getCorrespond($totalItem) {

        $query = \ServiceChecking::read();
        $query->andWhere('status="'.self::STATUS_ACTIVE.'"');
        $query->andWhere($totalItem.'>=begin')->andWhere($totalItem.'<=end');
        $result  = $query->execute()->fetch();

        return $result;
    }

    public static function getFixedFee() {
        $result = \Services::findOneByCode(\Services::TYPE_CHECKING);
        return $result;
    }
    public static function getTotalFee($totalItem, $normal_item , $access_item,$deposit_time = "", $level_id = 1) {
        if($deposit_time == ""){
            $deposit_time = date("Y-m-d H:i:s");
        }
        $fee = 0;
        $result = self::getCorrespond($totalItem);

        if($result && !empty($result)) {
            if(isset($result[self::TYPE_NORMAL_ITEM]) && isset($result[self::TYPE_ACCESS_ITEM])){

                $fee+=$result[self::TYPE_NORMAL_ITEM]*$normal_item;
                $fee+=$result[self::TYPE_ACCESS_ITEM]*$access_item;
            }
        }

        $checking_fixed_fee = 0;

        $time_stamp = Common::getTimeStamp($deposit_time);

        if($time_stamp < strtotime(\Services::TIME_FEE_OLD) && $time_stamp > 0){
            $checking_fixed_fee = \Services::getFixedFeeByCode(\Services::TYPE_CHECKING);
        }

        $fee = $fee + $checking_fixed_fee;
        $result['fee_origin'] = Common::roundingMoney($fee);

        $discount_fee = \ServiceDiscount::getDiscountFee($level_id,\Services::TYPE_CHECKING,$fee);

        $result['fee_discount'] = Common::roundingMoney($discount_fee);

        return $result;
    }
}