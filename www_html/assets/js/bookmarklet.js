// get current url

//var seudo_host = "http://loco.seudo.vn";
var seudo_host = "http://seudo.vn";
//var seudo_host = "http://seudo.t90.vn";
//var seudo_host = "http://192.168.1.250/seudo/www_html";

var url_js = seudo_host + "/assets/js/process/paid_tool/";
var translate_url = seudo_host + "/goodies_util/translate";
var seudo_script = document.getElementById("seudo_script");
var link_get_exchange = seudo_host+"/cart/get_exchange_rate";
var link_track_error = seudo_host+"/goodies_util/track_error";
var exchange_rate = 3475;
if(seudo_script != null){
    seudo_script.removeAttribute("id");
}
//var seudo_host = "http://seudo.vn";
//var seudo_host = 'http://192.168.1.150/seudo/www_html';
//var seudo_host = 'http://loco.seudo.vn';
//console.log(b);
var global = new GlobalTool();
var server = global.getHostname();

global.getExchangeRate();

//global.getPriceSku();
if (server.indexOf('taobao') != -1 || server.indexOf('tmall') != -1) { // Taobao Tmall

    global.loadCssLoading();
    global.loadCssLoading();
    global.loading();
    showLoading();
    global.loadTaobao();
    global.loadTaobao();

    setTimeout(global.loadSeudoPaid(),1000);
}else if (server.indexOf('alibaba') != -1 || server.indexOf('1688') != -1) {  // Alibaba
    global.loadCssLoading();
    global.loadCssLoading();
    global.loading();
    showLoading();
    global.loadAlibaba();
    global.loadAlibaba();
    setTimeout(global.loadSeudoPaid(),1000);
}else if(server.indexOf('eelly') != -1 || server.indexOf('nahuo') != -1){
    global.loadCssLoading();
    global.loadCssLoading();
    global.loading();
    showLoading();
    global.loadBookmarkIframe();
    global.loadBookmarkIframe();
}else{
    alert("Duong dan dat hang chua duoc ho tro, lien he bo phan CSKH de duoc huong dan.");
}



function GlobalTool(){

    this.loading = function(){
        var div = document.createElement('div');
        div.setAttribute("class","_loading");
        div.innerHTML = '<div class="_content"><img src="'+seudo_host+'/assets/img/small/loading51.gif"><span>...</span></div>'
        document.body.insertBefore(div, document.body.nextSibling);
    };
    this.loadAlibaba = function(){
        var file_ali = document.createElement('script');
        file_ali.setAttribute('src', url_js+'alibaba_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_ali);
    };
    this.loadTaobao = function(){
        var file_taobao = document.createElement('script');
        file_taobao.setAttribute('src', url_js+'taobao_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_taobao);
    };
    this.loadSeudoPaid = function(){
        var file_seudo = document.createElement('script');
        file_seudo.setAttribute('src', url_js+'seudo_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_seudo);
    };
    this.loadBookmarkIframe = function(){
        var file_bookmark = document.createElement('script');
        file_bookmark.setAttribute('src', url_js+'bookmark_iframe.js?t=' + Math.random());
        document.body.appendChild(file_bookmark);
    };

    this.loadCssLoading = function(){
//        <link href="http://seudo.vn/assets/css/loading.css" rel="stylesheet" type="text/css" media="all">
        var file_css = document.createElement('link');
        file_css.setAttribute('href', seudo_host+'/assets/css/loading1.css?t=' + Math.random());
        file_css.setAttribute('rel', "stylesheet");
        file_css.setAttribute('type', "text/css");
        file_css.setAttribute('media', "all");
        document.body.appendChild(file_css);
    };
    this.getHostname = function() {
        var url = window.location.href;
        url = url.replace("http://", "");

        var urlExplode = url.split("/");

        return urlExplode[0];
    };
    this.translate = function(text,type,obj){
//        var param = "text="+text+"&type"+type;
//        var xmlHttpReq = false;
//        // Mozilla/Safari
//        if (window.XMLHttpRequest) {
//            xmlHttpReq = new XMLHttpRequest();
//        }
//        // IE
//        else if (window.ActiveXObject) {
//            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
//        }
//        xmlHttpReq.open('POST',translate_url, true);
//        xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//        xmlHttpReq.withCredentials = "true";
//
//        xmlHttpReq.onreadystatechange = function () {
//            if (xmlHttpReq.readyState == 4) {
////                _title.innerText();
//                var data = JSON.parse(xmlHttpReq.responseText);
//                if(data.data_translate != ""){
//                    obj.textContent = data.data_translate;
//                }
//            }
//        }
//        //console.log(getLink());
//        xmlHttpReq.send(param);
    };

    this.getExchangeRate = function(){
        var xmlHttpReq = false;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlHttpReq.open('POST', link_get_exchange, true);
        xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttpReq.withCredentials = "true";

        xmlHttpReq.onreadystatechange = function () {
            if (xmlHttpReq.readyState == 4) {
                if(!isNaN(xmlHttpReq.responseText)){
                    exchange_rate = xmlHttpReq.responseText;
                }
            }
        };
        //console.log(getLink());
        xmlHttpReq.send(link_get_exchange);
    };

    this.getPriceSku = function(){
        var link = "http://detailskip.taobao.com/json/sib.htm?itemId=4353993513&sellerId=277960099&u=1&p=1&rcid=25&sts=337145856,1170936092172484612,144467031830331520,8800388121603&chnl=pc&price=1380&shopId=&vd=1&skil=false&pf=1&al=false&ap=1&ss=0&free=0&st=1&ct=1&prior=1&ref=";
        var param = link.split('?')[1];
        var xmlHttpReq = false;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlHttpReq.open('GET', link, true);
        xmlHttpReq.setRequestHeader('Content-Type', 'application/x-javascript;charset=GBK');
        xmlHttpReq.setRequestHeader('Set-Cookie', '_tb_token_=f87ff687e3da;Domain=.taobao.com;Path=/;HttpOnly uc1=cookie14=UoLVbblhqZ41Aw%3D%3D; Domain=.taobao.com; Path=/');
        xmlHttpReq.withCredentials = "true";

        xmlHttpReq.onreadystatechange = function () {
            if (xmlHttpReq.readyState == 4) {
                console.log(xmlHttpReq.responseText);
            }
        };
        //console.log(getLink());
        xmlHttpReq.send(param);
    }


}

var GlobalPaid = {
    baseUrl : function(){
        alert(100);
        return seudo_host;
    },
    imgUrl : function(){
        return seudo_host+"/assets/img/small/";
    },
    currency_format : function (num) {

        if(num>0){
            var round = 10;
            num = Math.ceil(num / round) * round;
            num = num.toString().replace(/\$|\,/g,'');

            for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++){
                num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
            }
            return (num );
        }
        return '0';
    },
    getExchangeRate : function(){
        return exchange_rate;
    },
    loadAlibaba : function(){
        var file_ali = document.createElement('script');
        file_ali.setAttribute('src', url_js+'alibaba_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_ali);
    },
    translate : function(text,type,obj){
        var param = "text="+text+"&type="+type;
        var xmlHttpReq = false;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlHttpReq.open('POST',translate_url, true);
        xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttpReq.withCredentials = "true";

        xmlHttpReq.onreadystatechange = function () {
            if (xmlHttpReq.readyState == 4) {
//                _title.innerText();
                var data = JSON.parse(xmlHttpReq.responseText);
                if(data.data_translate != ""){
                    obj.setAttribute("data-text",text);
                    obj.textContent = data.data_translate;
                }
            }
        };
        //console.log(getLink());
        xmlHttpReq.send(param);
    },
    trackError : function(link,error){
        var param = "link="+link+"&error="+error+"&tool=bookmarklet";
        var xmlHttpReq = false;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlHttpReq.open('POST',link_track_error, true);
        xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttpReq.withCredentials = "true";

        xmlHttpReq.onreadystatechange = function () {
            if (xmlHttpReq.readyState == 4) {
                //                _title.innerText();
            }
        };
        //console.log(getLink());
        xmlHttpReq.send(param);
    },

    hasClass : function(element,$class){
        return (element.className).indexOf( $class) > -1;
    },

    resizeImage : function (image){
        return image.replace(/[0-9]{2,3}[x][0-9]{2,3}/g, '150x150');
    },

    getParamsUrl : function(name, link){
        var l = '';
        if(link) {
            l = link;
        } else {
            l = window.location.href;
        }
        if(l == '') return null;

        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(l);
        if (results==null) return null;

        return results[1] || 0;
    }
};

function showLoading(){
    document.getElementsByClassName("_loading")[0].style.display = "block";
}

function hideLoading(){
    var loading = document.getElementsByClassName("_loading");
    if(loading.length > 0){
        for (var i = 0; i < loading.length; i++) {
            loading[i].style.display = "none";
        }
    }
}
