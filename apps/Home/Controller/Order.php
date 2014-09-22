<?php
namespace Home\Controller;

use Home\Controller\HomeBase;
use \Flywheel\Redis\Client as Client;
class Order extends HomeBase
{


    public $authen;
    public $user;


    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->authen = \HomeAuth::getInstance();
        $this->user = $this->authen->getUser();


        $eventDispatcher = $this->getEventDispatcher();
        //$eventDispatcher->addListener('onSuccessCartAdd', array(new \HomeEvent(), 'onSuccessCartAdd'));
    }


    public function executeDefault()
    {



        //$this->view()->title('Đơn hàng chờ thanh toán');
        $this->setView('Order/deposited');
        return $this->renderComponent();
    }

    public function executeUpdateQuantityOrderItem(){
        if($_SERVER['REMOTE_ADDR']=='42.115.210.26'){
            $order_item = \OrderItem::retrieveById(18);
            if($order_item instanceof \OrderItem){
                if($order_item->getItemId() == "1291858162" && $order_item->getOrderId() == 9 && $order_item->getReciveQuantity() == 8){
                    $result = $order_item->updateReceiveQuantity(9);
                    if($result){
                        echo "OK";
                        exit();
                    }else{
                        echo "not ok";
                    }

                }
            }
        }
    }




    public function executeGetOrders()
    {
        $type = strtolower($this->request()->get('type'));
        $type = $type ? $type : 'deposited';

        $date_from = $this->request()->get('date_from');
        $date_to = $this->request()->get('date_to');
        switch ($type) {
            case 'deposited':


                break;
            default:
                break;


        }


    }
}
