$(document).ready(function(){

//    $('.opacity-popup').css('height', $(window).height() + 'px');  
//  jquery funtion open and close my modal popup     

 
  $(window).on('load', function () {
        $('.selectpicker').selectpicker({
            'selectedText': 'cat'
        });
    });

//lady load img
    $(function() {
        $("img").lazyload();
    });
    
  var noneheader = $('.no-header');
  if(noneheader.length > 0 && noneheader != null){
      $("#page-header").addClass("noscol-header");  
  }
  //add class chat 
  var blockchat = $('.seubox-chat');
  if(blockchat.length > 0 && blockchat != null){
      $(".seu-maincontent .tab-content ").addClass("main-chat-v2");  
  }
  
  //height chat siderbar-left
  $('.seubox-chat').css('height', window.innerHeight + 'px');

//  var heightchat = $(".position-7").innerHeight() - 95;
  var heightchat = window.innerHeight - 158;
  $('.position-7 .content-box-wrapper').css('height', heightchat + 'px');
  
  $(".show-chat-item").click(function(){
    $(".content-chat-item").toggleClass("view"); 
  });
  
  //for(var i = 0; i<10;i++){
//        $('#custom-top'+i).scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[0]).offset().top - 60 });
//  }       

 // tooltip
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
    // scol to fixed
    var summaryv2 = $('.summaryv2');
      if(summaryv2.length > 0 && summaryv2 != null){
          $('.summaryv2').scrollToFixed({ marginTop: 0 });
      }
             
     var customtop = $('#custom-top');
      if(customtop.length > 0 && customtop != null){ 
          $('#custom-top').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[0]).offset().top - 60 });
          $('#custom-top1').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[1]).offset().top - 60 });
          $('#custom-top2').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[2]).offset().top - 60  });  
          $('#custom-top3').scrollToFixed({ marginTop: 0, limit: $($('.border-list-item')[3]).offset().top - 60  });  
      }

    var customtopdelivering = $('#custom-top-delivering');
    if(customtopdelivering.length > 0 && customtopdelivering != null){
        $('#custom-top-delivering').scrollToFixed({ marginTop: 0});
    }
      
     var winheight = window.innerHeight + 80;
     
     var rightchatheight = $($('.position-7')[1]).innerHeight();
     if(rightchatheight < winheight){
          $(".position-7 .summary ").removeClass("summary");
     } 
     var windowheight = window.innerHeight - 40;
     var purchasesummary = $('#summarypu');
      if(purchasesummary.length > 0 && purchasesummary != null){
          $('#summarypu').scrollToFixed({ marginTop: 65, limit: $($('.border-list-item')[0]).offset().top - windowheight });
          $('#summarypu1').scrollToFixed({ marginTop: 65, limit: $($('.border-list-item')[1]).offset().top - windowheight });
          $('#summarypu2').scrollToFixed({ marginTop: 65, limit: $($('.border-list-item')[2]).offset().top - windowheight  });  
          $('#summarypu3').scrollToFixed({ marginTop: 65, limit: $($('.border-list-item')[3]).offset().top - windowheight  });  
      }
      
      
}); 

