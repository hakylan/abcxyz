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
//    $( "#datepicker" ).datepicker();
//    $( "#datepicker1" ).datepicker();
//    $( "#datepicker" ).datepicker({
//        dateFormat: 'dd-mm-yy'
//    });
//    $( "#datepicker2" ).datepicker({
//        dateFormat: 'dd-mm-yy'
//    });

    Order.orderFilter('');

    $(document).on('click','._open_modal_confirm',function(){
        var order_id = $(this).attr('data-order-id');
        $('._del_order').attr('data-order-id',order_id);
        $('._modal_confirm').addClass('bg-black');
        $('._modal_confirm').removeClass('hide');
        $('._modal').show();
    });
    $(document).on('click','._close_modal',function(){
        $('._modal_confirm').removeClass('bg-black');
        $('._modal_confirm').addClass('hide');
        $('._modal').hide();
    });
    //end popup

    
    $(document).on('click','.btnFilter',function(){
        Order.rmClassActive();
        $(this).addClass('active');
    });
    
    $(document).on('click','._del_order',function(){
        var order_id = $(this).attr('data-order-id');
        $('._tr_order[data-order-id='+order_id+']').fadeOut();
        Order.deleteOrder(order_id);
    });

    $(document).on('click','._time_before',function(event){
        var data_time = $(this).attr('data-time');
        data_time = parseInt(data_time);
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth()+1;
        var y = date.getFullYear();
        var _time_before = date.getTime() - (24*60*60*1000*data_time);
        var date_before = new Date(_time_before);
        var to = d+'-'+m+'-'+y;
        var month = date_before.getMonth() + 1;
        var from = date_before.getDate()+'-'+month+'-'+date_before.getFullYear();
        console.log(from);
        $('._from').val(from);
        $('._to').val(to);
        Order.orderFilter();
    });

    $(document).on('click','._order_history',function(){
        var order_id = $(this).attr('data-order-id');
        Order.loadOrderHistory(order_id);
    });

    $(document).on('click','._tab',function(){
        var data_id = $(this).attr('data-id');
        $('._btn_search').attr('data-id',data_id);
        var data_status = $(this).attr('data-status');
        $('._select_status_search').val(data_status);
        Order.orderFilter(null,1);
//        Order.loadOrderList(data_id,data_status);
    });


    //thêm sửa xóa mã vận đơn và mã hóa đơn
    $(document).on('keyup','._input_edit_fre',function(e){
        if(e.keyCode==13){
            var order_id = $(this).attr('data-order-id');
            var data_id = $(this).attr('data-id');
            var new_fre = $(this).val();
            var old_fre = $(this).attr('data-text');

            Order.updateFreightBill(order_id,new_fre,old_fre,data_id);
        }
    });
    $(document).on('click','._save_edit_fre',function(){
        var order_id = $(this).attr('data-order-id');
        var data_id = $(this).attr('data-id');
        var new_fre = $('._input_edit_fre[data-id='+data_id+']').val();
        var old_fre = $('._input_edit_fre[data-id='+data_id+']').attr('data-text');

        Order.updateFreightBill(order_id,new_fre,old_fre,data_id);
    });
    $(document).on('click','._save_add_fre',function(){
        var order_id = $(this).attr('data-order-id');
        var new_fre = $('_input_add_fre[data-order-id='+order_id+']').val();

        Order.addFreightBill(order_id,new_fre,'');
    });
    $(document).on('click','._save_edit_inv',function(){
        var order_id = $(this).attr('data-order-id');
        var data_id = $(this).attr('data-id');
        var new_inv = $('._input_edit_inv[data-id='+data_id+']').val();
        var old_inv = $('._input_edit_inv[data-id='+data_id+']').attr('data-text');

        Order.updateInvoice(order_id,new_inv,old_inv,data_id);
    });
    $(document).on('click','._save_add_inv',function(){
        var order_id = $(this).attr('data-order-id');
        var new_inv = $('._input_edit_inv[data-order-id='+order_id+']').val();

        Order.addInvoice(order_id,new_inv,'');
    });

    $(document).on('keyup','._input_add_fre',function(e){
        if(e.keyCode==13){
            var order_id = $(this).attr('data-order-id');
            var new_fre = $(this).val();

            Order.addFreightBill(order_id,new_fre,'');
        }
    });

    $(document).on('keyup','._input_add_inv',function(e){
        if(e.keyCode==13){
            var order_id = $(this).attr('data-order-id');
            var new_inv = $(this).val();

            Order.addInvoice(order_id,new_inv,'');
        }
    });

    $(document).on('keyup','._input_edit_inv',function(e){
        if(e.keyCode==13){
            var order_id = $(this).attr('data-order-id');
            var data_id = $(this).attr('data-id');
            var new_inv = $(this).val();
            var old_inv = $(this).attr('data-text');

            Order.updateInvoice(order_id,new_inv,old_inv,data_id);
        }
    });

    // end


    // event click edit and add
    $(document).on('click','._add_fre',function(){
        var order_id = $(this).attr('data-order-id');
        Order.hideInput(order_id);
        Order.showDivFre(order_id);
        $(this).hide();
        $('._div_add_fre[data-order-id='+order_id+']').show();
        $('._input_add_fre[data-order-id='+order_id+']').focus();

    });
    
    $(document).on('click','._edit_fre',function(){

        var order_id = $(this).attr('data-order-id');
        Order.hideInput(order_id);
        Order.showDivFre(order_id);
        var data_id = $(this).attr('data-id');
        $(this).hide();
        $('._span_fre[data-id='+data_id+']').hide();
        $('._div_edit_fre[data-id='+data_id+']').show();
        $('._input_edit_fre[data-id='+data_id+']').focus();
        $('._input_edit_fre[data-id='+data_id+']').select();
    });
    $(document).on('click','._add_inv',function(){
        var order_id = $(this).attr('data-order-id');
        Order.hideInput(order_id);
        Order.showDivFre(order_id);
        $(this).hide();
        $('._div_add_inv[data-order-id='+order_id+']').show();
        $('._input_add_inv[data-order-id='+order_id+']').focus();
    });
    $(document).on('click','._edit_inv',function(){

        var order_id = $(this).attr('data-order-id');
        Order.hideInput(order_id);
        Order.showDivFre(order_id);
        var data_id = $(this).attr('data-id');
        $(this).hide();
        $('._span_inv[data-id='+data_id+']').hide();
        $('._div_edit_inv[data-id='+data_id+']').show();
        $('._input_edit_inv[data-id='+data_id+']').focus();
        $('._input_edit_inv[data-id='+data_id+']').select();
    });
    
    // Search
    
    $(document).on('click','._order_by',function(e){
        e.preventDefault();
        var order_by = $(this).attr('data-order-by');
        $('._input_order_by').val(order_by);
        Order.orderFilter();
    });
    
    $(document).on('click','._btn_search',function(e){
        e.preventDefault();
        Order.orderFilter();
    });
    
    $(document).on('click','._checkbox_kho',function(e){
        Order.orderFilter();
    });
    
    $(document).on('change','._select_staff',function(){
        Order.orderFilter();
    });
});

var Order = {
    rmClassActive : function(){
        $('.btnFilter').removeClass('active');
    },

    addFreightBill : function(order_id,new_fre,old_fre){
        if(new_fre == ''){
            return
        }
        $('._div_add_fre[data-order-id='+order_id+']').hide();
        $('._input_add_fre[data-order-id='+order_id+']').val('');
        $('._add_fre[data-order-id='+order_id+']').show();
        var key = Math.floor((Math.random()*100)+1);
        key = key + '';
        order_id = order_id + '';
        var data_id = order_id + key;
        Order.setTempFre(order_id,new_fre,old_fre,data_id);
        var tem_fre = $('._temp_fre');
        var fre = tem_fre.html();
        tem_fre.find('._div_fre_one').attr("data-id","0");
        tem_fre.find('._span_fre').attr("data-id","0");
        tem_fre.find('._edit_fre').attr("data-id","0");
        tem_fre.find('._div_edit_fre').attr("data-id","0");
        tem_fre.find('._save_edit_fre').attr("data-id","0");
        tem_fre.find('._input_edit_fre').attr("data-id","0");
        $('._div_fre[data-order-id='+order_id+']').append(fre);
        $('._input_edit_fre[data-id='+data_id+']').val(new_fre);
        $.ajax({
            url: LinkEditFre,
            type: 'POST',
            data:{ order_id : order_id ,old_fre : old_fre ,new_fre : new_fre},
            success:function(result) {
                var data = $.parseJSON(result);

                if(data.type == 1){

                }else{
                    alert(data.message);
                }
            }
        });
    },
    updateFreightBill : function(order_id,new_fre,old_fre,data_id){
        if(new_fre == ''){
            $('._div_fre_one[data-id='+data_id+']').remove();
        }else{
            $('._edit_fre[data-id='+data_id+']').attr('data-text',new_fre);
            $('._edit_fre[data-id='+data_id+']').show();
            $('._span_fre[data-id='+data_id+']').text(new_fre);
            $('._span_fre[data-id='+data_id+']').attr(new_fre);
            $('._span_fre[data-id='+data_id+']').show();
            $('._div_edit_fre[data-id='+data_id+']').hide();
            $('._div_edit_fre[data-id='+data_id+']').attr('data-text',new_fre);
        }

        $.ajax({
            url: LinkEditFre,
            type: 'POST',
            data:{ order_id : order_id ,old_fre : old_fre ,new_fre : new_fre},
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.type == 1){

                }else{
                    alert(data.message);
                }
            }
        });
    },
    updateInvoice : function(order_id,new_inv,old_inv,data_id){
        console.log(new_inv +"--new_inv");

        if(new_inv == ''){
            $('._div_inv_one[data-id='+data_id+']').remove();
        }

        $('._edit_inv[data-id='+data_id+']').attr('data-text',new_inv);
        $('._edit_inv[data-id='+data_id+']').show();
        $('._span_inv[data-id='+data_id+']').text(new_inv);
        $('._span_inv[data-id='+data_id+']').attr(new_inv);
        $('._span_inv[data-id='+data_id+']').show();
        $('._div_edit_inv[data-id='+data_id+']').hide();
        $('._div_edit_inv[data-id='+data_id+']').attr('data-text',new_inv);
        $.ajax({
            url: LinkEditInv,
            type: 'POST',
            data:{ order_id : order_id ,old_inv : old_inv ,new_inv : new_inv},
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.type == 1){
                }else{
                    alert(data.message);
                }
            }
        });
    },
    addInvoice : function(order_id,new_inv,old_inv){
        console.log(new_inv+"_--new_inv");
        if(new_inv == ''){
            return
        }
        $('._div_add_inv[data-order-id='+order_id+']').hide();
        $('._input_add_inv[data-order-id='+order_id+']').val('');
        $('._add_inv[data-order-id='+order_id+']').show();
        var key = Math.floor((Math.random()*100)+1);
        key = key + '';
        order_id = order_id + '';
        var data_id = order_id + key;
        Order.setTempInv(order_id,new_inv,order_id,data_id);

        var temp_inv = $('._temp_inv');
        var inv = temp_inv.html();
        console.log(inv);
        temp_inv.find('._div_inv_one').attr("data-id","0");
        temp_inv.find('._span_inv').attr("data-id","0");
        temp_inv.find('._edit_inv').attr("data-id","0");
        temp_inv.find('._div_edit_inv').attr("data-id","0");
        temp_inv.find('._save_edit_inv').attr("data-id","0");
        temp_inv.find('._input_edit_inv').attr("data-id","0");
        $('.div_invoice[data-order-id='+order_id+']').append(inv);
        $('._input_edit_inv[data-id='+data_id+']').val(new_inv);
        $.ajax({
            url: LinkEditInv,
            type: 'POST',
            data:{ order_id : order_id ,old_inv : old_inv ,new_inv : new_inv},
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.type == 1){
                }else{
                    alert(data.message);
                }
            }
        });
    },

    deleteOrder : function(order_id){
        $.ajax({
            url : LinkDelOrder,
            type : "POST",
            data : {
                order_id : order_id
            },
            success : function (data){
                var result = $.parseJSON(data);

                if(result.type == 1){
                    $('._tr_order[data-order-id='+order_id+']').remove();
                }else{
                    $('._tr_order[data-order-id='+order_id+']').slideDown();
                    alert(result.message);
                }
            }
        });
    },
    loadOrderHistory : function(order_id){
        $.ajax({
            url : LinkLoadOrderHistory,
            type : "get",
            data : {
                order_id:order_id
            },
            success : function(result){
                var data = $.parseJSON(result);
                if(data.type == 1){
                    $('._order_history_content[data-order-id='+order_id+']').html(data.html).fadeIn();
                }else{
                    alert(data.message);
                }
            }
        });
    },

    loadOrderList : function(data_id,data_status){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        ajax_rq = $.ajax({
            url: LinkOrderLoad,
            type: 'POST',
            data:{ status : data_status },
            success:function(result) {
                $('._tbody_content[data-id='+data_id+']').fadeOut().html(result).fadeIn();
            }
        });
    },

    orderFilter : function(type,is_tab){

        var data_value = $('._select_status_search').val();//$('._tab[tabindex="0"]').attr('data-id');

        var data_id = $('._tab[data-status='+data_value+']').attr('data-id');
        var search_data = $('#frm_search_order').serialize();
        if(type == null){
            var pageUrl = OrderManageUrl+'?'+search_data;
            Order.push_state(pageUrl);
        }

        Order.orderSearch(data_id,search_data,is_tab);

    },

    orderSearch : function(data_id,search_data,is_tab){
        Order.removeClassActive();
        $('._tab[data-id='+data_id+']').addClass('ui-tabs-active');
        $('._tab[data-id='+data_id+']').addClass('ui-state-active');
        if(is_tab == null){
            data_id = $('._btn_search').attr('data-id');
        }

        if(data_id == ''){
            data_id = 'tab_all';
        }

        if(ajax_rq != null){
            ajax_rq.abort();
        }
        $('._loading').show();
        ajax_rq = $.ajax({
            url: LinkOrderLoad,
            type : "POST",
            data: search_data,
            success: function (result) {
                $('._loading').hide();
                $('._tbody_content[data-id='+data_id+']').fadeOut().html(result).fadeIn();
            }
        })
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    hideInput : function(order_id){
        $('._div_edit_fre[data-order-id='+order_id+']').hide();
        $('._div_add_fre[data-order-id='+order_id+']').hide();
        $('._div_edit_inv[data-order-id='+order_id+']').hide();
        $('._div_add_inv[data-order-id='+order_id+']').hide();
    },
    showDivFre : function(order_id){
        $('._span_fre[data-order-id='+order_id+']').show();
        $('._edit_fre[data-order-id='+order_id+']').show();
        $('._add_fre[data-order-id='+order_id+']').show();
        $('._span_inv[data-order-id='+order_id+']').show();
        $('._edit_inv[data-order-id='+order_id+']').show();
        $('._add_inv[data-order-id='+order_id+']').show();
    },
    setTempFre : function(order_id,new_fre,old_fre,data_id){
        $('._temp_fre').find('._div_fre_one').attr('data-order-id',order_id);
        $('._temp_fre').find('._div_fre_one').attr('data-id',data_id);
        $('._temp_fre').find('span._span_fre').attr('data-order-id',order_id);
        $('._temp_fre').find('span._span_fre').attr('data-text',new_fre);
        $('._temp_fre').find('span._span_fre').attr('data-id',data_id);
        $('._temp_fre').find('span._span_fre').text(new_fre);
        $('._temp_fre').find('span._span_fre').show();
        $('._temp_fre').find('a._edit_fre').attr('data-order-id',order_id);
        $('._temp_fre').find('a._edit_fre').attr('data-text',new_fre);
        $('._temp_fre').find('a._edit_fre').attr('data-id',data_id);
        $('._temp_fre').find('a._edit_fre').show();
        $('._temp_fre').find('div._div_edit_fre').attr('data-order-id',order_id);
        $('._temp_fre').find('div._div_edit_fre').attr('data-id',data_id);
        $('._temp_fre').find('div._div_edit_fre').hide();
        $('._temp_fre').find('div._save_edit_fre').attr('data-order-id',order_id);
        $('._temp_fre').find('div._save_edit_fre').attr('data-id',data_id);
        $('._temp_fre').find('._input_edit_fre').val(new_fre);
        $('._temp_fre').find('._input_edit_fre').attr('data-text',new_fre);
        $('._temp_fre').find('._input_edit_fre').attr('data-id',data_id);
        $('._temp_fre').find('._input_edit_fre').attr('data-order-id',order_id);
    },
    setTempInv : function(order_id,new_inv,old_inv,data_id){
        var temp_inv = $('._temp_inv');
        if(temp_inv.length > 0){
            temp_inv.find('._div_inv_one').attr('data-order-id',order_id);
            temp_inv.find('._div_inv_one').attr('data-id',data_id);
            temp_inv.find('span._span_inv').attr('data-order-id',order_id);
            temp_inv.find('span._span_inv').attr('data-text',new_inv);
            temp_inv.find('span._span_inv').attr('data-id',data_id);
            temp_inv.find('span._span_inv').text(new_inv);
            temp_inv.find('span._span_inv').show();
            temp_inv.find('a._edit_inv').attr('data-order-id',order_id);
            temp_inv.find('a._edit_inv').attr('data-text',new_inv);
            temp_inv.find('a._edit_inv').attr('data-id',data_id);
            temp_inv.find('a._edit_inv').show();
            temp_inv.find('div._div_edit_inv').attr('data-order-id',order_id);
            temp_inv.find('div._div_edit_inv').attr('data-id',data_id);
            temp_inv.find('div._div_edit_inv').hide();
            temp_inv.find('div._save_edit_inv').attr('data-order-id',order_id);
            temp_inv.find('div._save_edit_inv').attr('data-id',data_id);
            temp_inv.find('._input_edit_inv').val(new_inv);
            temp_inv.find('._input_edit_inv').attr('data-text',new_inv);
            temp_inv.find('._input_edit_inv').attr('data-id',data_id);
            temp_inv.find('._input_edit_inv').attr('data-order-id',order_id);
        }
    },
    removeClassActive : function(){
        $('._tab').removeClass('ui-tabs-active');
        $('._tab').removeClass('ui-state-active');
    }
}
