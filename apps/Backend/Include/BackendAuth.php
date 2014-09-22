<?php
use Flywheel\Factory;
use SeuDo\Permission;
use Flywheel\Session\Session;
class BackendAuth extends BaseAuth {
    const ERROR_USER_NOT_ACCESS_ADMIN = -10;

    protected $valid = false;
    public $myModule = 'backend';
    protected static $_instance;

    public static function getInstance() {
        if(null === static::$_instance){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function authenticate($credential, $password, $cookie = false) {
        $result = parent::authenticate($credential, $password, $cookie);

        if ($result) {
            $user = $this->getUser();
            if (($user instanceof \Users) && $user->isActive()
                && $user->getSection() == \Users::SECTION_CRANE) {
                $this->_setIsAuthenticatedAdmin(true);
                return true;
            }

            return self::ERROR_USER_NOT_ACCESS_ADMIN;
        }

        return $result;
    }

    protected function _setIsAuthenticatedAdmin($b) {
        Session::getInstance()->set('auth/admin', $b);
    }

    public function isBackendAuthenticated() {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $auth = Session::getInstance()->get('auth/admin');
        return $auth;
    }

    public function buildPermission() {
        if ($user = $this->getUser()) {
            $data = Session::getInstance()->get('auth/permission');
            if (empty($data)) {
                $data = \Permissions::buildPermission($user);
            }

            Permission::getInstance()->init($data);
        }
    }
}