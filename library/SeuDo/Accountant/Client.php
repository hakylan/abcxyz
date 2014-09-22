<?php
namespace SeuDo\Accountant;


use FlyApi\Request;
use FlyApi\SignatureMethod\HMACSHA1;
use Flywheel\Config\ConfigHandler;
use Flywheel\Object;

class Client extends Object {
    protected static $_instance;

    protected $_apiKey;
    protected $_apiSecret;
    protected $_apiUrl;
    protected $_httpCode;
    protected $_paramString;
    protected $_parameters;
    protected $_rawResponse;
    protected $_response;
    protected $_requestUrl;
    protected $_connectTimeout;
    protected $_timeout = 30;
    protected $_userAgent;
    protected $_sslVerifyPeer;
    protected $_httpHeader = array();

    public function __construct($apiKey, $apiSecret, $apiUrl) {
        $this->_apiKey = $apiKey;
        $this->_apiSecret = $apiSecret;
        $this->_apiUrl = $apiUrl;
    }

    /**
     * @return Client
     */
    public static function getClient() {
        $config = ConfigHandler::get('accountant');
        $client = new self($config['key'], $config['secret'], $config['url']);
        return $client;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * @return mixed
     */
    public function getApiSecret() {
        return $this->_apiSecret;
    }

    /**
     * @return mixed
     */
    public function getApiUrl() {
        return $this->_apiUrl;
    }

    public function getHttpCode() {
        return $this->_httpCode;
    }

    /**
     * @return mixed
     */
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->_requestUrl;
    }

    /**
     * @param mixed $connectTimeout
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->_connectTimeout = $connectTimeout;
    }

    /**
     * @return mixed
     */
    public function getConnectTimeout()
    {
        return $this->_connectTimeout;
    }

    /**
     * @param mixed $sslVerifyPeer
     */
    public function setSslVerifyPeer($sslVerifyPeer)
    {
        $this->_sslVerifyPeer = $sslVerifyPeer;
    }

    /**
     * @return mixed
     */
    public function getSslVerifyPeer()
    {
        return $this->_sslVerifyPeer;
    }

    /**
     * @param mixed $timeout
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }



    public function get($url, $params = array('format' => 'json'))
    {
        return $this->httpRequest($url, 'GET', $params);
    }

    public function post($url, $params = null)
    {
        return $this->httpRequest($url, 'POST', $params);
    }

    public function delete($url, $params = array('format' => 'json'))
    {
        return $this->httpRequest($url, 'DELETE', $params);
    }

    public function put($url, $params = array('format' => 'json'))
    {
        return $this->httpRequest($url, 'PUT', $params);
    }

    protected function _convertParams($params) {
        return http_build_query($params);
    }

    /**
     * @param $url
     * @param string $method
     * @param null $parameters
     * @return mixed
     */
    public function httpRequest($url, $method = "GET", $parameters = null) {
        if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
            $url = "{$this->_apiUrl}{$url}";
        }

        $consumer = new Consumer($this->_apiKey, $this->_apiSecret);
        $request = Request::fromConsumer($consumer, $method, $url, $parameters);
        $request->signRequest(new HMACSHA1(), $consumer);

        $this->_parameters = $request->getParameters();

        switch($method) {
            case "GET":
                $this->_requestUrl = $request->toUrl();
                return $this->http($request->toUrl(), 'GET');
            default:
                $this->_requestUrl = $request->getNormalizedHttpUrl();
                return $this->http($request->getNormalizedHttpUrl(), $method, $request->toPostData());
        }
    }

    public function http($url, $method, $parameters= null) {
        $this->_paramString = $parameters;
        $ci = curl_init();
        /* Curl settings */

        curl_setopt($ci, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->_connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->_sslVerifyPeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'retrieveHeader'));
        curl_setopt($ci, CURLOPT_HEADER, false);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($parameters)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
                }
                break;
            case 'DELETE':
                if (!empty($parameters)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
                }
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "PUT");
                if (!empty($parameters)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
                }
                break;
            case 'GET':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "GET");
                if (!empty($parameters)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
                }
                break;
        }


        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        $error = curl_errno($ci);
        $errorMess = curl_error($ci);
        $this->_httpCode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        curl_close($ci);
        if ($error) {
            throw new Exception("CURL Error: {$errorMess}[{$error}]");
        }
        $this->_rawResponse = $response;
        $this->_response = json_decode($response, true);
        return $this->_response;
    }

    function retrieveHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->_httpHeader[$key] = $value;
        }
        return strlen($header);
    }
}