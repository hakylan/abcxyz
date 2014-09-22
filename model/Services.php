<?php 
/**
 * Services
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ServicesBase.php';
class Services extends \ServicesBase {
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INIT = 'INIT';

    const TYPE_BUYING = 'BUYING';
    const TYPE_CHECKING = 'CHECKING';
    const TYPE_PACKING = 'PACKING';
    const TYPE_SHIPPING = 'SHIPPING';

    const TYPE_SHIPPING_CHINA_VIETNAM = 'SHIPPING_CHINA_VIETNAM';
    const TYPE_EXPRESS_CHINA_VIETNAM = 'EXPRESS_CHINA_VIETNAM';
    const TYPE_HIGH_VALUE = 'HIGH_VALUE';

    const TYPE_FRAGILE = 'FRAGILE';
    const TYPE_WOOD_CRATING = 'WOOD_CRATING';

    const TIME_FEE_OLD = "2014-06-13 00:00:00";
    const TIME_FEE_CPN = "2014-06-13 00:00:00";

    static $serviceSupport = array(
        self::TYPE_BUYING,
        self::TYPE_CHECKING,
        self::TYPE_PACKING,
        self::TYPE_SHIPPING,
        self::TYPE_SHIPPING_CHINA_VIETNAM,
        self::TYPE_FRAGILE,
        self::TYPE_WOOD_CRATING,
        self::TYPE_EXPRESS_CHINA_VIETNAM,
        self::TYPE_HIGH_VALUE
    );


    public function getCodeTitle(){
        return $this->getDescription();
    }

    /**
     * todo check one service is support or not
     * @param $service
     * @return bool
     */
    public static function isSupport($service) {
        return in_array($service, self::$serviceSupport);
    }

    /**
     * todo get list service available in db
     * @return Services[]
     */
    public static function getListServices () {
        return self::findByStatus(self::STATUS_ACTIVE);
    }

    /**
     * @param $code
     * @return int|number
     */
    public static function getFixedFeeByCode($code){
        $services = self::retrieveByCode($code);
        $fixed_fee = 0;
        if($services instanceof \Services){
            $fixed_fee = $services->getFixedFee();
        }
        return $fixed_fee;
    }

}