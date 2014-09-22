<?php
namespace User\Controller;


use Backend\Controller\OrderComment;
use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use Flywheel\Exception;
use Flywheel\Db\Manager;
use SeuDo\Main;
use SeuDo\Logger;

class OrderCommon extends UserBase
{
    /**
     * @var \Users
     */
    public $user;
    public function __construct(){
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault() {}

    public function executeAddComment(){
        $this->validAjaxRequest();
        $userInstance = \UserAuth::getInstance();

        if($userInstance->isAuthenticated() != true) {
            $response = \AjaxResponse::responseError('Bạn vui lòng đăng nhập lại để thực hiện comment !');
            return $this->renderText($response);
        }

        $userId = $userInstance->getUserId();
        $orderId = $this->post('order_id');
        $comment = $this->post('comment');
        if($comment == '') {
            $response = \AjaxResponse::responseError('Bạn chưa điền nội dung comment !');
            return $this->renderText($response);
        }

        $orderComment = new \OrderComment();
        $orderComment->setOrderId($orderId);
        $orderComment->setContent($comment);
        $orderComment->setType(\mongodb\OrderComment::TYPE_EXTERNAL);
        $orderComment->setCreatedBy($userId);
        $orderComment->setCreatedTime(new DateTime());
        $orderComment->setNew(true);

        $result = $orderComment->save();
        if($result == true) {
            $response = \AjaxResponse::responseSuccess('Thêm ghi chú thành công !');
            return $this->renderText($response);
        }

        $response = \AjaxResponse::responseError('Có lỗi xảy ra khi comment');
        return $this->renderText($response);
    }

    public function executeAddItemNote() {
        $this->validAjaxRequest();
        $userInstance = \UserAuth::getInstance();

        if($userInstance->isAuthenticated() != true) {
            $response = \AjaxResponse::responseError('Bạn vui lòng đăng nhập lại để thực hiện comment !');
            return $this->renderText($response);
        }

        $orderItemId = $this->post('orderItemId');
        $comment = $this->post('comment');

        $orderItem = \OrderItem::retrieveById($orderItemId);

        if(!$comment || $comment == ''){
            $response = \AjaxResponse::responseError('Chưa nhập ghi chú cho sản phẩm!!');
            return $this->renderText($response);
        }

        if(!$orderItem) {
            $response = \AjaxResponse::responseError('Sản phẩm không tồn tại !');
            return $this->renderText($response);
        }

        $orderItem->setNote($comment);
        $orderItem->setNew(false);
        $result = $orderItem->save();

        if($result == true) {
            $response = \AjaxResponse::responseSuccess('Thêm ghi chú thành công !');
            return $this->renderText($response);
        }
        $response = \AjaxResponse::responseError('Có lỗi xảy ra khi thêm ghi chú cho sản phẩm !');
        return $this->renderText($response);
    }

    public function executeCountOrder(){
        $this->validAjaxRequest();
        $userInstance = \UserAuth::getInstance();

        if($userInstance->isAuthenticated() != true) {
            $response = \AjaxResponse::responseError('Bạn vui lòng đăng nhập lại để thực hiện tác vụ này !');
            return $this->renderText($response);
        }

        $userId = $userInstance->getUserId();

        $status = $this->post('status','ARRAY');
        $query = \Order::read();
        $query->andWhere('buyer_id='.$userId);

        $result = \OrderPeer::countOrderByStatus($status, $query);

        $response = \AjaxResponse::responseSuccess('Xóa đơn hàng thành công !', json_encode($result));
        return $this->renderText($response);

    }

    public function executeDelete() {
        $this->validAjaxRequest();
        $orderId = $this->post('orderId');
        $userInstance = \UserAuth::getInstance();

        if($userInstance->isAuthenticated() != true) {
            $response = \AjaxResponse::responseError('Bạn vui lòng đăng nhập lại để thực hiện tác vụ này !');
            return $this->renderText($response);
        }
        $order = \Order::retrieveById($orderId);
        if(!$order && (!$order instanceof \Order)) {
            $response = \AjaxResponse::responseError('Đơn hàng không tồn tại');
            return $this->renderText($response);
        }

        $userId = $userInstance->getUserId();
        if($order->getBuyerId() != $userId) {
            $response = \AjaxResponse::responseError('Bạn không có quyền xóa đơn hàng này !');
            return $this->renderText($response);
        }

        if($order->getStatus() != \Order::STATUS_INIT) {
            $response = \AjaxResponse::responseError('Bạn không thể xóa đơn hàng này !');
            return $this->renderText($response);
        }
        $order->setIsDeleted(1);
        $order->setNew(false);
        $checkUpdate = $order->save();
        if($checkUpdate == true) {
            $response = \AjaxResponse::responseSuccess('Xóa đơn hàng thành công !');
            return $this->renderText($response);
        }

        $response = \AjaxResponse::responseError('Có lỗi xảy ra khi xóa đơn hàng !');
        return $this->renderText($response);
    }

    public function executeChangeQuantity() {
        $this->validAjaxRequest();
        $conn = Manager::getConnection();
        try {
            $conn->beginTransaction();
            $orderItemId = $this->post('orderItemId');
            $quantity = $this->post('quantity');

            if($quantity == 0){
                $response = \AjaxResponse::responseError('Số lượng sản phẩm đặt mua phải lớn hơn 0');
                return $this->renderText($response);
            }
            $orderItem = \OrderItem::retrieveById($orderItemId);
            if(!$orderItem || (!$orderItem instanceof \OrderItem)) {
                $response = \AjaxResponse::responseError('Sản phẩm không tồn tại !');
                return $this->renderText($response);
            }
            $order_id = $orderItem->getOrderId();
            $order = \Order::retrieveById($order_id);

            if($order->getStatus() != \Order::STATUS_INIT) {
                $response = \AjaxResponse::responseError('Bạn không được sửa số lượng khi đã thanh toán đặt cọc !');
                return $this->renderText($response);
            }

            if(($orderItem->getStep()) > $quantity){
                $msg = 'Số lượng tối đặt tối thiểu là '.$orderItem->getStep();
                $response = \AjaxResponse::responseError($msg);
                return $this->renderText($response);
            }

//            if($order->stock < $quantity){
//                $msg = 'Số lượng tối đặt tối đa là '.$order->stock;
//                $response = \AjaxResponse::responseError($msg);
//                return $this->renderText($response);
//            }

            $orderItem->setOrderQuantity($quantity);
            $orderItem->setPendingQuantity($quantity);
            $orderItem->setReciveQuantity($quantity);

            $orderItem->setNew(false);
            $orderItemSave = $orderItem->save();
            if($orderItemSave == true) {
                $result = $order->updateInfo();
                if($result == true) {
                    $conn->commit();
                    $newOrder = \Order::retrieveById($order_id);
                    $data = array(
//                        'item_amount'=>\Common::numberFormat(\GlobalHelper::rounding($orderItem->getPrice() * $orderItem->getOrderQuantity(), 1000)).'<sup>đ</sup>',
                        'item_amount'=>\Common::numberFormat($orderItem->getPrice()*$orderItem->getOrderQuantity(),true).'<sup>đ</sup>',
                        'order_amount'=>\Common::numberFormat($newOrder->getOrderAmount(),true).'<sup>đ</sup>',
                        'order_total_amount' => \Common::numberFormat($newOrder->getTotalAmount(),true).'<sup>đ</sup>',
                        'order_quantity'=>$newOrder->getOrderQuantity(),
                        'deposit_amount'=>\Common::numberFormat(\OrderPeer::calculateDepositAmount($newOrder->getOrderAmount()),true).'<sup>đ</sup>'
                    );
                    $response = \AjaxResponse::responseSuccess('Thay đổi số lượng thành công !',$data);

                    return $this->renderText($response);
                }
                $response = \AjaxResponse::responseError('Có lỗi !');
                return $this->renderText($response);
            }
            $response = \AjaxResponse::responseError('Có lỗi !');
            return $this->renderText($response);

        }catch (Exception $e) {

            $conn->rollBack();
            Logger::factory('order')->addError($e->getMessage());
            $response = \AjaxResponse::responseError($e->getMessage());

            return $this->renderText($response);
        }
    }

    public function executeChooseServices(){
        $order_id = $this->request()->post("order_id","INT",0);
        $services_type = $this->request()->post("services_type");
        $order = \Order::retrieveById($order_id);
        $ajax = new \AjaxResponse();

        try{
            if($order instanceof \Order){
                $result = false;
                $check_mapping = $order->mappingToService($services_type);
                if(!$check_mapping){
                    $result = $order->addService($services_type);
                    if($services_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM && $order->mappingToService(\Services::TYPE_SHIPPING_CHINA_VIETNAM)){
                        $order->removeService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    }elseif($services_type == \Services::TYPE_SHIPPING_CHINA_VIETNAM && $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
                        $order->removeService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
                    }
                    if($result){
                        Logger::factory("front_choose_services")->info("User {$this->user->getUsername()} - {$this->user->getFullName()} choose services {$services_type} success .");
                    }else{
                        Logger::factory("front_choose_services")->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} choose services $services_type not success .Could not map order's mapping service",array($services_type));
                    }
                }
                if($check_mapping){
                    $result = $order->removeService($services_type);

                    if($services_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM && !$order->mappingToService(\Services::TYPE_SHIPPING_CHINA_VIETNAM)){
                        $order->addService(\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    }

                    if($result){
                        Logger::factory("front_choose_services")->info("User {$this->user->getUsername()} - {$this->user->getFullName()} remove services {$services_type} success",array($services_type));
                    }else{
                        Logger::factory("front_choose_services")->addWarning("User {$this->user->getUsername()} - {$this->user->getFullName()} remove services {$services_type} not success",array($services_type));
                    }
                }

                if($result){
                    $this->dispatch('onAfterChooseServiceFront', new Event($this, array(
                        'order' => $order,
                        "service_type" => $services_type
                    )));
                    $deposit_ratio = $order->getDepositRatio();
                    if($deposit_ratio > 0){
                        $total_deposit = $order->getTotalAmount() * $deposit_ratio;
                    }else{
                        $total_deposit = $order->getTotalAmount() * 0.5;
                    }
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = "Thành công";
                    $ajax->fee = \OrderService::getServiceFee($order,$services_type);
                    $ajax->fee_discount = \OrderService::getServiceDiscountFee($order,$services_type);
                    $ajax->fee_shipping_china_vn = \OrderService::getServiceFee($order,\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    $ajax->fee_shipping_china_vn_discount = \OrderService::getServiceDiscountFee($order,\Services::TYPE_SHIPPING_CHINA_VIETNAM);
                    $ajax->total_amount = $order->getTotalAmount();
                    $ajax->deposit_amount = $total_deposit;
                }else{
                    $ajax->type = \AjaxResponse::ERROR;


                    $ajax->message = "Lỗi hệ thống hoặc đã quá hạn chọn dịch vụ. Liên hệ bộ phận CSKH đề được hỗ trợ";
                }
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không tồn tại đơn hàng";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            Logger::factory("front_choose_services")
                ->addWarning("User {$this->user->getUsername()} - {$this->user->getFullName()} choose services {$services_type} not success with order code : {$order->getCode()} - STATUS:{$order->getStatus()} - WAREHOUSE: {$order->getCurrentWarehouse()} - WAREHOUSE_STATUS: {$order->getWarehouseStatus()} ".$e->getMessage(),array($services_type));
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Lỗi hệ thống hoặc đã quá hạn chọn dịch vụ. Liên hệ bộ phận CSKH đề được hỗ trợ ".$e->getMessage();
            return $this->renderText($ajax->toString());
        }
    }


    public function executeDeleteQuantity() {

    }

}