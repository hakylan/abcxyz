/**
 * Created by QuyenMinh on 1/2/14.
 */
const ErrorPassEmpty = 0,
    ErrorDb = -1,
    ErrorNotUsername = -3,
    RegisterSuccess = 1,
    NotLogin = -4,
    NotSessionFacebook = -2;
    $(document).ready(function(){
//        $('#registerFacebook').click(function(){
//            var username = $('#username').val();
//            var first_name = $('#first_name').val();
//            var last_name = $('#last_name').val();
//            var password = $('#password').val();
//            var confirm_pass = $('#confirmPass').val();
//            var validateUsername = /^[\W_]/;
//            if(validateUsername.test(username)){
//                alert("Tài khoản không hợp lệ");
//                $('#username').focus();
//                return;
//            }else if(username.length <6 || username.length > 20){
//                alert("Tài khoản giới hạn từ 6 đến 20 kí tự.");
//                $('#username').focus();
//                return;
//            }
//            if(password == ''){
//                alert("Mật khẩu không được để trống");
//                $('#password').focus();
//                return;
//            }
//            if(password.length <5){
//                alert("Mật khẩu quá ngắn");
//                $('#password').focus();
//                return;
//            }
//            if(password != confirm_pass){
//                alert("Nhập lại mật khẩu không chính xác");
//                $('#confirmPass').focus();
//                return;
//            }
//            User.registerFacebook(username,first_name,last_name,password);
//        });
//
//        $('#btnConfirmPass').click(function(){
//            var password = $('#confirm_password').val();
//            User.confirmPassword(password);
//        });
    });
var User = {

    confirmPassword : function(password){
        $.ajax({
            url:LoginFacebookUrl+"/confirm_pass_login",
            type:'post',
            data:{ password:password },
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.error == 1){
                    alert(data.message);
                    window.location.href = base_url;
                }else{
                    alert(data.message);
                    $('#confirm_password').focus();
                    return;
                }
            }
        });
    },

    registerFacebook : function(username,first_name,last_name,password){
        $.ajax({
            url:LoginFacebookUrl+"/process_register",
            type:'post',
            data:{ username:username,first_name:first_name,
                last_name:last_name,password:password
            },
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.error == ErrorPassEmpty){
                    alert(data.message);
                    $('#password').focus();
                    return;
                }
                if(data.error == ErrorNotUsername){
                    alert(data.message);
                    $('#username').focus();
                    return;
                }
                if(data.error == RegisterSuccess){
                    alert(data.message);
                    window.location.href = base_url;
                }
            }
        });
    }
}
