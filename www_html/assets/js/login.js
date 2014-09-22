/**
 }
 * Login
 */

$(document).on('click','#bt-login-top',function (event) {
    event.preventDefault();
    var formLoginTop = $('#frm-login-top');
    if(validateLogin(formLoginTop)==true){
        $.ajax({
            url: url_login,
            type: 'POST',
            data: $('#frm-login-top').serialize(),
            beforeSend :function(){
                $('#bt-login-top').hide();
                $('#btnShow').show();
            },
            success: function(result) {
                if (result.type == 1) {
                    location.reload();
                    return false;
                }
                $('#btnShow').hide();
                $('#bt-login-top').show();
                $('#frm-login-top').find('.err-ajax').html(result.message).show();
                return false;
            }
        });
    }
});

$('#_bt-login').click(function (event) {
    event.preventDefault();
    var formLoginTop = $('#_frm-login');
    if(validateLogin(formLoginTop)==true){
        $.ajax({
            url: url_login,
            type: 'POST',
            data: $('#_frm-login').serialize(),
            beforeSend :function(){
                $('#_bt-login').hide();
                $('#btnShow-login').show();
            },
            success: function(result) {
                if (result.type == 1) {
                    return location.reload();
                }else{
                    $('#btnShow-login').hide();
                    $('#_bt-login').show();
                    $('#_frm-login').find('.err-ajax').html(result.message).show();
                    return false;
                }
            }
        });
    }
});

$('#frm-login-top').find('._inputPass').keyup(function (e) {
    if (e.keyCode == 13) {
        $('#bt-login-top').click();
    }
});
$('#_frm-login').find('._inputPass').keyup(function (e) {
    if (e.keyCode == 13) {
        $('#_bt-login').click();
    }
});

function validateLogin(formLoginTop){
    formLoginTop.find('.re-error').hide();
    if(formLoginTop.find('input[name=credential]').val().replace(/\s+/, '') == '')
    {
        formLoginTop.find('.err-cred').html('Bạn chưa điền tên đăng nhập').show();
        return false;
    }
    if(formLoginTop.find('input[name=credential]').val().replace(/\s+/, '').length <3)
    {
        formLoginTop.find('.err-cred').html('Tên đăng nhập từ 4 tới 15 ký tự').show();
        return false;
    }
    if(formLoginTop.find('input[name=password]').val().replace(/\s+/, '') == '')
    {
        formLoginTop.find('.err-pass').html('Bạn chưa điền mật khẩu').show();
        return false;
    }
    if(formLoginTop.find('input[name=password]').val().replace(/\s+/, '').length<6)
    {
        formLoginTop.find('.err-pass').html('Mật khẩu lớn hơn 5 ký tự').show();
        return false;
    }
    return true;
}

/**
 * forgotPassword model
 */
$(".click-resetpass").click(function(){
    $(".reset-password-block").toggleClass("block");
    $(".reset-password").toggleClass("_hide");
    $('#_email_request').focus();
});
$(".reset-password-back").click(function(e){
    e.preventDefault();
    $(".reset-password-block").removeClass("block");
});
$(".page-login-footer .click-resetpass-login").click(function(e){
    e.preventDefault();
    $(".reset-password").toggleClass("block");
    $(".page-login-footer .click-resetpass-login").toggleClass("up");
    $('#_email_request').focus();
});


//Forgot_Password
$(document).on('click','.click-resetpass-login', function(){
    $('.click-resetpass-login').toggleClass('up');
    $('._reset-password').toggleClass('_hide');
});

$('#_email_request').keyup(function (e) {
    if (e.keyCode == 13) {
        $('._request').click();
    }
});

$(document).on('click', '._request', function(){
    var form_id =$(this).attr('data-id-form');
    var form = $('#'+form_id);
    var email_request = form.find('#_email_request').val();
    var _csrf = form.find('#_csrf').val();

    if(email_request.length<1){
        form.find('._notifForgot').html('Vui lòng nhập địa chỉ email hoặc tên đăng nhập');
        form.find('._notifForgotPass').addClass('alert-error');
        return form.find('._notifForgotPass').removeClass('hidden alert-info _hide');
    }

//    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
//
//    if(!filter.test(email_request.replace(/\s+/, '')))
//    {
//        form.find('._notifForgot').html('Email nhập không hợp lệ');
//        form.find('._notifForgotPass').addClass('alert-error');
//        return form.find('._notifForgotPass').removeClass('hidden alert-info _hide');
//    }


    $.ajax({
        url:base_url + "/ForgotPassword/default",
        type: "POST",
        data:{ email_request: email_request ,_csrf: _csrf },
        beforeSend :function(){
            $('._request').addClass('hidden');
            //    $('._request_before').removeClass('hidden');
            form.find('._notifForgot').html('Đang gửi...');
            form.find('._notifForgotPass').removeClass('_hide hidden col-md-12');
            form.find('._notifForgotPass').addClass('col-md-offset-4 col-md-4 alert-error');
        },
        success:function(result) {
            form.find('._notifForgotPass').addClass('col-md-12');
            form.find('._notifForgotPass').removeClass('col-md-offset-4 col-md-4');
            if(result.type!=1){
                form.find('._notifForgot').html(result.message);
                form.find('._notifForgotPass').addClass('alert-error');
                form.find('._notifForgotPass').removeClass('hidden alert-info _hide');
            }else{
//                form.find('#_email_request').hide();
                form.find('._notifForgot').html(result.message);
                form.find('._notifForgotPass').removeClass('_hide');
            }
            var count = 8;
            var event = setInterval(function () {
                if (count < 1) {
                    $('._request').removeClass('hidden');
                    form.find('._notifForgotPass').addClass('hidden alert-info _hide');
//                    $('._request_before').addClass('hidden');
                    clearInterval(event);
                }
                count--;
            }, 1000);

        }
    });
    });

    
    $('#myModallogin').on('hide.bs.modal', function (e) {
        $(".reset-password-block").removeClass("block");
        $(".reset-password").addClass("_hide");
    });


