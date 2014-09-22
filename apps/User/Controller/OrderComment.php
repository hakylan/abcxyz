<?php
namespace User\Controller;

use Flywheel\Exception;
use SeuDo\Main;
use \mongodb\OrderCommentResource\BaseContext;
use \mongodb\OrderCommentResource\Chat;
use \mongodb\OrderCommentResource\Activity;
use \mongodb\OrderCommentResource\Log;

class OrderComment extends UserBase
{
    protected  $user;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
    }
    function executeDefault() {
    }

    public function executeListOrderComments()
    {
//        $this->validAjaxRequest();
        $order_id = $this->request()->get('order_id', 'INT', 0);
        $type = $this->request()->get('type');
        $order = \Order::retrieveById($order_id);
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';
        if ($order instanceof \Order) {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->order_id = $order_id;
            if ($type == \mongodb\OrderComment::TYPE_EXTERNAL) {
                $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
                $ajax->external_comments = $external_comments;
            } elseif ($type == \mongodb\OrderComment::TYPE_INTERNAL) {
                $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_CHAT);
                $ajax->internal_comments = $internal_comments;
            } else {
                $external_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
                $ajax->external_comments = $external_comments;
                $internal_comments = \OrderComment::loadOrderComments($order_id, \mongodb\OrderComment::TYPE_INTERNAL, BaseContext::TYPE_CHAT);
                $ajax->internal_comments = $internal_comments;
            }
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

    public function executeAddMessage() {
        $this->validAjaxRequest();
        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $type = $this->request()->post('type');

//        $message = $this->request()->get('message');
//        $order_id = $this->request()->get('order_id', 'INT', 0);
//        $type = $this->request()->get('type');

        // Check chat channel
        if ($type==\mongodb\OrderComment::TYPE_EXTERNAL) { // external
        }
        $username = \OrderComment::USER_SYSTEM;
        $is_public_profile = true;
        // dau's hard code
        $img_path = \Users::getAvatar32x($this->user);
        $time = "";
        $message = \OrderComment::convertToText($message);
        $ok = false;
        if(strlen($message) > 0 and $order_id > 0) {
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
        }
        if($ok){
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
}