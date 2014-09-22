/**
 * Created by Tan on 2/13/14.
 */

var validRequireMin = [];
var validItemTitle  = [];
var shopValidate    = [];
var actDel = false;
$(document).ready(function () {

    calcOrderSum();
    ChooseService.calculatorService();

    $('body').on('click', '._choose_service', function (e) {
        $('._checking_discount_fee').hide();
        var $obj = $(this);
        var $svType = $obj.val();
        var $shopId = $obj.data('shop-id');
        var $selectedObj = $('#_selected_services_' + $shopId);
        var $selectedSv = $selectedObj.val();

        var checked = $obj.prop("checked");
        if (checked) {
            $selectedSv = $selectedSv + ',' + $svType;
        }
        if (!checked) {
            //$selectedSv = $selectedSv.replace(',' + $svType, '');
            var listService = $selectedSv.split(',');
            for(var i = 0; i < listService.length; i++) {
                if(listService[i] == $svType) {
                    delete listService[i];
                }
            }
            $selectedSv = '', comma = '';
            for(var i =0; i < listService.length; i++) {
                if(listService[i] == undefined) { continue; }
                $selectedSv += comma + listService[i];
                comma = ',';
            }
            // Set amount for checking is zero
            if($(this).data('type') == 'checking') {
                $('#_totalCheckingFee_' + $(this).data('shop-id')).html('0');
            } else {
                $('.total_packing_money').html('0');
                $('#_totalShippingFee_' + $(this).data('shop-id')).html(
                    formatCurrency($('#_totalShippingFee_' + $(this).data('shop-id')).data('fix'))
                );
            }
        }
        $selectedObj.val($selectedSv);

        if(checked) {
            /*$.post(choose_service_url + '/ChangeService', {'sv': $selectedSv, 'sid': $shopId}, function (r) {
             $('#_totalAmount_' + $shopId).html(number_format(r.totalAmount));
             $('#_totalBuyingFee_' + $shopId).html(number_format(r.totalBuyingFee));
             $('#_totalCheckingFee_' + $shopId).html(number_format(r.totalCheckingFee));
             $('#_totalPackingFee_' + $shopId).html(number_format(r.totalPackingFee));
             $('#_totalFee_' + $shopId).html(number_format(r.totalFee));
             calcOrderSum();
             }, 'json');*/
            var obj = $('#_shopOrder_' + $(this).data('shop-id'));
            ChooseService.loadServiceByShop($(obj),
                parseInt($(obj).find('b[id^=_totalFee_]').html().replace(/,|\./g, '')),
                parseInt($(obj).find('b[id^=_cartItemCount_]').html().replace(/,|\./g, '')),
                0,
                $('#provinceReceiveId').val(),
                $(obj).find('#weight_' + $(obj).data('shop-id')).val()
            );
        }
    });

    $('body').on('keypress', '._commentCart', function (e) {

        if (e.keyCode == 13) {
            var $cmtObj = $(this);
            var $shopId = $cmtObj.data('shop-id');
            var $cmtContent = $cmtObj.val();
            $.post(choose_service_url + '/PostCommentOrder', {'sid': $shopId, 'c': $cmtContent}, function (data) {
                //console.log(result);
                if (data.error == 0) {
                    //console.log(result);
                    result = data.result;
                    $cmtObj.val('');
                    var $txt = '';
                    $txt += '<div class="mychat-item">';
                    $txt += '<div class="item-avatar">';
                    $txt += '<img src="'+result.avatar+'"/>';
                    $txt += '</div>';
                    $txt += '<div class="item-ct">';
                    $txt += '<p class="normal"><span class="normal-blod">'+result.fullname+'</span>';
                    $txt += '<span class="italic">';
                    $txt += result.created_time;
                    $txt += '</span>';
                    $txt += '</p>';
                    $txt += '<p class="normal">' + result.content + '</p>';
                    $txt += '</div>';
                    $txt += '</div>';

                    console.log('#_commentCartContent_' + $shopId);

                    $('#_commentCartContent_' + $shopId).prepend($txt);
                }
            },'json');
        }
    });

    $('body').on('click', '.change_quantity', function (e) {

        e.preventDefault();

        var $Obj        = $(this);
        var $type       = $Obj.data('change-type');
        var inputObj    = $(this).parent().parent().parent().find('input');
        var $step       = parseInt($(inputObj).data('step')) ? parseInt($(inputObj).data('step')) : 1;
        var $cartIdObj  = $Obj.data('cart-id-obj');
        var $cartId     = $Obj.data('cart-id');
        var $shopId     = $Obj.data('shop-id');
        var $Target     = $($cartIdObj);
        var $cTargetVal = parseInt($Target.val());
        if(curQuantity != $cTargetVal) {
            curQuantity = $cTargetVal;
        }
        var $newVal     = $cTargetVal;
        var $selectedSv = $('#_selected_services_' + $shopId).val();

        $newVal += ($type == 'up' ? $step : -$step);
        // Increase amount and check require min
        if($newVal <= $(inputObj).data('step')) { // require
            $newVal = $(inputObj).data('step');
        }
        $Target.val($newVal);
        // ReCalculator when change item quantity
        if(curQuantity != $newVal)
            ChooseService.updateItem($cartId, $newVal, $selectedSv, $shopId);

        // Validate require min for this item
        var amount = 0;
        $('input[id^=item_quantity_]').each(function(index) {
            if($(this).data('item-id') == $(inputObj).data('item-id')) {
                amount += parseInt($(this).val());
            }
        });

        var require_min = $(inputObj).data('require');
        var title = $(inputObj).parent().parent().parent().parent().parent().find('li:eq(0)').find('a:eq(0)').html().trim();
        var shopId = $(inputObj).data('shop-id');

        if(amount < require_min) {

            validItemTitle[shopId] = title;
            validRequireMin[shopId] = require_min;

            var isSet = false;
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        isSet = true;
                        break;
                    }
                }
            }
            if(!isSet) { shopValidate.push(shopId); }

        } else {
            // validate, then remove it from list
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        shopValidate.splice(i, 1);
                        if(typeof validItemTitle[shopId] != undefined) {
                            validItemTitle.splice(i, 1);
                        }
                    }
                }
            }
        }

    });

    // Close notify message
    $('li.delete-list-item').each(function() {
        var obj = $(this);
        $(this).find('.delete-itemshop').click(function() { $(obj).removeClass('block'); });
    });

    $('body').on('click', '._removeCartShop', function (e) {
        e.preventDefault();

        var $obj = $(this);
        var $shopId = $obj.data('shop-id');

        $.post(delCartByShop, {'shop_id': $shopId}, function (result) {

            $('#_shopOrder_' + $shopId).remove();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            calcOrderSum();
            // Reload form if no shop
            if($('section[id^=_shopOrder_]').length == 0)
                window.location = window.location.href;
        });


    });

    $('body').on('click', '._removeCartItem', function (e) {
        e.preventDefault();
        var itemId = $(this).data('cart-id');
        // Set item quantity to zero
        $('#item_quantity_' + itemId).val(0);
        actDel = true;
        $('#item_quantity_' + itemId).keyup();

    });

    $('#submitOrders').click(function (e) {
        e.preventDefault();

        if(shopValidate.length > 0) {
            var obj = null;
            for(var i = 0; i < shopValidate.length; i++) {
                $('ul.cart-list-content').each(function() {
                    //console.log(shopValidate[i]);
                    if($(this).data('shop') == shopValidate[i]) {
                        if(obj == null) {
                            obj = $(this);
                        }
                        $(this).find('.delete-list-item').find('.item-name').html(validItemTitle[shopValidate[i]]);
                        $(this).find('.delete-list-item').find('.item-require-min').html(validRequireMin[shopValidate[i]]);
                        $(this).find('.delete-list-item').addClass('block');
                    }
                });
            }
            // Scroll to obj
            if(obj != null) {
                $('html, body').animate({
                    scrollTop: $(obj).offset().top - 100
                }, 1000);
            }
            return; // ------- TEST
        }

        $(this).text('Vui lòng chờ...');
        $(this).addClass('disabled');
        $(this).attr("disabled", "disabled");

        ChooseService.submitOrder();

        /*var orderData = [];
        var addressId = $('#receiveAddressId').val();
        $('._selected_services').each(function () {
            var $Obj = $(this);
            var $shopId = $Obj.data('shop-id');
            var serviceChoose = $Obj.val().split(',');
            var serviceData = {};
            for(var i = 0; i < serviceChoose.length; i++) {
                console.log(serviceChoose[i]);
                serviceData[serviceChoose[i]] =
                    $('span.' + serviceChoose[i] + '_money') != null ?
                        $('span.' + serviceChoose[i] + '_money').html().replace(/,|\./g, '') : 0;
            }

            var item = [];
            item.push($shopId, JSON.stringify(serviceData), addressId);
            orderData.push(item);
        });

        console.log(orderData);

        return orderData;

        *//*$.post(choose_service_url + '/SubmitOrder', {'data': orderData, 'aid': addressId}, function (r) {
            console.log(r);
            if (r.error == 0) {
                document.location.href = r.redirectUrl;
            } else {
                $(this).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                // Notify alert message
                $('#myModalErr').find('p').html(r.msg);
                $('button.myModalErr').click();
            }
        }, 'json');*//*
        var obj = $(this);
        $.ajax({
            url: choose_service_url + '/SubmitOrder',
            type: 'POST',
            data: { 'data': orderData, 'aid': addressId },
            dataType: 'json',
            success: function(r) {
                if (r.error == 0) {
                    $(this).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                    document.location.href = r.redirectUrl;
                } else {
                    $(this).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                    // Notify alert message
                    $('#myModalErr').find('p').html(r.msg);
                    $('button.myModalErr').click();
                }
            }, error: function(xhr, status, error) {
                $(obj).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                // Notify alert message
                $('#myModalErr').find('p').html('Có lỗi khi kết đơn, hãy thử lại!'); // xhr.responseText
                $('button.myModalErr').click();
            }
        });*/
    });

    $('._item_note').click(function (event) {
        event.stopPropagation();
        var $obj = $(this);
        var $cartId = $obj.data('cart-id');

        $obj.addClass('note-none');
        $obj.next().addClass('note-submit-block');

        // Focus to textbox
        $obj.next().find('input').focus();
        var __focus = $obj.next().find('input');
        $(__focus).keyup(function(e) {
            if (e.keyCode == 13) {

                var $note = $(this).val();
                var $cartId = $(this).data('cart-id');
                $.post(choose_service_url + '/UpdateItemNote', {'cid': $cartId, 'c': $note}, function (r) {
                    if (r.error != 0) {
                        // Notify error save note
                    }
                }, 'json');
                // Change data
                if($note.replace(/\s/g, '') == '') {
                    $note = 'Click vào đây nếu bạn muốn ghi chú thích cho sản phẩm';
                }
                $('#_item_note_' + $cartId).html($note);
                $('.note-submit').removeClass('note-submit-block');
                $('._item_note').removeClass('note-none');
            }
        });

    });

    $('html').click(function () {
        $('.note-submit').removeClass('note-submit-block');
        $('._item_note').removeClass('note-none');
    });

    // Dropdown box message on change quantity
    $('.tooltip1').css({'display' : 'none'});

    var curQuantity = 0, item_timer = null, hide_dropdow = false;
    $('input[id^=item_quantity_]').each(function(index) {
        $(this).keyup(function(e) {
            var itemObj = $(this);
            clearTimeout(item_timer);
            if(hide_dropdow) $(itemObj).parent().find('.dropdown-menu').fadeOut();
            if($(itemObj).val().replace(/\s/, '') != '' & !isNaN($(itemObj).val())) {
                if(parseInt($(itemObj).val()) < $(itemObj).data('step') & !actDel) { // require
                    item_timer = setTimeout(function() {
                        hide_dropdow = false;
                        $(itemObj).val($(itemObj).data('step')); // require
                        // Show notify require
                        $(itemObj).parent().find('.dropdown-menu').fadeIn();
                        // Calculator again
                        $(itemObj).keyup();

                        setTimeout(function() {
                            $('.tooltip1.dropdown-menu').fadeOut();
                        }, 2000);
                    }, 1500);
                    return;
                }
                hide_dropdow = true;

                // Check step
                if(parseInt($(this).val()) % parseInt($(this).data('step')) != 0) {
                    $(this).val(parseInt($(this).data('step') *
                        (Math.floor(parseInt($(this).val()) / parseInt($(this).data('step'))) + 1))
                    );
                    setTimeout(function() {
                        $('.tooltip1.dropdown-menu').fadeOut();
                    }, 2000);
                }

                var shopId     = $(this).parent().find('a.left').data('shop-id');
                // Call ajax
                if(curQuantity != $(this).val() || actDel) {
                    curQuantity = $(this).val();
                    var itemId = $(this).parent().find('a.left').data('cart-id');
                    var service = $('#_selected_services_' + $(this).parent().find('a.left').data('shop-id')).val();
                    ChooseService.updateItem(itemId, $(this).val(), service, shopId);
                }

                // Validate require min for this item
                var amount = 0;
                $('input[id^=item_quantity_]').each(function(index) {
                    if($(this).data('item-id') == $(itemObj).data('item-id')) {
                        amount += parseInt($(this).val());
                    }
                });

                // Validate item amount
                var require_min = $(itemObj).data('require');
                var title = $(itemObj).parent().parent().parent().find('li:eq(0)').find('a:eq(0)').html().trim();

                if(amount < require_min) {

                    validItemTitle[shopId] = title;
                    validRequireMin[shopId] = require_min;

                    var isSet = false;
                    if(shopValidate.length > 0) {
                        for(var i = 0; i < shopValidate.length; i++) {
                            if(shopValidate[i] == shopId) {
                                isSet = true;
                                break;
                            }
                        }
                    }
                    if(!isSet) { shopValidate.push(shopId); }

                } else {
                    // validate, then remove it from list
                    if(shopValidate.length > 0) {
                        for(var i = 0; i < shopValidate.length; i++) {
                            if(shopValidate[i] == shopId) {
                                shopValidate.splice(i, 1);
                                if(typeof validItemTitle[shopId] != undefined) {
                                    validItemTitle.splice(i, 1);
                                }
                            }
                        }
                    }
                }
            }
        });
    });

    // Validate item amount
    $('.cart-list-content').each(function() {
        var shopId = $(this).data('shop'), amount = 0, item_id = '', require_min = 0, title = '';
        $(this).find('input[id^=item_quantity_]').each(function(index) {
            if(item_id == '') {
                item_id = $(this).data('item-id');
                require_min = $(this).data('require');
                title = $(this).parent().parent().parent().find('li:eq(0)').find('a:eq(0)').html().trim();
            }
            if($(this).data('item-id') == item_id) {
                amount += parseInt($(this).val());
            }
        });
        console.log(amount + ' - ' + require_min);
        if(amount < require_min) {

            validItemTitle[shopId] = title;
            validRequireMin[shopId] = require_min;

            var isSet = false;
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        isSet = true;
                        break;
                    }
                }
            }
            if(!isSet) { shopValidate.push(shopId); }

        } else {
            // validate, then remove it from list
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        shopValidate.splice(i, 1);
                        if(typeof validItemTitle[shopId] != undefined) {
                            validItemTitle.splice(i, 1);
                        }
                    }
                }
            }
        }
    });


});

var xhr = null;
var ChooseService = {
    'updateItem': function(cartId, quantity, service, shopId) {
        if(xhr != null) {
            xhr.abort();
        }
        if(actDel) {
            $('body').append('<div class="modal-backdrop fade in"></div>');
        }
        xhr = $.post(choose_service_url + '/ChangeQuantity', {'cid': cartId, 'sv': service, 'q': quantity}, function (result) {

            if (result.error == 1) {
                return;
            }

            var $itemData = result.data.itemInfo;
            var $orderInfo = result.data.orderInfo;
            var itemTxt = '<p class="red-normal">';
            itemTxt += '<b>' + formatCurrency($itemData.totalItemAmount) + '<sup>đ</sup></b>';
            itemTxt += '</p>';
            itemTxt += '<p class="italic">(' + $itemData.amount + ' x ' + formatCurrency($itemData.price) + '<sup>đ</sup>)</p>';
            $('#_totalItemPrice_' + $itemData.id).html(itemTxt);
            $('#_totalAmount_' + shopId).html(formatCurrency($orderInfo.totalAmount));
            $('#_totalBuyingFee_' + shopId).html(formatCurrency(rounding($orderInfo.totalBuyingFee, null, 500)));
            $('#_totalCheckingFee_' + shopId).html(formatCurrency($orderInfo.totalCheckingFee));
            $('#_totalPackingFee_' + shopId).html(formatCurrency($orderInfo.totalPackingFee));
            $('#_totalFee_' + shopId).html(formatCurrency($orderInfo.totalFee));
            $('#_totalDepositFee_' + shopId).html(formatCurrency($orderInfo.totalDepositFee));
            $('#_cartItemCount_' + shopId).html($orderInfo.totalQuantity);
            $('#_cartWeightCount_' + shopId).html($orderInfo.totalWeight);
            $('#_totalWeight_' + shopId).val($orderInfo.totalWeight);
            $('#_totalQuantity_' + shopId).val($orderInfo.totalQuantity);
            $('#_totalFee_' + shopId).val($orderInfo.totalFee);
            $('#_totalDeposit_' + shopId).val($orderInfo.totalDepositFee);
            $('#_totalAmount_' + shopId).val($orderInfo.totalAmount);

            // Find other patterns of this item
            if($itemData.itemByPriceTable) {
                var itemId = $('#item_quantity_' + cartId).data('item-id');
                $('input[id^=item_quantity_]').each(function(index) {
                    if($(this).data('item-id') == itemId) {
                        var itemTxt = '<p class="red-normal">';
                        itemTxt += '<b>' + formatCurrency(rounding($(this).val() * $itemData.price)) + '<sup>đ</sup></b>';
                        itemTxt += '</p>';
                        itemTxt += '<p class="italic">(' + $(this).val() + ' x ' + formatCurrency($itemData.price) + '<sup>đ</sup>)</p>';
                        $('#_totalItemPrice_' + $(this).prop('id').replace(/item_quantity_/, '')).html(itemTxt);
                    }
                });
            }

            // Update fee
            ChooseService.loadServiceByShop(
                $('#_shopOrder_' + shopId),
                $orderInfo.totalAmount,
                $orderInfo.totalQuantity,
                0,
                $('#provinceReceiveId').val(),
                $($('#_shopOrder_' + shopId)).find('#weight_' + $($('#_shopOrder_' + shopId)).data('shop-id')).val()
            );

            // Delete action
            if(actDel & quantity == 0) {
                $('#_cartItem_' + cartId).remove();
                // Check shop has items
                if($('#_shopOrder_' + shopId).find('li[id^=_cartItem_]').length == 0) {
                    // Call action del shop
                    $('button._removeCartShop').each(function() {
                        if($(this).data('shop-id') == shopId) {
                            $(this).click();
                        }
                    });
                    // Remove shop
                    $('#_shopOrder_' + shopId).remove();
                }
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                $.post(delCartByItem, {'id': cartId}, function (result) { });

                // Set action del = false
                actDel = false;
            }

            calcOrderSum();
        }, 'json');
    },
    'calculatorService': function() {
        // Find all shops
        $('section[id^=_shopOrder_]').each(function() {
            ChooseService.loadServiceByShop($(this),
                parseInt($(this).find('b[id^=_totalFee_]').html().replace(/,|\./g, '')),
                parseInt($(this).find('b[id^=_cartItemCount_]').html().replace(/,|\./g, '')),
                0,
                $('#provinceReceiveId').val(),
                $(this).find('#weight_' + $(this).data('shop-id')).val()
            );
        });
    },
    'loadServiceByShop': function(obj, amount, quantity, accessories_quantity, province, weigth) {
        $.ajax({
            url: calService + '?t=' + Math.random(),
            type: 'POST',
            data: {
                'is_choose_service' : 1,
                'data[accessItemCount]' : accessories_quantity,
                'data[normalItemCount]' : quantity,
                'data[services][2]' : 'CHECKING',
                'data[services][3]' : 'PACKING',
                'data[totalAmount]' : amount+' vnd',
                'data[targetCode]'  : province,
                'data[totalWeight]' : weigth
            }, success: function(d) {
                // Update view data
                $(obj).find('span[id^=_totalBuyingFee_]').html(formatCurrency(rounding(d.data.buyingDiscountFee, null, 500)));
                if(d.data.buyingFee != d.data.buyingDiscountFee){
                    $('._buying_discount_fee').show();
                    var $html = Global.currency_format(d.data.buyingFee) + '<sup>đ</sup>';
                    $('._buying_discount_fee').find('del').html($html);
                }

                if(d.data.checkingFee != d.data.checkingDiscountFee){
                    $('._checking_discount_fee').show();
                    var $html = Global.currency_format(d.data.checkingFee) + '<sup>đ</sup>';
                    $('._checking_discount_fee').find('del').html($html);
                }
                // Check checking type has choose
                $(obj).find('._choose_service').each(function(index) {
                    if($(this).data('type') == 'checking' & $(this).prop('checked')) {
                        $(obj).find('span[id^=_totalCheckingFee_]').html(formatCurrency(rounding(d.data.checkingDiscountFee, null, 500)));
                    }
                    if($(this).data('type') == 'packing' ) {
                        if($(this).prop('checked')) {
                            $(obj).find('span[id^=_totalShippingFee_]').html(
                                formatCurrency(
                                    rounding($(obj).find('span[id^=_totalShippingFee_]').data('fix') + d.data.packingFee, null, 500))
                            );
                            $('.total_packing_money').html(d.data.packingFee);
                        } else {
                            $('.total_packing_money').html('0');
                        }
                    }
                });
            }
        })
    },
    'validateAmount': function(shopId, amount, require, title, validRequire, validTitle) {
        //console.log(amount + ' - ' + shopId + ' - ' + require + ' - ' + title);
        if(amount < require) {

            validTitle[shopId] = title.trim();
            validRequire[shopId] = require;
            console.log(validRequire);

            var isSet = false;
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        isSet = true;
                        break;
                    }
                }
            }
            if(!isSet) { shopValidate.push(shopId); }

        } else {
            // validate, then remove it from list
            if(shopValidate.length > 0) {
                for(var i = 0; i < shopValidate.length; i++) {
                    if(shopValidate[i] == shopId) {
                        delete shopValidate[i];
                        if(typeof validTitle[shopId] != undefined) {
                            delete validTitle[shopId];
                        }
                    }
                }
            }
        }
    },
    submitOrder : function(){
        var submitOrders = $('#submitOrders');
        var orderData = [];
        var addressId = $('#receiveAddressId').val();
        var input_services = $('._selected_services[data-is-submit='+0+']').first();
        if(input_services.length > 0){
            $('._loading_submit').click();
            var shopId = input_services.attr('data-shop-id');
            var serviceChoose = input_services.val().split(',');
            var serviceData = {};
            for(var i = 0; i < serviceChoose.length; i++) {

                serviceData[serviceChoose[i]] =
                    $('span.' + serviceChoose[i] + '_money')[0] ?
                        $('span.' + serviceChoose[i] + '_money').html().replace(/,|\./g, '') : 0;
            }

            var item = [];
            item.push(shopId, JSON.stringify(serviceData), addressId);
            orderData.push(item);
            $.ajax({
                url: choose_service_url + '/SubmitOrder',
                type: 'POST',
                data: { 'data': orderData, 'aid': addressId },
                dataType: 'json',
                success: function(r) {
                    if (r.error == 0) {
                        input_services.attr('data-is-submit',1);
                        var selected_services = $('._selected_services[data-is-submit='+0+']').first();
                        if(selected_services.length > 0){
                            ChooseService.submitOrder();
                        }else{
                            $(this).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                            document.location.href = r.redirectUrl;
                        }
                    } else {
                        $(this).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                        // Notify alert message
                        $('#myModalErr').find('p').html(r.msg);
                        $('button.myModalErr').click();
                    }
                }, error: function(xhr, status, error) {
                    $(submitOrders).removeAttr('disabled').removeClass('disabled').html('KẾT ĐƠN');
                    // Notify alert message
                    $('#myModalErr').find('p').html('Có lỗi khi kết đơn, hãy thử lại!'); // xhr.responseText
                    $('button.myModalErr').click();
                }
            });
        }

    }
};

function calcOrderSum() {
    var totalFee = 0;
    var totalWeight = 0;
    var totalQuantity = 0;
    var totalServiceFee = 0;
    var totalDepositFee = 0;

    $('._goSum').each(function () {
        var $obj = $(this);
        var type = $obj.data('type');
        var val = parseInt($obj.val());
        switch (type) {
            case 'totalWeight':
                totalWeight += val;
                break;
            case 'totalAmount':
                totalServiceFee += val;
                break;
            case 'totalQuantity':
                totalQuantity += val;
                break;
            case 'totalDepositFee':
                totalDepositFee += val;
                break;
            case 'totalFee':
                totalFee += val;
            default :
                break;
        }
    });
    $('#totalFee').html(formatCurrency(totalFee));
    $('#totalDeposit').html(formatCurrency(totalDepositFee));
    $('#totalWeight').html(formatCurrency(totalWeight));
    $('#totalQuantity').html(formatCurrency(totalQuantity));
}
