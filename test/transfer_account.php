<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
    $user = \Users::retrieveByUsername('luuhieu');
    $amount = mt_rand(1, 100) * 1000000;
    $response = \SeuDo\Accountant\Util::refund($user, $amount, 'Thanh toán từ Sếu Đỏ', 'Test thôi mà');
    print_r($response);

} catch (\Exception $e) {
    print_r($e->getMessage());
}