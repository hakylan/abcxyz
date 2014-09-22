/**
 * Created by Admin on 6/24/14.
 */
$(document).ready(function(){
    checkdevice();
    checkBrowse();
});

function checkBrowse(){
//    console.log(navigator.userAgent.toLowerCase());
    if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
        $('._link_chrome').show();
        $('._link_coccoc').show();
    }else{
        $('._link_chrome').hide();
        $('._link_coccoc').hide();
    }

//    $.browser.chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
//    if($.browser.chrome){
//        alert("chrome");
//    }else{
//        alert("no");
//    }
}

function checkdevice()
{
    if (navigator == undefined)
    {
        return;
    }

    if (navigator.appVersion == undefined)
    {
        return;
    }

    if (navigator.appVersion.indexOf("Windows Phone")!=-1) {
        $('#myCarousel').removeClass("carousel slide");
        $('#myCarousel').removeAttr("id");
        var a = document.getElementById('download');
        a.setAttribute('href','http://seudo.vn/ung-dung/seudo.xap'); // link download
        a = document.getElementById('space3');
        a.setAttribute('style','display: none'); // hide phone os section
        a = document.getElementById('copyright');
        a.className = "copyright phone"; // change copyright color
        return;
    }
    if (navigator.appVersion.indexOf("Android")!=-1)
    {
        $('#myCarousel').removeClass("carousel slide");
        $('#myCarousel').removeAttr("id");
        var a = document.getElementById('download');
        a.setAttribute('href','http://seudo.vn/ung-dung/seudo.apk'); // link download
        a = document.getElementById('space3');
        a.setAttribute('style','display: none'); // hide phone os section
        a = document.getElementById('copyright');
        a.className = "copyright phone"; // change copyright color
        a = document.getElementById('play-store');
        a.style.display = ""; // show google play store
        return;
    }
    if (navigator.appVersion.indexOf("iOS")!=-1)
    {
        $('#myCarousel').removeClass("carousel slide");
        $('#myCarousel').removeAttr("id");
        var a = document.getElementById('download');
        a.setAttribute('href','#'); // link download do nothing
        a = document.getElementById('space3');
        a.setAttribute('style','display: none'); // hiden phone os section
        a = document.getElementById('copyright');
        a.className = "copyright phone"; // change copyright color
        return;
    }
    if (navigator.appVersion.indexOf("Win")!=-1)
    {
    }
}
