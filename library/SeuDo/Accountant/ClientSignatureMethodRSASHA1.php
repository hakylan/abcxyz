<?php
namespace SeuDo\Accountant;


use FlyApi\SignatureMethod\RSASHA1;

class ClientSignatureMethodRSASHA1 extends RSASHA1 {
    protected function _fetchPublicCert(&$request) {}

    protected function _fetchPrivateCert(&$request) {
        //import private key
    }
} 