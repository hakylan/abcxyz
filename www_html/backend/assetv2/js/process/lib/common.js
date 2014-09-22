$(document).ready(function () {
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
            //$('._send_cart').addClass('load-wait');
        },
        complete:function(){
            $('._loading').hide();
            //$('._send_cart').removeClass('load-wait').html('Tiếp tục<span class="arow-next"></span>');
        }
    });

    if ("undefined" != typeof Handlebars) {
        Handlebars.registerHelper('date', function(datetime, options) {

            try{
                var dateSplit = datetime.split(/[- :]/);
                if (dateSplit[0] == '0000' || isNaN(dateSplit[5])) {
                    return '';
                }

//                console.log('year: ' + dateSplit[0]);
//                console.log('month: ' + dateSplit[1]);
//                console.log('day: ' + dateSplit[2]);
//                console.log('hour: ' + dateSplit[3]);
//                console.log('minute: ' + dateSplit[4]);
//                console.log('second: ' + dateSplit[5]);

                var date = new Date(dateSplit[0], dateSplit[1], dateSplit[2], dateSplit[3], dateSplit[4], dateSplit[5]),
                    today = new Date(),
                    day = parseInt(date.getDate()),
                    month = parseInt(date.getMonth());

                day = parseInt(dateSplit[2]);
                month = parseInt(dateSplit[1]);

                var minute = parseInt(date.getMinutes());
                var hour = parseInt(date.getHours());

                month = (month < 10)? '0' +month.toString() : month.toString();
                day = (day < 10)? '0' +day.toString() : day.toString();
                minute = (minute < 10) ? '0' + minute.toString() : minute.toString();
                hour = (hour < 10) ? '0' + hour.toString() : hour.toString();


                if (date.getFullYear() == today.getFullYear()) {
                    if (date.getDate() == today.getDay() && date.getMonth() == today.getMonth()) {
                        return hour +':' + minute + ' hôm nay';
                    }
                    return hour +':' + minute +' ' + day +'/' +month;
                } else {
                    return day + '/' +month +'/' +date.getFullYear();
                }
            }catch (e){
                return "";
            }

        });

        Handlebars.registerHelper("debug", function(optionalValue) {
            console.log("Current Context");
            //console.log(this);

            if (optionalValue) {
                console.log("Value");
                console.log(optionalValue);
            }
        });

        Handlebars.registerHelper('icon_homeland', function(homeland) {
            var icon = '';
            switch (homeland) {
                case '1688':
                    icon = backend_url + '/assets/images/icons/1688.png';
                    break;
                case 'TAOBAO':
                    icon = backend_url + '/assets/images/icons/taobao.png';
                    break;
                case 'TMALL':
                    icon = backend_url + '/assets/images/icons/tmal.png';
                    break;
                case 'EELLY':
                    icon = backend_url + '/assets/images/icons/elly.png';
                    break;
                default :
                    icon = '';
            }
            return icon;
        });

        Handlebars.registerHelper('console_log', function(value) {
            console.log(value);
        });

        Handlebars.registerHelper('money_format', function(money) {

            return  "+"+number_with_dot(money);

        });

        Handlebars.registerHelper('class_money', function(money) {

            money = parseFloat(money);

            return (money > 0)? "font-blue" : "font-red";
        });

        Handlebars.registerHelper('convert_image', function(image, options) {
            if( image == '' || image == null || image == undefined ) {
                return '';
            }

            //size 32x32
            image = image.replace(".32x32.jpg", ".100x100.jpg");
            image = image.replace(".32x32.png", ".100x100.png");
            image = image.replace(".32x32.jpeg", ".100x100.jpeg");
            image = image.replace(".32x32.gif", ".100x100.gif");
            image = image.replace(".32x32.bmp", ".100x100.bmp");

            //size 64x64
            image = image.replace(".64x64.jpg", ".100x100.jpg");
            image = image.replace(".64x64.png", ".100x100.png");
            image = image.replace(".64x64.jpeg", ".100x100.jpeg");
            image = image.replace(".64x64.gif", ".100x100.gif");
            image = image.replace(".64x64.bmp", ".100x100.bmp");

            return image;
        }) ;

        Handlebars.registerHelper('order_status', function(status, options) {
            var vi_status = '';
            switch (status) {
                case 'INIT':
                    vi_status = 'Đơn Mới';
                    break;
                case 'DEPOSITED':
                    vi_status = 'Đã Đặt Cọc';
                    break;
                case 'BUYING':
                    vi_status = 'Chờ Pai';
                    break;
                case 'NEGOTIATING':
                    vi_status = 'Đã Pai';
                    break;
                case 'WAITING_BUYER_CONFIRM':
                    vi_status = 'Chờ Xác Nhận';
                    break;
                case 'BUYER_CONFIRMED':
                    vi_status = 'Khách Xác Nhận';
                    break;
                case 'NEGOTIATED':
                    vi_status = 'Chờ Thanh Toán';
                    break;
                case 'BOUGHT':
                    vi_status = 'Đã Mua';
                    break;
                case 'SELLER_DELIVERY':
                    vi_status = 'Người Bán Giao';
                    break;
                case 'RECEIVED_FROM_SELLER':
                    vi_status = 'Seudo nhận';
                    break;
                case 'CHECKING':
                    vi_status = 'Đang Kiểm';
                    break;
                case 'CHECKED':
                    vi_status = 'Đã Kiểm';
                    break;
                case  'TRANSPORTING':
                    vi_status = 'Đang Vận Chuyển';
                    break;
                case 'WAITING_DELIVERY':
                    vi_status = 'Chờ Giao';
                    break;

                case 'CONFIRM_DELIVERY':
                    vi_status = 'Yêu Cầu Giao Hàng';
                    break;
                case 'DELIVERING':
                    vi_status = 'Đang Giao';
                    break;
                case 'RECEIVED':
                    vi_status = 'Khách Nhận Hàng';
                    break;
                case 'COMPLAINT':
                    vi_status = 'Khiếu Nại';
                    break;
                case 'OUT_OF_STOCK':
                    vi_status = 'Hết Hàng';
                    break;
                case 'CANCELLED':
                    vi_status = 'Hủy';
                    break;
                default :
                    vi_status = status;
            }

            return vi_status;
        });
    }
});

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function number_with_dot(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1.$2");
        console.log(x);
    return x;
}

function numberFormat(nStr){
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1))
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    return x1 + x2;
}

function stripNonNumeric( str ){
    str += '';
    var rgx = /^\d|\.|-$/;
    var out = '';
    for( var i = 0; i < str.length; i++ ){
        if( rgx.test( str.charAt(i) ) ){
            if( !( ( str.charAt(i) == '.' && out.indexOf( '.' ) != -1 ) ||
                ( str.charAt(i) == '-' && out.length != 0 ) ) ){
                out += str.charAt(i);
            }
        }
    }
    return out;
}

(function($) {
    $.fn.moneyFormat = function(options) {
        var settings = $.extend({
            // These are the defaults.
            positiveClass: "font-blue",
            negativeClass: "font-red",
            signal : true,
            symbol: "đ",
            useClass: true,
            useThousand: false
        }, options);

        return this.each(function() {
            var $this = $(this),
                amount,
                html = '';

            if ($this.data('amount')) {
                amount = parseFloat($this.data('amount'));
            } else {
                amount = parseFloat($this.text());
            }

            amount = settings.useThousand ? amount / 1000 : amount;

            var styleClass = (amount > 0)? settings.positiveClass : settings.negativeClass,
                amountWithDot = numberFormat(amount);
            if (settings.signal) {
                amountWithDot = ((amount > 0)? '+' : '') + amountWithDot;
            }

            styleClass = settings.useClass ? styleClass : '';

            if(amountWithDot == 0) {
                amountWithDot = '--';
            }

            if(settings.useThousand){
                html = '<span class="' +styleClass +'">'
                    +amountWithDot
                    + ' ' + ((settings.symbol)? settings.symbol:'')
                    +'</span>';
            }else{
                html = '<span class="' +styleClass +'">'
                    +amountWithDot
                    + ' ' + ((settings.symbol)? '<sup>' +settings.symbol +'</sup>':'')
                    +'</span>';
            }

            $this.html(html);
        });
    }


})(jQuery);

//Something fucking init
jQuery(document).ready(function($) {
    $('._money-amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });

    $('._money-amount-k').moneyFormat({
        useClass: false,
        useThousand: true,
        symbol: 'K'
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }

    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

var Common = {
    BSAlert : function(message){
        $('div#_alert p._message').text(message);
        $('span._alert').click();
    },
    BSConfirm : function(message){
        $('div#_confirm p._message').text(message);
        $('span._confirm').click();
    },
    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },
    currency_format:function (num,rounding,roundingConfig) {
        if(!$.isNumeric(num)){
            return num
        }
        if(roundingConfig == null){
            roundingConfig = 10;
        }
        if(rounding){
            num = Math.ceil(num / roundingConfig) * roundingConfig;
        }
        num = num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");

        return (num );
    }
};

$(document).ajaxComplete(function(){
    $('._money-amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-red').moneyFormat({
        positiveClass : 'font-red',
        negativeClass : 'font-red',
        signal: false
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-blue').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-blue',
        signal: false
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-black').moneyFormat({
        positiveClass : 'font-black',
        negativeClass : 'font-black'
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-gray').moneyFormat({
        positiveClass : 'font-gray-dark',
        negativeClass : 'font-gray-dark',
        signal: false
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money_cny_amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });

    var custom_select = $(".custom-select");
    if(custom_select != null && custom_select.length > 0){
        custom_select.uniform();
    }
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});