/**
 * Created by hosi on 5/28/14.
 */
var product_template, comment_template, file_template, pro_tpl;
var page_size = 3;
var scope = 'EXTERNAL';
var total_file = 0;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var datetime = year + '-' + (month<10 ? '0' : '') + month + '-' + day + ' ' + currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds();
var current_short_time = currentdate.getHours() + ':' + currentdate.getMinutes() + ' ' + day + '/' + (month<10 ? '0' : '') + month;

$(document).ajaxComplete(function(){

});

$(document).ready(function(){
    //autoNumeric
    $('#_txt-customer-amount-reimbursement').autoNumeric({ aPad: false, mDec: 9 });

    product_template = Handlebars.compile($("#_info-product").html());
    comment_template = Handlebars.compile($("#_comment-view").html());
    file_template = Handlebars.compile($("#_item-file-view").html());

    Complaint.goStep(step);

    Complaint.init();
//    Complaint.infoComplaint(complaint_id);
    Complaint.files();
//    Complaint.complaints();

    $('.modal').on('shown.bs.modal', function () {
        $('.focusnew').focus();
    });

    $('#_btn-customer-amount-reimbursement').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('success')){
            return false;
        }

        var input = $('#_txt-customer-amount-reimbursement');
        var customer_amount_reimbursement = input.autoNumeric('get');
        if(customer_amount_reimbursement == '' || customer_amount_reimbursement % 1 != 0){
            alert('Định dạng không hợp lệ!');
            $('#_txt-customer-amount-reimbursement').focus();
            return false;
        }
        e.addClass('success');

        $('#_panel-update-customer-amount-reimbursement').hide();
        $('#_show-customer-amount-reimbursement').show();
        $('#_show-customer-amount-reimbursement span').html('<span class="bold-blue">' + input.val() + '<sup>đ</sup></span>');

        Complaint.updateCustomerAmountReimbursement(e, customer_amount_reimbursement);
    });

    $('#_txt-customer-amount-reimbursement').keypress(function(e){
        if(e.keyCode == 13){
            $('#_btn-customer-amount-reimbursement').click();
        }
    });

    $('._btn-action-customer').click(function(e){
        var e = $(e.currentTarget);
        var $this = $('._btn-action-customer[data-type="' + e.data('type') + '"]');

        if(e.hasClass('clicked')){
            return false;
        }

        e.addClass('clicked');

        $('._btn-action-customer').hide();
        $this.show();
        $this.html(e.data('message-doing'));
        Complaint.updateStatus(e, e.data('type'));
    });

    $('#_btn-load-comment-more').click(function(e){
        var page = $(e.currentTarget).attr('page');
        Complaint.comments(page);
    });

    $('#_btn_product_not_received').click(function(){
        $('#myModalfinish').modal('show');
        $('#_reasons').removeClass('slow');
    });

    $('#_txt-quantity-received').keypress(function(e){
        console.log('keypress');
        if(e.keyCode == 13){
            var e = $(e.currentTarget);
            $('#_btn-confirm-quantity-received').click();
        }
    });

    $('#_btn-confirm-quantity-received').click(function(e){
        var e = $(e.currentTarget);
        var error = '';
        var order_quantity = $('#_quantity-order').val();
        var quantity_received = $('#_txt-quantity-received').val();
        if(quantity_received == '' || quantity_received % 1 != 0){
            error += '<p class="text-left">Số lượng không hợp lệ!</p>';
        }else if (quantity_received >= order_quantity){
            error += '<p class="text-left">Số lượng hàng bạn nhận được không được lớn hơn hoặc bằng số lượng hàng bạn mua</p>';
        }

        if(error != ''){
            $('#_message-alert-error').html(error);
            return false;
        }else{
            $('#_message-alert-error').html('');
        }

        if(e.hasClass('clicked')){
            return false;
        }
        e.addClass('clicked');

        $('#myModalfinish').modal('hide');
        $('#_quantity').html(quantity_received);

        //Chuyển đến bước 2
        Complaint.goStep(2);

        Complaint.addComplaint({
            type: 'PRODUCT_NOT_RECEIVED',
            quantity: quantity_received,
            item_id: order_item_id,
            order_id: order_id
        });
    });

    $('#_btn_defective_product').click(function(){
        $('#_reasons').addClass('slow');
    });

    $('input[name="_reasons"]').change(function(){
        if($('input[name="_reasons"]:checked').length > 0){
            $('#_btn-next-step').removeClass('disabled');
        }else{
            $('#_btn-next-step').addClass('disabled');
        }
    });

    $('#_btn-next-step').click(function(e){
        var e = $(e.currentTarget);
        if(e.hasClass('clicked')){
            return false;
        }
        e.addClass('clicked');

        //Chuyển đến bước 3
        Complaint.goStep(3);

        var reasons = [];
        $('input[name="_reasons"]').each(function(i){
            if($(this).is(':checked')){
                reasons[i] = $(this).val();
            }
        });

        Complaint.addComplaint({
            type: 'PRODUCT_ERROR',
            reasons: reasons,
            item_id: order_item_id,
            order_id: order_id
        });
    });

    $('#_txt-message').keypress(function(e){
        if(e.keyCode == 13){
            var e = $(e.currentTarget);
            if(e.val() == ''){
                return false;
            }
            $('#_btn-send-message').click();
        }
    });

    $('#_btn-send-message').click(function(){
        var message = $('#_txt-message').val();
        if(message == ''){
            return false;
        }
        Complaint.addMessage(message, 'CHAT');
    });

    //upload Image
    $('#photoimg').change(function(){
        $("#imageform").ajaxForm({
            target: '#_preview-image',
            data: { order_id: order_id, item_id: order_item_id },
            complete: function(response){
                var data = $.parseJSON(response.responseText);
                if(data.flag == false){
                    alert(data.message);
                    return false;
                }

                //append image
                $('#_list-file-upload').append(file_template(data));
                /* fix width and height image */
                var widthitem = $(".item-img .module-float").innerWidth();
                $('.item-img .module-float').css('height', widthitem + 'px');

                total_file++;
                Complaint.showHideUploadFile();

                //delete file image
                $('._remove-image:last').click(function(e){
                    if(!confirm('Bạn có chắc muốn xóa?')){
                        return false;
                    }
                    var e = $(e.currentTarget);
                    var file_id = e.data('file-id');
                    e.parents('._item-img').remove();
                    Complaint.deleteFile(file_id);
                    total_file--;
                    Complaint.showHideUploadFile();
                });

            }
        }).submit();
    });

    $('#_edit-customer-amount-reimbursement').click(function(e){
        $('#_show-customer-amount-reimbursement').hide();
        $('#_panel-update-customer-amount-reimbursement').show();
    });

});


var Complaint = {
    init: function(){
        $.ajax({
            url:  linkgetOneOrderItem,
            type: "GET",
            data: { order_item_id: order_item_id },
            success: function (response) {
                pro_tpl = product_template(response);
                $('#_product-view').html(pro_tpl);
            },
            error: function() {}
        });
    },

    updateCustomerAmountReimbursement: function(e, customer_amount_reimbursement){
        $.ajax({
            url:  linkUpdateCustomerAmountReimbursement,
            type: "POST",
            data: { customer_amount_reimbursement: customer_amount_reimbursement, complaint_id: complaint_id },
            success: function (response) {
                e.removeClass('success');
                if(customer_amount_reimbursement != 0 && customer_amount_reimbursement != complaint.customer_amount_reimbursement){
                    Complaint.addMessage('Yêu cầu số tiền bồi thường :' + numeral(customer_amount_reimbursement).format('0,0') + 'đ', 'ACTIVITY');
                }
                complaint.customer_amount_reimbursement = customer_amount_reimbursement;
            },
            error: function() {}
        });
    },

    showHideUploadFile: function(){
        if(total_file  == 6){
            $('#_new-upload').hide();
        }
        if(total_file < 6){
            $('#_new-upload').show();
        }
    },

    deleteFile: function(file_id){
        $.ajax({
            url:  linkDeleteFileComplaint,
            type: "POST",
            data: { file_id: file_id },
            success: function (response) {
                //TODO
            },
            error: function() {}
        });
    },

    files: function(){
        $.ajax({
            url:  linkGetListFileComplaint,
            type: "GET",
            data: { order_id: order_id, item_id: order_item_id },
            success: function (response) {

                if(response.items.length > 0){
                    $.each(response.items, function(i, item){
                        $('#_list-file-upload').append(file_template(item));
                    });
                }

                //show hide upload file
                total_file = response.items.length;
                Complaint.showHideUploadFile();

                /* begin js design */
                var widthitem = $(".item-img .module-float").innerWidth();
                $('.item-img .module-float').css('height', widthitem + 'px');
                /* end js design */

                //delete file image
                $('._remove-image').click(function(e){
                    if(!confirm('Bạn có chắc muốn xóa?')){
                        return false;
                    }
                    var e = $(e.currentTarget);
                    var file_id = e.data('file-id');
                    e.parents('._item-img').remove();
                    Complaint.deleteFile(file_id);
                    total_file--;
                    Complaint.showHideUploadFile();
                });
            },
            error: function() {}
        });
    },

    addComplaint: function(data){
        $.ajax({
            url:  linkAddComplaint,
            type: "POST",
            data: data,
            success: function (response) {
                if(response.type == 0){
                    alert(response.message);
                }
                complaint_id = response.complaint_id;
            },
            error: function() {}
        });
    },

    goStep: function(t){
        $('._step').hide();
        $('._step' + t).show();
        if(t == 2 || t == 3){
            Complaint.comments(1);
        }
    },

    getListReasons: function(){
        $.ajax({
            url:  linkgetComplaintReasons,
            type: "GET",
            success: function (response) {
                //TODO
            },
            error: function() {}
        });
    },

    addMessage: function(message, context){
        $('#_txt-message').val('');
        var html = comment_template({
            user_avatar: current_user_avatar,
            username: current_username,
            short_time: current_short_time,
            message: message,
            is_chat: context == 'CHAT' ? true : false,
            is_activity: context == 'ACTIVITY' ? true : false,
            is_log: context == 'LOG' ? true : false
        });
//        console.log(html);
        $('#_comments').prepend(html);
        $.ajax({
            url: linkAddComplaintItemComment,
            type : "POST",
            data: {
                complaint_id: complaint_id,
                order_id: order_id,
                item_id: order_item_id,
                message: message,
                type: 'EXTERNAL',
                context: context
            },
            success: function (data) {
                //TODO
            }
        })
    },

    comments: function(page){
        $.ajax({
            url: linkMoreComplaintItemComment,
            type: 'POST',
            data: {
                complaint_id: complaint_id,
                page: page,
                page_size: page_size,
                scope: scope,
                order_id: order_id,
                item_id: order_item_id
            },
            success: function (response) {
                if(response.type != 1) {
                    return false;
                }
                if(response.info.length > 0){
                    $.each(response.info, function(idx, item){
                        $('#_comments').append(comment_template(item));
                    });
                }
                var load_more = $('#_btn-load-comment-more');
                if (response.page_next > response.pages) {
                    load_more.hide();
                } else {
                    load_more.show();
                    load_more.attr('page', response.page_next);
                }

                //add border
                $('._comment-ct-view').removeClass('no-border');
                $('._comment-ct-view:last').addClass('no-border');
            }
        });
    },

    infoComplaint: function(id){
        $.ajax({
            url: linkGetInfoComplaint,
            type: 'GET',
            data: { id: id },
            success: function (response) {

            }
        });
    },

    complaints: function(){
        $.ajax({
            url: linkGetListComplaints,
            type: 'GET',
            data: { page: 1 },
            success: function (response) {
                //TODO
            }
        });
    },

    //Cập nhật trạng thái khiếu nại
    updateStatus: function(e, type){
        $.ajax({
            url:  linkUpdateStatusComplaint,
            type: "POST",
            data: { complaint_id: complaint_id, type: type },
            success: function (response) {
                e.html(e.data('message-success'));
                if(type == "ACCEPTED"){//Đồng ý
                    Complaint.addMessage('Chấp nhận số tiền bồi hoàn là : ' + numeral(response.recipient_amount_reimbursement).format('0,0') + ' đ', 'ACTIVITY');
                }
                if(type == "REFUSED"){//Từ chối
                    Complaint.addMessage('Từ chối số tiền bồi hoàn là : ' + numeral(response.recipient_amount_reimbursement).format('0,0') + ' đ', 'ACTIVITY');
                }
            },
            error: function() {}
        });
    }
};