<?php

namespace SeuDo\Notification\Transporters;


class Email implements ITransporter {

    private $_providerName = 'Amazon';



    public function __construct($params) {
        foreach($params as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @return string
     */
    public function getProviderName() {
        return $this->_providerName;
    }


    public function sendNotification($from, $to, $body) {
        include_once GLOBAL_INCLUDE_PATH . '/UserMailUtil.php';
        $userMailUtil = new \UserMailUtil();
        $userMailUtil->pushNotificationEmail( $to, $body );

    }

}