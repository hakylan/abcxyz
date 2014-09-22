<?php

class UserRank
{
    public static $user_rank = array(

        'member'=>array(
            'title'=>'Thành viên',
            'discount_buying'=>0,
            'discount_checking'=>0,
            'discount_shipping_nation'=>0,
        ),
        'vip1'=>array(
            'title'=>'Vip 1',
            'discount_buying'=>5,
            'discount_checking'=>5,
            'discount_shipping_nation'=>1
        ),
        'vip2'=>array(
            'title'=>'Vip 2',
            'discount_buying'=>10,
            'discount_checking'=>10,
            'discount_shipping_nation'=>1
        ),
        'vip3'=>array(
            'title'=>'Vip 3',
            'discount_buying'=>15,
            'discount_checking'=>15,
            'discount_shipping_nation'=>2,
            'checking_fixed_fee'=>0,
        ),
        'vip4'=>array(
            'title'=>'Vip 4',
            'discount_buying'=>20,
            'discount_checking'=>20,
            'discount_shipping_nation'=>3,
            'checking_fixed_fee'=>0,
        ),
        'vip5'=>array(
            'title'=>'Vip 5',
            'discount_buying'=>25,
            'discount_checking'=>25,
            'discount_shipping_nation'=>4,
            'checking_fixed_fee'=>0,
        ),
        'vip6'=>array(
            'title'=>'Vip 6',
            'discount_buying'=>30,
            'discount_checking'=>30,
            'discount_shipping_nation'=>5,
            'checking_fixed_fee'=>0,
        )
    );

    public static $userLevel = array(
        'member' =>0,
        'vip1' => 1,
        'vip2' => 2,
        'vip3'=>3,
        'vip4'=>4,
        'vip5'=>5,
        'vip6'=>6
    );
}