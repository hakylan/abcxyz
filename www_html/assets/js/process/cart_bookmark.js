/**
 * Created by Admin on 1/23/14.
 */
$(document).ready(function(){
    $(document).on('click','._load_cart',function(){
        CartBook.loadCart();
    });
    setTimeout("CartBook.getTotalQuantity()",2000);
});
var CartBook = {
    getTotalQuantity : function(){
        var total_item = $('._total_item').text();
        if(total_item == null || total_item == ''){
            total_item = '0';
        }
        $('._total_cart_item').text(total_item);
    },
    loadCart : function(){
        $.ajax({
            url: paitool_url+"/load_cart",
            type : "POST",
            data : "",
            success : function(data){
                $('#showcart-content').html(data);
                setTimeout("CartBook.getTotalQuantity()",2000);
            }
        })
    }
}
