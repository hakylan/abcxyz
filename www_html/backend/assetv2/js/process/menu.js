/**
 * Created by hosi on 5/21/14.
 */

/**
 * Created by hosi on 5/21/14.
 */

$(document).ready(function(){
    $('a.navbar-nav-a').hover(function(){
        $('.custom-nav-dropdown').hide();
        $('._custom-navbar-nav li.first').removeClass('dropdown-toggle open');
        $('.dropdown-menu-item-arrow').addClass('hidden');

        var $this = $(this).parent();


        $this.addClass('dropdown-toggle open');
        $this.find('.custom-nav-dropdown').show();
        $this.find('.dropdown-menu-item-arrow').removeClass('hidden');
    });

    $('.custom-nav-dropdown').mouseleave(function(){
        $(this).hide();
        $(this).parent().find('.dropdown-menu-item-arrow').addClass('hidden');
    });

    $('._custom-navbar-nav li').mouseleave(function(){
        $(this).find('.custom-nav-dropdown').hide();
        $(this).find('.dropdown-menu-item-arrow').addClass('hidden');
        $(this).removeClass('open');
    });

    var menuchat = $('.sidebar-left.seubox-chat');
    if(menuchat.length > 0 && menuchat != null){
        $('body').addClass("menu-chat");
    }

    $(window).scroll(function(){
        var y = $(window).scrollTop();
        if(y>10){
            $(".module-back-top").removeClass("hidden");
        }
        if(y<10){
            $(".module-back-top").addClass("hidden");
        }
    });
    $( ".module-back-top" ).click(function() {
        $('html, body').animate({
            scrollTop: $(".header").offset().top
        });
    });
});
