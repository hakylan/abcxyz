<?php

namespace SeuDo\SMS\Transporters;

use Flywheel\Config\ConfigHandler;
use SeuDo\Logger;
use Zend\Soap\Client;

class VHT implements ITransporter {
    public $account;
    public $code;
    public $service_url;
    private $_client;
    private $_providerName = 'VHT';

    protected static $_responseCode = array(
        -3 => 'Out of quota',
        -4 => 'Not enough params',
        -5 => 'Recipient invalid',
        -6 => 'Message is null',
        -7 => 'BrandName is null',
        -8 => 'Ip address Invalid',
        -9 => 'BrandName not register',
        -10 => 'Recipient not receiver SMS message',
        -11 => 'User postpaid',
        -12 => 'BrandName has existed in system',
        -13 => 'Ip has existed in system',
        -14 => 'Out of length message',
        -15 => 'Not support telcos',
        -17 => 'Authentication faild',
        -20 => 'Brandname existed',
        -21 => 'Out of limit recipient' //(500 recipients) (use for sendSmsToListPhone)
    );

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

    /**
     * Get Soap Client
     *
     * @return Client
     */
    public function getSoapClient() {
        if (!$this->_client) {
            $this->_client = new Client($this->service_url);
        }
        return $this->_client;
    }

    /**
     * Send sms
     *
     * @param $from
     * @param $to
     * @param $body
     * @param array $options
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendSms($from, $to, $body, $options = array()) {
        // TODO: Implement sendSms() method.
        $result = $this->sendTextSms($this->code, $this->account, $to, $from, $body);
        $this->log('VHT', $from, $to, $body, $options, $result);

        if (!$result->return && isset(self::$_responseCode[$result])) {
            throw new \RuntimeException("SMS could not send" .self::$_responseCode[$result]);
        }

        return $result->return;
    }

    /**
     * Send text message via webservice
     * @param $code
     * @param $account
     * @param $phone
     * @param $from
     * @param $sms
     * @return mixed
     */
    public function sendTextSms($code, $account, $phone, $from, $sms) {
        $client = $this->getSoapClient();
        return $client->sendSms(array(
            'code' => $code,
            'account' => $account,
            'phone' => $phone,
            'from' => $from,
            'sms' => $sms
        ));
    }

    public function sendTextSmsDeliverReport($code, $account, $phone, $from, $sms, $partner_id) {}

    public function getBalance($account, $code) {}

    /**
     * Logging
     *
     * @param $provider
     * @param $from
     * @param $to
     * @param $body
     * @param $options
     * @param $result
     */
    public function log($provider, $from, $to, $body, $options, $result) {
        $data = array(
            'sms'   => $body,
            'brandname' => $from,
            'provider'=> $provider,
            'receive'=> $to,
            'type' => 'BRANDNAME',
            'status' => 'SENT',
            'deliver' => 'UNKNOWN',
            'lenght' => mb_strlen($body),
            'return' => $result,
            'options' => $options,
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