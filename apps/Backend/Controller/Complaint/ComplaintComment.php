<?php
namespace Backend\Controller\Complaint;

use Backend\Controller\BackendBase;
use SeuDo\Main;
use Flywheel\Exception;
use \mongodb\ComplaintCommentResource\BaseContext;
use \mongodb\ComplaintCommentResource\Chat;
use \mongodb\ComplaintCommentResource\Activity;
use \mongodb\ComplaintCommentResource\Log;

class ComplaintComment extends BackendBase{
    protected  $user;

    public function beforeExecute(){
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }
    function executeDefault() {

    }

    public function executeListComplaintComments(){

        $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
        $type = $this->request()->post('type');

        $complaint = \Complaints::retrieveById($complaint_id);
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';
        if ($complaint instanceof \Complaints) {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->complaint_id = $complaint_id;

            if ($type == \mongodb\ComplaintComment::TYPE_EXTERNAL) {
                $external_comments = \ComplaintComment::loadComplaintComments($complaint_id, \mongodb\ComplaintComment::TYPE_EXTERNAL, '');
                $ajax->external_comments = $external_comments;
            } elseif ($type == \mongodb\ComplaintComment::TYPE_INTERNAL) {
                $internal_comments = \ComplaintComment::loadComplaintComments($complaint_id, \mongodb\ComplaintComment::TYPE_INTERNAL, '');
                $ajax->internal_comments = $internal_comments;
            } else {
                $external_comments = \ComplaintComment::loadComplaintComments($complaint_id, \mongodb\ComplaintComment::TYPE_EXTERNAL, '');
                $ajax->external_comments = $external_comments;
                $internal_comments = \ComplaintComment::loadComplaintComments($complaint_id, \mongodb\ComplaintComment::TYPE_INTERNAL, '');
                $ajax->internal_comments = $internal_comments;
            }
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Không tồn tại khiếu nại!';
        }
        return $this->renderText($ajax->toString());
    }

    public function executeAddMessage() {
        $this->validAjaxRequest();
        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);
        $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
        $type = $this->request()->post('type');
        $context = $this->request()->post('context', 'STRING', 'CHAT');

        // Check chat channel
        if ($type==\mongodb\OrderComment::TYPE_EXTERNAL) { // external
            //TODO
        }
        $username = \OrderComment::USER_SYSTEM;
        $is_public_profile = true;

        $img_path = \Users::getAvatar32x($this->user);
        $time = "";
        $message = \ComplaintComment::convertToText($message);
        $ok = false;
        if(strlen($message) > 0 and $order_id > 0) {
            if ($this->user instanceof \Users) {
                $user_id = $this->user->getId();
                if ($is_public_profile) {
                    $username = $this->user->getFullName();
                }
                if($context == 'CHAT'){
                    $context = new Chat($message);
                }
                if($context == 'ACTIVITY'){
                    $context = new Activity($message);
                }
                if($context == 'LOG'){
                    $context = new Log($message);
                }

                $created_time = new \MongoDate();
                $time = date('h:i:s d/m/Y', $created_time->sec);
                $type_context = BaseContext::TYPE_CHAT;
                $ok = \ComplaintComment::addComment($user_id, $order_id, $item_id, $complaint_id, $type, $context, $is_public_profile,
                    $type_context);
            }
        }
        if($ok){
            $info = array('username' => $username, 'message' => $message, 'time' => $time,
                'user_id' => $user_id, 'img_path' => $img_path);
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