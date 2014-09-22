/**
 * Created by Admin on 3/27/14.
 */
var url_js = "http://loco.seudo.vn/assets/js/process/";
var global = new GlobalTool();
var server = global.getHostname();
if (server.indexOf('taobao') != -1 || server.indexOf('tmall') != -1) {
    global.loadTaobao();
    global.loadTaobao();
}
// alibaba
if (server.indexOf('alibaba') != -1 || server.indexOf('detail.1688') != -1) {
    global.loadAlibaba();
    global.loadAlibaba();
}
global.loadSeudoPaid();

/*
function GlobalTool(){
    this.loadAlibaba = function(){
        var file_ali = document.createElement('script');
        file_ali.setAttribute('src', url_js+'alibaba_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_ali);
    }
    this.loadTaobao = function(){
        var file_taobao = document.createElement('script');
        file_taobao.setAttribute('src', url_js+'taobao_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_taobao);
    }
    this.loadSeudoPaid = function(){
        var file_seudo = document.createElement('script');
        file_seudo.setAttribute('src', url_js+'seudo_paid_tool.js?t=' + Math.random());
        document.body.appendChild(file_seudo);
    }
    this.getHostname = function() {
        var url = window.location.href;
        url = url.replace("http://", "");

        var urlExplode = url.split("/");
        var serverName = urlExplode[0];

        return serverName;
    }
}*/
