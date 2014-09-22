<?php
namespace Backend\Controller;
use Flywheel\Base;
use Flywheel\Controller\Web;
use Flywheel\Router\WebRouter;
use Flywheel\Session\Session;
use Flywheel\Translation\Translator;
use SeuDo\GlobalEventDispatcher;
use SeuDo\Permission;

abstract class BackendBase extends Web {
    public static $language;
    public $need_login = true;

    public function beforeExecute() {
        $authenticator = \BackendAuth::getInstance();
        $controller = $this->getName();

        if ($this->need_login && !in_array($this->getName(), array('Login', 'Error'))) {
            /** @var \BackendAuth $auth */
            $auth = \BackendAuth::getInstance();
            if (!$auth->isBackendAuthenticated()) {
                //need authen
                if ($this->request()->isXmlHttpRequest()) {
                    Base::end(json_encode(array(
                        'error' => 'AUTHENTICATE FAIL',
                        'error_code' => 'E0001',
                        'message' => t('Phiên đăng nhập của bạn đã hết, đề nghị đăng nhập lại')
                    )));
                } else {
                    //redirect
                    $this->redirect($this->createUrl('login', array(
                        'r' => urlencode($this->request()->getUri())
                    )));
                }
            }
            
            $auth->buildPermission();
        }


        self::$language = $this->get('language');
        $this->_initCss();
        $this->_initJs();
    }

    public static function t($id, array $parameters = array(), $domain = 'messages', $locale = null) {
        if (null == $locale && self::$language) {
            $locale = self::$language;
        }

        return t($id, $parameters, $domain, $locale);
    }

    public static function td($id, array $parameters = array(), $domain = 'messages', $locale = null) {
        echo self::t($id, $parameters, $domain, $locale);
    }


    /**
     * @param $resource
     * @return bool
     */
    public function isAllowed($resource){
        $instance = \BackendAuth::getInstance();
        /* if user is god */
        if($instance->getUserId() == 1) {
            return true;
        }
        if(!$resource || $resource == null) return false;

        return Permission::getInstance()->isAllowed($resource);
    }

    public function raise403() {
        $this->redirect($this->createUrl('error/not_permission'));
    }
    public function raise404() {
        $this->redirect($this->createUrl('error/not_found'));
    }

    protected function _initCss() {
        $document = $this->document();

        if($this->getTemplateName() == 'Default'){
            $document->addCss('css/aui-production.min.css');
            $document->addCss('css/dark-blue.min.css');
            $document->addCss('css/common.min.css');
            $document->addCss('css/animations.min.css');
            $document->addCss('css/responsive.min.css');
            $document->addCss('css/bootstrap-formhelpers.min.css');
            $document->addCss('css/backend.css');
            $document->addCss('css/style-header.css');
            $document->addCss('css/stylev1.css');
            $document->addCss('css/style-typo.css');
//        $document->addCss('css/style-backendv1.css');
            $document->addCss('fonts/roboto/roboto.css');
            $document->cssBaseUrl = $document->getBaseUrl() .'assets/';
            $document->cssBaseDir = $document->getPublicPath() .'assets/';
        }
        if($this->getTemplateName() == 'Seudo'){
            $document->cssBaseUrl = $document->getBaseUrl() .'assetv2/';
            $document->cssBaseDir = $document->getPublicPath() .'assetv2/';
            $document->addCss('css/bootstrap.min.css');
            $document->addCss('css/style-typo.css');
            $document->addCss('css/style-header.css');
            $document->addCss('css/style-menu-tab.css');
            $document->addCss('css/cssloading.css');
            $document->addCss('css/font-awesome.min.css');
            $document->addCss('css/loading.css');
        }

    }

    protected function _initJs() {
        $document = $this->document();
        $document->addJs('js/jquery-1.10.2.min.js', 'TOP');
        $document->addJs('bootstrap/js/bootstrap.min.js', 'TOP');
        $document->addJs('js/jquery.lazyload.min.js', 'TOP');

        if($this->getTemplateName() == 'Default'){
            $document->addJs('js/process/common.js', 'TOP');
            $document->addJs('js/tooltip.js', 'TOP');
            $document->addJs('js/jquery-scrolltofixed.js', 'TOP');
            $document->addJs('bootstrap/js/bootstrap-select.min.js', 'TOP');
            $document->addJs('js/jquery.confirm.min.js', 'TOP');
            $document->addJs('js/jquery.uniform.min.js', 'TOP');
            $document->addJs('js/seudo-backendv1.js', 'TOP');
            $document->jsBaseUrl = $document->getBaseUrl() .'assets/';
            $document->jsBaseDir = $document->getPublicPath() .'assets/';
        }
        if($this->getTemplateName() == 'Seudo'){
            $document->addJs('js/process/lib/common.js?v=11', 'BOTTOM');
            $document->addJs('js/process/menu.js', 'TOP');
            $document->addJs('js/tooltip.js', 'TOP');
            $document->addJs('bootstrap/js/bootstrap-select.min.js', 'TOP');
            $document->jsBaseUrl = $document->getBaseUrl() .'assetv2/';
            $document->jsBaseDir = $document->getPublicPath() .'assetv2/';

        }

    }
}
