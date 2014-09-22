$(document).ready(function () {
    $('img').lazyload();
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
    var obj;
    $(document).on('click','a.left',function(){
        var parent = $(this).parent().parent().parent().parent();
        var value =
            (isNaN(parseInt($(parent).find('input[type=text]').val())) ?
                0 : parseInt($(parent).find('input[type=text]').val())) + parseInt(step);
        value = parseInt(value) < parseInt(beginAmount) ? parseInt(beginAmount) : parseInt(value);
        $(parent).find('input[type=text]').val(value);
        // Increase total amount
        var total = parseInt($($('.book-ct-footer').find('span')[0]).text()) + parseInt(step) < beginAmount ?
            beginAmount : parseInt($($('.book-ct-footer').find('span')[0]).text()) + parseInt(step);
        $($('.book-ct-footer').find('span')[0]).text(total);

        getTotalMoney(total);
    });
    
    $(document).on('click','a.right',function(){
        var parent = $(this).parent().parent().parent().parent();
        var value =
            (isNaN(parseInt($(parent).find('input[type=text]').val())) ?
                0 : parseInt($(parent).find('input[type=text]').val()));
        value = parseInt(value) <= 0 ? 0 : parseInt(value);
        if (value == 0) {
            return;
        }
        if (value <= beginAmount) {
            return;
        }

        $(parent).find('input[type=text]').val(value - parseInt(step));
        // Increase total amount
        $($('.book-ct-footer').find('span')[0]).text(
            parseInt($($('.book-ct-footer').find('span')[0]).text()) - parseInt(step)
        );
        getTotalMoney(parseInt($($('.book-ct-footer').find('span')[0]).text()));
    });

    $('input[name=input-amount]').each(function () {
        $(this).keyup(function () {
            var num = isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
            var total = 0;
            $('input[name=input-amount]').each(function () {
                total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
            });
            $($('.book-ct-footer').find('span')[0]).text(total);
            getTotalMoney(total);

            obj = $(this);
            setTimeout(function () {
                if (obj == null) {
                    return;
                }
                var num = isNaN(parseInt($(obj).val())) ? 0 : parseInt($(obj).val());
                if (num < parseInt(beginAmount) & num != 0) {
                    $(obj).val(beginAmount);
                    num = beginAmount;
                    Global.sAlert('Số lượng mua tối thiểu của sản phẩm này là ' + beginAmount);
                }
                if (num % step != 0 & num != 0) {
                    $(obj).val(num - num % step);
                    Global.sAlert('Số sản phẩm đặt phải là bội của ' + step);
                }
                // Check max amount
                if (num > $(obj).data('canbook')) {
                    $(obj).val($(obj).data('canbook'));
                    Global.sAlert('Số lượng trong kho không đủ');
                }
                var total = 0;
                $('input[name=input-amount]').each(function () {
                    total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
                });
                $($('.book-ct-footer').find('span')[0]).text(total);
                getTotalMoney(total);
            }, 1000);
        });
    });

    // Add cart
    $('button.order').click(function () {
        $(this).val("Đang xử lý...");
        $(this).attr('disabled');
        var book = {};
        book.data = {};
        // Get price by total items
        for (var o in price) {
            if (parseInt(price[o].begin) <= total &
                (isNaN(parseInt(price[o].end)) || parseInt(price[o].end) > parseInt(total))
                ) {
                book.price = price[o].price;
            }
        }
        if (typeof book.price == 'undefined') {
            book.price = 0;
        }
        book.item_id = item_id;
        book.link_origin = link_origin;
        book.wangwang = wangwang;
        book.data.stock = {};
        book.data.property_translate = {};
        var properties = {};
        var total = 0;
        $('div.my-tab-content').find('li').each(function () {
            var amount = $(this).find('input[name=input-amount]').val();
            if (isNaN(parseInt(amount)) || parseInt(amount) == 0) {
                return;
            }
            properties[$(this).data('key').replace(/>/, '-')] = {};
            properties[$(this).data('key').replace(/>/, '-')].imgUrl = $(this).data('color');
            properties[$(this).data('key').replace(/>/, '-')].amount = amount;
            properties[$(this).data('key').replace(/>/, '-')].price =
                $(this).find('input[name=input-amount]').data('price');
            total += parseInt(amount);
            book.data.stock[$(this).data('key').replace(/>/, '-')] =
                $(this).find('input[name=input-amount]').data('canbook');
            book.data.property_translate[$(this).data('key').replace(/>/, '-')] =
                $(this).find('input[name=input-amount]').data('translate');
        });
        book.properties = properties;
        book.description = $('div.book-ct-footer').find('textarea').val();
        book.shop = shop;
        book.shop_id = shop_id;
        book.data.title_init = $('span.title-page').data('source');
        book.title = $('span.title-page').html();
        book.require_min = beginAmount;
        book.item_img = $('img._images').attr('src');
        book.price_table = price;
        book.data.transfer_fee = transfer_fee;
        book.data.step = step;
                book.site = site;

        console.log(book);
        // Add cart
        $.ajax({
            url: url_add_cart,
            data: { item: JSON.stringify(book) },
            success: function (d) {
                $('button.order').val('Đặt hàng');
                $(this).removeAttr('disabled');
                if(d.type == 1){
                    $('._confirm_success').click();
                    return;
                }else{
                    if(d.message == null){
                        Global.sAlert("Không thể thêm sản phẩm vào giỏ");
                        return;
                    }else{
                        Global.sAlert(d.message);
                        return;
                    }
                    alert(d.message);
                }
            }
        });
    });
});
function getTotalMoney(total_item) {
    var total = 0, money = 0;
    for (var o in price) {
        if (parseInt(price[o].begin) <= total_item &
            (isNaN(parseInt(price[o].end)) || parseInt(price[o].end) >= parseInt(total_item))
            ) {
            money = price[o].price;
        }
    }
    if(!$.isNumeric(money)){
        money = money[0];
    }
    if(!$.isNumeric(money)){
        money = 0;
    }
    $('p.pricePerItem').html(formatCurrency(money * exchange) + '<sup>đ</sup>');
    $('div.item-tab-content li').each(function () {
        total += rounding(money * exchange) * ($.isNumeric(parseInt($(this).find('input[name=input-amount]').val())) ?
            parseInt($(this).find('input[name=input-amount]').val()) : 0);
    });
    total = $.isNumeric(total) ? total : 0;
    $('._total_price').text(formatCurrency(total));
}
    function formatCurrency(n) {
        return n.toFixed(0).replace(/./g, function (c, i, a) {
            return i && c !== "," && !((a.length - i) % 3) ? '.' + c : c;
        });
    }

    function rounding(money, round) {
        if (round == null) round = 1000;
        return (Math.floor(money / round) + 1) * round;
    }