/**
 * Created by Admin on 3/6/14.
 */
$(document).ready(function(){
    $(document).on('click','._transaction_modal_view',function(){
        var order_code = $(this).attr('data-code-order');
        var app = $(this).attr('data-app-id');
        ModalBox.loadTransaction(order_code,app);
    });
});
var ModalBox = {
    loadTransaction : function(order_code,app){
        $.ajax({
            url : urlLoadTransaction,
            type : "GET",
            data : {
                order_code : order_code,app:app
            },
            success : function(result){
                var data = $.parseJSON(result);
                if(data.type == 1){
                    $('._transaction'+order_code).html(data.html);
                }
            }
        })
    }
}