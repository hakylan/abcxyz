<?php
namespace SeuDo\Notification\Transporters;

interface ITransporter {
    public function getProviderName();
    public function sendNotification($from, $to, $body);
} 