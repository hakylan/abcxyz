$(document).on('click','.btnSubmitEditUser',function(event){

    event.preventDefault();

    var formUpdateUser = $('#frm-edit-user');
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (formUpdateUser.find('#users-first_name').val()=='') {
        formUpdateUser.find('#error-fullname').html('Tên không được để trống.');

        return false;
    }

    if (formUpdateUser.find('#users-last_name').val()=='') {
        formUpdateUser.find('#error-fullname').html('Họ không được để trống.');

        return false;
    }

    edit_UserProfile();
});

function edit_UserProfile(){
    $.ajax({
        url:  url_editUserProfile,
        type: 'post',
        data: $('#frm-edit-user').serialize(),

        beforeSend :function(){
            $(".btnSubmitEditUser").html('Đang xử lý...');
        },

        success: function (data) {
            $(".btnSubmitEditUser").html('LƯU');

            if(data.type == 0){
                $("#"+data.element).html(data.message);
            }else{
                $(".btnSubmitEditUser").html(data.message);
            }
        }
    });
}

//ResetPass
$(document).on('click','#_btn-reset-password',function(){
    $.ajax({
        url:backend_url + "/user/user_profile/change_pass",
        type: "POST",
        data:$('#_reset-password').serialize(),
        success:function(result) {
            if(result.type!=1){
                $('._err-new_pass').html(result.error['new_pass']);
                $('._err-re_new_pass').html(result.error['re_new_pass']);
                $('._err-old_pass').html(result.error['old_pass']);
                $('#_btn-reset-password').html(result.message);
                var count = 3;
                var event = setInterval(function () {
                    if (count < 1) {
                        $('#_btn-reset-password').html('Thử lại');
                        clearInterval(event);
                    }
                    count--;
                }, 1000);
            }else{
                $('#new_pass').val('');
                $('#re_new_pass').val('');
                $('#old_pass').val('');
                $('#_btn-reset-password').html('Đã đổi mật khẩu');
                $('#myModal').modal('hide');

                $('#changePassSuccess').modal();

                var myTime = 5;

                setInterval(function(){
                    if (myTime > 0) {
                        $("#seconds_logout").html(myTime);
                        myTime--;
                    }
                    if (myTime == 0) {
                        location. href = $("#ok_logout").val();
                    }
                }, 1000);
            }
        }
    });
});

//Reset Pass Payment
$(document).on('click','#_btn-reset-password-payment',function(){
    $.ajax({
        url:backend_url + "/user/user_profile/change_pass_payment",
        type: "POST",
        data:$('#_reset-password-payment').serialize(),
        success:function(result) {
            if(result.type!=1){
                $('._err-new_pass_payment').html(result.error['new_payment_pass']);
                $('._err-re_payment').html(result.error['re_payment_pass']);
                $('._err-old_payment_pass').html(result.error['old_payment_pass']);
                $('#_btn-reset-password-payment').html(result.message);
                var count = 3;
                var event = setInterval(function () {
                    if (count < 1) {
                        $('#_btn-reset-password-payment').html('Thử lại');
                        clearInterval(event);
                    }
                    count--;
                }, 1000);
            }else{
                $('#new_payment_pass').val('');
                $('#re_payment_pass').val('');
                $('#old_payment_pass').val('');

                $('._err-new_pass_payment').html(result.error['new_payment_pass']);
                $('._err-re_payment').html(result.error['re_payment_pass']);
                $('._err-old_payment_pass').html(result.error['old_payment_pass']);
                $('#_btn-reset-password-payment').html('LƯU');

                $('#myModalPayment').modal('hide');
                $('#changePaymentPassSuccess').modal();
                $('#old_payment').show();
            }
        }
    });
});

$(document).on('click', '#ok_logout', function(){
   var url =  $('#ok_logout').val();
    location.href = url;
});