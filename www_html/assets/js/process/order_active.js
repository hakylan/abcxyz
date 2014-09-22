/**
 * Created by Admin on 2/15/14.
 */
parseInt()

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
    $('img').lazyload();
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
    OrderActive.loadCountOrderMenu();

    var ul_active = $('._order_active_main');
    if(ul_active != null && ul_active.length > 0){
        OrderActive.orderFilter('');
    }else{
        return;
    }

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
        OrderActive.orderFilter();
    });

    $(document).on('click','._btn_filter',function(){
        OrderActive.orderFilter();
    });

    $(document).on('click','._page_order',function(){
        var page = $(this).attr('data-page-id');
        $('._li_page').removeClass("active");
        $(this).parent().addClass("active");
        $('._page').val(page);
        OrderActive.orderFilter();
    });

    $(document).on('click','._tab',function(){
        var data_status = $(this).attr('data-status');
        $('._select_status').val(data_status);
        $('._page').val(1);
        $('._order_active_main').hide();
        $('._order_active_main[data-status="'+data_status+'"]').fadeIn();
        OrderActive.orderFilter();
    });
    
    $('#_search').on('keyup','._keyword',function(e){
        if(e.keyCode == 13){
            OrderActive.orderFilter();
        }
    });
});

var OrderActive = {
//    loadOrderActive : function(status){
//        $('._loading').show();
//        $.ajax({
//            url: UrlLoadOrderActive,
//            data: { status: status },
//            success: function (data) {
//                $('._loading').hide();
//                var result = $.parseJSON(data);
//                $('._order_active').html(result.html_result).fadeIn();
//                $('._total_filter').text(result.total);
//            }
//        });
//    },
    loadCountOrder : function(status){
//        return 0;
        var quantity = 0;
        $.ajax({
            url: OrderActiveCount,
            type : "GET",
            data: { status: status },
            success: function (data) {

                if(data.type == 1){
                    quantity = parseInt(data.data);

                    if(quantity >=100){
                        quantity = "99+";
                    }
                    $('a._order_count[data-status='+status+']').find('span.count').text(quantity);
                }else{
                    $('a._order_count[data-status='+status+']').find('span.count').text(0);
                }

            }
        })
    },
    loadCountOrderMenu : function(){
        $('a._order_count').each(function(){
            var status = $(this).attr('data-status');
            OrderActive.loadCountOrder(status);
        });
    },
    orderFilter : function(type){

        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = OrderActiveUrl+'?'+search_data;
            OrderActive.push_state(pageUrl);
        }

        OrderActive.orderSearch(search_data);

    },
    orderSearch : function(search_data){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        $('._loading').show();
        ajax_rq = $.ajax({
            url: UrlLoadOrderActive,
            type : "POST",
            data: search_data,
            success: function (data) {
                var status = $('._select_status').val();

                if(status == STATUS_WAITING_FOR_DELIVERY){
                    $('._total_filter').text(data.total);
                    $('._total_filter_status[data-status='+status+']').text(data.total);
                    $('#tab_dcg').html(order_template(data)).fadeIn();
                    return;
                }

                var order_active_main = $('._order_active_main');
                if(order_active_main.length > 0){
                    $('._order_active_main').hide();
                    $('._order_active_main[data-status='+status+']').show();
                }
                $('._loading').hide();
                $('._order_active_main[data-status='+status+']').show();
                $('._order_active[data-status='+status+']').html(data.html_result).fadeIn();
                $('._total_filter').text(data.total);
                $('._total_filter_status[data-status='+status+']').text(data.total);
            }
        })
    },
    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    }
}