/**
 * Created by Admin on 2/26/14.
 */
var abortLoadFee = null;
$(document).ready(function(){

    $(window).scroll(function(){
        var h = $(this).scrollTop() - $(document).height() + $(window).height();
        if ((h + 130) > 0){
            $("._scroll_bottom").removeClass("scoll");
        }else{
            jQuery('._scroll_bottom').addClass("scoll");
        }
    });

    OrderInit.loadOrderItem();
    OrderInit.loadComment();
    $(document).on('click','._edit_comment',function(){
        $('._div_comment').hide();
        $('._edit_comment').show();
        $(this).hide();
        var item_id = $(this).attr("data-item-id");
        $('._div_comment[data-item-id='+item_id+']').fadeIn();
        $('._input_comment[data-item-id='+item_id+']').focus();
    });

    $(document).on('keypress','._input_comment',function(e){
        if(e.keyCode==13){
            var comment = $(this).val();
            var item_id = $(this).attr("data-item-id");
            $('._edit_comment[data-item-id='+item_id+']').text(comment);
            OrderInit.commentSuccess();
            $.ajax({
                url:  order_common+'/add_item_note',
                type: "POST",
                data: {orderItemId:item_id,comment:comment}
            });
        }
    });

    $(document).on('click','._submit_chat',function(event) {
        event.preventDefault();
        OrderInit.add_comment();
    });

    /* add comment */
    $(document).on('keyup','._input_order_comment',function(event) {
        event.preventDefault();
        if(event.keyCode == 13) {
            var order_id = $(this).attr('data-order-id');
            OrderInit.add_comment(order_id);
        }

    });

    $(document).on('click','._order_deposit',function(){
        $(this).find('span').text('Đang xử lý...');
        $(this).addClass('disable');
        var order_id = $(this).attr('data-order-id');

        var order_array = [];

        order_array.push(order_id);

        order_array = JSON.stringify(order_array);

        OrderInit.orderDeposit(order_array);
    });

    $(document).on('click','._order_deposit_all',function(){
        $(this).find('span').text('Đang xử lý...');
        $(this).addClass('disable');

        var quantity = $('#total_order').val();

        quantity = parseInt(quantity);

        if(quantity == 0){
            OrderInit.removeDisabledButton();
            Global.sAlert("Bạn chưa chọn đơn hàng nào để thanh toán.");
            return;
        }

        var order_array = [];
        var check_quantity = 1;

        $('._select_order').each(function(){
            if($(this).prop('checked')) {
                var order_id = $(this).val();
                order_array.push(order_id);
//                $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
//                    var cart_id = $(this).attr('data-cart-id');
//                    var check = Cart.checkQuantity(cart_id,'');
//
//                    if(check == 0){
//                        check_quantity = 0;
//                    }
//                });
            }
        });

        order_array = JSON.stringify(order_array);
        OrderInit.orderDeposit(order_array);
    });

    $(document).on('click','._delete_order',function(){

        var order_id = $(this).attr('data-order-id');
        OrderInit.deleteOrder(order_id);
    });

    $(document).on('click','._select_all_order',function(){
        var checkboxes = $('._order_content').find('._select_order');
        if($(this).prop('checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
        OrderInit.checkPriceSelect();
    });

    $(document).on('click','._select_order',function(){
        OrderInit.checkPriceSelect();
    });

    $(document).on('click','._delete_item',function(e){
        e.preventDefault();
        var item_id = $(this).attr('data-item-id');

        OrderInit.deleteOrderItem(item_id);
    });

    $(document).on('click','._up_quantity',function (event) {
        event.preventDefault();

        var item_id = $(this).attr('data-item-id');
        OrderInit.checkQuantity(item_id,'up');
    });

    $(document).on('click','._down_quantity',function (event) {
        event.preventDefault();

        var item_id = $(this).attr('data-item-id');
        OrderInit.checkQuantity(item_id,'down');
    });


    $(document).on('keyup','._quantity_item',function(){
        var item_id = $(this).attr('data-item-id');
        OrderInit.checkQuantity(item_id,'');
    });

    $(document).on('click','._choose_services',function (event) {
        var order_id = $(this).attr("data-order-id")
        OrderInit.load_service_fee(order_id);
    });

    $(document).on('click','._choose_services_express',function (event) {
        var order_id = $(this).attr("data-order-id");
        var services_type = $(this).val();
        OrderInit.load_express_service_fee(order_id,services_type);
    });
});

var OrderInit = {
    load_express_service_fee: function(order_id,services_type){
        $.ajax({
            url : order_common+'/chooseServices',
            type : "post",
            data : {
                order_id:order_id,
                services_type : services_type

            },
            success : function(data){
                if (data.type) {
                    if(services_type =='CHECKING'){
                        $('._checking_order[data-order-id=' + order_id + ']').html(Global.currency_format(data.fee)+"<sup>đ</sup>");
                        $('._checking_order[data-order-id=' + order_id + ']').attr('data-price', Global.currency_format(data.fee)+"<sup>đ</sup>");
                    }else if(services_type =='EXPRESS_CHINA_VIETNAM'){
                        if(parseInt(data.fee) > 0){
                            $('._shipping_order[data-order-id=' + order_id + ']').html(Global.currency_format(data.fee)+"<sup>đ</sup>");
                            $('._shipping_order[data-order-id=' + order_id + ']').attr('data-price', Global.currency_format(data.fee)+"<sup>đ</sup>");
                        }else{
                            $('._shipping_order[data-order-id=' + order_id + ']').html(Global.currency_format(data.fee_shipping_china_vn)+"<sup>đ</sup>");
                            $('._shipping_order[data-order-id=' + order_id + ']').attr('data-price', Global.currency_format(data.fee_shipping_china_vn)+"<sup>đ</sup>");
                        }

                    }else if(services_type =='PACKING'){

                    }
                    $('._total_order_price[data-order-id=' + order_id + ']').html(Global.currency_format(data.total_amount)+"<sup>đ</sup>");
                    $('._price_deposit_min[data-order-id=' + order_id + ']').html(Global.currency_format(data.deposit_amount)+"<sup>đ</sup>");
                } else {

                }
            }
        })
    },
    loadOrderItem : function(){
        $('._order_detail').each(function(){
            var order_id = $(this).attr("data-order-id");
            $.ajax({
                url : OrderInitUrl+'/load_order_item',
                type : "get",
                data : {
                    order_id:order_id
                },
                success : function(data){
                    $('._order_item_content[data-order-id='+order_id+']').html(data).fadeIn();
                }
            })
        });
    },
    commentSuccess : function(){
        $('._div_comment').hide();
        $('._edit_comment').show();
    },
    loadComment : function(){
        $('._order_detail').each(function(){
            var order_id = $(this).attr("data-order-id");
            $.ajax({
//                url : OrderInitUrl+'/load_order_comment',
                url : linkLoadOrderComments,
                type : "GET",
                data : {
                    order_id: order_id,
                    type: 'EXTERNAL'
                },
                success : function(result){
                    var listComment = $('._list_comments[data-order-id='+order_id+']');
                    listComment.prepend(order_comment_template(result));
                    $('._chat_order[data-order-id='+order_id+']').find('._loading_order').remove();
                    $('._chat_order[data-order-id='+order_id+']').append(result).fadeIn();
                }
            })
        });
    },
    // dau's edit add_comment 06/04/2014
    add_comment : function (orderId) {
        var inputComment = $('._input_order_comment[data-order-id='+orderId+']');
        var comment = inputComment.val();
        if(comment.length == 0) {
//            inputComment.css('border','1px solid red');
            inputComment.attr('placeholder','Vui lòng điền nội dung comment');
            return false;
        }
//        var date = new Date();
//        var d = date.getDate();
//        var m = date.getMonth()+1;
//        var y = date.getFullYear();
//        var time_chat =  d+'/'+m+'/'+y +' '+ date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
//        $('._time_chat').text(time_chat);
//        var temp_chat = $('._temp_comment');
//        var listComment = $('._list_comments[data-order-id='+orderId+']');
//        $('._chat_order[data-order-id='+orderId+']').find('._loading_order').remove();
//        temp_chat.find('._content_chat').html(comment);
//        listComment.prepend(temp_chat.html());
        inputComment.val('');
        inputComment.prop('disabled', true);
        /* right comment*/
        $.ajax({
            url: linkAddOrderComment,
            type: "POST",
            data: {order_id: orderId, message: comment, type: 'EXTERNAL'},
            success : function(result){
                inputComment.prop('disabled', false);
                var listComment = $('._list_comments[data-order-id='+orderId+']');
//                $('._chat_order[data-order-id='+orderId+']').find('._loading_order').remove();
                listComment.prepend(item_order_comment_template(result.info));
            },
            error: function() {
                inputComment.prop('disabled', false);
            }
        });
    },
    orderDeposit : function(order_ids){
        $.ajax({
            url : OrderInitUrl+"/order_deposit",
            type : "post",
            data : {
                order_ids : order_ids
            },
            success : function(data){
                OrderInit.removeDisabledButton();
                var result = $.parseJSON(data);
                if(result.type == 1){
                    window.location.href = result.url;
                }else{
                    Global.sAlert(result.message);
                    return;
                }
            }
        })
    },
    deleteOrder : function(order_id){
        $('._order_content[data-order-id='+order_id+']').slideUp();
        $.ajax({
            url : order_common+'/delete',
            type : "post",
            data : {
                orderId : order_id
            },
            success : function(data){
                if(data.type == 1){
                    $('._order_detail[data-order-id='+order_id+']').remove();
                    setTimeout(function(){
                        $('._order_content[data-order-id='+order_id+']').remove();
                    },1000);
                }else{
                    $('._order_content[data-order-id='+order_id+']').slideDown();
                    Global.sAlert(data.message);
                }
            },
            error : function(){
                $('._order_content[data-order-id='+order_id+']').slideDown();
                Global.sAlert("Có lỗi xảy ra khi xóa đơn hàng");
            }
        })
    },
    checkPriceSelect : function(){
        var total_price = 0;
        var total_order = 0;
        var total_item = 0;
        $('._select_order').each(function(){
            if($(this).prop('checked')) {
                total_order += 1;
                var order_id = $(this).val();
                $('._quantity_item[data-order-id='+order_id+']').each(function(){
                    var quantity = $(this).val();
                    var order_item_id = $(this).attr('data-item-id');
                    quantity = parseFloat(quantity);
                    total_item += quantity;
                });
                $('._price_deposit_min[data-order-id='+order_id+']').each(function(){
                    var price = parseFloat($(this).attr("data-deposit-min"));
                    if(!$.isNumeric(price)){
                        price = 0;
                    }
                    total_price += price;
                });
            }
        });
        $('#total_order').val(total_order);
        $('._total_order').text(total_order+" Đơn / "+total_item + " Sản Phẩm");
        $('._total_order_count').text("Đặt cọc "+total_order+ " đơn");
        $('._total_deposit').html(Global.currency_format(total_price)+"<sup>đ</sup>");
    },
    removeDisabledButton : function(){
        $('._order_deposit').find('span').text('Đặt cọc');
        $('._order_deposit').removeClass('disable');
        $('._order_deposit_all').find('span').text('Đặt cọc 0 đơn');
        $('._order_deposit_all').removeClass('disable');
    },
    deleteOrderItem : function(order_item_id){
        $('._li_order_item[data-item-id='+order_item_id+']').fadeOut();
        var order_id = $('._li_order_item[data-item-id='+order_item_id+']').attr('data-order-id');
        $.ajax({
            url : OrderInitUrl+"/delete_order_item",
            type : "post",
            data : {
                order_item_id : order_item_id
            },
            success : function(data){
                var result = data; //$.parseJSON(data);
                if(result.type == 1){
                    $('._li_order_item[data-item-id='+order_item_id+']').remove();
                    if(result.delete_order == 1){
                        OrderInit.deleteOrder(order_id);
                    }
                }else if(result.type == 0){
                    $('._li_order_item[data-item-id='+order_item_id+']').fadeIn();
                    Global.sAlert(result.message);
                    if(result.element != '' && result.element + "" != "undefined"){
                        window.location.href = result.element;
                    }
                }
            }
        })
    },
    detectPrice : function(is_delete){
        $('._order_detail').each(function(){
            var order_id = $(this).attr('data-order-id');
            var total_price = 0;
            var total_order = 0;
            var total_item = 0;
            var quantity_item = $('._quantity_item[data-order-id='+order_id+']');
            if(quantity_item.length > 0){
                quantity_item.each(function(){
                    var quantity = $(this).val();
                    quantity = quantity == '' ? 0 : quantity;
                    var item_id = $(this).attr('data-item-id');
                    quantity = parseInt(quantity);
                    total_item += quantity;
                    var price = $('._total_price[data-item-id='+item_id+']').attr('data-price');
                    price = parseInt(price);
                    total_price += price;
                });
                var total = parseInt(total_price);
                $('._total_item[data-order-id='+order_id+']').html(Global.currency_format(total_item));
                $('._total_order_price[data-order-id='+order_id+']').html(Global.currency_format(total_price)+"<sup>đ</sup>");
                $('._total_order_price[data-order-id='+order_id+']').attr('data-price',total_price);
                OrderInit.getTotalAmount(order_id);

                if(is_delete == 1){
                    if(total == 0){
                        $('._order_content[data-order-id='+order_id+']').fadeOut();
                        $('._order_content[data-order-id='+order_id+']').remove();
                        OrderInit.deleteOrder(order_id);
                    }
                }

            }else{
                if(is_delete == 1){
                    $('._order_content[data-order-id='+order_id+']').fadeOut();
                    $('._order_content[data-order-id='+order_id+']').remove();
                    OrderInit.deleteOrder(order_id);
                }

            }

        })
    },
    checkQuantity : function(item_id,action,is_submit){
        var check_quantity = 0;

        var current_quantity = $('._quantity_item[data-item-id='+item_id+']').val();
        var inventory = $('._detail_order_item[data-item-id='+item_id+']').attr('data-inventory');
        var require_min = $('._detail_order_item[data-item-id='+item_id+']').attr('data-require_min');
        var step = $('._detail_order_item[data-item-id='+item_id+']').attr('data-step');
        current_quantity = $.isNumeric(current_quantity) ? current_quantity : 0;

        inventory = $.isNumeric(inventory) ? inventory : 0;
        step = $.isNumeric(step) ? step : 1;
        require_min = $.isNumeric(require_min) ? require_min : 0;
        current_quantity = parseInt(current_quantity);
        step = parseInt(step);
        inventory = parseInt(inventory);
        require_min = parseInt(require_min);
        if(action == 'up'){
            var quantity = current_quantity + step;
        }else if(action == 'down'){
            var quantity = current_quantity - step;
        }else{
            var quantity = current_quantity;
        }

        quantity = quantity > 0 ? quantity : '';

        var quan_check = quantity >= 0 ? quantity : 0;

        if(quan_check < require_min){
            OrderInit.showError(item_id,"Sản phẩm yêu cầu mua tối thiểu là "+require_min,'show');
            $('._quantity_item[data-item-id='+item_id+']').focus();
            check_quantity = 0;
        }else if(quan_check % step != 0){
            OrderInit.showError(item_id,"Số lượng phải là bội số của "+step,'show');
            check_quantity = 0;
        }else if(quan_check > inventory){
            if(inventory<= 0){
                OrderInit.showError(item_id,"Sản phẩm đã hết hàng, hãy xóa sản phẩm để tiếp tục mua hàng",'show');
            }else{
                OrderInit.showError(item_id,"Số lượng trong kho còn lại là "+inventory,'show');
            }
            check_quantity = 0;
        }else{
            OrderInit.showError(item_id,'','');
            check_quantity = 1;
        }
        $('._quantity_item[data-item-id='+item_id+']').val(quantity);
        $('._quantity_item_span[data-item-id='+item_id+']').text(quantity);

        OrderInit.changePrice(quantity,item_id);

        if(is_submit == 0 && is_submit != null){
            return;
        }

        OrderInit.detectPrice(0);

        OrderInit.checkPriceSelect();

        if(check_quantity == 1){
            var click_num = $('#click_num').val();
            click_num = parseInt(click_num);

            if(click_num == 3){
                $('#click_num').val(0);
                $('#check_timeout').val(0);
                OrderInit.changeQuantity(quantity,item_id);
            }else{
                click_num++;
                $('#click_num').val(click_num);
                var checkTimeOut = $('#check_timeout').val();
                checkTimeOut = parseInt(checkTimeOut);
                if(checkTimeOut == 0){
                    $('#check_timeout').val(1);
                    setTimeout("OrderInit.changeQuantity1("+quantity+","+item_id+")",1500);
                }
            }
        }

        return check_quantity;

    },
    showError : function(item_id,message,action){
        if(action == 'show'){
            $('._div_error[data-item-id='+item_id+']').css('display','block');
            $('._message_error[data-item-id='+item_id+']').text(message);
        }else{
            $('._div_error[data-item-id='+item_id+']').css('display','none');
            $('._message_error[data-item-id='+item_id+']').text(message);
        }

    },
    changePrice : function(quantity,item_id){
        var unit_price = $('._detail_order_item[data-item-id='+item_id+']').attr('data-price');
        console.log(unit_price);
        console.log(quantity);
        var price = Global.currency_format(unit_price * quantity);
        $('._total_price[data-item-id='+item_id+']').html(price+"<sup>đ</sup>");
        $('._total_price[data-item-id='+item_id+']').attr('data-price',unit_price * quantity);
    },
    changeQuantity : function(quantity,item_id){
        var order_id = $('._quantity_item[data-item-id='+item_id+']').attr('data-order-id');
        $('#click_num').val(0);
        $('#check_timeout').val(0);
        $.ajax({
            url : order_common+"/change_quantity",
            type : "POST",
            data : {
                quantity : quantity,orderItemId : item_id
            },
            success : function(result){
                if(result.type == 1){
                    var data = result.data;
                    $('._price_deposit_min[data-order-id='+order_id+']').html(data.deposit_amount);
                    $('._total_price_order_all[data-order-id='+order_id+']').html(data.order_total_amount);
                    $('._real_amount[data-order-id='+order_id+']').html(Global.currency_format(data.real_amount)+"<sup>đ</sup>");
//                    var price = Global.currency_format(data.price_vnd)+"<sup>đ</sup>";
//                    $('._price_vnd[data-item-id='+item_id+']').html(price);
                }
                $('#click_num').val(0);
                $('#check_timeout').val(0);
            }
        });
    },
    changeQuantity1 : function(quantity,orderItemId){
        var order_id = $('._quantity_item[data-item-id='+orderItemId+']').attr('data-order-id');
        var click_num = $('#click_num').val();

        click_num = parseInt(click_num);
        if(click_num > 0){
            $.ajax({
                url : order_common+"/change_quantity",
                type : "POST",
                data : {
                    quantity : quantity,orderItemId : orderItemId
                },
                success : function(result){
                    if(result.type == 1){
                        var data = result.data;
                        $('._price_deposit_min[data-order-id='+order_id+']').html(data.deposit_amount);
                        $('._total_price_order_all[data-order-id='+order_id+']').html(data.order_total_amount);
                        $('._real_amount[data-order-id='+order_id+']').html(Global.currency_format(data.real_amount)+"<sup>đ</sup>");
//                    var price = Global.currency_format(data.price_vnd)+"<sup>đ</sup>";
//                    $('._price_vnd[data-item-id='+item_id+']').html(price);
                    }

                    $('#click_num').val(0);
                    $('#check_timeout').val(0);
                }
            });
        }

    },
    getTotalAmount : function(order_id){
        var buying = $('._buying_order[data-order-id='+order_id+']').attr('data-price');
        var checking = $('._checking_order[data-order-id='+order_id+']').attr('data-price');
        var shipping = $('._shipping_order[data-order-id='+order_id+']').attr('data-price');
        var total_order_price = $('._total_order_price[data-order-id='+order_id+']').attr('data-price');
        buying = parseInt(buying);
        checking = parseInt(checking);
        shipping = parseInt(shipping);
        total_order_price = parseInt(total_order_price);
        var total = total_order_price + buying + checking + shipping;
        $('._total_price_order_all[data-order-id='+order_id+']').attr('data-price',total);
        $('._total_price_order_all[data-order-id='+order_id+']').html(Global.currency_format(total)+"<sup>đ</sup>")
    },
    load_service_fee:function (order_id) {

        var data = {};
        var services = {};
        var normalItemCount = 0, accessItemCount = 0, totalAmount = 0, i = 0;
        $('._choose_services[data-order-id='+order_id+']').each(function () {
            if($(this).is(':checked')) {
                i++;
                services[i] = $(this).val();
            }
        });
        services[i+1] = 'BUYING';

        data.services = services;
        data.order_id = order_id;
        var total_weight = 0;
        $('._order_item_weight[data-order-id='+order_id+']').each(function () {
            total_weight+=parseFloat($(this).text());
        });
        data.totalWeight = total_weight;
        data.targetCode = $('#targetCode[data-order-id='+order_id+']').val();
        $('._order_item_type[data-order-id='+order_id+']').each(function () {
            var order_item_id=$(this).attr('data-item-id');
            var quantity = $('._quantity_item[data-item-id="'+order_item_id+'"]').val();
            if($(this).val() == 'access') {
                accessItemCount+= parseInt(quantity);
            }else{
                normalItemCount+= parseInt(quantity);
            }
        });
        data.normalItemCount = normalItemCount;
        data.accessItemCount = accessItemCount;

        $('._total_price[data-order-id='+order_id+']').each(function () {
            totalAmount+=parseFloat($(this).attr('data-price'));
        });
        data.totalAmount = totalAmount+" vnd";
        if(abortLoadFee != null){
            abortLoadFee.abort();
        }
        var abortLoadFee = $.ajax({
            url:  ServicesCalc,
            type: "POST",
            data: {data : data},
            success: function (result) {
                var data = result.data;
                $('._buying_order[data-order-id='+order_id+']').html(Global.currency_format(data.buyingFee)+"<sup>đ</sup>");
                $('._buying_order[data-order-id='+order_id+']').attr('data-price',data.buyingFee);
                var checking = parseInt(data.checkingFee);
                if(checking == 0){
                    $('._checking_order[data-order-id='+order_id+']').html("~~");
                }else{
                    $('._checking_order[data-order-id='+order_id+']').html(Global.currency_format(data.checkingFee)+"<sup>đ</sup>");
                }
                $('._checking_order[data-order-id='+order_id+']').attr('data-price',data.checkingFee);
                $('._shipping_order[data-order-id=' + order_id + ']').html("~~");
                $('._shipping_order[data-order-id=' + order_id + ']').attr('data-price', 0);

            }
        });
    }
}
