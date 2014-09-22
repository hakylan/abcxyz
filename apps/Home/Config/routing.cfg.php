<?php
$r = array(
    '__urlSuffix__' => '.html',
    '__remap__' => array(
        'route' => 'home/default'
    ),
    '/' => array (
        'route' => 'home/default'
    ),
    '{controller}' => array (
        'route' => '{controller}/default'
    ),
    '{controller}/{action}' => array (
        'route' => '{controller}/{action}'
    ),
        '{controller}/{action}/{id:\d+}' => array(

        'route' => '{controller}/{action}'
    ),

    'trang-chu' => array (
        'route' => 'home/default'
    ),
    'bo-xung-thong-tin' => array(
        'route' => 'Home/AskConfirm'
    ),

    'dat-hang-qua-link/{url:.*?}' => array (
        'route' => 'OrderLink/default'
    ),
    'dang-ky-thanh-cong' => array (
        'route' => 'ConnectFacebook/register_success'
    ),
    'dat-hang-qua-link' => array (
        'route' => 'OrderLink'
    ),
    'gio-hang' => array (
        'route' => 'Cart/default'
    ),

    'cong-cu-dat-hang-tren-trang' => array (
        'route' => 'home/page_bookmark'
    ),

    'dang-nhap' => array (
        'route' => 'login/login'
    ),
    'thoat' => array (
        'route' => 'login/logout'
    ),
    'dang-ky' => array(
        'route' => 'register/default'
    ),
    'chon-dich-vu' => array (
        'route' => 'chooseService/default'
    ),
    'dat-coc-don-hang' => array(
        'route' => 'orderDeposit/default'
    ),
    'ket-don-thanh-cong' => array(
        'route' => 'orderDeposit/finish'
    ),
    'don-hang-cho-thanh-toan' => array(
        'route' => 'orderDeposited/default'
    ),

    'don-hang-o-quang-chau' => array(
        'route' => 'orderGuangzhou/default'
    ),

    'khach-hang-o-quang-chau' => array(
        'route' => 'customerGuangzhou/default'
    )
);
return $r;