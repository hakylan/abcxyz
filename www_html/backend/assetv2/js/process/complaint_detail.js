var ajax_rq = null;
var chat_row_template;
var item_row_complaint_seller;
var item_row_complaint;
var refocus_time_tpl;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var _sub_time = currentdate.getHours() + ':' + currentdate.getMinutes() + ' ' + day + '/' + (month<10 ? '0' : '') + month;
var flag_amount = 0;
//0: Chấp nhận theo mức yêu cầu của khách hàng
//1: Chấp nhận mức đề xuất do chính mình đưa ra

$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#datepicker2" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
});
$(window).on('load', function () {

});

$(document).ajaxComplete(function(){

});

function getURLParameters (url) {
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

$(document).ready(function(){

    console.log( complaint );

    $( "#_refocus-time" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $('.fancybox-thumbs').fancybox({
        prevEffect : 'none',
        nextEffect : 'none',

        closeBtn  : false,
        arrows    : false,
        nextClick : true,

        helpers : {
            thumbs : {
                width  : 50,
                height : 50
            }
        }
    });

    if(disabled_form){ Complaint.disableForm(); }
    Complaint.getListComplaintSeller();

    $('#main-body').removeClass('container');
    $('.seubox-chat').css('height', window.innerHeight + 'px');

    chat_row_template = Handlebars.compile($("#_item-chat-row").html());
    item_row_complaint_seller = Handlebars.compile($("#_item-row-complaint-seller").html());
    item_row_complaint = Handlebars.compile($("#_item-row-complaint").html());
    refocus_time_tpl = Handlebars.compile($("#_refocus-time-tpl").html());

    //autoNumeric
    $('#_txt-recipient-amount-reimbursement').autoNumeric({ aPad: false, mDec: 9, vMax: 999999999999999999999999999999999999999999999999 });
    $('#_damage-amount').autoNumeric({ aPad: false, mDec: 9 });


    //checked checkbox by URL
    var params = getURLParameters( window.location.href );

    var c = false;
    for (var paramName in params){
        switch ( paramName ) {
            case 'CHAT':
                c = true;
                $('._filter-comment[name="CHAT"]').prop('checked', true);
                break;
            case 'ACTIVITY':
                c = true;
                $('._filter-comment[name="ACTIVITY"]').prop('checked', true);
                break;
            case 'LOG':
                c = true;
                $('._filter-comment[name="LOG"]').prop('checked', true);
                break;
            default:
                c = true;
                $('._filter-comment[name="CHAT"]').prop('checked', true);
                break;
        }
    }

    if( !c ) {
        $('._filter-comment[name="CHAT"]').prop('checked', true);
    }

    $('#_btn-recipient-amount-reimbursement').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        var recipient_amount_reimbursement = $('#_txt-recipient-amount-reimbursement').autoNumeric('get');

        if(recipient_amount_reimbursement == complaint.recipient_amount_reimbursement){
            return false;
        }

        if(recipient_amount_reimbursement == 0){
            Common.BSAlert('Số tiền không hợp lệ!');
            $('#_txt-recipient-amount-reimbursement').focus();
            return false;
        }

        e.addClass('clicked');
        e.html('Đang xử lý');
        Complaint.updateRecipientAmountReimbursement(e, recipient_amount_reimbursement);
    });

    $('#_btn-problem-output').click(function(e){
        $('._panel-recipient-amount-reimbursement').hide();
        $('._panel-recipient-amount-reimbursement[type=1]').show();

        $('#_btn-recipient-amount-reimbursement').removeClass('clicked');
    });

    //Trạng thái hoàn tiền
    $('#_btn-approval').click(function(e){
        Common.BSConfirm('Mức bồi hoàn sẽ là ' + numeral(complaint.recipient_amount_reimbursement).format('0,0') + 'đ. Bạn có chắc muốn hoàn tiền cho khách hàng hay không?');
    });

    $('#_btn-agree-confirm').click(function(e){
        var e = $(e.currentTarget);
        var $this = $('#_btn-approval');
        if(e.hasClass('clicked')){
            return false;
        }

        e.addClass('clicked');
        $('._btn-status').prop('disabled', true);
        $this.html($this.data('doing'));

        Complaint.updateStatusRefund($this);
    });

    //Trạng thái chấp nhận
    $('#_btn-accept').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.addClass('clicked');
        $('._btn-status').prop('disabled', true);
        e.html(e.data('doing'));

        Complaint.updateStatusAccept(e);
    });

    //Trạng thái từ chối
    $('#_btn-reject').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.addClass('clicked');
        $('._btn-status').prop('disabled', true);
        e.html(e.data('doing'));

        Complaint.updateStatusReject(e);
    });

    //Trangj thái tiếp nhận
    $('#_btn-recipient').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.addClass('clicked');
        $('._btn-status').prop('disabled', true);
        e.html(e.data('doing'));

        Complaint.updateStatusReception(e);
    });

    //Checkbox
    $('._chk').click(function(e){
        var e = $(e.currentTarget);
        if(e.is(':checked')){
            e.val('YES');
        }else{
            e.val('NO');
        }

        //nếu là thiệt hại cho cty thì được phép nhập số tiền gây thiệt hại
        if(e.data('type') == 'DAMAGE'){
            if(e.is(':checked')){
                $('#_panel-damage-amount').show();
            }else{
                $('#_panel-damage-amount').hide();
            }
        }

        Complaint.updateReasonError(e, e.data('type'), e.val());
    });

    $('#_damage-amount').focusout(function(e){
        var e = $(e.currentTarget);
        var damage_amount = e.autoNumeric('get');
        Complaint.updateDamageAmount(e, e.autoNumeric('get'));
    });

    $('#_damage-amount').keypress(function(e){
        if(e.keyCode == 13){
            var e = $(e.currentTarget);
            var damage_amount = e.autoNumeric('get');
            Complaint.updateDamageAmount(e, e.autoNumeric('get'));
        }
    });

    $('._input-chat').keypress(function(e){
        if(e.keyCode == 13){
            var e = $(e.currentTarget);
            Chat.addMessge(e.val(), e.data('type'), "CHAT");
            e.val('');
        }
    });

    $('#_textarea-description').focusout(function(e){
        var e = $(e.currentTarget);
        if(e.val().length == 0){
            return false;
        }
        Complaint.updateComplaintDescription(e.val());
    });

    $('#_open-form-update-damage-amount').click(function(e){
        $('._update-damage-amount').hide();
        $('._update-damage-amount[data-type="2"]').show();
        $('#_damage-amount').focus();
    });



    $('#_btn-accept-refused').click(function(e) {
        var e = $(e.currentTarget);
        var reason = $('#_area-reason-refused').val();
        if(e.hasClass('disabled')){
            return false;
        }
        e.addClass('disabled');
        e.html(e.data('doing'));

        $.ajax({
            url:  linkUpdateStatusRefusedReceptionComplaint,
            type: "POST",
            data: { complaint_id: complaint_id },
            success: function (response) {
                if(response.type == 1){
                    $('._btn-status').hide();
                    var $btn_show_popup_refused = $('#_btn-show-popup-refused');
                    $btn_show_popup_refused.html($btn_show_popup_refused.data('message')).show().addClass('disabled');
                    $btn_show_popup_refused.removeAttr('data-target', '');

                    //insert log and activity
                    var reason_title = reason == '' ? '' : '. Lý do: ' + reason;
                    Chat.addMessge("Từ chối tiếp nhận khiếu nại" + reason_title, "INTERNAL", "ACTIVITY");
                    Chat.addMessge("Từ chối tiếp nhận khiếu nại" + reason_title, "EXTERNAL", "LOG");

                    //cập nhật lại trạng thái hiện tại cho khiếu nại
                    $('#_status').html(response.status);
                    complaint.status = response.status;

                    //hide modal reason
                    $('#_show-popup-refused').modal('hide');
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            },
            error: function() {}
        });
    });

    $('#_refocus-time').change(function(){
        var refocus_time = $(this).val();
        Complaint.updateRefocusTime( refocus_time );
    });

    Complaint.getListOtherComplaints();

    Chat.getList();
});

var Chat = {
    /* Hàm lấy ra danh sách comment */
    getList: function(){
        $.ajax({
            url: linkGetListComments,
            type: 'POST',
            data: {
                complaint_id: complaint_id,
                context: ''
            },
            success: function (response) {
                if(response.external_comments.length > 0){
                    $.each(response.external_comments, function(idx, item){
                        $('#_box-external').append(chat_row_template(item));
                    });
                }
                if(response.internal_comments.length > 0){
                    $.each(response.internal_comments, function(idx, item){
                        $('#_box-internal').append(chat_row_template(item));
                    });
                }

                $('[rel="tooltipbottom"]').tooltip({
                    html: true,
                    placement: 'bottom'
                });

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
                    Complaint.filter_comment();
                });
            }
        });
    },

    /* data input is array (first_name, username, sub_time, img_path, is_chat, is_log, is_activity, is_internal, user_id) */
    prependComment: function(document, data){
        document.prepend(chat_row_template(data)).show();
        document.find('._item-view-comment:first .user-img-tool').tooltip({
            html: true,
            placement: 'bottom'
        });
    },

    replaceURLWithHTMLLinks: function replaceURLWithHTMLLinks(text) {
        var exp = /(\b(https?|ftp|file):\/\/([-A-Z0-9+&@#%?=~_|!:,.;]*)([-A-Z0-9+&@#%?\/=~_|!:,.;]*)[-A-Z0-9+&@#\/%=~_|])/ig;
        return text.replace(exp, "<a target='_blank' href='$1' target='_blank'>$3</a>");
    },

    addMessge : function(message, type, context){
//        if(ajax_rq != null){
//            ajax_rq.abort();
//        }

        //Xử lý content
        if(message != ''){
            var arrMessage = message.split(' ');
            var str = '';
            for(var i = 0; i < arrMessage.length; i++){
//                console.log(arrMessage[i]);
                str += Chat.replaceURLWithHTMLLinks(arrMessage[i]) + ' ';
            }
            message = str;
        }

        if(message.length == 0){
            return false;
        }

        if(type == 'EXTERNAL'){
            Chat.prependComment($('#_box-external'), {
                account: _account,
                first_name: _first_name,
                username: _username,
                sub_time: _sub_time,
                img_path: _img_path,
                is_chat: context == "CHAT" ? true : false,
                is_log: context == "LOG" ? true : false,
                is_activity: context == "ACTIVITY" ? true : false,
                is_internal: false,
                user_id: _user_id,
                message: message
            });
        }

        if(type == 'INTERNAL'){
            Chat.prependComment($('#_box-internal'), {
                account: _account,
                first_name: _first_name,
                username: _username,
                sub_time: _sub_time,
                img_path: _img_path,
                is_chat: context == "CHAT" ? true : false,
                is_log: context == "LOG" ? true : false,
                is_activity: context == "ACTIVITY" ? true : false,
                is_internal: true,
                user_id: _user_id,
                message: message
            });
        }

        if (message.length > 0) {
            ajax_rq = $.ajax({
                url: linkAddComplaintComment,
                type : "POST",
                data: {
                    order_id: order_id,
                    item_id: item_id,
                    complaint_id: complaint_id,
                    type: type,
                    message: message,
                    context: context
                },
                success: function (data) {
                    //TODO
                }
            });
        }
    }
};

var Complaint = {

    updateRefocusTime: function(refocus_time) {

        if( refocus_time == '' || refocus_time == null || complaint.refocus_time == refocus_time ) {
            return false;
        }

        $.ajax({
            url: linkUpdateRefocusTime,
            type : "POST",
            data: {
                refocus_time: refocus_time,
                complaint_id: complaint_id
            },
            success: function (response) {
//                console.log('refocus_time: ' + refocus_time);
                if( response.type == 1 ) {

                    if( complaint.refocus_time == null || complaint.refocus_time == '' ) {
                        Chat.addMessge(" đặt hạn xử lý cho khiếu nại này vào " + refocus_time, "INTERNAL", "ACTIVITY");
                    } else {
                        Chat.addMessge(" thay đổi hạn xử lý khiếu nại từ " + complaint.refocus_time + " thành " + refocus_time, "INTERNAL", "ACTIVITY");
                    }

                    complaint.refocus_time = refocus_time;
                } else {
                    Common.BSAlert( response.message );
                }
            },
            error: function() {}
        });
    },

    getListOtherComplaints: function() {
        $.ajax({
            url: linkGetListOtherComplaint,
            type : "POST",
            data: {
                order_id: order_id,
                all: 1,
                get_by_buyer: 1,//Lấy theo tất cả ko riêng gì user nào
                not_in: [ complaint_id ]//Loại bỏ KNDV hiện tại
            },
            success: function (response) {
                if(response.type == 1){
                    if(response.items.length > 0){
                        $.each(response.items, function(idx, item){
                            $('#_list-complaints').append(item_row_complaint(item));
                        });

                        $('._money-amount-k').moneyFormat({
                            useClass: false,
                            useThousand: true,
                            symbol: 'K',
                            signal: false
                        });
                    }else{
                        //hide table
                        $('#_show-table-complaints').hide();
                    }
                }
            }
        });
    },

    filter_comment:function(type) {
        var search_data = $('#_filter-comment-by-type').serialize();
        if(type == null){
            var pageUrl = LinkListBackendComplaintUrl + '?' + search_data;
            Complaint.push_state(pageUrl);
        }
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    updateStatusReception: function(e){
        $.ajax({
            url:  linkUpdateStatusReceptionComplaint,
            type: "POST",
            data: { complaint_id: complaint_id },
            success: function (response) {
                if(response.type == 1){
                    var $btn_accept = $('#_btn-accept');//button chấp nhận
                    var $btn_reject = $('#_btn-reject');//button từ chối
                    var $btn_approval = $('#_btn-approval');//button hoàn tiền

                    e.html(e.data('message'));

                    //Nếu sau khi tiếp nhận mà user đó có quyền xử lý khiếu nại thì hiển thị form xử lý khiếu nại lên
                    var check = false;
                    $('#_panel-complaint-process').show();
                    if(permission_complaint_can_accept || permission_complaint_can_reject){
                        check = true;
                        if(permission_complaint_can_accept){
                            $btn_accept.show();
                            $btn_accept.prop('disabled', false);
                        }
                        if(permission_complaint_can_reject){
                            $btn_reject.show();
                            $btn_reject.prop('disabled', false);
                        }
                    }

                    if(check){
                        $('#_panel-complaint-reception').hide();
                    }else{
                        $('#_panel-complaint-reception').show();
                    }

                    Chat.addMessge("Đã tiếp nhận khiếu nại", "INTERNAL", "ACTIVITY");
                    Chat.addMessge("Khiếu nại đã được chấp nhận giải quyết", "EXTERNAL", "ACTIVITY");

                    $('#_status').html(response.status);
                    complaint.status = response.status;
                    e.html(e.data('message'));
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            },
            error: function() {}
        });
    },

    updateStatusAccept: function(e){
        $.ajax({
            url:  linkUpdateStatusAcceptComplaint,
            type: "POST",
            data: { complaint_id: complaint_id },
            success: function (response) {
                if(response.type == 1){
                    var $btn_approval = $('#_btn-approval');
                    var $btn_reject = $('#_btn-reject');
                    var $btn_status = $('._btn-status');
                    //Nếu mức bồi thường > 0 thì chấp nhận mức này
                    if(complaint.recipient_amount_reimbursement > 0){
                        Chat.addMessge("Khiếu nại đã được chấp nhận với mức đề xuất là " + numeral(complaint.recipient_amount_reimbursement).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                        Chat.addMessge("Khiếu nại đã được chấp nhận với mức đề xuất là " + numeral(complaint.recipient_amount_reimbursement).format('0,0') + "đ", "EXTERNAL", "ACTIVITY");
                    }
                    //Nếu mức bồi thường = 0 thì chấp nhận mức khách yêu cầu
                    if(complaint.recipient_amount_reimbursement == 0){
                        complaint.recipient_amount_reimbursement = complaint.customer_amount_reimbursement;
                        $('#_txt-recipient-amount-reimbursement').autoNumeric('set', complaint.customer_amount_reimbursement);
                        $('#_recipient-amount-reimbursement').html(complaint.customer_amount_reimbursement + '<sup>đ</sup>');
                        $('._panel-recipient-amount-reimbursement').hide();
                        $('._panel-recipient-amount-reimbursement[type=2]').show();

                        Chat.addMessge("Chấp nhận mức yêu cầu bồi thường " + numeral(complaint.customer_amount_reimbursement).format('0,0') + "đ của khách hàng", "INTERNAL", "ACTIVITY");
                        Chat.addMessge("Chấp nhận mức yêu cầu bồi thường " + numeral(complaint.customer_amount_reimbursement).format('0,0') + "đ của khách hàng", "EXTERNAL", "ACTIVITY");
                    }

                    //Nếu có quyền hoàn tiền thì show nút hoàn tiền + nút từ chối
                    if(permission_complaint_can_censorship_financical){
                        $btn_status.hide();
                        $btn_approval.show();
                        $btn_approval.prop('disabled', false);
                        $btn_reject.show();
                        $btn_reject.prop('disabled', false);
                    }

                    $('#_status').html(response.status);
                    complaint.status = response.status;
                    e.html(e.data('message'));
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            },
            error: function() {}
        });
    },

    updateStatusReject: function(e){
        //Kiểm tra xem đang từ chối ở bước nào
        var flag = true;
        var amount = 0;
        //Trạng thái hoàn tiền
        if(complaint.status == 'ACCEPT'){
            flag = false;
        }
        if(complaint.recipient_amount_reimbursement > 0){
            amount = complaint.recipient_amount_reimbursement;
        }else{
            amount = complaint.customer_amount_reimbursement;
        }

        $.ajax({
            url:  linkUpdateStatusRejectComplaint,
            type: "POST",
            data: { complaint_id: complaint_id },
            success: function (response) {
                if(response.type == 1){
                    if(flag){
                        if(complaint.recipient_amount_reimbursement > 0){
                            Chat.addMessge("Đã từ chối mức bồi thường: " + numeral(amount).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                            Chat.addMessge("Từ chối mức bồi thường " + numeral(amount).format('0,0') + "đ", "EXTERNAL", "ACTIVITY");
                        }else{
                            Chat.addMessge("Đã từ chối mức yêu cầu bồi hoàn của khách hàng: " + numeral(amount).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                            Chat.addMessge("Từ chối mức yêu cầu bồi hoàn của khách hàng " + numeral(amount).format('0,0') + "đ", "EXTERNAL", "ACTIVITY");
                        }
                    }else{
                        Chat.addMessge("Đã từ chối hoàn " + numeral(amount).format('0,0') + "đ, khiếu nại thất bại", "INTERNAL", "ACTIVITY");
                        Chat.addMessge("Từ chồi hoàn " + numeral(amount).format('0,0') + "đ, khiếu nại thất bại", "EXTERNAL", "ACTIVITY");
                    }

                    $('#_status').html(response.status);
                    complaint.status = response.status;
                    e.html(e.data('message'));

                    //hide modal reason
                    $('#_show-popup-refused').modal('hide');
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            },
            error: function() {}
        });
    },

    updateStatusRefund: function(e){
        $.ajax({
            url:  linkUpdateStatusRefundComplaint,
            type: "POST",
            data: { complaint_id: complaint_id },
            success: function (response) {
                if(response.type == 1){
                    Chat.addMessge("Khiếu nại đã được hoàn số tiền " + numeral(complaint.recipient_amount_reimbursement).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                    Chat.addMessge("Khiếu nại đã được hoàn số tiền " + numeral(complaint.recipient_amount_reimbursement).format('0,0') + "đ", "EXTERNAL", "ACTIVITY");
                    Complaint.disableForm();

                    $('#_transaction-code').html(response.transaction_code);
                    $('#_status').html(response.status);
                    complaint.status = response.status;
                    e.html(e.data('message'));
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            },
            error: function() {}
        });
    },

    disableForm: function(){
        //disabled form
        $('#_txt-recipient-amount-reimbursement').prop('disabled', true);
        $('._chk').prop('disabled', true);
        $('#_damage-amount').prop('disabled', true);
        $('#_textarea-description').prop('disabled', true);
        $('#_btn-problem-output').remove();
    },

    createPaging: function(total_page, current_page){
        var html = '';
        if(total_page > 1){
            var j = 2;
            html += '<div class="row link-bottom">';
            html += '<div class="col-lg-12 col-md-12">';
            html += '<ul class="pagination pull-left">';
            if(current_page > 1){
                html += '<li class="pre"><a id="_page-prev">&lt;</a></li>';
            }

            for(var i = j; i > 0; i--){
                if(current_page - i > 0){
                    html += '<li>';
                    html += '<a class="_paging" data-page="' + ( current_page - i ) + '">' + ( current_page - i ) + '</a>';
                    html += '</li>';
                }
            }

            html += '<li class="active"><a>' + current_page + '</a></li>';

            for(var i = 1; i <= j; i++){
                if(current_page + i <= total_page){
                    html += '<li>';
                    html += '<a class="_paging" data-page="' + ( current_page + i ) + '">' + ( current_page + i ) + '</a>';
                    html += '</li>';
                }
            }

            if(current_page < total_page){
                html += '<li class="next"><a id="_page-next">&gt;</a></li>';
            }
            html += '</ul>';
            html += '</div>';
            html += '</div>';
        }
        return html;
    },

    getListComplaintSeller: function(search_data){
        if(search_data == undefined){
//            search_data = { order_id: 140 };
            search_data = { order_id: order_id };
        }
        $.ajax({
            url: linkGetListComplaintSeller,
            type : "POST",
            data: search_data,
            success: function (response) {
                if(response.type == 1){
                    $('#_list-complaint-sellers').empty();
                    if(response.items.length > 0){
                        $.each(response.items, function(idx, item){
                            $('#_list-complaint-sellers').append(item_row_complaint_seller(item));
                        });

                        $('#_show-paging').html(Complaint.createPaging(response.total_page, response.current_page));

                        //paging
                        $('._paging').click(function(e){
                            console.log('paging');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var page = e.data('page');
                                $('#_current-page').val(page);
                                e.addClass('clicked');
                                Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                            }
                        });

                        $('#_page-prev').click(function(e){
                            console.log('page prev');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var $page = $('#_current-page');
                                var current_page = parseInt($page.val());
                                current_page--;
                                $page.val(current_page);
                                e.addClass('clicked');
                                Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                            }
                        });

                        $('#_page-next').click(function(e){
                            console.log('page next');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var $page = $('#_current-page');
                                var current_page = parseInt($page.val());
                                current_page++;
                                $page.val(current_page);
                                e.addClass('clicked');
                                Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                            }
                        });
                    }else{
                        $('#_tbl-list-complaint-sellers').hide();
                    }
                }else{
//                  Common.BSAlert(response.message);
//                  return false;
                }
            }
        });
    },

    complaintFilter : function(type){
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = LinkListBackendComplaintUrl+'?'+search_data;
            Complaint.push_state(pageUrl);
        }
        Complaint.getListComplaintSeller(search_data);
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    //Hàm cập nhật số tiền gây thiệt hại cho cty là bao nhiêu.
    updateDamageAmount: function(e, damage_amount){
//        if(ajax_rq != null){
//            ajax_rq.abort();
//        }
        ajax_rq = $.ajax({
            url: linkUpdateComplaintDamageAmount,
            type : "POST",
            data: {
                complaint_id: complaint_id,
                damage_amount: damage_amount
            },
            success: function (data) {
                if(damage_amount != 0 && damage_amount != complaint.damage_amount){
                    Chat.addMessge("Đã cập nhật số tiền gây thiệt hại cho công ty là : " + numeral(damage_amount).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                    complaint.damage_amount = damage_amount;

                    $('#_value-damage-amount').html(e.val());
                    $('._update-damage-amount').hide();
                    $('._update-damage-amount[data-type="1"]').show();
                }
            }
        });
    },

    //Cập nhật số tiền đề xuất từ phía người quản trị
    updateRecipientAmountReimbursement: function(e, recipient_amount_reimbursement){
        $.ajax({
            url:  linkUpdateRecipientAmountReimbursement,
            type: "POST",
            data: { complaint_id: complaint_id, recipient_amount_reimbursement: recipient_amount_reimbursement },
            success: function (response) {
//                console.log('success');
                e.html('Đề xuất');
                $('#_recipient-amount-reimbursement').html($('#_txt-recipient-amount-reimbursement').val());
                $('._panel-recipient-amount-reimbursement').hide();
                $('._panel-recipient-amount-reimbursement[type=2]').show();

                if(recipient_amount_reimbursement != complaint.recipient_amount_reimbursement){
                    Chat.addMessge("Đã đề xuất mức bồi thường cho khách hàng là: " + numeral(recipient_amount_reimbursement).format('0,0') + "đ", "INTERNAL", "ACTIVITY");
                    Chat.addMessge("Đề xuất mức bồi thường là: " + numeral(recipient_amount_reimbursement).format('0,0') + "đ", "EXTERNAL", "ACTIVITY");
                    flag_amount = 1;
                    complaint.recipient_amount_reimbursement = recipient_amount_reimbursement;
                }

                //Sau khi đề xuất mức bồi hoàn mới thì hiển thị 2 nút chấp nhận và từ chối
                var $btn_accept = $('#_btn-accept');
                var $btn_reject = $('#_btn-reject');
                var $btn_status = $('._btn-status');
                if(permission_complaint_can_accept || permission_complaint_can_reject){
                    $btn_status.hide();
                    if(permission_complaint_can_accept){
                        $btn_accept.show();
                        $btn_accept.prop('disabled', false);
                    }
                    if(permission_complaint_can_reject){
                        $btn_reject.show();
                        $btn_reject.removeClass('clicked');
                        $btn_reject.html('Từ chối');
                        $btn_reject.prop('disabled', false);
                    }
                }
            },
            error: function() {}
        });
    },

    updateComplaintDescription: function(description){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        ajax_rq = $.ajax({
            url: linkUpdateComplaintDescription,
            type : "POST",
            data: {
                complaint_id: complaint_id,
                description: description
            },
            success: function (data) {
                //TODO
            }
        });
    },

    updateReasonError: function(e, type, status){
        $.ajax({
            url:  linkUpdateReasonError,
            type: "POST",
            data: { complaint_id: complaint_id, type: type, status: status },
            success: function (response) {
                if(type == 'DAMAGE'){
                    if(e.is(':checked')){
                        Chat.addMessge("Đã đánh dấu khiếu nại này gây thiệt hại cho công ty", "INTERNAL", "ACTIVITY");
                    }else{
                        Chat.addMessge("Đã bỏ đánh dấu khiếu nại này gây thiệt hại cho công ty", "INTERNAL", "ACTIVITY");
                    }
                }
                if(type == 'ERROR_DIVISION_COMPANY'){
                    if(e.is(':checked')){
                        Chat.addMessge("Đã đánh dấu lỗi xuất phát từ bộ phận công ty", "INTERNAL", "ACTIVITY");
                    }else{
                        Chat.addMessge("Đã bỏ đánh dấu lỗi xuất phát từ bộ phận công ty", "INTERNAL", "ACTIVITY");
                    }

                }
                if(type == 'ERROR_PARTNER'){
                    if(e.is(':checked')){
                        Chat.addMessge("Đã đánh dấu lỗi từ phía đối tác", "INTERNAL", "ACTIVITY");
                    }else{
                        Chat.addMessge("Đã bỏ đánh dấu lỗi từ phía đối tác", "INTERNAL", "ACTIVITY");
                    }
                }
                if(type == 'ERROR_SELLER'){
                    if(e.is(':checked')){
                        Chat.addMessge("Đã đánh dấu lỗi từ phía người bán", "INTERNAL", "ACTIVITY");
                    }else{
                        Chat.addMessge("Đã bỏ đánh dấu lỗi từ phía người bán", "INTERNAL", "ACTIVITY");
                    }
                }
            },
            error: function() {}
        });
    }
};