$(document).ready(function(){
    $('#demo-form-valid').click(function(event){

        event.preventDefault();

        var username = $('#username');
        var usernameValue = username.find('input').val();

        var password = $('#password');
        var passwordValue = password.find('input').val();

        if(usernameValue == ''){
            alert('username is not valid');
            username.css('border','1px solid red');
        }
        if(passwordValue == ''){
            alert('password is not valid');
            password.css('border','1px solid red');
        }
        var url = $('form[name=login-frm]').attr('action');
        $.post(url,
            {
                username: usernameValue,
                password:passwordValue
            },
            function(data) {
                console.log(data);
                console.log(data.type);
                if(data.type == 1){
                    location.href = base_url;
                }else{
                    alert('Tên đăng nhập hoặc mật khẩu không đúng');
                    return false;
                }
        });
    })
});