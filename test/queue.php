<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
    \Flywheel\Queue\Queue::factory('email_queue')->push(json_decode($data));
} catch (\Exception $e) {
    print_r($e->getMessage());
}