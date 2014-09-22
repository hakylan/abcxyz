/**
 * Created by Admin on 6/2/14.
 */

$(document).ajaxComplete(function(){
    $('._cod').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });
    $('._domestic_shipping_fee').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });
    Delivery.GetOrderChecked();
});
$(document).ready(function(){
    Delivery.SearchOrder();

    $(document).on('click','._checkbox_order',function(){
        Delivery.GetOrderChecked();
    });

    $(document).on('keyup','._address_search',function(e){
        if(e.keyCode == 13){
            $('._btn_filter').click();
        }
    });


    $(document).on('keyup','._customer_search',function(e){
        if(e.keyCode == 13){
            $('._btn_filter').click();
        }
    });


    $(document).on('click','._li_warehouse',function(){
        var warehouse = $(this).find("._warehouse").attr("data-value");
        $('._li_warehouse').removeClass("active");
        $(this).addClass("active");
        $('._warehouse_search').val(warehouse);
        $('._btn_filter').click();
    });
    
    $(document).on('click','._btn_filter',function(){
        var img_loading = $('._loading_main').html();
        $('._main_content').html(img_loading);
        Delivery.SearchOrder();
    });

    $(document).on('keyup','._cod',function(e){
        if(e.keyCode == 13){
            var address_id = $(this).attr("data-address-id");
            $('._btn_save_cod[data-address-id='+address_id+']').click();
        }
    });

    $(document).on('keyup','._domestic_shipping_fee',function(e){
        if(e.keyCode == 13){
            var address_id = $(this).attr("data-address-id");
            $('._btn_save_fee[data-address-id='+address_id+']').click();
        }
    });
    
    $(document).on('click','._btn_save_fee',function(){
        var address_id = $(this).attr("data-address-id");
        var domestic_shipping_fee = $('._domestic_shipping_fee[data-address-id='+address_id+']').autoNumeric('get');
        var purpose_charge = $('._purpose_charge[data-address-id='+address_id+']').val();
        $('._btn_close_fee[data-address-id='+address_id+']').click();
        if(!$.isNumeric(domestic_shipping_fee)){
            Common.BSAlert("Phí bạn nhập không hợp lệ, vui lòng nhập lại");
            return;
        }
        $('._shipping_fee[data-address-id='+address_id+']').html(Common.currency_format(domestic_shipping_fee)+"<sup>đ</sup>");
        $('._shipping_fee[data-address-id='+address_id+']').addClass("font-blue");
        $('._shipping_fee[data-address-id='+address_id+']').attr("data-amount",domestic_shipping_fee);

        $.ajax({
            url : ChangeShippingFee,
            type : "POST",
            data : {
                fee: domestic_shipping_fee,
                purpose: purpose_charge,
                address_id: address_id
            },
            success : function(data){
                if(data.type){

                }else{
                    Common.BSAlert(data.message);
                }
            }
        })
    });

    $(document).on('click','._print_bill',function(){
        var domestic_id = $(this).attr("data-domestic-id");
        window.open(FramePrint+"?domestic_id="+domestic_id);
    });

    $(document).on('click','._btn_save_cod',function(){
        var address_id = $(this).attr("data-address-id");
//        var cod = $('._cod[data-address-id='+address_id+']').val();
        var cod = $('._cod[data-address-id='+address_id+']').autoNumeric('get');
        $('._btn_close_cod[data-address-id='+address_id+']').click();
        if(!$.isNumeric(cod)){
            Common.BSAlert("Cod bạn nhập không hợp lệ, vui lòng nhập lại");
            return;
        }

        var $html = Common.currency_format(cod)+"<sup>đ</sup>";
        $('._cod_span[data-address-id='+address_id+']').html($html);
        $('._cod_span[data-address-id='+address_id+']').addClass("font-blue");
        $('._cod_span[data-address-id='+address_id+']').attr('data-amount',cod);
        $.ajax({
            url : ChangeCod,
            type : "POST",
            data : {
                cod: cod,
                address_id: address_id
            },
            success : function(data){
                if(data.type){

                }else{
                    Common.BSAlert(data.message);
                }
            }
        })
    });


    $(document).on('click','._warehouse',function(){
        var warehouse = $(this).attr("data-value");
        $('._warehouse_search').val(warehouse);
    });

    $(document).on('click','._request_delivery',function(){
        var request = $(this);
        if(request.find("span").hasClass("disabled")){
            return;
        }
        request.find("span").addClass("disabled");
        var order_id = $(this).attr('data-order-id');
        Delivery.RequestDelivery(order_id);
    });

    $(document).on('click','._refresh_order',function(){
        var refresh  = $(this);
        if(refresh.hasClass("disabled")){
            return;
        }
        refresh.addClass("disabled");
        var order_id = $(this).attr('data-order-id');
        $.ajax({
            url : RefreshOrder,
            type : "POST",
            data : {
                order_id : order_id
            },
            success : function(data){
                refresh.removeClass("disabled");
                if(data.type == 1){
                    if(data.status){
                        $('._order_detail[data-order-id='+order_id+']').attr("data-is-out",1);
                        $('._warehouse_status[data-order-id='+order_id+']').text("Xuất kho");
                        Delivery.CheckEnabled(order_id);
                        Delivery.GetOrderChecked();
                    }
                }else{
                    Common.BSAlert(data.message);
                }
            }
        })
    });

    $(document).on('click','._create_bill',function(){
        var create_bill = $(this);
        create_bill.attr("data-is-create","1");
        create_bill.attr("disabled","disabled");
        create_bill.addClass("disabled");
        create_bill.text("Đang xử lý ...");
        var address_id = $(this).attr('data-address-id');
        var order_id_list = [];
        $('._checkbox_order[data-address-id='+address_id+']').each(function(){
            if($(this).prop('checked')) {
                var order_id = $(this).val();
                $('._order_detail[data-order-id='+order_id+']').fadeOut();
                order_id_list.push(order_id);
            }
        });
        $.ajax({
            url : CreateBill,
            type : "POST",
            data : {
                order_id_list: order_id_list,
                address_id: address_id
            },
            success : function(data){
                if(data.type){
                    create_bill.text("Đã tạo");
                    $('._notify_success[data-address-id='+address_id+']').show();
                    $('._print_bill[data-address-id='+address_id+']').attr('data-domestic-id',data.bill_id);
                    $('._print_bill[data-address-id='+address_id+']').removeAttr("disabled");
                    $('._print_bill[data-address-id='+address_id+']').removeClass("disabled");
                    setTimeout(function(){
                        $('._notify_success[data-address-id='+address_id+']').hide();
                    },20000);
                }else{
                    create_bill.attr("data-is-create","0");
                    $("._close_create_bill[data-address-id='+address_id+']").click();
                    create_bill.text("Tạo phiếu");
                    create_bill.removeAttr("disabled");
                    create_bill.removeClass("disabled");
                    Common.BSAlert(data.message);
                }
            }
        })
    });
});

var Delivery = {
    Users : {},
    Orders : {},

    SearchOrder : function(){
        var data = $('#_frm_search').serialize();
        var url = DeliverManage+"?"+data;
        Common.push_state(url);
        $.ajax({
            url : SearchOrder,
            type : "GET",
            data : data,
            success : function(data){
                $('._main_content').html(data.html_search);
                Delivery.GetLoadUser();
                Delivery.GetLoadOrder();
                Delivery.GetLoadAddress();
            }
        })
    },

    GetOrderChecked : function(){
        $('._main_order').each(function(){
            var address_id = $(this).attr("data-address-id");
            var $order_code = "";
            $('._checkbox_order[data-address-id='+address_id+']').each(function(){
                if($(this).prop('checked')) {
                    var order_id = $(this).val();
                    var order_code = $(this).attr("data-code");
                    $order_code += ","+order_code;
                }
            });
            if($order_code.charAt(0) == ','){
                $order_code = $order_code.substr(1,$order_code.length);
            }

            if($order_code.length > 150){
                $order_code = $order_code.substr(0,80) + "...";
            }

            if($order_code == ""){

                $('._create_bill[data-address-id='+address_id+']').attr("disabled","disabled");
                $('._create_bill[data-address-id='+address_id+']').addClass("disabled");
            }else{
                var create_bill = $('._create_bill[data-address-id='+address_id+']');
                var is_create =  create_bill.attr("data-is-create");
                if(is_create != 1 && is_create != "1"){
                    $('._create_bill[data-address-id='+address_id+']').removeAttr("disabled");
                    $('._create_bill[data-address-id='+address_id+']').removeClass("disabled");
                }
            }

            $('._list_order_check[data-address-id='+address_id+']').text($order_code);
        });

    },

    CheckEnabled : function (order_id){
        var order_detail = $('._order_detail[data-order-id='+order_id+']');
        var is_request_delivery = order_detail.attr("data-request-delivery");
        var is_out = order_detail.attr("data-is-out");
        if(is_request_delivery == "0" || is_out == "0"){
            if(!order_detail.hasClass("opacity")){
                order_detail.addClass("opacity");
            }
            $('._checkbox_order[data-order-id='+order_id+']').attr("disabled","disabled");
            $('._checkbox_order[data-order-id='+order_id+']').prop("checked",false);
            if(!is_request_delivery){
                $('._request_delivery[data-order-id='+order_id+']').show();
                $('._p_refresh_order[data-order-id='+order_id+']').hide();
                $('._time_delivery[data-order-id='+order_id+']').hide();
            }else{
                $('._request_delivery[data-order-id='+order_id+']').hide();
                $('._p_refresh_order[data-order-id='+order_id+']').show();
            }
        }else{
            order_detail.removeClass("opacity");
            $('._checkbox_order[data-order-id='+order_id+']').removeAttr("disabled");
            $('._checkbox_order[data-order-id='+order_id+']').prop("checked",true);
            $('._request_delivery[data-order-id='+order_id+']').hide();
            $('._p_refresh_order[data-order-id='+order_id+']').hide();
            $('._time_delivery[data-order-id='+order_id+']').show();
        }
    },

    RequestDelivery : function(order_id){
        $.ajax({
            url : RequestDeliveryUrl,
            type : "POST",
            data : {
                order_id : order_id
            },
            success : function(data){
                Delivery.GetOrderChecked();
                $('._request_delivery[data-order-id='+order_id+']').removeClass("disabled");
                if(data.type == 1){
                    $('._time_delivery[data-order-id='+order_id+']').text(data.confirm_delivery_time);
                    $('._order_detail[data-order-id='+order_id+']').attr("data-request-delivery",1);
                    Delivery.CheckEnabled(order_id);
                }else{
                    $('._request_delivery[data-order-id='+order_id+']').removeAttr("disabled");
                    Common.BSAlert(data.message);
                }
            },error:function(){
                $('._request_delivery[data-order-id='+order_id+']').removeClass("disabled");
            }
        })
    },

    GetLoadUser : function(){
        var main_user = $('._main_user[data-load="0"]');
        if(main_user.length > 0 && main_user != null){
            var address_id = main_user.attr('data-address-id');
            Delivery.LoadUser(address_id);
        }
    },

    GetLoadOrder : function(){
        var main_order = $('._main_order[data-load="0"]');
        if(main_order.length > 0 && main_order != null){
            var address_id = main_order.attr('data-address-id');
            Delivery.LoadOrder(address_id);
        }
    },

    GetLoadAddress : function(){
        var main_address = $('._main_address[data-load="0"]');
        if(main_address.length > 0 && main_address != null){
            var address_id = main_address.attr('data-address-id');
            Delivery.LoadAddress(address_id);
        }
    },

    LoadOrder : function(address_id){
        $.ajax({
            url : LoadOrderUrl,
            type : "GET",
            data : {
                address_id : address_id
            },
            success : function(data){
                $('._main_order[data-address-id='+address_id+']').attr("data-load",1);
                if(data.type == 1){
                    $('._main_order[data-address-id='+address_id+']').html(order_template(data.order_list)).fadeIn();
                }
                Delivery.GetLoadOrder();
            }
        })
    },
    LoadUser : function(address_id){
        $.ajax({
            url : LoadUsersUrl,
            type : "GET",
            data : {
                address_id : address_id
            },
            success : function(data){
                $('._main_user[data-address-id='+address_id+']').attr("data-load",1);
                if(data.type == 1){
                    Delivery.FillUser(address_id,data.user);
                }
                Delivery.GetLoadUser();
            }
        })
    },
    LoadAddress:function(address_id){
        $.ajax({
            url : LoadAddressUrl,
            type : "GET",
            data : {
                address_id : address_id
            },
            success : function(data){
                $('._main_address[data-address-id='+address_id+']').attr("data-load",1);
                if(data.type == 1){
                    $('._main_address[data-address-id='+address_id+']').html(address_template(data.address)).fadeIn();
                }
                Delivery.GetLoadAddress();
            }
        })
    },
    FillUser : function(address_id,user){
        $('._main_user[data-address-id='+address_id+']').html(user_template(user)).fadeIn();
    }
};