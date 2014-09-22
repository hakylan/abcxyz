<?php
use Flywheel\Db\Type\DateTime;
/**
 * OrderTracking
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/OrderTrackingBase.php';
class OrderTracking extends \OrderTrackingBase {
    public static function track(\Order $order, $userImpactId, $action = null, $description = null){

        $track = new \OrderTracking();

        $track->setOrderId($order->getId());
        $track->setStatus($order->getStatus());

        $track->setAction($action);
        $track->setDescription($description);
        $track->setCreatedTime(new DateTime('now'));
        $track->setUserId($userImpactId);
        $track->setNew(true);
        $track->save();
        return ($track->getId() > 0);
    }
}