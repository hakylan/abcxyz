/**
 * Created by binhnt on 2/12/14.
 */
var urlDepositFinish = '',
    urlDeposit = '',
    depositList = [],
    __crsf = '',
    balance = 0;
$(document).ready(function() {

    var data = ($('.order-deposit-info').data('attr'));
    urlDepositFinish = data.urlFinish;__crsf
    urlDeposit = data.urlDeposit;

    // Load balance
    $.ajax({
        url : data.urlSynBalance, type :'POST', data: { },
        success: function(d) {
            d = $.parseJSON(d);
            if(d.status == undefined) {
                balance = d.balance;
                // Update client amount
                $('span.blueprice').html(formatCurrency(balance) + '<sup>đ</sup>');
                OrderDeposit.sumDeposit();
            } else {
                $('#cannotDeposit').find('.content-msg').html(d.msg);
                $('.cannotDeposit').click();
            }
        }
    });

    // Check all order need deposit
    $('.check-all').click(function() {
        var checked = $(this).is(':checked');
        $('.cart-list-content').find('input[type=checkbox]').prop('checked', checked);
        OrderDeposit.sumDeposit();
    });

    // UnCheck all
    $('.cart-list-content').find('input[type=checkbox]').each(function(index) {
        $(this).click(function() {
            $('.check-all').prop('checked', OrderDeposit.getAllStateOfCheckbox($('.cart-list-content').find('input[type=checkbox]')));
            OrderDeposit.sumDeposit();
        });
    })

    // Order deposit
    $('button.deposit').click(function() {
        if($(this).hasClass('disabled')) {
            $('#cannotDeposit').find('.content-msg').html('Bạn không thể thanh toán đặt cọc!');
            $('.cannotDeposit').click();
            return; // Stop deposit for order
        }
        depositList = [];
        // Get list order need deposit on page
        $('.cart-list-content').find('input[type=checkbox]').each(function(index) {
            if($(this).is(':checked')) {
                depositList.push($(this).val());
            }
        });
        __crsf = data.crsf;
        if(depositList.length == 0) {
            $('#cannotDeposit').find('.content-msg').html('Không có đơn hàng nào được thanh toán!');
            $('.cannotDeposit').click();
            return;
        }
        // Auth deposit - open model box confirm
        $('.err-msg').html('');
        $('#confirm-deposit').val('');
        $('.confirmDeposit').click();

        // Auto forcus
        setTimeout(function() {$('#confirm-deposit').focus();}, 1000);

        $('#confirm-deposit').keyup(function(e) {
            $('.err-msg').html('');
            if(e.keyCode == 13) {
                OrderDeposit.authDeposit(data.urlAuthDeposit, $('#confirm-deposit').val(), __crsf);
            }
        });
    });
    // Confirm action
    $('button.deposit-confirm').click(function() {
        OrderDeposit.authDeposit(data.urlAuthDeposit, $('#confirm-deposit').val(), __crsf);
    })
});

var OrderDeposit =  {
    'getAllStateOfCheckbox': function(obj) {
        var state = true;
        $(obj).each(function(index) {
            if(!$(this).is(':checked')) {
                state = false;
                return false;
            }
        });
        return state;
    }, 'authDeposit': function(urlAuthDeposit, data) {
        $.ajax({
            url: urlAuthDeposit,
            type: 'POST',
            data: { 'confirm' : data },
            success: function(d) {
                d = $.parseJSON(d);
                if(d.state) {
                    // Call deposit action
                    OrderDeposit.deposit(urlDeposit, depositList, __crsf);
                    $('#confirmDeposit').find('.btn-gray').click();
                } else {
                    /*$('#cannotDeposit').find('.content-msg').html(d.msg);
                     $('.cannotDeposit').click();*/
                    $('.err-msg').html(d.msg);
                }
            }, error: function(err) {
                $('.err-msg').html(err);
                /*$('#cannotDeposit').find('.content-msg').html(err);
                 $('.cannotDeposit').click();*/
            }
        });
    }, 'deposit': function(link, depositList, __crsf) {
        // Get first result of list
        var order_id = depositList.shift(), depositObj = $('.deposit-num-' + order_id);
        OrderDeposit.depositProcess('open', depositObj);
        $('button.deposit').addClass('disabled');

        $.ajax({
            url: link,
            type: 'POST',
            data: { '__crsf' : __crsf, 'id' : order_id },
            success: function(d) {
                d = $.parseJSON(d);
                if(d.state) {
                    $(depositObj).fadeOut().remove();
                    if(depositList.length > 0) {
                        OrderDeposit.deposit(link, depositList, __crsf);
                    } else {
                        window.location = urlDepositFinish;
                    }
                } else { // Error
                    $('button.deposit').removeClass('disabled');
                    $('#cannotDeposit').find('.content-msg').html(d.msg);
                    $('.cannotDeposit').click();
                }
                OrderDeposit.depositProcess('close', depositObj);
            }
        });
    }, 'depositProcess': function(option, obj) {
        switch (option) {
            case 'close':
                $(obj).find('.loading').remove();
                break;
            case 'open':
                $(obj).append($('div.loading-content').html());
                break;
            default : break;
        }
    }, 'sumDeposit': function() {
        var totalItems = 0, totalAmount = 0, totalDeposit = 0;
        $('.cart-list-content').find('input[type=checkbox]').each(function(index) {
            if($(this).is(':checked')) {
                var data = $('.deposit-num-' + $(this).val()).data('info');
                totalItems += parseInt(data.item);
                totalAmount += parseInt(data.amount);
                totalDeposit += parseInt(data.deposit);
            }
        });
        // Reset sum result
        $('.totalItems').html(formatCurrency(totalItems));
        $('.totalDeposit').html(formatCurrency(rounding(totalDeposit, '')));
        $('.totalAmount').html(formatCurrency(rounding(totalAmount, '')));

        // Check
        if(balance < totalDeposit) {
            $('.alert-error').show();
            $('button.deposit').addClass('disabled');
            $('button.deposit').addClass('hidden');
        } else {
            if($('.cart-list-content').find('input[type=checkbox]').length == 0) {
                // Không đơn hàng nào
                $('.alert-error').hide();
                $('button.deposit').addClass('disabled');
                $('button.deposit').removeClass('hidden');
            } else {
                $('.alert-error').hide();
                $('button.deposit').removeClass('disabled');
                $('button.deposit').removeClass('hidden');
            }
        }
    }
};

function formatCurrency(n) {
    return n.toFixed(0).replace(/./g, function (c, i, a) {
        return i && c !== "," && !((a.length - i) % 3) ? '.' + c : c;
    });
}

function rounding(money, rounding_config, round) {
    if(round == null) {
        round = 1000;
    }
    if (typeof rounding_config == 'object') {
        for (var i = 0; i < rounding_config.length; i++) {
            if (rounding_config[i].end == null) {
                round = rounding_config[i].round;
                break;
            }
            if (money >= rounding_config[i].begin & money < rounding_config[i].end) {
                round = rounding_config[i].round;
                break;
            }
        }
    }
    return (Math.ceil(money / round) * round);
}