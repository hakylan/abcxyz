<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
 
<!-- Website Title --> 
<title>SeuDo</title>

<!-- Meta data for SEO -->
<meta name="description" content="">
<meta name="keywords" content="">

<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">-->


<!-- Template stylesheet -->                                                           
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<link href="bootstrap/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" media="all">
<link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet" type="text/css" media="all">
<link href="bootstrap/css/bootstrap-theme.min.min.css" rel="stylesheet" type="text/css" media="all">  
<link href="css/mytypo.css" rel="stylesheet" type="text/css" media="all">
<link href="css/header.css" rel="stylesheet" type="text/css" media="all">
<link href="css/owl.carousel.css" rel="stylesheet" type="text/css" media="all"> 
<link href="js/rate/rateit.css" rel="stylesheet" type="text/css" media="all"> 
<link href="css/owl.theme.css" rel="stylesheet" type="text/css" media="all">
<link href="css/cssloading.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"> 
<link href="css/footer.css" rel="stylesheet" type="text/css" media="all">
<link href="css/stylenote.css" rel="stylesheet" type="text/css" media="all">  
<link href="css/linkorder.css" rel="stylesheet" type="text/css" media="all"> 
<link href="css/bieuphi.css" rel="stylesheet" type="text/css" media="all"> 
 
  


<link rel="icon" href="images/icon-footer/LogoSeuDo1_ICO.ico">
 




<!-- Jquery and plugins -->               
<script type="text/javascript" src="js/jquery1.10.2.js"></script>
 
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="bootstrap/js/bootstrap-select.min.js"></script> 
 
<!--<script type="text/javascript" src="js/owl.carousel.min.js"></script> -->
<script type="text/javascript" src="js/rate/jquery.rateit.min.js"></script> 
<script type="text/javascript" src="js/jqueryui.js"></script>
<script type="text/javascript" src="js/owl.carousel.min.js"></script>  
<script type="text/javascript" src="js/masonry.pkgd.js"></script>
<script type="text/javascript" src="js/jquery.nicescroll.js"></script> 
<script type="text/javascript" src="js/lazy-load.js"></script> 
<!--<script type="text/javascript" src="js/jquery.carouFredSel-6.2.1-packed.js"></script> -->
<script type="text/javascript" src="js/jquery.carouFredSel-6.0.4-packed.js"></script>
<script type="text/javascript" src="js/seudo-frontend.js"></script> 
<script type="text/javascript" src="js/seudo-frontendv2.js"></script> 
<script type="text/javascript" src="js/slide-show.js"></script> 
 <script>
$(document).ready(function(){
  $(".click-block").click(function(){
    $(".content-ct-none").addClass("block"); 
    $(".click-block").addClass("none"); 
  }); 
  
});

</script>

<style>
      .lazy-load, .lazy-loaded {
        -webkit-transition: opacity 0.3s;
        -moz-transition: opacity 0.3s;
        -ms-transition: opacity 0.3s;
        -o-transition: opacity 0.3s;
        transition: opacity 0.3s;
        opacity: 0;
      }

      .lazy-loaded { opacity: 1; }

      .demo img { display: block; margin: 10px 0; }
    </style>
</head>
<body >

	<!-- Begin header -->
	<header id="header" class="header">
        <section class="container header-top">
            <div class="row">
                    <div class="pull-right top-header">
                        <div class="rate-tq">
                            <p class="normal">Tỉ giá: <span class="red-bold">1 Nhân dân tệ</span> = <span class="red-bold">3.500<sup>đ</sup></span></p>
                        </div>
                        <div class="pull-right register login item-top-header cart-img" >
                            <span class="arrow-header-cart"></span>
                            <span class="arow-count">12</span>
                            <span class="border"></span>
                        </div>
                        <div class="pull-right item-top-header accout dropdown my-dropdow" >
                            <a id="drop1" role="button" class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/page.html">
                            <span class="img-avatar"><img src="images/imgdemo/itemcart.jpg"></span> 
                            <span class="acc-usd">Luu Trong</span>
                            <span class="arow"></span>
                          </a>
                          <span class="border"></span>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="drop1"> 
                                <div class="acc-title">
                                    <span class="arow-tick"></span>
                                    <p class="italic"><span class="normal">Mã khách: </span> <span class="uppercase normal-blod"> Ha1234</span></p>
                                </div>
                                <ul class="acc-gd">
                                    <li> 
                                        <a href="#">Quản lý đơn hàng</a> 
                                    </li>
                                    <li>
                                        <a href="#">Lịch sử giao dịch</a>
                                        
                                    </li>
                                    
                                    <li>                                
                                        <a href="#">Nạp tiền</a>
                                    </li> 
                                </ul>
                                <span class="border-hr"></span>
                                <ul class="acc-gd">
                                    <li>
                                        <a href="#">Thông tin cá nhân</a>
                                    </li> 
                                    <li> 
                                        <a href="#">Đổi mật khẩu</a> 
                                    </li>
                                    <li>                                
                                        <a href="#">Thoát</a>
                                    </li> 
                                </ul> 
                                
                          </ul>
                        </div>
                        
                        <div class="pull-right register item-top-header" >
                            <a class="registerlink" href="#" data-target="#myModalregister" data-toggle="modal">Đăng ký</a>
                            <span class="border"></span>
                            <!-- Modal -->
                            <div class="modal fade" id="myModalregister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel"><p class="red-normal">Đăng ký</p></h4>
                                  </div>
                                  <div class="modal-body">
                                    <form action="#">
                                        <div class="regis-item">
                                        <div class="regis-item">
                                            <span class="normal name-label">Tài khoản<span class="red-normal"> *</span></span>
                                            <input id="exampleInput_name" class="inputregister form-control form-myinput pull-right" type="text" placeholder="Tài khoản" >
                                            <p class="red-normal">Tên đăng nhập đã được sư dụng vui lòng điền tên đăng nhập khác.</p>
                                            <div class="tooltipregister">
                                                <div class="arrow_box">
                                                <p class="normal">Sử dụng các ký tự latin (a-z) và ký tự gạch dưới</p>
                                                <p class="normal">Từ 3 đến 15 ký tự</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="regis-item name"> 
                                                <span class="normal name-label">Họ & tên</span>
                                                <input id="exampleInput" class="form-control form-myinput f-name" type="text" placeholder="Họ" >
                                                <span class="and">&</span>
                                                <input id="exampleInput" class="form-control form-myinput pull-right l-name" type="text" placeholder="Tên" >
                                            
                                             
                                        </div>
                                        <div class="regis-item">
                                            <span class="normal name-label">Email<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="inputregister form-control form-myinput" type="text" placeholder="Email" >
                                            <div class="tooltipregister">
                                                <div class="arrow_box">
                                                <p class="normal">Seudo sẽ gửi các thông báo tới email này.</p>
                                                <p class="normal">Vui lòng sử dụng email tồn tại.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="regis-item">
                                            <span class="normal name-label">Mật khẩu<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="form-control form-myinput" type="password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" >
                                            
                                        </div>
                                        
                                        <p class="normal">Với việc đăng ký tài khoản, tôi đã đồng ý với các điều khoản của seudo.vn</p> 
                                    </form>
                                  </div>
                                  <div class="modal-footer">
                                    <button class="btn btn-gray disable " type="button">Hủy </button>
                                    <button class="btn btn-blue" type="button">Đăng ký</button>
                                  </div>
                                  
                                </div><!-- /.modal-content -->
                              </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->  
                        </div>
                        <div class="pull-right register login item-top-header" >
                            <a class="registerlink" href="#" data-target="#myModallogin" data-toggle="modal">Đăng nhập</a>
                            <span class="border"></span>
                            <!-- Modal -->
                            <div class="modal login fade" id="myModallogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog reset-password-block">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel"><p class="red-normal">Đăng nhập</p><a href="#">Đăng ký</a></h4>
                                  </div>
                                  <div class="modal-body">
                                    <form action="#">
                                        <div class="regis-item">
                                            <span class="normal name-label">Tài khoản<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="form-control form-myinput" type="text" placeholder="Tài khoản hoặc email đã kích hoạt" >
                                            <p class="red-normal">Tên đăng nhập sai vui lòng nhập lại tên đăng nhập khác.</p>
                                        </div>
                                        
                                        <div class="regis-item">
                                            <span class="normal name-label">Mật khẩu<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="form-control form-myinput" type="password" placeholder="Mật khẩu" >
                                            
                                        </div>
                                        
                                        <div class="regis-item made">
                                            <p class="normal">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    Ghi nhớ đăng nhập
                                                </label>
                                            </p>
                                            <button class="btn btn-blue btn-lg btn-login pull-right" type="button">Đăng nhập</button>
                                        </div>
                                        
                                    </form>
                                  </div>
                                  <div class="modal-footer">
                                    <a class="pull-left click-resetpass" href="#">Quên mật khẩu ?</a>
                                    <p class="normal facebook pull-right">Đăng nhập bằng facebook<span class="icon-facebook">login facebook</span></p> 
                                    <div class="reset-password">
                                        <p class="normal email-text">Nhập mail bạn đăng ký</p>
                                        
                                        <div class="regis-item ">  
                                                <input id="exampleInput" class="form-control form-myinput inputregister" type="text" placeholder="Email" >
                                                <p class="red-normal">Địa chỉ mail chưa đúng.</p>
                                                <div class="tooltipregister">
                                                            <div class="arrow_box">
                                                            <p class="normal">Vui lòng nhập địa chỉ email đăng ký tài khoản SeuDo.vn của bạn.</p>
                                                            </div>
                                                </div> 
                                        </div>
                                        <button class="btn btn-blue" type="button">Lấy lại mật khẩu</button>
                                        <p class="normal loading-resetpass"><span class="alert alert-error"> Đang gửi..... </span></p>
                                        <p class="alert alert-info"> Email sent! If the email address you entered is registered at Freelancer.com, you'll receive an email with instructions on how to set a new password.  </p>
                                    </div>
                                  </div>
                                  
                                </div><!-- /.modal-content -->
        <!--                        loading-->
                                 <!--<div class="loading-face">
                                    <div id="facebookG">
                                        <div id="blockG_1" class="facebook_blockG">
                                        </div>
                                        <div id="blockG_2" class="facebook_blockG">
                                        </div>
                                        <div id="blockG_3" class="facebook_blockG">
                                        </div>
                                        </div>
                                 </div>-->
        <!--                        end loading-->
                              </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->    
                        </div>
                        <div class="pull-right register login item-top-header" >
                            <a href="#">Hướng dẫn</a>
                            <span class="border"></span>
                        </div>
                        <div class="pull-right register login item-top-header price" >
                            <span >Số dư:</span><span class="blueprice">+2.000.000<sup>đ</sup></span>
                            <span class="border"></span>
                        </div>
                        <div class="pull-right register login item-top-header hotline" >
                            <span >Hotline: 09999999</span>
                        </div>
                        
                    </div>       
                     
            </div>
            <div class="logo">
                <a href="#"><img src="images/icon-footer/logoSeuDo.png"></a>
            </div>
        </section>
        
        <section class="container search-cart">
            <div class="row">
                <div class="header-cart pull-right">
                    <span class="arrow-header-cart"></span>
                    <p class="normal"><span class="normal-blod">0</span> sản phẩm</p>
                    <p class="red-normal">123.000.000<sup>đ</sup></p>
                    <span class="arrow-next-header-cart"></span>
                </div>
                <div class="header-search pull-right">
                    <input id="exampleInput" class="form-control form-myinput" type="text" placeholder="Nhập link sản phẩm bạn muốn đặt từ taobao.com, tmall.com, 1688.com và eely.com....">
                    <span class="uppercase normal-blod">đặt hàng</span>
                </div>
            </div>
        </section>
        
        <section class=" header-top scoll">
            <div class="container">
                <div class="row">
                    <div class="rate-scoll">
                        <p class="normal"><span class="red-bold">1<sup>ndt</sup></span> = <span class="red-bold">3.500<sup>đ</sup></span></p>
                    </div>
                    
                    <div class="pull-right top-header">
                        <div class="pull-right register login item-top-header cart-img" >
                            <span class="arrow-header-cart"></span>
                            <span class="arow-count">12</span>
                            <span class="border"></span>
                        </div>
                        <div class="pull-right item-top-header accout dropdown my-dropdow" >
                            <a id="drop1" role="button" class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/page.html">
                            <span class="img-avatar"><img src="images/imgdemo/itemcart.jpg"></span> 
                            <span class="acc-usd">Luu Trong</span>
                            <span class="arow"></span>
                          </a>
                          <span class="border"></span>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="drop1"> 
                                <div class="acc-title">
                                    <span class="arow-tick"></span>
                                    <p class="italic"><span class="normal">Mã khách: </span> <span class="uppercase normal-blod"> Ha1234</span></p>
                                </div>
                                <ul class="acc-gd">
                                    <li> 
                                        <a href="#">Quản lý đơn hàng</a> 
                                    </li>
                                    <li>
                                        <a href="#">Lịch sử giao dịch</a>
                                        
                                    </li>
                                    
                                    <li>                                
                                        <a href="#">Nạp tiền</a>
                                    </li> 
                                </ul>
                                <span class="border-hr"></span>
                                <ul class="acc-gd">
                                    <li>
                                        <a href="#">Thông tin cá nhân</a>
                                    </li> 
                                    <li> 
                                        <a href="#">Đổi mật khẩu</a> 
                                    </li>
                                    <li>                                
                                        <a href="#">Thoát</a>
                                    </li> 
                                </ul> 
                                
                          </ul>
                        </div>
                        
                        <div class="pull-right register item-top-header" >
                            <a class="registerlink" href="#" data-target="#myModalregister" data-toggle="modal">Đăng ký</a>
                            <span class="border"></span>
                            <!-- Modal -->
                            <div class="modal fade" id="myModalregister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel"><p class="red-normal">Đăng ký</p></h4>
                                  </div>
                                  <div class="modal-body">
                                    <form action="#">
                                        <div class="regis-item">
                                            <span class="normal name-label">Tài khoản<span class="red-normal"> *</span></span>
                                            <input id="exampleInput_name" class="inputregister form-control form-myinput pull-right" type="text" placeholder="Tài khoản" >
                                            <p class="red-normal">Tên đăng nhập đã được sư dụng vui lòng điền tên đăng nhập khác.</p>
                                            <div class="tooltipregister">
                                                <div class="arrow_box">
                                                <p class="normal">Sử dụng các ký tự latin (a-z) và ký tự gạch dưới</p>
                                                <p class="normal">Từ 3 đến 15 ký tự</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="regis-item name"> 
                                                <span class="normal name-label">Họ & tên</span>
                                                <input id="exampleInput" class="form-control form-myinput f-name" type="text" placeholder="Họ" >
                                                <span class="and">&</span>
                                                <input id="exampleInput" class="form-control form-myinput pull-right l-name" type="text" placeholder="Tên" >
                                            
                                             
                                        </div>
                                        <div class="regis-item">
                                            <span class="normal name-label">Email<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="inputregister form-control form-myinput" type="text" placeholder="Email" >
                                            <div class="tooltipregister">
                                                <div class="arrow_box">
                                                <p class="normal">Seudo sẽ gửi các thông báo tới email này.</p>
                                                <p class="normal">Vui lòng sử dụng email tồn tại.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="regis-item">
                                            <span class="normal name-label">Mật khẩu<span class="red-normal"> *</span></span>
                                            <input id="exampleInput" class="form-control form-myinput" type="password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" >
                                            
                                        </div>
                                        
                                        <p class="normal">Với việc đăng ký tài khoản, tôi đã đồng ý với các điều khoản của seudo.vn</p> 
                                    </form>
                                  </div>
                                  <div class="modal-footer">
                                    <button class="btn btn-gray disable " type="button">Hủy </button>
                                    <button class="btn btn-blue" type="button">Đăng ký</button>
                                  </div>
                                  
                                </div><!-- /.modal-content -->
                              </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->  
                        </div>
                        <div class="pull-right register login item-top-header" >
                            <a class="registerlink" href="#" data-target="#myModallogin" data-toggle="modal">Đăng nhập</a>
<!--                            <span class="border"></span>-->
                            <!-- Modal -->
                            <div class="modal login fade" id="myModallogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog reset-password-block">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel"><p class="red-normal">Đăng nhập</p><a href="#">Đăng ký</a></h4>
                                  </div>
                                  <div class="modal-body">
                                    <form action="#">
                                        <div class="regis-item">
                                            <input id="exampleInput" class="form-control form-myinput" type="text" placeholder="Tài khoản hoặc email đã kích hoạt" >
                                            <p class="red-normal">Tên đăng nhập sai vui lòng nhập lại tên đăng nhập khác.</p>
                                        </div>
                                        
                                        <div class="regis-item">
                                            <input id="exampleInput" class="form-control form-myinput" type="password" placeholder="Mật khẩu" >
                                            
                                        </div>
                                        
                                        <div class="regis-item made">
                                            <p class="normal">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    Ghi nhớ đăng nhập
                                                </label>
                                            </p>
                                            <button class="btn btn-blue btn-lg btn-login pull-right" type="button">Đăng nhập</button>
                                        </div>
                                        
                                    </form>
                                  </div>
                                  <div class="modal-footer">
                                    <a class="pull-left click-resetpass" href="#">Quên mật khẩu ?</a>
                                    <p class="normal facebook pull-right">Đăng nhập bằng facebook<span class="icon-facebook">login facebook</span></p> 
                                    <div class="reset-password">
                                        <p class="normal email-text">Nhập mail bạn đăng ký</p>
                                        
                                        <div class="regis-item ">  
                                                <input id="exampleInput" class="form-control form-myinput inputregister" type="text" placeholder="Email" >
                                                <p class="red-normal">Địa chỉ mail chưa đúng.</p>
                                                <div class="tooltipregister">
                                                            <div class="arrow_box">
                                                            <p class="normal">Vui lòng nhập địa chỉ email đăng ký tài khoản SeuDo.vn của bạn.</p>
                                                            </div>
                                                </div> 
                                        </div>
                                        <button class="btn btn-blue" type="button">Lấy lại mật khẩu</button>
                                        <p class="normal loading-resetpass"><span class="alert alert-error"> Đang gửi..... </span></p>
                                        <p class="alert alert-info"> Email sent! If the email address you entered is registered at Freelancer.com, you'll receive an email with instructions on how to set a new password.  </p>
                                    </div>
                                  </div>
                                  
                                </div><!-- /.modal-content -->
        <!--                        loading-->
                                 <!--<div class="loading-face">
                                    <div id="facebookG">
                                        <div id="blockG_1" class="facebook_blockG">
                                        </div>
                                        <div id="blockG_2" class="facebook_blockG">
                                        </div>
                                        <div id="blockG_3" class="facebook_blockG">
                                        </div>
                                        </div>
                                 </div>-->
        <!--                        end loading-->
                              </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->    
                        </div>
                        
                        
                    </div> 
                    
                    <div class="header-search pull-right">
                        <input type="text" placeholder="Nhập link sản phẩm bạn muốn đặt từ taobao.com, tmall.com, 1688.com và eely.com...." class="form-control form-myinput" id="exampleInput">
                        <span class="uppercase normal-blod">đặt hàng</span>
                    </div>      
                </div>
                
                <div class="logo">
                    <a href="#"><img src="images/icon-footer/menu_mini.jpg"></a>
                </div>    
            </div>
            
        </section>
        
    </header>
    <!--<section class="seu-menu">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8 megamenu-left">
                        <ul class="seu-megamenu">
                            <li class="item-megamenu active">
                                <a href="#"><span class="uppercase home"><span class="icon"></span>trang chủ</span></a>
                            </li>
                            <li class="item-megamenu">
                                <a href="#"><span class="uppercase sevice"><span class="icon"></span> giới thiệu</span></a>
                            </li>
                            <li class="item-megamenu">
                                <a href="#"><span class="uppercase"> biểu phí</span></a>
                            </li>
                            <li class="item-megamenu">
                                <a href="#"><span class="uppercase"> hướng dẫn</span></a>
                            </li> 
                        </ul>
                    </div>
                    <div class="megamenu-right pull-right">
                        <div class="seu-facemenu">
                                <a href="#"><span class="uppercase home"><span class="icon"></span>cộng đồng</span></a>
                            
                        </div>
                    </div>
                </div>
            </div>
    </section>-->
    
    <section class="seudo-menu">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8 seu-left">
                        <ul class="seudo-megamenu">
                            <li class="item-megamenu active">
                                <a href="#"><span class="uppercase home"><span class="icon"></span>trang chủ</span></a>
                            </li>
                            <li class="item-megamenu ">
                                <a href="#"><span class="uppercase sevice"><span class="icon"></span> giới thiệu</span></a>
                            </li>
                            <li class="item-megamenu">
                                <a href="#"><span class="uppercase"> biểu phí</span></a>
                            </li>
                            <li class="item-megamenu">
                                <a href="#"><span class="uppercase"> hướng dẫn</span></a>
                            </li> 
                        </ul>
                    </div>
                    <div class="seu-right pull-right">
                        <div class="seu-facemenu">
                                <a href="#"><span class="icon"></span><span class="uppercase home">công cụ đặt hàng</span></a>
                        </div>
                    </div>
                </div>
            </div>
    </section> 

	<!-- End header -->
