/**
 * Created by Admin on 1/3/14.
 */
// ==UserScript==
// @name                SeudoToolOrder
// @namespace	        http://seudo.vn
// @description	        Seudo- Công cụ tối ưu đặt hàng trên các site Trung Quốc
// @include		http://item.taobao.com/*
// @include     http://detail.taobao.com/meal_detail.htm?spm=*
// @include		http://item.lp.taobao.com/*
// @include		http://item.beta.taobao.com/*
// @include		http://auction.taobao.com/*
// @include		http://item.tmall.com/*
// @include		http://detail.tmall.com/*
// @include		http://detailp4p.china.alibaba.com/*
// @include		http://detail.china.alibaba.com/*
// @include		http://detail.1688.com/*
// @include		http://auction1.paipai.com/*
// @include		http://item.wanggou.com/*

function alibaba() {
    function htmlOnLoad() {
        var script = document.createElement('script');
        script.src = "http://whatever.com/the/script.js";
        document.getElementsByTagName('head')[0].appendChild(script);
    }

}
function taobao() {
    function htmlOnLoad() {
        var script = document.createElement('script');
        script.src = "http://whatever.com/the/script.js";
        document.getElementsByTagName('head')[0].appendChild(script);
    }

}

function getHostname() {
    var url = window.location.href;
    url = url.replace("http://", "");

    var urlExplode = url.split("/");
    var serverName = urlExplode[0];

    return serverName;
}

var host    = getHostname();
var ex      = null;
// Taobao (tmall)
if (host.indexOf('taobao') != -1 || host.indexOf('tmall') != -1) {
    ex = new taobao();
}
// Alibaba (1688)
if (host.indexOf('alibaba') != -1 || host.indexOf('detail.1688') != -1) {
    ex = new alibaba();
}
// Paipai (wanggou)
if (host.indexOf('paipai') != -1 || host.indexOf('wanggou') != -1) {
    ex = new paipai();
}

ex.htmlOnLoad();