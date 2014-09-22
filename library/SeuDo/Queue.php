<?php
namespace SeuDo;

use Flywheel\Queue\Queue as QueueBase;


class Queue extends QueueBase {
    const EMAIL_VERIFY = 'email_verify';
    const EMAIL_ERROR_ALERT = 'email_error';
    const ORDER_PUSH_LOGISTIC = 'order_push_logistic';
    const EMAIL_QUEUE = 'email_queue';
    const TRANSFER_ORDER_LOGISTIC = "transfer_order_logistic";
    const TRANSFER_ORDER_LOGISTIC_3M = "transfer_order_logistic_3m";
    const TRANSFER_ORDER_LOGISTIC_15M = "transfer_order_logistic_15m";

    public static function emailVerify() {
        return self::factory(self::EMAIL_VERIFY);
    }

    public static function emailError() {
        return self::factory(self::EMAIL_ERROR_ALERT);
    }

    public static function emailNotification() {
        return self::factory(self::EMAIL_QUEUE);
    }

    public static function getQueue($key){
        return self::factory($key);
    }
}