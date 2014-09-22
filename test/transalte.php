<?php
require_once __DIR__ .'/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');
\Flywheel\Loader::import('global.include.*');

try {
    $result = GlobalHelper::resourceKeyToTranslate();
    print_r(iterator_to_array($result));
} catch (\Exception $e) {
    print_r($e->getMessage());
}