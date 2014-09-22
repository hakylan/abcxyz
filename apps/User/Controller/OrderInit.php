<?php
namespace User\Controller;

use Flywheel\Exception;
use SeuDo\Main;

class OrderInit extends UserBase
{
    private $user = null;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault(){

        $status_init = \Order::STATUS_INIT;
        $query = \Order::read();
        $query->andWhere("status = '{$status_init}'")->andWhere("buyer_id = {$this->user->getId()}");
        $order_list = \OrderPeer::getOrder($query);
        $this->assign('order_list',$order_list);
        $this->assign('currentUser',$this->user);
        $document = $this->document();
        $document->title = "Đơn hàng chờ thanh toán";
        $document->addJsVar("OrderInitUrl",Main::getUserRouter()->createUrl("OrderInit"));
        // dau's not use
        $document->addJsVar("order_common",Main::getUserRouter()->createUrl("order_common"));
        // dau's hard code
        $document->addJsVar("linkAddOrderComment", Main::getHomeRouter()->createUrl('user/OrderComment/AddMessage'));
        $document->addJsVar("linkLoadOrderComments", Main::getHomeRouter()->createUrl('user/OrderComment/ListOrderComments'));

        $document->addJsVar("ServicesCalc",Main::getHomeRouter()->createUrl("service/calc"));
        $this->setView('OrderInit/default');
        $this->setLayout("order_init");
        return $this->renderComponent();
    }

    public function executeLoadOrderItem(){
        $this->validAjaxRequest();

        $order_id = $this->request()->get("order_id","INT",0);

        $order_items = \OrderPeer::getOrderItem($order_id);

        $this->assign("order_items",$order_items);
        $this->assign("order_id",$order_id);

        $this->setView("OrderInit/item");

        return $this->renderPartial();
    }

    public  function executeLoadOneOrderItem(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $order_item_id = (int)$this->get('order_item_id');

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "";

            //item info
            $item = \OrderItem::retrieveById($order_item_id);

            $order_quantity = (float)$item->getOrderQuantity();
            $price = (float)$item->getPrice();
            $price_cny = (float)$item->getPriceCny();
            $amount = $order_quantity * $price;
            $order_id = $item->getOrderId();
            $id = $item->getId();

            $item = $item->toArray();
            $item['amount'] = $amount;
            $item['amount_format'] = \Common::numberFormat($amount);
            $item['price_format'] = \Common::numberFormat($price);
            $item['price_cny_format'] = \Common::numberFormat($price_cny);
            $item['order_quantity_format'] = \Common::numberFormat($order_quantity);

            $ajax->item = $item;

            $complaint = \Complaints::getOneComplaint($order_id, $id);
            $ajax->complaint_id = (int)$complaint['id'];

            //order info
            $order = \Order::retrieveById($order_id)->toArray();
            $order['user_address'] = \UserAddress::retrieveById($order['user_address_id'])->toArray();
            $ajax->order = $order;

            return $this->renderText($ajax->toString());
        }catch (Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Có lỗi xảy ra, xin thử lại";
            $ajax->element = '';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeLoadOrderComment(){
        $this->validAjaxRequest();

        $this->setLayout('order_detail');
        $this->setView('OrderInit/comment');
        $orderId = $this->get('order_id');

        $order_comment = \OrderPeer::getOrderComment($orderId);

        $this->assign('orderComments',$order_comment);
        $this->assign('orderId',$orderId);
        $this->assign('currentUser',$this->user);

        return $this->renderPartial();
    }

    public function executeOrderDeposit(){
        $this->validAjaxRequest();
        $order_ids = $this->request()->request("order_ids", "STRING", '');

        if($order_ids == ''){
            $response = array();
            $response['type'] = 0;
            $response['message'] = "Bạn chưa chọn đơn hàng để đặt cọc";
            $response['url'] = "";
            return $this->renderText(json_encode($response));
        }
        $url_deposit = Main::getHomeRouter()->createUrl('OrderDeposit', array('cid' => base64_encode($order_ids)));

        $response = array(
            "type" => 1,
            "message" => "Thành công",
            "url" => $url_deposit
        );

        return $this->renderText(json_encode($response));
    }


    public function executeDeleteOrderItem(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $order_item_id = $this->request()->post("order_item_id");
        try{
            if(!($this->user instanceof \Users)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Vui lòng đăng nhập lại để thực hiện tác vụ này";
                $ajax->element = Main::getHomeRouter()->createUrl("Login/default");
                return $this->renderText($ajax->toString());
            }

            $order_item = \OrderItem::retrieveById($order_item_id);
            if(!($order_item instanceof \OrderItem)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Sản phẩm không tồn tại, Vui long nhấn F5 để tải mới trình duyệt";
                $ajax->element = '';
                return $this->renderText($ajax->toString());
            }

            $order = $order_item->getOrder();

            if(!($order instanceof \Order)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng không tồn tại, Vui long nhấn F5 để tải mới trình duyệt";
                $ajax->element = '';
                return $this->renderText($ajax->toString());
            }

            if($this->user->getId() != $order->getBuyerId()){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Đơn hàng này không phải của bạn. Xin thử lại";
                $ajax->element = '';
                return $this->renderText($ajax->toString());
            }

            $result  = $order_item->delete();

            if($result){
                $item_order = $order->getItemInOrder();

                if(empty($item_order)){
                    $ajax->delete_order = 1;
                }else{
                    $ajax->delete_order = 0;
                    $order->updateInfo();
                }
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Xóa sản phẩm thành công.";
                return $this->renderText($ajax->toString());
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Có lỗi xảy ra, xin thử lại";
                $ajax->element = '';
                return $this->renderText($ajax->toString());
            }
        }catch (Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Có lỗi xảy ra, xin thử lại";
            $ajax->element = '';
            return $this->renderText($ajax->toString());
        }

    }
}