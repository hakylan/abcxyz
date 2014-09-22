<?php
use SeuDo\Logger;
use mongodb\DeliveryBillCommentResource\BaseContext;
use mongodb\DeliveryBillCommentResource\Chat;
use \mongodb\DeliveryBillCommentResource;
use SeuDo\MongoDB;

class DeliveryEvent extends \Flywheel\Event\Event
{

    private $logger = null;

    public function __construct()
    {

    }

    /**
     * Log order comment when choose services in backend
     * @param $data
     */
    public function logDeliveryCreateBill( $data )
    {
        $params = $data->params;
        $domestic_shipping = isset( $params[ "domestic_shipping" ] ) ? $params[ "domestic_shipping" ] : '';
        $staff = isset( $params[ "staff" ] ) ? $params[ "staff" ] : '';
        $is_public_profile = isset( $params[ "is_public" ] ) ? $params[ "is_public" ] : true;

        if ( $domestic_shipping instanceof \DomesticShipping && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE
        ) {
            $activity = BaseContext::TYPE_ACTIVITY;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;
            $content_activity = new DeliveryBillCommentResource\Activity( "Tạo phiếu giao hàng" );

            $created_time = new \MongoDate();
            $delivery_bill_comment = new \mongodb\DeliveryBillComment();
            $delivery_bill_comment->setCreatedBy( $staff->getId() );
            $delivery_bill_comment->setDomesticShippingId( $domestic_shipping->getId() );
            $delivery_bill_comment->setScope( $internal );
            $delivery_bill_comment->setContext( $content_activity );
            $delivery_bill_comment->setIsPublicProfile( $is_public_profile );
            $delivery_bill_comment->setTypeContext( $activity );
            $delivery_bill_comment->setCreatedTime( $created_time );
            return $delivery_bill_comment->save();
         }
    }

    public function logChangeRealCod($data){
        $params = $data->params;
        $domestic_shipping = isset( $params[ "domestic_shipping" ] ) ? $params[ "domestic_shipping" ] : '';
        $staff = isset( $params[ "staff" ] ) ? $params[ "staff" ] : '';
        $is_public_profile = isset( $params[ "is_public" ] ) ? $params[ "is_public" ] : true;
        $real_cod = isset( $params[ "real_cod" ] ) ? $params[ "real_cod" ] : '';
        $message = "Sửa phí thực thu COD: " . \Common::numberFormat( $real_cod ) . " VNĐ";
        if ( $domestic_shipping instanceof \DomesticShipping && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE
        ) {
            $activity = BaseContext::TYPE_ACTIVITY;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;
            $content_activity = new DeliveryBillCommentResource\Activity( $message );

            $created_time = new \MongoDate();
            $delivery_bill_comment = new \mongodb\DeliveryBillComment();
            $delivery_bill_comment->setCreatedBy( $staff->getId() );
            $delivery_bill_comment->setDomesticShippingId( $domestic_shipping->getId() );
            $delivery_bill_comment->setScope( $internal );
            $delivery_bill_comment->setContext( $content_activity );
            $delivery_bill_comment->setIsPublicProfile( $is_public_profile );
            $delivery_bill_comment->setTypeContext( $activity );
            $delivery_bill_comment->setCreatedTime( $created_time );
            return $delivery_bill_comment->save();
        }
    }


}