$( document ).ready(function() {
    //Xác nhận email đã tồn tại
    $(document).on('click','._verifyEmail', function(){
            $.ajax({
                url: backend_url + '/user/detail/verify_email?id='+$(this).attr('data-id'),
                beforeSend :function(){
                },
                success: function(result) {
                    alert(result.type);
                    if(result.type==1){

                    }else{

                    }
                }
            });
    });

    $(document).on('click','.chanPass', function(){
        $('#myModal').modal();
        $('#myModal').modal('show');
        $('.dash-menu-pass').toggleClass('open');
//        $('#myModal-chanPass').modal('hide');
        $('#myModal-chanPass').modal();
    });
//    Add Number Accountant
    $(document).on('click','#_accountant',function(){
            var accountant = $('#data-accountant-id').attr('data-accountant-id');
            $.ajax({
                url:backend_url + "/user/detail/add_accountant",
                type:"POST",
                data:{ accountant:accountant},
                success:function(result) {
                    if(result.type!=1){
                        alert(result.message);
                    }else{
                        $('#_returnAccountant').html(result.element+'<span>&nbsp;- <span id="_syncBalance" style="cursor: pointer; color:blue;">Đồng bộ</span>');
                        $('#_syncBalance').click();
//                        $('#_getBalance').html('Số dư: 0 <sup>đ</sup>');
                    }
                }
            });
    });

    //get Sync Balance for Accountant
    $(document).on('click','#_syncBalance',function(){
        var id = $('#data-accountant-id').attr('data-accountant-id');
        $.ajax({
            url:backend_url + "/user/detail/get_balancer",
            type:"POST",
            data:{ id:id},
            success:function(result) {
                if(result.type!=1){
                    alert(result.message);
                }else{
                    $('#_getBalance').html(result.element+'<sup>đ</sup>');
                }
            }
        });
    });



    //add Mobile

    $(document).on('click','#_addMobile',function(){
        var mobile = $('._inputMobile').val();
        var userId = $('#_addMobile').attr('data-user');
        $.ajax({
            url:backend_url + "/user/detail/add_new_mobile",
            type: "POST",
            data:{ mobile: mobile, userId: userId },
            success:function(result) {
                if(result.type!=1){
                    alert(result.message);
                }else{
                    $('._showMobile').append(result.element+"<br>")
                }
            }
        });
    });


});
