<?php

namespace SystemApi;

use FlyApi\SignatureMethodHmacSha1;

class Server extends \FlyApi\Server {
    public function __construct($data_store) {
        /*print_r($data_store);exit;*/
        parent::__construct($data_store);
        $this->addSignatureMethod(new SignatureMethodHmacSha1());
//        $this->addSignatureMethod(new SystemApiSignatureMethod_RSA_SHA1());
    }
}

class DataStore extends \FlyApi\DataStore {
    public function lookupConsumer($consumer_key)
    {
        $consumer = \Consumer::retrieveByKey($consumer_key);

        if (!$consumer) {
            throw new \FlyApi\Exception('Consumer not found');
        }

        if ('ACTIVE' != $consumer->status) {
            throw new \FlyApi\Exception('Consumer not active');
        }

//        $consumer = new \FlyApi\Consumer($consumer->key, $consumer->secret);
        return $consumer;
    }

    /**
     * @param \FlyApi\Consumer $consumer
     * @param string $nonce
     * @param int $timestamp
     * @return bool
     */
    public function lookupNonce($consumer, $nonce, $timestamp) {}
}

class Exception  extends \FlyApi\Exception {}

class ErrorHandler {
    static private $_format = 'json';

    public static function printExceptionInfo(\Exception $e) {
        if (($e instanceof Exception) || ($e instanceof \Flywheel\Exception\Api)) {
            self::printError($e->getCode(), $e->getMessage());
        } else {
            error_log(\Flywheel\Exception::outputStackTrace($e, 'txt'));
            self::printError($e->getCode());
        }
    }

    private static function _responseError($code, $error) {
        $response = new \stdClass();
        $response->hash = array(
            'request' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? 'https://':'http://')
            .$_SERVER['HTTP_HOST']
            .$_SERVER['REQUEST_URI'],
            'error' => $error,
        );

        if (400 != $code) {
            $response->hash['api'] = self::_getRequestInfo();
            $response->hash['format'] = self::$_format;
        }

        return $response;
    }

    public static function printError($code, $body = null) {
        while (ob_get_level()) {
            if (!ob_end_clean()) {
                break;
            }
        }

        if (!headers_sent()) {
            if (null == $code) {
                $code = '500';
            }

            $headMsg = self::_getHeaderMessage($code);

            header("HTTP/1.1 $code $headMsg");
        }

        $response = self::_responseError($code, $body);
        $format = self::$_format;
        switch ($format) {
            case 'xml' :
                header('Content-type:text/xml');
                break;
            case 'text':
                break;
            default:
                header('Content-type:application/json');
                $response = json_encode($response);

        }

        echo $response;
        exit;
    }

    private static function _getRequestInfo() {
        if (isset($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] != '')) {
            $pathInfo = $_SERVER['PATH_INFO'];
        }
        else {
            $pathInfo = preg_replace('/^'.preg_quote($_SERVER['SCRIPT_NAME'], '/').'/', '', $_SERVER['REQUEST_URI']);
            $pathInfo = preg_replace('/^'.preg_quote(preg_replace('#/[^/]+$#', '', $_SERVER['SCRIPT_NAME']), '/').'/', '', $pathInfo);
            $pathInfo = preg_replace('/\??'.preg_quote($_SERVER['QUERY_STRING'], '/').'$/', '', $pathInfo);
            if ($pathInfo == '') $pathInfo = '/';
        }

        $segment = explode('/', $pathInfo);
        $_cf = explode('.', end($segment)); //check define format
        if (isset($_cf[1])) {
            self::$_format = $_cf[1];
        }

        return $pathInfo;
    }

    private static function _getHeaderMessage($code) {
        switch ($code) {
            case 400:
                return 'Bad Request';
            case 401:
                return 'Unauthorized';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 406:
                return 'Not Acceptable';
            case 500:
                return 'Internal Server Error';
            case 502:
                return 'Bad Gateway';
            case 503:
                return 'Service Unavailable';
        }
    }
}