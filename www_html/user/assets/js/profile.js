$(document).on('click','.btnSubmitEdit',function(){
    var formUpdateUser = $('#frm-update-user');
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (formUpdateUser.find('#first_name').val()=='') {
        formUpdateUser.find('#error-fullname').html('Tên không được rỗng!');
        Global.sAlert('Tên không được rỗng!');
        formUpdateUser.scrollTo('#error-fullname');
        return false;
    }
    if (formUpdateUser.find('#last_name').val()=='') {
        formUpdateUser.find('#error-fullname').html('Họ không được rỗng!');
        Global.sAlert('Họ không được rỗng!');
        return false;
    }
    formUpdateUser.find('#error-fullname').html('');

    if (formUpdateUser.find('#email').val()=='') {
        formUpdateUser.find('#_error-email').html('Email không được rỗng!');
        Global.sAlert('Email không được rỗng!');
        return false;
    }

    if (!filter.test(formUpdateUser.find('#email').val())) {
        formUpdateUser.find('#_error-email').html('Email không hợp lệ!');
        Global.sAlert('Email không hợp lệ!');
        return false;
    }
    formUpdateUser.find('#_error-email').html('');

    if(formUpdateUser.find('#tt_id').val()>0 || formUpdateUser.find('#qh_id').val()>0 || formUpdateUser.find('#detail_address').val()!=''){
        if(formUpdateUser.find('#tt_id').val()<0 || formUpdateUser.find('#qh_id').val()<0 || formUpdateUser.find('#detail_address').val()==''){
            formUpdateUser.find('#error-address').html('Bạn chưa nhập đầy đủ thông tin địa chỉ!');
            Global.sAlert('Bạn chưa nhập đầy đủ thông tin địa chỉ!');
        return false;
        }
    }
    formUpdateUser.find('#error-address').html('');


    $.ajax({
        url:  url_updateUser,
        type: "POST",
        data: $('#frm-update-user').serialize(),
        beforeSend :function(){
            $('.btnSubmitEdit').find('span').html('Đang xử lý...');
        },
        success: function (data) {
//            $('.btnSubmitEdit').find('span').html(data.message);
            $("#"+data.element).html(data.message);
            if(data.type == 0){
                    $("#"+data.element).html(data.message);
//                Global.sAlert(data.message);
            }else{
                $('.btnSubmitEdit').find('span').html('Đã cập nhật');
                $('._error').html('');
            }
            var count = 2;
            var event = setInterval(function () {
                if (count < 1) {
                    $('.btnSubmitEdit').find('span').html('Lưu');
                    clearInterval(event);
                }
                count--;
            }, 1000);
        }
    });


});


$(document).on('click','._selectState',function(){
    var id = $(this).val();
    $.ajax({
        url:base_url + "/register/choose_district",
        data:{ id:id },
        success:function(result) {
            var data = $.parseJSON(result);
            $('._ajaxDistrict').html(data);
            $('._ajaxDistrict').removeClass('hidden');
            $('._afterDistrict').addClass('hidden');
        }
    });
});



//Update password profile
$(document).on('click', '#btnSubmit',function(e){
    e.preventDefault();
    if($('#pwd').val() == '') {
        $('.red-normal').html('');
        $('#error-pwd').html('Bạn chưa điền mật khẩu hiện tại!');
        return false;
    }
    if($('#newpwd').val() == '') {
        $('.red-normal').html('');
        $('#error-newpwd').html('Bạn chưa điền mật khẩu mới!');
        return false;
    }
    if($('#re_newpwd').val() == '') {
        $('.red-normal').html('');
        $('#error-renewpwd').html('Xác nhận mật khẩu mới không đúng!');
        return false;
    }
    if($('#newpwd').val() != $('#re_newpwd').val()) {
        $('.red-normal').html('');
        $('#error-renewpwd').html('Mật khẩu không khớp!');
        return false;
    }
    $('.red-normal').html('');
    changePass();
});

function changePass() {
    if(1==1) {
        $.ajax({
            url: user_url + '/user/UpdatePassword',
            type: "POST",
            data: $('#frm-change-pass').serialize(),
            beforeSend :function(){
                $("#btnSubmit").addClass('hidden');
                $("#btnWaiting").removeClass('hidden');
            },
            success: function(result) {
                $("#btnSubmit").html('Xác nhận');
                if(result.type==1){
                    $("#btnSubmit").removeClass('hidden');
                    $("#btnWaiting").addClass('hidden');
                    Global.sAlert(result.message);
                    var count = 4;
                    var event = setInterval(function () {
                        if (count < 1) {
                            window.location.href = base_url+'/login/logout?r='+base_url+'/login/default';
                            clearInterval(event);
                        }
                        count--;
                    }, 1000);
                }else{
                    $("#btnSubmit").removeClass('hidden');
                    $("#btnWaiting").addClass('hidden');
                    $('#'+result.element).html(result.message);
                }
            }
        });
    }
    return false;
}

$(document).on('click','#edit_noActive, #edit_Active',function(){
    $(this).parent().parent().toggleClass('_img-edit');
    var element = $(this).attr('data-edit');
    $('.edit_noActive').toggleClass('hide');
    $('.edit_Active').toggleClass('hide');
    var text = $(this).html();
    if(text=='Sửa'){
        $(this).html('Hủy');
    }
    if(text=='Hủy'){
        $('#'+element).val($(this).attr('data-value'));
        $(this).html('Sửa');
    }
});


$(document).on('click','#disconectFacebook',function(){
    $.ajax({
        url:user_url + "/user/disconect_facebook",
        type: "POST",
        success:function(result) {
            if(result.type == 0){
                Global.sAlert(result.message);
            }else{
                $('._cd_facebook').html(result.element);
            }
        }
    });
});


//Delete mobile number
$(document).on('click','#_delMobile', function(){
    var id = $(this).attr('data-mobile');
    var data_modal = $(this).attr('data-modal');
    $.ajax({
        url:user_url + "/user/delete_mobile",
        type:"POST",
        data:{ id:id },
        success:function(result) {
            $('#'+data_modal).modal('hide');
            $('#_notif').html(result.message);
            $('#myModalNotif').modal();
            if(result.type==1){
                $('#id_'+id).hide();
            }
        }
    });
});

$(document).on('click','#_activeByEmail',function(e){
    $('#_activeByEmail').html('Đang gửi mail kích hoạt...');
    e.preventDefault();
    var id = $(this).attr('data-id');
    $.ajax({
        url:user_url + "/user/ClickVerifyEmail",
        data:{ id:id },
        type:"POST",
        beforeSend :function(){
            $('._notif').removeClass('_hide');
            $('._notif').html('Đang gửi mail kích hoạt tài khoản...');
            $(this).removeClass('alert-error');
            $(this).addClass('alert-success');
            $('._hideEmail').addClass('_hide');
            $('._hideEmailTow').addClass('hidden');
        },
        success:function(result) {
            if(result.type==1){
                $('._notif').addClass('hidden');
                $('._returnVerify').removeClass('hidden');
                $('._returnVerify').removeClass('_hide');
                $('._returnVerify').addClass('alert-success');
                $('._returnVerify').removeClass('alert-error');
                $('._returnVerify').html(result.message);
            }else{
                $('._returnVerify').html('Lỗi, không thể gửi mail kích hoạt tài khoản');
            }
        }
    });
});

$(document).on('click','._setByEmail',function(e){
    e.preventDefault();
    var email = $('#_inputEmail').val();
    $.ajax({
        url:user_url + "/user/SetEmailNew",
        data:{ email:email },
        type:"POST",
        beforeSend :function(){
            $('._setByEmail_').removeClass('_hide');
            $('._setByEmail').addClass('_hide');
        },
        success:function(result) {
            if(result.type!=1){
                $('._setByEmail_').addClass('_hide');
                $('._setByEmail').removeClass('_hide');
                $('._setByEmail').html('Thử lại');
                $('._returnNewMail').html(result.message);
            }else{
                $('._setByEmail_').removeClass('_hide');
                $('._setByEmail').addClass('_hide');
                Global.sAlert(result.message);
            }
        }
    });
});

$(document).on('click','._edit-profile',function(){
    $(this).parent().parent().toggleClass('_img-edit');
    var element = $(this).attr('data-edit');
    $('.'+element).toggleClass('hide');
    var text = $(this).html();
    if(text=='Sửa'){
        if(element=='gender'){
            $('._male').val(1);
        }
        $(this).html('Hủy');
    }
    if(text=='Hủy'){
        if(element=='gender'){
            $( "input:checked" ).val($(this).attr('data-value'));
        }else{
            $('#'+element).val($(this).attr('data-value'));
        }
        if(element=='full_name'){
            $('#first_name').val($(this).attr('data-first'));
            $('#last_name').val($(this).attr('data-last'));
        }
        $(this).html('Sửa');
    }
});

$(document).on('click','._edit-disabled',function(){
    $(this).addClass('hide');
    $(this).parent().find('._no-edit-disabled').removeClass('hide');
    var element = $(this).attr('data-edit');
    $('#'+element).removeAttr('disabled');
    $('.'+element).removeAttr('disabled');
    $('.'+element).removeClass('hide');
    if(element=='birthday'){
        $('span.'+element).toggleClass('hide');
    }
    $('.'+element).parent().parent().find('._img-edit').toggleClass('_img-edit');
});

$(document).on('click','._no-edit-disabled',function(){
    $(this).addClass('hide');
    $(this).parent().find('._edit-disabled').removeClass('hide');
    var element = $(this).attr('data-edit');
    $('.detail_address address').attr('disabled')
    $('.'+element).attr('disabled','');
    $('#'+element).attr('disabled','');
    $('.'+element).parent().find('button').addClass('hide');
    $('.'+element).parent().parent().find('._img').toggleClass('_img-edit');
    if(element=='birthday'){
        var birth = $(this).attr('data-value');
        $('span.'+element).toggleClass('hide');
        $('input.'+element).toggleClass('hide');
        $('input.'+element).val(birth);
    }
    if(element=='cmtnd'){
        $('#num_cmtnd').val($(this).attr('data-num-cmnd'));
        $('#datepicker.cmtnd').val($(this).attr('data-date-cmnd'));
    }
});



