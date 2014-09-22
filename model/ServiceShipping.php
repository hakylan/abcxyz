<?php 
/**
 * ServiceShipping
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServiceShippingBase.php';
class ServiceShipping extends \ServiceShippingBase {
    
    const TYPE_IN_VIETNAM = 'IN_VIETNAM';

    const SUB_TYPE_HANOI = 'HANOI';
    const SUB_TYPE_SAIGON = 'SAIGON';

    const TYPE_CHINA_VIETNAM = 'CHINA_VIETNAM';
    const TYPE_EXPRESS_CHINA_VIETNAM = 'EXPRESS_CN_VN';





    public static function getInChinaFee($weight) {
        $fee = \ServiceChinaDomestic::getTotalFee($weight);

        //sd
        return $fee;

    }

    public static function getInVietnamFee($weight, $target = null) {
        $fee = 0;
        if(isset($weight) && $weight>0){
            $query = self::read();
            $query->andWhere('type="'.self::TYPE_IN_VIETNAM.'"');
            $query->andWhere($weight.'>=weight_from')->andWhere($weight.'<=weight_to');

            $query->andWhere('sub_type="'.$target.'"');

            $result = $query->execute()->fetch();

            if($result && isset($result['weight_fee'])) {
                $fee = $result['weight_fee'];
            }
        }
        return GlobalHelper::rounding(ceil($fee));
    }

    /**
     * @param $weight
     * @param $sub_type
     * @param string $deposit_time
     * @param $level_id
     * @return mixed
     */
    public static function getChinaVietnamFee($weight, $sub_type,$deposit_time, $level_id = 1) {
        $fee = 0;
        $fixed_fee = 0;

        $time_stamp = Common::getTimeStamp($deposit_time);

        if($time_stamp >= strtotime(\Services::TIME_FEE_OLD) || $time_stamp <= 0){
            $fixed_fee = \Services::getFixedFeeByCode(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
        }

        if(isset($weight) && $weight>0){
            $query = self::read();
            $query->andWhere('type="'.self::TYPE_CHINA_VIETNAM.'"');
            $query->andWhere($weight.'>=weight_from')->andWhere($weight.'<=weight_to');

            $query->andWhere('sub_type="'.$sub_type.'"');

            $result = $query->execute()->fetch();

            if($result && isset($result['weight_fee'])) {
                $fee = $result['weight_fee']*$weight;
            }
        }

        $fee = $fee + $fixed_fee;

        $result['fee_origin'] = Common::roundingMoney($fee);

        $discount_fee = \ServiceDiscount::getDiscountFee($level_id,\Services::TYPE_SHIPPING_CHINA_VIETNAM,$fee);

        $discount_fee = \ServiceDiscount::getDiscountFee($level_id,\ServiceDiscount::SERVICE_ORDER_FIXED,$discount_fee);

        $result['fee_discount'] = Common::roundingMoney($discount_fee);

        return $result;
    }

    /**
     * @param $weight
     * @param $sub_type
     * @param string $deposit_time
     * @param $level_id
     * @return mixed
     */
    public static function getExpressChinaVietnamFee($weight, $sub_type,$deposit_time = "",$level_id = 1) {
        $fee = 0;

        $fixed_fee = 0;

        $time_stamp = Common::getTimeStamp($deposit_time);

        if($time_stamp >= strtotime(\Services::TIME_FEE_OLD) || $time_stamp <= 0){
            $fixed_fee = \Services::getFixedFeeByCode(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
        }

        if(isset($weight) && $weight>0){

            $query = self::read();
            $query->andWhere('type="'.self::TYPE_EXPRESS_CHINA_VIETNAM.'"');
            $query->andWhere($weight.'>=weight_from')->andWhere($weight.'<=weight_to');

            $query->andWhere('sub_type="'.$sub_type.'"');

            $result = $query->execute()->fetch();

            $unit_fee = 0;

            if($result && isset($result['weight_fee'])) {
                $unit_fee = $result['weight_fee'];
            }

            $fee = $unit_fee * $weight;
        }

        $fee = $fixed_fee + $fee;

        $result['fee_origin'] = Common::roundingMoney($fee);

        $discount_fee = \ServiceDiscount::getDiscountFee($level_id,\Services::TYPE_SHIPPING_CHINA_VIETNAM,$fee);

        $discount_fee = \ServiceDiscount::getDiscountFee($level_id,\ServiceDiscount::SERVICE_ORDER_FIXED,$discount_fee);

        $result['fee_discount'] = Common::roundingMoney($discount_fee);

        return $result;
    }
}