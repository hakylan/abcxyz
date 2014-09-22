var order_template, item_template , item_row_complaint_seller, item_row_complaint
    , list_transaction_order_template, box_guide, order_total, services_template, render_fee;
var ajax_rq = null;
var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var datetime = year + '-' + (month<10 ? '0' : '') + month + '-' + day + ' ' + currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds();
var number = 0;
var complaint_seller_id = number;
var page_complaint = 1;
var limit_complaint = 2;
var page_complaint_seller = 1;
var limit_complaint_seller = 5;

$(document).ajaxComplete(function(){
//    order_total = Handlebars.compile($("#_order_tatal_detail").html());
//    $("#_order_total").html(order_total(order));

    //autonumeric
    $('#_order-weight-input').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });

    //tooltip
    $("a.user-img-tool[rel='tooltiptop']").tooltip({
        html: true,
        placement: 'top'
    });

    //tooltip
    $("a.user-img-tool[rel='tooltipbottom']").tooltip({
        html: true,
        placement: 'bottom'
    });
});
jQuery(document).ready(function($) {
    item_row_complaint_seller = Handlebars.compile($("#_item-row-complaint-seller").html());
    item_row_complaint = Handlebars.compile($("#_item-row-complaint").html());
    list_transaction_order_template = Handlebars.compile($("#_list-transaction-order-tpl").html());
    render_fee = Handlebars.compile($("#_render_fee").html());

    $('#_complaint-seller-refocus-time').datepicker({ dateFormat: 'dd-mm-yy' });
    $('#_complaint-seller-amount-seller-refund').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });

//    console.log('current_status: ' + order.status);
    order_template = Handlebars.compile($("#_order-detail-page").html());
    item_template = Handlebars.compile($("#_list-item-template").html());

    box_guide = Handlebars.compile($("#_box-guide").html());

    if(order.is_show_guide == 1){
        //show dialog
        $('#page-wrapper').after(box_guide);
        //append dialog
        $('#myModalalert').modal('show');
    }

    $(document).on('click','._chooseServices',function(){
        var services_type = $(this).attr('data-type');
        var order_id = $(this).attr('data-order-id');
        var check_box = $(this);
        $.ajax({
            url : ChooseServicesLink,
            type : "POST",
            data : {
                order_id : order_id,
                services_type : services_type
            },
            success : function(data){
//                console.log($('._money_services[data-type="'+services_type+'"]'));
//                Common.format
                $('._money_services[data-type="'+services_type+'"]').text(data.fee);
                if (services_type == "WOOD_CRATING") {
                    if (data.permission_order_edit_services) {
                        if (check_box.prop('checked')) {
                            $('._span_fee_wood_crating').removeClass('hide');
                        } else {
                            $('._span_fee_wood_crating').addClass('hide');
                        }
                    }
                }
            }
        })
    });

    //chkall
    $(document).on('click', '#chkall', function() {
        if(this.checked) { // check select status
            $('.chk:not(":disabled")').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"
            });
        }else{
            $('.chk:not(":disabled")').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"
            });
        }
    });
    //chk
    $(document).on('click', '.chk', function() {
        if($('.chk:not(":disabled")').length == $('.chk:checked:not(":disabled")').length){
            $('#chkall').prop('checked', true);
        }else{
            $('#chkall').prop('checked', false);
        }
    });

    $(document).on('click', '#_btn_out_of_stock', function() {
        if(!checkOrderChecked()){
            alert('Đơn hàng này đã được kiểm!');
            return false;
        }

        if($('.chk:checked').length == 0){
            alert('Vui lòng chọn một sản phẩm!');
            return false;
        }
        if(!confirm('Bạn có chắc chắn?')){
            return false;
        }

        var arrItemId = new Array();
        $('.chk:checked:not(":disabled")').each(function(i){
            var t = $(this).parents('.body-listorder-item');
            var quantity = 0;
            var item_id = t.data('item-id');
            t.find('._item-change-out-of-stock').click();
        });
    });

    $(document).on('click', '#_btn_receive_enough', function() {
        if(!checkOrderChecked()){
            alert('Đơn hàng này đã được kiểm!');
            return false;
        }

        if($('.chk:checked').length == 0){
            alert('Vui lòng chọn một sản phẩm!');
            return false;
        }

        if(!confirm('Bạn có chắc chắn?')){
            return false;
        }
        $('.chk:checked:not(":disabled")').each(function(i){
            var t = $(this).parents('.body-listorder-item');
            t.find('._item-change-pendding-quantity').click();
        });
    });

    $("#_order-detail-placeholder").html(order_template(order));
//    services_template = Handlebars.compile($("#_services_fee_js").html());
//    var services = $.extend({}, order_services);
//    $("#_services_fee").html(services_template(services));

    get_order_item(order.id);

    //change weight

    $(document).on('keyup', '#_order-weight-input', function(e){
        if(e.keyCode == 13){
            $('._change-weight').click();
        }
    });

    $(document).on('click', 'a._change-weight', function(e) {
        var $this = $(this);
        var weight = $('#_order-weight-input').autoNumeric('get');
        if(order.weight == weight){
            return false;
        }

        if ($this.hasClass('disabled')) {
            return;
        }

        $this.addClass('disabled');

        change_weight(order.id);
    });

    $(document).on('click', '#_order-weight-input', function() {
        $(this).select();
    });

    $(document).on('click', '#_checked', function() {
        var $this = $(this);
        if ($this.hasClass('disabled')) {
            return ;
        }

        $this.addClass('disabled');
        $.post(confirm_checked_url, {'id' : order.id}, function(response) {
            console.log(response);
            if (response.type == 1) {
                $this.find('i')
                    .removeClass('icon-thumbs-up')
                    .addClass('icon-check');
                //success
                insertComment($('#box_external'), 'Đơn hàng đã được kiểm, tìm thấy tổng ' + order.recive_quantity + ' sản phẩm', first_name, current_username, datetime, current_img_path, false, false, true, false)
            } else {
                $this.removeClass('disabled');
                alert(response.message);
            }
        });
    });

    //add fee wood crating
    //autoNumeric
    $('#_money_wood_crating').autoNumeric({ aPad: false, mDec: 9 });

    $(document).on('click', '#add_fee_wood_crating', function () {
        var $this = $(this);
        if ($this.hasClass('disabled')) {
            return;
        }

        $this.addClass('disabled');
        var money = $('#_money_wood_crating').val();

        $.post(add_fee_wood_crating, {'order_id': order.id, 'money': money}, function (response) {
            console.log(response);
            if (response.type == 1) {
                $this.removeClass('disabled');

                //update all fee
                $("#list_fee").html(render_fee(response.data));
                $('#_money_wood_crating').val('');
                alert(response.message);
                $('#checkwood').modal('hide')
            } else {
                $this.removeClass('disabled');
                alert(response.message);
            }
        });
    });

    //click out of stock
    $(document).on('click', 'a._item-change-out-of-stock', function() {
        if(!checkOrderChecked()){
            alert('Đơn hàng này đã được kiểm!');
            return false;
        }

        var $this = $(this),
            order_code = $this.data('order-code'),
            item_id = $this.data('item-id');
//        console.log('item_id: ' + item_id);
        $("#_current-item-receive-quantity-" +item_id).text(0);
        $("#_item-receive-quantity-" +item_id).val(0);

        change_receive_quantity(item_id, order_code);
    });

    $(document).on('click', '._item-change-pendding-quantity', function(e) {
        console.log('vao day');
        if(!checkOrderChecked()){
            alert('Đơn hàng này đã được kiểm!');
            return false;
        }

        var $this = $(this),
            item_id = $this.data('item-id');

        var e = $(e.currentTarget);
        var q = e.parents('.body-listorder-item').find('.pending-quantity').data('pending-quantity');
        if(q % 1 != 0 || e.hasClass('received_enough')) return false;

        e.addClass('received_enough');
        e.parents('.body-listorder-item').find('.quantity').val(q);
        e.html('Đã nhận');
        e.addClass('font-gray');
        console.log('item_id: ' + e.parents('.body-listorder-item').data('item-id'));

        change_receive_quantity(e.parents('.body-listorder-item').data('item-id'), e.parents('.body-listorder-item').data('order-code'));

//        var q = e.parents('.body-listorder-item').find('input[name="quantity"]').val();
//
//        if(q % 1 != 0 || e.hasClass('received_enough')) return false;
//
//        e.addClass('received_enough');
//        e.parents('.body-listorder-item').find('.pending-quantity').html('/ ' + q);
//
//        e.html('Đã nhận');
//        e.addClass('font-gray');
//
//        change_pendding_quantity(item_id);
    });

    $(document).on('blur', 'input[name="quantity"]', function() {
        var $this = $(this);
        if (order.receive_quantity != $this.val()) {
            change_receive_quantity($this.data('item-id'), $this.data('order-code'));
            $("#_current-item-receive-quantity-" +$this.data('item-id')).text($this.val());
        }
    });

    $(document).on('click', 'input[name="quantity"]', function() {
        var $this = $(this);
        $this.select();
    });

    $(document).on('click', 'a._print-barcode', function() {
        var store;
        if (order.destination_warehouse == 'VNHN') {
            store = 'HN'
        } else if (order.destination_warehouse == 'VNSG') {
            store = 'TP.HCM';
        }

        var link = 'http://deliver.alimama.vn/SeudoBarcode?website=S%2F' +
            '&store=' + store +
            '&buyer=' + order.buyer['username'] +
            '&quantity=' + order.recive_quantity +
            '&code=' + order.code +
            '&order_id=' + order.id +
            '&user_code=' +order.buyer['code'] +
            '&weight=' +order.weight / 1000 +
            '&express_delivery=' + order.express_delivery;

        window.open(link, '_blank');
    });

    $(document).on('click', 'a#_change-wait-delivery', function() {
        var e = $(this);
        if (e.hasClass('disbaled')) {
            return;
        }

        e.addClass('disabled');
        change_waiting_delivery(order.id, function(response) {
            if (response.type) {
                $("#_order-status-container").html("Chờ giao");
                e.find('span').html('ĐÃ CHUYỂN');
                //e.fadeOut();
            } else {
                e.removeClass('disabled');
                alert(response.message);
            }
        });
    });

    $(document).on('click', '#_change-request-delivery', function() {
        var e = $(this);
        if (e.hasClass("disabled")) {
            return;
        }

        e.addClass('disabled');
        change_request_delivery(order.id, function(response) {
            if (response.type) {
                $("#_order-status-container").html("Yêu cầu giao");
                e.find('span').html('ĐÃ CHUYỂN');
                //e.fadeOut();
            } else {
                e.removeClass('disabled');
                alert(response.message);
            }
        });
    });

    $(document).on('click', '#_change-delivery', function() {
        var e = $(this);
        if (e.hasClass("disabled")) {
            return;
        }

        e.addClass('disabled');
        change_delivery(order.id, function(response) {
            if (response.type) {
                $("#_order-status-container").html("Đang giao");
                e.find('span').html('ĐÃ CHUYỂN');
                //e.fadeOut();
            } else {
                e.removeClass('disabled');
                alert(response.message);
            }
        });
    });
});

function checkOrderChecked(){
    //CHECKED - trước khi kiểm hàng
    //CONFIRM_DELIVERY - trước khi tất toán
    if(order.status == 'CONFIRM_DELIVERY'){
        return false;
    }else{
        return true;
    }
}

function getOrderItemComments(){
    $.get(linkGetAllOrderItemComments, { order_id: order_id }, function(result){
        if(result.type == 1){
            if(result.comments.length > 0){
                $.each(result.comments, function(idx, item){
                    var order_id = item.order_id;
                    var item_id = item.item_id;
                    var page_next = item.comment.page_next;
                    var pages = item.comment.pages;

                    var doc = $('#show-more-item-comment-' + order_id + '-' + item_id);
                    var root = $('#chat-item-body-' + order_id + '-' + item_id);
                    if (page_next > pages) {
                        doc.hide();
                    } else {
                        doc.show();
                        doc.attr('page', page_next);
                    }
                    root.find('.content-chat-item').append(order_item_comment_template(item.comment));
                });
            }
        }
        //CHAT (PUBLIC AND PRIVATE)
        getListChat();
    });
}

function get_order_item(order_id) {
    $.get(get_items_url, {'order_id' : order_id}, function(response) {
        $("#_list-item-placeholder").html(item_template(response));
        $('img.lazy').lazyload();
        //Danh sách comment trên từng sản phẩm
        getOrderItemComments();
    });
}

function change_receive_quantity(item_id, order_code, callback) {
    var input = $('#_item-receive-quantity-' +item_id),
        order_id = input.data('order-id'),
        item_id = input.data('item-id'),
        order_code = input.data('order-code'),
        new_quantity = input.val();
    var root = $('#seu-chat-tab-' + order_id);

    if(!checkOrderChecked()){
        alert('Đơn hàng này đã được kiểm!');
        return false;
    }

    $.post(change_item_quantity, {'item_id' : item_id, 'quantity' : new_quantity} , function(response) {
        if (callback) {
            callback(response);
            order.recive_quantity = response.order.recive_quantity;
        } else {
            console.log(response);
            if (response.type == 1) {
                insertComment(root.find('#box_internal'), 'Sửa số lượng sản phẩm với mã ' + response.order_item.id + ' từ ' + response.old_recive_quantity + ' thành ' + new_quantity, first_name, current_username, datetime, current_img_path, false, false, true, true);
                order.recive_quantity = response.order.recive_quantity;
            } else {
                alert(response.message);
            }
        }
    });
}

function change_pendding_quantity(item_id, callback) {
    var input = $('#_item-receive-quantity-' +item_id),
        new_quantity = input.val();

    $.post(change_item_pendding_quantity, {'item_id' : item_id, 'quantity' : new_quantity} , function(response) {
        if (callback) {
            callback(response);
            order.pending_quantity = response.order.pending_quantity;
        } else {
            if (response.type == 1) {
                order.pending_quantity = response.order.pending_quantity;
            } else {
                alert(response.message);
            }
        }
    });
}

function change_weight(order_id) {
    var input = $("#_order-weight-input"),
//        weight = input.val();
        weight = input.autoNumeric('get');
    input.addClass('disabled');
    var root = $('#seu-chat-tab-' + order_id);
    var $this = $('a._change-weight');

    $.post(change_weight_url, {'id': order_id, 'weight' : weight}, function(response) {
        input.removeClass('disabled');
        if (response.type == 1) {
            $this.addClass('bg-azure')
                .find('i')
                .removeClass('icon-save')
                .addClass('icon-check');

            setTimeout(function() {
                $this.removeClass('disabled')
                    .removeClass('bg-azure')
                    .find('i')
                    .removeClass('icon-check')
                    .addClass('icon-save');
            }, 1800);

            //1.Chat với khách
            insertComment(root.find('#box_external'), 'Đơn hàng bổ sung ' + ( weight / 1000 ) + ' kg', first_name, current_username, datetime, current_img_path, false, false, true, false);
            //2. Chat nội bộ
            insertComment(root.find('#box_internal'), first_name + ' đã sửa trọng lượng đơn từ ' + ( order.weight / 1000 ) + ' -> ' + ( weight / 1000 ) + ' kg"', first_name, current_username, datetime, current_img_path, false, false, true, true);
            console.log(response.order.weight);
            order.weight = response.order.weight;
        } else {
            $this.removeClass('disabled');
            alert(response.message);
        }

    });
}

$(document).on('keyup','#msg_chat_external',function(e){
    e.preventDefault();
    if(e.keyCode == 13){
        var order_id = $(this).data('order-id');
        var textarea = $('#msg_chat_external');
        var message = textarea.val();
        var type = $(this).data('type');
        addOrderComment(order_id, message, textarea, type);
    }
});

$(document).on('keyup','#msg_chat_internal',function(e){
    if(e.keyCode == 13){
        var order_id = $(this).data('order-id');
        var textarea = $('#msg_chat_internal');
        var message = textarea.val();
        var type = $(this).data('type');
        if(message == '') return;
        addOrderComment(order_id, message, textarea, type);
    }
});

function addOrderComment(order_id, message, textarea, type) {
    OrderComment.addMessge(order_id, message, textarea, type);
}

function change_waiting_delivery(order_id, callback) {
    $.post(change_to_waiting_delivery, {'order_id' : order_id}, function(response) {
        callback(response);
    });
}

function change_request_delivery(order_id, callback) {
    $.post(change_to_request_delivery, {'order_id' : order_id}, function(response) {
        callback(response);
    });
}

function change_delivery(order_id, callback) {
    $.post(change_to_delivery, {'order_id' : order_id}, function(response) {
        callback(response);
    });
}

var OrderComment = {
    addMessge : function(order_id, message, textarea, type){
//        if(ajax_rq != null){
//            ajax_rq.abort();
//        }
        var root = $('#seu-chat-tab-' + order_id);
        if (message.length > 0) {
            textarea.val('');

            if(type == 'EXTERNAL'){
                insertComment(root.find('#box_external'), message, first_name, current_username, datetime, current_img_path, true, false, false, false);
            }
            if(type == 'INTERNAL'){
                insertComment(root.find('#box_internal'), message, first_name, current_username, datetime, current_img_path, true, false, false, true);
            }

            $.ajax({
                url: link_add_order_comment,
                type : "POST",
                data: {order_id: order_id, message: message, type: type},
                success: function (data) {
                    if(data.type == 0){
                        Common.BSAlert(data.message);
                    }
                    //TODO
                }
            })
        }
    }
}

function insertComment(document, message, first_name, username, sub_time, img_path, is_chat, is_log, is_activity, is_internal){
//    console.log('insertComment');
//    console.log(document);
    document.prepend(chat_row_template({ message: message,
        first_name: first_name,
        username: username,
        sub_time: sub_time,
        img_path : img_path,
        is_chat: is_chat,
        is_log: is_log,
        is_activity: is_activity,
        is_internal: is_internal,
        user_id: current_user_id
    }));
}

/** BEGIN COMMENT ORDER ITEM **/
// dau's order_item_comment
$(document).on('keyup','._item_input_chat',function(e){
    if(e.keyCode == 13){
        var order_id = $(this).data('order-id');
        var item_id = $(this).data('item-id');
        addOrderItemComment(order_id, item_id);
    }
});

// dau's button send order_item_comment
$(document).on('click','._btn_send_message',function(e){
    var e = $(e.currentTarget);
    var order_id = e.parent().find('._item_input_chat').data('order-id');
    var item_id = e.parent().find('._item_input_chat').data('item-id');
    addOrderItemComment(order_id, item_id);
});

// dau's load more item comment
$(document).on('click', '._view_item_chat', function(e) {
    var order_id = $(this).data('order-id');
    var item_id = $(this).data('item-id');
    var page = $(this).attr('page');
    var page_size = $(this).data('page-size');

    getOrderItemComment(order_id, item_id, page, page_size);
});

// dau's
function getOrderItemComment(order_id, item_id, page, page_size){
    $.ajax({
        url: linkMoreOrderItemComment,
        type: 'POST',
        data: {
            order_id: order_id, item_id: item_id, page: page, page_size: page_size
        },
        success: function (result) {
            var doc = $('#show-more-item-comment-'+order_id+'-'+item_id);
            var root = $('#chat-item-body-'+order_id+'-'+item_id);
            if (result.page_next > result.pages) {
                // Load end page => hide show more order item comment
                doc.hide();
            } else {
                doc.show();
                doc.attr('page', result.page_next);
            }
            root.find('.content-chat-item').append(order_item_comment_template(result));
        }
    });
}

function getListChat(){
    $.ajax({
        url: linkListOrderComments,
        type: 'POST',
        data: {
            order_id: order_id,
            type: ''
        },
        success: function (response) {
            if(response.type == 1){
                $.each(response.external_comments, function(idx, item){
                    $('#box_external').append(chat_row_template(item));
                });
                $.each(response.internal_comments, function(idx, item){
                    $('#box_internal').append(chat_row_template(item));
                });
            }
            ComplaintSeller.getListComplaintSeller({ order_id: order_id, page: 1, limit: limit_complaint_seller });
        }
    });
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
        var loaded = root.find('._item_input_chat').attr('loaded');
        var doc = root.find('._item_input_chat');
        var message = doc.val();

        if (message.length > 0) {
            doc.val('');
            root.find('.content-chat-item').prepend(item_order_item_comment_template({ message: message, username: current_username }));
            ajax_rq = $.ajax({
                url: linkAddOrderItemComment,
                type : "POST",
                data: {order_id: order_id, item_id: item_id, message: message},
                success: function (data) {
                    //TODO
                }
            })
        }
    }
}

/** END COMMENT ORDER ITEM **/

$(document).on('click', '._btn_receive_enough', function(e) {
    var e = $(e.currentTarget);
    var q = e.parents('.body-listorder-item').find('input[name="quantity"]').val();

    if(q % 1 != 0) return false;
    if(e.hasClass('received_enough')) return;

    e.addClass('received_enough');
    e.parents('.body-listorder-item').find('.pending-quantity').html('/ ' + q);

    e.html('Đã nhận');
    e.addClass('font-gray');
});

$(document).on('click', '._qtyminus', function(e) {
    var $this = $(e.currentTarget).parent();
    var quantity = $this.find('.sl-input').val();
    //Kiểm tra xem có phải là số hay không?
    if(quantity % 1 != 0){
        $this.find('.sl-input').val('0');
        return false;
    }
    //Giảm đi 1 đơn vị
    quantity = parseInt(quantity);
    if(quantity == 0) {
        $this.find('.sl-input').val('0');
        return false;
    }else{
        quantity--;
        $this.find('.sl-input').val(quantity);
    }
    change_receive_quantity($this.find('.quantity').data('item-id'));
    $(e.preventDefault);
});

$(document).on('click', '._qtyplus', function(e) {
    var $this = $(e.currentTarget).parent();
    var quantity = $this.find('.sl-input').val();
    //Kiểm tra xem có phải là số hay không?
    if(quantity % 1 != 0){
        $this.find('.sl-input').val('0');
        return false;
    }
    //Tăng thêm 1 đơn vị
    quantity = parseInt(quantity);
    quantity++;
    $this.find('.sl-input').val(quantity);
    change_receive_quantity($this.find('.quantity').data('item-id'));
    $(e.preventDefault);
});

$(document).on('click', '#_btn-save-complaint-seller', function(e) {
    var reason = $('#_complaint-seller-reason').val();
    var refocus_time = $('#_complaint-seller-refocus-time').val();
    var order_id = $('#_complaint-seller-order_id').val();
    var message = "";
    var e = $(e.currentTarget);
    if(e.hasClass('clicked')){
        return false;
    }

    if(reason == ""){
        message += "<p style='text-align:left; color: red'>Vui lòng chọn lý do đòi tiền</p>";
    }
    if(refocus_time == ""){
        message += "<p style='text-align:left; color: red'>Vui lòng chọn thời hạn xử lý</p>";
    }
    if(message != ""){
        $('#_complaint-seller-message-error').html(message);
        return false;
    }else{
        $('#_complaint-seller-message-error').html("");
    }

    e.addClass('clicked');
    e.find('span').html('Đang xử lý');

//    if(ajax_rq != null){
//        ajax_rq.abort();
//    }
    ajax_rq = $.ajax({
        url: linkAddComplaintSeller,
        type : "POST",
        data: {reason: reason, refocus_time: refocus_time, order_id: order_id},
        success: function (response) {
            complaint_seller_id = response.complaint_seller_id;
            if(response.type == 0){
                alert(response.message);
                return false;
            }

            $('#myModalalert1').modal('hide');
            $('#_btn-show-complaint-seller span').html('Đang khiếu nại người bán');
            $('#_btn-show-complaint-seller').attr('id', '_btn-doing-complaint-seller');
            insertComment($('#box_external'), "Đơn hàng " + order.code + " đang được khiếu nại với người bán", first_name, current_username, datetime, current_img_path, false, false, true, false);
            insertComment($('#box_internal'), "Đơn hàng " + order.code + " đang được khiếu nại với người bán", first_name, current_username, datetime, current_img_path, false, false, true, true);
        }
    });


});

$(document).on('click', '#_btn-show-complaint-seller', function(e) {
    if(complaint_seller_id == 0){
        $('#myModalalert1').modal('show');
    }
});

$(document).on('click', '#_btn-doing-complaint-seller', function(e) {
    var e = $(e.currentTarget);
    $('#myModalalert2').modal('show');
});

$(document).on('mouseover', '#_btn-doing-complaint-seller', function(e) {
    var e = $(e.currentTarget);
    e.find('span').html('Kết thúc khiếu nại');
});

$(document).on('mouseout', '#_btn-doing-complaint-seller', function(e) {
    var e = $(e.currentTarget);
    e.find('span').html('Đang khiếu nại người bán');
});

$(document).on('change', '#_complaint-seller-status', function(e) {
    var e = $(e.currentTarget);
    if(e.val() == 'SUCCESS'){
        $('#_complaint-seller-status-success').show();
    }else{
        $('#_complaint-seller-status-success').hide();
    }
});

$(document).on('click', '#_btn-save-complaint-seller-status', function(e) {
    var e = $(e.currentTarget);
    var status = $('#_complaint-seller-status').val();
    var description = $('#_complaint-seller-description').val();
    var amount_seller_refund = $('#_complaint-seller-amount-seller-refund').autoNumeric('get');
    var order_id = $('#_complaint-seller-order_id').val();

    e.find('span').html('Đang xử lý');
    if(e.hasClass('clicked')){
        return false;
    }
    e.addClass('clicked');

    if(status == 'SUCCESS'){
        $.ajax({
            url: linkUpdateStatusSuccessComplaintSeller,
            type : "POST",
            data: {
                status: status,
                description: description,
                amount_seller_refund: amount_seller_refund,
                order_id: order_id,
                insert_log: 1
            },
            success: function (response) {
                $('#myModalalert2').modal('hide');
                $('#_btn-doing-complaint-seller').find('span').html('Khiếu nại thành công');
                $('#_btn-doing-complaint-seller').removeAttr('id', '')
                insertComment($('#box_internal'),
                    "Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " + amount_seller_refund + " NDT",
                    first_name, current_username, datetime, current_img_path, false, false, true, true);
                insertComment($('#box_external'), "Khiếu nại người bán thành công",
                    first_name, current_username, datetime, current_img_path, false, false, true, false);
            }
        });
    }
    if(status == 'FAILURE'){
        $.ajax({
            url: linkUpdateStatusFailureComplaintSeller,
            type : "POST",
            data: { status: status, order_id: order_id, insert_log: 1 },
            success: function (response) {
                $('#myModalalert2').modal('hide');
                $('#_btn-doing-complaint-seller').find('span').html('Khiếu nại thất bại');
                $('#_btn-doing-complaint-seller').removeAttr('id', '');
                insertComment($('#box_internal'),
                    "Chuyển trạng thái khiếu nại người bán sang thất bại",
                    first_name, current_username, datetime, current_img_path, false, false, true, true);
                insertComment($('#box_external'), "Khiếu nại người bán thất bại",
                    first_name, current_username, datetime, current_img_path, false, false, true, false);
            }
        });
    }
});

function getListFreeOrder(){
    $.ajax({
        url: linkGetListFreeOrder,
        type : "GET",
        data: { order_id: order_id },
        success: function (response) {
            if(response.type == 1){
                $("#list_fee").html(render_fee(response.data));
            }
        },
        error: function() {}
    });
}

function getListTransactionOrder(){
    $.ajax({
        url: linkGetListTransactionOrder,
        type : "GET",
        data: { order_id: order_id },
        success: function (response) {
            if(response.type == 1){
                $('#_show-list-transaction-order').html(list_transaction_order_template(response));
            }
            getListFreeOrder();
        },
        error: function() {}
    });
}

var Complaint = {
    getListComplaint: function(search_data){
        $.ajax({
            url: linkGetListComplaint,
            type : "POST",
            data: search_data,
            success: function (response) {
                if(response.type == 1){
                    $('#_list-complaints').empty();
                    if(response.items.length > 0){
                        $.each(response.items, function(idx, item){
                            $('#_list-complaints').append(item_row_complaint(item));
                        });

                        $('#_show-paging-complaint').html(createPaging(response.total_page, response.current_page, '-complaint'));

                        //paging
                        $('._paging-complaint').click(function(e){
                            console.log('paging');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var page = e.data('page');
                                page_complaint = page;
                                e.addClass('clicked');
                                Complaint.getListComplaint({ order_id: order_id,
                                                                page: page, limit: limit_complaint});
                            }
                        });

                        $('#_page-prev-complaint').click(function(e){
                            console.log('page prev');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                page_complaint--;
                                e.addClass('clicked');
                                Complaint.getListComplaint({ order_id: order_id,
                                                                page: page_complaint, limit: limit_complaint});
                            }
                        });

                        $('#_page-next-complaint').click(function(e){
                            console.log('page next');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                page_complaint++;
                                e.addClass('clicked');
                                Complaint.getListComplaint({ order_id: order_id,
                                    page: page_complaint, limit: limit_complaint});
                            }
                        });
                    }else{
                        //TODO
                    }
                }else{
                    //TODO
                }
                getListTransactionOrder();
            }
        });
    }
};

var ComplaintSeller = {
    getListComplaintSeller: function(search_data){
        $.ajax({
            url: linkGetListComplaintSellers,
            type : "POST",
            data: search_data,
            success: function (response) {
                if(response.type == 1){
                    $('#_list-complaint-sellers').empty();
                    if(response.items.length > 0){
                        $.each(response.items, function(idx, item){
                            $('#_list-complaint-sellers').append(item_row_complaint_seller(item));
                        });

                        $('#_show-paging-complaint-seller').html(createPaging(response.total_page, response.current_page, '-complaint-seller'));

                        //paging
                        $('._paging-complaint-seller').click(function(e){
                            console.log('paging');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var page = e.data('page');
                                page_complaint_seller = page;
                                e.addClass('clicked');
                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
                                                                            page: page, limit:
                                                                            limit_complaint_seller });
                            }
                        });

                        $('#_page-prev-complaint-seller').click(function(e){
                            console.log('page prev');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                page_complaint_seller--;
                                e.addClass('clicked');
                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
                                                                            page: page_complaint_seller,
                                                                            limit: limit_complaint_seller });
                            }
                        });

                        $('#_page-next-complaint-seller').click(function(e){
                            console.log('page next');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                page_complaint_seller++;
                                e.addClass('clicked');
                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
                                                                            page: page_complaint_seller,
                                                                            limit:limit_complaint_seller });
                            }
                        });
                    }
                }else{
    //                  Common.BSAlert(response.message);
    //                  return false;
                }
                Complaint.getListComplaint({ order_id: order_id, page: 1, limit:limit_complaint });
            }
        });
    }
}

function createPaging(total_page, current_page, prefix){
    var html = '';
    if(total_page > 1){
        var j = 2;
        html += '<div class="row link-bottom">';
        html += '<div class="col-lg-12 col-md-12">';
        html += '<ul class="pagination pull-left">';
        if(current_page > 1){
            html += '<li class="pre"><a id="_page-prev' + prefix + '">&lt;</a></li>';
        }

        for(var i = j; i > 0; i--){
            if(current_page - i > 0){
                html += '<li>';
                html += '<a class="_paging' + prefix + '" data-page="' + ( current_page - i ) + '">' + ( current_page - i ) + '</a>';
                html += '</li>';
            }
        }

        html += '<li class="active"><a>' + current_page + '</a></li>';

        for(var i = 1; i <= j; i++){
            if(current_page + i <= total_page){
                html += '<li>';
                html += '<a class="_paging' + prefix + '" data-page="' + ( current_page + i ) + '">' + ( current_page + i ) + '</a>';
                html += '</li>';
            }
        }

        if(current_page < total_page){
            html += '<li class="next"><a id="_page-next' + prefix + '">&gt;</a></li>';
        }
        html += '</ul>';
        html += '</div>';
        html += '</div>';
    }
    return html;
}