<?php
defined('APP_PATH') or define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
\Flywheel\Loader::addNamespace('Api', dirname(APP_PATH));

return array(
    'app_name'=>'Api',
    'app_path'=> APP_PATH,
    'import' => array(
        'root.model.*',
        'app.Include.*'
    ),
    'namespace'=> 'Api',
    'timezone'=>'Asia/Ho_Chi_Minh',
);