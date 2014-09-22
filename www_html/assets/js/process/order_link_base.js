/**
 * Created by Admin on 2/7/14.
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

    $('img').lazyload();
//    $('#load_item').loadingbar({
//        done : function(result){
//            $('._order_link_content').html(result).fadeIn();
//            $('html,body').animate({
//                scrollTop: $('#_main_content').offset().top
//            }, 700);
//        }
//    });
    $(document).on('click','._order',function(){
        var link = $('._link_detail').val();
        if(link == ''){
            link = url;
        }
        $('._link_detail_hd').val(link);
        $('._link_detail_scroll').val(link);
        Order.loadOrder();
    });
});
var Order = {
    loadOrder : function(){
        if(ajax_rq != null){
            ajax_rq.abort();
        }
        if(typeof (url_load_order) == 'undefined'){
            return;
        }
        var link = $('._link_detail').val();

        if(link == ''){
            link = url;
        }
        link = link + '';
        if(link.match('undefined')){
            return;
        }
        var link_load = url_load_order+'?url='+link;

        var url = urlOrderLink+'?url='+encodeURIComponent(link)
        window.history.pushState({path: url}, '', url);

        ajax_rq = $.ajax({
            url : url_load_order,
            type : "GET",
            data : {
                url : encodeURIComponent(link)
            },
            success : function(data){
                $('._order_link_content').html(data).fadeIn();
                $('html,body').animate({
                    scrollTop: $('#_main_content').offset().top
                }, 700);
            }
        })
    }
}