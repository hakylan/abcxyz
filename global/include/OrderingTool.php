<?php
use SeuDo\Queue;
class OrderingTool {

    public static function sendMailError($link_origin, $tool , $reason = "",$user = ""){
        $array_email = array(
            "tronghieu.luu@gmail.com",
            "quyenminh.102@gmail.com"
        );
        $user_name = $user instanceof \Users ? $user->getUsername() : $user;
        $subject = "{$tool} Không đặt được hàng link {$link_origin} ".date("H:i:s d/m/y");
        $body = "Không đặt được hàng qua {$tool}<br/>Lý do : {$reason}<br/>
        Link đặt hàng : {$link_origin}<br/>
        User: {$user_name}<br/>
        IP: ".Common::getClientIp();
        $params = $body;
        foreach ($array_email as $email) {
            $sendMail= \MailHelper::mailHelperWithBody("",$params);
            $sendMail->setReciver($email);
            $sendMail->setSubject($subject);
            $sendMail->sendMail();
        }
//        foreach ($array_email as $email) {
//            $sendMail= \MailHelper::mailHelperWithBody("",$params);
//            $sendMail->setReciver($email);
//            $sendMail->setSubject($subject);
//            $sendMail->sendMail();
//        }
    }
}