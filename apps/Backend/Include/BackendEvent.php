<?php
use Flywheel\Factory;
use Flywheel\Event\Event;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use \mongodb\OrderCommentResource;
use \mongodb\ComplaintSellerCommentResource;

class BackendEvent extends Event
{

    public function afterAddNewUser( $data )
    {
        $user = \Users::findOneByUserName( $data[ 'username' ] );
        $user_mobile = new \UserMobiles();

        $data_mobile[ 'user_id' ] = $user->getId();
        $data_mobile[ 'mobile' ] = $data[ 'mobile' ];
        $data_mobile[ 'created_time' ] = $data[ 'joined_time' ];
        $user_mobile->hydrate( $data_mobile );
        $user_mobile->save();
    }

    public function logComplaintSellerComment( $data )
    {
        $params = $data->params;
        $user = BaseAuth::getInstance()->getUser();
        $complaint_seller_id = isset( $params[ "complaint_seller_id" ] ) ? $params[ "complaint_seller_id" ] : "";
        $order = isset( $params[ "order" ] ) ? $params[ "order" ] : "";
        $message = isset( $params[ "message" ] ) ? $params[ "message" ] : "";
        $activity = isset( $params[ "is_activity" ] ) ? $params[ "is_activity" ] : false;
        $is_log = isset( $params[ "is_log" ] ) ? $params[ "is_log" ] : false;
        $is_chat = isset( $params[ "is_chat" ] ) ? $params[ "is_chat" ] : false;
        $is_public = isset( $params[ "is_public" ] ) ? $params[ "is_public" ] : false;
        $is_external = isset( $params[ "is_external" ] ) ? $params[ "is_external" ] : false;

        if($user instanceof \Users && $message != "") {
            $order_id = $order->getId();
            $user_id = $user->getId();
            $message_log = "{$user->getUsername()} - {$user->getFullName()} " . $message;
            $context_chat = new Chat( $message );
            $context_log = new ComplaintSellerCommentResource\Log( $message_log );
            $context_activity = new ComplaintSellerCommentResource\Activity( $message );

            if ( $is_external ) {
                $type = \mongodb\ComplaintSellerComment::TYPE_EXTERNAL;
            } else {
                $type = \mongodb\ComplaintSellerComment::TYPE_INTERNAL;
            }
            if ( $activity ) {
                \ComplaintSellerComment::addComment( $user, $order_id, 0, $complaint_seller_id, $type, $context_activity,
                                                        $is_public, BaseContext::TYPE_ACTIVITY );
            }
            if ( $is_log ) {
                \ComplaintSellerComment::addComment( $user, $order_id, 0, $complaint_seller_id, $type, $context_activity,
                                                        $is_public, BaseContext::TYPE_LOG );

            }
            if ( $is_chat ) {
                \ComplaintSellerComment::addComment( $user, $order_id, 0, $complaint_seller_id, $type, $context_activity,
                                                        $is_public, BaseContext::TYPE_CHAT );
            }
        }
    }

    public function logOrderComment( $data )
    {
        $params = $data->params;
        $user = BaseAuth::getInstance()->getUser();
        $order = isset( $params[ "order" ] ) ? $params[ "order" ] : array();
        $message = isset( $params[ "message" ] ) ? $params[ "message" ] : "";
        $activity = isset( $params[ "is_activity" ] ) ? $params[ "is_activity" ] : false;
        $is_log = isset( $params[ "is_log" ] ) ? $params[ "is_log" ] : false;
        $is_chat = isset( $params[ "is_chat" ] ) ? $params[ "is_chat" ] : false;
        $is_public = isset( $params[ "is_public" ] ) ? $params[ "is_public" ] : false;
        $is_external = isset( $params[ "is_external" ] ) ? $params[ "is_external" ] : false;
//        $order = isset($params);
        if ( $user instanceof \Users && $order instanceof \Order && $message != "" ) {
            $order_id = $order->getId();
            $user_id = $user->getId();
            $context_chat = new Chat( $message );
            $context_log = new OrderCommentResource\Log( $message );
            $context_activity = new OrderCommentResource\Activity( $message );

            if ( $is_external ) {
                $type = \mongodb\OrderComment::TYPE_EXTERNAL;
            } else {
                $type = \mongodb\OrderComment::TYPE_INTERNAL;
            }
            if ( $activity ) {
                \OrderComment::addComment( $user_id, $order_id, $type, $context_activity, $is_public,
                    BaseContext::TYPE_ACTIVITY );
            }
            if ( $is_log ) {
                \OrderComment::addComment( 0, $order_id, $type, $context_log, $is_public,
                    BaseContext::TYPE_LOG );

            }
            if ( $is_chat ) {
                \OrderComment::addComment( $user_id, $order_id, $type, $context_chat, $is_public,
                    BaseContext::TYPE_CHAT );
            }
        }
    }



    public function afterCraneLogin()
    {
    }

    public function afterUserLogin()
    {

    }

    public function afterAddNewRole()
    {

    }

    public function afterAddExchange()
    {

    }

    public function afterEditOrder()
    {

    }

    public function afterDelOrder()
    {

    }

    public function afterEditOrderFreightBill()
    {

    }

    public function afterEditOrderInvoice()
    {

    }

    public function afterSelectOrderPurchasers()
    {

    }

    public function afterSkipsOrderPurchasers()
    {

    }

}