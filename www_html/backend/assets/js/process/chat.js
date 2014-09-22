/**
 * Created by Hindua88 03/04/2014
 */
var ajax_rq = null;
$(document).ready(function(){
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
        },
        complete:function(){
            $('._loading').hide();
        }
    });
    // Add message chat
    $(document).on('keyup','._input_chat',function(e){
        if(e.keyCode == 13){
//            sendMessage();
        }
    });
    $(document).on('onclick','#btn_send_message_external',function(e){
//        sendMessage();
    });
    $(document).on('onclick','#btn_send_message_internal',function(e){
//        sendMessage();
    });
});

