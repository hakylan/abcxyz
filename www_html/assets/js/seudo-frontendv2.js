$(document).ready(function(){
//        Onclick hiện nhập trạng thái lỗi của khiếu nại
        $(".complaint .click-error").click(function(){
            $(".module-complaint-step3").addClass("slow");
        });



//      Onclick hiện nhập tiền đơn hàng chờ giao
        $(".awaiting-block").click(function(){
            $(".awaiting-none").slideToggle("slow");
            $(".modal-footer.v1").toggleClass("none");
          });


        $(".notifi ").click(function(){
            $(".awaiting-none").slideToggle("slow");
            $(".modal-footer.v1").toggleClass("none");
        });
      
         //Onclick hiện nhập tiền đơn hàng chờ giao
        $(".check-exp .close-exp ").click(function(){
            $(".check-exp").addClass("none");
        });
//        End hiện input nhập tiền Đơn hàng chờ giao
      $(window).scroll(function(){ 
            var y = $(window).scrollTop();
            //alert(y);
            if(y>20){                         
                $(".container.header-top .item-top-header.dropdown").removeClass("open"); 
            }
            if(y<150){                         
                $(".header-top.scoll .item-top-header.dropdown").removeClass("open"); 
            }  
        });
                                                
      
      $(window).scroll(function(){ 
            var y = $(window).scrollTop();
            //alert(y); 
            if(y>610){                         
                $(".scol-chat").addClass("block-opacity"); 
                $(".scol-chat-fix").addClass("block"); 
            }
            else{
                $(".scol-chat").removeClass("block-opacity");
                $(".scol-chat-fix").removeClass("block");
            }
            
        });    
        
        $(".item-img-boder").click(function(){
            $(".slide-order-img .modal-dialog").addClass("block");
            $(".opacity-popup-linkorrder").addClass("block");
            $("body").addClass("overflow");
        });
        $(".opacity-popup-linkorrder").click(function(){
            $(".slide-order-img .modal-dialog").removeClass("block");
            $(".opacity-popup-linkorrder").removeClass("block");
            $("body").removeClass("overflow");
        });
        
        $(".slide-order-img .modal-header .close").click(function(){
            $(".slide-order-img .modal-dialog").removeClass("block");
            $(".opacity-popup-linkorrder").removeClass("block");
            $("body").removeClass("overflow");
        });
        
        //click history
        $(".page-history .module-listitem-ct").click(function(){
            $(".page-history .cart-list-content .arow-up").toggleClass("block");
            $(".page-history .block-item-ct").toggleClass("block");
        });
//        end click history

    $( document ).ajaxComplete(function() {
        $('.opacity-popup-linkorrder').css('height', $(window).height() + 'px');
        $('.scol-chat-fix').css('height', window.innerHeight + 'px');
        $('.scol-chat-fix .module-ct').css('height', window.innerHeight - 55 + 'px');
//        alert(window.innerHeight); scol-chat

        var delaybody = $(".item-delay .module-body").height();
        $('.scol-chat .single-chat').css('height', delaybody  + 'px');
        $('.scol-chat .single-chat .mychat .module-ct').css('height', (delaybody - 45 )  + 'px');
    });


        $(".comment-kd .note").click(function(){
    $(".comment-kd .note-submit").addClass("note-submit-block"); 
    $(".comment-kd .note").addClass("note-none"); 
  });
  $(".comment-kd .note-submit a").click(function(){
    $(".comment-kd .note").removeClass("note-none");
    $(".comment-kd .note-submit").removeClass("note-submit-block");
  });
  $( ".note-submit input" ).keypress(function(e) {
      if(e.keyCode == 13) {
         $(".comment-kd .note").removeClass("note-none");
         $(".comment-kd .note-submit").removeClass("note-submit-block");
      }
    
    });
    
    $(window).on('load', function () {
            $('.selectpicker').selectpicker({
                'selectedText': 'cat'
            });
//            $('.fileinput').fileinput();

            // $('.selectpicker').selectpicker('hide');
        });
        
        $(function() {
        $( "#datepicker" ).datepicker();
        $( "#datepicker2" ).datepicker();
    });





});

