$(document).ready(function () {

    var item_step = $('._finish_step');

    if(item_step.length > 0){
        item_step = item_step.last();
        item_step.removeClass("finish");
        item_step.addClass("active");
    }

    var OrderDetail = {
        '_items':$('._items'),
        '_comments':$('._comments'),
        'orderId':$('#orderId').val(),
        'base_url':$('#base_url').val(),
        'home_url':$('#home_url').val(),
        'ajaxAbort':{},

        'init':function () {
            OrderDetail.load_item();
            OrderDetail.load_comment();

        },
        'abort_all_ajax':function(){
            for(var i=0;i<OrderDetail.ajaxAbort.length;i++){
                OrderDetail.ajaxAbort[i].abort();
            }
            OrderDetail.ajaxAbort = [];
        },
        'load_item':function() {
            $.ajax({
                url:  OrderDetail.base_url+'order_detail/load_item',
                type: "POST",
                data: {orderId:OrderDetail.orderId},
                success: function (html) {
                    OrderDetail._items.html(html);
                    var delaybody = $(".item-delay .module-body").height();
                    $('.scol-chat .single-chat').css('height', delaybody  + 'px');
                    OrderDetail.load_service_fee();
                    //
                }
            });
        },
        'load_comment':function() {
            $.ajax({
                // dau's hard code
                url:  OrderDetail.home_url + 'user/OrderComment/ListOrderComments',
                type: "GET",
                data: {order_id: OrderDetail.orderId, type: 'EXTERNAL'},
                success: function (result) {
//                    OrderDetail._comments.html(result);
                    $('._comments').append(order_comment_template(result));
                    $('._comments2').append(order_comment_template(result));
                    $('.scol-chat-fix .module-ct').css('height', window.innerHeight - 55 + 'px');
                }
            });
        },
        'load_service_fee':function () {

            var data = {};
            var services = {};
            var normalItemCount = 0, accessItemCount = 0, totalAmount = 0, i = 0;
            $('._choose_services').each(function () {
                if($(this).is(':checked')) {
                    i++;
                    services[i] = $(this).val();
                }
            });
            services[i+1] = 'BUYING';

            data.services = services;

            var total_weight = 0;
            $('._order_item_weight').each(function () {
                total_weight+=parseFloat($(this).text());
            });
            data.totalWeight = total_weight;
            data.targetCode = $('#targetCode').val();
            $('._order_item_type').each(function () {
                var order_item_id=$(this).attr('data-item-id');
                var quantity = $('._item_quantity_input[data-item-id="'+order_item_id+'"]').val();
                if($(this).val() == 'access') {
                    accessItemCount+= parseInt(quantity);
                }else{
                    normalItemCount+= parseInt(quantity);
                }
            });

            if(isNaN(normalItemCount)) {
                normalItemCount = 0;
            }
            data.normalItemCount = normalItemCount;
            data.accessItemCount = accessItemCount;

            $('._order_link_price').each(function () {
                totalAmount+=parseFloat($(this).attr('data-link-price'));
            });
            data.totalAmount = totalAmount+'vnd';
            OrderDetail.abort_all_ajax();
            // update
//            var abortLoadFee = $.ajax({
//                url:  OrderDetail.home_url+'service/calc',
//                type: "POST",
//                data: {data : data},
//                success: function (result) {
//                    var data = result.data;
//                    $('._buying_money').html(number_format(data.buyingFee));
//                    $('._checking_money').html(number_format(data.checkingFee));
//                    $('._shipping_money').html(data.shippingFeeDetail.chinaVietnam +(data.shippingFeeDetail.inlandChina.from+data.shippingFeeDetail.inlandChina.to)/2+data.shippingFeeDetail.inlandVietnam);
//                }
//            });
//            OrderDetail.ajaxAbort.push(abortLoadFee);
        },
        load_express_service_fee: function(order_id,services_type){
            $.ajax({
                url : OrderDetail.base_url+'order_common/chooseServices',
                type : "post",
                data : {
                    order_id:order_id,
                    services_type : services_type

                },
                success : function(data){
                    if (data.type) {
                        if(services_type =='CHECKING'){
                            if(data.fee != data.fee_discount){
                                $('._CHECKING_NOT_DISCOUNT').show();
                                var $html = "(Chưa chiết khấu : "+Global.currency_format(data.fee) + '<sup>đ</sup>)';
                                $('._CHECKING_NOT_DISCOUNT').html($html);
                            }
                            if(parseFloat(data.fee_discount) <=0){
                                $('._CHECKING_NOT_DISCOUNT').hide();
                            }
                            $('._CHECKING').html(Global.currency_format(data.fee_discount)+"<sup>đ</sup>");
                        }else if(services_type =='EXPRESS_CHINA_VIETNAM'){

                            $('._EXPRESS_CHINA_VIETNAM').html(Global.currency_format(data.fee_discount)+"<sup>đ</sup>");
                            if($('._choose_services_express[value="EXPRESS_CHINA_VIETNAM"]').is(':checked')){
                                $('._SHIPPING_CHINA_VIETNAM_NOT_DISCOUNT').hide();
                                if(data.fee != data.fee_discount){
                                    $('._EXPRESS_CHINA_VIETNAM_NOT_DISCOUNT').show();
                                    var $html = "(Chưa chiết khấu : "+Global.currency_format(data.fee) + '<sup>đ</sup>)';
                                    $('._EXPRESS_CHINA_VIETNAM_NOT_DISCOUNT').html($html);
                                }
                                if(parseFloat(data.fee_discount) <=0){
                                    $('._EXPRESS_CHINA_VIETNAM_NOT_DISCOUNT').hide();
                                }
                                $('._SHIPPING_CHINA_VIETNAM').html(Global.currency_format(0)+"<sup>đ</sup>");
                            }else{
                                $('._EXPRESS_CHINA_VIETNAM_NOT_DISCOUNT').hide();
                                if(data.fee_shipping_china_vn_discount != data.fee_shipping_china_vn){
                                    $('._SHIPPING_CHINA_VIETNAM_NOT_DISCOUNT').show();
                                    var $html = "(Chưa chiết khấu : "+Global.currency_format(data.fee_shipping_china_vn) + '<sup>đ</sup>)';
                                    $('._SHIPPING_CHINA_VIETNAM_NOT_DISCOUNT').html($html);
                                }
                                if(parseFloat(data.fee_shipping_china_vn_discount) <=0){
                                    $('._SHIPPING_CHINA_VIETNAM_NOT_DISCOUNT').hide();
                                }
                                $('._SHIPPING_CHINA_VIETNAM').html(Global.currency_format(data.fee_shipping_china_vn_discount)+"<sup>đ</sup>");
                            }
                        }
                        $('._total_money').html(Global.currency_format(data.total_amount)+"<sup>đ</sup>");
                        $('._deposit_money').html(Global.currency_format(data.deposit_amount));
                    }else{
                        Global.sAlert(data.message);
                        return false;
                    }
                }
            })
        },
        // dau's edit change class => id
        'add_comment':function (cmt) {
            var inputComment = $('._input_order_comment');
            var comment = cmt;

            // disable input
            inputComment.val('');
            inputComment.prop('disabled', true);

//            var time = new Date();
//            var monthNow = time.getMonth()+1;
//            if(monthNow<10){ monthNow = "0"+monthNow;}
//            var now = time.getHours()+":"+time.getMinutes()+" "+time.getDate()+"-"+monthNow;
//            if(comment.length == 0) {
//                inputComment.css('border','1px solid red');
//                return inputComment.attr('placeholder','Vui lòng điền nội dung comment!');
//            }
//            inputComment.attr('placeholder','Trao đồi về đơn hàng, Enter để gửi ');
            /* right comment*/
            // dau's hard code ??? Why => Truong
            $.ajax({
                url:  OrderDetail.home_url + 'user/OrderComment/AddMessage',
                type: "POST",
                data: {order_id: OrderDetail.orderId, message: comment, type: 'EXTERNAL'},
                success: function(result) {
                    inputComment.prop('disabled', false);
                    var listComment = $('._comments');
                    listComment.prepend(item_order_comment_template(result.info));
                    var listComment2 = $('._comments2');
                    listComment2.prepend(item_order_comment_template(result.info));
                },
                error: function(result) {
                    inputComment.prop('disabled', false);
                }
            });
        },
        'item_change_quantity':function (orderItemId, quantity) {
            var price = $('._order_item_price[data-item-id="'+orderItemId+'"]').attr('data-item-price');
            $('._order_item_quantity[data-item-id="'+orderItemId+'"]').html(quantity);


            $('._order_link_price[data-item-id="'+orderItemId+'"]').attr('data-link-price',parseInt(quantity)*parseFloat(price));
            OrderDetail.abort_all_ajax();
            var abortChangeQuantity = $.ajax({
                url:  OrderDetail.base_url+'order_common/change_quantity',
                type: "POST",
                data: {orderItemId:orderItemId,quantity:quantity},
                success:function (result) {
                    if(result.type == 1) {
                        $('._div_error'+orderItemId).hide();
                        var data = result.data;
                        $('._order_money').html(data.order_total_amount);
                        $('._deposit_money').html(data.deposit_amount);
                        $('._order_quantity').html(data.order_quantity);
                        $('._amount'+orderItemId).html(data.item_amount);
                        OrderDetail.load_service_fee();
                    }else{
                        $('._div_error'+orderItemId).show();
                        $('._message_error'+orderItemId).html(result.message);
                    }
                }
            });
            OrderDetail.ajaxAbort.push(abortChangeQuantity);
        }
    };


    OrderDetail.init();

    /* add note item*/
    $(document).on('click','._edit_comment',function (event) {
        event.preventDefault();


        var item_id = $(this).parent().attr('data-item-id');

        var form = $(this).next();
        $(this).hide();

        form
            .show()
            .find('input[type=text]')
            .focus();



        var that = $(this);
        form.on('keyup','._input_comment',function (event) {
            var keyCode = event.keyCode;
            if(keyCode == 13) {

                var comment = $(this).val();
                var temp=comment.replace(/\s+/g,"");

                if (temp.length>0 && comment != ''){
                    that.html(comment).show();
                    form.hide();
                    $.ajax({
                        url:  OrderDetail.base_url+'order_common/add_item_note',
                        type: "POST",
                        data: {orderItemId:item_id,comment:comment}
                    });
                }
            }
        });

    });
    /* add comment */
    // dau's edit convert class => id
    $(document).on('keyup','#_input_order_comment',function(event) {
        event.preventDefault();
        var cmt = $(this).val();
        if(event.keyCode == 13) {
            if (cmt.length > 0) {
                $(this).removeClass('form-myinput-warning');
                $(this).addClass('form-myinput');
                OrderDetail.add_comment(cmt);
            } else {
                $(this).removeClass('form-myinput');
                $(this).addClass('form-myinput-warning');
                $(this).attr('placeholder','Vui lòng điền nội dung comment!');
            }
        }
    });
    // dau's change class => id
    $(document).on('click','._submit_chat',function(event) {
        event.preventDefault();
        var cmt = $('#_input_order_comment').val();
        if (cmt.length > 0) {
            $('#_input_order_comment').removeClass('form-myinput-warning');
            $('#_input_order_comment').addClass('form-myinput');
            OrderDetail.add_comment(cmt);
        } else {
            $('#_input_order_comment').removeClass('form-myinput');
            $('#_input_order_comment').addClass('form-myinput-warning');
            $('#_input_order_comment').attr('placeholder','Vui lòng điền nội dung comment!');
        }
    });

    $(document).on('click','#myModaldeleteitemBtn',function() {
        var $confirm = $('#myModaldeleteitem');
        $confirm.modal();
        $confirm.on('')
    });

    $(document).on('click','._choose_services',function (event) {
        OrderDetail.load_service_fee();
    });

    $(document).on('click','._choose_services_express',function (event) {

        if ( !$( this ).prop( "checked" ) ){
            var data_disabled = $(this).attr("data-disabled");
            if(parseInt(data_disabled) == 1){
                $(this).attr("disabled","disabled");
            }
        }
        var services_type = $(this).val();
        OrderDetail.load_express_service_fee(OrderDetail.orderId,services_type);
    });

    $(document).on('click','._up_quantity',function (event) {
        event.preventDefault();
        var orderItemId = $(this).attr('data-item-id');

        var step = $(this).attr('data-step');

        var stock = $(this).parent().find('input').attr('data-stock');

        var input = $('._item_quantity_input[data-item-id="'+orderItemId+'"]');

        var new_quantity = parseInt(input.val()) + parseInt(step);

        if(new_quantity>stock){
            $(this).parent().parent().parent().find('._div_error').show();
            $(this).parent().parent().parent().find('._message_error').html('Số lượng đặt tối đa là '+stock);
            return false;
        }

        new_quantity = $.isNumeric(new_quantity) ? new_quantity : step;

        $('._item_quantity_input[data-item-id="'+orderItemId+'"]').val(new_quantity);

        input.val(new_quantity);


        OrderDetail.item_change_quantity(orderItemId, new_quantity);
    });

    $(document).on('click','._down_quantity',function (event) {
        event.preventDefault();
        var orderItemId = $(this).attr('data-item-id');

        var step = $(this).attr('data-step');

        var stock = $(this).parent().find('input').attr('data-stock');

        var input = $('._item_quantity_input[data-item-id="'+orderItemId+'"]');

        var new_quantity = parseInt(input.val()) - parseInt(step);

        new_quantity = $.isNumeric(new_quantity) ? new_quantity : step;

        new_quantity = (new_quantity<0)?step:new_quantity;

        $('._item_quantity_input[data-item-id="'+orderItemId+'"]').val(new_quantity);

        input.val(new_quantity);


        OrderDetail.item_change_quantity(orderItemId, new_quantity);

    });
    $(document).on('keyup','._item_quantity_input',function () {

        var orderItemId = $(this).attr('data-item-id');

        var step = $(this).attr('data-step');

        var stock = $(this).attr('data-stock');

        var input = $('._item_quantity_input[data-item-id="'+orderItemId+'"]');

        var new_quantity = input.val();

        new_quantity = $.isNumeric(new_quantity) ? new_quantity : step;

        if(new_quantity>stock){
            $(this).parent().find('._div_error').show();
            $(this).parent().find('._message_error').html('Số lượng đặt tối đa là '+stock);
            return false;
        }

        $('._item_quantity_input[data-item-id="'+orderItemId+'"]').val(new_quantity);

        if(isNaN(new_quantity)) return false;


        OrderDetail.item_change_quantity(orderItemId, new_quantity);
    });

    $(document).on('click','._delete_order', function (event) {
        event.preventDefault();
        var orderId = $(this).attr('data-order-id');
        $.ajax({
            url:  OrderDetail.base_url+'order_common/delete_order',
            type: "POST",
            data: {orderId:orderId}
        });
    });

    $(document).on('click','._change_status',function (event) {

        event.preventDefault();
        var status = $(this).attr('data-status');
        if(status == 'INIT') {
            var href = $(this).attr('data-deposit-link');
            location.href = href;
        }
        return false;
    });

    $(document).on('click','.act',function (event) {
        event.preventDefault();
        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
        var id = $(this).parent().attr('data-address-id');
        $('._idAddress').val(id);
        $('._submitAddress').click();
    });

    $(document).on('click','._submitAddress',function (event) {

        var idAddress = $('._idAddress').val();

        var idOder = $('._idOder').val();

        $.ajax({
            url:  OrderDetail.base_url+'OrderDetail/set_user_address',
            type: "POST",
            data: {idAddress:idAddress,idOder:idOder},
            success:function (result) {
                if(result.type!=1){
                    Global.sAlert(result.message);
                }else{
                    location.reload();
                }
            }
        });
    });

    $(document).on('click','._submit_buy_confirm',function() {
        $('._submit_buy_confirm').html('Đang xác nhận');
        var status = $(this).attr('data-status');
        var idOrder = $(this).attr('data-id');
        if(status == 'WAIT') {
            $.ajax({
                url:  OrderDetail.base_url+'OrderDetail/buyer_confirm',
                type: "POST",
                data: {id:idOrder,status:status},
                success:function (result) {
                    if(result.type!=1){
                        $('._submit_buy_confirm').html('Xác nhận');
                        Global.sAlert(result.message);
                    }else{
                        $('._submit_buy_confirm').html('✔ Đã xác nhận');
                        $('._submit_buy_confirm').toggleClass('btn-blue-active btn-blue _submit_buy_confirm');
                    }
                }
            });
        }
        $('._submit_buy_confirm').html('xác nhận');
        return false;
    });

});
