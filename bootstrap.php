<?php
//echo "He thong dang bao tri. Quy khach vui long quay lai sau it phut";
//exit;
use Flywheel\Loader;
define('ROOT_PATH', dirname(__FILE__));
define('GLOBAL_PATH', ROOT_PATH .DIRECTORY_SEPARATOR .'global');
define('GLOBAL_INCLUDE_PATH', ROOT_PATH .DIRECTORY_SEPARATOR .'global'.DIRECTORY_SEPARATOR.'include');
define('GLOBAL_TEMPLATES_PATH', ROOT_PATH .DIRECTORY_SEPARATOR .'global'.DIRECTORY_SEPARATOR.'templates');
define('LIBRARY_PATH', ROOT_PATH .DIRECTORY_SEPARATOR .'library');
define('RUNTIME_PATH', ROOT_PATH .DIRECTORY_SEPARATOR .'runtime');
define('PUBLIC_DIR', ROOT_PATH .DIRECTORY_SEPARATOR .'www_html'.DIRECTORY_SEPARATOR);

require_once ROOT_PATH .'/redis_keys.php';
require_once ROOT_PATH .'/permissions.cfg.php';
require_once ROOT_PATH.'/vendor/autoload.php';

Loader::register();
Loader::setPathOfAlias('root', ROOT_PATH);
Loader::setPathOfAlias('global', GLOBAL_PATH);

Loader::addNamespace('SeuDo', LIBRARY_PATH);
Loader::addNamespace('mongodb', ROOT_PATH);

Loader::import('global.include.*');

\Flywheel\Config\ConfigHandler::import('root.config');

set_error_handler(array('SeuDo\ErrorHandler', 'errorHandling'));
set_exception_handler(array('SeuDo\ErrorHandler', 'exceptionHandling'));

require_once ROOT_PATH .'/global_event.php';
