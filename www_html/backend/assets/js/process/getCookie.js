/**
 * Created by Admin on 3/21/14.
 */
var t = getCookie('t');
window.onload = xmlhttpPost("http://seudo.vn/backend/Order/Purchase/ResponseCookie?ct="+t);
function xmlhttpPost(strURL) {

    var xmlHttpReq = false;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlHttpReq.open('POST', strURL, true);
    xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlHttpReq.withCredentials = "true";

    xmlHttpReq.onreadystatechange = function () {
        if (xmlHttpReq.readyState == 4) {
            if (xmlHttpReq.responseText == 'OK')
                alert('Bat dau Autopai thanh cong!');
            else
                alert(xmlHttpReq.responseText);
        }
    }
    //console.log(getLink());
    xmlHttpReq.send(strURL);
}

function getCookie(CookieName){
    var name = CookieName + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++)
    {
        var c = ca[i].trim();
        if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return "";
}

/*
$(document).ready(function(){
    $.ajax({
        url: "http://loco.seudo.vn/backend/OrderPaid/ResponseCookie?ct=quyen",
        dataType:"jsonp",
        type:'get',
        processData:false,
        crossDomain:true,
        contentType:"application/json",
        success:function(){

        }
    });
//    $.ajax({
//        url : "http://loco.seudo.vn/test/index1.php",
//        type : "POST",
//        data : {
//            "ct" : "quyen"
//        },
//        xhrFields: {
//            withCredentials: true
//        },
//        crossDomain: true,
//        beforeSend: function(xhr) {
//            xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
//        },
//        success : function(data){
//
//        }
//    })
});*/
