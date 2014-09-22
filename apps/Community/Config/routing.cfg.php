<?php
$r = array(
    '__urlSuffix__' => '.html',
    '__remap__' => array(
        'route' => 'community/default'
    ),
    '/' => array(
        'route' => 'community/default'
    ),
    '{controller}' => array(
        'route' => '{controller}/default'
    ),
    '{controller}/{action}' => array(
        'route' => '{controller}/{action}'
    ),
    '{controller}/{action}/{id:\d+}' => array(
        'route' => '{controller}/{action}'),

    'home/{page:\d+}' => array(
        'route' => 'community/default'
    ),
    'post/{alias:[a-zA-Z0-9-]+}/' => array(
        'route' => 'community/post'),
    'category/{slug:[a-zA-Z0-9-]+}' => array(
        'route' => 'community/category'),
    'category/{slug:[a-zA-Z0-9-]+}/{page:[0-9-]+}' => array(
        'route' => 'community/category'),
);
return $r;