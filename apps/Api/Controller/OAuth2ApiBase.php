<?php
/**
 * Created by PhpStorm.
 * User: Piggat
 * Date: 5/20/14
 * Time: 4:43 PM
 */

namespace Api\Controller;
use FlyApi\Request;
use SeuDo\Api\DataStore;
use SeuDo\Api\Server;
use Flywheel\Controller\Api;
use Flywheel\Redis\Client;

abstract class OAuth2ApiBase extends Api {

    protected $_timestampThreshold = 300000; // in seconds, five minutes
    protected $_consumer;

    protected $_dev = false;

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
        //$this->_verifyRequest();
        header("Access-Control-Allow-Origin: *");
    }

    /**
     * encrypt string using AES 256
     * @param $string
     * @param $secret_key
     * @param $secret_iv
     * @return string
     */
    protected function _encrypt($string, $secret_key, $secret_iv)
    {
        $encrypt_method = "AES-256-CBC";

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    /**
     * decrypt string using AES 256
     * @param $string
     * @param $secret_key
     * @param $secret_iv
     * @return string
     */
    protected function _decrypt($string, $secret_key, $secret_iv)
    {
        $encrypt_method = "AES-256-CBC";

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

        return $output;
    }

    /**
     * Generate access token based on user_id, client_id, client_secret, scope
     * @param $user_id
     * @param $client_id
     * @param $scope
     * @param $client_secret
     * @return string
     */
    protected function _generateAccessToken($user_id, $client_id, $scope, $client_secret)
    {
        //$now = new \DateTime();
        $now = $this->_randomString();
        $token = $this->_encrypt($user_id.'-'.$client_id.'-'.$scope.'-'.$now, $client_secret, $client_id);
        //generate new access token
        $this->_saveAccessToken(null, $token);
        return $token;
    }

    /**
     * Generate refresh token based on user_id, client_id, client_secret, scope
     * @param $user_id
     * @param $client_id
     * @param $scope
     * @param $client_secret
     * @return string
     */
    protected function _generateRefreshToken($user_id,$client_id,$scope, $client_secret )
    {
        $now = $this->_randomString();
        $token =  $this->_encrypt($user_id.'-'.$client_id.'-'.$scope.'-'.$now,$client_id, $client_secret);
        $result = $this->_saveRefreshToken($client_id, $token);
        return $token;
    }

    /**
     * Verify token, return false if fail,return $user_id if valid
     * @return mixed
     */
    protected function _verifyAccessToken()
    {
        $client_id = $this->post('client_id');
        $access_token = $this->post('access_token');

        if (!$this->_dev)
        {
            define ('REDIS_ACCESS_TOKEN','_access_token_');
            $redis = Client::getConnection('accesstoken');
            $key = REDIS_ACCESS_TOKEN . $access_token;
            if ($redis->get($key)) {
                // verify success
            }
            else{
                $result["error_code"] = 4;
                $result["error"] = "access token is expired";
                return $result;
            }
        }

        //verify consumer id and secret
        $consumer = \Consumer::retrieveById($client_id);
        $client_secret = $consumer->getConsumerSecret();
        //$client_secret = '123456';

        try
        {
            $result['error_code'] = 0;
            $result["error"] = "0";
            //decrypt access token
            $string = $this->_decrypt($access_token, $client_secret, $client_id);
            $data = explode("-",$string);
            $count = count($data);
            if ($count < 4)
            {
                $result["error"] = "access token is not valid, exploded count =".$count;
                return $result;
            }
            $user_id = $data[0];
            $consumer_id = $data[1];
            $scope = $data[2];
            //$time = new \DateTime($data[3]);

            //validate client id
            if ($consumer_id != $client_id)
            {
                $result["error"] = "client id is not valid";
                return $result;
            }

            $result["user_id"] = $user_id;
            $result["scope"] = $scope;
            $result['access_token'] = $access_token;
            $result['client_id'] = $client_id;
            return $result;
        }
        catch (\Exception $ex)
        {
            $result["error"] = $ex->getMessage();
            return $result;
        }
    }

    /**
     * Verify refresh token
     */
    protected function _verifyRefreshToken($consumer_id, $refresh_token)
    {
        //verify consumer id and secret
        $consumer = \Consumer::retrieveById($consumer_id);
        $consumer_secret = $consumer->getConsumerSecret();

        try
        {
            $string = $this->_decrypt($refresh_token, $consumer_id, $consumer_secret);
            $data = explode("-",$string);
            $count = count($data);
            if ($count < 3)
            {
                $result["error"] = "refresh token is not valid, exploded count =".$count;
                return $result;
            }
            $user_id = $data[0];
            $client_id = $data[1];

            //validate client id
            if ($consumer_id != $client_id)
            {
                $result["error"] = "client id is not valid";
                return $result;
            }

            $scope = $data[2];

            // verify on database
            $record = \ClientApi::findOneByRefreshToken($refresh_token);
            if ($record)
            {
                // verify success
                $client_id_record = $record->getClientId();
                $user_id_record = $record->getUserId();
                if ($client_id != $client_id_record || $user_id != $user_id_record)
                {
                    $result["error"] = "refresh token is not valid";
                    return $result;
                }
            }
            else
            {
                $result["error_code"] = 3;
                $result["error"] = "refresh token is not valid";
                return $result;
            }

            //generate new access token
            $result['access_token'] = $this->_generateAccessToken($user_id, $consumer_id, $scope, $consumer_secret);
            return $result;
        }
        catch (\Exception $ex)
        {
            $result["error"] = $ex->getMessage();
            return $result;
        }
    }

    /**
     * Save access token to REDIS
     */
    protected function _saveAccessToken($consumer, $access_token) {
        define ('REDIS_ACCESS_TOKEN','_access_token_');
        $redis = Client::getConnection('accesstoken');
        $key = REDIS_ACCESS_TOKEN . $access_token;
        $result = $redis->setex($key, $this->_timestampThreshold, $access_token);
        $redis->save();

        //$redis->defect();
        //echo $result;
        //TODO: make sure REDIS executed
        //usleep(500000); // 500ms???
        return $result;
    }

    /**
     * Save refresh token to database
     * @param $refresh_token
     * @param $client_id
     * @return mixed
     */
    protected function _saveRefreshToken($client_id, $refresh_token)
    {
        //verify consumer id and secret
        $consumer = \Consumer::retrieveById($client_id);
        $consumer_secret = $consumer->getConsumerSecret();

        $string = $this->_decrypt($refresh_token, $client_id, $consumer_secret);
        $data = explode("-",$string);
        $count = count($data);
        $result["error_code"] = 0;
        if ($count < 3)
        {
            $result["error_code"] = 1;
            $result["error"] = "refresh token is not valid, exploded count =".$count;
            return $result;
        }
        $user_id = $data[0];
        $client_id = $data[1];
        $scope = $data[2];

        /*$record = \ClientApi::findOneByClientIdAndUserId($client_id, $user_id);

        if ($record)
        {
            $record->refresh_token = $refresh_token;
            $record->scope = $scope;
            $record->save();
            return $result; // already have, reset refresh token & scope
        }*/

        $record= new \ClientApi();
        $record->user_id = $user_id;
        $record->client_id = $client_id;
        $record->refresh_token = $refresh_token;
        $record->scope = $scope;
        $record->save();
        return $result;
    }

    /**
     * Remove access token from REDIS
     * @param $access_token
     */
    protected function _removeAccessToken($access_token)
    {
        define (REDIS_ACCESS_TOKEN,'_access_token_');
        $redis = Client::getConnection('accesstoken');
        $key = REDIS_ACCESS_TOKEN . $access_token;
        if ($redis->get($key)) {
            $redis->del($key);
        }
    }

    /**
     * Remove refresh token from Database
     * @param $refresh_token
     */
    protected function _removeRefreshToken($client_id, $user_id, $refresh_token = null)
    {
        // support cả trường hợp chưa gửi refresh token
        if (empty($refresh_token))
        {
            $record = \ClientApi::findOneByClientIdAndUserId($client_id, $user_id);
            $record->delete();
        }
        else{
            $record = \ClientApi::findOneByRefreshToken($refresh_token);
            $record->delete();
        }
    }

    /**
     * Generate random string to scramble access_token and refresh_token
     * @return string
     */
    private function _randomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
    return $randstring;
    }
}