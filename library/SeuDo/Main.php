<?php
namespace SeuDo;
use Flywheel;
use Flywheel\Config\ConfigHandler;

class Main {
    protected static $_appsPath = array();
    protected static $_init = false;
    protected static $_routerList = array();

    protected static function _init() {
        if (self::$_init) {
            return;
        }

        self::$_appsPath = array(
            'home' => ROOT_PATH . '/apps/Home',
            'user' => ROOT_PATH . '/apps/User',
            'backend' => ROOT_PATH . '/apps/Backend'
        );;

        self::$_init = true;
    }

    public static function getAppUrl($appName){
        $url = ConfigHandler::get('url');
        return (isset($url[strtolower($appName)])?$url[strtolower($appName)]:'');
    }

    public static function getPublicUrl() {
        $url = ConfigHandler::get('url');
        return $url['home'];
    }

    public static function getHomeUrl() {
        return self::getAppUrl('home');
    }

    public static function getUserUrl() {
        return self::getAppUrl('user');
    }

    public static function getBackendUrl(){
        return self::getAppUrl('backend');
    }


    public static function getHomeRouter() {
        return self::getRouter('home');
    }

    public static function getBackendRouter() {
        return self::getRouter('backend');
    }

    public static function getUserRouter() {
        return self::getRouter('user');
    }


    /**
     * @param $name
     * @return \Flywheel\Router\WebRouter
     */
    public static function getRouter($name) {
        self::_init();

        if (!isset(self::$_appsPath[$name])) {
            return Flywheel\Factory::getRouter();
        }

        $class = '\SeuDo\\' . ucfirst($name) . 'Router';

        if (!isset(self::$_routerList[$name])) {
            $config = require self::$_appsPath[$name] . '/Config/routing.cfg.php';
            $router = new $class();
            $router->init($config);
            self::$_routerList[$name] = $router;
        }
        return self::$_routerList[$name];
    }

    public static function CacheIsEnable(){
        $caching = ConfigHandler::get('caching');
        if(!$caching || empty($caching)) return false;

        return (isset($caching['__enable__'])?$caching['__enable__']:false);
    }

}