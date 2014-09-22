<?php
defined('APP_PATH') or define('APP_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
\Flywheel\Loader::addNamespace('Community', dirname(APP_PATH));

return array(
    'app_name' => 'Community',
    'app_path' => APP_PATH,
    'view_path' => APP_PATH . DIRECTORY_SEPARATOR . 'Template/',
    'import' => array(
        'app.Library.*',
        'app.Controller.*',
        'app.Library.*',
        'app.Widget.*',
        'root.model.*',
        'global.helper.*',
        'global.include.*',
        'global.widget.*',
    ),
    'namespace' => 'Community',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'template' => 'Default',
    /*'assets' => array(
        'community' => array(
            'envi' => 'dev',
            'combine' => true,
            'minify' => true,
            'base_url' => '',
            'assets_path' => 'E:\Copy\uwamp\www\seudo\www_html\community\assets',
            'assets_dir' => 'assets',
            'base_path' => 'E:\Copy\uwamp\www\seudo\www_html\community\assets',
            'cache_dir' => 'cache',
            'cache_path' => 'E:\Copy\uwamp\www\seudo\www_html\community\assets\cache', //
            'cache_url' => 'cache', // base_url/cache_dr
            'js_dir' => 'js',
            'js_path' => 'js', //
            'js_url' => 'js',
            'css_dir' => 'css',
            'css_path' => 'css', //
            'css_url' => 'css',
        ),
        'common' => array(
            'envi' => 'dev',
            'combine' => true,
            'minify' => true,
            'base_url' => 'http://localhost/seudo/www_html/',
            'assets_path' => 'E:\Copy\uwamp\www\seudo\www_html\assets',
            'assets_dir' => 'assets',
            'base_path' => 'E:\Copy\uwamp\www\seudo\www_html\assets',
            'cache_dir' => 'cache',
            'cache_path' => 'E:\Copy\uwamp\www\seudo\www_html\assets\cache', //
            'cache_url' => 'cache', // base_url/cache_dr
            'js_dir' => 'js',
            'js_path' => 'js', //
            'js_url' => 'js',
            'css_dir' => 'css',
            'css_path' => 'css', //
            'css_url' => 'css',
        ),
    )*/

);