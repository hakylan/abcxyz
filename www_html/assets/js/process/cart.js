var ajax_quantity = null;
$(document).ready(function(){
//    Cart.addDisable();
    $("img").lazyload({
        effect : "fadeIn"
    });
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
            Cart.addDisable();
            //$('._send_cart').addClass('load-wait');
        },
        complete:function(){
            $('._loading').hide();
            Cart.removeDisable();
            //$('._send_cart').removeClass('load-wait').html('Tiếp tục<span class="arow-next"></span>');
        },error : function(){
            Cart.removeDisable();
            $('._loading').hide();
        }
    });
    $(window).scroll(function(){
        var h = $(this).scrollTop() - $(document).height() + $(window).height();
        if ((h + 100) > 0){
            $(".scol-bottom-cart").addClass("block");
        }else{
            jQuery('.scol-bottom-cart').removeClass("block");
        }
    });

    $(document).on('click','._close_error',function(){
        $('._warning_require_min').removeClass('block');
    });


    $(document).on('click','._up_quantity',function(){
        var cart_id = $(this).attr('data-cart-id');
        Cart.checkQuantity(cart_id,'up');
    });
    
    $(document).on('click','._purchase_continue',function(){
        $('._link_detail_scroll').focus();
    });
    
    $(document).on('click','._down_quantity',function(){
        var cart_id = $(this).attr('data-cart-id');
        Cart.checkQuantity(cart_id,'down');
    });
    $(document).on('keyup','._quantity_item',function(){
        var cart_id = $(this).attr('data-cart-id');
        Cart.checkQuantity(cart_id,'');
    });

    $('#load_cart').loadingbar({
        done : function(result){
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            var data = $.parseJSON(result);
            var cart_id = $('#load_cart').attr('data-cart-id');
            if(data.type == 1){
                $('._li_item_cart[data-cart-id="'+cart_id+'"]').slideUp();
                $('._detail_cart[data-item-site-id='+data.item_site_id+']').attr('data-price',data.price_vnd);
                var price = Cart.currency_format(data.price_vnd,1)+"<sup>đ</sup>";
                $('._price_vnd[data-item-site-id='+data.item_site_id+']').html(price);
                $('._li_item_cart[data-cart-id="'+cart_id+'"]').remove();

                Cart.detectPrice(1);
                Cart.checkPriceSelect();
                CartBase.countCart();
            }else{
                Global.sAlert("Đã có lỗi xảy ra. Xin thử lại");
            }
        }
    });

    $('#payment_shop').loadingbar({
        done : function(result){
            var data = $.parseJSON(result);
            if(data.type == 1){
                Cart.removeDisable();
                window.location.href = data.url;
            }else{
                Cart.removeDisable();

                Global.sAlert(data.message);
            }
        }
    });

    setTimeout("Cart.detectPrice(1)",2000);
    Cart.checkPriceSelect();
    setTimeout("Cart.infoShopEach()",2000);


    $(document).on('click','._delete_items',function(){
        var cart_id = $(this).attr('data-cart-id');
        $('._li_item_cart[data-cart-id="'+cart_id+'"]').slideUp();
        $('#load_cart').attr('data-cart-id',cart_id);
        $('#load_cart').attr('data-href',cart_url+"/delete?id="+cart_id);
        $('#load_cart').click();
    });

    $(document).on('click','._payment_button',function(){

        $(this).find('span').text("Đang xử lý...");
        $(this).attr("disabled","disabled");

        var shop_id = $(this).attr('data-shop-id');

        var shop_array = [];

        shop_array.push(shop_id);

        shop_array = JSON.stringify(shop_array);

        var li_address = $('._li_address');
        var address_id = 0;
        li_address.each(function(){
            if($(this).hasClass('active')){
                address_id = $(this).attr('data-address-id');
            }
        });

        $('#payment_shop').attr('data-shop-id',shop_id);
        $('#payment_shop').attr('data-href',cart_url+"/payment?shop_ids="+shop_array+"&address_id="+address_id);

        var check_quantity = 1;
        $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
            var cart_id = $(this).attr('data-cart-id');
            var check = Cart.checkQuantity(cart_id,'');

            if(check == 0){
                check_quantity = 0;
            }
        });

        if(check_quantity == 1){
            $('#payment_shop').click()
        }
    });

    $(document).on('click','._send_cart_all',function(){

        if($(this).hasClass("disabled")){
            return;
        }

        $(this).find('span').text("Đang xử lý...");
        $(this).attr("disabled","disabled");

        var quantity = $('._total_item_all').text();

        if(quantity == 0 || quantity == '0' || quantity == ''){
            Global.sAlert("Bạn chưa chọn đơn hàng nào để thanh toán.");
            Cart.removeDisable();
            return;
        }

        var shop_array = [];
        var check_quantity = 1;

        $('._select_shop').each(function(){
            var flag = true;
            if($(this).prop('checked')) {
                var shop_id = $(this).val();
                if($('._payment_button[data-shop-id='+shop_id+']').hasClass("disabled")){
                    flag = false;
                }
                shop_array.push(shop_id);
                $('#payment_shop').attr('data-shop-id',shop_id);
                $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                    var cart_id = $(this).attr('data-cart-id');
                    var check = Cart.checkQuantity(cart_id,'',1);

                    if(check == 0){
                        check_quantity = 0;
                    }
                });
            }

            if(flag == false){
                $('._send_cart_all').addClass("disabled");
            }else{
                $('._send_cart_all').removeClass("disabled");
            }
        });
        shop_array = JSON.stringify(shop_array);
        $('#payment_shop').attr('data-href',cart_url+"/payment?shop_ids="+shop_array);

        if(check_quantity == 1){
            $('#payment_shop').click()
        }
    });

//    $('._select_all_cart').click(function(){
//        alert(2);
//    });

    $(document).on('click','._select_all_cart',function(){
        var checkboxes = $('#cart').find(':checkbox');
        if($(this).prop('checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
        Cart.checkPriceSelect();
    });

    $(document).on('click','._select_shop',function(){
        Cart.checkPriceSelect();
    });
})

var Cart = {
    infoShopEach : function(){
        $('._info_shop').each(function(){
            var shop_id = $(this).attr('data-shop-id');
            $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                var cart_id = $(this).attr('data-cart-id');
                Cart.checkQuantity(cart_id,'',0);
            });
        })
    },
    detectPrice : function(is_delete){
        $('._info_shop').each(function(){
            var shop_id = $(this).attr('data-shop-id');
            var total_item = 0;
            var total_price = 0;
            var quantity_item = $('._quantity_item[data-shop-id='+shop_id+']');
            if(quantity_item != null && quantity_item != '' && quantity_item != false){
                $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                    var quantity = $(this).val();
                    quantity = quantity == '' ? 0 : quantity;
                    var cart_id = $(this).attr('data-cart-id');
                    quantity = parseInt(quantity);
                    total_item += quantity;
                    var price = $('._total_price[data-cart-id="'+cart_id+'"]').attr('data-price');
                    price = parseInt(price);
                    total_price += price;
                });
                var total = parseInt(total_price);
                $('._total_item[data-shop-id='+shop_id+']').html(Cart.currency_format(total_item));
                $('._total_shop_price[data-shop-id='+shop_id+']').html(Cart.currency_format(total_price)+"<sup>đ</sup>");

                if(is_delete == 1){
                    if(total == 0){
                        $('._cart_shop[data-shop-id='+shop_id+']').fadeOut();
                        $('._cart_shop[data-shop-id='+shop_id+']').remove();
                        $.ajax({
                            url : cart_url+"/DeleteCartShop",
                            type : "POST",
                            data : {
                                shop_id : shop_id
                            },
                            success : function(){

                            }
                        });
                    }
                }

            }else{
                if(is_delete == 1){
                    $('._cart_shop[data-shop-id='+shop_id+']').fadeOut();
                    $('._cart_shop[data-shop-id='+shop_id+']').remove();
                    $.ajax({
                        url : cart_url+"/DeleteCartShop",
                        type : "POST",
                        data : {
                            shop_id : shop_id
                        },
                        success : function(){

                        }
                    });
                }

            }

        })
    },
    changeQuantity : function(quantity,cart_id){
        $('#click_num').val(0);
        $('#check_timeout').val(0);
        $.ajax({
            url : cart_url+"/save_quantity",
            type : "POST",
            data : {
                quantity : quantity,cart_id : cart_id
            },
            success : function(result){
                var data = $.parseJSON(result);
                if(data.type == 1){
                    CartBase.countCart();
                    if(data.site != "TAOBAO" && data.site != "TMALL"){
                        $('._detail_cart[data-item-site-id='+data.item_site_id+']').attr('data-price',data.price_vnd);
                        var price = Cart.currency_format(data.price_vnd,1)+"<sup>đ</sup>";
                        $('._price_vnd[data-item-site-id='+data.item_site_id+']').html(price);
                    }
                    Cart.detectPrice(0);
                    $('#click_num').val(0);
                    $('#check_timeout').val(0);
                }
            }
        });
    },
    changeQuantity1 : function(quantity,cart_id){
        var click_num = $('#click_num').val();

        click_num = parseInt(click_num);
        if(click_num > 0){
            $.ajax({
                url : cart_url+"/save_quantity",
                type : "POST",
                data : {
                    quantity : quantity,cart_id : cart_id
                },
                success : function(result){
                    var data = $.parseJSON(result);
                    if(data.type == 1){
                        CartBase.countCart();
                        $('._detail_cart[data-cart-id="'+cart_id+'"]').attr('data-price',data.price_vnd);
                        var price = Cart.currency_format(data.price_vnd)+"<sup>đ</sup>";
                        $('._price_vnd[data-cart-id="'+cart_id+'"]').html(price);
                    }
                    $('#click_num').val(0);
                    $('#check_timeout').val(0);
                }
            });
        }

    },
    checkQuantity : function(cart_id,action,is_submit){
        var check_quantity = 0;
        var input_quantity = $('._quantity_item[data-cart-id="'+cart_id+'"]');

        var current_quantity = input_quantity.val();
        var item_site_id = input_quantity.attr('data-item-site-id');
        var title = $('._title_item[data-cart-id="'+cart_id+'"]').text();
        var shop_id = input_quantity.attr('data-shop-id');
        var inventory = $('._detail_cart[data-cart-id="'+cart_id+'"]').attr('data-inventory');
        var require_min = $('._detail_cart[data-cart-id="'+cart_id+'"]').attr('data-require_min');
        var step = $('._detail_cart[data-cart-id="'+cart_id+'"]').attr('data-step');
        current_quantity = $.isNumeric(current_quantity) ? current_quantity : 0;

        inventory = $.isNumeric(inventory) ? inventory : 0;
        step = $.isNumeric(step) ? step : 1;
        require_min = $.isNumeric(require_min) ? require_min : 0;
        current_quantity = parseInt(current_quantity);
        step = parseInt(step);
        inventory = parseInt(inventory);
        require_min = parseInt(require_min);
        var quantity = 0;
        if(action == 'up'){
            quantity = current_quantity + step;
        }else if(action == 'down'){
            quantity = current_quantity - step;
        }else{
            quantity = current_quantity;
        }
        quantity = quantity > 0 ? quantity : '';

        input_quantity.val(quantity);

        var total_quantity = 0;

        $('._quantity_item[data-item-site-id='+item_site_id+']').each(function(){
            var quan = $(this).val();
            if(quan == ''){
                quan = 0;
            }
            quan = parseInt(quan);
            total_quantity += quan;
        });

        var quan_check = quantity >= 0 ? quantity : 0;

        if(total_quantity < require_min){
            Cart.removeDisable();
            $('._warning_require_min[data-shop-id='+shop_id+']').slideDown();
            $('._payment_button[data-shop-id='+shop_id+']').addClass("disabled");
            $('._item_title_error').text(title);
            $('._item_require_error').text(require_min);
//            Cart.showError(cart_id,"Sản phẩm yêu cầu đặt tối thiểu là "+require_min,'show');
            $('._quantity_item[data-cart-id="'+cart_id+'"]').focus();
            check_quantity = 0;
        }else{
            $('._payment_button[data-shop-id='+shop_id+']').removeClass("disabled");
            $('._warning_require_min[data-shop-id='+shop_id+']').slideUp();
        }
        if(quan_check % step != 0 || quan_check == 0){
            Cart.removeDisable();
            Cart.showError(cart_id,"Số lượng đặt phải là bội số của "+step,'show');
            check_quantity = 0;
        }else if(quan_check > inventory){
            Cart.removeDisable();
            Cart.showError(cart_id,"Số lượng trong kho còn lại là "+inventory,'show');
            check_quantity = 0;
        }else{
            Cart.showError(cart_id,'','');
            check_quantity = 1;
        }
        Cart.changePrice(quantity,cart_id);

        Cart.detectPrice(0);

        Cart.checkPriceSelect();

        if(is_submit == 0 && is_submit != null){
            return 0;
        }

        if(is_submit == 1){
            return check_quantity;
        }

//        Cart.changeQuantity(quantity,cart_id);

        if(check_quantity == 1){
            var click_num = $('#click_num').val();
            click_num = parseInt(click_num);

            if(click_num == 3){
                $('#click_num').val(0);
                $('#check_timeout').val(0);
                $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                    var cartId = $(this).attr("data-cart-id");
                    var quan = $(this).val();
                    if(quan == ''){
                        quan = 0;
                    }
                    quan = parseInt(quan);
                    Cart.changeQuantity(quan,cartId);
                });


            }else{
                click_num++;
                $('#click_num').val(click_num);
                var checkTimeOut = $('#check_timeout').val();
                checkTimeOut = parseInt(checkTimeOut);
                if(checkTimeOut == 0){
                    $('#check_timeout').val(1);

                    setTimeout(function(){
                        $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                            var cartId = $(this).attr("data-cart-id");
                            var quan = $(this).val();
                            if(quan == ''){
                                quan = 0;
                            }
                            quan = parseInt(quan);
                            Cart.changeQuantity1(quan,cartId);
                        });
                    },1500);
                }
            }
        }

        return check_quantity;

    },
    showError : function(cart_id,message,action){
        if(action == 'show'){
            $('._div_error[data-cart-id="'+cart_id+'"]').css('display','block');
            $('._quantity_item[data-cart-id="'+cart_id+'"]').addClass("form-myinput-warning");
            $('._quantity_item[data-cart-id="'+cart_id+'"]').removeClass("form-myinput");
            $('._message_error[data-cart-id="'+cart_id+'"]').text(message);
        }else{
            $('._div_error[data-cart-id="'+cart_id+'"]').css('display','none');
            $('._quantity_item[data-cart-id="'+cart_id+'"]').removeClass("form-myinput-warning");
            $('._quantity_item[data-cart-id="'+cart_id+'"]').addClass("form-myinput")
            $('._message_error[data-cart-id="'+cart_id+'"]').text(message);
        }

    },
    changePrice : function(quantity,cart_id){
        var unit_price = $('._detail_cart[data-cart-id="'+cart_id+'"]').attr('data-price');
        var price = Cart.currency_format(unit_price * quantity);
        $('._total_price[data-cart-id="'+cart_id+'"]').html(price+"<sup>đ</sup>");
        $('._total_price[data-cart-id="'+cart_id+'"]').attr('data-price',unit_price * quantity);
    },
    currency_format:function (num,is_format) {
        if(is_format != null){
            for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++){
                num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
            }
            return num;
        }else{
            if(num>0){
                num = num.toString().replace(/\$|\,/g,'');

                num = Math.floor(num*100+0.50000000001);
                num = Math.floor(num/100).toString();

                for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++){
                    num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
                }
                return (num );
            }
        }

        return '0';
    },
    checkPriceSelect : function(){
        var total_price = 0;
        var total_item = 0;
        var flag = true;
        $('._select_shop').each(function(){
            if($(this).prop('checked')) {
                var shop_id = $(this).val();
                if($('._payment_button[data-shop-id='+shop_id+']').hasClass("disabled")){
                    flag = false;
                }

                $('._quantity_item[data-shop-id='+shop_id+']').each(function(){
                    var quantity = $(this).val();
                    quantity = quantity == '' ? 0 : quantity;
                    var cart_id = $(this).attr('data-cart-id');
                    quantity = parseInt(quantity);
                    total_item += quantity;
                    var price = $('._total_price[data-cart-id="'+cart_id+'"]').attr('data-price');
                    price = parseInt(price);
                    total_price += price;
                });
            }
        });
        if(flag == false){
            $('._send_cart_all').addClass("disabled");
        }else{
            $('._send_cart_all').removeClass("disabled");
        }
        $('._total_item_all').html(Cart.currency_format(total_item));
        $('._total_price_all').html(Cart.currency_format(total_price)+"<sup>đ</sup>");
    },
    favoriteItem: function(item_id) {
        // Like
        $.ajax({
            url: fav_url,
            type: 'POST',
            data: { 'id' : item_id, 'type' : 'cart' },
            success: function(d) {
                Global.sAlert("Bạn đã lưu sản phẩm thành công!");
            }, error: function(err) {


            }
        })
    },
    removeDisable : function(){
        $('._button_top').find('span').text('Chọn dịch vụ');
        $('._button_buttom').removeAttr("disabled");
        $('._button_top').removeAttr("disabled");
        $('._button_buttom').find('span').text("Bước tiếp");
        $('._payment_button').find('span').text("CHỌN DỊCH VỤ");
        $('._payment_button').removeAttr("disabled");
    },
    addDisable : function(){
//        alert(4);
        $('._button_top').find('span').text('Đang xử lý ...');
        $('._button_buttom').attr("disabled","disabled");
        $('._button_top').attr("disabled","disabled");
        $('._button_buttom').find('span').text("Đang xử lý ...");
        $('._payment_button').find('span').text("Đang xử lý ...");
        $('._payment_button').attr("disabled","disabled");
    }
}