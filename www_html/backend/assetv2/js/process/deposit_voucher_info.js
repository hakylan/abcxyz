$(document).ready(function () {
    var DepositVoucherInfo = {
        render:function(uid, wrap, from){

            $.ajax({
                type: "GET",

                url: backend_url+"/deposit_voucher/detail",
                data: { uid:uid,from:from} ,
                success: function(html) {

                    $("._loading").hide();
                    if(from == 'detail') {
                        wrap.addClass('col-md-9');
                    }
                    wrap.html(html);
                }
            });
        }
    };
    if($("._detail").length > 0) {
        DepositVoucherInfo.render(voucherUid, $("._detail"), 'detail');
    }

    $(document).on('click','._deposit_voucher_refuse',function(event){

        event.preventDefault();

        var voucherUid = $(this).attr('data-voucher-id');
        $.ajax({
            type: "POST",

            url: backend_url+"/deposit_voucher/refuse",
            data:{voucherUid:voucherUid},

            success: function(result){
                if(result.type == 1) {
                    $("._loading").hide();
                    var wrap = '',from = '';
                    var deposit = result.deposit;

                    if($('.depositInfo').length > 0) {
                        wrap = $('.depositInfo');
                        from = 'add_deposit';
                    } else {
                        wrap = $('._detail');
                        from = 'detail';
                    }

                    wrap.html('');

                    DepositVoucherInfo.render(deposit.uid, wrap, from);
                    return false;
                }
                console.log(result.errorMessage);
                return false;

            }
        });
    });
    $(document).on('click','._deposit_voucher_approval', function(event){
        event.preventDefault();

        var voucherUid = $(this).attr('data-voucher-id');
        $.ajax({
            type: "POST",

            url: backend_url+"/deposit_voucher/approval",
            data:{voucherUid:voucherUid},

            success: function(result){
                if(result.type == 1) {
                    $("._loading").hide();
                    var wrap = '',from = '';
                    var deposit = result.deposit;

                    if($('.depositInfo').length > 0) {
                        wrap = $('.depositInfo');
                        from = 'add_deposit';
                    }else{
                        wrap = $('._detail');
                        from = 'detail';
                    }

                    wrap.html('');

                    DepositVoucherInfo.render(deposit.uid, wrap, from);
                    return false;
                }
                console.log(result.errorMessage);
                return false;
            }
        });
    });
});