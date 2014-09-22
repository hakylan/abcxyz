<?php
defined('APP_PATH') or define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
\Flywheel\Loader::addNamespace('Backend', dirname(APP_PATH));

return array(
    'i18n' => array(
        'enable' => true,
        'default_fallback' => array('vi'),
        'default_locale' => 'vi-VN',
        'resource' => array(
            'zh-CN' => array(
                ROOT_PATH .'/resource/languages/zh-CN/backend.php',
            )
        )
    ),
    'app_name' => 'Backend',
    'app_path' => APP_PATH,
    'view_path' => APP_PATH . DIRECTORY_SEPARATOR . 'Template/',
    'import' => array(
        'app.Library.*',
        'app.Controller.*',
        'app.Include.*',
        'app.Widget.*',
        'root.model.*',
        'root.library.*',
        'global.helper.*',
        'global.include.*',
        'global.widget.*',
    ),
    'namespace' => 'Backend',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'template' => 'Default',
);




