$(document).on('click','#_btnNewAvatar',function(){
    window.location.href = user_url+'/user/edit_avatar';
});
//UploadAvatar
$(document).on('click','.saveEditavatar',function(){
    $.ajax({
        url:  base_url+'/register/upload_avatar_step3',
        type: "POST",
        data: $('#_uploadAvatarStep3').serialize(),
        beforeSend :function(){
            $('saveEditavatar').html('Đang Lưu...');
        },
        success: function (data) {
            $('saveEditavatar').html('Sử dụng');
            $('._waitingImg').html(data.message);
            if(data.type==1){
                $('.img-avatar').find('img').attr('src',data.element);
                $('.opacity-upavatar').removeClass('hidden');
                $('._waitingImg').addClass('alert-success');
                $('._waitingImg').removeClass('alert-alert hidden');
            }else{
                $('.opacity-upavatar').removeClass('hidden');
                $('._waitingImg').removeClass('alert-success hidden');
                $('._waitingImg').addClass('alert-alert');
            }
            var count = 3;
            var event = setInterval(function () {
                if (count < 1) {
                    $('.opacity-upavatar').addClass('hidden');
                    $('._waitingImg').addClass('hidden');
                    clearInterval(event);
                }
                count--;
            }, 1000);
        }
    });
});