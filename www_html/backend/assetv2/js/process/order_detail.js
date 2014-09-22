var order_total;
var ajax_rq = null;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var datetime = year + '-' + (month<10 ? '0' : '') + month + '-' + day + ' ' + currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds();

var complaint_seller_id = 0;
var page_complaint = 1;
var limit_complaint = 5;
var page_complaint_seller = 1;
var limit_complaint_seller = 5;

var page_size_product_comment = 3;

$(document).ajaxComplete(function(){
    //TODO
});

jQuery(document).ready(function($) {



    console.log(order);
//    console.log('--------------------');
//    console.log(order.recive_quantity);
    $('#main-body').removeClass('container');
    $('#_content').html(order_tpl(order));



    //checked checkbox by URL
    var params = Order.getURLParameters( window.location.href );

    var c = false;
    for (var paramName in params){
        switch ( paramName ) {
            case 'CHAT':
//                console.log('CHAT');
                c = true;
                $('._filter-comment[name="CHAT"]').prop('checked', true);
                break;
            case 'ACTIVITY':
//                console.log('ACTIVITY');
                c = true;
                $('._filter-comment[name="ACTIVITY"]').prop('checked', true);
                break;
            case 'LOG':
//                console.log('LOG');
                c = true;
                $('._filter-comment[name="LOG"]').prop('checked', true);
                break;
            default:
//                console.log('DEFAULT');
                c = true;
                $('._filter-comment[name="CHAT"]').prop('checked', true);
                break;
        }
    }

    if( !c ) {
        $('._filter-comment[name="CHAT"]').prop('checked', true);
    }

    $("#_select").hover(function(){
        $(this).find('#_module-dropdow').slideToggle();
    });

    //autonumeric
    $('#_order-weight-input').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });

    getOrderItem(order.id);
    $('img.lazy').lazyload();

//    $('._input-chat').textareaAutogrow();
//    $('._input-chat').shiftenter();

    //js tab
    var $currentTab = $('ul#_tab li');
    $currentTab.click(function(){
        var $this = $(this);
        var $index = $this.index();
        $currentTab.removeClass('active');
        $this.addClass('active');
        $('._detail-tab-ct').hide();
        $('._detail-tab-ct:eq(' + $index + ')').show();
    });

    $('#_complaint-seller-refocus-time').datepicker({ dateFormat: 'dd-mm-yy' });
    $('#_complaint-seller-amount-seller-refund').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });

    if(order.is_show_guide == 1){
        $('#_content').after(box_guide);
        //append dialog
        $('#myModalalert').modal('show');
    }

    //Hành động click hết hàng
    $('#_show-popup-refund-for-customer').click(function(){
        if($(this).hasClass('disabled')){
            return false;
        }

        $.ajax({
            url : linkGetRealPaymentAmount,
            type : "GET",
            data : { order_id : order.id },
            success : function(response){
                $('#_popup-refund-for-customer').modal('show');
                $('#_refund-amount-for-customer').html(response.amount_format);
            }
        });
    });

    $('#_btn-click-customer-confirm').click(function() {
        if( $(this).hasClass('disabled') ) {
            return false;
        }

        var message = '';
        if( order.note_customer_confirm != '' ) {
            message = 'Bạn có muốn xác nhận đơn hàng này khi bị ' + order.note_customer_confirm + ' không?';
        } else {
            message = 'Bạn có chắc chắn muốn thực hiện xác nhận đơn hàng nay?';
        }
        message = 'Bạn có chắc chắn xác nhận mua đơn hàng này thay khách hàng?';

        Common.BSConfirm( message );
    });

    $('#_btn-agree-confirm').click(function(e){
        var e = $(e.currentTarget);
        var $this = $('#_btn-approval');
        if(e.hasClass('clicked')){
            return false;
        }

        var message_public = ' xác nhận mua đơn hàng thay khách hàng.';
        var message_private = ' xác nhận mua đơn hàng thay khách hàng.';

        $.ajax({
            url : LinkManageConfirm,
            type : "POST",
            data : {
                id : order.id,
                status : CUSTOMER_CONFIRM_WAIT,
                message_public: message_public,
                message_private: message_private
            },
            success : function(response){
                if( response.type == 1 ) {
                    //private
                    insertComment($('#_box-internal'),
                        message_private,
                        first_name, current_username, datetime, current_img_path, false, false, true, true);
                    //public
                    insertComment($('#_box-external'), message_public,
                        first_name, current_username, datetime, current_img_path, false, false, true, false);

                    $('#_show-btn-click-customer-confirm').html('· <span class="font-gray">Đã xác nhận</span>');
                } else {
                    Common.BSAlert( 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu' );
                }
            }
        });

    });

    $('#_click-out-of-stock').click(function(e){
        var $t = $('#_show-popup-refund-for-customer');
        var $this = $(this);

        if($this.hasClass('disabled')){
            return false;
        }

        $this.addClass('disabled');
        $t.addClass('disabled');
        $.ajax({
            url : linkOutOfStock,
            type : "POST",
            data : { id : order.id },
            success : function(response){
                $('#_message-error-refund-for-customer').show().html(response.message);
                if(response.type == 1){
                    $('#_popup-refund-for-customer').modal('hide');
                    $("#_order-status-current").html("Hết hàng");
                    if(response.flag == 0){
                        //private
                        insertComment($('#_box-internal'),
                            " Chuyển trạng thái đơn hàng sang hết hàng.",
                            first_name, current_username, datetime, current_img_path, false, false, true, true);
                        //public
                        insertComment($('#_box-external'), "Đơn hàng chuyển sang trạng thái hết hàng.",
                            first_name, current_username, datetime, current_img_path, false, true, false, false);
                    }else{
                        //private
                        insertComment($('#_box-internal'),
                            " Chuyển trạng thái đơn hàng sang hết hàng. Hoàn lại cho khách số tiền " + response.amount_format + " với mã giao dịch là " + response.transacion,
                            first_name, current_username, datetime, current_img_path, false, false, true, true);
                        //public
                        insertComment($('#_box-external'), "Đơn hàng chuyển sang trạng thái hết hàng. Đơn hàng được trả lại số tiền " + response.amount_format,
                            first_name, current_username, datetime, current_img_path, false, true, false, false);
                    }
                }else{
                    $t.removeClass('disabled');
                    $this.removeClass('disabled');
                }
            }
        });
    });

    $(document).on('click','._choose-service',function(){
        var $this = $(this);
        var services_type = $this.data('type');
        var check = $this.is(':checked');
        $.ajax({
            url : ChooseServicesLink,
            type : "POST",
            data : {
                order_id : order.id,
                services_type : services_type
            },
            success : function(response){
                if( response.type == 1 ) {
//                    console.log(response);
                    switch ( services_type ) {
                        case 'CHECKING':

                            if( check ) {
                                insertComment($('#_box-internal'), 'Chọn dịch vụ Kiểm hàng'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Chọn dịch vụ Kiểm hàng'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            } else {
                                insertComment($('#_box-internal'), 'Bỏ dịch vụ Kiểm hàng'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Bỏ dịch vụ Kiểm hàng'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            }

                            break;
                        case 'WOOD_CRATING':

                            if( check ) {
                                insertComment($('#_box-internal'), 'Chọn dịch vụ Đóng gỗ'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Chọn dịch vụ Đóng gỗ'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            } else {
                                insertComment($('#_box-internal'), 'Bỏ dịch vụ Đóng gỗ'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Bỏ dịch vụ Đóng gỗ'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            }

                            break;
                        case 'EXPRESS_CHINA_VIETNAM':

                            if( check ) {
                                insertComment($('#_box-internal'), 'Chọn dịch vụ CPN'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Chọn dịch vụ CPN'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            } else {
                                insertComment($('#_box-internal'), 'Bỏ chọn dịch vụ CPN'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Bỏ chọn dịch vụ CPN'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            }

                            break;
                        case 'FRAGILE':

                            if( check ) {
                                insertComment($('#_box-internal'), 'Chọn dịch vụ Dễ vỡ'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Chọn dịch vụ Dễ vỡ'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            } else {
                                insertComment($('#_box-internal'), 'Bỏ dịch vụ Dễ vỡ'
                                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                                insertComment($('#_box-external'), 'Bỏ dịch vụ Dễ vỡ'
                                    , first_name, current_username, datetime, current_img_path, false, true, false, false);
                            }

                            break;
                        default:
                            //TODO
                            break;
                    }
                } else {
                    $this.prop('checked', false);
                    Common.BSAlert(response.message);
                }
            }
        });
    });

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
        if($('.chk:checked').length == 0){
            Common.BSAlert('Vui lòng chọn một sản phẩm!');
            return false;
        }

        var arrItemId = new Array();
        $('.chk:checked:not(":disabled")').each(function(i){
            var t = $(this).parents('._item-product-view');
            t.find('._item-change-out-of-stock').click();
        });
    });

    $(document).on('click', '#_btn_receive_enough', function() {
        if($('.chk:checked').length == 0){
            Common.BSAlert('Vui lòng chọn một sản phẩm!');
            return false;
        }

        $('.chk:checked:not(":disabled")').each(function(i){
            var t = $(this).parents('._item-product-view');
            t.find('._item-change-pendding-quantity').click();
        });
    });

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
//        $(this).select();
    });

    $(document).on('click', '#_checked', function() {
        var $this = $(this);
        if ($this.hasClass('disabled')) {
            return ;
        }

        $this.addClass('disabled');
        $.post(confirm_checked_url, {'id' : order.id}, function(response) {
            if (response.type == 1) {
                //success
                $this.html('ĐÃ KIỂM');
                insertComment($('#_box-external'), 'Đơn hàng đã được kiểm, tìm thấy tổng ' + order.recive_quantity + ' sản phẩm', first_name, current_username, datetime, current_img_path, false, false, true, false)
            } else {
                $this.removeClass('disabled');
                Common.BSAlert(response.message);
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
//            console.log(response);
            if (response.type == 1) {
                $this.removeClass('disabled');

                //update all fee
                $("#_list-fee").html(render_fee(response.data));
                $('#_money_wood_crating').val('');
//                alert(response.message);
//                Common.BSAlert(response.message);
                $('#checkwood').modal('hide');

                insertComment($('#_box-internal'), 'Thêm phí đóng gỗ ' + money + ' VNĐ'
                    , first_name, current_username, datetime, current_img_path, false, false, true, true);
                insertComment($('#_box-external'), 'Đã thêm ' + money + ' VNĐ phí đóng gỗ'
                    , first_name, current_username, datetime, current_img_path, false, true, false, false);

                moneyFormat();
            } else {
                $this.removeClass('disabled');
                Common.BSAlert(response.message);
            }
        });
    });

    //click out of stock
    $(document).on('click', 'a._item-change-out-of-stock', function() {
        var $this = $(this),
            order_code = $this.data('order-code'),
            item_id = $this.data('item-id');
        var current = $("#_item-receive-quantity-" +item_id);
        current.val('0');
        change_receive_quantity(item_id, order_code);
    });

    $(document).on('click', '._item-change-pendding-quantity', function(e) {
        var $this = $(this),
            item_id = $this.data('item-id');

        var e = $(e.currentTarget);
        var target = e.parents('._item-product-view');
        var q = target.find('.pending-quantity').data('pending-quantity');

        if(q % 1 != 0 || e.hasClass('received_enough')) return false;

        e.addClass('received_enough font-gray');
        target.find('.quantity').val(q);
        e.html('Đã nhận');

        change_receive_quantity(target.data('item-id'), target.data('order-code'));
    });

    $(document).on('blur', 'input[name="quantity"]', function() {
        var $this = $(this);
        if($this.val() % 1 != 0){
            Common.BSAlert('Định dạng không hợp lệ!');
            $this.focus();
            return false;
        }
        change_receive_quantity($this.data('item-id'), $this.data('order-code'));
    });

    $(document).on('click', 'input[name="quantity"]', function() {
        var $this = $(this);
        $this.select();
    });

    $(document).on('click', '._print-barcode', function() {
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

    $(document).on('click', 'a#_change-transporting', function() {
        var e = $(this);
        if (e.hasClass('disbaled')) {
            return;
        }

        e.addClass('disabled');
        change_transporting(order.id, function(response) {
            if (response.type) {
                $("#_order-status-current").html("Vận Chuyển");
                e.find('span').html('ĐÃ CHUYỂN');

                //next button status
//                if( permission_change_to_waiting_for_delivery ){
//                    e.removeClass('disabled');
//                    e.attr('id', '_change-wait-delivery');
//                    e.find('span').html('Chờ giao');
//                }

                insertComment($('#_box-internal'), ' Chuyển trạng thái đơn hàng sang vận chuyển', first_name, current_username, datetime, current_img_path, false, false, true, true);
            } else {
                e.removeClass('disabled');
                Common.BSAlert(response.message);
            }
        });
    });

    $(document).on('click', 'a#_change-wait-delivery', function() {
        var e = $(this);
        if (e.hasClass('disbaled')) {
            return;
        }

        e.addClass('disabled');
        change_waiting_delivery(order.id, function(response) {
            if (response.type) {
                $("#_order-status-current").html("Chờ giao");
                e.find('span').html('ĐÃ CHUYỂN');

                //next button status
                if(PERMISSION_ORDER_CHANGE_REQUEST_DELIVERY){
                    e.removeClass('disabled');
                    e.attr('id', '_change-request-delivery');
                    e.find('span').html('Y/C giao hàng');
                }

                insertComment($('#_box-internal'), ' Chuyển trạng thái đơn hàng sang chờ giao', first_name, current_username, datetime, current_img_path, false, false, true, true);
            } else {
                e.removeClass('disabled');
                Common.BSAlert(response.message);
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
                $("#_order-status-current").html("Yêu cầu giao");
                e.find('span').html('ĐÃ CHUYỂN');

                //next button status
                if(PERMISSION_ORDER_CHANGE_DELIVERY){
                    e.removeClass('disabled');
                    e.attr('id', '_change-delivery');
                    e.find('span').html('Đã giao hàng');
                }

                insertComment($('#_box-internal'), ' Chuyển trạng thái đơn hàng sang yêu cầu giao hàng'
                                , first_name, current_username, datetime, current_img_path, false, false, true, true);
            } else {
                e.removeClass('disabled');
                Common.BSAlert(response.message);
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
                $("#_order-status-current").html("Đang giao");
                e.find('span').html('ĐÃ CHUYỂN');
                //e.fadeOut();

                insertComment($('#_box-internal'), ' Chuyển trạng thái đơn hàng sang đang giao', first_name
                                            , current_username, datetime, current_img_path, false, false, true, true);
            } else {
                e.removeClass('disabled');
                Common.BSAlert(response.message);
            }
        });
    });

    $('#_change-action-select').change(function(e){
        var $e = $(e.currentTarget);
//        alert($e.val());
    });


});

function calTotalComplaint(){
    var $total = $('#_total-complaint');
    var t = parseInt($('input[name="total-complaint-seller"]').val())
                + parseInt($('input[name="total-complaint-service"]').val());
    if(t > 0){
        $total.html(t);
    }else{
        $total.remove();
    }
    $total.show();
}

function getOrderItemComments(){
    $.get(linkGetAllOrderItemComments, { order_id: order_id, page_size: page_size_product_comment }, function(result){
        if(result.type == 1){
            if(result.comments.length > 0){
                $.each(result.comments, function(idx, item){
                    var order_id = item.order_id;
                    var item_id = item.item_id;
                    var page_next = item.comment.page_next;
                    var pages = item.comment.pages;

                    var doc = $('#_show-more-item-comment-' + order_id + '-' + item_id);
                    var root = $('#_chat-item-body-' + order_id + '-' + item_id);
                    if (page_next > pages) {
                        doc.hide();
                    } else {
                        doc.show();
                        doc.attr('page', page_next);
                    }
                    root.find('._item-product-comments').append(order_item_comment_template(item.comment));
                    var total = item.comment.total_record - page_size_product_comment;
                    root.find('._total-remain-product-comments').attr('total', total);
                    root.find('._total-remain-product-comments').html(total);

                    if(item.comment.total_record == 0){
                        root.hide();
                    }
                });
            }
        }

        $('._item-product-view').mouseover(function(e){
            var $this = $(this);
            var len = $this.find('._content_chat_item_view').length;
            if(len == 0){
                $this.find('.comment-item').show();
            }
        });

        $('._item-product-view').mouseleave(function(e){
            var $this = $(this);
            var len = $this.find('._content_chat_item_view').length;
            if(len == 0){
                $this.find('.comment-item').hide();
            }
        });

        //CHAT (PUBLIC AND PRIVATE)
        getListChat();
    });
}

function getOrderItem(order_id) {
    $.get(get_items_url, {'order_id' : order_id}, function(response) {
//        console.log(response);
        $("#_list-item-placeholder").html(item_template(response));
        $('#_total-quantity-current').html('(' + response.total_quantity + ')');
        $('img.lazy').lazyload();

        $('._money-amount-k-none').moneyFormat({
            useClass: false,
            useThousand: true,
            symbol: 'K',
            signal: false
        });

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

    if(new_quantity == input.attr('recive_quantity')){
        return false;
    }

    $.post(change_item_quantity, {'item_id' : item_id, 'quantity' : new_quantity} , function(response) {
        if (callback) {
            callback(response);
        } else {
//            console.log(response);
            if (response.type == 1) {
                insertComment(root.find('#_box-internal'), 'Sửa số lượng sản phẩm với mã ' + response.order_item.id
                                                            + ' từ ' + response.old_recive_quantity
                                                            + ' thành ' + new_quantity, first_name, current_username, datetime, current_img_path, false, false, true, true);
                $('._item-product-view[data-item-id="' + item_id + '"]').find('.quantity').attr('recive_quantity', new_quantity);
                console.log('SỬA SỐ LƯỢNG');
                order.recive_quantity = order.recive_quantity + ( new_quantity - response.old_recive_quantity );
                console.log('SL của cả đơn: ' + order.recive_quantity);

//                if( new_quantity > response.old_recive_quantity  ) {
//                    order.recive_quantity = order.recive_quantity + ( new_quantity - response.old_recive_quantity );
//                } else {
//                    order.recive_quantity = order.recive_quantity + ( new_quantity - response.old_recive_quantity );
//                }

            } else {
                Common.BSAlert(response.message);
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
                Common.BSAlert(response.message);
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
            insertComment(root.find('#_box-external'), 'Đơn hàng bổ sung ' + ( weight / 1000 ) + ' kg', first_name, current_username, datetime, current_img_path, false, false, true, false);
            //2. Chat nội bộ
            insertComment(root.find('#_box-internal'), ' Đã sửa trọng lượng đơn từ ' + ( order.weight / 1000 ) + ' -> ' + ( weight / 1000 ) + ' kg', first_name, current_username, datetime, current_img_path, false, false, true, true);
            console.log(response.order.weight);
            order.weight = response.order.weight;
        } else {
            $this.removeClass('disabled');
            Common.BSAlert(response.message);
        }

    });
}

$(document).on('keyup','#_msg-chat-external',function(e){
    e.preventDefault();
    if(e.keyCode == 13){
//        console.log('chat voi khach');
        var order_id = $(this).data('order-id');
        var textarea = $('#_msg-chat-external');
        var message = textarea.val();
        var type = $(this).data('type');
        if(message == '') return;
        addOrderComment(order_id, message, textarea, type);
    }
});

$(document).on('keyup','#_msg-chat-internal',function(e){
    if(e.keyCode == 13){
//        console.log('chat noi bo');
        var order_id = $(this).data('order-id');
        var textarea = $('#_msg-chat-internal');
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

function change_transporting(order_id, callback) {
    $.post(change_to_transporting, {'order_id' : order_id}, function(response) {
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
                insertComment(root.find('#_box-external'), message, first_name, current_username, datetime, current_img_path, true, false, false, false);
            }
            if(type == 'INTERNAL'){
                insertComment(root.find('#_box-internal'), message, first_name, current_username, datetime, current_img_path, true, false, false, true);
            }

            $.ajax({
                url: linkAddOrderComment,
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
    document.prepend(chat_row_template({ message: message,
        account: _account,
        shorten_fullname: shorten_fullname,
        first_name: first_name,
        username: username,
        sub_time: sub_time,
        img_path : img_path,
        is_chat: is_chat,
        is_log: is_log,
        is_activity: is_activity,
        is_internal: is_internal,
        user_id: current_user_id
    })).show();

    document.find('._item-view-comment:first .user-img-tool').tooltip({
        html: true,
        placement: 'bottom'
    });
}

/** BEGIN COMMENT ORDER ITEM **/
// dau's order_item_comment
$(document).on('keyup','._item_input_chat',function(e){
    if(e.keyCode == 13){
        var order_id = $(this).data('order-id');
        var item_id = $(this).data('item-id');
        OrderItemComment.addMessge(order_id, item_id);
    }
});

// dau's button send order_item_comment
$(document).on('click','._btn_send_message',function(e){
    var e = $(e.currentTarget);
    var order_id = e.parent().find('._item_input_chat').data('order-id');
    var item_id = e.parent().find('._item_input_chat').data('item-id');
    OrderItemComment.addMessge(order_id, item_id);
});

// dau's load more item comment
$(document).on('click', '._view_item_chat', function(e) {
    var order_id = $(this).data('order-id');
    var item_id = $(this).data('item-id');
    var page = $(this).attr('page');

    var $e = $(e.currentTarget);
    if(!$e.hasClass('clicked')){
        $e.addClass('clicked');
        getOrderItemComment($e, order_id, item_id, page);
    }

});

// dau's
function getOrderItemComment(e, order_id, item_id, page){
    $.ajax({
        url: linkMoreOrderItemComment,
        type: 'POST',
        data: {
            order_id: order_id, item_id: item_id, page: page, page_size: page_size_product_comment
        },
        success: function (result) {
            var doc = $('#_show-more-item-comment-'+order_id+'-'+item_id);
            var root = $('#_chat-item-body-'+order_id+'-'+item_id);
            if (result.page_next > result.pages) {
                // Load end page => hide show more order item comment
                doc.hide();
            } else {
                doc.show();
                doc.attr('page', result.page_next);
            }
            root.find('._item-product-comments').append(order_item_comment_template(result));
            var total = parseInt(root.find('._total-remain-product-comments').attr('total')) - page_size_product_comment;
            root.find('._total-remain-product-comments').attr('total', total);
            root.find('._total-remain-product-comments').html(total);
            e.removeClass('clicked');
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
                    $('#_box-external').append(chat_row_template(item));
                });
                $.each(response.internal_comments, function(idx, item){
                    $('#_box-internal').append(chat_row_template(item));
                });
            }

            $("* [rel='tooltipbottom']").tooltip({
                html: true,
                placement: 'bottom'
            });
            $("abbr.timeago").timeago();

            Package.getListPackages();

            var $filter_comment = $('._filter-comment');
            var $row_item_comment = $('._item-view-comment');

            //show hide chat by checkbox checked
            $filter_comment.each(function(i) {
                var $this = $(this);
                var value = $this.data('value');
                if( $this.is(':checked') ) {
                    $('._item-view-comment.' + value).show();
                } else {
                    $('._item-view-comment.' + value).hide();
                }
            });

            //filter chat
            $filter_comment.click(function() {
                var check = false;
                $row_item_comment.hide();

                $filter_comment.each(function(i) {
                    var $this = $(this);
                    var value = $this.data('value');
                    if( $this.is(':checked') ) {
                        check = true;
                        $('._item-view-comment.' + value).show();
                    } else {
                        $('._item-view-comment.' + value).hide();
                    }
                });

                if(!check) {
                    $row_item_comment.show();
                }

                //push URL
                Order.filter_comment();
            });
        }
    });
}

var Order = {
    filter_comment:function(type) {
        var search_data = $('#_filter-comment-by-type').serialize();
        if(type == null){
            var pageUrl = LinkOrderDetailUrl + '?' + search_data;
            Order.push_state(pageUrl);
        }
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    getURLParameters: function(url) {
        var result = {};
        var searchIndex = url.indexOf("?");
        if (searchIndex == -1 ) return result;
        var sPageURL = url.substring(searchIndex +1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            result[sParameterName[0]] = sParameterName[1];
        }
        return result;
    }
};

var Package = {
    saveWeight: function( arrPackage ) {
        $('._click-save-weight-package').click(function() {
            var $this = $(this);
            var $row = $this.parents('._item-row-package-view');
            var weight = $row.find('._txt-weight-package').autoNumeric('get');
            var package_id = $row.data('id');
            console.log('weight: ' + weight);

            var classDisabled = 'disabled';

            if( $this.hasClass(classDisabled)
                || weight == arrPackage[ package_id ].weight ) {
                return false;
            }

            $this.addClass(classDisabled);

            $.ajax({
                url: LinkUpdatePackageWeight,
                type : "POST",
                data: { package_id: package_id, weight: weight, order_id: order.id },
                success: function ( response ) {
                    console.log( response );
                    $this.removeClass(classDisabled);
                    if( response.type == 0 ) {
                        Common.BSAlert( response.message );
                        return false;
                    } else {
                        //INSERT LOG & ACTIVITY
                        insertComment($('#_box-internal'),
                            "Sửa cân nặng từ " + arrPackage[ package_id ].weight + "gr thành " + weight + "gr với kiện có mã " + arrPackage[ package_id ].package_code + ".Cập nhật cân nặng đơn hàng " + response.data + " gr",
                            first_name, current_username, datetime, current_img_path, false, false, true, true);
                        insertComment($('#_box-external'),
                            "Sửa cân nặng từ " + arrPackage[ package_id ].weight + "gr thành " + weight + "gr với kiện có mã " + arrPackage[ package_id ].package_code + ".Cập nhật cân nặng đơn hàng " + response.data + " gr",
                            first_name, current_username, datetime, current_img_path, false, true, false, false);
                        arrPackage[ package_id ].weight = weight;

                        order.weight = parseFloat( response.data );
                    }
                }
            });
        });

        $('._txt-weight-package').keypress(function(e) {
            if(e.keyCode == 13) {
                $(this).parent().find('._click-save-weight-package').click();
            }
        });
    },

    checking: function( arrPackage ) {
        $('._click-check-packing').click(function(){
            var $this = $(this);
            var $row = $this.parents('._item-row-package-view');
            var package_id = $row.data('id');
            var total_product = 5;

            var classDisabled = 'disabled';

            $this.addClass(classDisabled);

            $.ajax({
                url: LinkAddPackageChecking,
                type : "POST",
                data: { package_id: package_id, total_product: total_product },
                success: function ( response ) {
                    $this.removeClass(classDisabled);
                    if( response.type == 0 ) {
                        Common.BSAlert( response.message );
                        return false;
                    } else {
                        $('._item-row-package-view[data-id="' + package_id + '"]').find('._list-checking').append(item_row_package_checking( response.data ));
                    }
                }
            });
        });
    },

    getListPackages: function() {
        $.ajax({
            url: LinkGetPackages,
            data: { order_id: order.id },
            type : "GET",
            success: function ( response ) {
                if( response.type == 0 ) {
                    //TODO
                } else {
                    var $main = $('#_main-list-package-by-order');
                    var $list = $('#_list-package-by-order');
                    var $total_receive_quantity_package = $('#_total-receive-quantity-package');
                    var total_checking_quantity = 0;
                    var arrPackage = [];
                    if( response.total > 0 ) {
                        $.each(response.data, function(idx, item) {
//                            console.log(item);
                            arrPackage[ item.id ] = item;
                            $list.append(item_row_package(item));
                            total_checking_quantity += parseFloat( item.total_checking );

                            //list checking
                            if( item.package_checking_history.length > 0 ) {
                                $.each(item.package_checking_history, function(i, row){
                                    $('._item-row-package-view:last').find('._list-checking').append(item_row_package_checking(row));
                                });
                            }
                        });
                    } else {
                        $main.html('Đơn hàng chưa có kiện nào!');
                    }

                    if( total_checking_quantity > 0 ) {
                        $total_receive_quantity_package.show().html('(' + total_checking_quantity + ')');
                    }

                    $('._txt-weight-package').autoNumeric({ aPad: false, mDec: 9 });

                    //save weight package
                    Package.saveWeight( arrPackage );

                    //checking packing
                    Package.checking( arrPackage );

                    //tooltips
                    $("span.user-img-tool").tooltip({
                        html: true,
                        placement: 'bottom'
                    });
                }

                ComplaintSeller.getListComplaintSeller({ order_id: order_id, page: 1, all: 1 });

            }
        })
    }
};

// dau's
var OrderItemComment = {
    addMessge : function(order_id, item_id){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        var root = $('#_chat-item-body-' + order_id + '-' + item_id);
        var loaded = root.find('._item_input_chat').attr('loaded');
        var doc = root.find('._item_input_chat');
        var message = doc.val();
        var short_time = currentdate.getHours() + ':' + (currentdate.getMinutes()<10 ? '0' : '') + currentdate.getMinutes() + ' ' + day + '/' + (month<10 ? '0' : '') + month;

        if (message.length > 0) {
            doc.val('');
            root.find('._item-product-comments').prepend(item_order_item_comment_template({ message: message,
                                                                                                username: current_username,
                                                                                                shorten_fullname: shorten_fullname,
                                                                                                user_id: current_user_id,
                                                                                                short_time: short_time}));
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
    var reason_title = $('#_complaint-seller-reason option[value="' + reason + '"]').text();
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
            insertComment($('#_box-external'), "Đơn hàng " + order.code + " đang được khiếu nại với người bán. Lý do: " + reason_title, first_name, current_username, datetime, current_img_path, false, false, true, false);
            insertComment($('#_box-internal'), "Đơn hàng " + order.code + " đang được khiếu nại với người bán. Lý do: " + reason_title, first_name, current_username, datetime, current_img_path, false, false, true, true);
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
    e.html('Đang khiếu nại người bán');
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
                $('#_btn-doing-complaint-seller').html('Khiếu nại thành công');
                $('#_btn-doing-complaint-seller').removeAttr('id', '');
                insertComment($('#_box-internal'),
                    "Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " + amount_seller_refund + " NDT",
                    first_name, current_username, datetime, current_img_path, false, false, true, true);
                insertComment($('#_box-external'), "Khiếu nại người bán thành công",
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
                $('#_btn-doing-complaint-seller').html('Khiếu nại thất bại');
                $('#_btn-doing-complaint-seller').removeAttr('id', '');
                insertComment($('#_box-internal'),
                    "Chuyển trạng thái khiếu nại người bán sang thất bại",
                    first_name, current_username, datetime, current_img_path, false, false, true, true);
                insertComment($('#_box-external'), "Khiếu nại người bán thất bại",
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
                var init = response.data;
                $("#_list-fee").html(render_fee(response.data));
                moneyFormat();
            }
            $('html, body').animate({
                scrollTop: $(".order-detai-content").offset().top - 30
            });
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
                moneyFormat();
            }
            getListFreeOrder();
        },
        error: function() {}
    });
}

function moneyFormat(){
    $('._money-amount-k').moneyFormat({
        useClass: false,
        useThousand: true,
        symbol: 'K'
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
                            //Những sản phẩm trong đơn hàng có khiếu nại
                            $('._item-product-view[data-item-id="' + item.item_id + '"]').find('._order-item-complaint').html('Khiếu nại dịch vụ');
                        });

//                        $('#_show-paging-complaint').html(createPaging(response.total_page, response.current_page, '-complaint'));
//
//                        //paging
//                        $('._paging-complaint').click(function(e){
//                            console.log('paging');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                var page = e.data('page');
//                                page_complaint = page;
//                                e.addClass('clicked');
//                                Complaint.getListComplaint({ order_id: order_id,
//                                                                page: page, limit: limit_complaint});
//                            }
//                        });
//
//                        $('#_page-prev-complaint').click(function(e){
//                            console.log('page prev');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                page_complaint--;
//                                e.addClass('clicked');
//                                Complaint.getListComplaint({ order_id: order_id,
//                                                                page: page_complaint, limit: limit_complaint});
//                            }
//                        });
//
//                        $('#_page-next-complaint').click(function(e){
//                            console.log('page next');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                page_complaint++;
//                                e.addClass('clicked');
//                                Complaint.getListComplaint({ order_id: order_id,
//                                    page: page_complaint, limit: limit_complaint});
//                            }
//                        });
                    }else{
                        $('#_tbl-complaint-service').hide();
                    }
                }else{
                    //TODO
                }
                $('#_total-complaint-service').html(response.total_record);
                $('input[name="total-complaint-service"]').val(response.total_record);
                calTotalComplaint();
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

//                        $('#_show-paging-complaint-seller').html(createPaging(response.total_page, response.current_page, '-complaint-seller'));
//
//                        //paging
//                        $('._paging-complaint-seller').click(function(e){
//                            console.log('paging');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                var page = e.data('page');
//                                page_complaint_seller = page;
//                                e.addClass('clicked');
//                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
//                                                                            page: page, limit:
//                                                                            limit_complaint_seller });
//                            }
//                        });
//
//                        $('#_page-prev-complaint-seller').click(function(e){
//                            console.log('page prev');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                page_complaint_seller--;
//                                e.addClass('clicked');
//                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
//                                                                            page: page_complaint_seller,
//                                                                            limit: limit_complaint_seller });
//                            }
//                        });
//
//                        $('#_page-next-complaint-seller').click(function(e){
//                            console.log('page next');
//                            var e = $(e.currentTarget);
//                            if(!e.hasClass('clicked')){
//                                page_complaint_seller++;
//                                e.addClass('clicked');
//                                ComplaintSeller.getListComplaintSeller({ order_id: order_id,
//                                                                            page: page_complaint_seller,
//                                                                            limit:limit_complaint_seller });
//                            }
//                        });
                    }else{
                        $('#_tbl-complaint-seller').hide();
                    }
                }else{
                    //TODO
                }
                $('#_total-complaint-seller').html(response.total_record);
                $('input[name="total-complaint-seller"]').val(response.total_record);
                Complaint.getListComplaint({ order_id: order_id, page: 1, get_by_buyer: 1, all: 1 });
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

$(document).ready(function(){
    $(window).scroll(function(){
        var y = $(window).scrollTop();
        if(y>150){
            $(".order-detai-scoll-top").removeClass("hidden");
        }
        if(y<150){
            $(".order-detai-scoll-top").addClass("hidden");
        }
    });


});
