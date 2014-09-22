<?php
$r = array(
  '__urlSuffix__'=>'.html',
  '__remap__'=> array(
     'route'=>'user/default'
  ),
  '/'=>array(
     'route'=>'user/default'
  ),
  '{controller}'=>array(
    'route'=>'{controller}/default'
  ),
  '{controller}/{action}'=>array(
    'route'=>'{controller}/{action}'
  ),
  '{controller}/{action}/{id:\d+}'=>array(
    'route'=>'{controller}/{action}'
  ),
    'dia-chi-nhan-hang' => array(
        'route' => 'UserAddress/default'
    ),
    'don-hang/huy-het-hang' => array(
        'route' => 'OrderDeleted/default'
    ),
    'don-hang/ket-thuc' => array(
        'route' => 'OrderGet/default'
    ),
    'don-hang/dang-khieu-nai' => array(
        'route' => 'OrderComplaint/default'
    ),
    'don-hang/cho-thanh-toan' => array(
        'route' => 'OrderInit/default'
    ),
    'don-hang/xac-nhan' => array(
        'route' => 'OrderConfirm/default'
    ),
    'lich-su-giao-dich' => array(
        'route' => 'UserTransaction/default'
    ),
    'thong-tin' => array(
        'route' => 'user/detail'
    ),
    'don-hang/hoat-dong' => array(
        'route' => 'order_active/default'
    ),
    'bo-xung-thong-tin' => array(
        'route' => 'user/ask_confirm'
    ),
    'doi-mat-khau' => array(
        'route' => 'user/password_profile'
    ),
    'nap-tien' => array(
        'route' => 'user_transaction/transaction_form'
    ),
    'chi-tiet-don-hang/{id:\d+}'=>array(
        'route' => 'order_detail/default'
    ),

    'khieu-nai-san-pham/{order_id:\d+}/{item_id:\d+}'=>array(
        'route' => 'complaint2/default'
    ),
    'khieu-nai-san-pham/{order_id:\d+}/{item_id:\d+}/{step:\d+}'=>array(
        'route' => 'complaint2/default'
    ),
    'chi-tiet-khieu-nai-san-pham/{order_id:\d+}/{item_id:\d+}'=>array(
        'route' => 'complaint2/detail'
    ),
    'danh-sach-khieu-nai'=>array(
        'route' => 'complaint2/list'
    ),
    'diem-tich-luy' => array(
        'route' => 'accumulation_score/default'
    ),

    'thong-bao' => array(
    'route' => 'NotificationController/default'
),

);
return $r;