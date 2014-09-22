<?php 
/**
 * ExchangeRate
 * @version		$Id$
 * @package		Model

 */

use Flywheel\Redis\Client as RedisClient;

require_once dirname(__FILE__) .'/Base/ExchangeRateBase.php';
class ExchangeRate extends \ExchangeRateBase {
    const ExchangeRateConfig = 3500;

    /**
     * Lưu tỉ giá vào redis theo dạng sorted_set
     * @param $exchange_rate
     */
    private static function zAddExchange($exchange_rate){
        $redis = RedisClient::getConnection('system_config');
        $redis->zAdd(REDIS_EXCHANGE_RATE,time(),$exchange_rate);
    }

    /**
     * Lấy tỉ giá từ redis
     * @return int
     */
    private static function zRangeExchange(){
        $redis = RedisClient::getConnection('system_config');
        $exchange_rate = $redis->zRange(REDIS_EXCHANGE_RATE,-1,-1);
        $exchange = 0;
        if(is_array($exchange_rate)){
            foreach ($exchange_rate as $ex) {
                $exchange = $ex;
                break;
            }

        }else if($exchange_rate != ''){
            $exchange = intval($exchange);
        }

        return $exchange;
    }

    /**
     * Add new exchange
     * @param $exchange_rate
     */
    public static function addNewExchangeRate($exchange_rate){
        self::zAddExchange($exchange_rate);
    }

    /**
     * get exchange rate
     * @return float|int
     */
    public static function getExchange(){
        $exchange = self::zRangeExchange();
        if($exchange == 0){
            $exchange_rate = ExchangeRate::retrieveByStatus(1);

            if($exchange_rate){

                $exchange = $exchange_rate->getExchangeRate() / $exchange_rate->getYuan();
            }else{
                $exchange = ExchangeRate::ExchangeRateConfig;
            }
            self::zAddExchange($exchange);
        }

        return $exchange;

    }
}