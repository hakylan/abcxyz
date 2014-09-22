/**
 * Created by Hindua88 03/04/2014
 */
var ajax_rq = null;
$(document).ready(function(){
    $.ajaxSetup({
        beforeSend:function(){
//            $('._loading').show();
        },
        complete:function(){
//            $('._loading').hide();
        }
    });
    function sendMessage() {
        var root = $('.seu-chat-item-order-' + orderId + '-' + itemId);
        var doc = root.find('._input_chat');
        msg = doc.val();
        if (msg.length > 0) {
            doc.val('');
            OrderItemComment.addMessge(msg);
        } else {
            doc.addClass('form-myinput-warning');
//            doc.attr('placeholder','Vui lòng điền nội dung comment!');
        }
    }
    // Add message chat
    $(document).on('keyup','._input_chat',function(e){
        if(e.keyCode == 13){
            sendMessage();
        }
    });
});

var OrderItemComment = {
    addMessge : function(message){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        var root = $('.seu-chat-item-order-' + orderId + '-' + itemId);
        var doc = root.find('._input_chat');
        doc.prop('disabled', true);
        ajax_rq = $.ajax({
            url: linkPost,
            type : "POST",
            data: {order_id: orderId, item_id: itemId, message: message},
            success: function (data) {
//                console.log(typeof(data));
                doc.prop('disabled', false);
                root.find('.content-chat-item').prepend(data.html);
            }
        })
    }
}
