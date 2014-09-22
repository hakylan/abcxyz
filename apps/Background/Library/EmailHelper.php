<?php
namespace Background\Library;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use SeuDo\Logger;

class EmailHelper {
    public static function sendEmailVertify ($data) {
        try {
            $mail = new \MailHelper();
            $mailSent = $data['email'];
            if(isset($data['email'])) {
                $mail->setReciver($data['email']);
            }
            if(isset($data['subject'])) {
                $mail->setSubject($data['subject']);
            }

            if(isset($data['template']) && isset($data['params'])) {
                $mail->parseData();
            }else{
                if(isset($data['body'])) $mail->setBody($data['body']);
            }

            if(isset($data['replyTo'])) {
                $mail->setReplyTo($data['replyTo']);
            }

            if(isset($data['replyName'])) {
                $mail->setReplyName($data['replyName']);
            }
            $mail->send();
            return $mailSent;

        } catch(\Swift_SwiftException $se) {
            Logger::factory('email')->addAlert('Exception',array($se));
        }
        return false;
    }

    public static function sendVerifyEmail($data){
        if(!$data || !is_array($data)){
            return false;
        }
        $template = $data['template'];
        $subject = $data['subject'];
        $params = $data['params'];
        $sendMail= \MailHelper::mailHelperWithBody($template,$params);
        $sendMail->setReciver($params['email']);
        $sendMail->setSubject($subject);
        $checkSend = $sendMail->sendMail();
        if($checkSend >0)return true;
        return false;
    }

    public static function sendEmailError($data) {
        try{
            return self::sendEmail($data);
        }catch (\Exception $e){
        }
    }

    public static function sendEmail($data) {
        if(is_object($data)) $data = json_encode($data);
        try {
            $mail = new \MailHelper();
            $mailSent = $data['email'];
            if(isset($data['email'])) {
                $mail->setReciver($data['email']);
            }
            if(isset($data['subject'])) {
                $mail->setSubject($data['subject']);
            }

            if(isset($data['body'])) $mail->setBody($data['body']);

            if(isset($data['replyTo'])) {
                $mail->setReplyTo($data['replyTo']);
            }

            if(isset($data['replyName'])) {
                $mail->setReplyName($data['replyName']);
            }
            $mail->send();
            return $mailSent;

        } catch(\Swift_SwiftException $se) { }
        catch(\Exception $e){

        }
        return false;
    }

    public static function valid($data) {
        if(is_object($data)) $data = json_encode($data);

        if(is_string($data['email'])){
            $checkBanned = \EmailBanned::retrieveByEmail($data['email']);

            if( $checkBanned && !empty($checkBanned) && ($checkBanned instanceof \EmailBanned) ) {
                return false;
            }

            return true;
        }

        return false;


    }
}
