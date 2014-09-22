<?php
$r = array(
    '__urlSuffix__' => '.html',
    '__remap__' => array(
        'route' => 'dashboard/default'
    ),
    '/' => array(
        'route' => 'dashboard/default'
    ),
    '{controller}' => array(
        'route' => '{controller}/default'
    ),
    '{controller}/{action}' => array(
        'route' => '{controller}/{action}'
    ),
    '{controller}/{action}/{id:\d+}' => array(
        'route' => '{controller}/{action}'
    ),
    'order/detail/default/{id:[_a-zA-Z0-9-]+}' => array(
        'route' => 'order/detail/'
    ),
    'user/user_profile/edit' => array(
        'route' => 'user/user_profile/edit'
    ),
    'don-hang/{id:[_a-zA-Z0-9-]+}' => array(
        'route' => 'order/detail/default'
    ),
);
return $r;