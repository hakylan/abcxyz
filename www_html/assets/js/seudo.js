$(document).ready(function(){ 
        $( "#draggable" ).draggable();
        
        var height = $('#draggable').height();
        var width = $('#draggable').width();
        if(width>height){
            $(".ui-draggable").addClass("left");
            
        }
        if(width<height){
            $(".ui-draggable").addClass("top"); 
        }
        
        $(".saveavatar").click(function(){
        var imgwidth =$("#demotimg").width(); 
        var imgheight =$("#demotimg").height(); 
        if(imgwidth>imgheight)  {
             imgsave = imgwidth - imgheight; 
        }
        if(imgwidth<imgheight)  {
             imgsave = imgheight-  imgwidth;
        }
        
        var x =$("#draggable").position();
        var top = x.top+"px";
        alert(top);
        var x =$("#draggable").position();
        var left = x.left+"px";
        alert(left);
        });
        
      $(".click-resetpass").click(function(){
        $(".reset-password-block").addClass("block");
      });
      $(".reset-password-back").click(function(){
        $(".reset-password-block").removeClass("block");
      });
      $(".page-login-footer .click-resetpass-login").click(function(){
        $(".reset-password").toggleClass("block");
    });
});
$(window).on('load', function () {
    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
});

/**
 * Login from taskbar
 */
$('#bt-login-top').click(function (event) {
    event.preventDefault();
    var formLoginTop = $('#frm-login-top');
    formLoginTop.find('.re-error').hide();
    if(formLoginTop.find('input[name=credential]').val().replace(/\s+/, '') == '')
    {
        formLoginTop.find('.err-cred').html('Bạn chưa điền tên đăng nhập').show();
        return;
    }
    if(formLoginTop.find('input[name=credential]').val().replace(/\s+/, '').length <3)
    {
        formLoginTop.find('.err-cred').html('Tên đăng nhập từ 4 tới 15 ký tự').show();
        return;
    }
    if(formLoginTop.find('input[name=password]').val().replace(/\s+/, '') == '')
    {
        formLoginTop.find('.err-pass').html('Bạn chưa điền mật khẩu').show();
        return;
    }
    if(formLoginTop.find('input[name=password]').val().replace(/\s+/, '').length<6)
    {
        formLoginTop.find('.err-pass').html('Mật khẩu lớn hơn 5 ký tự').show();
        return;
    }

    $.ajax({
        url: url_login,
        type: 'POST',
        data: formLoginTop.serialize(),
        success: function(result) {
            if (result.type == 'SUCCESS') {
                alert('Đăng nhập thành công');
                $(location).attr('href',base_url);
            }else{
                formLoginTop.find('.err-ajax').html(result.message).show();
                return false;
            }
        }
    });
});

$('#frm-login-top').find('input').keyup(function (e) {
    if (e.keyCode == 13) {
        $('#bt-login-top').click();
    }
});
//
$(".click-resetpass").click(function(){
    $(".reset-password-block").addClass("block");
});
$(".reset-password-back").click(function(){
    $(".reset-password-block").removeClass("block");
});

    //Avatar for Lanhk
    var height = $('#draggable').height();
    var width = $('#draggable').width();
    if(width>height){
        $(".ui-draggable").addClass("left");

    }
    if(width<height){
        $(".ui-draggable").addClass("top");

    }

    $(".saveavatar").click(function(){

    var x =$("#draggable").position();
    var top = x.top+"px";
    alert(top);
    var x =$("#draggable").position();
    var left = x.left+"px";
    alert(left);
    });

  $(".click-resetpass").click(function(){
    $(".reset-password-block").addClass("block");
  });
  $(".reset-password-back").click(function(){
    $(".reset-password-block").removeClass("block");
  });
  $(".page-login-footer .click-resetpass-login").click(function(){
    $(".reset-password").toggleClass("block");
    $(".page-login-footer .click-resetpass-login").toggleClass("up");
  });
    //End: Avatar for Lanhk



