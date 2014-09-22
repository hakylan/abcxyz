/**
 * Created by Admin on 2/17/14.
 */
var ajax_quantity = null;
$(document).ready(function(){
    CartBase.countCart();
});
var CartBase = {
    countCart : function(){
        if(ajax_quantity != null){
            ajax_quantity.abort();
        }
        ajax_quantity = $.ajax({
            url : cart_load_price,
            type : "GET",
            success : function(data){
                var result = $.parseJSON(data);
                $('._total_quantity_cart').html(result.total_cart);
                $('._total_price_cart').html(result.total_price);
            }
        });
    }
}
