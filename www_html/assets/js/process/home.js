$(document).ready(function(){
    $(document).on('click','.buy_item',function() {
        var link = $(this).prev().val();
        location.href = base_url + '/order_link?url='+encodeURIComponent(link);
        return false;
    });
    /*$(document).on('keyup','.totalAmount',function(){
        $(".totalAmount").number(true,0);
    });*/


    $(document).on('click','.ads_banner',function (event) {
        event.preventDefault();
        location.href = $(this).attr('data-link');
    });

    $(document).on('hover','.ads_banner',function (event) {
        event.preventDefault();
        $(this).css('cursor','pointer');
    });


});
$(function() {

    $('#home-slideshow').carouFredSel({
        responsive: true,
        width: '100%',
        prev: '#prev3',
        next: '#next3',
        auto: true
    });
});