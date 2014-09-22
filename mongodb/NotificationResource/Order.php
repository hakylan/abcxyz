<?php
namespace mongodb\NotificationResource;

class Order extends Notification {
    protected $_order_status;
    protected $_type_confirm;
    protected $_type_chat;
    protected $_message_content;
    protected $_sender_id;
    protected $_is_express_cn_vn;
    public function saveOrderStatus($type,$array_data) {

        if ( is_array( $array_data ) && count( $array_data ) > 0 ) {
            $this->_order_status = isset($array_data['order_status'])? $array_data['order_status']: '';
            $this->_user_id = isset($array_data['user_id'])? intval($array_data['user_id']): 0;
            $this->_user_name = isset($array_data['user_name'])? $array_data['user_name'] : '';
            $this->_type = $type;
            $this->_order_id = isset($array_data['order_id'])? intval($array_data['order_id']): 0;
            $this->_order_name = isset($array_data['order_name'])? $array_data['order_name'] : '';
            $this->_created_time = new \MongoDate(intval($array_data['time']));
            $this->_is_new = 1;
            $this->_priority = isset($array_data['priority'])? floatval($array_data['priority']): 1;
            $this->_is_express_cn_vn = isset($array_data['is_express_cn_vn'])? intval($array_data['is_express_cn_vn']): 0;
            $this->_attributes['user_id']= $this->_user_id;
            $this->_attributes['user_name']= $this->_user_name;
            $this->_attributes['type']= $this->_type;
            $this->_attributes['order_id']= $this->_order_id;
            $this->_attributes['order_name']= $this->_order_name;
            $this->_attributes['created_time']= $this->_created_time;
            $this->_attributes['is_new']= $this->_is_new;
            $this->_attributes['priority']= $this->_priority;
            $this->_attributes['is_express_cn_vn']= $this->_is_express_cn_vn;
            $this->_attributes['order_status']= $this->_order_status;
            $result = $this->saveNotification();
            return $result;
        }else{
            return false;
        }

    }

    public function saveConfirmOrder($type,$array_data) {
        if ( is_array( $array_data ) && count( $array_data ) > 0 ) {
            $this->_type_confirm = isset($array_data['type_confirm'])? $array_data['type_confirm'] : '';
            $this->_user_id = isset($array_data['user_id'])? intval($array_data['user_id']): 0;
            $this->_user_name = isset($array_data['user_name'])? $array_data['user_name'] : '';
            $this->_type = $type;
            $this->_order_id = isset($array_data['order_id'])? intval($array_data['order_id']): 0;
            $this->_order_name = isset($array_data['order_name'])? $array_data['order_name'] : '';
            $this->_created_time = new \MongoDate(intval($array_data['time']));
            $this->_is_new = 1;
            $this->_priority = isset($array_data['priority'])? floatval($array_data['priority']): 1;
            $this->_attributes['user_id']= $this->_user_id;
            $this->_attributes['user_name']= $this->_user_name;
            $this->_attributes['type']= $this->_type;
            $this->_attributes['order_id']= $this->_order_id;
            $this->_attributes['order_name']= $this->_order_name;
            $this->_attributes['created_time']= $this->_created_time;
            $this->_attributes['is_new']= $this->_is_new;
            $this->_attributes['priority']= $this->_priority;
            $this->_attributes['type_confirm']= $this->_type_confirm;
            $result = $this->saveNotification();
            return $result;
        }else{
            return false;
        }

    }

    public function saveChatOrder($type,$array_data) {
        if ( is_array( $array_data ) && count( $array_data ) > 0 ) {
            $this->_type_chat = isset($array_data['type_chat'])? $array_data['type_chat'] : '';
            $this->_message_content = isset($array_data['message_content'])? $array_data['message_content'] : '';
            $this->_sender_id = isset($array_data['sender_id'])? intval($array_data['sender_id']): 0;
            $this->_user_id = isset($array_data['user_id'])? intval($array_data['user_id']): 0;
            $this->_user_name = isset($array_data['user_name'])? $array_data['user_name'] : '';
            $this->_type = $type;
            $this->_order_id = isset($array_data['order_id'])? intval($array_data['order_id']): 0;
            $this->_order_name = isset($array_data['order_name'])? $array_data['order_name'] : '';
            $this->_created_time = new \MongoDate(intval($array_data['time']));
            $this->_is_new = 1;
            $this->_priority = isset($array_data['priority'])? floatval($array_data['priority']): 1;
            $this->_attributes['user_id']= $this->_user_id;
            $this->_attributes['user_name']= $this->_user_name;
            $this->_attributes['type']= $this->_type;
            $this->_attributes['order_id']= $this->_order_id;
            $this->_attributes['order_name']= $this->_order_name;
            $this->_attributes['created_time']= $this->_created_time;
            $this->_attributes['is_new']= $this->_is_new;
            $this->_attributes['priority']= $this->_priority;
            $this->_attributes['type_chat']= $this->_type_chat;
            $this->_attributes['message_content']= $this->_message_content;
            $this->_attributes['sender_id']= $this->_sender_id;
            $result = $this->saveNotification();
            return $result;
        }else{
            return false;
        }

    }
} 