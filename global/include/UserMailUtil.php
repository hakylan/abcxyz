<?php
use SeuDo\Queue;
class UserMailUtil extends \MailHelper {

    public static function pushVerifyEmail($username, $email, $link, $fullname){
        if(!$username || !$email|| !$link || !$fullname){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/Verify';
        $subject = 'Yêu cầu kích hoạt địa chỉ email';
        $data = array(
            'email' => $email,
            'subject' => $subject,
            'template' => $template,
            'params' => array(
                'fullname' => $fullname,
                'link' => $link,
                'username' => $username,
                'email' => $email
            )
        );
        if(Queue::emailVerify()->push(json_encode($data))){
            return true;
        }
        return false;
    }

    public static function pushNotificationEmail($email,$message_content){
        if(!$email){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/Notification';
        $subject = 'Thông báo từ Sếu Đỏ ngày '.date('d-m-Y');
        $data = array(
            'email' => $email,
            'subject' => $subject,
            'template' => $template,
            'body' => $message_content,
            'params' => array(
                'message' => $message_content
             )
        );
        if(Queue::emailNotification()->push(json_encode($data))){
            return true;
        }
        return false;
    }

    public static function sendWelcomeEmail($username, $email, $link, $fullname){
        if(!$username || !$email|| !$link || !$fullname){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/WelcomeEmail';
        $subject = 'Chúc mừng, bạn đã là thành viên trên hệ thống của Seudo.vn!';
        $params = array(
        'fullname' => $fullname,
        'link' => $link,
        'username' => $username,
        'email' => $email
        );
        $sendMail= \MailHelper::mailHelperWithBody($template,$params);
        $sendMail->setReciver($email);
        $sendMail->setSubject($subject);
        $checkSend = $sendMail->sendMail();
        if($checkSend >0)return true;
        return false;
    }


    public static function sendRequestresetpassword($username, $email, $link, $fullname){
        if(!$username || !$email|| !$link || !$fullname){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/Requestresetpassword';
        $subject = 'Xác nhận yêu cầu lấy lại mật khẩu';
        $params = array(
            'fullname' => $fullname,
            'link' => $link,
            'username' => $username,
            'email' => $email
        );
        $sendMail= \MailHelper::mailHelperWithBody($template,$params);
        $sendMail->setReciver($email);
        $sendMail->setSubject($subject);
        $checkSend = $sendMail->sendMail();
        if($checkSend >0)return true;
        return false;
    }

    public static function sendUsersGM($username, $email, $link, $fullname){
        if(!$username || !$email|| !$link || !$fullname){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/sendUsersGM';
        $subject = 'Xác nhận yêu cầu lấy lại mật khẩu';
        $params = array(
            'fullname' => $fullname,
            'link' => $link,
            'username' => $username,
            'email' => $email
        );
        $sendMail= \MailHelper::mailHelperWithBody($template,$params);
        $sendMail->setReciver($email);
        $sendMail->setSubject($subject);
        $checkSend = $sendMail->sendMail();
        if($checkSend >0)return true;
        return false;
    }

    public static function sendUsersEmail( $subject = '', $email = '', $params = array() ){
        if($email == ''){
            return false;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/sendUsersEmail';
        $sendMail= \MailHelper::mailHelperWithBody($template,$params);
        $sendMail->setReciver($email);
        $sendMail->setSubject($subject);
        $checkSend = $sendMail->sendMail();
        if($checkSend >0)return true;
        return false;
    }

}