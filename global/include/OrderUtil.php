<?php
use SeuDo\Queue;
use SeuDo\Logistic\BarcodeTracking;

class OrderUtil {

    public static function pushTransferOrderLogistic($order,$message = ""){
        try{
            if(is_numeric($order)){
                $order = \Order::retrieveById($order);
            }

            if(!$order instanceof \Order){
                throw new \InvalidArgumentException("Not exist Order when post order info to Logistic");
            }
//
//            $buyer = $order->getBuyer();
//
//            if(!$buyer instanceof \Users){
//                throw new \InvalidArgumentException("Not exist User when post order info to Logistic with order :".$order->getId());
//            }
//
//            $address = $order->getAddress();
//
//            if(!$address instanceof \UserAddress){
//                throw new \InvalidArgumentException("Not exist Address when post order info to Logistic with order :".$order->getId());
//            }
//
//            $reciver_phone = $address->getReciverPhone() == "" ? $buyer->getOneMobileUsing() : $address->getReciverPhone();
//
//            $freight_barcode = \Packages::findByOrderId($order->getId());
//
//            $freight = array();
//
//            if(!empty($freight_barcode)){
//                foreach ($freight_barcode as $freight_code) {
//                    if($freight_code instanceof \Packages){
//                        $freight[] = $freight_code->getFreightBill();
//                    }
//                }
//            }
//
//            $is_express = $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
//            $standard_delivery = $order->mappingToService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
//            $wood_services = $order->mappingToService(\Services::TYPE_WOOD_CRATING);
//            $fragile_services = $order->mappingToService(\Services::TYPE_FRAGILE);
//            $avoid_water = false;
//            $electron = false;
//            $mechanic = false;
//            $high_value = $order->mappingToService(\Services::TYPE_HIGH_VALUE);
//            $district = $address->getDistrict();
//            $province = $address->getProvince();

            $param = array(
                "message" => $message,
                "data" => BarcodeTracking::getOrderInfo($order)
            );


//            $param = array(
//                "message" => $message,
//                'data'=>
//                    array(
//                    "buyer" => array(
//                        "username" => $buyer->getUsername(),
//                        "fullname" => $buyer->getFullName(),
//                        "phone"    => $buyer->getOneMobileUsing(),
//                        "email"    => $buyer->getEmail(),
//                        "customer_code" => $buyer->getCode()
//                    ),
//                    "receiver" => array(
//                        "receiver_name" => $address->getReciverName(),
//                        "phone" => $reciver_phone,
//                        "note" => $address->getNote(),
//                        "email" => $buyer->getEmail(),
//                        "address" => array(
//                            "detail" => $address->getDetail(),
//                            "district_code" => $district->getLogisticCode(),
//                            "state_code" => $province->getLogisticCode()
//                        ),
////                        $address->getDetail()." {$address->getDistrictLabel()} - {$address->getProvinceLabel()}",
//                        "distribution_warehouse" => $order->getDestinationWarehouse()
//                    ),
//                    "order_information" => array(
//                        "order_id" => $order->getId(),
//                        "order_code"=> $order->getCode(),
//                        "seller_name"=> $order->getSellerName(),
//                        "homeland"=> $order->getSellerHomeland(),
//                        "homeland_url"=> Common::getHomelandUrl($order->getSellerHomeland()),
//                        "service"=> "seudo",
//                        "service_url"=> \SeuDo\Main::getHomeUrl(),
//                        "service_barcode" => "S/{$order->getId()}",
//                        "freight_barcode" => $freight,
//                        "service_request"=> array(
//                            "transport_method"=> array(
//                                "EXPRESS" => $is_express,
//                                "STANDARD_DELIVERY" => $standard_delivery
//                            ),
//                            "extra_service" => array(
//                                "WOODEN_CRATING"=> $wood_services,
//                                "FRAGILE"=> $fragile_services,
//                                "AVOID_WATER"=> $avoid_water,
//                                "ELECTRON"=> $electron,
//                                "MECHANIC"=> $mechanic,
//                                "EXPENSIVENESS"=> $high_value
//                            ),
//                            "private_service" => array(
//                                "VIP" => false
//                            )
//                        )
//                    ))
//            );
            if(Queue::getQueue(Queue::TRANSFER_ORDER_LOGISTIC)->push(json_encode((array) $param))){
                return true;
            }
            return false;
        }catch (\Exception $e){
            throw $e;
        }
    }

//
//    /**
//     * Push order data to backup queue when send to api Logistic error
//     * @param $param
//     * @return bool
//     * @throws Exception
//     */
//    public static function pushBackupTransferOrderLogistic($param){
//        try{
//            if(is_array($param)){
//                $param = json_encode($param);
//            }
//            if(Queue::transferOrderLogistic3M()->push($param)){
//                return true;
//            }
//            return false;
//        }catch (\Exception $e){
//            throw $e;
//        }
//    }
}