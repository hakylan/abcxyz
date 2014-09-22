var ajax_setup = null;
var ajax_rq = null;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var datetime = year + '-' + (month<10 ? '0' : '') + month + '-' + day + ' ' + currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds();

$(document).ajaxComplete(function(){
    //tooltip
    $("a.user-img-tool").tooltip({
        html: true,
        placement: 'top'
    });
});

$(document).ready(function () {

    list_comments_external = Handlebars.compile($("#_order_item_chat_external").html());
    list_comments_internal = Handlebars.compile($("#_order_item_chat_internal").html());

    item_order_item_comment_template = Handlebars.compile($("#_item_order_item_comment").html());

    if( window.console && window.console.firebug ){
        alert("Sorry! This system does not support Firebug.\nClick OK to log out.");
        return;
//        window.location='/login_out';
    }

    $(document).on('click','._address_receive',function(){
        $(this).select();
    });

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

    // dau's order_comment
    $(document).on('keyup','._input_chat',function(e){
        if(e.keyCode == 13){
            var order_id = $(this).data('order-id');
            addOrderComment(order_id);
        }
    });
    // dau's
    $(document).on('click', '#btn_send_message_external', function(e){
        var order_id = $(this).data('order-id');
//        alert(order_id);
        addOrderComment(order_id);
    });
    // dau's
    $(document).on('click', '#btn_send_message_internal', function(e){
        var order_id = $(this).data('order-id');
        addOrderComment(order_id);
    });

    // dau's order_item_comment
    $(document).on('keyup','._item_input_chat',function(e){
        if(e.keyCode == 13){
            var order_id = $(this).data('order-id');
            var item_id = $(this).data('item-id');
            addOrderItemComment(order_id, item_id);
        }
    });

    // dau's load more item comment
    $(document).on('click', '._view_item_chat', function(e) {
       var order_id = $(this).data('order-id');
       var item_id = $(this).data('item-id');
       var page = $(this).attr('page');
       var page_size = $(this).data('page-size');

//       alert('Page: ' + page);
        $.ajax({
            url: linkMoreOrderItemComment,
            type: 'POST',
            data: {
                order_id: order_id, item_id: item_id, page: page, page_size: page_size
            },
            success: function (result) {
                var doc = $('#show-more-item-comment-'+order_id+'-'+item_id);
                var root = $('#chat-item-body-'+order_id+'-'+item_id);
                if (result.page_next >= result.pages) {
                    // Load end page => hide show more order item comment
                    doc.hide();
                } else {
                    doc.attr('page', result.page_next);
                }
                root.find('.content-chat-item').append(order_item_comment_template(result));
            }
        });
    });

    $(document).on('keyup','._quantity_item',function(e){
        if(e.keyCode == 13){
            var item_id = $(this).attr('data-item-id');
            var order_id = $(this).attr("data-order-id");
            var old_quantity = $(this).attr('data-quantity');
            old_quantity = parseInt(old_quantity != '' ? old_quantity : 0);
            var quantity = $(this).val();
            if(!$.isNumeric(quantity)){
                Common.BSAlert("Số lượng phải là kiểu số");
                return;
            }
            quantity = parseInt(quantity);
            if(quantity > old_quantity){
                $('._btn_change_status[data-order-id='+order_id+']').find('span').text("Chờ khách xác nhận");
                $('._btn_change_status[data-order-id='+order_id+']').attr('data-is-confirm',customer_wait);
                $('._btn_change_status[data-order-id='+order_id+']').removeClass("disabled");
//                Common.BSAlert("Bạn sửa số lượng nhiều hơn số lượng khách đã đặt." +
//                    " Hãy chờ khách hàng xác nhận để tiếp tục");
//                $('._order_content[data-order-id='+order_id+']').fadeOut();
//                OrderPaid.moveNumberTab(status,customer_wait);
            }

            OrderPaid.changeQuantity(quantity,item_id);
        }

    });
    OrderPaid.orderFilter();

    OrderPaid.checkActiveButton();

//    $(document).on('click','.wrapper',function(){
//        $('div[class^=purchase-dialog-]').css({'display' : 'none'});
//        $(this).css({'display' : 'none'});
//    });

//    $(document).on('click','button.close',function(){
//        var item_id = $(this).attr("data-order-item-id");
//        $('#edit_price'+item_id).click();
//    });
//
//    $(document).on('click','button._close',function(){
//        var item_id = $(this).attr("data-order-item-id");
//        $('#edit_price'+item_id).click();
//    });
    
    $(document).on('keyup','._comment_input',function(e){
        if (e.keyCode == 13) {
            if ($(this).val().replace(/\s+/, '').length == 0) {
                return;
            }
            $(this).parent().parent().find('._comment_btn').click();
        }
    });

    $(document).on('change','._select_account',function(){
        var order_id = $(this).attr('data-order-id');
        var account = $(this).val();
        $('._select_account').each(function(){
            var my_val = $(this).val();
            if($.isNumeric(my_val)){
                $('._select_account').val(account);
            }
        });
        OrderPaid.SelectAccountPurchase(account,order_id);
    });
    
    $(document).on('keyup','._item_chat_input',function(e){
        if(e.keyCode == 13){
            var item_id = $(this).attr('data-item-id');
            var data_id = $(this).attr('data-id');
            var orderId = $(this).attr('data-order-id');
            var boxChat = $('._item_chat_content[data-id=' + data_id + ']');
            var comment = $(this).val();
            if (comment.replace(/\s+/, '').length == 0) {
                return;
            }
            var orderPaidData = $('div.orderPaid-data').data('config');
            var time = getFormattedDate();

            var html =
                '<li class="float-left">' +
                    '<div class="chat-author">' +
                    '<img width="36" alt="" src="' + orderPaidData.userAvatar + '">' +
                    '</div>' +
                    '<div class="popover left no-shadow">' +
                    '<div class="arrow"></div>' +
                    '<div class="popover-content">' + orderPaidData.fullName + ': ' + comment +
                    '<div class="chat-time">' +
                    '<i class="glyph-icon icon-time">' + time + '</i>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</li>';
            // Check has comment
            if (boxChat.find('li').length == 0) { // Display comment box
                $('._item_box_chat_content[data-id='+data_id+']').fadeIn();
//            boxChat.find('.content-box-wrapper').css({'display': 'block'});
            }
            boxChat.prepend(html);

            $('._item_chat_input[data-id=' + data_id + ']').val("");

        OrderPaid.addCommentItem(orderId,item_id,comment);
        }

    });

    var comment = $('.comment_action');

    $(document).on('click','._comment_btn',function(e){
        e.preventDefault();
        var orderId = $(this).attr('data-order-id');
        var boxChat = $('._chat_box_content[data-order-id=' + orderId + ']');
        var comment_input = $('._comment_input[data-order-id=' + orderId + ']');
        var content = comment_input.val();
        if (content.replace(/\s+/, '').length == 0) {
            return;
        }
        var orderPaidData = $('div.orderPaid-data').data('config');
        var currentPositionOfBoxChat = boxChat.find('li').first().hasClass('float-left') ? 'left' : 'right';
        var time = getFormattedDate();

        var html =
            '<li class="' + (currentPositionOfBoxChat == 'left' ? 'float-left' : '') + '">' +
                '<div class="chat-author">' +
                '<img width="36" alt="" src="' + orderPaidData.userAvatar + '">' +
                '</div>' +
                '<div class="popover ' + (currentPositionOfBoxChat == 'left' ? 'right' : 'left') + ' no-shadow">' +
                '<div class="arrow"></div>' +
                '<div class="popover-content">' + orderPaidData.fullName + ': ' + content +
                '<div class="chat-time">' +
                '<i class="glyph-icon icon-time">' + time + '</i>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</li>';
        // Check has comment
        if (boxChat.find('li').length == 0) { // Display comment box
            $('._item_chat_wrapper[data-order-id='+orderId+']').fadeIn();
//            boxChat.find('.content-box-wrapper').css({'display': 'block'});
        }
        boxChat.prepend(html);

        comment_input.val("");

        $.ajax({
            url: LinkAddComment,
            type: 'POST',
            data: {
                orderId: orderId, content: content
            },
            success: function (result) {

            }
        });
    });
    
    $(document).on('click','._receive-order',function(){
//        $('.wrapper').css('display', 'block');
//        $('.waiting').css({'display' : 'block'});
        var total_order = $('._total_order_deposited').text();
        total_order = parseInt(total_order);
        $.ajax({
            url: $(this).data('url'),
            type: 'GET',
            data: {},
            dataType: 'json',
            success: function (result) {
                if(result.type == 1) {
                    $('._status_search').val("BUYING");

                    OrderPaid.orderFilter();
                    var total_get = parseInt(result.total)
                    var total = total_order - total_get;
                    $('._total_order_deposited').text(total);
                    var number_order = $('._number_order[data-status='+buying+']').text();
                    number_order = parseInt(number_order);
                    $('._number_order[data-status='+buying+']').text(number_order+total);
                    //window.location = window.location.href;
//                    $('div.list-order').prepend(d);
                } else {
                    if(result.type == null) {
                        Common.BSAlert("Không tìm thấy đơn nào.");
                    } else{
                        Common.BSAlert(result.message);
                    }
//                        Common.BSAlert(d.message);
                }
//                $('.waiting').css({'display' : 'none'});
//                $('.wrapper').css('display', 'none');
            }, error: function(d) {
                if(d.responseText.match(/BUYING/)) {
                    $('div.list-order').prepend(d.responseText);
                }
                $('.waiting').css({'display' : 'none'});
                $('.wrapper').css('display', 'none');
            }
        })
    });

    // Set height of box chat
    $('div[class^=item-shop-]').each(function () {
        var scroll_height = $(this).find('div._order_item').height();
        if ($(this).find('.scrollable-large').find('li').length > 0) { // Has comment
            $(this).find('.scrollable-large').height(scroll_height - 30).css({'display': 'block'});
            $(this).find('.content-box-wrapper').css({'display': 'block'});
        }
        // Can change order data
        var hasChange = false;
        for(var o in canChange) {
            if(canChange[o] == $(this).data('status')) {
                hasChange = true;
                break;
            }
        }
        if(!hasChange) {
            $(this).find('._order_item').find('input').prop('disabled', true).addClass('disabled');
            $(this).find('._domestic_transfer_fee').find('input').prop('disabled', true).addClass('disabled');
            //$(this).find('.total-order-money').prop('disabled', true).addClass('disabled');
        }
    });
    
    $(document).on('click','._item_out_of_stock',function(){
        if($(this).hasClass('disabled')) { return; }
        var iid = $(this).data('item-id');
        var order_id = $(this).attr("data-order-id");

        $('._total_item_price_ndt[data-item-id='+iid+']').text(0);
        $('._total_item_price_vnd[data-item-id='+iid+']').text(0);
        $('._quantity_item[data-item-id='+iid+']').val(0);

        $.ajax({
            url: LinkChangeQuantity,
            type: 'POST',
            data: { quantity: 0, iid: iid },
            success: function (result) {
                $('._total_order_price_ndt[data-order-id='+order_id+']').text(result.total_order_price_ndt);
                $('._total_order_price_vnd[data-order-id='+order_id+']').text(result.total_order_price_vnd);
                $('._total_item_quantity[data-order-id='+order_id+']').text(result.total_item_quantity);
            }
        });
    });

    $(document).on('click','._change_pvc',function(){
        if($(this).hasClass('disabled')) { return; }

        $(this).addClass("disabled");

        var order_id = $(this).attr('data-order-id');

        var amount = $('._input_pvc[data-order-id='+order_id+']').val();
        if (!$.isNumeric(amount)) {
            $('._error_pvc[data-order-id='+order_id+']').text("Giá trị tiền không hợp lệ. Nhập lại!");
            $('._error_pvc[data-order-id='+order_id+']').show();
            return;
        }
        OrderPaid.ChangeDomesticFee(order_id,amount);
    });
    
    $(document).on('keyup','._input_pvc',function(e){
        var order_id = $(this).attr('data-order-id');
        var pvc_tq = parseInt($(this).val() == '' ? "0" : $(this).val());
        var check = true;
        if(pvc_tq < 0 || !$.isNumeric(pvc_tq)){
            $('._error_pvc[data-order-id='+order_id+']').show();
            check = false;
        }
        OrderPaid.checkError(order_id);
        if(check){
            if (e.keyCode == 13) {
                $(this).addClass("disabled");
                $('._change_pvc[data-order-id='+order_id+']').click();
            }
        }
    });

    /**
     * thay doi tong gia tri don
     */
    $(document).on('click','._change_total_money',function(){
        if($(this).hasClass('disabled')) { return; }

        $(this).addClass("disabled");

        var order_id = $(this).attr('data-order-id');

        var amount = $('._inout_total_money[data-order-id='+order_id+']').val();

        var total_money_order = parseInt(amount == '' ? "0" : amount);

        var check = true;
        if(total_money_order <= 0 || !$.isNumeric(total_money_order)){
            $('._error_total_money[data-order-id='+order_id+']').text("Tổng giá trị đơn không hợp lệ");
            $('._error_total_money[data-order-id='+order_id+']').show();
            check = false;
        }
        OrderPaid.checkError(order_id);
        if(check){
            OrderPaid.ChangeOrderMoney(order_id,amount);
        }
    });


    $(document).on('keyup','._inout_total_money',function(e){
        var order_id = $(this).attr('data-order-id');
        var total_money_order = parseInt($(this).val() == '' ? "0" : $(this).val());
        var check = true;
        if(total_money_order <= 0 || !$.isNumeric(total_money_order)){
            $('._error_total_money[data-order-id='+order_id+']').text("Tổng giá trị đơn không hợp lệ");
            $('._error_total_money[data-order-id='+order_id+']').show();
            check = false;
        }
        OrderPaid.checkError(order_id);
        if(check){
            if (e.keyCode == 13) {
                $(this).addClass("disabled");
                $('._change_total_money[data-order-id='+order_id+']').click();
            }
        }

    });


    /**
     * thay doi Mã đơn trên site gốc
     */
    $(document).on('click','._change_order_code_origin',function(){
        if($(this).hasClass('disabled')) { return; }

        $(this).addClass("disabled");

        var order_id = $(this).attr('data-order-id');

        var invoice = $('._input_order_code_origin[data-order-id='+order_id+']').val();

        var check = OrderPaid.checkError(order_id);
//        if(check){
        if($.trim(invoice) != ''){
            OrderPaid.ChangeInvoice(order_id,invoice);
        }

//        }

    });

    $(document).on('keyup','._input_order_code_origin',function(e){
        var order_id = $(this).attr('data-order-id');
        var check = OrderPaid.checkError(order_id);
        if($(this).val() != ''){
            if (e.keyCode == 13) {
                $(this).addClass("disabled");
                $('._change_order_code_origin[data-order-id='+order_id+']').click();
            }
        }
    });
    
    $(document).on('click','._btn_change_status',function(e){
        e.preventDefault();

        var btn_change = $(this);

        var status = btn_change.attr('data-status');

        var is_customer_confirm = btn_change.attr("data-is-confirm");

        if(is_customer_confirm == customer_wait){
            status = customer_wait;
        }

        var order_id = btn_change.attr('data-order-id');

        var data_link = btn_change.attr("data-link");

        data_link = data_link + '';

        var pvc_tq = $('._input_pvc[data-order-id='+order_id+']').val();
        var total_money = $('._inout_total_money[data-order-id='+order_id+']').val();
        var order_code_origin = $('._input_order_code_origin[data-order-id='+order_id+']').val();
        var account = $('._select_account[data-order-id='+order_id+']').val();
//        var alipay = $('._input_alipay[data-order-id='+order_id+']').val();

        if(parseInt(account) == 0){
            Common.BSAlert("Bạn phải chọn User mua hàng Site gốc.");
            return;
        }

        if(status == negotiating) {
            $('._order_content[data-order-id='+order_id+']').fadeOut();
        }

        var action_order = $('._action_order[data-order-id='+order_id+']');
        // ------------
        if(btn_change.hasClass('disabled')) { return; }

        btn_change.addClass('disabled');

        if(data_link == '' || data_link == 'undefined'){
            var data = {
                oid : order_id,
                status : status,
                pvc_tq: pvc_tq,
                total_money: total_money,
                order_code_origin: order_code_origin,
                account : account
            };
        }else{
            var payment_link = $('._link_payment_direct[data-order-id='+order_id+']').val();
            var data = {
                oid : order_id,
                status : status,
                pvc_tq: pvc_tq,
                payment_link : payment_link,
                total_money: total_money,
                order_code_origin: order_code_origin,
                account : account
            };
        }


        $.ajax({
            url: ChangeStatusLink,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(d) {
                if(!d.type){

                    if(status == negotiating) {
                        $('._order_content[data-order-id='+order_id+']').fadeIn();
                    }
                    btn_change.removeClass('disabled');
                    Common.BSAlert(d.msg);
                    return;
                }

                if(status == customer_wait){
                    OrderPaid.moveNumberTab(btn_change.attr('data-status'),customer_wait);
                    $('._order_content[data-order-id='+order_id+']').fadeOut();
                }

                if(status == negotiating) {
                    OrderPaid.moveNumberTab(status,negotiated);
                    $('._order_content[data-order-id='+order_id+']').remove();
                }

                if(status == buying) {
                    OrderPaid.moveNumberTab(buying,negotiating);
                    var payment_tt = $('._is_order_payment[data-order-id='+order_id+']');
                    if(payment_tt.length > 0){
                        payment_tt.show();
                    }
                    $('._status_btn[data-order-id='+order_id+']').fadeOut();
                }
                // Get new status
                var status_check = null;
                if(status == negotiated) {
                    OrderPaid.moveNumberTab(status,bought);
                    $('._order_content[data-order-id='+order_id+']').fadeOut();
                    return;
                }
                if(typeof nextStatus != 'undefined') {
                    var nextSta = $.parseJSON(nextStatus);
                    for(var o in nextSta) {
                        if(o == status) {
                            // Change current status of this order
                            $('.current-order-status-' + order_id).html(statusTitle[o]);
                            status_check = nextSta[o];
                            if(status_check == negotiating) {
                                action_order.find('a').prop('title', 'Bạn hãy nhập mã đơn trên Site gốc trước khi xác nhận đàm phán');
                            }
                            break;
                        }
                    }
                }
                // Change text action
                var actionTit = $.parseJSON(actionTitle);

                for(var o in actionTit) {
                    if(o == status_check) {
                        // Set new status for attribute
                        btn_change.attr('data-status', status_check);
                        btn_change.find('span').html(actionTit[o]);
                        action_order.attr('data-status', status_check);
                        if($('#invoice-' + order_id).val() == '')
                            action_order.find('a').addClass('disabled');
                        break;
                    }
                }
            }, error: function(err) {
                btn_change.addClass('disabled');
            }
        })
    });
    
    $(document).on('click','._payment_order',function(){
        var oid = $(this).attr('data-order-id');

        var status = negotiating;
        var payment_link = $('#payment_link-' + oid).val();
        var paid_staff_id = $('#paid_staff_id_' + oid).val();

        if(paid_staff_id == 0) {
            Common.BSAlert("Chưa chọn người thanh toán, chọn lại.");
            return;
        }
        $('.item-shop-' + oid).fadeOut();
        // Success
        $('.wrapper').click();
        var pvc_tq = $('._input_pvc[data-order-id='+oid+']').val();
        var total_money = $('._inout_total_money[data-order-id='+oid+']').val();
        var order_code_origin = $('._input_order_code_origin[data-order-id='+oid+']').val();

        $.ajax({
            url: ChangeStatusLink,
            type: 'POST',
            data: {oid : oid,
                status: status,
                paid_staff_id: paid_staff_id,
                payment_link : payment_link,
                "pvc_tq": pvc_tq,
                "total_money": total_money,
                "order_code_origin": order_code_origin
            },
            dataType: 'json',
            success: function(d) {
                if(!d.status) {
                    $('.item-shop-' + oid).fadeIn();
                    return;
                }
            }, error: function(err) {

            }
        })
    });

    $(document).on('click','._autopai',function(){
        var obj = $(this);
        if($(this).hasClass('disabled')) return;
        $(this).addClass('disabled');

        // Add iframe to auto pai link
        var iid = $(this).attr('data-item-id');
        var outer_id = $(this).attr('data-outer-id');
        var item_site_id = $(this).attr('data-item-site-id');
        var outer_id_type = (item_site_id == outer_id ? 1 : 2);
        var quantity = $('._quantity_item_span[data-item-id='+iid+']').text();
        var site = $(this).attr("data-site");
        var ct = $(this).data('ct');
        if(ct == '' || ct == null){
            Common.BSAlert("Chưa có thông tin Autopai, sử dụng chức năng Bắt đầu Autopaid");
            return  false;
        }

        if(outer_id == '' || outer_id == null){
            Common.BSAlert("Sản phẩm này thiếu thông tin để AutoPai");
            return  false;
        }

        var url = site == "TAOBAO" ? "http://cart.taobao.com" : "http://cart.tmall.com";

        var pai_url = url+'/add_cart_item.htm?item_id=' + item_site_id
            + '&bankfrom=&outer_id=' + outer_id + '&outer_id_type=' + outer_id_type + '&quantity=' + quantity
            + '&ct=' + ct;
        var html = '<iframe style="display:none" id="iframe" class="sessionframe" src="' + pai_url + '"></iframe>';
        $('#iframe_autopaid').html(html);

        // Update checked pai to db
        $.ajax({
            url: AutopaiLink,
            type: 'POST',
            data: {iid : iid},
            dataType: 'json',
            success: function(d) {
                if(d.status){
                    $('._da_paid[data-item-id='+iid+']').show();
                }
                $(obj).removeClass('disabled');
            }, error: function(err) {
                $(obj).removeClass('disabled');
            }
        });
    });
    
    $(document).on('click','._site_filter',function(){
        $('._site_filter').removeClass("active");
        $(this).addClass("active");
        var site = $(this).attr('data-site');
        $('._site_origin_search').val(site);
        OrderPaid.orderFilter();
    });
    
    $(document).on('click','._out_of_stock_order',function(){
        var order_id = $(this).data("order-id");
        var status = $(this).data("status");
        $.ajax({
            url : out_of_stock_url,
            type : "POST",
            data : {
                id : order_id
            },
            success : function(data){
                if (data.type == 1) {
                    OrderPaid.moveNumberTab(status,out_of_stock);
                    $('._order_content[data-order-id='+order_id+']').fadeOut();
                } else {
                    Common.BSAlert(data.message);
                }
            }
        })

    });

    $(document).on('click','._tab',function(){
        var data_status = $(this).attr('data-status');
        if($(this).hasClass("_customer_confirm_tab")){
            $('._customer_confirm_search').val(data_status);
            $('._status_search').val("all");
        }else{
            $('._customer_confirm_search').val("NONE");
            $('._status_search').val(data_status);
        }
        OrderPaid.orderFilter();
    });

    $(document).on('click','._save_price_item',function(){
        var order_item_id = $(this).attr("data-order-item-id");
        var order_id = $(this).attr("data-order-id");
        var price_item = $('._price_edit_item[data-order-item-id='+order_item_id+']').val();
        price_item = price_item.replace(",",".");
        if(!$.isNumeric(price_item)){
            Common.BSAlert("Yêu cầu nhập giá chính xác");
            $('._price_edit_item[data-order-item-id='+order_item_id+']').focus();
            return;
        }
        var old_price = $(this).attr('data-price');
        old_price = old_price.replace(",",".");
        old_price = parseFloat(old_price);
        var is_promotion = 0;
        if($('._is_price_promotion[data-order-item-id='+order_item_id+']').is(':checked') ){
            is_promotion = 1;
        }else{
            is_promotion = 0;
        }
        var reason_edit = $('._reason_edit_price[data-order-item-id='+order_item_id+']').val();

        if(price_item > old_price){
            $('._btn_change_status[data-order-id='+order_id+']').find('span').text("Chờ khách xác nhận");
            $('._btn_change_status[data-order-id='+order_id+']').attr('data-is-confirm',customer_wait);
            $('._btn_change_status[data-order-id='+order_id+']').removeClass("disabled");
//                Common.BSAlert("Bạn sửa số lượng nhiều hơn số lượng khách đã đặt." +
//                    " Hãy chờ khách hàng xác nhận để tiếp tục");
//                $('._order_content[data-order-id='+order_id+']').fadeOut();
//                OrderPaid.moveNumberTab(status,customer_wait);
        }

//        if(price_item > old_price){
//            var status = $('._btn_change_status[data-order-id='+order_id+']').attr("data-status");
//            Common.BSAlert("Bạn vừa sửa số đơn giá sản phẩm lớn hơn đơn giá khách đã đặt." +
//                " Hãy chờ khách hàng xác nhận để tiếp tục");
//            $('._order_content[data-order-id='+order_id+']').fadeOut();
//            OrderPaid.moveNumberTab(status,customer_wait);
//        }

        OrderPaid.editPrice(price_item,old_price,reason_edit,order_item_id,is_promotion,order_id);
    });

    $(document).on('click','._select_services',function(){
        var services_type = $(this).attr('data-type');
        var order_id = $(this).attr('data-order-id');
        var is_check = 0;
        if($(this).prop('checked')){
            is_check = 1;
        }
        OrderPaid.chooseServices(order_id,services_type,is_check);
    });

    $(document).on('click','._edit_quantity',function(){
        var item_id = $(this).data('item-id');
        $(this).hide();
        $('._quantity_item_span[data-item-id='+item_id+']').hide();
        $('._quantity_item[data-item-id='+item_id+']').removeClass('hidden');
        $('._quantity_item[data-item-id='+item_id+']').focus();
    });
});

function getFormattedDate() {
    var date = new Date();
    var month = date.getMonth()+1;
    var second = date.getSeconds() > 10 ? date.getSeconds() : "0"+date.getSeconds();
    var str = date.getHours() + ":" + date.getMinutes() + ":" + second +" "+  date.getDate() + "-" + month+"-"+ date.getFullYear();

    return str;
}

function formatCurrency(n) {
    return n.toFixed(0).replace(/./g, function (c, i, a) {
        return i && c !== "," && !((a.length - i) % 3) ? '.' + c : c;
    });
}

function rounding(money) {
    var round = 1000;
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

// dau's
function addOrderItemComment(order_id, item_id) {
    OrderItemComment.addMessge(order_id, item_id);
}

// dau's
var OrderItemComment = {
    addMessge : function(order_id, item_id, message){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        var root = $('#chat-item-body-' + order_id + '-' + item_id);
        var doc = root.find('._item_input_chat');
        var message = doc.val();
        if (message.length > 0) {
            doc.val('');
            doc.prop('disabled', true);
            // get order_id
            ajax_rq = $.ajax({
                url: linkAddOrderItemComment,
                type : "POST",
                data: {order_id: order_id, item_id: item_id, message: message},
                success: function (data) {
    //                console.log(typeof(data));
                    doc.prop('disabled', false);
                    root.find('.content-chat-item').prepend(item_order_item_comment_template(data.info));
                }
            })
        }
    }
}

// dau's
function addOrderComment(order_id) {
//        doc.removeClass('form-myinput-warning');
//        doc.addClass('form-myinput-warning');
//        doc.attr('placeholder','Vui lòng điền nội dung comment!');
    OrderComment.addMessge(order_id);
}

/* dau's */
var OrderComment = {
    addMessge : function(order_id){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        var root = $('#seu-chat-tab-' + order_id);
        var external = root.find('#chat_external_active').attr('class');
        var type = 'INTERNAL';
        var doc = null;
        // dau's
        // check class active in string external ?
        if (external.indexOf('active') != -1) {
            type = 'EXTERNAL';
            doc = root.find('#msg_chat_external');
        } else {
            doc = root.find('#msg_chat_internal');
        }
        var message = doc.val();
        if (message.length > 0) {
            doc.val('');
//            doc.prop('disabled', true);
            var d = {
                username: first_name,
                message: message,
                time: datetime,
                sub_time: datetime,
                user_id: current_user_id,
                img_path: current_img_path,
                is_chat: true,
                is_activity: false,
                is_log: false
            };
            if (type=='EXTERNAL') {
                root.find('#box_external').prepend(chat_item_template(d));
            } else {
                root.find('#box_internal').prepend(chat_item_template(d));
            }
            // get order_id
            ajax_rq = $.ajax({
                url: linkAddOrderComment,
                type : "POST",
                data: {order_id: order_id, message: message, type: type},
                success: function (data) {
//                    doc.prop('disabled', false);
                    //TODO
                }
            })
        }
    }
}

var OrderPaid = {
    orderFilter : function(){
        var data_value = $('._status_search').val();

        if($.trim($('._customer_confirm_search').val()) != 'NONE'){
            data_value = $('._customer_confirm_search').val();
        }

        //$('._tab[tabindex="0"]').attr('data-id');

        var data_id = $('._tab[data-status='+data_value+']').attr('data-id');
        var search_data = $('#_frm_search_purchase').serialize();

        var pageUrl = LinkOrderPaid+'?'+search_data;
        OrderPaid.push_state(pageUrl);

        OrderPaid.orderSearch(data_id,search_data);

    },

    orderSearch : function(data_id,search_data){
        $('._div_content_order').hide();
        $('#'+data_id).show();
        console.log('data_id: ' + data_id);
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        $('._loading').show();
        ajax_rq = $.ajax({
            url: LinkLoadOrder,
            type : "GET",
            data: search_data,
            success: function (result) {
                console.log(result.orders_list);
                if($.type(result.orders_list) === "object"){
                    $('#'+data_id).fadeOut().html(order_template(result)).fadeIn();

                    //load comment by order
                    $('.item-order-purchase').each(function(){
                        var order_id = $(this).data('order-id');
                        $.ajax({
                            url : LoadOrderCommentUrl,
                            type : "POST",
                            data : { order_id : order_id },
                            success : function(data){
                                $('#seu-chat-tab-' + order_id).find('#box_external').html(list_comments_external(data));
                                $('#seu-chat-tab-' + order_id).find('#box_internal').html(list_comments_internal(data));
                            }
                        });
                    });

                    $("* [rel='tooltipbottom']").tooltip({
                        html: true,
                        placement: 'top'
                    });
                    OrderPaid.scrollHeaderAndComment();
                    $(".position-7").each(function(){
                        var heightchat = $(this).innerHeight() - 95;
                        $(this).find('.content-box-wrapper').css('height', heightchat + 'px');
                    });
                }else{
                    var html = '<div class="text-center" style="width: 100%">' +
                        '<h1>Không tồn tại đơn hàng nào</h1>' +
                        '</div>';
                    $('#'+data_id).fadeOut().html(html).fadeIn();
                }
            }
        })
    },

    loadOrderPaid : function(status){
        $.ajax({
            url : LinkLoadOrder,
            type : "GET",
            data : {
                status:status
            },
            success : function(data){
                $('._list_order[data-status='+status+']').fadeOut().html(data).fadeIn();
            }
        })
    },
    ChangeDomesticFee : function(order_id,amount){
        $.ajax({
            url: LinkChangeDomesticFee,
            type: 'POST',
            data: {amount: amount, oid: order_id},
            success: function (d) {
                $('._change_pvc[data-order-id='+order_id+']').removeClass("disabled");
                $('._input_pvc[data-order-id='+order_id+']').removeClass("disabled");
                if (!d.type) {
                    Common.BSAlert(d.message);
                }else{
                    $('._change_pvc[data-order-id='+order_id+']').find("i").removeClass("icon-save");
                    $('._change_pvc[data-order-id='+order_id+']').find("i").addClass("icon-check");
                    setTimeout(function(){
                        $('._change_pvc[data-order-id='+order_id+']').find("i").removeClass("icon-check");
                        $('._change_pvc[data-order-id='+order_id+']').find("i").addClass("icon-save");
                    },1000);
                }
            }, error: function (err) {
                $('._change_pvc[data-order-id='+order_id+']').removeClass("disabled");
                $('._input_pvc[data-order-id='+order_id+']').removeClass("disabled");
            }
        });
    },
    ChangeOrderMoney : function(order_id,money){
        $.ajax({
            url: UpdateOrderMoney,
            type: 'POST',
            data: {amount: money, oid: order_id},
            success: function (d) {
                $('._change_total_money[data-order-id='+order_id+']').removeClass("disabled");
                $('._inout_total_money[data-order-id='+order_id+']').removeClass("disabled");
                if (!d.type) {
                    Common.BSAlert(d.message);
                }else{
                    $('._change_total_money[data-order-id='+order_id+']').find("i").removeClass("icon-save");
                    $('._change_total_money[data-order-id='+order_id+']').find("i").addClass("icon-check");
                    setTimeout(function(){
                        $('._change_total_money[data-order-id='+order_id+']').find("i").removeClass("icon-check");
                        $('._change_total_money[data-order-id='+order_id+']').find("i").addClass("icon-save");
                    },1000);
                }
            }, error: function (err) {
                $('._change_total_money[data-order-id='+oid+']').removeClass("disabled");
                $('._inout_total_money[data-order-id='+oid+']').removeClass("disabled");
                // Process error here
            }
        });
    },
    ChangeInvoice : function(oid,data){
        $.ajax({
            url: ChangeInvoice,
            type: 'POST',
            data: { data: data, oid: oid },
            success: function (d) {
                $('._change_order_code_origin[data-order-id='+oid+']').removeClass("disabled");
                $('._input_order_code_origin[data-order-id='+oid+']').removeClass("disabled");
                if (!d.type) {
                    Common.BSAlert(d.message);
                }else{
                    $('._change_order_code_origin[data-order-id='+oid+']').find("i").removeClass("icon-save");
                    $('._change_order_code_origin[data-order-id='+oid+']').find("i").addClass("icon-check");
                    setTimeout(function(){
                        $('._change_order_code_origin[data-order-id='+oid+']').find("i").removeClass("icon-check");
                        $('._change_order_code_origin[data-order-id='+oid+']').find("i").addClass("icon-save");
                    },1000);
                }
            }, error: function (err) {
                $('._change_order_code_origin[data-order-id='+oid+']').removeClass("disabled");
                $('._input_order_code_origin[data-order-id='+oid+']').removeClass("disabled");
            }
        });
    },
    ChangeAlipay : function(oid,alipay){
        $.ajax({
            url: ChangeAlipay,
            type: 'POST',
            data: { data: alipay, oid: oid },
            success: function (d) {
                if (!d.status) {
                    // Notify error here
                }
            }, error: function (err) {

            }
        });
    },
    SelectAccountPurchase : function(account,order_id){
        $.ajax({
            url : SelectAccountPurchase,
            type : "POST",
            data : {
                username:account,order_id : order_id
            },
            success : function(data){
                
            }
        })
    },
    checkActiveButton : function(order_id){
        $( document ).ajaxComplete(function() {
            $('._btn_change_status').each(function(){

                var order_id = $(this).attr('data-order-id');
                OrderPaid.checkError(order_id);
            });
        })
    },
    fadeOutError : function(order_id){
        $('._error_total_money[data-order-id='+order_id+']').hide();
        $('._error_order_code_origin[data-order-id='+order_id+']').hide();
        $('._error_pvc[data-order-id='+order_id+']').hide();
    },
    checkError : function(order_id){
        var btn_change = $('._btn_change_status[data-order-id='+order_id+']');
        var status = btn_change.attr('data-status');
        var invoice = $('._input_order_code_origin[data-order-id='+order_id+']').val();
        var check = true;
        if($.trim(invoice) == ''){
            $('._error_order_code_origin[data-order-id='+order_id+']').show();
            check = false;
        }else{
            $('._error_order_code_origin[data-order-id='+order_id+']').hide();
            check = true;
        }

        var VcTq = $('._input_pvc[data-order-id='+order_id+']').val();
        if(VcTq <0 || !$.isNumeric(VcTq)){
            $('._error_pvc[data-order-id='+order_id+']').show();
            check = false;
        }else{
            $('._error_pvc[data-order-id='+order_id+']').hide();
        }

        if(status == negotiating || status == negotiated){

            var total_money_order = $('._inout_total_money[data-order-id='+order_id+']').val();

//                    VcTq = parseInt(VcTq == '' ? "0" : VcTq);
            total_money_order = parseFloat(total_money_order == '' ? "0" : total_money_order);
            var error_money = $('._error_total_money[data-order-id='+order_id+']');
            if(total_money_order <= 0 || !$.isNumeric(total_money_order)){
                error_money.text("Tổng giá trị đơn không hợp lệ");
                error_money.show();
                check = false;
            }else{
                error_money.hide();
            }
        }



        if(check == false){
            btn_change.addClass('disabled');
            var is_confirm = btn_change.attr('data-is-confirm');
            if(is_confirm == customer_wait){
                btn_change.removeClass('disabled');
            }
        }else{
            btn_change.removeClass('disabled');
            OrderPaid.fadeOutError(order_id);
        }
    },
    changeQuantity : function(quantity,item_id){
        $('._edit_quantity[data-item-id='+item_id+']').show();
        $('._quantity_item_span[data-item-id='+item_id+']').text(quantity);
        $('._quantity_item_span[data-item-id='+item_id+']').show();
        $('._quantity_item[data-item-id='+item_id+']').addClass('hidden');
        var order_id = $('._total_item_price_ndt[data-item-id='+item_id+']').attr("data-order-id");
        var quantity_old = $('._quantity_item[data-item-id='+item_id+']').attr("data-quantity");
        if(ajax_setup != null){
            ajax_setup.abort();
        }
        ajax_setup = $.ajax({
            url: LinkChangeQuantity,
            type: 'POST',
            data: {
                quantity: quantity,
                iid: item_id,
                quantity_old : quantity_old
            },
            success: function (data) {
                if(data.type == 1){

                    $('._total_item_price_ndt[data-item-id='+item_id+']').text(data.total_item_price_ndt);
                    $('._total_item_price_vnd[data-item-id='+item_id+']').text(data.total_item_price_vnd);
                    $('._total_order_price_ndt[data-order-id='+order_id+']').text(data.total_order_price_ndt);
                    $('._total_order_price_vnd[data-order-id='+order_id+']').text(data.total_order_price_vnd);
                    $('._total_item_quantity[data-order-id='+order_id+']').text(data.total_item_quantity);
                }
            }
        });
    },
    addCommentItem : function(order_id,order_item_id,comment){
        $.ajax({
            url: LinkAddCommentItem,
            type: 'POST',
            data: { order_id: order_id, order_item_id: order_item_id,comment:comment },
            success: function (d) {

            }
        });
    },
    chooseServices : function(order_id,services_type,is_check){
        $.ajax({
            url : ChooseServicesLink,
            type : "POST",
            data : {
                order_id : order_id,
                services_type : services_type,
                is_check : is_check
            },
            success : function(data){
                if(data.type == 1){
                    var text_show = data.address_receive+" " + data.name_origin ;
                    $('._address_receive[data-order-id='+order_id+']').val(text_show);
                    $('._name_recipient[data-order-id='+order_id+']').text(data.name_origin);
                }else{
//                    Common.BSAlert(data.message);
                }
            }
        })
    },
    push_state:function(pageurl){
        if(pageurl!=window.location){
            console.log(pageurl);
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },
    editPrice : function(price_item,old_price,reason_edit,order_item_id,is_price_promotion,order_id){
        $.ajax({
            url : LinkEditPrice,
            type : "POST",
            data : {
                price_item: price_item,
                price_old: old_price,
                reason_edit: reason_edit,
                order_item_id: order_item_id,
                is_price_promotion: is_price_promotion
            },
            success : function(result){
                if(result.type == 0){
                    Common.BSAlert(result.message);
                }else{
                    $('._unit_item_price_ndt[data-item-id='+order_item_id+']').text(price_item);
                    $('._unit_item_price_vnd[data-item-id='+order_item_id+']').text(result.price_vnd_format);
                    $('._total_item_price_ndt[data-item-id='+order_item_id+']').text(result.total_item_price_ndt);
                    $('._total_item_price_vnd[data-item-id='+order_item_id+']').text(result.total_item_price_vnd);
                    $('._total_order_price_ndt[data-order-id='+order_id+']').text(result.total_order_price_ndt);
                    $('._total_order_price_vnd[data-order-id='+order_id+']').text(result.total_order_price_vnd);
                    $('._total_item_quantity[data-order-id='+order_id+']').text(result.total_item_quantity);
                }
            },error :function(){
                Common.BSAlert("Đã xảy ra lỗi, xin thử lại");
            }
        })
    },
    moveNumberTab: function(status_start,status_end){
        var number_start = $('._number_order[data-status='+status_start+']');
        var number_order_st = number_start.text();
        number_order_st = parseInt(number_order_st);
        number_start.text(number_order_st-1);
        var number_end = $('._number_order[data-status='+status_end+']');
        var number_order_end = number_end.text();
        number_order_end = parseInt(number_order_end);
        number_end.text(number_order_end+1);
    },
    scrollHeaderAndComment : function(){

        var customtop = $('.custom-top');
        if(customtop.length > 0){
            customtop.each(function(){
                var order_id = $(this).attr("data-order-id");
                $(this).scrollToFixed({ marginTop: 0, limit: $($('.border-list-item[data-order-id='+order_id+']')).offset().top - 60 });
            });
        }

//        var windowHeight = window.innerHeight - 40;
//        var purchaseSummary = $('.position-7');
//        if(purchaseSummary.length > 0 && purchaseSummary != null){
//            purchaseSummary.each(function(){
//                if($(this).height() > (window.innerHeight-158)){
//                    var summary = $(this).find('.summary');
//                    var order_id = $(this).attr("data-order-id");
//                    summary.scrollToFixed({ marginTop: 65, limit: $($('.border-list-item[data-order-id='+order_id+']')).offset().top - windowHeight });
//                }
//            });
//        }
//        if(customtop.length > 0 && customtop != null){
//            $('#custom-top').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[0]).offset().top - 60 });
//            $('#custom-top1').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[1]).offset().top - 60 });
//            $('#custom-top2').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[2]).offset().top - 60  });
//            $('#custom-top3').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[3]).offset().top - 60  });
//        }
//        var summaries1 = $('.custom-top');
//        summaries1.each(function(i) {
//            var summary1 = $(summaries1[i]);
//            var next = summaries1[i + 1];
//            summary1.scrollToFixed({
//                marginTop: 0,
//                limit: function() {
//                    var limit = 0;
//                    if (next) {
//                        limit = $(next).offset().top - $(this).outerHeight(true) + 150;
//                    } else {
//                        //limit = $('.item-order-purchase').offset().top - $(this).outerHeight(true) - 10;
//                    }
//                    return limit;
//                },
//                zIndex: 9999
//            });
//        });
//
//        var summaries = $('.summary');
//        summaries.each(function(i) {
//            var summary = $(summaries[i]);
//            var next = summaries[i + 1];
//            summary.scrollToFixed({
//                marginTop: $('.custom-top').outerHeight(true) - 0,
//                limit: function() {
//                    var limit = 0;
//                    if (next) {
//                        limit = $(next).offset().top - $(this).outerHeight(true) - 68;
//                    } else {
//                        //limit = $('.border-list-item-footer').offset().top - $(this).outerHeight(true) - 10;
//                    }
//                    return limit;
//                },
//                zIndex: 999
//            });
//        });
    }
}
