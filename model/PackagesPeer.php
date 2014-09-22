<?php
use Flywheel\Redis\Client as RedisClient;

/**
 * Packages
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/PackagesBase.php';
class PackagesPeer {

    /**
     * Yêu cầu giao hàng với kiện
     * @param $package
     * @throws Exception
     */
    public static function requestDeliveryPackage($package){
        try{
            if(is_string($package) || is_numeric($package)){
                $package = \Packages::retrieveById($package);
            }

            if($package instanceof \Packages){
                $order = $package->getOrder();

                if($order instanceof \Order){
                    // Check is package first
                    if(1 == 1){
                        \OrderPeer::requestDeliveryOrder($order,$package);
                    }else{
                        \OrderPeer::requestDeliveryOrder($order,$package,false);
                    }
                }else{
                    throw new \Flywheel\Exception("Not found Order with Package {$package->getId()}");
                }
            }
        }catch (\Exception $e){
            throw $e;
        }

    }


}