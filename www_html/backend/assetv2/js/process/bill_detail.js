/**
 * Created by ha bau on 7/18/14.
 */
$(document).ready(function () {
    $('._input_real_cod').autoNumeric({ aPad: false, mDec: 3, vMax: 9999999999999999 });
    if (real_cod > 0) {
        $('._input_real_cod').autoNumeric('set', real_cod);
    }

    $('._input_real_cod').blur(function () {
        if ($(this).val() == '' && real_cod > 0) {
            $('._input_real_cod').autoNumeric('set', real_cod);
        }
    });
    var click_save = false;
    $("#btn_save_real_cod").on("click", function () {
        if(click_save){
            return false;
        }
        click_save =true;
        var money = $('._input_real_cod').autoNumeric('get');
        if (money > 0) {
            $.ajax({
                url: save_real_cod_url,
                type: "POST",
                data: {
                    real_cod: $('._input_real_cod').autoNumeric('get'),
                    id: bill_detail_id
                },
                success: function (data) {
                    click_save = false;
                    if (data.type == 1) {
                        $("#error_cod").html('');
                        $("#btn_save_real_cod").attr('value', 'Sửa');
                    }
                    $("#error_cod").html(data.message);
                    setTimeout(function () {
                        $("#error_cod").html('');
                    }, 3000);
                }
            })
        } else {
            click_save = false;
            $("#error_cod").html("vui lòng nhập số tiền thực thu!");
            setTimeout(function () {
                $("#error_cod").html('');
            }, 3000);
        }
    });
});
