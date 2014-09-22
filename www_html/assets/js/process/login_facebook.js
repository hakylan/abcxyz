/**
 * Created by Admin on 1/3/14.
 */
const ErrorPassEmpty = 0,
    ErrorDb = -1,
    ErrorNotUsername = -3,
    ErrorNotEmail = -5,
    RegisterSuccess = 1,
    NotLogin = -4,
    NotSessionFacebook = -2,
    RegisterFacebook = 1,
    ConfirmPass = 2,
    appId = 1449847118569980;// 1449847118569980;// //328868587191915

$(document).ready(function(){
    $(document).on('click','#btnConfirmPassFacebook',function(){
        $('div.loading-face').show();
        var password = $('#passwordConfirm').val();
        UserFacebook.confirmPassword(password);
    });
    $(document).on('click','#btnRegisterFacebook',function(){
        UserFacebook.regisFacebook();
    })
});

window.fbAsyncInit = function() {
    FB.init({
        appId      : appId,
        status     : true, // check login status
        xfbml      : true  // parse XFBML
    });


};

function fb_login(is_select_avatar){
    var cnFbProfile = $('._connect_fb_profile');
    if(cnFbProfile.length > 0){
        cnFbProfile.addClass('disable');
    }
    $('div.loading-face').show();
    FB.login(function(response) {
        $('div.loading-face').hide();
        if (response.authResponse) {

            $('div.loading-face').hide();
//            console.log('Welcome!  Fetching your information.... ');
            //console.log(response); // dump complete info
            access_token = response.authResponse.accessToken; //get access token
            user_id = response.authResponse.userID; //get FB UID

            FB.api('/me', function(profile_fb) {

                UserFacebook.loginFacebook(profile_fb,is_select_avatar);
            });

        } else {
            $('div.loading-face').hide();
            //user hit cancel button
            console.log('User cancelled login or did not fully authorize.');

        }
    }, {
        scope: 'publish_stream,email'
    });
}



var UserFacebook = {
    regisFacebook : function(){
        var username = $('#usernameRegis').val();
        var first_name = $('#first_nameRegis').val();
        var last_name = $('#last_nameRegis').val();
        var emailReg = /^[\w._-]+@[\w.-]+\.[A-Za-z]{2,4}$/;
        var email = $('#emailRegis').val();
        var password = $('#passwordRegis').val();
        var validateUsername = /^[a-zA-Z0-9._]+/;
        $('._error').slideUp();


        if(!validateUsername.test(username)){
            $('._errorUserName').text('Sử dụng các ký tự (a-z,0-9), ký tự gạch dưới và từ 3 đến 15 ký tự');
            $('._errorUserName').slideDown();
            $('#usernameRegis').focus();
            return;
        }else if(username.length < 3 || username.length > 15){
            $('._errorUserName').text('Tài khoản giới hạn từ 3 đến 15 kí tự.');
            $('._errorUserName').slideDown();
            $('#username').focus();
            return;
        }

        if(first_name == ''){
            $('._errorFirstName').text('Họ không được bỏ trống');
            $('._errorFirstName').slideDown();
            $('#first_nameRegis').focus();
            return;
        }
        if(last_name == ''){
            $('._errorLastName').text('Tên không được bỏ trống');
            $('._errorLastName').slideDown();
            $('#last_nameRegis').focus();
            return;
        }

        if(!emailReg.test(email)){
            $('._errorEmail').text('Email sai dạng, xin thử lại bằng Email khác');
            $('._errorEmail').slideDown();
            $('#emailRegis').focus();
            return;
        }

        if(password == ''){
            $('._errorPassword').text('Mật khẩu không được để trống');
            $('._errorPassword').slideDown();
            $('#passwordRegis').focus();
            return;
        }
        if(password.length <6){
            $('._errorPassword').text('Mật khẩu từ 6 ký tự trở lên.');
            $('._errorPassword').slideDown();
            $('#passwordRegis').focus();
            return;
        }
//        if(password != confirm_pass){
//            $('._errorConfirmPassword').text('Nhập lại mật khẩu không chính xác');
//            $('._errorConfirmPassword').slideDown();
//            $('#confirmPassRegis').focus();
//            return;
//        }
        $('div.loading-face').show();
        UserFacebook.registerFacebook(username,first_name,last_name,password,email);
    },
    loginFacebook : function(profile_fb,is_select_avatar){
        profile_fb = JSON.stringify(profile_fb);

        if(profile_fb.error && profile_fb.error.length>0){
            fb_login();
            return;
        }
        $.ajax({
            url:LoginFacebookUrl+"/connect_facebook",
            type:'post',
            data:{ profile_fb:profile_fb },
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.result == RegisterFacebook){

                    $('#registerFacebook').html(data.html);
                    $('._registerFacebook').click();
                    $('div.loading-face').hide();
                }else if(data.result == ConfirmPass){
                    //location.reload();
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('#myModallogin').removeClass('in');
                    $('#confirmPassword').html(data.html);
                    $('._confirmPassFace').click();
                    $('div.loading-face').hide();
                }else if(data.result == 3){
                    location.reload();
                }else if(data.result == 5){

                    if(is_select_avatar == 1){
                        window.location.href = data.url;
                    }else{
                        var div_fb = $('._cd_facebook');
                        if(div_fb.length > 0){
                            div_fb.html(data.html);
                        }
                    }


                }
                else{
                    location.reload();
                }
            }
        });
    },

    confirmPassword : function(password){
        $.ajax({
            url:LoginFacebookUrl+"/confirm_pass_login",
            type:'post',
            data:{ password:password },
            success:function(result) {
                $('div.loading-face').hide();
                var data = $.parseJSON(result);
                if(data.error == 1){
                    location.reload();
                }else{
                    $('._errorPassword').text(data.message);
                    $('._errorPassword').slideDown();
                    $('#passwordConfirm').focus();

                    return;
                }
            }
        });
    },

    registerFacebook : function(username,first_name,last_name,password,email){
        $.ajax({
            url:LoginFacebookUrl+"/process_register",
            type:'post',
            data:{ username:username,first_name:first_name,
                last_name:last_name,password:password,email:email
            },
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.error == ErrorPassEmpty){
                    $('div.loading-face').hide();
                    $('._errorPassword').text(data.message);
                    $('._errorPassword').slideDown();
                    $('#passwordRegis').focus();
                    return;
                }
                if(data.error == ErrorNotUsername){
                    $('div.loading-face').hide();
                    $('._errorUserName').text(data.message);
                    $('._errorUserName').slideDown();
                    $('#usernameRegis').focus();
                    return;
                }
                if(data.error == ErrorNotEmail){
                    $('div.loading-face').hide();
                    $('._errorEmail').text(data.message);
                    $('._errorEmail').slideDown();
                    $('#emailRegis').focus();
                    return;
                }
                if(data.error == RegisterSuccess){
                    if(data.url != '' && data.url != 'undefined'){
                        window.location.href = data.url;
                    }
                    //alert(data.message);
                    //location.reload();
                }
            }
        });
    },
    checkExistAccount : function (username){

    }
}