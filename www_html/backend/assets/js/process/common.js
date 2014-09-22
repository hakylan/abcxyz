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
            var dateSplit = datetime.split(/[- :]/);
            if (dateSplit[0] == '0000' || isNaN(dateSplit[5])) {
                return '';
            }

            var date = new Date(dateSplit[0], dateSplit[1], dateSplit[2], dateSplit[3], dateSplit[4], dateSplit[5]),
                today = new Date(),
                day = parseInt(date.getDate()),
                month = parseInt(date.getMonth());

            month = (month < 10)? '0' +month.toString() : month.toString();
            day = (day < 10)? '0' +day.toString() : day.toString();

            if (date.getFullYear() == today.getFullYear()) {
                if (date.getDate() == today.getDay() && date.getMonth() == today.getMonth()) {
                    return date.getHours() +':' + date.getMinutes() + ' hôm nay';
                }
                return date.getHours() +':' + date.getMinutes() +' ' + day +'/' +month;
            } else {
                return day + '/' +month +'/' +date.getFullYear();
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
                    vi_status = 'Chờ giao';
                    break;

                case 'CONFIRM_DELIVERY':
                    vi_status = 'Yêu cầu giao hàng';
                    break;
                case 'DELIVERING':
                    vi_status = 'Đang giao';
                    break;
                case 'RECEIVED':
                    vi_status = 'Nhận Hàng';
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
                case 'CONFIRM_DELIVERY':
                    vi_status = 'Yêu cầu giao hàng';
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
    return x;
}

(function($) {
    $.fn.moneyFormat = function(options) {
        var settings = $.extend({
            // These are the defaults.
            positiveClass: "font-blue",
            negativeClass: "font-red",
            signal : true,
            symbol: "đ"
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

            var styleClass = (amount > 0)? settings.positiveClass : settings.negativeClass,
                amountWithDot = number_with_dot(amount);

            if (settings.signal) {
                amountWithDot = ((amount > 0)? '+' : '') + amountWithDot;
            }

            html = '<span class="' +styleClass +'">'
                +amountWithDot
                +((settings.symbol)? '<sup>' +settings.symbol +'</sup>':'')
                +'</span>';

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

    $(".custom-select").uniform();
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
    }
}

$(document).ajaxComplete(function(){
    $('._money-amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-red').moneyFormat({
        positiveClass : 'font-red',
        negativeClass : 'font-red',
        signal: false
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-blue').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-blue',
        signal: false
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-black').moneyFormat({
        positiveClass : 'font-black',
        negativeClass : 'font-black'
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money-amount-gray').moneyFormat({
        positiveClass : 'font-gray-dark',
        negativeClass : 'font-gray-dark',
        signal: false
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

$(document).ajaxComplete(function(){
    $('._money_cny_amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });

    $(".custom-select").uniform();
    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
});

//dd/mm/yyyy
function isDate(txtDate){
    var currVal = txtDate;
    if(currVal == '')
        return false;

    //Declare Regex
    var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
    var dtArray = currVal.match(rxDatePattern); // is format OK?

    if (dtArray == null)
        return false;

//        //Checks for mm/dd/yyyy format.
//        dtMonth = dtArray[1];
//        dtDay= dtArray[3];
//        dtYear = dtArray[5];

    //Checks for dd/mm/yyyy format.
    dtMonth = dtArray[3];
    dtDay= dtArray[1];
    dtYear = dtArray[5];

//        console.log('dtMonth: ' + dtMonth);
//        console.log('dtDay: ' + dtDay);
//        console.log('dtYear: ' + dtYear);

    if (dtMonth < 1 || dtMonth > 12)
        return false;
    else if (dtDay < 1 || dtDay> 31)
        return false;
    else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
        return false;
    else if (dtMonth == 2)
    {
        var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
        if (dtDay> 29 || (dtDay ==29 && !isleap))
            return false;
    }
    return true;
}