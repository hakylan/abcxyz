<?php
namespace SeuDo\SMS\Transporters;

interface ITransporter {
    public function getProviderName();
    public function sendSms($from, $to, $body, $options = array());
} 