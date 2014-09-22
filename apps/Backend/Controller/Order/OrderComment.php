<?php

namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use Flywheel\Event\Event;
use Flywheel\Exception;
use Flywheel\Factory;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use SeuDo\Main;

class OrderComment extends BackendBase
{
    private $user;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();

//        header("Cache-Control: no-store, no-cache, must-revalidate");
//        header("Cache-Control: post-check=0, pre-check=0", false);
//        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

//        Factory::getResponse()->setHeader('no-store', "Cache-Control: no-store, no-cache, must-revalidate");
//        Factory::getResponse()->setHeader('check', "Cache-Control: post-check=0, pre-check=0", false);
//        Factory::getResponse()->setHeader('last-modified', "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    }

    public function executeDefault() {
        // default show chat infor external
        $order_id = intval($this->request()->get('order_id', 'INT', 0));
        $external_comments = $internal_comments = array();
        if($order_id > 0){
            $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
            $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_CHAT);
        }
        $this->setView('OrderComment/box_chat_external_internal');
        $this->view()->assign('external_comments', $external_comments);
        $this->view()->assign('internal_comments', $internal_comments);

        return $this->renderComponent();
    }

    public function executeListOrderComments()
    {
        $this->validAjaxRequest();

        $order_id = $this->request()->post('order_id', 'INT', 0);
        $type = $this->request()->post('type', 'STRING', '');
        $context = $this->request()->post('context', 'STRING', '');

        $order = \Order::retrieveById($order_id);
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';
        if ($order instanceof \Order) {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->order_id = $order_id;
            $external_comments = $internal_comments = array();
            //EXTERNAL
            if ($type == \mongodb\OrderComment::TYPE_EXTERNAL || $type == '') {
                switch ($context){
                    case '':
                        $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, '');
                        break;
                    case BaseContext::TYPE_CHAT:
                        $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
                        break;
                    case BaseContext::TYPE_ACTIVITY:
                        $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_ACTIVITY);
                        break;
                    case $context == BaseContext::TYPE_LOG:
                        $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_LOG);
                        break;
                    default:
                        $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
                        break;
                }
            }

            //INTERNAL
            if ($type == \mongodb\OrderComment::TYPE_INTERNAL || $type == '') {
                switch ($context){
                    case '':
                        $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, '');
                        break;
                    case BaseContext::TYPE_CHAT:
                        $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_CHAT);
                        break;
                    case BaseContext::TYPE_ACTIVITY:
                        $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_ACTIVITY);
                        break;
                    case $context == BaseContext::TYPE_LOG:
                        $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_LOG);
                        break;
                    default:
                        $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_CHAT);
                        break;
                }
            }
            $ajax->external_comments = $external_comments;
            $ajax->internal_comments = $internal_comments;
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Không tồn tại đơn hàng!';
        }
        // default show chat infor external
//        $this->setView('OrderComment/box_chat_external_internal');
//        $this->view()->assign('external_comments', $external_comments);
//        $this->view()->assign('internal_comments', $internal_comments);

        return $this->renderText($ajax->toString());

//        return $this->renderComponent();
    }

    public function  executeLoadBoxChat() {
        $this->validAjaxRequest();

        $order_id = $this->request()->get('order_id', 'INT', 0);
        $type = $this->request()->get('type');
        $type_context = $this->request()->get('type_context');

        $order = \Order::retrieveById($order_id);
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';
        if ($order instanceof \Order) {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->params = array('order_id' => $order_id, 'type' => $type, 'type_context' => $type_context);
            $items = \OrderComment::loadOrderComments($order_id, $type, $type_context);
            $ajax->items = (array)$items;
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Không tồn tại đơn hàng!';
        }

        return $this->renderText($ajax->toString());
    }

    public function executeAddMessage() {
        $this->validAjaxRequest();
        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id');
        $order_id = intval($order_id);
        $type = $this->request()->post('type');
        // Check chat channel
        if ($type==\mongodb\OrderComment::TYPE_EXTERNAL) { // external
            // Check permission
            if (!$this->isAllowed(PERMISSION_COMMUNICATE_CUSTOMER)) {
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->msg = 'Bạn không có quyền chat với khách hàng, liên hệ Admin để được cấp quyền này!';
                $ajax->format = 'JSON';

                return $this->renderText($ajax->toString());
            }
        }
        $username = \OrderComment::USER_SYSTEM;
        $is_public_profile = false;
        // dau's hard code
        $img_path = \Users::getAvatar32x($this->user);
        $time = "";
        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $is_public_profile = true;
        }
        $message = \OrderComment::convertToText($message);
        if(strlen($message) > 0 and $order_id > 0) {
            $ok = false;
            if ($this->user instanceof \Users) {
                $user_id = $this->user->getId();
                if ($is_public_profile) {
                    $username = $this->user->getFullName();
                }
                $context = new Chat($message);
                $created_time = new \MongoDate();
                $time = date('h:i:s d/m/Y', $created_time->sec);
                $type_context = BaseContext::TYPE_CHAT;
                $ok = \OrderComment::addComment($user_id, $order_id, $type, $context, $is_public_profile,
                                                $type_context);
            }
            if($ok){
                $order = \Order::retrieveById($order_id);
                $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
                    'order' => $order,
                    'sender_id'=>$user_id,
                    'message_content'=>$message,
                    'type_chat'=>'human'
                )));
//                $this->setView('OrderComment/item_box_message');
                $info = array('username' => $username, 'message' => $message, 'time' => $time,
                                'user_id' => $user_id, 'img_path' => $img_path);
//                $html = $this->renderPartial($info);
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->type_chat = $type;
                $ajax->info = $info;
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
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->msg = 'Dữ liệu rỗng hoặc không tồn tại đơn hàng!';
        $ajax->format = 'JSON';

        return $this->renderText($ajax->toString());
    }
}
