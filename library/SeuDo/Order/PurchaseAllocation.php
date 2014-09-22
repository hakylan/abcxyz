<?php

namespace SeuDo\Order;


use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use SeuDo\Logger;

class PurchaseAllocation {
    /** @var \Users */
    public $teller;

    public $numberOrdersPerRequest;

    function __construct(\Users $teller, $numberOrdersPerRequest) {
        $this->numberOrdersPerRequest = $numberOrdersPerRequest;
        $this->teller = $teller;
    }

    protected function _calculateBuyerWeight(\Order $order) {
        return 1;
    }

    protected function _calculateSellerWeight(\Order $order) {
        return 1;
    }

    /**
     * allocating orders, return array
     * @return \Order[]
     */
    public function allocate() {
        $orders = \Order::findByStatus(\Order::STATUS_DEPOSITED);
        if (!$orders) {
            return array();
        }

        /**
         * @TODO
         * when staff finish dealing and change order status to "NEGOTIATED"
         * system will store this history. Each day, system will calculate allocate priority for staff
         * we will calculate seller weight and buyer weight and prioritize
         */
        if (sizeof($orders) < $this->numberOrdersPerRequest) {
            $rand = array_keys($orders);
        } else {
            $rand = array_rand($orders, $this->numberOrdersPerRequest);
        }
        $receiveOrders = array();

        for($i = 0; $i < sizeof($rand); ++$i) {
            $orders[$rand[$i]]->setTellersId($this->teller->getId());
            if ($orders[$rand[$i]]->changeStatus(\Order::STATUS_BUYING)) {
                $receiveOrders[] = $orders[$rand[$i]];
            } else {
                //logging
                Logger::factory('system')->error("Could not save order when assign purchasing staff");
            }
        }

        return $receiveOrders;
    }

    public static function purchaseOrderEventHandling(Event $event) {
        //write log to redis
    }
} 