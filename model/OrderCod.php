<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/9/14
 * Time: 12:57 AM
 */
use Flywheel\Redis\Client;
class OrderCod{
    const CONFIG_REDIS = "order_cod";

    /**
     * zAdd cod to redis with key is address id
     * @param UserAddress $address
     * @param $cod
     * @return bool
     * @throws Exception
     */
    private static function hSetOrderCod(\UserAddress $address,$cod){
        try{
            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::CONFIG_REDIS);
                $key = REDIS_ORDER_COD."{$address->getUserId()}";
                $redis->hSet($key,$address->getId(),$cod);
                $redis->expire($key,60*60*24*30);
                return true;
            }else{
                throw new InvalidArgumentException("Variable address not instanceof UserAddress");
            }

        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * hGet Order cod from redis
     * @param UserAddress $address
     * @return string
     * @throws Exception
     */
    private static function hGetOrderCod(\UserAddress $address){
        try{
            if($address instanceof \UserAddress){
                $redis = Client::getConnection(self::CONFIG_REDIS);
                $key = REDIS_ORDER_COD."{$address->getUserId()}";
                $cod = $redis->hGet($key,$address->getId());
                $cod = floatval($cod) > 0 ? $cod : 0;
                return $cod;
            }else{
                throw new InvalidArgumentException("Variable address not instanceof UserAddress");
            }

        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * Set cod cho 1 địa chỉ
     * @param $address
     * @param $cod
     * @return bool
     * @throws Exception
     */
    public static function setCod($address,$cod){
        try{
            if(floatval($cod) < 0){
                throw new InvalidArgumentException("Cod không hợp lệ, xin thử lại");
            }
            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }
            $result = false;

            if($address instanceof \UserAddress){
                $result =  self::hSetOrderCod($address,$cod);
            }
            return $result;

        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * Update Cod cho 1 địa chỉ = cod cũ + cod mới
     * @param $address
     * @param $cod
     * @return bool
     * @throws Exception
     */
    public static function updateCod($address,$cod){
        try{
            if(floatval($cod) <= 0){
                return false;
            }
            if(is_numeric($address)){
                $address = \UserAddress::retrieveById($address);
            }
            if($address instanceof \UserAddress){
                $cod_redis = self::hGetOrderCod($address);
                if(floatval($cod_redis) > 0){
                    $cod += $cod_redis;
                }
                self::hSetOrderCod($address,$cod);
            }

        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * get Cod from redis
     * @param $address
     * @return int|string
     */
    public static function getCod($address){
        $cod = 0;
        if(is_numeric($address)){
            $address = \UserAddress::retrieveById($address);
        }
        if($address instanceof \UserAddress){
            $cod = self::hGetOrderCod($address);
        }
        return floatval($cod);
    }

    /**
     * del cod from redis when delivery order
     * @param $address
     * @return bool|int
     */
    public static function delCod($address){
        if(is_numeric($address)){
            $address = \UserAddress::retrieveById($address);
        }
        $result = false;
        if($address instanceof \UserAddress){
            $redis = Client::getConnection(self::CONFIG_REDIS);
            $key = REDIS_ORDER_COD."{$address->getUserId()}";
            $result = $redis->hDel($key,$address->getId());
        }
        return $result;
    }
}