
$(function() {
    var home_slideshow = $('#home-slideshow');
    if(home_slideshow.length > 0){
        home_slideshow.carouFredSel({
            responsive: true,
            width: '100%',
            prev: '#prev3',
            next: '#next3',
            lazyLoad : true,
            transition: true,
            auto: 3000,
            scroll : {
                duration        : 2500,
                pauseOnHover    : true,
                fx : "crossfade"
            }


        });
    }

    var home_slideheader = $('#home-slideheader');
    if(home_slideheader.length > 0){
        home_slideheader.owlCarousel({

                            itemsDesktop : [1599,1],
                            itemsDesktopSmall : [980,1],
                            navigation : true,
                            pagination : false,
                            responsive: true,
                            autoPlay : 3000,
                            slideSpeed : 2000,  

                            paginationSpeed : 3000,
                            goToFirstSpeed : 3000,
                            singleItem : true,
                            autoHeight : true,
                            transitionStyle:"fade",
                            stopOnHover : true,
                            });
    }

});
$(window).scroll(function(){
    var y = $(window).scrollTop();
    //alert(y);
    if(y>185){
        $(".scoll").addClass("block");
        $("body").addClass("modal-block");
    }
    else{
        jQuery('.scoll').removeClass("block");
        jQuery('body').removeClass("modal-block");
    }
});

$('.inputregister').each(function() {
    $(this).hover(function() {
        $(this).parent().find('.tooltipregister').css('display', 'inline-block');
    }).focus(function() {
            $(this).parent().find('.tooltipregister').css('display', 'inline-block');
        }).blur(function() {
            $(this).parent().find('.tooltipregister').css('display', 'none');
        }).mouseout(function() {
            $(this).parent().find('.tooltipregister').css('display', 'none');
        });
});

/*
$(window).on('load', function () {

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
});*/
