<?php
namespace SeuDo\Logistic;


use SeuDo\Logger;

class OrderTransfer {

    public $order, $order_item, $order_comment, $customer, $shipping_address;
    public $valid = false,$error_message;
    private $logger = null;


    public function __construct( $data = array() ) {
    }

    public static function postOrderInfo($param){
        $client = Client::getClient();
        try {
//            if(is_numeric($order)){
//                $order = \Order::retrieveById($order);
//            }
//
//            if(!$order instanceof \Order){
//                throw new \InvalidArgumentException("Not exist Order when post order info to Logistic");
//            }
//
//            $buyer = $order->getBuyer();
//
//            if(!$buyer instanceof \Users){
//                throw new \InvalidArgumentException("Not exist User when post order info to Logistic with order :".$order->getId());
//            }
//
//            $address = $order->getAddress();
//
//            if(!$buyer instanceof \Users){
//                throw new \InvalidArgumentException("Not exist Address when post order info to Logistic with order :".$order->getId());
//            }
//
//            $param = array(
//                "message" => "",
//                "buyer" => array(
//                    "username" => $buyer->get,
//                    "fullname" => "THIẾU HÀNG PHẢI BÁO CHO KHÁCH HÀNG TRƯỚC KHI ĐẶT",
//                    "phone"    => "0934909020",
//                    "email"    => "lazashop.vn@gmail.com",
//                    "customer_code" => "NU64"
//                )
//            );


            print_r($param);
            print_r("\n");
            print("param");
            print("\n");


            if(is_string($param)){
                $param = json_decode($param,true);
            }

            $message = isset($param["message"]) ? $param["message"] : "";

            $data = isset($param["data"]) ? $param["data"] : array();

            $param = array(
                "message" => $message,
                "data" => json_encode($data)
            );
            if(!empty($data)){
                $client->post("orders/updateOrder",$param);

                return $client->getResponse();
            }else{
                return false;
            }
        } catch(\Exception $e) {
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }

    /**
     * @param string $level the log level follow Monolog level
     * @param string $message
     * @param array $context
     */
    public static function log($level, $message, $context = array()) {
        try {
            $logger = Logger::factory('Logistic');
            $logger->addRecord($level, $message, $context);
        } catch (\Exception $e) {
            Logger::factory('system')->error("Fail when create mongo logging: {$message}", $context);
            Logger::factory('system')->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
        }
    }
}