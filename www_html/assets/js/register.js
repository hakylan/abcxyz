/**
 * Register
 */
$(document).on('click','._openModalRegis',function(e){
    e.preventDefault();
    $('#myModallogin').modal('hide');
    $('#myModalregister').modal();
});


$('._btnSubmitTop').click(function(e){
    e.preventDefault();
    var formRegister = $('#frm-register-top');
    formRegister.find('#repassword').val(formRegister.find('#password').val());
    if(validateRegister(formRegister)==true) {
        $.ajax({
            url:  base_url+'/register/register',
            type: "POST",
            data: $('#frm-register-top').serialize(),
            beforeSend :function(){
                $('._btnSubmitTop').hide();
                $('.btnShow').show();
            },
            success: function (data) {
                $('.btnShow').hide();
                $('._btnSubmitTop').show();
                if(data.type == 1){
                    $('#myModalregister').modal('hide')
                    Global.sAlert(data.message);
                    var count = 5;
                    var event = setInterval(function () {
                        if (count < 1) {
                            location.reload();
                            clearInterval(event);
                        }
                        count--;
                    }, 1000);
                }else{
                    formRegister.find("#"+data.element).html(data.message);
                }
                var count = 1;
                var event = setInterval(function () {
                    if (count < 1) {
                        clearInterval(event);
                    }
                    count--;
                }, 1000);

                return false;
            }
        });
    }
    return false;
});

$('._btnSubmit').click(function(e){
    e.preventDefault();
    var formRegister = $('#frm-register');
    if(validateRegister(formRegister)==true) {
        $.ajax({
            url:  base_url+'/register/register',
            type: "POST",
            data: $('#frm-register').serialize(),
            beforeSend :function(){
                formRegister.find("._btnSubmit").hide();
                formRegister.find(".btnShow").show();
            },
            success: function (data) {
                formRegister.find(".btnShow").hide();
                formRegister.find("._btnSubmit").show();
                var count = 1;
                var event = setInterval(function () {
                    if (count < 1) {
                        clearInterval(event);
                    }
                    count--;
                }, 1000);
                if(data.type == 0){
                    formRegister.find("#"+data.element).html(data.message);
                    formRegister.find("._btnSubmit").html(data.message+', thử lại');
                }else{
//                    formRegister.find("#"+data.element).html(data.message);return false;
                    window.location.href = base_url+'/register/upload_avatar';
                }
            }
        });
    }
    return false;
});

    function validateRegister(formRegister){

        if( formRegister.find('#username').val().replace(/\s+/, '') == '' || formRegister.find('#username').val().replace(/\s+/, '').length<4)
        {
            formRegister.find('#username').addClass('form-myinput-warning');
            formRegister.find('#error-username').html('Tên tài khoản không được rỗng và phải lớn hơn 3 ký tự!');
            return false;
        }
        formRegister.find('#username').removeClass('form-myinput-warning');

        if(formRegister.find('#email').val().replace(/\s+/, '') == '')
        {
            formRegister.find('#email').addClass('form-myinput-warning');
            formRegister.find('#error-email').html('Email không được rỗng!');
            return false;
        }

        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!filter.test(formRegister.find('#email').val().replace(/\s+/, '')))
        {
            formRegister.find('#email').addClass('form-myinput-warning');
            formRegister.find('#error-email').html('Email không hợp lệ!');
            return false;
        }

        formRegister.find('#email').removeClass('form-myinput-warning');
        formRegister.find('#error-email').html('');

        if(formRegister.find('#password').val().replace(/\s+/, '') == '' || formRegister.find('#password').val().replace(/\s+/, '').length<6)
        {
            formRegister.find('#password').addClass('form-myinput-warning');
            formRegister.find('#error-password').html('Mật khẩu từ 6 ký tự trở lên!');
            return false;
        }
        formRegister.find('#password').removeClass('form-myinput-warning');
        formRegister.find('#error-password').html('');
        if(formRegister.find('#repassword').val().replace(/\s+/, '') == '')
        {
            formRegister.find('#repassword').addClass('form-myinput-warning');
            formRegister.find('#error-repassword').html('Chưa điền lại mật khẩu!');
            return false;
        }

        if(formRegister.find('#repassword').val() != formRegister.find('#password').val())
        {
            formRegister.find('#repassword').addClass('form-myinput-warning');
            formRegister.find('#error-repassword').html('Mật khẩu không khớp!');
            return false;
        }
        formRegister.find('#repassword').removeClass('form-myinput-warning');
        formRegister.find('#error-repassword').html('');


        if(formRegister.find('#first_name').val().replace(/\s+/, '')=='' || formRegister.find('#last_name').val().replace(/\s+/, '')=='')
        {
            formRegister.find('#first_name').addClass('form-myinput-warning');
            formRegister.find('#error-fullname').html('Họ tên không được rỗng!');
            return false;
        }
        formRegister.find('#first_name').removeClass('form-myinput-warning');
        formRegister.find('#error-fullname').html('');


        if(formRegister.find('#tt_id').val()>0 || formRegister.find('#qh_id').val()>0 || formRegister.find('#detail_address').val()!=''){
            if(formRegister.find('#tt_id').val()<0 || formRegister.find('#qh_id').val()<0 || formRegister.find('#detail_address').val()==''){
                formRegister.find('#tt_id').addClass('form-myinput-warning');
                formRegister.find('#qh_id').addClass('form-myinput-warning');
                formRegister.find('#detail_address').addClass('form-myinput-warning');
                formRegister.find('#error-address').html('Bạn chưa nhập đầy đủ thông tin địa chỉ!');
                return false;
            }
        }
        formRegister.find('#tt_id').removeClass('form-myinput-warning');
        formRegister.find('#qh_id').removeClass('form-myinput-warning');
        formRegister.find('#detail_address').removeClass('form-myinput-warning');
        formRegister.find('#error-address').html('');
        formRegister.find('.error').html('');
        return true;
    }



    function blurUsername(formRegister){
        var name = $('#'+formRegister).find('#username').val();
        if( name.replace(/\s+/, '') == '' || name.replace(/\s+/, '').length<4)
        {
            $('#'+formRegister).find('#error-username').html('Tên tài khoản không được rỗng và phải lớn hơn 3 ký tự!');
            return false;
        }
        $('#'+formRegister).find('#error-username').html('');

        $.ajax({
            url:base_url + "/register/check_username",
            data:{ name:name },
            type:"POST",
            success:function(result) {
                if(result.type!=1){
                    $('#'+formRegister).find('#error-username').html(result.message);
                    $('#'+formRegister).find('#username').addClass('form-myinput-warning');
                    $('#'+formRegister).find('#username').removeClass('form-myinput-typing');
                    $('#'+formRegister).find('#error-username').addClass('red-normal');
                    $('#'+formRegister).find('#error-username').removeClass('blue-normal');
                }else{
                    $('#'+formRegister).find('#error-username').removeClass('red-normal');
                    $('#'+formRegister).find('#error-username').addClass('blue-normal');
                    $('#'+formRegister).find('#username').addClass('form-myinput-typing');
                    $('#'+formRegister).find('#username').removeClass('form-myinput-warning');
                    $('#'+formRegister).find('#error-username').html(result.message);
                }
            }
        });

    }


$(document).on('change','._ajaxState',function(){
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


//upload Avatar
    $('#photoimg').change(function(){
        $("#sizeHeight").val('');
        $("#sizeWidth").val('');
        $("#preview").html('');
        $('._errorUrl').html('');
        $('._titleMoveImg').addClass('hidden');
        $('.saveavatar').addClass('hidden');
        $('.saveEditavatar ').addClass('hidden');
        $('._disable').removeClass('hidden');
        $('.opacity-upavatar').removeClass('hidden');
        $('._waitingImg').removeClass('hidden');
        $('._waitingImg').html(' Đang tải hình ảnh...');
        $("#preview").html('<img style="width: 100%" src="'+base_url+'/assets/images/loader.gif" alt="Uploading...."/>');
        $("#imageform").ajaxForm({
            target: '#preview',
            complete: function(){
                $('._loading').hide();
                $('.opacity-upavatar').addClass('hidden');
                $('._waitingImg').addClass('hidden');
                    $('#draggable').load(function(){
                        $('._titleMoveImg').removeClass('hidden');
                        $('.saveavatar').removeClass('hidden');
                        $('.saveEditavatar ').removeClass('hidden');
                        $('._disable').addClass('hidden');

                        $("#sizeHeight").val($('#draggable').height());
                        $("#sizeWidth").val($('#draggable').width());
                        auto_fixImg();
                    });
                    draggable();
            }
        }).submit();
    });

    function draggable(){
        $( "#draggable" ).draggable({
            drag: function( event, ui ) {
                auto_fixImg();
                }
        });
    }

    function auto_fixImg(){
            var height = $('#draggable').height();
            var width = $('#draggable').width();
            if(width>height){
                $(".ui-draggable").addClass("left");
            }
            if(width<height){
                $(".ui-draggable").addClass("top");
            }
            if(width==height){
                $(".ui-draggable").addClass("top");
                $(".ui-draggable").addClass("left");
            }
            $("#locationX").val($("#draggable").css('left'));
            $("#locationY").val($("#draggable").css('top'));
    }


    //UploadAvatar
    $(document).on('click','.saveavatar',function(){
        $.ajax({
            url:  base_url+'/register/upload_avatar_step3',
            type: "POST",
            data: $('#_uploadAvatarStep3').serialize(),
            beforeSend :function(){
                $('saveavatar').html('Đang sử lý...');
            },
            success: function (data) {
                $('saveavatar').html('Sử dụng');
                if(data.type==1){
                    $('.img-avatar').find('img').attr('src',data.element);
                    $('.opacity-upavatar').removeClass('hidden');
                    $('._waitingImg').addClass('alert-success');
                    $('._waitingImg').removeClass('alert-alert hidden');
                    $('._waitingImg').html('Chọn ảnh đại diện thành công');
                    var count = 3;
                    var event = setInterval(function () {
                        if (count < 1) {
                            window.location.href = base_url+'/register/register_success';
                            clearInterval(event);
                        }
                        count--;
                    }, 1000);
                }else{
                    $('.opacity-upavatar').removeClass('hidden');
                    $('._waitingImg').removeClass('alert-success hidden');
                    $('._waitingImg').addClass('alert-alert');
                    $('._waitingImg').html(data.message);
                    var count = 2;
                    var event = setInterval(function () {
                        if (count < 1) {
                            $('.opacity-upavatar').addClass('hidden');
                            $('._waitingImg').removeClass('alert-alert');
                            clearInterval(event);
                        }
                        count--;
                    }, 1000);
                }
            }
        });
    });
    //add Url link avatar
    $(document).on('click','._addUrlAvatar',function(){
//        if($('._urlAvatar').val()==''){
//            $(this).parent().find('p').html('Chưa nhập đường dẫn ảnh!');
//        }
        $('._errorUrl').html('');
        $('._waitingImg').removeClass('alert-success hidden');
        $('._waitingImg').addClass('alert-alert');
        $('._waitingImg').html(' Đang tải hình ảnh...');
        $('.opacity-upavatar').removeClass('hidden');

        $("#sizeHeight").val('');
        $("#sizeWidth").val('');
        var url = $('._urlAvatar').val();
        var ext = url.split('.').pop();
        var arr = ["jpg", "png", "gif", "bmp", "jpeg"];

        if(jQuery.inArray(ext,arr) == -1){
            $(this).parent().find('p').html('Không phải đường dẫn ảnh, vui lòng sử dụng đường dẫn ảnh khác!');
            $(this).addClass('');
            $('.opacity-upavatar').addClass('hidden');
            $('._waitingImg').addClass('hidden');
            return false;
        }
        $(this).parent().find('p').html('');

        $('#preview').html("<img id='draggable' src='" + url + "' class='preview'>");
        $('#preview').append("<input type='hidden' name='filenameUrl' value='" + url + "'>");

        $('#draggable').load(function(){
            $("#sizeHeight").val($('#draggable').height());
            $("#sizeWidth").val($('#draggable').width());
            draggable();
            var count = 0;
            var event = setInterval(function () {
                if (count < 1) {
                    auto_fixImg();
                    $('.opacity-upavatar').addClass('hidden');
                    $('._waitingImg').addClass('hidden');
                    $('._titleMoveImg').removeClass('hidden');
                    $('.saveavatar').removeClass('hidden');
                    $('.saveEditavatar ').removeClass('hidden');
                    $('._disable').addClass('hidden');
                    clearInterval(event);
                }
                count--;
            }, 1000);
            return false;
        });
        $('#draggable').error(function() {
            $('._titleMoveImg').addClass('hidden');
            $('.saveavatar').addClass('hidden');
            $('.saveEditavatar ').addClass('hidden');
            $('.opacity-upavatar').addClass('hidden');
            $('._disable').removeClass('hidden');
            $('._errorUrl').html('Đường dẫn ảnh lỗi, vui lòng sử dụng đường dẫn ảnh khác!');
        });
    });



    //Tooltip input
    $('.inputregister').each(function() {
        $(this).hover(function() {
            $(this).parent().find('.tooltipregister').css('display', 'inline-block');
        }).focus(function() {
                $(this).parent().find('.tooltipregister').css('display', 'inline-block');
            }).blur(function() {
                $(this).parent().find('.tooltipregister').css('display', 'none');
            }).mouseout(function() {
                $(this).parent().find('.tooltipregister').css('display', 'none');
            });
    });



