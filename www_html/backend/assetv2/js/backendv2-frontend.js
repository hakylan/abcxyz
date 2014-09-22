$(document).ready(function(){

//    $('textarea').textareaAutogrow();

    function goStep(step){
        $('.step-next').hide();
        $('.main-ct-step' + step).show();
    }

    $(".main-ct-step1 .click-new").click(function(){
			goStep(3);
        });
    $(".main-ct-step1 .click-step-finish").click(function(){
        goStep(4);
    });

    $('#popupbarcode').on('hidden.bs.modal', function () {
        goStep(1);
    });

    /**
     *
     * @param $btn
     * @param $content
     */
//    function test($btn, $content){
//        $btn.click(function(){
//            $this = $(this);
//            $root = $this.parents('.content-box');
//            if($btn.find('i.fa').hasClass('fa-chevron-down')){
//                $btn.find('i.fa').removeClass("fa-chevron-down");
//                $btn.find('i.fa').addClass("fa-chevron-up");
//            }else{
//                $btn.find('i.fa').addClass("fa-chevron-down");
//                $btn.find('i.fa').removeClass("fa-chevron-up");
//            }
//
//            $content.slideToggle();
//        });
//    }
//
//    test($(".content-box .up-down"), $(".content-box .content-box-wrapper"));

    var slidedown = $('.content-box .up-down .fa');
    $(".content-box .up-down").click(function(){
        var $root = $(this).parents('.content-box');
        var $target = $root.find('.up-down .fa');
        var $content = $root.find(".content-box-wrapper");

        if($target.hasClass('fa-chevron-down')){
            $target.removeClass("fa-chevron-down");
            $target.addClass("fa-chevron-up");
        } else {
            $target.addClass("fa-chevron-down");
            $target.removeClass("fa-chevron-up");
        }
        $content.slideToggle();
    });


    $(".arrow-active").click(function(){
        $(".arrow-active").toggleClass("open");
        $(".search .module-item.bottom").slideToggle();
    });


    //height chat siderbar-left
    $('.seubox-chat').css('height', window.innerHeight + 'px');

//  var heightchat = $(".position-7").innerHeight() - 95;
    var heightchat = window.innerHeight - 158;
    $('.position-7 .content-box-wrapper').css('height', heightchat + 'px');

    $(".show-chat-item").click(function(){
        $(".content-chat-item").toggleClass("view");
    });

 
  $(window).on('load', function () {
        $('.selectpicker').selectpicker({
            'selectedText': 'cat'
        }); 
    });






//    focus new popup
    $('.modal').on('shown.bs.modal', function () {
        $('.focusnew').focus();
    })
	
//	var height = $(".main-ct-step1 .dropdown-menu .selectpicker").innerHeight();
////		alert(height);
//        $(function(){
//            $('.content').slimScroll({
//                height: height
//            });
//
//
//        });
	
	$(function() {
        $( "#datepicker" ).datepicker();
        $( "#datepicker2" ).datepicker();
    });

 // tooltip
//    $('.openpopover').popover('toggle');



    $('.openpopover').popover({
        trigger: 'focus',
        html: true
    })

    $("* [rel='tooltiptop']").tooltip({
       html: true, 
       placement: 'top'
    }); 

    $("* [rel='tooltipbottom']").tooltip({
       html: true, 
       placement: 'bottom'
    }); 
    
    $("* [rel='tooltipleft']").tooltip({
       html: true, 
       placement: 'left'
    });
    
    $("* [rel='tooltipright']").tooltip({
       html: true, 
       placement: 'right'
    });



    $(function() {
//        $("img").lazyload();
    });
    
      
}); 

