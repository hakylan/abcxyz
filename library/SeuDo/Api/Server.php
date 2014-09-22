<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2/14/14
 * Time: 5:04 PM
 */

namespace SeuDo\Api;


use FlyApi\SignatureMethod\HMACSHA1;

class Server extends \FlyApi\Server {
    public function __construct($data_store) {
        parent::__construct($data_store);
        $this->addSignatureMethod(new HMACSHA1());
    }
} 