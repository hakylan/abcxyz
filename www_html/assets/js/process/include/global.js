/**
 * Created by Admin on 2/12/14.
 */
var Global = {
    sAlert : function(message){
        $('div#_alert p._message').text(message);
        $('span._alert').click();
    },
//    sConfirm : function(message){
//
//    },
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
    },

    formatKMoney : function(number,isRounding,rounding){
        if(!$.isNumeric(number)){
            return number;
        }

        var p = "";
        if(parseInt(number) > 0){
            p = "+";
        }
        var number_round = (parseFloat(number) / 1000);
        if(isRounding == true){
            if(rounding == null){
                rounding = 10;
                if(number_round / rounding < 0){
                    number_round = Math.floor(number_round / rounding) * rounding;
                }else{
                    number_round = Math.ceil(number_round / rounding) * rounding;
                }
            }
        }

        number_round = Global.roundToTwo(number_round);
        number_round = number_round.toString().replace(".00","");
        return p+number_round+"k";
    },

    selectAllCheckbox : function(checkboxAll,checkboxChildren,isAllClick){
        if(isAllClick == true){
            if(checkboxAll.prop('checked')) {
                checkboxChildren.prop('checked', true);
            } else {
                checkboxChildren.prop('checked', false);
            }
        }else{
            var check = true;
            checkboxChildren.each(function(){
                if(!$(this).prop('checked')){
                    check = false;
                }
            });

            if(check){
                checkboxAll.prop("checked",true);
            }else{
                checkboxAll.prop("checked",false);
            }
        }
    },
    validNumber : function(number){
        if(!$.isNumeric(number)){
            return 0;
        }
        return parseFloat(number);
    },
    roundToTwo : function(num) {
        if(!$.isNumeric(num)){
            return num;
        }
        return +(Math.ceil(num + "e+2")  + "e-2");
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}