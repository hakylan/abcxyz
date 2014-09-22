<?php
namespace mongodb\NotificationResource;

use MongoQB\Exception;
use SeuDo\MongoDB;
use Flywheel\Redis\Client;

class Notification
{
    const
        TOTAL_NEW_NOTIFICATION = 'total_notification_by_user_id';
    protected $_attributes = array();
    protected $_collectionName = 'notification';
    protected $_user_id;
    protected $_user_name;
    protected $_type;
    protected $_order_id;
    protected $_order_name;
    protected $_created_time;
    protected $_is_new;
    protected $_priority;

    protected function saveNotification()
    {
        $conn = MongoDB::getConnection( $this->_collectionName );
        try {

            if ( is_array( $this->_attributes ) && count( $this->_attributes ) > 0 ) {
                //if chat with customer then merge notification
                $is_insert = true;
                if(isset($this->_attributes['type_chat']) && $this->_attributes['type_chat'] == 'human'){
                    try {
                        $data_notify = $conn->where( array( 'order_id' => $this->_attributes['order_id'],'type_chat'=>'human','is_new'=> 1 ) )
                            ->orderBy( array('created_time'=>'desc') )->limit( 1 )
                            ->get( $this->_collectionName );

                        if(count($data_notify)==1){
                            $data = $data_notify[0];
                            $total_message = isset($data['total_message'])? intval($data['total_message']) : 1;
                            $total_message++;
                            $conn->where( array( '_id' => new \MongoId($data[ '_id' ]->{'$id'}) ) )
                                ->set( array( 'created_time' => new \MongoDate(time()),'total_message'=>strval($total_message),'message_content'=>$this->_attributes['message_content'] ) )
                                ->update( $this->_collectionName );
                            $is_insert = false;

                        }else{
                            $conn->insert( $this->_collectionName, $this->_attributes );
                        }
                    } catch ( \MongoQB\Exception $e ) {
                        \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
                        return false;
                    }

                }else{
                    $conn->insert( $this->_collectionName, $this->_attributes );
                }

                //update total notification by user

                if ( isset( $this->_attributes[ 'user_id' ] ) && $is_insert ) {
                    $redis_client = Client::getConnection('notification');
                    $user_id = intval( $this->_attributes[ 'user_id' ] );

                    if ( $redis_client->get( self::TOTAL_NEW_NOTIFICATION . '_' . $user_id ) === false ) {
                        $redis_client->set( self::TOTAL_NEW_NOTIFICATION . '_' . $user_id, 0 );
                        $redis_client->incr( self::TOTAL_NEW_NOTIFICATION . '_' . $user_id );
                    } else {
                        $redis_client->incr( self::TOTAL_NEW_NOTIFICATION . '_' . $user_id );
                    }

                }
                return true;
            } else {
                return false;
            }
        } catch ( \MongoQB\Exception $e ) {
            \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
            return false;
        }
    }

    public static function getNewTotalNotificationByUser( $user_id )
    {
        $redis_client = Client::getConnection('notification');
        $total = $redis_client->get( self::TOTAL_NEW_NOTIFICATION . '_' . intval( $user_id ) );
        return $total;
    }

    public static function resetNewTotalNotificationByUser( $user_id )
    {
        $redis_client = Client::getConnection('notification');
        $total = $redis_client->set( self::TOTAL_NEW_NOTIFICATION . '_' . intval( $user_id ), 0 );
        return $total;
    }

    public function setReadNotifyById( $notify_id )
    {
        $conn = MongoDB::getConnection( $this->_collectionName );
        try {
            $conn->where( array( '_id' => new \MongoId( $notify_id ) ) )
                ->set( array( 'is_new' => 0 ) )
                ->update( $this->_collectionName );
            return true;
        } catch ( \MongoQB\Exception $e ) {
            \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
            return false;
        }

    }

    public function checkNotifyById( $notify_id )
    {
        $conn = MongoDB::getConnection( $this->_collectionName );
        try {
            $data_notify = $conn->where( array( '_id' => new \MongoId( $notify_id ) ) )
                ->get( $this->_collectionName );
            return $data_notify;
        } catch ( \MongoQB\Exception $e ) {
            \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
            return false;
        }
    }

    public static function getTotalNotifyByUser($user_id)
    {
        $notification = new Notification();
        $conn = MongoDB::getConnection( $notification->_collectionName );
        $total_notify = $conn->where( 'user_id', intval( $user_id ) )->get( $notification->_collectionName );
        return count($total_notify);
    }

    public static function readNotificationByUser( $user_id, $page = 1, $limit = 10 )
    {
        $limit = intval( $limit );
        $page = intval( $page );
        $from = ( $page - 1 ) * $limit;
        $notification = new Notification();
        $conn = MongoDB::getConnection( $notification->_collectionName );
        try {
            $order_by_new = array();
            $order_by_new[ 'is_new' ] = 'desc';
            $order_by_new[ 'priority' ] = 'desc';
            $order_by_new[ 'created_time' ] = 'desc';
            $order_by_old = array();
            $order_by_old[ 'created_time' ] = 'desc';
            $total_notify_new = count($conn->where(array('user_id'=>intval( $user_id ),'is_new'=>1) )->get( $notification->_collectionName ));
            $items ='';
            $to = $from+ $limit;
            if($total_notify_new <= $from){
                //get old notify
                $from_old = $from - $total_notify_new;
                $items = $conn->where( array('user_id'=>intval( $user_id ),'is_new'=>0) )->orderBy( $order_by_old )->offset( $from_old )->limit( $limit )->get( $notification->_collectionName );
            }elseif($from <$total_notify_new && $total_notify_new < $to){
                //get both new notify  and old notify
                $items_new = $conn->where( array('user_id'=>intval( $user_id ),'is_new'=>1) )->orderBy( $order_by_new )->offset( $from )->limit( $limit )->get( $notification->_collectionName );
                $items_old = $conn->where( array('user_id'=>intval( $user_id ),'is_new'=>0) )->orderBy( $order_by_old )->offset( 0 )->limit( $limit-count($items_new) )->get( $notification->_collectionName );
                $items = array_merge($items_new,$items_old);
            }elseif($total_notify_new >=$to){
                $items = $conn->where( array('user_id'=>intval( $user_id ),'is_new'=>1) )->orderBy( $order_by_new )->offset( $from )->limit( $limit )->get( $notification->_collectionName );
            }
            return $items;
        } catch ( \MongoQB\Exception $e ) {
            \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
            return false;
        }
    }

    public static function searchNotificationByCondition( $condition, $page = 1, $limit = 10 )
    {
        $limit = intval( $limit );
        $page = intval( $page );
        $from = ( $page - 1 ) * $limit;
        $notification = new Notification();
        $conn = MongoDB::getConnection( $notification->_collectionName );
        try {
            $order_by = array();
            $order_by[ 'created_time' ] = 'desc';
            $items = $conn->where( $condition )->orderBy( $order_by )->offset( $from )->limit( $limit )->get( $notification->_collectionName );
            return $items;
        } catch ( \MongoQB\Exception $e ) {
            \SeuDo\Logger::factory( 'notification' )->addError( $e->getMessage() );
            return false;
        }
    }

}