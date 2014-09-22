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
            }catch (e){
                return "";
            }

        });
    }


    $('._money-amount').moneyFormat({
        positiveClass : 'font-blue',
        negativeClass : 'font-red'
    });
});

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

(function($) {
    $.fn.moneyFormat = function(options) {
        var settings = $.extend({
            // These are the defaults.
            positiveClass: "font-blue",
            negativeClass: "font-red",
            signal : true,
            symbol: "đ",
            useClass: true,
            useThousand: false,
            useZeroNumber: false
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
                amountWithDot = settings.useZeroNumber ? 0 : '--';
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