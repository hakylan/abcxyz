<?php
use \Flywheel\Redis\Client as RedisClient;
/**
 * Consumer
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ConsumerBase.php';
class Consumer extends \ConsumerBase {
    protected function _afterSave() {
        return self::_pushToRedis($this);
    }

    protected function _afterDelete() {
        //remove from redis
        $redis = RedisClient::getConnection('consumer');
        $redis->del(REDIS_CONSUMER .$this->getConsumerKey());
    }

    public static function retrieveByConsumerKey($key) {
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

        $obj = self::findOneByConsumerKey($key);
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
        $redis = RedisClient::getConnection('consumer');
        return $redis->hGetAll(REDIS_CONSUMER .$cfg_key);
    }

    /**
     * @param \Consumer $obj
     * @return bool
     */
    private static function _pushToRedis($obj) {
        $redis = RedisClient::getConnection('consumer');
        return $redis->hMset(REDIS_CONSUMER .$obj->getConsumerKey(), $obj->toArray());
    }
}