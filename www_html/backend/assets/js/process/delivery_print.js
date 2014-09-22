/**
 * Created by Admin on 5/21/14.
 */
$(document).ready(function(){
    $(document).on('keyup','._input_weight',function(e){
        if(e.keyCode == 13){
            var weight = $(this).val();
            if(weight == ''){
                weight = 0;
            }
            if(!$.isNumeric(weight)){
                Common.BSAlert("Yêu cầu nhập đúng trọng lượng");
                return;
            }
            $('._span_weight').text(weight);
            $(this).hide();
            $('._span_weight').show();

        }
    });

    $(document).on('click','._span_weight',function(){
        $(this).hide();
        $('._input_weight').show();
        $('._input_weight').focus();
    });

    $(document).on('click','._btn_delivery',function(){
        $('._body_print').printElement();
    });
});

function myFunction() {
    window.open("http://www.w3schools.com");
}

function popitup(url) {

    var newwindow=window.open(url,'','location=1,status=1,scrollbars=1height=600,width=850');
    if (window.focus) {newwindow.focus()}
    return false;
}
