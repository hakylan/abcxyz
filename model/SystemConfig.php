<?php
use Flywheel\Redis\Client as RedisClient;

/**
 * SystemConfig
 * @version		$Id$
 * @package		Model
 */

require_once dirname(__FILE__) .'/Base/SystemConfigBase.php';
class SystemConfig extends \SystemConfigBase {

    /* Thông tin seudo */
    const HOTLINE = '04 629 335 36';
    const EMAIL_CSKH = 'cskh@seudo.vn';
    const ACCOUNT_TECHCOMBANK = '<span class="normal-blod"> 1402 1327 7170 30 </span> <span class="uppercase"> Nguyễn Văn Giang</span> - Chi nhánh Trương Định';
    const ACCOUNT_VIETCOMBANK = '<span class="normal-blod">0691 0003 06265</span> <span class="uppercase">Nguyễn Văn Giang</span> - Chi nhánh Thanh Xuân';
    const ACCOUNT_VIETINBANK = '<span class="normal-blod">1010 1000 6834 804</span> <span class="uppercase">Nguyễn Văn Giang</span> - Chi nhánh Nam Thăng Long <span class="italic"> Từ 05/07/2014</span>';
    /*****************************/

    const SD_ACCOUNTANT_UK = 'sd_accountant_uk';
    const ACCESS_MONEY_LIMIT = 'accessories_money_limit';
    const ORDER_DEPOSIT_PERCENT_REQUIRE = 'order_deposit_percent_require';
    const ORDER_MAXIMUM_RECEIVED_ONCE = 'ORDER_MAXIMUM_RECEIVED_ONCE'; // Số đơn tối đa nhận một lần
    const ORDER_MAXIMUM_RECEIVED_A_DAY = 'ORDER_MAXIMUM_RECEIVED_A_DAY'; // Số đơn tối đa nhận một ngày
    const ORDER_MAXIMUM_TIME_TO_PAYMENT = 'ORDER_MAXIMUM_TIME_TO_PAYMENT'; // Thời gian tối đa phải thanh toán (giờ)
    const ORDER_MAXIMUM_TIME_TO_PLACE = 'ORDER_MAXIMUM_TIME_TO_PLACE'; // Thời gian tối đa phải đặt hàng (giờ)
    const ORDER_MAXIMUM_TIME_PROCESS = 'ORDER_MAXIMUM_TIME_PROCESS'; // Thời gian tối đa phải xử lý đơn hàng (giờ)
    const MAX_USER_MOBILES_NO = 'max_user_mobile_no'; //Maximum number of mobiles user can used
    const MAX_PURCHASE_ORDERS_ALLOCATED = 'maximum_purchase_orders_allocated'; //Số lượng tối đa nhân viên mua hàng cùng một lúc đảm nhận.
    const ORDER_EXPIRE_DATE = 'ORDER_EXPIRE_DATE'; //Thời gian hết hạn của một đơn hàng khi tạo đơn mà ko đặt cọc (Tính theo ngày).

    const SITE_ROOT_LOGISTIC = 'http://logistic.seudo.vn/';

    public static function getAddressSendEmailDailyReport(){
        return array('hosivan@alimama.vn',
//                        'luutronghieu@alimama.vn',
            'luongthithanhtam@alimama.vn',
            'tranthihuong@alimama.vn',
            'dinhthithanhhai@alimama.vn',
            'buithicuc@alimama.vn ',

            'nguyenvangiang@alimama.vn',
            'chuminhquyen@alimama.vn',
            'phamthidiuhien@alimama.vn');
//        return array('hosivan@alimama.vn');
    }

    protected function _beforeSave() {
        $this->setConfigKey(mb_strtoupper($this->getConfigKey()));
    }

    protected function _afterSave() {
        return self::_pushToRedis($this);
    }

    protected function _afterDelete() {
        //remove from redis
        $redis = RedisClient::getConnection('system_config');
        $redis->del(REDIS_SYSCONFIG .$this->getConfigKey());
    }

    /**
     *
     * @param string $key
     * @return false|null|SystemConfig
     */
    public static function retrieveByConfigKey($key) {
        return self::retrieveByKey($key);
    }

    /**
     * @param string $key
     * @return null|SystemConfig|false
     */
    public static function retrieveByKey($key) {
        if (!$key) {
            return false;
        }

        if (null != ($obj = self::getInstanceFromPool($key))) {
            return $obj;
        }

        //retrieve from redis
        if (($data = self::retrieveFromRedis($key))) {
            $obj = new self($data, false);
            return $obj;
        }

        $obj = parent::findOneByConfigKey($key);
        if ($obj) {
            self::addInstanceToPool($obj, $key);
            self::_pushToRedis($obj);
        }

        return $obj;
    }

    /**
     * @param $cfg_key
     * @return array
     */
    public static function retrieveFromRedis($cfg_key) {
        $redis = RedisClient::getConnection('system_config');
        return $redis->hGetAll(REDIS_SYSCONFIG .$cfg_key);
    }

    /**
     * Update Config - Truyền vào new value  - Quyền
     * @param $cfg_key
     * @param $cfg_value
     * @return bool
     */
    public static function updateConfig($cfg_key,$cfg_value){
        $config = self::findOneByConfigKey($cfg_key);
        if(!empty($config) && $config instanceof SystemConfig){

            $config->setConfigValue($cfg_value);

//            $config

            $config->save();

            return self::_pushToRedis($config);
        }

        return false;

    }

    /**
     * @param \SystemConfig $obj
     * @return bool
     */
    private static function _pushToRedis($obj) {
        $redis = RedisClient::getConnection('system_config');
        return $redis->hMset(REDIS_SYSCONFIG .$obj->getConfigKey(), $obj->toArray());
    }

    public function __toString() {
        return $this->getConfigValue();
    }
}