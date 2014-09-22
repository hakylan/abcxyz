$(document).ready(function(){

//    $('#demo-form-valid').click(function(event){
//
//        event.preventDefault();
//
//        var username = $('#username');
//        var usernameValue = username.find('input').val();
//
//        var password = $('#password');
//        var passwordValue = password.find('input').val();
//
//        if(usernameValue == ''){
//            alert('username is not valid');
//            username.css('border','1px solid red');
//        }
//        if(passwordValue == ''){
//            alert('password is not valid');
//            password.css('border','1px solid red');
//        }
//        var url = $('#backendUrl').val();
//        $.post(url+'/login/login',{username:usernameValue,password:passwordValue},function(data){
//
//            console.log(data);
//            console.log(data.type);
//            if(data.type == 1){
//                location.href = url+'/dashboard';
//            }else{
//                alert('Tên đăng nhập hoặc mật khẩu không đúng');return false;
//            }
//        });
//    });

    $(document).on('click','#reload_captcha',function(){
        $('#captcha_image').attr('src', $('#captcha_image').attr('src')+'#');
        $(this).find('i').toggleClass('icon-refresh icon-repeat');
        $('#_txtGetCapcha').html('Geting Capcha...');
    });
    $('#captcha_image').load(function(){
        $('#reload_captcha').find('i').toggleClass('icon-refresh icon-repeat');
        $('#_txtGetCapcha').html('Get a new Captcha');
    });

//    $('#login_pass').keypress(function(event){
//        var keycode = (event.keyCode ? event.keyCode : event.which);
//        alert(keycode);
//        if(keycode == '9'){
//            $('#login-validation').find('#_captcha').focus();
//        }
//    });

});
