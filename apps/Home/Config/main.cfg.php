<?php
defined('APP_PATH') or define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
\Flywheel\Loader::addNamespace('Home', dirname(APP_PATH));

return array(
  'app_name'=>'Home',
  'app_path'=> APP_PATH,
  'view_path'=> APP_PATH .DIRECTORY_SEPARATOR.'Template/',
  'import'=>array(
        'root.library.*',
        'app.Controller.*',
        'app.Include.*',
        'root.model.*',
        'global.include.*',
        'global.widget.*',
  ),
  'namespace'=> 'Home',
  'timezone'=>'Asia/Ho_Chi_Minh',
  'template'=>'Default'
);