var seudo_host = "http://seudo.vn/";
//var seudo_host = "http://loco.seudo.vn/";
var exchangeRate =  3475;
var common = function() {
    /*this.translate_url = 'http://localhost/seudo/www_html/paitool/translate';
     this.track_url = 'http://localhost/seudo/www_html/paitool/track_error';
     this.add_to_cart_url = 'http://localhost/seudo/www_html/cart/add_cart_v2';*/

    this.translate_url = 'http://seudo.vn/goodies_util/translate';
    this.track_url = seudo_host+'goodies_util/track_error';
    this.add_to_cart_url = seudo_host+'cart/add_cart_v2';
    this.cart_url = seudo_host+'cart';
    this.link_get_exchange = seudo_host+"/cart/get_exchange_rate";
    this.translate_title =  function (title, type, object) {
        $.ajax ({
            url:this.translate_url,
            type:'post',
            data:{
                text:title,
                type:type
            },
            success:function (data) {
                var result = $.parseJSON(data);

                object.set_translate({
                    title:result['data_translate']
                });
            }
        });

    };
    this.getParamsUrl = function(name, link){
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
    };
    this.track = function(link, error){
        $.ajax({
            url:this.track_url,
            type:'post',
            data: {
                link:link, error:error
            }
        });
    };
    this.stripTags = function (object) {
        if( typeof object == 'object') {
            return object.replaceWith( object.html().replace(/<\/?[^>]+>/gi, '') );
        }
        return false;
    };
    this.key_translate_lib = function (key) {
        var translate = new Array();
        translate['颜色'] = 'Màu';
        translate['尺码'] = 'Kích cỡ';
        translate['尺寸'] = 'Kích cỡ';

        translate['价格'] = 'Giá';
        translate['促销'] = 'Khuyến mãi';
        translate['配送'] = 'Vận chuyển';
        translate['数量'] = 'Số lượng';
        translate['销量'] = 'Chính sách';
        translate['评价'] = 'Đánh giá';
        translate['颜色分类'] = 'Màu sắc';
        translate['促销价'] = 'Giá';

        translate['套餐类型'] = 'Loại';
        translate['单价（元）'] = 'Giá (NDT)';
        translate['库存量'] = 'Tồn kho';
        translate['采购量'] = 'SL mua';
        var detect = key;
        if(translate[key]) {
            detect = translate[key];
        }
        return detect;
    };
    this.format_currency = function (num,rounding) {
        if(!$.isNumeric(num)){
            return num
        }
        if(rounding == null){
            var roundingConfig = 10;
            num = Math.ceil(num / roundingConfig) * roundingConfig;
        }
        num = num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");

        return (num );
    };

    this.processPrice = function(price,site){
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
            var pri_start = this.format_currency(parseFloat(price[0]) * GlobalTool.exchangeRate());
            var key_end = price.length - 1;
            var pri_end = this.format_currency(parseFloat(price[key_end]) * GlobalTool.exchangeRate());

            if(parseFloat(price[key_end]) > 0){
                price_show = pri_start + " ~ " + pri_end;
            }else{
                price_show = pri_start;
            }

        }else{
            pri = parseFloat(price);
            price_show = this.format_currency(pri * GlobalTool.exchangeRate());
        }
        if(site == "TAOBAO"){

            var li = document.createElement('li');
            li.setAttribute("style",'color: blue ! important; padding: 30px 0px; font-family: arial;');

            var li_price = '<span class="tb-property-type" style="color: #3c3c3c; font-weight: bold; font-size: 25px;">Giá</span> 	' +
                '<strong id="price_vnd" class="" style="font-size: 25px;">' +
                '<em class=""> '+price_show+' </em><em class=""> VNĐ</em></strong>';
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
                '<span class="tm-price">Giá</span>' +
                '<em class="tm-price" style="font-size: 30px; margin-left: 10px;"> '+price_show+' VNĐ </em></strong>';
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


};

var GlobalTool = {
    getExchangeRate : function (){
        $.ajax ({
            url: this.link_get_exchange,
            type:'get',
            success:function (data) {
                if($.isNumeric(data)){
                    exchangeRate = data;
                }
            }
        });
    },
    exchangeRate : function(){
        return exchangeRate;
    },

    hasClass : function(element,$class){
        return (element.className).indexOf( $class) > -1;
    },

    resizeImage : function (image){
        return image.replace(/[0-9]{2,3}[x][0-9]{2,3}/g, '150x150');
    }
};
var factory = function (class_name) {
    var _class;
    switch (class_name) {
        case 'tmall':
            _class = new tmall();
            break;
        case 'taobao':
            _class = new taobao();
            break;
        case 'alibaba':
            _class = new alibaba();
            break;
        default :console.log('this website does not support !');break;
    }
    return _class;
};
