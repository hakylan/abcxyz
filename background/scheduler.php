<?php
chdir(__DIR__);
set_time_limit(0);
require __DIR__ .'/../bootstrap.php';
$globalCnf = require ROOT_PATH . '/config.cfg.php';

$config = array_merge( $globalCnf, require __DIR__ . '/../apps/Background/Config/main.cfg.php');

//define('APP_DIR',ROOT_PATH.'/'.'apps'.'/Background');
try {
    $app = \Flywheel\Base::createConsoleApp($config, \Flywheel\Base::ENV_DEV, true);
    $app->run();
//    Common::writeFileMemoryLog("memory_background.html");
} catch (\Exception $e) {
    \Flywheel\Exception::printExceptionInfo($e);
}