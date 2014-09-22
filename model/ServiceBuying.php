<?php 
/**
 * ServiceBuying
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServiceBuyingBase.php';
class ServiceBuying extends \ServiceBuyingBase {
    const STATUS_ACTIVE = 'ACTIVE';

    public static function getCorrespond($amount) {
        $query = \ServiceBuying::read();
        $query->andWhere('status="'.self::STATUS_ACTIVE.'"');
        $query->andWhere($amount.'>=begin')->andWhere($amount.'<=end');

        $result  = $query->execute()->fetch();
        return $result;
    }

    public static function getFixedFee() {
        $result = \Services::findOneByCode(\Services::TYPE_BUYING);
        return $result;
    }
    public static function getTotalFee($amount, $level_id = 1) {
        $fee = 0;
        $result = self::getCorrespond($amount);
        if($result && isset($result['fee_percent'])) {
            $feeFollowPercent = ($amount/100 * $result['fee_percent']);
            if(isset($result['fee']) && $feeFollowPercent < $result['fee']) {
                $feeFollowPercent = $result['fee'];
            }
            $fee += $feeFollowPercent;
        }

        $service = self::getFixedFee();

        if($service && ($service instanceof \Services)) {
            $fee+=$service->getFixedFee();
        }
        /* nếu phí mua hàng nhỏ hơn phí tối thiểu => lấy phí tối thiểu */
        if($fee < $result['min_fee']) $fee = $result['min_fee'];

        $result = array();
        $result['fee_origin'] = Common::roundingMoney($fee);// GlobalHelper::rounding($fee,500);

        $fee = \ServiceDiscount::getDiscountFee($level_id,\Services::TYPE_BUYING,$fee);

        $result['fee_discount'] = Common::roundingMoney($fee);// GlobalHelper::rounding($fee,500);

        return $result;
    }

}