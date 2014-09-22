<?php
namespace Background\Library;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use SeuDo\DataManipulator;
use SeuDo\Logger;

class OrderHelper {

    public static function buildData($orderId) {

        $orderCommentData = $orderItemData =  $customerData = $orderData = $addressData = array();

        $order = \Order::retrieveById($orderId);

        if($order && ($order instanceof \Order)) {

            /* order data */
            $orderData = DataManipulator::getOrderData($order);
            $orderData['service_code'] = 'SD';

            /* order comment */
            $orderComments = \OrderPeer::getOrderComment($order);
            if(!empty($orderComments)) {
                foreach ($orderComments as $orderComment) {
                    if($orderComment && ($orderComment instanceof \OrderComment)) {
                        array_push($orderCommentData, DataManipulator::getOrderCommentData($orderComment));
                    }
                }
            }

            /*order item*/
            $orderItems = \OrderPeer::getOrderItem($order);
            if(!empty($orderItems)) {
                foreach ($orderItems as $orderItem) {
                    if($orderItem && ($orderItem instanceof \OrderItem)) {
                        array_push($orderItemData, DataManipulator::getOrderItemData($orderItem));
                    }
                }
            }

            /* customer */
            $customer = \Users::retrieveById($order->getBuyerId());
            if($customer) {
                $customerData = DataManipulator::getCustomerData($customer);
            }

            /* shipping address */
            $address = \UserAddress::retrieveById($order->getUserAddressId());
            if($address) {
                $addressData = DataManipulator::getShippingAddress($address);
            }
        }

        return array(
            'order'=>$orderData,
            'order_item'=>$orderItemData,
            'order_comment'=>$orderCommentData,
            'customer'=>$customerData,
            'shipping_address'=>$addressData
        );
    }
}
