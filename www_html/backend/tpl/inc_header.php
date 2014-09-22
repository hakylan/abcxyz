<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<!-- Website Title -->
<title>Backend SeuDo</title>

<!-- Meta data for SEO -->
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

<!-- Favicons -->

<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/icons/apple-touch-icon-144-precomposed.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/icons/apple-touch-icon-114-precomposed.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/icons/apple-touch-icon-72-precomposed.png" />
<link rel="apple-touch-icon-precomposed" href="assets/images/icons/apple-touch-icon-57-precomposed.png" />
<link rel="shortcut icon" href="assets/images/icons/favicon.png" />

<!--[if lt IE 9]>
  <script src="assets/js/minified/core/html5shiv.min.js"></script>
  <script src="assets/js/minified/core/respond.min.js"></script>
<![endif]-->

<!-- Fides Admin CSS Core -->

<link rel="stylesheet" type="text/css" href="assets/css/aui-production.min.css" />

<!-- Theme UI -->

<link id="layout-theme" rel="stylesheet" type="text/css" href="assets/css/dark-blue.min.css" />

<!-- Fides Admin Responsive -->

<link rel="stylesheet" type="text/css" href="assets/css/common.min.css" />
<!--        <link rel="stylesheet" type="text/css" href="assets/themes/minified/fides/bootstrap.min.css" />-->
<link rel="stylesheet" type="text/css" href="assets/css/responsive.min.css" />


<link rel="stylesheet" type="text/css" href="assets/css/stylev1.css" />
<link rel="stylesheet" type="text/css" href="assets/css/style-typo.css" />
<!--<link rel="stylesheet" type="text/css" href="assets/css/style-backendv1.css" />-->
<link rel="stylesheet" type="text/css" href="assets/fonts/roboto/roboto.css" />
<link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome/css/font-awesome.min.css" />

<!-- Fides Admin JS -->
<script type="text/javascript" src="assets/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/tooltip.js"></script>
<script type="text/javascript" src="assets/js/jquery-scrolltofixed.js"></script> 
<script type="text/javascript" src="assets/bootstrap/js/bootstrap-select.min.js"></script>                                                           
<script type="text/javascript" src="assets/js/seudo-quantity.js"></script>                                                           
<script type="text/javascript" src="assets/js/seudo-backendv1.js"></script>

</head>
<body >


        <div id="loading" class="ui-front loader ui-widget-overlay bg-white opacity-100">
            <img src="assets/images/loader-dark.gif" alt="" />
        </div>

        <div id="page-wrapper" class="demo-example">

            <div class="theme-customizer">
                <a href="javascript:;" class="change-theme-btn" title="Change theme">
                    <i class="glyph-icon icon-cog"></i>
                </a>
                <div class="theme-wrapper">

                    <div class="popover-title">Boxed layout options</div>
                    <div class="pad10A clearfix">
                        <a class="fluid-layout-btn hidden bg-blue-alt medium btn" href="javascript:;" title=""><span class="button-content">Full width layout</span></a>
                        <a class="boxed-layout-btn bg-blue-alt medium btn" href="javascript:;" title=""><span class="button-content">Boxed layout</span></a>
                    </div>
                    <div class="popover-title">Boxed layout backgrounds</div>
                    <div class="pad10A clearfix">
                        <a href="javascript:;" class="choose-bg" boxed-bg="#000" style="background: #000;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#333" style="background: #333;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#666" style="background: #666;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#888" style="background: #888;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#383d43" style="background: #383d43;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#fafafa" style="background: #fafafa; border: #ccc solid 1px;" title=""></a>
                        <a href="javascript:;" class="choose-bg" boxed-bg="#fff" style="background: #fff; border: #eee solid 1px;" title=""></a>
                    </div>
                    <div class="popover-title">Color schemes</div>
                    <div class="pad10A clearfix change-layout-theme">
                        <p class="font-gray-dark font-size-11 pad0B">More color schemes will be available soon!</p>
                        <div class="divider mrg10T mrg10B"></div>
                        <a href="javascript:;" class="choose-theme" layout-theme="dark-blue" title="">
                            <span style="background: #2381E9;"></span>
                        </a>
                        <a href="javascript:;" class="choose-theme opacity-30 mrg15R" layout-theme="white-blue" title="">
                            <span style="background: #2381E9;"></span>
                        </a>
                        <a href="javascript:;" class="choose-theme" layout-theme="dark-green" title="D">
                            <span style="background: #78CE12;"></span>
                        </a>
                        <a href="javascript:;" class="choose-theme opacity-30 mrg15R" layout-theme="white-green" title="D">
                            <span style="background: #78CE12;"></span>
                        </a>
                        <a href="javascript:;" class="choose-theme" layout-theme="dark-orange" title="">
                            <span style="background: #FF6041;"></span>
                        </a>
                        <a href="javascript:;" class="choose-theme opacity-30 mrg15R" layout-theme="white-orange" title="">
                            <span style="background: #FF6041;"></span>
                        </a>
                    </div>

                </div>
            </div>

            <div id="page-header" class="clearfix">
                <div id="header-logo">
                    <a href="javascript:;" class="tooltip-button" data-placement="bottom" title="Close sidebar" id="close-sidebar">
                        <i class="glyph-icon icon-caret-left"></i>
                    </a>
                    <a href="javascript:;" class="tooltip-button hidden" data-placement="bottom" title="Open sidebar" id="rm-close-sidebar">
                        <i class="glyph-icon icon-caret-right"></i>
                    </a>
                    <a href="javascript:;" class="tooltip-button hidden" title="Navigation Menu" id="responsive-open-menu">
                        <i class="glyph-icon icon-align-justify"></i>
                    </a>
                    Fides Admin <i class="opacity-80">1.1</i>
                    <div class="logo-seudo">
                        <a href="#"><img src="assets/images/menu_mini.jpg"></a>
                    </div>
                </div>
                <div class="hide" id="black-modal-60" title="Modal window example">
                    <div class="pad20A">

                        <div class="infobox notice-bg">
                            <div class="bg-azure large btn info-icon">
                                <i class="glyph-icon icon-bullhorn"></i>
                            </div>
                            <h4 class="infobox-title">Modal windows</h4>
                            <p>Thanks to the solid modular Fides Admin arhitecture, modal windows customizations are very flexible and easy to apply.</p>
                        </div>

                        <h4 class="heading-1 mrg20T clearfix">
                            <div class="heading-content" style="width: auto;">
                                Icons
                                <small>
                                    All icons across the Fides Admin Framework use FontAwesome icons.
                                </small>
                            </div>
                            <div class="clear"></div>
                            <div class="divider"></div>
                        </h4>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-compass" href="../icon/compass"><i class="glyph-icon icon-compass"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-collapse" href="../icon/collapse"><i class="glyph-icon icon-collapse"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-collapse-top" href="../icon/collapse-top"><i class="glyph-icon icon-collapse-top"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-expand" href="../icon/expand"><i class="glyph-icon icon-expand"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-eur" href="../icon/eur"><i class="glyph-icon icon-eur"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-euro" href="../icon/eur"><i class="glyph-icon icon-euro"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-gbp" href="javascript:;"><i class="glyph-icon icon-gbp"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-usd" href="javascript:;"><i class="glyph-icon icon-usd"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-dollar" href="javascript:;"><i class="glyph-icon icon-dollar"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-inr" href="javascript:;"><i class="glyph-icon icon-inr"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-rupee" href="javascript:;"><i class="glyph-icon icon-rupee"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-jpy" href="javascript:;"><i class="glyph-icon icon-jpy"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-yen" href="javascript:;"><i class="glyph-icon icon-yen"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-cny" href="javascript:;"><i class="glyph-icon icon-cny"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-renminbi" href="javascript:;"><i class="glyph-icon icon-renminbi"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-krw" href="javascript:;"><i class="glyph-icon icon-krw"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-won" href="javascript:;"><i class="glyph-icon icon-won"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-btc" href="javascript:;"><i class="glyph-icon icon-btc"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-bitcoin" href="javascript:;"><i class="glyph-icon icon-bitcoin"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-file" href="javascript:;"><i class="glyph-icon icon-file"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-file-text" href="javascript:;"><i class="glyph-icon icon-file-text"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-alphabet" href="javascript:;"><i class="glyph-icon icon-sort-by-alphabet"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-alphabet-al" href="javascript:;"><i class="glyph-icon icon-sort-by-alphabet-alt"></i>t</a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-attributes" href="javascript:;"><i class="glyph-icon icon-sort-by-attributes"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-attribu" href="javascript:;"><i class="glyph-icon icon-sort-by-attributes-alt"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-order" href="javascript:;"><i class="glyph-icon icon-sort-by-order"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-sort-by-order-alt" href="javascript:;"><i class="glyph-icon icon-sort-by-order-alt"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-thumbs-up" href="javascript:;"><i class="glyph-icon icon-thumbs-up"></i></a>

                        <a class="btn medium radius-all-4 mrg5A ui-state-default tooltip-button" title="icon-thumbs-down" href="javascript:;"><i class="glyph-icon icon-thumbs-down"></i></a>

                    </div>
                </div>

                <div class="hide" id="white-modal-80" title="Dialog with tabs">
                    <div class="tabs pad15A remove-border opacity-80">
                        <ul class="opacity-80">
                            <li><a href="#example-tabs-1">First</a></li>
                            <li><a href="#example-tabs-2">Second</a></li>
                            <li><a href="#example-tabs-3">Third</a></li>
                        </ul>
                        <div id="example-tabs-1">
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                            <p>Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum eget, sagittis et, consequat vestibulum, lacus. Mauris porttitor ullamcorper augue.
                            </p>
                        </div>
                        <div id="example-tabs-2">
                            <p>Phasellus mattis tincidunt nibh. Cras orci urna, blandit id, pretium vel, aliquet ornare, felis. Maecenas scelerisque sem non nisl. Fusce sed lorem in enim dictum bibendum.
                            </p>
                            <p>Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum eget, sagittis et, consequat vestibulum, lacus. Mauris porttitor ullamcorper augue.
                            </p>
                        </div>
                        <div id="example-tabs-3">
                            <p>Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum eget, sagittis et, consequat vestibulum, lacus. Mauris porttitor ullamcorper augue.
                            </p>
                            <p>Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum eget, sagittis et, consequat vestibulum, lacus. Mauris porttitor ullamcorper augue.
                            </p>
                        </div>
                    </div>
                    <div class="pad10A">
                        <div class="infobox success-bg radius-all-4">
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque</p>
                        </div>
                    </div>
                    <div class="ui-dialog-buttonpane clearfix">

                        <a href="dropdown_menus.html" class="btn medium float-left bg-azure">
                            <span class="button-content text-transform-upr font-size-11">Dropdown menus</span>
                        </a>
                        <div class="button-group float-right">
                            <a href="buttons.html" class="btn medium bg-black" title="View more buttons examples">
                                <i class="glyph-icon icon-star"></i>
                            </a>
                            <a href="buttons.html" class="btn medium bg-black" title="View more buttons examples">
                                <i class="glyph-icon icon-random"></i>
                            </a>
                            <a href="buttons.html" class="btn medium bg-black" title="View more buttons examples">
                                <i class="glyph-icon icon-map-marker"></i>
                            </a>
                        </div>
                        <a href="javascript:;" class="medium btn bg-blue-alt float-right mrg10R tooltip-button" data-placement="left" title="Remove comment">
                            <i class="glyph-icon icon-plus"></i>
                        </a>

                    </div>
                </div>
                <div class="user-profile dropdown">
                    <a href="javascript:;" title="" class="user-ico clearfix" data-toggle="dropdown">
                        <img width="36" src="assets/images/gravatar.jpg" alt="" />
                        <span>Horia Simon</span>
                        <i class="glyph-icon icon-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu float-right">
                        <li>
                            <a href="javascript:;" title="">
                                <i class="glyph-icon icon-user mrg5R"></i>
                                Account Details
                            </a>
                        </li>
                        <li>
                            <a href="javascript: user-edit();" title="">
                                <i class="glyph-icon icon-cog mrg5R"></i>
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" title="">
                                <i class="glyph-icon icon-flag mrg5R"></i>
                                Notifications
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" title="">
                                <i class="glyph-icon icon-signout font-size-13 mrg5R"></i>
                                <span class="font-bold">Logout</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu float-left">
                            <a href="javascript:;" data-toggle="dropdown" title="">
                                Dropdown menu
                            </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="javascript:;" title="">
                                            Submenu 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" title="">
                                            Submenu 2
                                        </a>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="javascript:;" title="">
                                            Submenu 3
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" title="">
                                                    Submenu 2-1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" title="">
                                                    Submenu 2-2
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                        </li>
                        <li>
                            <a href="javascript:;" title="">
                                Another menu item
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="dropdown dash-menu">
                    <a href="javascript:;" data-toggle="dropdown" data-placement="left" class="medium btn primary-bg float-right popover-button-header hidden-mobile tooltip-button" title="Example menu">
                        <i class="glyph-icon icon-th"></i>
                    </a>
                    <div class="dropdown-menu float-right">
                        <div class="small-box">
                            <div class="pad10A dashboard-buttons clearfix">
                                <p class="font-gray-dark font-size-11 pad0B">This menu type can be used in pages, not just popovers.</p>
                                <div class="divider mrg10T mrg10B"></div>
                                <a href="javascript:;" class="btn vertical-button hover-blue-alt" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-dashboard opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Dashboard</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button hover-green" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-tags opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Widgets</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button hover-orange" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-reorder opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Tables</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button hover-orange" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-bar-chart opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Charts</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button hover-purple" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-laptop opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Buttons</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button hover-azure" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-code opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Panels</span>
                                </a>
                            </div>

                            <div class="bg-gray text-transform-upr font-size-12 font-bold font-gray-dark pad10A">Dashboard menu</div>
                            <div class="pad10A dashboard-buttons clearfix">
                                <a href="javascript:;" class="btn vertical-button remove-border bg-blue" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-dashboard opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Dashboard</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button remove-border bg-red" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-tags opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Widgets</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button remove-border bg-purple" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-reorder opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Tables</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button remove-border bg-azure" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-bar-chart opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Charts</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button remove-border bg-yellow" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-laptop opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Buttons</span>
                                </a>
                                <a href="javascript:;" class="btn vertical-button remove-border bg-orange" title="">
                                    <span class="glyph-icon icon-separator-vertical pad0A medium">
                                        <i class="glyph-icon icon-code opacity-80 font-size-20"></i>
                                    </span>
                                    <span class="button-content">Panels</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="top-icon-bar menubackend-v2">
                    <div class="dropdown">

                        <a data-toggle="dropdown" href="javascript:;" title="Dashboard" href="index.html">
                            <i class="glyph-icon icon-dashboard"></i>
                            Dashboard
                            <i class="glyph-icon icon-angle-down arow-down-v2"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 1
                                </a>
                            </li>
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 2
                                </a>
                            </li>
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 3
                                </a>
                            </li>
                        </ul>                     </div>
                    <div class="dropdown">

                        <a data-toggle="dropdown" href="javascript:;" title="??n hàng" href="index.html">
                            <i class="glyph-icon icon-paste"></i>
                            ??n hàng
                            <i class="glyph-icon icon-angle-down arow-down-v2"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 1
                                </a>
                            </li>
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 2
                                </a>
                            </li>
                            <li>
                                <a title="" href="javascript:;">
                                    Nav link 3
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="dropdown">

                        <a data-toggle="dropdown" href="javascript:;" title="Bi?u phí" href="index.html">
                            <i class="glyph-icon icon-dollar"></i>
                            Bi?u phí
                            <i class="glyph-icon icon-angle-down arow-down-v2"></i>
                        </a>
                        <div class="dropdown-menu">

                            <div class="popover-title display-block clearfix form-row pad10A">
                                <div class="form-input">
                                    <div class="form-input-icon">
                                        <i class="glyph-icon icon-search transparent"></i>
                                        <input type="text" placeholder="Search notifications..." class="radius-all-100" name="" id="" />
                                    </div>
                                </div>
                            </div>
                            <div class="scrollable-content medium-box scrollable-small">

                                <ul class="no-border notifications-box">
                                    <li>
                                        <span class="btn bg-purple icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text">This is an error notification</span>
                                        <div class="notification-time">
                                            a few seconds ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="btn bg-orange icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text">This is a warning notification</span>
                                        <div class="notification-time">
                                            <b>15</b> minutes ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="bg-green btn icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text font-green font-bold">A success message example.</span>
                                        <div class="notification-time">
                                            <b>2 hours</b> ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="btn bg-purple icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text">This is an error notification</span>
                                        <div class="notification-time">
                                            a few seconds ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="btn bg-orange icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text">This is a warning notification</span>
                                        <div class="notification-time">
                                            <b>15</b> minutes ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="bg-blue btn icon-notification glyph-icon icon-user"></span>
                                        <span class="notification-text font-blue">Alternate notification styling.</span>
                                        <div class="notification-time">
                                            <b>2 hours</b> ago
                                            <span class="glyph-icon icon-time"></span>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                            <div class="pad10A button-pane button-pane-alt text-center">
                                <a href="notifications.html" class="btn medium primary-bg">
                                    <span class="button-content">View all notifications</span>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="dropdown">

                        <a data-toggle="dropdown" href="javascript:;" title="Ng??i dùng" href="index.html">
                            <i class="glyph-icon icon-user"></i>
                            Ng??i dùng
                            <i class="glyph-icon icon-angle-down arow-down-v2"></i>
                        </a>
                        <div class="dropdown-menu" id="progress-dropdown">

                            <div class="scrollable-content small-box scrollable-small">

                                <ul class="no-border progress-box">
                                    <li>
                                        <div class="progress-title">
                                            Finishing uploading files
                                            <b>23%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="23">
                                            <div class="progressbar-value bg-blue">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Roadmap progress
                                            <b>91%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="91">
                                            <div class="progressbar-value primary-bg">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Images upload
                                            <b>58%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="58">
                                            <div class="progressbar-value bg-blue-alt"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            WordPress migration
                                            <b>74%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="74">
                                            <div class="progressbar-value bg-purple"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Agile development procedures
                                            <b>91%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="91">
                                            <div class="progressbar-value primary-bg">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Systems integration
                                            <b>58%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="58">
                                            <div class="progressbar-value bg-blue-alt"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Code optimizations
                                            <b>97%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="97">
                                            <div class="progressbar-value bg-yellow"></div>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                            <div class="pad10A button-pane button-pane-alt text-center">
                                <a href="notifications.html" class="btn medium font-normal bg-green">
                                    <span class="button-content">View all</span>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="dropdown">

                        <a data-toggle="dropdown" href="javascript:;" title="H? th?ng" href="index.html">
                            <i class="glyph-icon icon-cog"></i>
                            H? th?ng
                            <i class="glyph-icon icon-angle-down arow-down-v2"></i>
                        </a>
                        <div class="dropdown-menu" id="progress-dropdown">

                            <div class="scrollable-content small-box scrollable-small">

                                <ul class="no-border progress-box">
                                    <li>
                                        <div class="progress-title">
                                            Finishing uploading files
                                            <b>23%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="23">
                                            <div class="progressbar-value bg-blue">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Roadmap progress
                                            <b>91%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="91">
                                            <div class="progressbar-value primary-bg">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Images upload
                                            <b>58%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="58">
                                            <div class="progressbar-value bg-blue-alt"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            WordPress migration
                                            <b>74%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="74">
                                            <div class="progressbar-value bg-purple"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Agile development procedures
                                            <b>91%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="91">
                                            <div class="progressbar-value primary-bg">
                                                <div class="progressbar-overlay"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Systems integration
                                            <b>58%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="58">
                                            <div class="progressbar-value bg-blue-alt"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="progress-title">
                                            Code optimizations
                                            <b>97%</b>
                                        </div>
                                        <div class="progressbar-small progressbar" data-value="97">
                                            <div class="progressbar-value bg-yellow"></div>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                            <div class="pad10A button-pane button-pane-alt text-center">
                                <a href="notifications.html" class="btn medium font-normal bg-green">
                                    <span class="button-content">View all</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- #page-header -->






