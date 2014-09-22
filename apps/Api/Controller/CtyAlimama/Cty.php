<?php
namespace Api\Controller\CtyAlimama;

use Api\Controller\ApiBase;
use Flywheel\Controller\Api;
use Flywheel\Validator\Util;
use mongodb\BarcodeTracking;
use SeuDo\Logger;
use SeuDo\Queue;

class Cty extends ApiBase {
    public function beforeExecute() {
        //overwrite to remove verify request. Need remove it

        $this->getEventDispatcher()
            ->addListener('orderStockOut',
                array( new \ApiEvent(), 'logStockOrderStatus' ) );

        $this->getEventDispatcher()
            ->addListener('orderStockIn',
                array( new \ApiEvent(), 'logStockOrderStatus' ) );

        header("Access-Control-Allow-Origin: *");
    }

    public function getRequestData(){
        $start_time = strtoupper(trim($this->get('start_time')));
        $end_time = strtoupper(trim($this->get('end_time')));
//        $freight = trim($this->post("freight"));
        try{
            $start_time = \Common::validDateTime($start_time);
            $end_time = \Common::validDateTime($end_time);
            $query = \Order::read();
            $query->andWhere("created_time >='{$start_time}'");
            $query->andWhere("created_time <= '{$end_time}'");
            $query->andWhere("status != '".\Order::STATUS_INIT."'");
            $order_list = \OrderPeer::getOrder($query);

            $order_list = $this->_buildOrderArray($order_list);

            $array = array("code" => 200,
                "data" => $order_list);

            return $this->sendResponse(200,$array);

        }catch (\Exception $e){
            return $this->sendResponse(500,$e->getMessage());
        }
    }

    /**
     * @param $order_list
     * @return array
     */
    private function _buildOrderArray($order_list){

        $order_cty_array = array();

        foreach ($order_list as $order) {
            if($order instanceof \Order){
                $freight = \Packages::findByOrderId($order->getId());
                $freight_bill = "";
                if($freight){
                    foreach ($freight as $bill) {
                        if($bill instanceof \Packages){
                            $freight_bill .= $bill->getFreightBill().";";
                        }
                    }
                }

                $teller_staff = \Users::retrieveById($order->getTellersId());
                $teller = "";
                if($teller_staff instanceof \Users){
                    $teller = $teller_staff->getUsername();
                }


                $data = array(
                    'order_id'=> $order->getId(),
                    'order_code'=> $order->getCode(),
                    'homeland'=> $order->getSellerHomeland(),
                    'account'=> $order->getAccountPurchaseOrigin(),
                    'status'=> $this->_getStatusForCty($order),//trang thai don hang
                    'exchange'=> $order->getExchange(),
                    'total_price'=> $order->getTotalAmount() / $order->getExchange(),
                    'price'=> $order->getBoughtAmount() / $order->getExchange(),
                    'ship'=> $order->getDomesticShippingFee(),
                    'plus'=> $order->getRealSurcharge() / $order->getExchange(),//phu phi
                    'refund'=> $order->getRealRefundAmount() / $order->getExchange(),//tra lai
                    'complain'=> "",
                    'freight_bill'=> $freight_bill,
                    'taobao_bill'=> $order->getInvoice(),
                    'weight'=> $order->getWeight(),
                    'item_num'=> $order->getPendingQuantity(),
                    'staff_buyer'=> $teller,
                    'time_created'=> \Common::getTimeStamp($order->getCreatedTime()),
                    'time_bought'=> \Common::getTimeStamp($order->getBoughtTime()),
                    'time_checked'=> \Common::getTimeStamp($order->getCheckedTime()),
                    'service'=> 'sd',
                );
                $order_cty_array[] = $data;
            }
        }
        return $order_cty_array;
    }


    private function _getStatusForCty(\Order $order){
        if($order->getStatus() == \Order::STATUS_CANCELLED || $order->getIsDeleted() == 1){
            return 12;
        }
        if($order->isBeforeStatus(\Order::STATUS_BOUGHT)){
            return 2;
        }else if($order->isBetweenStatus(\Order::STATUS_BOUGHT,\Order::STATUS_TRANSPORTING)){
            return 4;
        }else if($order->isBetweenStatus(\Order::STATUS_CHECKED,\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY)){
            return 6;
        }else if($order->getStatus() == \Order::STATUS_DELIVERING){
            return 8;
        }
        if($order->getStatus() == \Order::STATUS_RECEIVED){
            return 10;
        }
        return 0;
    }
}