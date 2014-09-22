var totalItem = 0;
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
    var canBookCount = $('._quantity_item').attr("data-canbook");
    canBookCount = parseInt(canBookCount);
    if(canBookCount == 0){
        $('button.order').attr("disabled","disabled");
    }
    $('.cart-alert').click(function () {
        $(this).fadeOut();
    });
    
    $(document).on('click','#myTab li a',function(){
        $('._ul_price').fadeOut().fadeIn();
    });

    var obj;

    $(document).on('click','a._left',function(){
        var parent = $(this).parent().parent().parent().parent();
        var value =
            (isNaN(parseInt($(parent).find('input[type=text]').val())) ?
                0 : parseInt($(parent).find('input[type=text]').val())) + parseInt(step);
        //value = parseInt(value) < parseInt(beginAmount) ? parseInt(beginAmount) : parseInt(value);
        value = parseInt(value) < parseInt(step) ? parseInt(step) : parseInt(value);
        $(parent).find('input[type=text]').val(value);
        // Increase total amount
        var total = 0;
        $('input[name=input-amount]').each(function () {
            total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
        });
        $($('.book-ct-footer').find('span')[0]).text(total);

        getTotalMoney(total, $(this));
    });

    $(document).on('click','a._right',function(){
        var parent = $(this).parent().parent().parent().parent();
        var value =
            (isNaN(parseInt($(parent).find('input[type=text]').val())) ?
                0 : parseInt($(parent).find('input[type=text]').val()));
        value = parseInt(value) <= 0 ? 0 : parseInt(value);
        if (value == 0) {
            return;
        }
        if (value < step) {
            return;
        }

        // Check canbook
        if(value > $(parent).find('input[name=input-amount]').data('canbook')) {
            value = $(parent).find('input[name=input-amount]').data('canbook');
        }

        $(parent).find('input[type=text]').val(value - parseInt(step));
        // Increase total amount
        var total = 0;
        $('input[name=input-amount]').each(function () {
            total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
        });
        $($('.book-ct-footer').find('span')[0]).text(total);

        getTotalMoney(parseInt($($('.book-ct-footer').find('span')[0]).text()), $(this));
    });

//    $('div.arowsl-ct').find('a.left').each(function () {
//        $(this).click(function () {
//
//
//        });
//    });

//    $('div.arowsl-ct').find('a.right').each(function () {
//        $(this).click(function () {
//
//
//        });
//    });

    $(document).on('keyup','input[name=input-amount]',function(e){
        if(e.keyCode == 38) { // Up
            $(this).parent().find('a.left').click();
        }
        if(e.keyCode == 40) { // down
            $(this).parent().find('a.right').click();
        }
        var num = isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
        var total = 0;
        $('input[name=input-amount]').each(function () {
            total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
        });
        $($('.book-ct-footer').find('span')[0]).text(total);
        getTotalMoney(total, $(obj));

        obj = $(this);
        setTimeout(function () {
            if (obj == null) {
                return;
            }
            var num = isNaN(parseInt($(obj).val())) ? 0 : parseInt($(obj).val());
            if (num < parseInt(step) & num != 0) {
                $(obj).val(step);
                num = step;
                /*var msg = 'Số lượng mua tối thiểu của sản phẩm này là ' + beginAmount;
                 $('#myModalfinishErr').find('p').html(msg);
                 $('.notify-button-err').click();*/
            }
            if (num % step != 0 & num != 0) {
                $(obj).val(num - num % step);

                /*var msg = 'Số sản phẩm đặt phải là bội của ' + step;
                 $('#myModalfinishErr').find('p').html(msg);
                 $('.notify-button-err').click();*/
            }
            // Check max amount
            if (num > $(obj).data('canbook')) {
                $(obj).val($(obj).data('canbook'));
                /*var msg = 'Số lượng trong kho không đủ';
                 $('#myModalfinishErr').find('p').html(msg);
                 $('.notify-button-err').click();*/
            }
            var total = 0;
            $('input[name=input-amount]').each(function () {
                total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
            });
            $($('.book-ct-footer').find('span')[0]).text(total);
            getTotalMoney(total, $(obj));
        }, 500);
    });

//    $('input[name=input-amount]').each(function () {
//        obj = $(this);
//        $(this).keyup(function () {
//            var num = isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
//            var total = 0;
//            $('input[name=input-amount]').each(function () {
//                total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
//            });
//            $($('.book-ct-footer').find('span')[0]).text(total);
//            getTotalMoney(total, $(obj));
//
//            obj = $(this);
//            setTimeout(function () {
//                if (obj == null) {
//                    return;
//                }
//                var num = isNaN(parseInt($(obj).val())) ? 0 : parseInt($(obj).val());
//                if (num < parseInt(step) & num != 0) {
//                    $(obj).val(step);
//                    num = step;
//                    /*var msg = 'Số lượng mua tối thiểu của sản phẩm này là ' + beginAmount;
//                     $('#myModalfinishErr').find('p').html(msg);
//                     $('.notify-button-err').click();*/
//                }
//                if (num % step != 0 & num != 0) {
//                    $(obj).val(num - num % step);
//
//                    /*var msg = 'Số sản phẩm đặt phải là bội của ' + step;
//                     $('#myModalfinishErr').find('p').html(msg);
//                     $('.notify-button-err').click();*/
//                }
//                // Check max amount
//                if (num > $(obj).data('canbook')) {
//                    $(obj).val($(obj).data('canbook'));
//                    /*var msg = 'Số lượng trong kho không đủ';
//                     $('#myModalfinishErr').find('p').html(msg);
//                     $('.notify-button-err').click();*/
//                }
//                var total = 0;
//                $('input[name=input-amount]').each(function () {
//                    total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
//                });
//                $($('.book-ct-footer').find('span')[0]).text(total);
//                getTotalMoney(total, $(obj));
//            }, 500);
//        });
//    }).keyup(function(e) {
//            if(e.keyCode == 38) { // Up
//                $(this).parent().find('a.left').click();
//            }
//            if(e.keyCode == 40) { // down
//                $(this).parent().find('a.right').click();
//            }
//        });

    // Add cart
    $(document).on('click','button._order_add_cart',function(e){
        e.preventDefault();
        if(order_link == 1){
            $(this).val("Đang xử lý...");
            $(this).attr('disabled');
        }

        $(this).attr('disabled');
        // disabled click event
        var curObj = $(this);
        $(this).addClass('disabled');
        var book = {};
        book.data = {};
        book.data.stock = {};
        book.data.property_translate = {};
        var properties = {};
        var total = 0;

        var price_origin = 0;

        $('div.my-tab-content').find('li').each(function () {

            var amount = $(this).find('input[name=input-amount]').val();
            if (isNaN(parseInt(amount)) || parseInt(amount) == 0) {
                return;
            }

            total += parseInt(amount);
            book.data.stock[$(this).data('key').replace(/>/, '-')] =
                $(this).find('input[name=input-amount]').data('canbook').toString().replace(/\./, '');
            book.data.property_translate[$(this).data('key').replace(/>/, '-')] =
                $(this).find('input[name=input-amount]').data('translate');

            /*if(distancePrice) {
                book.price += parseFloat(properties[$(this).data('key').replace(/>/, '-')].price * exchange * amount);
            }*/
        });

        if(total == 0) {
            $('.myModalNoItem').click();
            return;
        }

        // Get price by total items
        if(!distancePrice) {
            if(typeof price == 'object' ) {
                for (var o in price) {
                    var begin = price[o].begin;
                    var end = price[o].end;


                    if ((begin <= total && total <= end) || (begin <= total && (parseInt(end) == 0 || end == null))) {
                        price_origin = price[o].price;
                    }
                }

                if($.isArray(price_origin)){
                    price_origin = 0;
                }
            }else{
                try{
                    price = JSON.parse(price);
                    if(typeof price == 'object' ) {
                        console.log(price);
                        for (var o in price) {
                            var begin = price[o].begin;
                            var end = price[o].end;

                            console.log(begin);
                            console.log(end);

                            if ((begin <= total && total <= end) || (begin <= total && (parseInt(end) == 0 || end == null))) {
                                price_origin = price[o].price;
                            }
                            console.log(price_origin);
                        }

                        if($.isArray(price_origin)){
                            price_origin = 0;
                        }
                    }
                }catch (e){
                    price_origin = 0;
                }
            }
        }

        $('div.my-tab-content').find('li').each(function () {
            var price_promotion = 0;
            if(price_origin == 0){
                price_origin = $(this).find('input[name=input-amount]').data('price');
                price_promotion = 0;
            }

            if(website == 'TAOBAO' || website == 'TMALL'){
                price_origin = $(this).find('input[name=input-amount]').data('price');
            }
            if(is_promotion == 1){
                price_promotion = $(this).find('input[name=input-amount]').data('price');
            }
            var amount = $(this).find('input[name=input-amount]').val();
            if (isNaN(parseInt(amount)) || parseInt(amount) == 0) {
                return;
            }
            properties[$(this).data('key').replace(/>/, '-')] = {};
            properties[$(this).data('key').replace(/>/, '-')].imgUrl = $(this).data('color');
            properties[$(this).data('key').replace(/>/, '-')].amount = amount;
            properties[$(this).data('key').replace(/>/, '-')].price = price_origin;
            properties[$(this).data('key').replace(/>/, '-')].price_promotion = price_promotion;

            // Can auto pai: taobao, tmall
            properties[$(this).data('key').replace(/>/, '-')].outer_id = $(this).find('input[name=input-amount]').data('outer-id');
        });
        book.properties = properties;

        if (typeof book.price == 'undefined' || isNaN(book.price)) {
            book.price = 0;
        }
        var comment = $('._note_cart_item');
        if(comment.length > 0){
            book.comment = comment.val();
        }else{
            book.comment = $('div.book-ct-footer').find('textarea').val();
        }
        if(order_link == 1){
            book.tool = "Order Link";
        }else{
            book.tool = "Bookmarklet Iframe";
        }
        book.item_id = item_id;
        book.link_origin = link_origin;
        book.wangwang = wangwang;
        book.shop = shop;
        book.shop_id = shop_id;
        book.data.title_init = $('span.title-page').data('source');
        book.title = $('span.title-page').html();
        book.require_min = beginAmount;
        book.item_img = $('img._images').attr('src');
        book.price_table = price;
        book.data.transfer_fee = transfer_fee;
        book.data.step = step;
        book.site = website;
        if($('input[name=origin_price]').length > 0) {
            book.promotion_price = book.price;
            book.price = parseFloat($('input[name=origin_price]').val());
        }else if($('._promotion_price').length > 0){
            book.promotion_price = $('._promotion_price').val();
        }

        console.log(book);
        // Add cart
        $.ajax({
            url: root_url + 'cart/add',
            type: 'POST',
            data: { item: JSON.stringify(book) },
            success: function (d) {
                //alert('Bạn đã sản phẩm vào giỏ thành công!');
                if(order_link == 0){
                    if (d.type) {
                        //$('.notify-button').click();
                        $('span.item-order').html(totalItem);
                        $('._total_cart_item').html(
                            (isNaN(parseInt($('._total_cart_item').html())) ? 0 : parseInt($('._total_cart_item').html())) + totalItem
                        );
                        $('.cart-alert').fadeIn();
                        // Delay 5 seconds to close cart notify
                        setTimeout(function () {
                            $('.cart-alert').fadeOut();
                        }, 5000);
                        //$(curObj).removeClass('disabled');

                        // Reset data
                        $('input[name=input-amount]').val('');
                        $('p.pricePerItem').each(function() {
                            $(this).html(formatCurrency(($(this).data('money') * exchange)));
                        });

                        // Empty total current items choose
                        $('.book-ct-footer').find('p').each(function() {
                            $(this).find('span').first().html('0');
                        });

                    } else {
                        $(curObj).removeClass('disabled');
                        var msg = '&nbsp;&nbsp;Cố lỗi xảy ra khi đặt hàng. Hãy liên hệ nhân viên chăm sóc'
                            + '<br/>'
                            + 'khách hàng để được trợ giúp.';
                        $('#myModalfinishErr').find('p').html(msg);
                        $('.notify-button-err').click();
                    }
                } else if(order_link == 1) {
                    $('button.order').val('Đặt hàng');
                    $('button.order').removeAttr('disabled');
                    $('button.order').removeClass('disabled');
                    $('._quantity_item').val(0);
                    $('._total_item').text(0);
                    $('._total_price').text(0);

                    if(d.type == 1){
                        $('._confirm_success').click();
                        book = null;
                        CartBase.countCart();
                        return false;
                    }else{
                        if(d.message == null){
                            Global.sAlert("Không thể thêm sản phẩm vào giỏ");
                            return false;
                        }else{
                            Global.sAlert(d.message);
                            return false;
                        }
                    }
                }

            }, error: function() {
                $('button.order').removeClass('disabled');
            }
        });
    });
});

function getTotalMoney(total_item, obj) {
    var parent = $(obj).parent().parent().parent().parent().parent().parent();
    if($(obj).prop('tagName') == 'INPUT') {
        parent = $(obj).parent().parent().parent().parent().parent();
    }
    totalItem = 0; // Reset total item choose
    var total = 0, money = 0;
    /**/
    if(!distancePrice) { // Không có khoảng giá: Giá sp được tính trên tổng sp (gộp các loại mẫu khác nhau nếu có)
        for (var o in price) {
            money = price[0].price;
            if (parseInt(price[o].begin) <= total_item &
                (isNaN(parseInt(price[o].end)) || parseInt(price[o].end) >= parseInt(total_item))
                )
            {
                money = price[o].price;
                break;
            }
        }
        $('div.item-tab-content li').each(function () {
            total += (money * exchange) * (isNaN(parseInt($(this).find('input[name=input-amount]').val())) ?
                0 : parseInt($(this).find('input[name=input-amount]').val()));

            totalItem += isNaN(parseInt($(this).find('input[name=input-amount]').val())) ?
                0 : parseInt($(this).find('input[name=input-amount]').val());
        });
        if(price.length == 1) {
            // Giá trên từng sp là giống nhau
            money = $(parent).find('input[name=input-amount]').data('price');
            $('._ul_price').find('p.pricePerItem').html(formatCurrency((money * exchange)) + '<sup>đ</sup>');
        } else {
            $('._ul_price').parent().find('p.pricePerItem').html(formatCurrency((money * exchange)) + '<sup>đ</sup>')
        }

        if(!$.isNumeric( total )){
            total = 0;
            $('div.item-tab-content li').each(function () {
                var quantity = $.isNumeric($(this).find('input[name=input-amount]').val()) ? $(this).find('input[name=input-amount]').val() : 0;
                total += ($(this).find('input[name=input-amount]').data('price') * exchange) * quantity;

                totalItem += isNaN(parseInt($(this).find('input[name=input-amount]').val())) ?
                    0 : parseInt($(this).find('input[name=input-amount]').val());
            });
            console.log(total+"---total");
            money = $(parent).find('input[name=input-amount]').data('price');

            // Giá trên từng sp là khác nhau
            $('._ul_price').find('li').each(function() {
                money = $(this).find('input[name=input-amount]').data('price');
                $(this).find('p.pricePerItem').html(formatCurrency((money * exchange)) + '<sup>đ</sup>');
            });

        }

    } else { // Có khoảng giá
        $('div.item-tab-content li').each(function () {
            total += ($(this).find('input[name=input-amount]').data('price') * exchange)
                * (isNaN(parseInt($(this).find('input[name=input-amount]').val())) ? 0 : parseInt($(this).find('input[name=input-amount]').val()));

            totalItem += isNaN(parseInt($(this).find('input[name=input-amount]').val())) ?
                0 : parseInt($(this).find('input[name=input-amount]').val());
        });
        money = $(parent).find('input[name=input-amount]').data('price');

        // Giá trên từng sp là khác nhau
        $('._ul_price').find('li').each(function() {
            money = $(this).find('input[name=input-amount]').data('price');
            $(this).find('p.pricePerItem').html(formatCurrency((money * exchange)) + '<sup>đ</sup>');
        });
    }

    if(!$.isNumeric( total )){
        total = 0;
    }

    $($('.book-ct-footer').find('span')[1]).text(formatCurrency(total));

    if(total <= 0) {
        $('button.order').addClass('disabled');
    } else {
        $('button.order').removeClass('disabled');
    }
}

function formatCurrency(n) {
    return n.toFixed(0).replace(/./g, function (c, i, a) {
        return i && c !== "," && !((a.length - i) % 3) ? '.' + c : c;
    });
}

function rounding(money) {
    var round = 100;
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

var PaiTool = {
    translateText : function(){
        $('._text_translate').each(function(){
            var obj_translate = $(this);
            var text = $(this).attr("data-source");
            var type = $(this).attr("data-type");
            $.ajax({
                url : TranslateUrl,
                type : "POST",
                data : {
                    text:text,
                    type:type
                },
                success : function(data){
                    var result = $.parseJSON(data);
                    obj_translate.text(result.data_translate);
                }
            });
        });
    }
}

