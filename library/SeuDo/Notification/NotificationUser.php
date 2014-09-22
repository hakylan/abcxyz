<?php
namespace SeuDo\Notification;

use MongoQB\Exception;
use SeuDo\MongoDB;
use mongodb\NotificationResource\Notification;
use mongodb\NotificationResource\Chat;
use mongodb\NotificationResource\MoneyAccount;
use mongodb\NotificationResource\Order;
use Flywheel\Config\ConfigHandler;
/**
 * Class Notification
 * @package mongodb
 */
class NotificationUser
{
    const
        TYPE_NOTIFY_ORDER_STATUS = 'ORDER_STATUS',
        TYPE_NOTIFY_MONEY_ACCOUNT = 'MONEY_ACCOUNT',
        TYPE_NOTIFY_CONFIRM_ORDER = 'CONFIRM_ORDER',
        TYPE_NOTIFY_CHAT_ORDER = 'CHAT_ORDER',
        PRIORITY_CONFIRM_ORDER = 2,
        PRIORITY_NORMAL = 1;

    protected static $_transporters = array();
    protected $_enable;
    protected $_bandname;

    public function __construct()
    {
        ConfigHandler::import('root.config');
        $config = ConfigHandler::get( 'notification' );
        $this->_enable = (bool) $config[ 'enable' ];
        $this->_bandname = $config['brandname'];
        if ( !$this->_bandname ) {
//            throw new \RuntimeException( 'Missing config "brandname"' );
        }
        if ( $this->_enable && empty( $transporter ) ) {
            foreach ( $config[ 'transporters' ] as $transporter => $params ) {
                self::addTransporter( $transporter, $params );
            }
        }

    }

    public function getBrandname()
    {
        return $this->_bandname;
    }

    public static function addTransporter( $transporter, $params )
    {
        if ( is_string( $transporter ) ) {
            $transporter = new $transporter( $params );
        }

        self::$_transporters[ ] = $transporter;
    }

    public function beforeSaveNotificationTypeOrderStatus( $data )
    {
        $params = $data->params;
        $order = $params[ 'order' ];
        if ( !( $order instanceof \Order ) ) {
            throw new \RuntimeException( "Something went wrong, event params 'order' is not instanceof Order" );
        }
        $data_array = array();
        if ( in_array( $order->status, \Order::$notificationCustomer ) ) {

            $buyer = $order->getBuyer();
            if ( !( $buyer instanceof \Users ) ) {
                throw new \RuntimeException( "Something went wrong, event params 'buyer' is not instanceof User" );
            }
            $data_array[ 'user_id' ] = $buyer->id;
            $data_array[ 'user_name' ] = $buyer->username;
            $data_array[ 'order_status' ] = $order->status;
            $data_array[ 'order_status' ] = $order->status;
            $data_array[ 'order_id' ] = $order->id;
            $data_array[ 'order_name' ] = $order->getCode();
            $data_array[ 'time' ] = time();
            $data_array[ 'priority' ] = self::PRIORITY_NORMAL;
            $data_array[ 'is_express_cn_vn' ] = 0;
            if (\Order::STATUS_WAITING_FOR_DELIVERY == $order->getStatus()) {
                //check is delivery express
                $express = \OrderService::findOneByOrderIdAndServiceCode($order->getId(), \Services::TYPE_EXPRESS_CHINA_VIETNAM);
                if ($express) {
                    $data_array[ 'is_express_cn_vn' ] = 1;
                }
            }
            return $this->saveNotification( self::TYPE_NOTIFY_ORDER_STATUS, $data_array );
        }


    }

    public function beforeSaveNotificationTypeConfirmOrder( $data )
    {
        $params = $data->params;
        $type_confirm = $params[ 'type_confirm' ]; //price, quantity
        $order = $params[ 'order' ];
        if ( !( $order instanceof \Order ) ) {
            throw new \RuntimeException( "Something went wrong, event params 'order' is not instanceof Order" );
        }
        $data_array = array();
        $buyer = $order->getBuyer();
        if ( !( $buyer instanceof \Users ) ) {
            throw new \RuntimeException( "Something went wrong, event params 'buyer' is not instanceof User" );
        }
        $data_array[ 'user_id' ] = $buyer->id;
        $data_array[ 'user_name' ] = $buyer->username;
        $data_array[ 'type_confirm' ] = $type_confirm;
        $data_array[ 'order_id' ] = $order->id;
        $data_array[ 'order_name' ] = $order->getCode();
        $data_array[ 'time' ] = time();
        $data_array[ 'priority' ] = self::PRIORITY_CONFIRM_ORDER;
        return $this->saveNotification( self::TYPE_NOTIFY_CONFIRM_ORDER, $data_array );


    }

    public function beforeSaveNotificationTypeChatOrder( $data )
    {
        $params = $data->params;
        $message_content = $params[ 'message_content' ];
        $sender_id = $params[ 'sender_id' ];
        $type_chat = $params[ 'type_chat' ]; //human,log,activity
        $order = $params[ 'order' ];
        if ( !( $order instanceof \Order ) ) {
            throw new \RuntimeException( "Something went wrong, event params 'order' is not instanceof Order" );
        }
        $data_array = array();
        $buyer = $order->getBuyer();
        if ( !( $buyer instanceof \Users ) ) {
            throw new \RuntimeException( "Something went wrong, event params 'buyer' is not instanceof User" );
        }
        $data_array[ 'user_id' ] = $buyer->id;
        $data_array[ 'user_name' ] = $buyer->username;
        $data_array[ 'sender_id' ] = $sender_id;
        $data_array[ 'type_chat' ] = $type_chat;
        $data_array[ 'message_content' ] = $message_content;
        $data_array[ 'order_id' ] = $order->id;
        $data_array[ 'order_name' ] = $order->getCode();
        $data_array[ 'time' ] = time();
        $data_array[ 'priority' ] = self::PRIORITY_NORMAL;
        return $this->saveNotification( self::TYPE_NOTIFY_CHAT_ORDER, $data_array );


    }

    public function saveNotification( $type, $array_data )
    {
        if ( is_array( $array_data ) && count( $array_data ) > 0 ) {
            switch ( $type ) {
                case self::TYPE_NOTIFY_CONFIRM_ORDER :
                    $order = new Order();
                    $result = $order->saveConfirmOrder( $type, $array_data );
                    break;
                case self::TYPE_NOTIFY_ORDER_STATUS:
                    $order = new Order();
                    $result = $order->saveOrderStatus( $type, $array_data );
                    break;
                case self::TYPE_NOTIFY_CHAT_ORDER:
                    $order = new Order();
                    $result = $order->saveChatOrder( $type, $array_data );
                    break;
//                case self::TYPE_NOTIFY_MONEY_ACCOUNT:

                default:
                    return false;
            }
            if ( $result ) {
                $this->sendNotification( $type, $array_data );
            }
            return $result;
        } else {
            return false;
        }
    }

    public function sendNotification( $type, $array_data )
    {
        if (!$this->_enable) {
            return;
        }
        $user = \Users::retrieveById( $array_data[ 'user_id' ] );
        $message_content = '';
        $order_name = $array_data[ 'order_name' ];
        switch ( $type ) {
            case self::TYPE_NOTIFY_ORDER_STATUS:
                switch ( $array_data[ 'order_status' ] ) {
                    case 'SELLER_DELIVERY':
                        $order_status = 'người bán đã giao hàng';
                        break;
                    case 'RECEIVED_FROM_SELLER':
                        $order_status = 'đã nhân hàng từ người bán';
                        break;
                    case 'CHECKED':
                        $order_status = 'đã được kiểm hàng';
                        break;
                    case 'BOUGHT':
                        $order_status = 'đã được mua';
                        break;
                    case 'WAITING_DELIVERY':
                        $order_status = 'đã có thể giao hàng cho bạn';
                        break;
                    case 'CONFIRM_DELIVERY':
                        $order_status = 'chuẩn bị được giao hàng cho bạn';
                        break;
                    case 'DELIVERING':
                        $order_status = 'đang trên đường giao cho bạn';
                        break;
                }
                $message_content = "Đơn hàng " . $order_name . " " . $order_status;
                break;
            case self::TYPE_NOTIFY_CONFIRM_ORDER:
                switch ( $array_data[ 'type_confirm' ] ) {
                    case 'price':
                        $confirm_msg = 'đang chờ được xác nhận về giá';
                        break;
                    case 'quantity':
                        $confirm_msg = 'đang chờ được xác nhận về số lượng';
                        break;
                }
                $message_content = "Đơn hàng " . $order_name . " " . $confirm_msg;
                break;
            case self::TYPE_NOTIFY_CHAT_ORDER:
                switch ( $array_data[ 'type_chat' ] ) {
                    case 'human':
                        $confirm_msg = 'Sếu Đỏ gửi cho bạn tin nhắn ở đơn hàng';
                        break;
                    case 'activity':
                        $confirm_msg = $array_data[ 'message_content' ];
                        break;
                    case 'log':
                        $confirm_msg = $array_data[ 'message_content' ];
                        break;
                }
                $message_content = "Đơn hàng " . $order_name . " " . $confirm_msg;
                break;
        }

        $transporters = self::getTransporters();
        foreach ( $transporters as $transporter ) {
            $from = $this->getBrandname();
            $transporter->sendNotification( $from, $user, $message_content );


        }

    }

    public function getTransporters()
    {
        return self::$_transporters;
    }


}