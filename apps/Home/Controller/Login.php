<?php
namespace Home\Controller;
use Flywheel\Factory;
use Flywheel\Filesystem\AjaxUploader;
use \Flywheel\Session\Session;
use Monolog\Handler\LogglyHandler;
use Home\Controller\HomeBase;
class Login extends HomeBase{

    public function beforeExecute() {
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('afterLogin', array(new \HomeEvent(), 'afterLogin'));
        $eventDispatcher->addListener('beginLogout', array(new \HomeEvent(), 'beginLogout'));

    }
    public function executeDefault() {
        return $this->executeLogin();
    }

    public function executeLogin(){
    // ----------------
            if($this->request()->isPostRequest()) {
                $this->validAjaxRequest();
                $credential = $this->request()->post('credential');
                $password = $this->request()->post('password');
                $remember = $this->request()->post('remember');
                $homeAuth = \HomeAuth::getInstance();
                $result =  $homeAuth->authenticate($credential, $password, $remember);
                if($result && $result > 0){
                    $user_id = $homeAuth->getUserId();
                    $ses = Session::getInstance(); //::getInstance();
                    $session_id = $ses->id();
                    \CartItem::mergeCartItem($session_id,$user_id);
                    $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'Đăng nhập thành công');
                    return $this->renderText($ajax->toString());
                }
                $ajax = new \AjaxResponse(\AjaxResponse::ERROR, 'Tên đăng nhập hoặc mật khẩu không chính xác');
                return $this->renderText($ajax->toString());
            }

        $homeAuth = \HomeAuth::getInstance();

        if ($homeAuth->isAuthenticated()) {
            $back = $this->request()->get('r');
            $back = (null != $back) ? urldecode($back) : '/';
            $back_url = $this->request()->get('url');
            if(!empty($back_url)) {
                $this->redirect(base64_decode($back_url));
            }
            $this->redirect($back);
        }
        $this->setView('Login/default');
        return $this->renderComponent();
    }

    public function executeLogout(){
        \HomeAuth::getInstance()->logout();
        session_destroy();
        $back = $this->request()->get('r');
        $back = (null != $back)? urldecode($back) : '/';
        $this->redirect($back);
    }

}
