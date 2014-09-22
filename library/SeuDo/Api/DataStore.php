<?php
namespace SeuDo\Api;


use FlyApi\Exception;
use \Flywheel\Redis\Client as RedisClient;

class DataStore extends \FlyApi\DataStore {
    /**
     * @param $consumer_key
     * @return Consumer
     * @throws \FlyApi\Exception
     */
    public function lookupConsumer($consumer_key) {
        $storage = \Consumer::retrieveByConsumerKey($consumer_key);
        if (!$storage) {
            throw new Exception('Consumer not found!');
        }

        $consumer = new Consumer($storage->getConsumerKey(), $storage->getConsumerSecret());
        return $consumer;
    }

    /**
     * @param \FlyApi\Consumer $consumer
     * @param $nonce
     * @param $timestamp
     * @return bool
     */
    public function lookupNonce($consumer, $nonce, $timestamp) {
        $redis = RedisClient::getConnection('consumer');
        $key = REDIS_NONCE .$consumer->key .'_' .$nonce;
        if ($redis->get($key)) {
            return true;
        }

        $redis->setex($key, 300, 1);
        return false;
    }
} 