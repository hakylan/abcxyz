<?php
namespace User\Controller;


use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use mongodb\NotificationResource\Notification;
use SeuDo\Main;

class OrderDetail extends UserBase
{

    private $authen = null;
    public function executeDefault() {
        $this->setLayout('order_detail');
        $user_id = \UserAuth::getInstance()->getUserId();
        $orderId = $this->get('id');
        $notify_id = $this->get('notify_id');
        $notify = new Notification();
        if($notify->checkNotifyById($notify_id)){
            $notify->setReadNotifyById($notify_id);
        }

        $order = \Order::retrieveById($orderId);

        $this->authen = \UserAuth::getInstance();
        $user_id = $this->authen->getUserId();
        $orderAddress = \UserAddress::retrieveById($order->getUserAddressId());
        $this->assign('orderAddress', $orderAddress);

        $order_services = \OrderService::buildOrderServicesArray($order);

        $user_address = \UserAddress::getUserAddresses($user_id);
        $this->view()->assign('user_address',$user_address);
        /**/
        $this->assign('orderServices', $order_services);
        /* buyer */
        $buyer = \Users::retrieveById($order->getBuyerId());
        $this->assign('buyer', $buyer);
        /* services */
        $services = \OrderPeer::getOrderServices($order);
        $this->assign('services',$services);
        /**/
        $status = \Order::$statusLevel;
        $stateClass =  array();
        for($i = 0;$i<sizeof($status);$i++){
            if($status[$i] != $order->getStatus()){
                $stateClass[$status[$i]] = 'finish';
            }else if($status[$i] == $order->getStatus()){
                $stateClass[$status[$i]] = 'active';
                break;
            }
        }
        $this->assign('stateClass',$stateClass);
        $this->assign('order', $order);
        $this->setView('OrderDetail/default');
        return $this->renderComponent();
    }

    public function executeLoadItem() {

        $this->validAjaxRequest();

        $this->setLayout('order_detail');

        $this->setView('OrderDetail/item');
        $orderId = $this->post('orderId');

        $orderItems = \OrderPeer::getOrderItem($orderId);

        $this->assign('root', Main::getHomeUrl());
        $this->assign('orderItems',$orderItems);
        $this->assign('order',\Order::retrieveById($orderId));

        return $this->renderPartial();
    }

    public function executeLoadComment() {
        $this->validAjaxRequest();

        $currentUser = \UserAuth::getInstance()->getUser();

        $this->setLayout('order_detail');
        $this->setView('OrderDetail/comment');
        $orderId = $this->post('orderId');

        $orderComments = \OrderPeer::getOrderComment($orderId);

        $this->assign('orderComments',$orderComments);
        $this->assign('orderId',$orderId);
        $this->assign('currentUser',$currentUser);

        return $this->renderPartial();
    }

    public function executeSetUserAddress(){
        $ajax = new \AjaxResponse();
        if($this->request()->isPostRequest()){
            $this->validAjaxRequest();
            $idAddress = $this->request()->post('idAddress');
            $idOrder = $this->request()->post('idOder');

            if(!$idAddress || !$idOrder){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Obj order or address not found';
                return $this->renderText($ajax->toString());
            }
            $order = \Order::findOneById($idOrder);

            if(!$order || !($order instanceof \Order)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Obj not Order';
                return $this->renderText($ajax->toString());
            }

            $order->setUserAddressId($idAddress);
            if($order->save()){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->element = 'error';
                $ajax->message = 'Lưu đ?a ch? đơn hàng thành công';
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error';
            $ajax->message = 'Không th? lưu đ?a ch? đơn hàng!';
            return $this->renderText($ajax->toString());
        }
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->element = 'error';
        $ajax->message = 'error!';
        return $this->renderText($ajax->toString());
    }

    public function executeBuyerConfirm(){
        if($this->request()->isPostRequest()){
            $this->validAjaxRequest();
            $idOrder = $this->request()->post('id');
            $status = $this->request()->post('status');
            if($status!=\Order::CUSTOMER_CONFIRM_WAIT){
                $ajax= \AjaxResponse::responseError('Đơn hàng chỉ có thể xác nhận trong trạng thái chờ xác nhận');
                return $this->renderText($ajax);
            }
            $order = \Order::retrieveById($idOrder);
            if(!$order || (!$order instanceof \Order)){
                $ajax= \AjaxResponse::responseError('Không tìm thấy đơn hàng để xác nhận');
                return $this->renderText($ajax);
            }else{
                if($order->getCustomerConfirm()!=\Order::CUSTOMER_CONFIRM_WAIT){
                    $ajax= \AjaxResponse::responseError('Đơn hàng chỉ có thể xác nhận trong trạng thái chờ xác nhận');
                    return $this->renderText($ajax);
                }else{
                    $order->setNew(false);
                    $order->setCustomerConfirm(\Order::CUSTOMER_CONFIRM_NONE);
                    $order->setConfirmApprovalTime(new DateTime());
                    if($order->save()){
                        $this->dispatch("afterCustomerConfirmed",new Event($this,array(
                            "order" => $order
                        )));
                        $ajax= \AjaxResponse::responseSuccess('Success');
                        return $this->renderText($ajax);
                    }else{
                        $ajax= \AjaxResponse::responseError('Không thể xác nhận, vui lòng liên hệ chăm sóc khách hàng để được giúp đỡ hoặc sử dụng công cụ chát trực tuyến trên trang!');
                        return $this->renderText($ajax);
                    }
                }
            }
        }
        $ajax= \AjaxResponse::responseError('Error');
        return $this->renderText($ajax);
    }

}