<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
    trigger_error('test push error');

} catch (\Exception $e) {
    print_r($e->getMessage());
}