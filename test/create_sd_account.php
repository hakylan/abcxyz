<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
//    $user = \Users::retrieveByUsername('god');
    $user = \SeuDo\Accountant\Util::createUserAccount(43);
//    $res = \SeuDo\Accountant\Util::createUserAccount($user);
    print_r(($res));
    /*$client = \SeuDo\Accountant\Util::apiCreateAccount('SeuDo God', 'god', 'SERVICE', 'ACTIVE');
    if ($client->getHttpCode() == 200) {
        $res = $client->getResponse();
        $user->setAccountNo($res['account']['uid']);
        $user->save();
    }*/
} catch (\Exception $e) {
    print_r($e->getMessage());
}