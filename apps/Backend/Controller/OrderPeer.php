<?php

namespace Backend\Controller;

use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Exception;
use SeuDo\Event\Order;
use \SeuDo\SFS\Client;
class OrderPeer extends BackendBase
{
    public function executeDefault(){

    }


    public function executeAddComment () {
        $this->validAjaxRequest();
        $user = \BackendAuth::getInstance()->getUser();
        $content = $this->post('content');
        $orderId = $this->post('orderId');

        $order  = \Order::retrieveById($orderId);
        if($content == '') {
            $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Chưa điền nội dung comment');
            return $this->renderText($ajax->toString());
        }
        if(!$order) {
            $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Đơn hàng không tồn tại');
            return $this->renderText($ajax->toString());
        }
        $check = \OrderPeer::addOrderComment($order, $user, $content, \OrderComment::TYPE_INTERNAL);
        if ($check === true) {
            $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'SUCCESS');
            return $this->renderText($ajax->toString());
        }
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'FAIL');
        return $this->renderText($ajax->toString());
    }

    public function executeChangeQuantity () {

    }

    public function executeCountOrder(){
        $status = $this->post('orderId');
        $query = \Order::read();
        $total = \OrderPeer::countOrder($query->andWhere('status="'.$status.'"'));
        return $this->renderPartial();
    }


}
