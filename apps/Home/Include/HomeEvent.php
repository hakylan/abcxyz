<?php

use Flywheel\Redis\Client;

class HomeEvent extends \Flywheel\Event\Event {

    public function onBeginCart(){}

    public function afterRegister($user) {

    }
    public function beginRegister($user) {

    }
    public function afterLogin() {

    }
    public function beginLogout() {

    }

    public function sendQueueHideItem($data) {

    }

    public function onSuccessCartAdd($data){
    }

    public function errorOrderLink($data){
        \OrderingTool::sendMailError($data->params['link_error'],"order_link",$data->params['message']);
        if(isset($data->params['link_error']) && $data->params['link_error'] != ''){
            $redis = Client::getConnection('order_link_error');
            $message = isset($data->params['message']) ? $data->params['message'] : 'Error';

            $error = array(
                "message" => $message,
                "link_error" => $data->params['link_error']
            );

            $redis->zAdd(REDIS_ORDER_LINK_ERROR,time(),json_encode($error));
        }
    }

    /**
     * After deposit for order success
     * @param $data
     */
    public function afterOrderDeposit($data) {

    }
}