<?php
use Flywheel\Event\Event;
use mongodb\OrderCommentResource\BaseContext;
use \mongodb\OrderCommentResource;

class ApiEvent extends Event
{
    public function logStockOrderStatus( $data )
    {
//        $params = $data->params;
//        $order = isset( $params[ "order" ] ) ? $params[ "order" ] : '';
//        $warehouse = isset( $params[ "warehouse" ] ) ? $params[ "warehouse" ] : '';
//        if ( $order instanceof \Order && $warehouse != "" ) {
//            $context_log = new OrderCommentResource\Log( "Trạng thái kho của đơn hàng là " . $order->getWarehouseStatusTitle() );
//            \OrderComment::addComment( 0, $order->getId(), \mongodb\OrderComment::TYPE_INTERNAL, $context_log, false,
//                BaseContext::TYPE_LOG);
//        }
    }
}
