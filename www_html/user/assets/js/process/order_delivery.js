/**
 * Created by Admin on 4/19/14.
 */
var balance = 0;
$(document).ready(function(){
    // Load balance

    OrderWaitingDelivery.synBalance();
    OrderWaitingDelivery.fillMissingAll();

    $(document).on('click','._check_order',function(){
        var general_id = $(this).attr("data-general-id");
        Global.selectAllCheckbox($('._check_general_order[data-general-id='+general_id+']'),$('._check_order[data-general-id='+general_id+']'),false);
        OrderWaitingDelivery.fillMissingAll();
    });

    $(document).on('click', '._confirm-delivery', function(){
        var $this = $(this);
        var $root = $(this).parents('._item-order-view');
        var order_id = $this.data('order-id');
//        console.log('order_id: ' + order_id);
        if($this.hasClass('clicked')){
            return false;
        }
        $this.addClass('clicked');
        $this.html('Đang xử lý');
        $.ajax({
            url: UrlSubmitConfirmDelivery,
            type : "POST",
            data : { order_id : order_id },
            success : function(response){
//                console.log(response);
                $this.html('Đã nhận hàng');
                $this.css('text-decoration', 'none');
                $root.find('._status-title').html('Đã nhận hàng');
                $root.delay(1000).slideUp();
            }
        })
    });

    $(document).on('click','._check_general_order',function(){
        var general_id = $(this).attr("data-general-id");
        Global.selectAllCheckbox($(this),$('._check_order[data-general-id='+general_id+']'),true);
        OrderWaitingDelivery.fillMissingAll();
    });

    $(document).on('click','._confirmed_general',function(){
        var general_id = $(this).attr("data-general-id");
        if($(this).hasClass("_close_popup")){
            $('._close_popup[data-general-id='+general_id+']').click();
            return false;
        }
        var order_id_list = "";
        $('._check_order[data-general-id='+general_id+']').each(function(){
            if($(this).prop('checked')){
                var order_id = $(this).attr("data-order-id");
                if($.isNumeric(order_id)){
                    order_id_list += ","+order_id;
                }
            }
        });

        var cod = 0;

        if(order_id_list.length == 0){
            $("._close_confirmed_general").click();
            Global.sAlert("Chưa có đơn hàng nào được chọn");
        }else{
            var option = $('input:radio[name=optionsRadios'+general_id+']:checked').val();
            var is_cod = option == 'nt' ? 0 : 1;
            if(is_cod == 1){
                var _cod = $('._cod[data-general-id='+general_id+']');
                var balance_general = $('._total_balance[data-general-id='+general_id+']').attr("data-balance");
                balance_general = parseFloat(balance_general);
                if(_cod != null){
                    cod = _cod.val();
                    if(!$.isNumeric(cod)){
                        $("._close_confirmed_general").click();
                        Global.sAlert("Số tiền không hợp lệ");
                        return false;
                    }
                    if(cod > 0 && parseFloat(cod) < -balance_general){
                        $("._close_confirmed_general").click();
                        Global.sAlert("Số tiền thanh toán sau phải lớn hơn số dư sau tất toán");
                        return false;
                    }
                }

            }

            OrderWaitingDelivery.submitDelivering(order_id_list,cod,general_id);
        }
    });

    $(document).on('click','._btn_confirm_pass',function(e){
        var general_id = $(this).attr('data-general-id');
        if($(this).hasClass("_confirm_success")){
            e.preventDefault();
            $('._open_confirmed[data-general-id='+general_id+']').click();
        }
    });

    $(document).on('click','._confirm_password',function(){
        $(this).attr("disabled",true);
        var general_id = $(this).attr('data-general-id');
        var order_id_list = "";
        $('._check_order[data-general-id='+general_id+']').each(function(){
            if($(this).prop('checked')){
                var order_id = $(this).attr("data-order-id");
                if($.isNumeric(order_id)){
                    order_id_list += ","+order_id;
                }
            }
        });

        var money_settle = 0;

        if(order_id_list.length == 0){
            $(this).removeAttr("disabled");
            $('._close_confirm_pass[data-general-id='+general_id+']').click();
            Global.sAlert("Chưa có đơn hàng nào được chọn");
        }else{
            OrderWaitingDelivery.confirmPassword(order_id_list,general_id);
        }
    });

    $(document).on('click','._close_popup',function(){
        var general_id = $(this).attr('data-general-id');
        $('#finishModal'+general_id).click();
        $('._group_order[data-general-id='+general_id+']').fadeOut();
        setTimeout(function(){
            $('._group_order[data-general-id='+general_id+']').remove();
        },2000);
    });

    $(document).on('keyup','._password',function(e){
        var general_id = $(this).attr('data-general-id');
        if(e.keyCode == 13){
            $('._confirm_password[data-general-id='+general_id+']').click();
        }
    });

    $(document).on('click','._checkbox_option',function(){
        var general_id = $(this).attr('data-general-id');
        if($(this).val() == 'nt'){
            $('._block_recharge[data-general-id='+general_id+']').addClass('block');
            $('._block_cod[data-general-id='+general_id+']').removeClass('block');
        }else{
            $('._block_recharge[data-general-id='+general_id+']').removeClass('block');
            $('._block_cod[data-general-id='+general_id+']').addClass('block');
        }
    });
});

var OrderWaitingDelivery = {
    fillMissingAll : function(){
        var check_general = $('._check_general_order');
        if(check_general.length > 0){
            $('._confirmed_general').each(function(){
                var general_id = $(this).attr("data-general-id");
                OrderWaitingDelivery.fillMissingGroup(general_id);
            });
        }else{
            $( document ).ajaxComplete(function() {
                $('._confirmed_general').each(function(){
                    var general_id = $(this).attr("data-general-id");
                    OrderWaitingDelivery.fillMissingGroup(general_id);
                });
            });
        }
    },
    fillMissingGroup : function(general_id){
        var total = OrderWaitingDelivery.totalMissingGroup(general_id);

        var balance_current = balance + total.total_missing;
        balance_current = parseFloat(balance_current);
        if(balance_current < 0){
            OrderWaitingDelivery.negativeShow(general_id);
        }else{
            OrderWaitingDelivery.positiveShow(general_id);
        }
        $('._total_missing[data-general-id='+general_id+']').text(Global.formatKMoney(total.total_missing));
        $('._total_receive_general[data-general-id='+general_id+']').text(total.receive_quantity);
        $('._total_pending_general[data-general-id='+general_id+']').text(total.pending_quantity);
        $('._total_weight_general[data-general-id='+general_id+']').text(total.weight.toFixed(2));
        $('._total_real_amount_general[data-general-id='+general_id+']').text(Global.formatKMoney(total.real_amount));
        $('._total_services_general[data-general-id='+general_id+']').text(Global.formatKMoney(total.services_fee));
        $('._total_payment_general[data-general-id='+general_id+']').text(Global.formatKMoney(total.real_payment));
        $('._total_real_refund[data-general-id='+general_id+']').text(Global.formatKMoney(total.real_refund));
        var $class = "";
        if(parseFloat(balance_current) < 0){
            $class = "red-bold";
        }else{
            $class = "bold-blue";
        }
        $('._total_balance[data-general-id='+general_id+']').parent().attr("class","");
        $('._total_balance[data-general-id='+general_id+']').parent().addClass($class);
        $('._total_balance[data-general-id='+general_id+']').attr("data-balance",balance_current);
        $('._total_balance[data-general-id='+general_id+']').text(Global.formatKMoney(balance_current));
        $('._total_balance_rounding[data-general-id='+general_id+']').text(Global.formatKMoney(-balance_current,true));
    },
    totalMissingGroup : function(general_id){
        var total_missing = 0;
        var total = {
            "total_missing":0,
            "receive_quantity" : 0,
            "pending_quantity" : 0,
            "weight" : 0,
            "real_amount" : 0,
            "services_fee" : 0,
            "real_payment" : 0,
            "real_refund" : 0
        };
        $('._check_order[data-general-id='+general_id+']').each(function(){
            if($(this).prop('checked')){
                var order_id = $(this).attr("data-order-id");
                var missing_money = $('._missing_order[data-order-id='+order_id+']').attr("data-missing");
                var receive_quantity = $('._receive_quantity_order[data-order-id='+order_id+']').attr("data-quantity");
                var pending_quantity = $('._pending_quantity_order[data-order-id='+order_id+']').attr("data-quantity");
                var weight = $('._weight_order[data-order-id='+order_id+']').attr("data-weight");
                var real_amount = $('._real_amount_order[data-order-id='+order_id+']').attr("data-money");
                var services_fee = $('._services_fee_order[data-order-id='+order_id+']').attr("data-money");
                var real_payment = $('._real_payment_order[data-order-id='+order_id+']').attr("data-money");
                var real_refund = $('._real_refund_order[data-order-id='+order_id+']').attr("data-money");
                total["total_missing"] += Global.validNumber(missing_money);
                total["receive_quantity"] += Global.validNumber(receive_quantity);
                total["pending_quantity"] += Global.validNumber(pending_quantity);
                total["weight"] += Global.validNumber(weight);
                total["real_amount"] += Global.validNumber(real_amount);
                total["services_fee"] += Global.validNumber(services_fee);
                total["real_payment"] += Global.validNumber(real_payment);
                total["real_refund"] += Global.validNumber(real_refund);
            }
        });
        return total;
    },
    positiveShow : function(general_id){
        $('._positive[data-general-id='+general_id+']').show();
        $('._negative[data-general-id='+general_id+']').hide();
    },
    negativeShow : function(general_id){
        $('._positive[data-general-id='+general_id+']').hide();
        $('._negative[data-general-id='+general_id+']').show();
    },
    confirmPassword : function (order_id_list,general_id){
        var password = $('._password[data-general-id='+general_id+']').val();
        var balance = $('._total_balance[data-general-id='+general_id+']').attr('data-balance');
        balance = $.isNumeric(balance) ? parseFloat(balance) : -1;
        $.ajax({
            url : UrlSubmitDelivering,
            type : "POST",
            data : {
                order_id_list : order_id_list,
                money_settle : 0,
                password : password,
                is_confirm : 1,
                balance : balance
            },
            success : function(data){
                $('._confirm_password[data-general-id='+general_id+']').removeAttr("disabled");
                if(data.type == 0){
                    if(data.element == "password"){
                        $('._password[data-general-id='+general_id+']').focus();
                        $('._err_confirm_pass[data-general-id='+general_id+']').removeClass('hidden');
                    }else{
                        $('#confirmPassword'+general_id).click();
                        Global.sAlert(data.message);
                    }
                }else{
                    $('._check_order[data-general-id='+general_id+']').attr("disabled","disabled")
//                    if(data.element == "password"){
                    $('#confirmPassword'+general_id).click();
                    $('._open_confirmed[data-general-id='+general_id+']').click();
                    $('._btn_confirm_pass[data-general-id='+general_id+']').addClass("_confirm_success");
//                    else{
//                        $('._open_confirmed[data-general-id='+general_id+']').click();
                    $('._btn_confirm_pass[data-general-id='+general_id+']').removeAttr('data-target');
//                    }
                }
                OrderWaitingDelivery.synBalance();
                $('._open_confirm[data-general-id='+general_id+']').click();

            }
        })
    },
    submitDelivering : function(order_id_list,cod,general_id){
        var balance = $('._total_balance[data-general-id='+general_id+']').attr('data-balance');
        balance = $.isNumeric(balance) ? parseFloat(balance) : -1;
        $.ajax({
            url : UrlSubmitDelivering,
            type : "POST",
            data : {
                order_id_list : order_id_list,
                money_settle : cod,
                password : "",
                is_confirm : 0,
                balance : balance
            },
            success : function(data){
                if(data.type == 1){
                    $('#finishModal'+general_id).click();
                    setTimeout(function(){
                        OrderWaitingDelivery.synBalance();
                        $('._group_order[data-general-id='+general_id+']').remove();
                    },500);
                }else{
                    $("._close_confirmed_general").click();
                    $('._group_order[data-general-id='+general_id+']').fadeIn();
                    Global.sAlert(data.message);
                }
            }
        })
    },
    synBalance : function(){
        $.ajax({
            url : urlSynBalance,
            type :'POST',
            data: { },
            success: function(d) {
                d = $.parseJSON(d);
                if(d.status == undefined) {
                    if(!$.isNumeric(d.balance)){
                        balance = 0;
                    }else{
                        balance = d.balance;
                    }
                }

                if(!$.isNumeric(d.balance)){
                    balance = 0;
                }else{
                    balance = d.balance;
                }
            }
        });
    }
}