<?php
use Flywheel\Factory;
use Flywheel\Db\Type\DateTime;
use \Flywheel\Session\Session;

class BaseAuth extends Flywheel\Session\Authenticate {


    protected static $_instance = null;

    public static function getInstance() {
        if(null === static::$_instance){
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    public function init() {
        if (null != ($id = Session::get('auth\id'))) { //session writed
            $this->_setIsAuthenticated(true);
        } else { //check in Cookie
            $cookie = Factory::getCookie();
            if ($data = $cookie->readSecure('auth')) {
                $data = json_decode($data, true);

                //HieuLT: we need retrieveByUsername to get data from redis
                // after that check secret key

                $user = \Users::retrieveByUsername($data['username']);
                if ($user && $data['secret_key'] == $user->getSecretKey()) {
                    //@TODO should throw a event, push to queue and tracking last login time in background
                    $user->setLastLoginTime(new DateTime());
                    $user->setLastLoginIp(\Flywheel\Base::getApp()->getClientIp());
                    $user->save();

                    $this->setSession($user);
                    $this->setCookie($user);
                    $this->_setIsAuthenticated(true);
                }
            }
        }
    }


    public function authenticate($credential, $password, $cookie = false) {
        $this->dispatch('onBeginAuthen', new BaseEvent($this,array($credential)));

        if (empty($credential) || empty($password)) return self::ERROR_CREDENTIAL_INVALID;

        $this->_identity = $credential;
        $this->_credential = $password;

        if (strpos($credential, '@') !== false) {
            $user = \Users::retrieveByEmail($credential);
        } else {
            $user = \Users::retrieveByUsername($credential);
        }

        if(!$user || empty($user) || !($user instanceof Users)){
            return self::ERROR_UNKNOWN_IDENTITY;
        }

        if(($user instanceof \Users)) {
            if ($user->password != Users::hashPassword($password, $user->password)) {
                return self::ERROR_CREDENTIAL_INVALID;
            }

            $this->clearCookie();
            $this->setCookie($user);
            $this->setSession($user);
            $this->_setIsAuthenticated(true);
            $user->setLastLoginTime(new DateTime());
            $user->save();
            if($user) $this->dispatch('onAfterAuthen', new BaseEvent($this,$user->getAttributes()));
            return $this->isAuthenticated();
        }
        return false;
    }

    /**
     * @return Users
     */
    public function getUser() {
        $id = $this->getUserId();
        return \Users::retrieveById($id);
    }

    public function getUserId(){
        return Session::get('auth\id');
    }

    /**
     * log out
     */
    public function logout() {
        //
        Session::set('auth',null);
        Session::getInstance()->remove('auth');
        //
        $this->clearCookie();
        $this->_setIsAuthenticated(false);
    }

    /**
     * set session
     */
    public function setSession(\Users $user) {
        Session::set('auth',  array('id'=>$user->getId()));
    }

    public function setCookie(\Users $user) {
        $cookie = Factory::getCookie();
        $cookie->writeSecure('auth', json_encode($user->getAttributes('username,secret_key')));
    }

    private function clearCookie() {
        Factory::getCookie()->writeSecure('auth', null, -100000);
    }

    public function setAuthenFrom($from){
        Session::set('authenFrom', $from);
    }
}