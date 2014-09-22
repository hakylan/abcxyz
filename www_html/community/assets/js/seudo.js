$(document).ready(function(){
    $(".click-block").click(function(){
        $(".content-ct-none").addClass("block");
        $(".click-block").addClass("none");
    });
});
    $(window).on('load', function () {
        $('.selectpicker').selectpicker({
            'selectedText': 'cat'
        });
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

