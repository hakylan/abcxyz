<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
    $bank = mt_rand(0, 1)? 'TCB' : 'VCB';
    $note = 'add cho a Hieu';
    $user = \Users::retrieveByAccountNo('2014031614455720');
    $amount = 10000000;
    $res = \SeuDo\Accountant\Util::createDeposit($user, $amount, 'BANK_TRANSFER', $bank, $note, 'Thanh Trung', null, null, null);
    var_dump($res);
    exit;
    /** @var Users[] $users */
    $users = \Users::findAll();
    shuffle($users);
    foreach ($users as $user) {
        $amount = mt_rand(100, 1000) *1000;
        $bank = mt_rand(0, 1)? 'TCB' : 'VCB';
        $note = 'TEST thôi mà';
        $res = \SeuDo\Accountant\Util::createDeposit($user, $amount, 'BANK_TRANSFER', $bank, $note, 'TRUNG GAY', null, null, null);
        var_dump($res);
    }

} catch (\Exception $e) {
    print_r($e->getMessage());
}