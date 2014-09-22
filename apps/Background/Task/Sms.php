<?php
namespace Background\Task;

use Flywheel\Redis\Client;
use SeuDo\SMS\CustomerSupport;

class Sms extends BackgroundBase {
    public function executeWaitDeliverNotification() {
        $last_send = Client::getConnection('system_config')->get(REDIS_SMS_WAIT_DELIVER_NOTIFICATION_LAST_SENT_TIME);
        $q = \Order::select()->where('`status` = :status')
            ->setParameter(':status', \Order::STATUS_WAITING_FOR_DELIVERY, \PDO::PARAM_STR)
            ->orderBy('buyer_id');

        if ($last_send) {
            $q->andWhere('`waiting_delivery_time` >= "' .$last_send .'"');
        }

        /** @var \Order[] $orders */
        $orders = $q->execute();
        $buyer_orders = array();
        //group order by buyers
        foreach($orders as $order) {
            $buyer_orders[$order->getBuyerId()][] = $order;
        }

        /** @var \Order[] $list */
        foreach ($buyer_orders as $buyer_id => $list) {
            $total_orders = sizeof($buyer_orders[$buyer_id]);
            $total_weight = 0;
            $warehouses = array();

            foreach ($list as $order) {
                $total_weight += $order->getWeight();
                $warehouses[$order->getDestinationWarehouse()] = true;
            }

            CustomerSupport::getInstance()->sendWaitDeliveryNotification($buyer_id, $total_orders, $total_weight, array_keys($warehouses));
        }

        $last_send = new \DateTime();
        Client::getConnection('system_config')->set(REDIS_SMS_WAIT_DELIVER_NOTIFICATION_LAST_SENT_TIME, $last_send->format('Y-m-d H:i:s'));
    }
} 