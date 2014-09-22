/**
 * Created by Admin on 2/8/14.
 */
var ajax_request = null;
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
    $('._link_detail_hd').bind('keypress', function(e) {

        if(e.keyCode==13){
            loadOrderLink();
        }
    });
    $('._link_detail_scroll').bind('keypress', function(e) {
        var link = $(this).val();

        $('._link_detail_hd').val(link);

        if(e.keyCode==13){
            loadOrderLink();
        }
    });
    $(document).on('click','._order_link_scroll',function(){
        var link = $('._link_detail_scroll').val();

        $('._link_detail_hd').val(link);

        loadOrderLink();
    });
    $(document).on('click','._order_link_hd',function(){

        var link = $('._link_detail_hd').val();

        $('._link_detail_scroll').val(link);

        loadOrderLink();
    });
});

function loadOrderLink(){
    if(ajax_request != null){
        ajax_request.abort();
    }

    var link = $('._link_detail_hd').val();
    var url = urlOrderLink+'?url='+encodeURIComponent(link)
    window.history.pushState({path: url}, '', url);
    ajax_request = $.ajax({
        url : OrderLinkUrl,
        type : "GET",
        data : {
            url : encodeURIComponent(link)
        },
        success : function(data){
            $('#_main_content').html(data).show();
            $('html,body').animate({
                scrollTop: $('#_main_content').offset().top
            }, 700);
        }
    })
}
