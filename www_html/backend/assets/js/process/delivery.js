/**
 * Created by Admin on 5/9/14.
 */
var frm_search = $("#frm_search");
$( document ).ajaxComplete(function() {
    $("._upload_file").fileupload({
        url: UploadFile,
        dataType: 'json',
        done: function (e,data) {
            var data = data.result;
            if(data.type == 1){
                var order_list_id = data.order_list_id;
                for (var i = 0; i < order_list_id.length; i++) {
                   var id = order_list_id[i];
                   if($.isNumeric(id)){
                       var warehouse = $('._warehouse_status[data-order-id='+id+']');
                       warehouse.text("Xuất kho");
                       warehouse.css("color","#1B9B1B");
                       warehouse.css("font-weight","bold");
                       warehouse.css("font-size","16px");
                       warehouse.attr("data-delivered","1");
                   }
                }
                var _warehouse = $('._warehouse_status[data-address-id='+data.address_id+']');
                var flag = false;
                _warehouse.each(function(){
                    var obj = $(this);
                    var is_delivered = obj.attr("data-delivered");
                    is_delivered = parseInt(is_delivered);
                    if(is_delivered == 0){
                        $('._btn_delivery[data-address-id='+data.address_id+']').attr("disabled","true");
                        flag = false;
                        return;
                    }else{
                        flag = true;
                    }
                });
                if(flag){
                   $('._btn_delivery[data-address-id='+data.address_id+']').removeAttr("disabled");
                }
            }else{
                Common.BSAlert(data.message);
            }
        }
    });
//    $(document).on('click','._upload_file',function(){
//        var upload = $(this);
//        var url = UploadFile;
//        $.ajax({
//            // Uncomment the following to send cross-domain cookies:
//            //xhrFields: {withCredentials: true},
//            url: upload.fileupload('option', url),
//            dataType: 'json',
//            context: upload[0]
//        });
//    });
});
$(document).ready(function(){
    OrderDelivery.OrderFilter();
    frm_search.on('keyup','._order_filter',function(e){
        e.preventDefault();
        if(e.keyCode == 13){
            OrderDelivery.OrderFilter();

        }
    });
    frm_search.on('keyup','._user_filter',function(e){
        e.preventDefault();
        if(e.keyCode == 13){
            OrderDelivery.OrderFilter();
        }
    });
    frm_search.on('change','._warehouse_filter',function(){
        OrderDelivery.OrderFilter();
    });
    frm_search.on('change','._time_filter',function(){
        OrderDelivery.OrderFilter();
    });
    frm_search.on('change','._warehouse_filter',function(){
        OrderDelivery.OrderFilter();
    });
    frm_search.on('click','._btn_filter',function(){
        OrderDelivery.OrderFilter();
    });
    $(document).on('click','._btn_delivery',function(e){
        if($(this).hasClass("disabled")){
            e.preventDefault();
            return;
        }
        var address_id = $(this).attr("data-address-id");
        $(this).attr("disabled",true);
        $('._loading_delivery[data-address-id='+address_id+']').show();
        OrderDelivery.Delivery(address_id);
    });



    $(document).on('click','._success_delivery',function(){
        var user_id = $(this).attr("data-user-id");
        var div_main = $(this).closest("div._order_by_address");
        div_main.remove();
        var order_address = $('._order_by_address[data-user-id='+user_id+']');
        if(order_address.length == 0 || order_address == null){
            $('._main_user[data-user-id='+user_id+']').remove();
        }
    });
});

var OrderDelivery = {
    OrderFilter : function(){
        var search_data = $('#frm_search').serialize();
        OrderDelivery.OrderSearch(search_data);
    },
    OrderSearch : function(search_data){
        $.ajax({
            url : UrlLoadOrder,
            type : "GET",
            data : search_data,
            success : function(data){
                $('#_order_content').html(order_template(data)).fadeIn();
            }
        })
    },
    Delivery: function(address_id){
        var order_list_id = "";
        var status_title = $('._status_title[data-address-id='+address_id+']');
        status_title.each(function(){
            var order_id = $(this).attr('data-order-id');
            order_list_id += ","+order_id;
        });
        var btn_delivery = $('._btn_delivery[data-address-id='+address_id+']');
        var user_id = btn_delivery.attr("data-user-id");
        var div_main = btn_delivery.closest("div._order_by_address");
        $.ajax({
            url : UrlChangeStatus,
            type : "POST",
            data : {
                order_list_id: order_list_id
            },
            success : function(data){
                $('._loading_delivery[data-address-id='+address_id+']').hide();
                btn_delivery.removeAttr("disabled");
                if(data.type == 0){
                    Common.BSAlert(data.message);
                }else{
//                    $('._body_print[data-address-id='+address_id+']').printElement();
//                    return;
                    btn_delivery.hide();
//                    $('._div_btn[data-address-id='+address_id+']').text(data.message);
//                    $('._success_delivery[data-address-id='+address_id+']').show();
                    var user_id = btn_delivery.attr("data-user-id");
                    status_title.each(function(){
                        $(this).text("Đang giao");
                        $(this).css("color","#1B9B1B");
                        $(this).css("font-weight","bold");
                        $(this).css("font-size","16px");
                    });
                    setTimeout(function(){
                        div_main.remove();
                        var order_address = $('._order_by_address[data-user-id='+user_id+']');
                        if(order_address.length == 0 || order_address == null){
                            $('._main_user[data-user-id='+user_id+']').remove();
                        }
                    },20000);
                }
            }
        })
    },
    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },
    popupPrint:function(address_id){
        var url = UrlPrint+"?address_id="+address_id;
        window.open(url);
//        var url = UrlPrint+"?address_id="+address_id;
//        var newwindow=window.open(url,'name','height=600,width=850');
//        if (window.focus) {newwindow.focus()}
//        return false;
    }
};
