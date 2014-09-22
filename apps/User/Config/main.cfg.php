<?php
defined('APP_PATH') or define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
\Flywheel\Loader::addNamespace('User', dirname(APP_PATH));

return array(
  'app_name'=>'User',
  'app_path'=> APP_PATH,
  'view_path'=> APP_PATH .DIRECTORY_SEPARATOR.'Template/',
  'import'=>array(
    'app.Library.*',
    'app.Controller.*',
    'app.Include.*',
    'root.model.*',
    'global.helper.*',
    'global.include.*',
    'global.widget.*',
  ),
  'namespace'=> 'User',
  'timezone'=>'Asia/Ho_Chi_Minh',
  'template'=>'Default'
);