/**
 * Created by Admin on 4/14/14.
 */
$(document).ready(function(){
    $('._ul_order_confirm').on('click','._order_confirm',function(){
        var order_id = $(this).data('order-id');
        OrderConfirm.confirmedOrder(order_id);
    });
});

var OrderConfirm  = {
    confirmedOrder : function(order_id){
        console.log(order_id);
        $.ajax({
            url : ConfirmUrl,
            type : "POST",
            data : {
                id : order_id,
                status : "WAIT"
            },
            success : function(data){
                if(data.type == 1){
                    $('._order_content[data-order-id='+order_id+']').slideUp();
                }else{
                    Global.sAlert(data.message);
                }
            }
        })
    }
};