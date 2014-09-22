// ==UserScript==
// @name                orderhangaddon
// @namespace	        http://orderhang.com
// @description	        orderhang- Công c? t?i ưu đ?t hàng trên các site Trung Qu?c
// @include		http://item.taobao.com/*
// @include             http://detail.taobao.com/meal_detail.htm?spm=*
// @include		http://item.lp.taobao.com/*
// @include		http://item.beta.taobao.com/*
// @include		http://auction.taobao.com/*
// @include		http://item.tmall.com/*
// @include		http://item.wanggou.com/*
// @include		http://detail.tmall.com/*
// @include		http://detailp4p.china.alibaba.com/*
// @include		http://detail.china.alibaba.com/*
// @include		http://detail.1688.com/*
// @include		http://auction1.paipai.com/*
// @include		http://mofayichu.com/shop_*Products.aspx*
// @include		http://mofayichu.com/shop_*products.aspx*
// @include		http://www.mofayichu.com/shop_*Products.aspx*
// @include		http://www.mofayichu.com/shop_*products.aspx*
// @include		http://mofayichu.q88e.net/shop_*Products.aspx*
// @include		http://mofayichu.q88e.net/shop_*products.aspx*
// @include		http://www.mofayichu.q88e.net/shop_*Products.aspx*
// @include		http://www.mofayichu.q88e.net/shop_*products.aspx*
// @include		http://5taobao.q88k.net/shop_*Products.aspx*
// @include		http://www.5taobao.net/shop_gkm2/products.aspx?sku*
// @include		http://www.5taobao.net/shop_gkm1/products.aspx?sku*
// @include		http://www.5taobao.net/shop_gmb1/products.aspx?sku*
// @include		http://www.5taobao.net/shop_gmb1/products.aspx?shbid=*
// @include		http://5taobao.q88k.net/shop_*products.aspx*
// @include		http://www.chenxifushi.com/shop_*products.aspx*
// @include		http://www.chenxifushi.com/shop_*Products.aspx*
// @include		http://www.lelefushi.com/SHOP_*Products.aspx*
// @include		http://www.lelefushi.com/SHOP_*products.aspx*
// @include		http://www.lelefushi.com/shop_*Products.aspx*
// @include		http://www.lelefushi.com/shop_*products.aspx*
// @include		http://www.yilanfushi.com/shop_*products.aspx*
// @include		http://www.yilanfushi.com/shop_*Products.aspx*
// @include		http://www.shmoyu.com/shop_*products.aspx*
// @include		http://www.shmoyu.com/shop_*Products.aspx*
// @include		http://www.yiranmeifushi.com/shop_*products.aspx*
// @include		http://www.yiwenfushi.com/shop_*products.aspx*
// @include		http://yiranmeifushi.q88d.net/shop_gma1/products.aspx?sku=*
// @include		http://yiwenfushi.q88j.net/shop_*products.aspx*
// @include		http://www.rihanfushi.com/shop_*products.aspx*
// @include		http://www.chengzifs.com/SHOP_*Products.aspx*
// @include		http://www.69shopfs.com/shop_*products.aspx*
// @include		http://fuzhuangpifa.cn/goods*
// @include		http://www.fuzhuangpifa.cn/goods*
// @include		http://jj-fashion.com/goods.php?id*
// @include		http://www.jj-fashion.com/goods.php?id=*
// @include		http://shanghai.q88i.net/shop_*products.aspx*
// @include		http://www.eeshow.com.cn/shop_*products.aspx*
// @include		http://eeshow.com.cn/shop_*products.aspx*
// @include		http://eeshow.q88a.net/shop_*products.aspx*
// @include		http://www.charm-dress.com/SHOP_*products.aspx*
// @include		http://www.baobaopifa.com/productshopxp.asp*
// @include		http://www.xinshij.com/shipin*
// @include		http://www.1925.cn/showproduct*
// @include		http://1925.cn/showproduct*
// @include		http://www.tygfushi.com/shop_gjq3/Products.aspx?sku=*
// @include		http://www.tygfushi.com/shop_gjq3/Products.aspx?shbid=*
// @include		http://rihanfs.q88b.net/shop_gln2/1280/products.aspx?sku=*
// @include		http://rihanfs.q88b.net/shop_gln2/1280/products.aspx?shbid=*
// @include             http://www.taopulu.com/shop_gjk1/Products.aspx?sku=*
// @include		http://www.hongwufushi.com/sp_xx.asp?bh*
// @include		http://hongwufushi.com/sp_xx.asp?bh*
//const TAOBAO = "TAOBAO";
// ==/UserScript==

var urlParams = ({
    getUrlVars: function (url) {
        var vars = [], hash;
        if (url == null) {
            url = window.location.href;
        }
        var hashes = url.slice(url.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function (name, url) {
        return this.getUrlVars(url)[name];
    }
});

function getObjectByClass(classname, elems) {
    var i;
    elems = elems != null ? elems : document.getElementsByTagName('*');
    for (i in elems) {
        if ((" " + elems[i].className + " ").indexOf(" " + classname + " ") > -1) {
            return elems[i];
        }
    }
    return false;
}
//create function, it expects 2 values.
function insertAfter(newElement, targetElement) {
    //target is what you want it to go after. Look for this elements parent.
    var parent = targetElement.parentNode;

    //if the parents lastchild is the targetElement...
    if (parent.lastchild == targetElement) {
        //add the newElement after the target element.
        parent.appendChild(newElement);
    } else {
        // else the target has siblings, insert the new element between the target and it's next sibling.
        parent.insertBefore(newElement, targetElement.nextSibling);
    }
}
function getHostname() {
    var url = window.location.href;
    url = url.replace("http://", "");

    var urlExplode = url.split("/");
    var serverName = urlExplode[0];

    return serverName;
}
function addIframe() {
    var iframe = document.createElement('iframe');
    //iframe.style.display = "none";
    iframe.height = 0;
    iframe.width = 0;
    iframe.src = 'http://orderhang.com';
    document.body.appendChild(iframe);
}
function addVersion(url) {
    return url + '&version=20140225&translate=1'; // nam-thang-ngay
}
//ham lam chon so den2 tp
function roundNumber(num, dec) {
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    return result;
}
function is_valid_url(url) {
    return url.match(/^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/);
}
//ham xu lý gia trong truong hop nguoi dung su dung chuc nang tu dong dich cua Chrome
function processPrice(price,site) {
    if (price == null || parseFloat(price) == 0)
        return 0;

    var p = 0;
    if(price.constructor === Array){
        p = String(price[0]).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
    }else{
        p = String(price).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
    }

    if(isNaN(p) || parseFloat(price) == 0){
        return 0;
    }

    var price_show = "";
    var pri = 0;
    if(price.constructor === Array && price.length > 1){
        var pri_start = GlobalPaid.currency_format(parseFloat(price[0]) * GlobalPaid.getExchangeRate());
        var key_end = price.length - 1;
        var pri_end = GlobalPaid.currency_format(parseFloat(price[key_end]) * GlobalPaid.getExchangeRate());

        if(parseFloat(price[key_end]) > 0){
            price_show = pri_start + " ~ " + pri_end;
        }else{
            price_show = pri_start;
        }

    }else{
        pri = parseFloat(price);
        price_show = GlobalPaid.currency_format(pri * GlobalPaid.getExchangeRate());
    }
    if(site == "TAOBAO"){

        var li = document.createElement('li');
        li.setAttribute("style",'color: blue ! important; padding: 30px 0px; font-family: arial;');

        var li_price = '<span class="tb-property-type" style="color: blue; font-weight: bold; font-size: 25px;">Gia</span> 	' +
            '<strong id="price_vnd" class="" style="font-size: 25px;">' +
            '<em class=""> '+price_show+' </em><em class=""> VND</em></strong>';
        li.innerHTML = li_price;
        var J_PromoPrice = document.getElementById("J_PromoPrice");
        if(J_PromoPrice != null){
            J_PromoPrice.parentNode.insertBefore(li, J_PromoPrice.nextSibling);
        }else{
            var J_StrPriceModBox = document.getElementById("J_StrPriceModBox");

            if(J_StrPriceModBox != null){
                J_StrPriceModBox.parentNode.insertBefore(li, J_StrPriceModBox.nextSibling);
            }
        }

    }else if(site == "TMALL"){
        var li = document.createElement('li');
        li.setAttribute("style",'font-weight: bold; padding: 10px 0px;');
        li.setAttribute("class",'tm-promo-price tm-promo-cur');

        var li_price = '<strong id="price_vnd" class="" style="font-size: 30px;">' +
            '<span class="tm-price">Gia</span>' +
            '<em class="tm-price" style="font-size: 30px; margin-left: 10px;"> '+price_show+'  VND </em></strong>';
        li.innerHTML = li_price;
        var J_PromoPrice = document.getElementById("J_PromoPrice");
        if(J_PromoPrice != null){
            J_PromoPrice.parentNode.insertBefore(li, J_PromoPrice.nextSibling);
        }else{
            var J_StrPriceModBox = document.getElementById("J_StrPriceModBox");

            if(J_StrPriceModBox != null){
                J_StrPriceModBox.parentNode.insertBefore(li, J_StrPriceModBox.nextSibling);
            }
        }
    }
    return parseFloat(p);
}

var host = getHostname();
var ex = null;

//var url = "http://loco.seudo.vn/cart/AddCartV2";
//var url_save="http://loco.seudo.vn/cart/AddCartV2?";
//var url = "http://192.168.1.250/seudo/www_html/cart/AddCartV2";
//var url_save="http://192.168.1.250/seudo/www_html/cart/AddCartV2?";
//var url = "http://seudo.t90.vn/cart/AddCartV2";
//var url_save="http://seudo.t90.vn/cart/AddCartV2?";
var url = "http://seudo.vn/cart/AddCartV2";
var url_save="http://seudo.vn/cart/AddCartV2?";
try{
    // taobao
    if (host.indexOf('taobao') != -1 || host.indexOf('tmall') != -1) {
        ex = new taobao(url, url_save);
    }

// alibaba
    if (host.indexOf('alibaba') != -1 || host.indexOf('detail.1688') != -1) {
        ex = new alibaba(url, url_save);
    }
    ex.htmlOnLoad();

//}

}catch (e){
    setTimeout(hideLoading(),3000);
}

