<?php

namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use Flywheel\Exception;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;

class OrderItemComment extends BackendBase
{
    private $user;
    private $per_page_comment_item = 3;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault() {
//        $order_id = 1;
//        $item_id = 2;
//        $comments = \OrderItemComment::loadOrderItemComments($order_id, $item_id);
//        $this->setView('OrderItemComment/default');
//        $this->view()->assign('comments', $comments);

//        return $this->renderComponent();
    }

    public function executeListComments()
    {
        $this->validAjaxRequest();

        $order_id = $this->request()->get('order_id', 'INT', 0);
        $item_id = $this->request()->get('item_id', 'INT', 0);

        if ($order_id > 0 && $item_id > 0) {
            // default show chat infor external
            $comments = \OrderItemComment::loadOrderItemComments($order_id, $item_id);
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->order_id = $order_id;
            $ajax->item_id = $item_id;
            $ajax->format = 'JSON';
            $ajax->comments = $comments;

            return $this->renderText($ajax->toString());
        }
        return $this->renderText(0);
    }

    public function executeGetAllOrderItemComments(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';
        try{
            $order_id = $this->request()->get('order_id', 'INT', 0);
            $page_size = $this->request()->get('page_size', 'INT', $this->per_page_comment_item);
            if($order_id == 0){
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Không tồn tại đơn hàng!';

                return $this->renderText($ajax->toString());
            }

            $comments = array();

            //get all items
            $items = \OrderItem::findByOrderId($order_id);

            if(sizeof($items) > 0){
                foreach($items as $key => $item){
                    if($item instanceof \OrderItem){
                        $item_id = (int)$item->getId();
                        $comments[$key]['order_id'] = $order_id;
                        $comments[$key]['item_id'] = $item_id;
                        $comments[$key]['comment'] = \OrderItemComment::loadOrderItemComments($order_id, $item_id, 1, $page_size, '');
                        $comments[$key]['comment']['page_next'] = $comments[$key]['comment']['page'] + 1;
                        $comments[$key]['comment']['info'] = $comments[$key]['comment']['data'];
                        $comments[$key]['comment']['total_record'] = $comments[$key]['comment']['total_record'];
                        $comments[$key]['comment']['page_size'] = $page_size;
                    }
                }
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->comments = $comments;
            $ajax->message = "OK";
            return $this->renderText($ajax->toString());

        }catch (\Flywheel\Exception $e){
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Vui lòng liên hệ kỹ thuật để được hỗ trợ!';

            return $this->renderText($ajax->toString());
        }
    }

    public function executeListMoreComments()
    {
        $this->validAjaxRequest();

        $order_id = $this->request()->post('order_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);
        $page = $this->request()->post('page', 'INT', 0);
        $page_size = $this->request()->post('page_size', 'INT', $this->per_page_comment_item);
        $type = $this->request()->post('type') ? $this->request()->post('type') : '';

        if ($order_id > 0 && $item_id > 0 && $page > 0 && $page_size > 0) {
            // default show chat infor external
            $results = \OrderItemComment::loadOrderItemComments($order_id, $item_id, $page, $page_size, $type);

            $pages = $results['pages'];
            // Condition load item comments when has page load
            if ($page <= $pages) {
                $page_next = $page + 1;
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->order_id = $order_id;
                $ajax->item_id = $item_id;
                $ajax->page_next = $page_next;
                $ajax->pages = $pages;
                $ajax->total_record = $results['total_record'];
                $ajax->page_size = $page_size;
                $ajax->format = 'JSON';
                $ajax->info = $results['data'];

                return $this->renderText($ajax->toString());
            }
        }
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::ERROR;
//        $ajax->order_id = $order_id;
//        $ajax->item_id = $item_id;
//        $ajax->page = $page;
//        $ajax->page_size = $page_size;
        $ajax->msg = 'Lỗi không lấy được dữ liệu!';

        return $this->renderText($ajax->toString());
    }

    public function executeAddMessage() {
        $this->validAjaxRequest();

        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);
        $type = $this->request()->post('type') ? $this->request()->post('type') : BaseContext::TYPE_CHAT;

        $message = \OrderComment::convertToText($message);
        $ok = false;
        if(strlen($message) > 0 and $order_id > 0 && $item_id > 0) {
            if ($this->user instanceof \Users) {
                $user_id = $this->user->getId();
                $username = $this->user->getFullName();
                $created_time = new \MongoDate();
                $time = date('h:i:s d/m/Y', $created_time->sec);
                $ok = \OrderItemComment::addComment($user_id, $order_id, $item_id, $message, $created_time, $type);
            }
        }
        if($ok){
//                $this->setView('OrderItemComment/box_chat_internal');
            $data = array('user_id' => $user_id, 'username' => $username, 'message' => $message, 'time' => $time);
//                $html = $this->renderPartial($data);

            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->info = $data;
            $ajax->format = 'JSON';

            return $this->renderText($ajax->toString());
        } else {
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->msg = 'Lỗi khi lưu dữ liệu!';
            $ajax->format = 'JSON';

            return $this->renderText($ajax->toString());
        }
    }
}
