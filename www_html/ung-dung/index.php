<?php
require __DIR__ .'/../../bootstrap.php';
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../../apps/Backend/Config/main.cfg.php');
$env = \Flywheel\Base::ENV_PRO;

$app = \Flywheel\Base::createWebApp($config, $env, true);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ứng dụng thông báo trên mobile, tablet.</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style-typo.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>
    <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>-->
    <!--<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="js/check_browse.js"></script>
    <script src="js/smoothscroll.js"></script>
    <?php require_once GLOBAL_PATH . '/include/GA.php'; ?>
</head>
<body>
<div id="myCarousel" class="carousel slide module-float" data-ride="carousel">
<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="logo module-float">
                    <a href="http://seudo.vn">
                        <img src="images/app-logo.png">

                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="menu module-float">
                    <ul class="menu-ct nav nav-pills nav-justified">
                        <li class="active" data-target="#myCarousel" data-slide-to="0">
                            <a href="javascript:void(0)">Công cụ đặt hàng</a>
                        </li >
                        <li data-target="#myCarousel" data-slide-to="1">
                            <a href="#">Mobileapp</a>
                        </li>
                        <li>
                            <a href="http://seudo.vn" target="_blank">Sếuđỏ.vn</a>
                        </li>
                    </ul>

                </div>
            </div>
        </div>


    </div>
</section>
<div class="carousel-inner">
<div class="item active pcapp">
    <section class="main-custom custom-one v2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 left">
                    <div class="module-float">
                        <div class="module-ct">
                            <p>Để có thể biết rõ</p>
                        </div>
                        <div class="module-title"><p>Cách sử dụng addon</p></div>
                        <div class="module-ct">
                            <p>Hãy xem video hướng dẫn</p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float">
                        <embed width="100%" height="370" type="application/x-shockwave-flash" src="https://www.youtube.com/v/AkExtBPoa6U">

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-custom custom-two v2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float left">
                        <p>addon</p>
                        <p>chrome & cờ rôm+</p>
                        <span class="arrow"></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float right">
                        <a href="http://seudo.vn/cong-cu-dat-hang-tren-trang" class="module-float" target="_blank" style="color: #222;margin: 0">
                            <p>đặt hàng</p>
                            <p>Trên thanh đánh dấu</p>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-custom custom-there v2">
        <div class="arrow-top"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-float">
                        <div class="module-title"><p>addon</p></div>
                        <div class="module-ct">
                            <p>Sử dụng trên trình duyệt chrome & cờ rôm + </p>
                            <p>nhanh chóng - chính xác - hiệu quả</p>
                        </div>
                    </div>
                    <div class="module-float img-order">
                        <img src="images/dathang.jpg">
                    </div>
                </div>

            </div>

        </div>
    </section>

    <section class="main-custom custom-fore v2">
        <div class="container">
            <div class="row top">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-float">
                        <div class="module-title"><p>Với addon sếuđỏ chúng tôi sẽ giúp bạn</p></div>
                    </div>
                </div>

            </div>
            <div class="row step">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float">
                        <div class="module-title"><p>Tiết kiệm thời gian và tăng cơ hội kinh doanh</p></div>
                        <span class="arrow font-user">1</span>
                    </div>
                    <div class="module-float">
                        <div class="module-title"><p>Đặt hàng nhanh chóng thuận tiện và chính xác</p></div>
                        <span class="arrow font-user">2</span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float">
                        <div class="module-title"><p>Form đặt hàng hiển thị sẵn khi vào trang chi tiết</p></div>
                        <span class="arrow font-user">3</span>
                    </div>
                    <div class="module-float">
                        <div class="module-title"><p>Hỗ trợ dịch tự động dịch từ tiếng Trung sang tiếng Việt</p></div>
                        <span class="arrow font-user">4</span>
                    </div>
                </div>

            </div>

            <div class="row step1">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-float">
                        <img src="images/dathang-step1.png">
                    </div>
                </div>
            </div>
            <div class="row step2">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-float">
                        <div class="module-title">
                            <p>Sử dụng trên trình duyệt chrome & cờ rôm + <span>(cốc cốc)</span></p>
                        </div>
                        <div class="module-ct">
                            <p>Cài đặt nhanh chóng, hạn chế tối đa việc cài đặt lại - Tự động cập nhật khi có phiên bản mới.</p>
                        </div>
                    </div>
                    <div class="module-float link">
                        <a class="link-brow chrome _link_chrome" target="_blank"
                           href="https://chrome.google.com/webstore/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-s%E1%BA%BFu-%C4%91%E1%BB%8F/limmgihpbambgjgfbdeannfbfcfpodfk?authuser=2">chrome</a>
                        <a class="link-brow corom _link_coccoc" target="_blank" href="https://chrome.google.com/webstore/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-s%E1%BA%BFu-%C4%91%E1%BB%8F/limmgihpbambgjgfbdeannfbfcfpodfk?authuser=2">co rom</a>
                    </div>
                    <div class="module-float link-brow">
                        <img src="images/brow-link.png">
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>


<div class="item mobileapp">

    <section class="main-custom custom-one">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 left">
                    <div class="module-float">
                        <div class="module-title"><p>Ứng dụng thông báo trên mobile, tablet</p></div>
                        <div class="module-ct">
                            <p>Nhằm đáp ứng nhu cầu theo dõi thông tin đơn hàng và tài khoản của Quý khách, ngày 11/06 SeuDo.vn ra mắt ứng dụng thông báo trên Iphone, Ipad, Android và Winphone..</p>
                        </div>
                        <div class="module-float item-save">
                            <div class="button-save">
                                <a href="#space3" id="download"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 right">
                    <div class="module-float">
                        <img src="images/img-ct1.png">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-custom custom-two">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float media">
                        <div class="pull-left" href="#">
                            <img src="images/gioithieu-1.png" style="width: 78px; height: 78px;" class="media-object" >
                        </div>
                        <div class="media-body">
                            <p>Cập nhật những thay đổi của đơn hàng</p>
                        </div>

                    </div>
                    <div class="module-float media">
                        <div class="pull-left" href="#">
                            <img src="images/gioithieu-2.png" style="width: 78px; height: 78px;" class="media-object" >
                        </div>
                        <div class="media-body">
                            <p>Cập nhật tình trạng vận chuyển của đơn hàng</p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float media">
                        <div class="pull-left" href="#">
                            <img src="images/gioithieu-3.png" style="width: 78px; height: 78px;" class="media-object" >
                        </div>
                        <div class="media-body">
                            <p>Thông báo trao đổi mới trên đơn hàng</p>
                        </div>

                    </div>
                    <div class="module-float media">
                        <div class="pull-left" href="#">
                            <img src="images/gioithieu-4.png" style="width: 78px; height: 78px;" class="media-object" >
                        </div>
                        <div class="media-body">
                            <p>Thông báo tiền nạp vào tài khoản</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-custom custom-there space3"  >
        <div class="container">
            <div class="module-float" id="space3">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="module-float">
                            <div class="module-title"><p>Tải ứng dụng</p></div>
                            <div class="module-ct">
                                <p>Bạn có thể tải ứng dụng tương ứng với thiết bị bạn sử dụng</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row link-app">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="module-float apo">
                            <a class="module-float" href="javascript:void(0)">
                                <span class="arrow"><i class="fa fa-apple"></i></span>
                                <p>Sắp ra mắt...</p>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="module-float android">
                            <a class="module-float" href="seudo.apk">
                                <span class="arrow"><i class="fa fa-android"></i></span>
                                <p>Android</p>
                                <span class="code"><img src="images/Androind.jpg"></span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="module-float win">
                            <a class="module-float" href="seudo.xap">
                                <span class="arrow"><i class="fa fa-windows"></i></span>
                                <p>Window</p>
                                <p>Phone</p>
                                <span class="code"><img src="images/Windowphone.jpg"></span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 col-sm-12 col-xs-12">
                    <img class="pc-full" src="images/note.png">
                    <img class="mobile-full" src="images/note2.png">
                </div>
            </div>
        </div>
    </section>
</div>


</div>

<section class="footer">
    <div class="container">
        <p class="copyright" id="copyright">Copyright © 2014 SeuDo. All rights reserved. Designed & developed by SeuDo.</p>
    </div>
</section>
<script type="text/javascript">
    $(document).ready( function() {
        if(navigator.appVersion.indexOf("iOS") == -1 && navigator.appVersion.indexOf("Android") == -1
            && navigator.appVersion.indexOf("Windows Phone") == -1){
            $('#myCarousel').carousel({
                interval:   false
            });

            var clickEvent = false;
            $('#myCarousel').on('click', '.nav a', function() {
                clickEvent = true;
                $('.nav li').removeClass('active');
                $(this).parent().addClass('active');
            }).on('slid.bs.carousel', function(e) {
                if(!clickEvent) {
                    var count = $('.nav').children().length -1;
                    var current = $('.nav li.active');
                    current.removeClass('active').next().addClass('active');
                    var id = parseInt(current.data('slide-to'));
                    if(count == id) {
                        $('.nav li').first().addClass('active');
                    }
                }
                clickEvent = false;
            });
        }

    });
</script>
</div>
</body>
</html>