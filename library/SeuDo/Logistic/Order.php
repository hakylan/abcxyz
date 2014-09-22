<?php
namespace SeuDo\Logistic;


use SeuDo\Logger;

class Order {

    public $order, $order_item, $order_comment, $customer, $shipping_address;
    public $valid = false,$error_message;


    public function __construct( $data = array() ) {

        if(isset($data['order'])) $this->order = $data['order'];

        if(isset($data['order_item'])) $this->order_item = $data['order_item'];

        if(isset($data['order_comment'])) $this->order_comment = $data['order_comment'];

        if(isset($data['customer'])) $this->custommer = $data['customer'];

        if(isset($data['shipping_address'])) $this->shipping_address = $data['shipping_address'];
    }

    public function valid () {
        if(!$this->order || empty($this->order)) {
            $this->error_message[] = 'order is not valid !';
            return false;
        }
        if(!$this->order_item || empty($this->order_item)) {
            $this->error_message[] = 'order_item is not valid !';
            return false;
        }
        if(!$this->custommer || empty($this->custommer)){
            $this->error_message[] = 'customer is not valid !';
            return false;
        }
        if(!$this->shipping_address || empty($this->shipping_address)) {
            $this->error_message[] = 'shipping_address is not valid !';
            return false;
        }
        $this->valid = true;
        return true;
    }

    public function synchronize() {
        $client = Client::getClient();

        $valid = $this->valid();

        if($valid == false) throw $this->error_message;

        try {
            $params = array (
                'order' => json_encode($this->order),
                'order_item' => $this->order_item,
                'order_comment' => $this->order_comment,
                'customer' => $this->custommer,
                'shipping_address' => $this->shipping_address
            );

            $client->post('orders/new', $params);

            Logger::factory('order_synchronize')->error('Order synchronize !', $params);

            return $client;
        } catch(\Exception $e) {
            Logger::factory('order_synchronize')->error('Fail when calling to synchronization', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }
}