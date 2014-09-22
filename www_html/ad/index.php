<?php
require realpath('../../bootstrap.php');
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../../apps/Home/Config/main.cfg.php');
$env = \Flywheel\Base::ENV_DEV;
$app = \Flywheel\Base::createWebApp($config, $env, true);

$userObj = \BaseAuth::getInstance()->getUser();
if($userObj instanceof \Users ){
    $user = $userObj->toArray();
}else{
    $user["username"] = "";
    $user["email"] = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Landdingpage.</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style-typo.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ga.js"></script>
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <script type="text/javascript">
        var zopim_username = '<?php echo $user['username'] ?>';
        var zopim_email = '<?php echo $user['email'] ?>';
    </script>

    <!--Start of Zopim Live Chat Script-->

    <script type="text/javascript">

        window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=

            d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.

            _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');

            $.src='//v2.zopim.com/?2MF4BOB41gzfdtkGI2MXp4qHKQPiKHRp';z.t=+new Date;$.

                type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');

    </script>

    <!--End of Zopim Live Chat Script-->

    <script type="text/javascript" src="js/zopim.js?v=1.1"></script>
    <script type="text/javascript" src="js/getUsernameEmailZopim.js?v=1.1"></script>

</head>
<body>
    <section class="container header">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <div class="module-float logo">
                    <a href="#"><img src="images/logo.png"></a>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                <div class="module-float baner">
                    <img src="images/banner_landingpage.png">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="module-float module-title">
                    <p class="title">Bạn làm kinh doanh & cần một phương thức nhập hàng hiệu quả?</p>
                    <p class="module-ct">Seudo.vn cung cấp giải pháp Nhập hàng trực tuyến từ Trung Quốc tiện lợi với chi phí thấp. Bạn chỉ cần ngồi tại nhà còn SeuDo.vn sẽ thực hiện tất cả các hoạt động nhập hàng (mua hàng, kiểm hàng, vận chuyển....) giúp bạn.</p>
                    <p class="module-bt">SeuDo giúp gì hoạt động kinh doanh của bạn?</p>

                </div>
            </div>
        </div>
        <div class="pic-arrow">
                <div class="text-center">
                    <div class="arrow-pic"></div>
                </div>
        </div>

    </section>

    <section class="body-step1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float logo-left">
                        <img src="images/step1-1.jpg">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-ct-step1" style="padding-left: 50px;">
                    <div class="module-float">
                        <div class="module-title">Chủ động về nguồn hàng</div>
                    </div>
                    <div class="module-float">
                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Hàng chục ngàn shop trên các web bán buôn lớn nhất TQ (Alibaba, 1688, eelly...) dành riêng cho khách hàng, kinh doanh.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Hàng triệu sản phẩm sản xuất tại đại công xưởng Trung Quốc.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Tư vấn tìm nguồn hàng miễn phí</p>
                        </div>

                    </div>
                    <div class="module-float submit">
                        <a href="https://docs.google.com/forms/d/10lDT_Lxw5qX1mfRfu_q_EBdhvoYmAq4Vuk3SHF6sAKY/viewform?fbzx=-2600927113112277285">
                            <button value="Chấp nhận" class="button-step1" id="" data-message="Chấp nhận"><span>Click để tư vấn tìm nguồn hàng</span></button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="body-step1 v2">
        <div class="container">
            <div class="row">

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-ct-step1">
                    <div class="module-float">
                        <div class="module-title">Chủ động về thời gian</div>
                    </div>
                    <div class="module-float">
                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Dành thời gian nhập hàng theo cách trước đây cho công việc bán hàng, mang lại doanh thu cao hơn.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Chủ động quản lý từng mốc tình trang vận chuyển trên website và qua mobile ( thông báo tức thời qua SMS, ứng dụng Smartphone...).</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Cung cấp giải pháp vận chuyển thường và vận chuyển nhanh để tối ưu thời gian, chi phí.</p>
                        </div>

                    </div>
                    <div class="module-float"></div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float logo-right">
                        <img src="images/step1v2.jpg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="body-step1 v3">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="module-float logo-left">
                        <img src="images/step1v3.jpg">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 module-ct-step1" style="padding-left: 50px;">
                    <div class="module-float">
                        <div class="module-title">Chủ động về tài chính</div>
                    </div>
                    <div class="module-float">
                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Tiết kiệm chi phí đi lại, ăn nghỉ... khi đi nhập hàng trực tiếp.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Giảm thiểu rủi ro mất tiền khi mang tiền theo người.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>lựa chọn linh hoạt các loạt chi phí để phù hợp với đặc thù kinh doanh của mình.</p>
                        </div>

                        <div class="module-item">
                            <span class="check"><i class="fa fa-check"></i></span>
                            <p>Hưởng chính sách đặt cọc, tỷ giá...chỉ có tại các đơn vị nhập hàng có tiềm lực.</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="body-step2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-title">
                        <p>kinh nghiệm của chúng tôi</p>
                    </div>
                </div>

            </div>
            <div class="pic-arrow">
                <div class="text-center">
                    <div class="arrow-pic"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="body-step3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-item">
                        <div class="number">
                            <div class="ct">05</div>
                        </div>
                        <p>Năm hoạt động tiên phong trong lĩnh vực nhập hàng trực tuyến</p>
                    </div>
                    <div class="module-item">
                        <div class="number">
                            <div class="ct">857</div>
                        </div>
                        <p>Khách hàng là các shop bán hàng đã được phục vụ</p>
                    </div>
                    <div class="module-item">
                        <div class="number">
                            <div class="ct">10663</div>
                        </div>
                        <p>Đối tác cung cấp hàng hóa đã hợp tác</p>
                    </div>
                    <div class="module-item">
                        <div class="number">
                            <div class="ct">1.403.478</div>
                        </div>
                        <p>Sản phẩm đã được nhập</p>
                    </div>
                </div>

            </div>
            <div class="pic-arrow">
                <div class="text-center">
                    <div class="arrow-pic"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="body-step4">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module-title">
                        <p>Bạn đã sẵn sàng ?</p>
                    </div>
                    <div class="module-ct">
                        <p>Tham gia chương trình</p>
                        <p>" Miễn phí dịch vụ khi nhập hàng thử "</p>
                        <p>Ngay hôm nay</p>
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <div class="module-float bottom">
                        <img src="images/bottom.jpg">
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <div class="module-float bottomv2">
                        <div class="contact">
                            <p class="title">Liên hệ:</p>
                            <p><i class="fa fa-phone"></i>&nbsp; 01204888886</p>
                            <p><i class="fa fa-envelope-o"></i>&nbsp; <a href="mailto:tuvan@seudo.vn">tuvan@seudo.vn</a></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</body>
</html>