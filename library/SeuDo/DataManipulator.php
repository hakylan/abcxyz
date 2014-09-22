<?php
namespace SeuDo;


class DataManipulator {

    public static function getOrderData(\Order $order) {
        return $order->getAttributes(array(
            'code',
            'pending_quantity',
            'seller_name',
            'seller_aliwang',
            'seller_homeland',
            'seller_info',
            'invoice',
            'freight_bill',
            'weight',
            'created_time'
        ));
    }

    public static function getOrderCommentData(\OrderComment $orderComment) {
        return $orderComment->getAttributes(array(
            'order_id',
            'content',
            'type',
            'created_by',
            'created_time'
        ));
    }

    /**
     * @param \OrderItem $orderItem
     * @return array
     */
    public static function getOrderItemData(\OrderItem $orderItem) {
        return $orderItem->getAttributes(array(
            'order_id',
            'title',
            'link',
            'image',
            'property',
            'property_translated',
            'weight',
            'pending_quantity',
            'created_time'
        ));
    }
    public static function getCustomerData(\Users $customer) {
        return $customer->getAttributes(array(
            'code',
            'username',
            'fullname',
        ));
    }
    public static function getShippingAddress(\UserAddress $userAddress) {
        return $userAddress->getAttributes(array(
            'user_id',
            'district_id',
            'province_id',
            'detail',
            'note',
            'receiver_name',
            'receiver_phone'
        ));
    }
}