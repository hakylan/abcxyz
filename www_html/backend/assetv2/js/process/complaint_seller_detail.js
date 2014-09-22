var ajax_rq = null;
var chat_row_template;
var item_row_complaint;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var _sub_time = currentdate.getHours() + ':' + currentdate.getMinutes() + ' ' + day + '/' + (month<10 ? '0' : '') + month;

$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#datepicker2" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#_txt-refocus-time" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
});
$(window).on('load', function () {
//    $('.selectpicker').selectpicker({
//        'selectedText': 'cat'
//    });
});

$(document).ajaxComplete(function(){

});

function getURLParameters(url) {
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
    //format refocus_time by js
    if(complaint_seller.refocus_time != '' && complaint_seller.refocus_time != '0000-00-00 00:00:00'){
        var a = complaint_seller.refocus_time.split(' ');
        var b = a[0];
        var c = b.split('-');
        complaint_seller.refocus_time = c[2] + '-' + c[1] + '-' + c[0];
    }
//    console.log(complaint_seller.refocus_time);

    if(disable_form) { ComplaintSeller.disableForm(); }

    $('#_txt-amount-seller-refund').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });
    //height chat siderbar-left
    $('.seubox-chat').css('height', window.innerHeight + 'px');

    ComplaintSeller.getListComplaint();

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

    $('#main-body').removeClass('container');
    chat_row_template = Handlebars.compile($("#_item-chat-row").html());
    item_row_complaint = Handlebars.compile($("#_item-row-complaint").html());

    $('._input-chat').keypress(function(e){
        if(e.keyCode == 13){
            var e = $(e.currentTarget);
            Chat.addMessge(e.val(), e.data('type'), "CHAT");
            e.val('');
        }
    });

    //Tiếp nhận
    $('#_btn-process').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.html(e.data('process'));
        e.addClass('clicked');
        $('._btn-public').hide();
        e.show();
        e.prop('disabled', true);

        ComplaintSeller.UpdateStatusProcess(e);
    });

    $('#_txt-amount-seller-refund').keypress(function(e){
        if(e.keyCode == 13){
            $('#_btn-success').click();
        }
    });

    //Thành công
    $('#_btn-success').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.html(e.data('process'));
        e.addClass('clicked');
        $('._btn-public').hide();
        var $btn_show_popup_success = $('#_btn-show-popup-success');
        $btn_show_popup_success.html($btn_show_popup_success.data('message'));
        $btn_show_popup_success.show();
        $btn_show_popup_success.prop('disabled', true);

        var amount_seller_refund = $('#_txt-amount-seller-refund').autoNumeric('get');
        if(amount_seller_refund == ""){
            amount_seller_refund = 0;
        }

        ComplaintSeller.updateStatusSuccess(e, amount_seller_refund, e.data('status'));
    });
    //Thất bại
    $('#_btn-reject').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.html(e.data('process'));
        e.addClass('clicked');
        $('._btn-public').hide();
        e.show();
        e.prop('disabled', true);

        ComplaintSeller.updateStatusFailure(e, e.data('status'));
    });

    //Thay đổi cấp khiếu nại
    $('#_sel-level').change(function(e){
        var level = $(e.currentTarget).val();
        var level_new = $('#_sel-level option[value="' + level + '"]').text();
        var level_old = $('#_sel-level option[value="' + complaint_seller.level + '"]').text();
        if(level != complaint_seller.level){
            ComplaintSeller.updateInfoComplaintSeller();
            Chat.addMessge('thay đổi mức độ khiếu nại từ "' + level_old + '" thành "' + level_new + '"', "INTERNAL", "ACTIVITY");
            complaint_seller.level = level;
        }
    });
    //Thay đổi lí do
    $('#_sel-reason').change(function(e){
        var reason = $(e.currentTarget).val();
        var reason_new = $('#_sel-reason option[value="' + reason + '"]').text();
        var reason_old = $('#_sel-reason option[value="' + complaint_seller.reason + '"]').text();
        if(reason != complaint_seller.reason){
            ComplaintSeller.updateInfoComplaintSeller();
            Chat.addMessge('thay đổi lý do khiếu nại từ "' + reason_old + '" thành "' + reason_new + '"', "INTERNAL", "ACTIVITY");
            complaint_seller.reason = reason;
        }
    });
    //Thay đổi hạn xử lý
    $('#_txt-refocus-time').change(function(e){
        var check = true;
        var refocus_time = $(e.currentTarget).val();

        //Kiểm tra định dạng ngày tháng xem có hợp lý hay không
        if(!ComplaintSeller.isDate(refocus_time)){
            Common.BSAlert('Định dạng ngày tháng không hợp lệ!');
            $('#_txt-refocus-time').focus();
            check = false;
        }
        if(!check){
            return false;
        }

        if(refocus_time != complaint_seller.refocus_time){
            ComplaintSeller.updateInfoComplaintSeller();
            Chat.addMessge("thay đổi hạn xử lý từ " + complaint_seller.refocus_time + " thành " + refocus_time, "INTERNAL", "ACTIVITY");
            complaint_seller.refocus_time = refocus_time;
        }

    });
    //Thay đổi ghi chú
    $('#_textarea-description').focusout(function(e){
        var description = $(e.currentTarget).val();
        if(description != complaint_seller.description){
            ComplaintSeller.updateInfoComplaintSeller();
            Chat.addMessge('thay đổi ghi chú từ "' + complaint_seller.description + '" thành "' + description + '"', "INTERNAL", "ACTIVITY");
            complaint_seller.description = description;
        }
    });

    Chat.getList();
});

var Chat = {
    /* Hàm lấy ra danh sách comment */
    getList: function(){
        $.ajax({
            url: linkGetListComments,
            type: 'POST',
            data: {
                complaint_seller_id: complaint_seller_id,
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
                    ComplaintSeller.filter_comment();
                });

                //tooltips
                $("* [rel='tooltipbottom']").tooltip({
                    html: true,
                    placement: 'bottom'
                });
            }
        });
    },

    /* data input is array (first_name, username, sub_time, img_path, is_chat, is_log, is_activity, is_internal, user_id) */
    prependComment: function(document, data){
        document.prepend(chat_row_template(data));

        $('._item-view-comment:first').find("* [rel='tooltipbottom']").tooltip({
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

        if(message.length == 0){
            return false;
        }

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
                url: linkAddComplaintSellerComment,
                type : "POST",
                data: {
                    order_id: order_id,
                    complaint_seller_id: complaint_seller_id,
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

var ComplaintSeller = {

    filter_comment:function(type) {
        var search_data = $('#_filter-comment-by-type').serialize();
        if(type == null){
            var pageUrl = LinkComplaintSellerDetailUrl + '?' + search_data;
            ComplaintSeller.push_state(pageUrl);
        }
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    updateInfoComplaintSeller: function(){
        var level = $('#_sel-level').val();
        var refocus_time = $('#_txt-refocus-time').val();
        var reason = $('#_sel-reason').val();
        var description = $('#_textarea-description').val();

        $.ajax({
            url: linkUpdateInfoComplaintSeller,
            type : "POST",
            data: {
                complaint_seller_id: complaint_seller_id,
                reason: reason,
                level: level,
                refocus_time: refocus_time,
                description: description
            },
            success: function (response) {
                //TODO
            }
        });
    },

    isDate: function(txtDate){
        var currVal = txtDate;
        if(currVal == '')
            return false;

        //Declare Regex
        var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
        var dtArray = currVal.match(rxDatePattern); // is format OK?

        if (dtArray == null)
            return false;

//        //Checks for mm/dd/yyyy format.
//        dtMonth = dtArray[1];
//        dtDay= dtArray[3];
//        dtYear = dtArray[5];

        //Checks for dd/mm/yyyy format.
        dtMonth = dtArray[3];
        dtDay= dtArray[1];
        dtYear = dtArray[5];

//        console.log('dtMonth: ' + dtMonth);
//        console.log('dtDay: ' + dtDay);
//        console.log('dtYear: ' + dtYear);

        if (dtMonth < 1 || dtMonth > 12)
            return false;
        else if (dtDay < 1 || dtDay> 31)
            return false;
        else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
            return false;
        else if (dtMonth == 2)
        {
            var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
            if (dtDay> 29 || (dtDay ==29 && !isleap))
                return false;
        }
        return true;
    },

    disableForm: function(){
        $('#_sel-level').prop('disabled', true);
        $('#_sel-reason').prop('disabled', true);
        $('#_txt-refocus-time').prop('disabled', true);
        $('#_textarea-description').prop('disabled', true);
    },

    createMessageLog: function(level, refocus_time, reason, description){
        var msg = '';
        if(reason != complaint_seller.reason){
            var reason_new = $('#_sel-reason option[value="' + reason + '"]').text();
            var reason_old = $('#_sel-reason option[value="' + complaint_seller.reason + '"]').text();
            msg += 'Thay đổi lý do từ "' + reason_old + '" thành "' + reason_new + '"<br />';
        }
        if(level != complaint_seller.level){
            var level_new = $('#_sel-level option[value="' + level + '"]').text();
            var level_old = $('#_sel-level option[value="' + complaint_seller.level + '"]').text();
            msg += 'Thay đổi mức độ khiếu nại từ "' + level_old + '" thành "' + level_new + '"<br />';
        }

        if(refocus_time != ''){
            var arrRefocusTime = refocus_time.split('-');
            var tmp_refocus_time = arrRefocusTime[2] + '-' + arrRefocusTime[1] + '-' + arrRefocusTime[0] + ' 00:00:00';

            if(tmp_refocus_time != complaint_seller.refocus_time){
                msg += 'Thay đổi hạn xử lý từ "' + complaint_seller.refocus_time + '" thành "' + tmp_refocus_time + '"<br />';
            }
        }
        if(description != '' && description != complaint_seller.description){
            msg += 'Thay đổi ghi chú từ "' + complaint_seller.description + '" thành "' + description + '"<br />';
        }

        if(msg != ''){
            Chat.addMessge("Cập nhật thay đổi một số thay đổi như sau: <br />" + msg, "INTERNAL", "ACTIVITY");
//            Chat.addMessge("Cập nhật thay đổi một số thay đổi như sau: <br />" + msg, "EXTERNAL", "ACTIVITY");
        }
    },

    UpdateStatusProcess: function(e){
        var level = $('#_sel-level').val();
        var refocus_time = $('#_txt-refocus-time').val();
        var reason = $('#_sel-reason').val();
        var description = $('#_textarea-description').val();
        var status = e.data('status');
//        console.log('ajax');
        $.ajax({
            url: LinkUpdateStatusProcess,
            type : "POST",
            data: {
                complaint_seller_id: complaint_seller_id,
                order_id: order_id,
                status: status
//                reason: reason,
//                level: level,
//                refocus_time: refocus_time,
//                description: description
            },
            success: function (response) {
                if(response.type == 1){
                    e.html(e.data('message'));

//                    ComplaintSeller.createMessageLog(level, refocus_time, reason, description);

                    Chat.addMessge("Chuyển trạng thái khiếu nại người bán sang đã tiếp nhận", "INTERNAL", "ACTIVITY");
//                    Chat.addMessge("Khiếu nại người bán đã được tiếp nhận", "EXTERNAL", "ACTIVITY");

                    complaint_seller.status = response.status;

//                    complaint_seller.reason = reason;
//                    complaint_seller.level = level;
//                    complaint_seller.refocus_time = refocus_time;
//                    complaint_seller.description = description;
                }else{
//                    Common.BSAlert(response.message);
//                    return false;
                }
            }
        });
    },

    updateStatusSuccess: function(e, amount_seller_refund, status){
        var level = $('#_sel-level').val();
        var refocus_time = $('#_txt-refocus-time').val();
        var reason = $('#_sel-reason').val();
        var description = $('#_textarea-description').val();

        $.ajax({
            url: LinkUpdateStatusSuccess,
            type : "POST",
            data: {
                complaint_seller_id: complaint_seller_id,
                order_id: order_id,
                amount_seller_refund: amount_seller_refund,
                status: status

//                level: level,
//                refocus_time: refocus_time,
//                reason: reason,
//                description: description
            },
            success: function (response) {
                if(response.type == 1){
                    e.html(e.data('message'));

//                    ComplaintSeller.createMessageLog(level, refocus_time, reason, description);

                    Chat.addMessge("Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " + amount_seller_refund + " NDT", "INTERNAL", "ACTIVITY");
//                    Chat.addMessge("Khiếu nại người bán thành công", "EXTERNAL", "ACTIVITY");

//                    complaint_seller.reason = reason;
//                    complaint_seller.level = level;
//                    complaint_seller.refocus_time = refocus_time;
//                    complaint_seller.description = description;

                    complaint_seller.status = response.status;

                    $('#popupnewvc').modal('hide');

                    ComplaintSeller.disableForm();
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            }
        });
    },

    updateStatusFailure: function(e, status){
        var level = $('#_sel-level').val();
        var refocus_time = $('#_txt-refocus-time').val();
        var reason = $('#_sel-reason').val();
        var description = $('#_textarea-description').val();

        $.ajax({
            url: LinkUpdateStatusFailure,
            type : "POST",
            data: {
                complaint_seller_id: complaint_seller_id,
                order_id: order_id,
                status: status

//                level: level,
//                refocus_time: refocus_time,
//                reason: reason,
//                description: description
            },
            success: function (response) {
                if(response.type == 1){
                    e.html(e.data('message'));

//                    ComplaintSeller.createMessageLog(level, refocus_time, reason, description);

                    Chat.addMessge("Chuyển trạng thái khiếu nại người bán sang thất bại", "INTERNAL", "ACTIVITY");
//                    Chat.addMessge("Khiếu nại người bán thất bại", "EXTERNAL", "ACTIVITY");

//                    complaint_seller.reason = reason;
//                    complaint_seller.level = level;
//                    complaint_seller.refocus_time = refocus_time;
//                    complaint_seller.description = description;

                    complaint_seller.status = response.status;

                    ComplaintSeller.disableForm();
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            }
        });
    },

    getListComplaint: function(search_data){
        if(search_data == undefined){
//            search_data = { order_id: 0 };
            search_data = { order_id: order_id };
        }
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

                        $('#_show-paging').html(ComplaintSeller.createPaging(response.total_page, response.current_page));

                        //paging
                        $('._paging').click(function(e){
                            console.log('paging');
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var page = e.data('page');
                                $('#_current-page').val(page);
                                e.addClass('clicked');
                                ComplaintSeller.complaintFilter();
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
                                ComplaintSeller.complaintFilter();
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
                                ComplaintSeller.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                            }
                        });
                    }else{
                        //Nếu không có bản ghi nào thì ẩn cả khung KNDV
                        $('#_show-table-complaints').hide();
                    }
                }else{
                    Common.BSAlert(response.message);
                    return false;
                }
            }
        });
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

    complaintFilter : function(type){
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = LinkListBackendComplaintUrl+'?'+search_data;
            ComplaintSeller.push_state(pageUrl);
        }
        ComplaintSeller.getListComplaint(search_data);
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    }
};