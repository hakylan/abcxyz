<?php
namespace SeuDo\Event;
use SeuDo\Logger;
use SeuDo\Event;

class Order extends Event {


    public function createOrderBegin($params){

    }
    public function createOrderSuccess($params){
        print_r($params);exit;
    }
    public function createOrderEnd($params){

    }
    public function createOrderError($params){

    }
    public function changeStatusOrderBegin($params){
        
    }
}