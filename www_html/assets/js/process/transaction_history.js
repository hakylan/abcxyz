/**
 * Created by Quyen Minh on 2/25/14.
 */

/**
 * Created by Admin on 2/15/14.
 */
var ajax_rq = null;
$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#datepicker2" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
});
$(window).on('load', function () {

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
//            $('.fileinput').fileinput();

    // $('.selectpicker').selectpicker('hide');
});

$(document).ready(function(){
    $(document).on('click','.page-history .module-listitem-ct',function(){
        var transaction_id = $(this).attr('data-transaction-id');
        $(".page-history .cart-list-content .arow-up[data-transaction-id="+transaction_id+"]").toggleClass("block");
        $(".page-history .block-item-ct[data-transaction-id="+transaction_id+"]").toggleClass("block");
    });
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
    var ul_active = $('._transaction_content');
    if(ul_active != null && ul_active.length > 0){
        UserTransaction.transactionFilter('');
    }else{
        return;
    }

    $(document).on('click','._time_before',function(event){
        var data_time = $(this).attr('data-time');
        data_time = parseInt(data_time);
        if(data_time < 0){
            $('._from').val('');
            $('._to').val('');
            UserTransaction.transactionFilter();
            return false;
        }
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth()+1;
        var y = date.getFullYear();
        var _time_before = date.getTime() - (24*60*60*1000*data_time);
        var date_before = new Date(_time_before);
        var to = d+'-'+m+'-'+y;
        var month = date_before.getMonth() + 1;
        var from = date_before.getDate()+'-'+month+'-'+date_before.getFullYear();
        $('._from').val(from);
        $('._to').val(to);
        UserTransaction.transactionFilter();
    });

    $(document).on('click','._btn_filter_transuction',function(){
        $('._order_active').html('');
        UserTransaction.transactionFilter();
    });

    $(document).on('click','._tab',function(){
        var data_id = $(this).attr('data-id');
        var data_status = $(this).attr('data-status');
        $('._select_status').val(data_status);
        UserTransaction.transactionFilter();
    });

    $(document).on('click','._page_order',function(){
        var page = $(this).attr('data-page-id');
        $('._page').val(page);
        $('._li_page').removeClass('active');
        $('._li_page[data-page-id='+page+']').addClass('active')
        UserTransaction.transactionFilter();
    });
});

var UserTransaction = {
    transactionFilter : function(type){
        $('._order_active_main').hide();
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = UserTransactionUrl+'?'+search_data;
            UserTransaction.push_state(pageUrl);
        }

        UserTransaction.orderSearch(search_data);

    },
    orderSearch : function(search_data){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        ajax_rq = $.ajax({
            url: UrlLoadTransaction,
            type : "POST",
            data: search_data,
            success: function (data) {
                var result = $.parseJSON(data);
                $('._transaction_content').html(result.html_result);
                $('._total_filter').text(result.total);

            }
        })
    },
    push_state:function(pageurl){
        if(pageurl != window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    }
}