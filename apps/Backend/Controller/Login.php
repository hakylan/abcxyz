<?php
namespace Backend\Controller;

use Flywheel\Base;
use Flywheel\Event\Event;
use Flywheel\Factory;
use Flywheel\Session\Session;
use \Flywheel\Captcha\Math;

class Login extends BackendBase {
    public function beforeExecute() {
        $this->setTemplate('Seudo');
        $this->need_login = false;
        parent::beforeExecute();
    }

    //List users
    public function executeDefault() {
        $this->need_login = false;
        $this->setLayout('login');
        $this->setView('Login/default');

        /** @var \BackendAuth $backendAuth */
        $backendAuth = \BackendAuth::getInstance();

        $comeback = $this->get('r');
        $comeback = (null != $comeback) ? urldecode($comeback) : '/';
        if ($backendAuth->isBackendAuthenticated()) {
            $this->redirect($comeback);
        }

        $display = $this->post('credential');
        if (!$display) {
            $display = Factory::getCookie()->read('username');
        }

        $error = array();

        if ($this->request()->isPostRequest()) {
            //check captcha first
            $password = $this->post('password');
            $credential = $this->post('credential'); //don't care display name
            $captcha = $this->post('captcha');
            Factory::getCookie()->write('username', $credential);

            if(Math::check($captcha)==false) {
                $error[] = t('Sai rồi, tính nhẩm kém quá');
            }

            if (empty($error) && true === ($result = $backendAuth->authenticate($credential, $password))) {
                //authenticated, redirect to pre-page
                $this->redirect($comeback);
            } else if (isset($result)) {
                switch ($result) {
                    case \BackendAuth::ERROR_USER_NOT_ACCESS_ADMIN:
                        $error[] = t('Tài khoản không có quyền truy cập');
                        break;
                    case \BackendAuth::ERROR_CREDENTIAL_INVALID:
                        $error[] = t('Vui lòng nhập email hoặc mật khẩu');
                        break;
                    case \BackendAuth::ERROR_UNKNOWN_IDENTITY:
                        $error[] = t('Thông tin đăng nhập không đúng');
                        break;
                    default:
                        $error[] = t('Thông tin đăng nhập không đúng');
                }
            }
        }

        $this->view()->assign('display', $display);
        $this->view()->assign('error', $error);

        return $this->renderComponent();
    }



    public function executeLogout(){
        \BackendAuth::getInstance()->logout();
        $this->redirect($this->createUrl('/'));
    }

    public function executeGetCaptcha() {
        $captcha = new Math();
        $captcha->show();
        Base::end();
    }

}