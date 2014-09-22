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

//Quyen --- Khong duoc Xoa
$(document).ready(function(){

    $(document).on('click','._img_left',function(e){
        e.preventDefault();
        var key = $(this).attr('data-id');
        $('._img_thumb[data-id='+key+']').click();
    });

    var carousel = $('#carousel');
    if(carousel.length > 0 && carousel != null){
        $('#carousel').carouFredSel({
            responsive: false,
            width: '100%',
            prev: '#prev3',
            next: '#next3',
            lazyLoad : true,
            transition: true,
            auto: false,
            scroll : {
                duration        : 1500,
                pauseOnHover    : false,
                fx : "crossfade"
            }
        });

        $('#thumbs').carouFredSel({
            responsive: true,
            circular: false,
            infinite: false,
            auto: false,
            prev: '#prev',
            next: '#next',
            items: {
                visible: {
                    min: 6,
                    max: 6
                },
                width: 150,
                height: '66%'
            }
        });

        $('#thumbs a').click(function() {
            $('#carousel').trigger('slideTo', '#' + this.href.split('#').pop() );
            $('#thumbs a').removeClass('selected');
            $(this).addClass('selected');
            return false;
        });
    }
});
