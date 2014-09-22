<?php
namespace Api\Controller\Sms;


use Api\Controller\ApiBase;
use Flywheel\Config\ConfigHandler;
use SeuDo\Logger;

abstract class Base extends ApiBase {
    protected $_message;
    protected $_shortCode;
    protected $_phone;
    protected $_time;
    protected $_reply;

    protected function _verifyRequest() {
        $consumer_key = $this->post('consumer_key');
        if (!$consumer_key) {
            throw new \InvalidArgumentException('Missing "consumer_key" parameter', 400);
        }

        $consumer = \Consumer::retrieveByConsumerKey($consumer_key);
        if (!$consumer) {
            throw new \InvalidArgumentException('Consumer not found', 400);
        }

        $secret = $consumer->getConsumerSecret();
        $signature = md5($this->post('message') .$this->post('phone') .$this->post('shortcode') .$this->post('time') .$secret);
        if ($signature != $this->post('signature')) {
            throw new \InvalidArgumentException('Signature not match', 400);
        }

        return true;
    }

    public function beforeExecute() {
        $this->_message = trim($this->post('message'));
        $this->_phone = trim($this->post('phone'));
        $this->_shortCode = trim($this->post('shortcode'));
        $this->_time = $this->post('time');

        register_shutdown_function(array($this, 'log'));
    }

    /**
     * send response
     * @param int $status
     * @param array $body
     * @return array
     */
    public function sendResponse($status = 200, $body = array()) {
        $this->_reply = $body;
        return parent::sendResponse($status, $body);
    }

    public function log() {
        $data = array(
            'sms'   => $this->_message,
            'phone' => $this->_phone,
            'provider'=> 'VHT',
            'receive'=> $this->_shortCode,
            'type' => 'GATEWAY',
            'status' => 'RECEIVED',
            'reply' => $this->_reply,
            'time' => new \MongoDate()
        );
        try {
            $logConfig = ConfigHandler::get('logger');
            if (isset($logConfig['mongo'])) {
                $logConfig['mongo']['database'];
                Logger::getMongoDBConnection()
                    ->selectCollection($logConfig['mongo']['database'], 'sms_log')
                    ->insert($data);
            }
        } catch (\Exception $e) {
            Logger::factory('system')->error('Could not save sms logging.' .$e->getMessage(), $data);
        }

    }
}