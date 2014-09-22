$(document).ready(function(){
    var DepositVoucherCommon = {
        baseUrl:backend_url,
        wrapContent:$(".make_payment_area")
    };
    var ajax = [];
    $(document).on('keyup','#accountNo',function () {
        $(this).parent().parent().addClass('open');
        var keyword = $(this).val();
        for(var i =0; i<ajax.length; i++) {
            ajax[i].abort();
        }
        var $ajax = $.ajax({
            type: "GET",
            data: {
                keyword:keyword
            },
            url: DepositVoucherCommon.baseUrl+"/deposit_voucher/search_account",
            success: function(result){
                var html = result.data;
                $('._suggest-list').html(html);


            }
        });
        ajax.push($ajax);
    });

    $(document).on('click','._account',function () {
        $(this).hide();
        $('#accountNo').parent().parent().removeClass('open');
        var $account = $(this).attr('data-account-uid');
        var input = $('#accountNo');
        input.val($account);
        var username_or_account = input.val();

        $('._accountInfo,._over_step_one').hide();
        $.ajax({
            type: "POST",
            url: DepositVoucherCommon.baseUrl+"/deposit_voucher/get_account",
            data:{username_or_account:username_or_account},

            success: function(_result){
                $("._loading").hide();
                var object = _result.message;

                object = $.parseJSON(object);
                console.log(object);

                //assign data
                $('#fullname').html(object['name']);
                $('#username').html(object['username']);
                $('#status').html(object['status']);
                $('#service').html(object['service_id']);
                $('#balance').html(object['balance']+'<sup>đ</sup>');

                $('._accountInfo').find('.accountInfo').show();
                $('._accountInfo,._over_step_one').show();
            }
        });
    });

    var DepositVoucherFinding = {

        render:function(){
            $.ajax({
                type: "GET",

                url: DepositVoucherCommon.baseUrl+"/deposit_voucher/step_one",
                success: function(html){
                    $("._loading").hide();
                    DepositVoucherCommon.wrapContent.append(html);
                    $('._accountInfo,._over_step_one').hide();
                }
            });
        }
    };

    var DepositVoucherCreate = {
        create:function(){

            var data = {};

            data.amount = $('#amount');
            data.tran_type = $('#tran_type');
            data.bank = $('#bank');
            data.bank_number = $('#bank_name');
            data.bank_journal_entry = $('#bank_journal_entry');
            data.description = $('#description');
            data.account_no = $("#accountNo");
            data.bank_tran_time = $('#bank_tran_time');
            console.log(data);

            var message = this.valid(data);
            if(message != ''){
                console.log(message);
                return false;
            }
            return DepositVoucherCreate.save({
                amount:data.amount.val(),
                tran_type:data.tran_type.val(),
                bank_name:data.bank?data.bank.val():'',
                bank_number:data.bank_number?data.bank_number.val():'',
                bank_journal_entry:data.bank_journal_entry?data.bank_journal_entry.val():'',
                note:data.description.val(),
                account_no:data.account_no.val(),
                bank_tran_time:data.bank_tran_time.val()
            });

        },
        valid:function(object){
            var messageError = '';
            if(object.amount.val() == ''){
                messageError+="Chưa điền số tiền giao dịch !\n";
                this.generateError(object.amount);
            }
            if(object.tran_type.val() == '-1' || object.tran_type.val() == undefined){
                messageError+="Chưa chọn hình thức nạp tiền\n";
                this.generateError(object.tran_type);
            } else {

                if(object.tran_type.val() == 'BANK_TRANSFER'){
                    if(object.bank.val() == '' || object.bank.val() == '-1' || object.bank.val() == undefined){
                        messageError+="Chưa chọn ngân hàng\n";
                        this.generateError(object.bank);
                    }
                    if(object.bank_number.val() == ''){
                        messageError+="Chưa điền số tài khoản !\n";
                        this.generateError(object.bank_number);
                    }
                    if(object.bank_journal_entry.val() == '') {
                        messageError+="Chưa điền số bút toán !\n";
                        this.generateError(object.bank_journal_entry);
                    }

                }
            }
            return messageError;

        },
        save:function(object){

            $.ajax({
                type: "POST",
                url: DepositVoucherCommon.baseUrl+'/deposit_voucher/create_voucher',
                async:false,
                data: object,

                success: function(result){
                    $("._loading").hide();
                    var voucherUid = result.voucherUid;
                    if( voucherUid !=''){
                        $("#loader-overlay").css('display','none');
                        /* tạo phiếu thành công thì ẩn nút đi*/
                        $('._over_step_two').hide();
                        DepositVoucherInfo.render(voucherUid);
                    }

                }
            });
        },
        generateError:function(object){
            object.css('border','1px solid red');
        },

        render:function(){
            $.ajax({
                type: "GET",
                url: DepositVoucherCommon.baseUrl+"/deposit_voucher/step_two",
                success: function(html){
                    $("._loading").hide();
                    DepositVoucherCommon.wrapContent.append(html);
                }
            });
        }
    };

    var DepositVoucherInfo = {
        render:function(uid){
            $.ajax({
                type: "GET",

                url: DepositVoucherCommon.baseUrl+"/deposit_voucher/detail",
                data: { uid:uid } ,

                success: function(html){
                    $("._loading").hide();
                    DepositVoucherCommon.wrapContent.append(html);
                }
            });
        }
    };



    if($('.make_payment_area').length > 0){

        DepositVoucherFinding.render();

        $(document).on('click','._search_account',function(event){
            event.preventDefault();
            var input = $('#accountNo');
            var username_or_account = input.val();
            if(username_or_account == '') {
                input.css('border','1px solid red');
            }
            $('._accountInfo,._over_step_one').hide();
            $.ajax({
                type: "POST",
                url: DepositVoucherCommon.baseUrl+"/deposit_voucher/get_account",
                data:{username_or_account:username_or_account},

                success: function(_result){
                    $("._loading").hide();
                    var object = _result.message;

                    object = $.parseJSON(object);
                    console.log(object);

                    //assign data
                    $('#fullname').html(object['name']);
                    $('#username').html(object['username']);
                    $('#status').html(object['status']);
                    $('#service').html(object['service_id']);
                    $('#balance').html(object['balance']+'<sup>đ</sup>');

                    $('._accountInfo').find('.accountInfo').show();
                    $('._accountInfo,._over_step_one').show();
                }
            });

        });

        $(document).on('click','._over_step_one',function(event){
            event.preventDefault();
            if($('._add_money_area').length == 0){
                DepositVoucherCreate.render();
                $(this).hide();
            }
        });

        $(document).on('change','._choose_type_transaction',function(event){
            event.preventDefault();
            var bankInfoArea = $('._choose_type_bank');
            bankInfoArea.hide();

            if($(this).val() == 'BANK_TRANSFER'){
                bankInfoArea.show();
            }
        });

        $(document).on('click','._over_step_two',function(event){
            event.preventDefault();
            DepositVoucherCreate.create();
        });

        /* checking */

        $(document).on('keyup','#amount',function(){
            $("#amount").number(true,0);
        });
    }

});
