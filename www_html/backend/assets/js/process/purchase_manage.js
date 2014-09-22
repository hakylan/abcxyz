/**
 * Created by Admin on 2/9/14.
 */
var ajax_rq = null;
$(document).ready(function(){
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
            //$('._send_cart').addClass('load-wait');
        },
        complete:function(){
            $('._loading').hide();
            //$('._send_cart').removeClass('load-wait').html('Tiếp tục<span class="arow-next"></span>');
        }
    });

    Purchase.loadPurchaseList("tab_all");
    //end popup

    $(document).on('click','.btnFilter',function(){
        Purchase.rmClassActive();
        $(this).addClass('active');
    });

    $(document).on('click','._tab',function(){
        var data_id = $(this).attr('data-id');
        var data_status = $(this).attr('data-status');
        $('._status').val(data_status);
        $('._status').attr("data-id",data_id);
        $('._page').val(1);
        Purchase.loadPurchaseList(data_id);
    });
    $(document).on('click','._add_fre',function(){
        var order_id = $(this).attr('data-order-id');
        Purchase.hideInput(order_id);
        $(this).hide();
        $('._input_add_fre[data-order-id='+order_id+']').show();
        $('._input_add_fre[data-order-id='+order_id+']').focus();
    });
    $(document).on('click','._edit_fre',function(){

        var order_id = $(this).attr('data-order-id');
        Purchase.hideInput(order_id);
        var data_id = $(this).attr('data-id');
        $(this).hide();
        $('._span_fre[data-id='+data_id+']').hide();
        $('._input_edit_fre[data-id='+data_id+']').show();
        $('._input_edit_fre[data-id='+data_id+']').focus();
    });
    $(document).on('click','._add_inv',function(){
        var order_id = $(this).attr('data-order-id');
        Purchase.hideInput(order_id);
        $(this).hide();
        $('._input_add_inv[data-order-id='+order_id+']').show();
        $('._input_add_inv[data-order-id='+order_id+']').focus();
    });
    $(document).on('click','._edit_inv',function(){

        var order_id = $(this).attr('data-order-id');
        Purchase.hideInput(order_id);
        var data_id = $(this).attr('data-id');
        $(this).hide();
        $('._span_inv[data-id='+data_id+']').hide();
        $('._input_edit_inv[data-id='+data_id+']').show();
        $('._input_edit_inv[data-id='+data_id+']').focus();
    });
    
    $(document).on('change','._select_tellers',function(e){
        e.preventDefault();
        var order_id = $(this).attr('data-order-id');
        var teller_id = $(this).val();
        $('._select_tellers[data-order-id='+order_id+']').hide();
        $('._img_loading[data-order-id='+order_id+']').show();
        $.ajax({
            url: LinkSelectPurchasers,
            type: 'POST',
            data:{ order_id : order_id, teller_id : teller_id},
            success:function(result) {
                var data = $.parseJSON(result);
                $('._img_loading[data-order-id='+order_id+']').hide();
                if(data.type == 1){

                    $('._div_tellers[data-order-id='+order_id+']').html(data.html);
                }else{
                    alert(data.message);
                }
            }
        });
    });

    $(document).on('click','.skips_purchasers',function(){
        $(this).hide();
        var order_id = $(this).attr('data-order-id');
        Purchase.skipsPurchase(order_id,'tellers');
    });

    $(document).on('click','._skips_paid_staff',function(){
        var order_id = $(this).attr('data-order-id');
        Purchase.skipsPurchase(order_id,'paid');
    });

    $(document).on('click','._save_config',function(){
        var key = $(this).attr('data-key');
        $(this).addClass("disabled");
        $('._input_config[data-key='+key+']').addClass('disabled');
        var value = $('._input_config[data-key='+key+']').val();
        Purchase.saveConfig(key,value);
    });
    
    $(document).on('keyup','._input_config',function(e){
        if(e.keyCode==13){
            var key = $(this).attr('data-key');
            $(this).addClass("disabled");
            $('._save_config[data-key='+key+']').addClass('disabled');
            var value = $(this).val();
            Purchase.saveConfig(key,value);
        }
    });
    
    $(document).on('click','._page_order',function(){
        var page = $(this).attr('data-page-id');
        $('._page').val(page);
        Purchase.loadPurchaseList();
    });
});

var Purchase = {
    rmClassActive : function(){
        $('.btnFilter').removeClass('active');
    },
    loadPurchaseList : function(data_id){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        if(data_id == null){
            data_id = $('._status').attr('data-id');
        }
        var data =  $('#frm_search').serialize();
        ajax_rq = $.ajax({
            url: LinkPurchaseLoad,
            type: 'POST',
            data: data,
            success:function(result) {
                $('._tbody_content[data-id='+data_id+']').fadeOut().html(result).fadeIn();
            }
        });
    },

    hideInput : function(order_id){
        $('._input_edit_fre[data-order-id='+order_id+']').hide();
        $('._input_add_fre[data-order-id='+order_id+']').hide();
        $('._input_edit_inv[data-order-id='+order_id+']').hide();
        $('._input_add_inv[data-order-id='+order_id+']').hide();
    },
    saveConfig : function(key,value){
        $.ajax({
            url: LinkSaveConfig,
            type: 'POST',
            data:{ key : key,value : value },
            success:function(result) {
                var data = $.parseJSON(result);
                $('._save_config[data-key='+key+']').removeClass("disabled");
                $('._input_config[data-key='+key+']').removeClass('disabled');
//                $('._tbody_content[data-id='+data_id+']').html(result).fadeIn();
            }
        });
    },
    skipsPurchase : function(order_id,type){
        if(type == 'tellers'){
            $('._div_tellers_detail[data-order-id='+order_id+']').fadeOut();
            $('._select_tellers[data-order-id='+order_id+']').show();
            $('._select_tellers[data-order-id='+order_id+']').val(0);
        }else{
            $('._td_paid_staff[data-order-id='+order_id+']').text('...');
        }

        $.ajax({
            url: LinkSkipsPurchasers,
            type: 'POST',
            data:{ order_id : order_id, type:type},
            success:function(result) {
                var data = $.parseJSON(result);
                if(type == 'tellers'){
                    if(data.type == 1){
                        $('._div_tellers_detail[data-order-id='+order_id+']').remove();
                    }else{
                        $('._div_tellers_detail[data-order-id='+order_id+']').fadeIn();
                        alert(data.message);
                    }
                }else{

                }

            }
        });
    }
}
