<?php
require_once '../bootstrap.php';
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../apps/Home/Config/main.cfg.php');
try {
    $app = \Flywheel\Base::createWebApp($config, \Flywheel\Base::ENV_DEV, true);

    $c = \Flywheel\Config\ConfigHandler::get('queue');

    $user = \Users::retrieveById(42);

    $data = \Permissions::buildPermission($user);

    $permission = new SeuDo\Permission();
    $permission->init($data);


    $p2 = \SeuDo\Permission::getInstance();
    $check = $p2->isAllowed('ORDER_FEE_EDITMENT');

    echo $check==1?'co quyen':'ko co quyen';

    exit;
} catch (\Exception $e) {
    print_r($e);
}
