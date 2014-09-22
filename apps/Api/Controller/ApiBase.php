<?php
namespace Api\Controller;
use FlyApi\Request;
use Flywheel\Controller\Api;
use SeuDo\Api\DataStore;
use SeuDo\Api\Server;

abstract class ApiBase extends Api {

    protected $_consumer;

    protected function _verifyRequest() {
        $server = new Server(new DataStore());

        if ('PUT' == $_SERVER['REQUEST_METHOD'] || 'DELETE' == $_SERVER['REQUEST_METHOD']) {
            $params = $this->request()->getRestParams();
            $request = Request::fromRequest(null, null, $params);
        } else {
            $request = Request::fromRequest();
        }

        $result = $server->verifyRequest($request);
        $this->_consumer = \Consumer::retrieveByConsumerKey($result[0]->key);
    }

    public function beforeExecute() {
        $this->_verifyRequest();
    }
}
