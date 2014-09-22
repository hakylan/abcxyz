var taobao =  function() {
    this.source = 'taobao';
    this.common = new common();
    this.init = function () {
        //specify aria to
        $('#detail').css('border','1px solid red');
        $('#detail').css('font-size','11px');
        $('.tb-rmb').remove();

        this.parse();
    };
    this.parse = function () {

        //parse label description
        var common = this.common;
        $('.tb-property-type').each(function (index, value) {
            var text = $(this).text();
            $(this).text(common.key_translate_lib(text));
        });
        //parse button
        var comment = '<textarea placeholder="Chú tích cho sản phẩm này" style="margin: 0px; width: 98%; height: 40px;padding:4px;"></textarea><br/><br/>';
        var button_html = '<a id="book-to-seudo" href="javascript:;" ' +
            'style="background: url(\'http://seudo.vn/assets/img/small/icon-bkv1.png\') no-repeat scroll 0 0 rgba(0, 0, 0, 0);' +
            'display: inline-block;height: 42px;width: 150px;"></a>';
        button_html += '<a target="_blank" style="display: inline-block;margin-left: 20px;' +
            'color: rgb(0, 114, 188);" href="'+common.cart_url+'">Vào giỏ hàng</a>' +
            '<p style="color: #FF6041">Đề nghị không sử dụng Google Translate - Điều này có thể làm chúng tôi mua nhầm hàng cho bạn</p>';
        var tb_action = $('.tb-action');
        console.log(tb_action);
        if(tb_action == null || tb_action == "" || (typeof tb_action === 'object' && tb_action.length == 0)){
            tb_action = $('#J_box_buycart');
        }
        console.log(tb_action);
        tb_action.html(
            comment
                + button_html );

        //parse price

//        $('#J_PPayGuide,#J_PayGuide').hide();
        var price = this.getPromotionPrice("TAOBAO");
        var price_html = '<p style="font-size: 16px;margin-top: 15px;">Tỉ giá: '+this.common.format_currency(exchangeRate,false)+'đ</p>';
        var j_str_price = $('#J_StrPriceModBox');
        if(j_str_price == null || j_str_price == "" || (typeof j_str_price === 'object' && j_str_price.length == 0)){
//            j_str_price = $('.tb-btn-buy');
            j_str_price = $('.tm-promo-price');
        }

        if(j_str_price == null || j_str_price == "" || (typeof j_str_price === 'object' && j_str_price.length == 0)){
            j_str_price = $('.tb-detail-hd');
        }
        if(j_str_price == null || j_str_price == "" || (typeof j_str_price === 'object' && j_str_price.length == 0)){
            j_str_price = $('#J_PromoPrice');
        }
        if(j_str_price != null && j_str_price != ""){
            j_str_price.append(price_html);
        }
//        $('#J_StrPriceModBox').append(button_html);

        //translate
        var title_content = this.getTitleOrigin();

        this.common.translate_title(title_content,'title', this);
        //translate prop
        var translate_url = this.common.translate_url;
        $('.J_TSaleProp li span').each(function(index, value) {
            var that = $(this);
            var context = $(this).text();

            $.post(translate_url, {text:context,type:'properties'}, function (data) {
                var result = $.parseJSON(data);
                if(result['data_translate'] && result['data_translate'] !=null) {
                    that.attr("data-text",that.text());
                    that.text(result['data_translate']);
                }
            });
        });

    };
    this.set_translate = function(data) {
        var _title = this.getDomTitle();

        if(_title != null && data.title != ""){
            _title.setAttribute("data-text",_title.textContent);
            _title.textContent = data.title;
        }
    };

    this.getPromotionPrice = function(site){
        try{
            var normal_price = document.getElementById('J_StrPrice');

            if(normal_price == null){
                normal_price = document.getElementById("J_priceStd");
            }

            if(normal_price == null) {
                normal_price = document.getElementById('J_StrPriceModBox');
            }

            if(normal_price == null){
                normal_price = document.getElementById('J_PromoPrice');
            }

            var promotion_price = document.getElementById('J_PromoPrice');

            // NEW
            var price = 0;
            if(promotion_price != null) { // Promotion price
                if(window.location.href.indexOf('tmall') > -1) {

                    if(promotion_price.getElementsByClassName('tm-price').length > 0) {
                        try{
                            price = promotion_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                        }catch(e){
                            price = promotion_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                        }
                    }
                } else {
                    if(promotion_price.getElementsByClassName('tb-rmb-num').length > 0) {
                        try{
                            price = promotion_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                        }catch(e){
                            price = promotion_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                        }
                    }
                }
                price = this.common.processPrice(price,site);

                if(price == 0) { // Try if price not found
                    try{
                        price = normal_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }catch(e){
                        price = normal_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }
                    price = this.common.processPrice(price,site);
                }
            } else {
                if(window.location.href.indexOf('tmall') > -1) {
                    try{
                        price = normal_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }catch(e){
                        price = normal_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }
                } else {
                    try{
                        price = normal_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }catch(e){
                        price = normal_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }
                }
                price = this.common.processPrice(price,site);
            }
            return price;
        }catch(ex){
            throw Error(ex.message+ " Line:" +ex.lineNumber + " function getPromotionPrice");
        }
    };

    this.getOriginPrice = function(){
        try{
            var origin_price = document.getElementById('J_StrPrice');

            if(origin_price == null){
                origin_price = document.getElementById("J_priceStd");
            }

            if(origin_price == null) {
                origin_price = document.getElementById('J_StrPriceModBox');
            }

            if(origin_price == null){
                origin_price = document.getElementById('J_PromoPrice');
            }

            if(window.location.href.indexOf('taobao') > -1) {
                var price = 0;

                var tb_rmb_num = origin_price.getElementsByClassName('tb-rmb-num');

                if(tb_rmb_num != null && (typeof tb_rmb_num === 'object' && tb_rmb_num.length > 0)){
                    try{
                        price = origin_price.getElementsByClassName("tb-rmb-num")[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                    }catch (e){
                        price = origin_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                    }
                }
            }else{
                try{
                    price = origin_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                }catch(e){
                    price = origin_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                }
            }

            return this.common.processPrice(price);
        }catch(ex){
            throw Error(ex.message+ " Can't get origin price function getOriginPrice");
        }
    };

    this.getOuterId = function(data_value){
        var scripts = document.getElementsByTagName('script');
        var skuId = "";
        var skuMap = null;
        if(scripts.length > 0) {
            for(var script = 0; script < scripts.length; script++) {
                if(scripts[script].innerHTML.match(/Hub\.config\.set/)) {
                    try{
                        detailJsStart();
                        skuId = Hub.config.get('sku').valItemInfo.skuMap[";"+data_value+";"].skuId;
                    }catch(e){
                        skuMap = scripts[script].innerHTML.replace(/\s/g, '').substr(scripts[script].innerHTML.replace(/\s/g, '').indexOf(data_value) , 60);
                        skuId = skuMap.substr(skuMap.indexOf('skuId') + 8, 15).match(/[0-9]+/);
                    }
                }else if(scripts[script].innerHTML.match(/TShop\.Setup/)){
                    skuMap = scripts[script].innerHTML.replace(/\s/g, '').substr(scripts[script].innerHTML.replace(/\s/g, '').indexOf(data_value) , 60);
                    skuId = skuMap.substr(skuMap.indexOf('skuId') + 8, 15).match(/[0-9]+/);
                }
            }
        }

        return skuId;
    };

    this.getTitleTranslate = function(){
        var _title = this.getDomTitle();
        var title_translate = _title.textContent;
        if(title_translate == ""){
            title_translate = _title.getAttribute("data-text");
        }
        return title_translate;
    };

    this.getTitleOrigin = function(){
        var _title = this.getDomTitle();
        var title_origin = _title.getAttribute("data-text");
        if(title_origin == "" || typeof title_origin == "undefined" || title_origin == null){
            title_origin = _title.textContent;
        }
        return title_origin;
    };

    this.getDomTitle = function(){
        var _title = null;
        if (document.getElementsByClassName("tb-main-title").length > 0) {
            _title =  document.getElementsByClassName("tb-main-title")[0];
        }

        if (_title == null && document.getElementsByClassName("tb-detail-hd").length > 0) {
            var h = document.getElementsByClassName("tb-detail-hd")[0];
            if (h.getElementsByTagName('h3').length > 0 && h != null) {
                _title = h.getElementsByTagName('h3')[0];
            }else{
                _title = h.getElementsByTagName("h1")[0];
            }
        }

        if (_title.textContent == "" && document.getElementsByClassName("tb-tit").length > 0) {
            _title = document.getElementsByClassName("tb-tit")[0];
        }

        // 27/7/2013
        if (_title.textContent == "") {
            _title = document.querySelectorAll('h3.tb-item-title');
            if (_title != null) {
                _title = _title[0];
            }else{
                _title = document.getElementsByClassName('tb-item-title');
                if(_title.length > 0){
                    _title = _title[0];
                }
            }
        }
        return _title;
    };

    this.getSellerName = function(){
        var seller_name = '';
        if(window.location.href.indexOf('tmall') > 0) { // Tmall
            if(document.getElementsByClassName('slogo').length > 0) { // Page detail of shop
                if (document.getElementsByClassName('slogo-shopname').length > 0) {
                    seller_name = document.getElementsByClassName('slogo-shopname')[0].textContent;
                } else if(document.getElementsByClassName('flagship-icon').length > 0) {
                    seller_name = document.getElementsByClassName('slogo')[0].getElementsByTagName('span')[1].getAttribute('data-tnick');
                } else {
                    seller_name = document.getElementsByClassName('slogo')[0].getElementsByTagName('span')[0].getAttribute('data-tnick');
                }
            } else { // Page detail of general
                if(document.getElementsByClassName('bts-extend').length > 0 ) {
                    try {
                        seller_name = document.getElementsByClassName('bts-extend')[0].getElementsByTagName('li')[1].getElementsByTagName('span')[0].getAttribute('data-tnick');
                    } catch(e) {
                        console.log('Seller name not found!' + e);
                    }
                }else{
                    var hd_shop = document.getElementsByClassName('hd-shop-name');
                    if((typeof hd_shop !== 'object' && hd_shop != "" && hd_shop != null) || (typeof hd_shop === 'object' && hd_shop.length > 0)){
                        seller_name = hd_shop[0].getElementsByTagName("a");
                        if(seller_name != null && seller_name != ""){
                            seller_name = seller_name[0].textContent;
                        }
                    }
                }
            }
        } else {
            seller_name = document.getElementsByClassName('tb-seller-name');

            if(seller_name != null && (typeof seller_name === 'object' && seller_name.length > 0)){
                seller_name = seller_name[0].textContent;
            }else{
                var tb_shop_name = document.getElementsByClassName("tb-shop-name");

                if(tb_shop_name != null && (typeof tb_shop_name === 'object' && tb_shop_name.length > 0)){
                    var h3_shop_name = tb_shop_name[0].getElementsByTagName("h3");
                    if(h3_shop_name != null && (typeof h3_shop_name === 'object' && h3_shop_name.length > 0)){
                        seller_name = h3_shop_name[0].textContent;
                    }
                }else{
                    var shop_card = document.getElementsByClassName('shop-card');
                    if(shop_card != null && (typeof shop_card === 'object' && shop_card.length > 0)){
                        var data_nick = shop_card.length > 0 ? shop_card[0].getElementsByClassName('ww-light') : '';
                        seller_name = (data_nick.length > 0 ? data_nick[0].getAttribute('data-nick') : '');
                        if(seller_name == '') {
                            // Find base info
                            if( document.getElementsByClassName('base-info').length > 0) {
                                for(var i =0; i < document.getElementsByClassName('base-info').length; i++) {
                                    if(document.getElementsByClassName('base-info')[i].getElementsByClassName('seller').length > 0) {
                                        if(document.getElementsByClassName('base-info')[i].getElementsByClassName('seller')[0].getElementsByClassName('J_WangWang').length > 0) {
                                            seller_name = document.getElementsByClassName('base-info')[i].getElementsByClassName('seller')[0].getElementsByClassName('J_WangWang')[0].getAttribute('data-nick');
                                            break;
                                        }
                                        if(document.getElementsByClassName('base-info')[i].getElementsByClassName('seller')[0].getElementsByClassName('ww-light').length > 0) {
                                            seller_name = document.getElementsByClassName('base-info')[i].getElementsByClassName('seller')[0].getElementsByClassName('ww-light')[0].getAttribute('data-nick');
                                            break;
                                        }
                                    }
                                }
                            }
                            //if(document.getElementById('J_TEnterShop') != null) seller_name = document.getElementById('J_TEnterShop').innerHTML;
                        }
                    }
                }
            }
        }

        seller_name = seller_name.trim();
        return seller_name;
    };

    this.getSellerId = function(){
        var seller_id = '';
        var url = '';
//        hd-shop-name

        var url_shop = "";

        var supplier = document.querySelectorAll('.tb-shop-name');
        if (supplier.length > 0) {
            url_shop = supplier[0].getElementsByTagName("a")[0];
        } else {
            supplier = document.querySelectorAll('div.shop-card');
            if (supplier.length > 0) {
                url_shop = supplier[0].getElementsByTagName("p")[0].getElementsByTagName("a")[0];
            }
        }

        if(!((typeof supplier !== 'object' && supplier != "" && supplier != null)
            || (typeof supplier === 'object' && supplier.length > 0))){
            supplier = document.getElementsByClassName('hd-shop-name');
            if((typeof supplier !== 'object' && supplier != "" && supplier != null)
                || (typeof supplier === 'object' && supplier.length > 0)){
                url_shop = supplier[0].getElementsByTagName("a")[0];
            }
        }

        if(!((typeof supplier !== 'object' && supplier != "" && supplier != null)
            || (typeof supplier === 'object' && supplier.length > 0))){
            supplier = document.querySelectorAll('span.shop-name');
            if (supplier.length > 0 && supplier != null) {
                url_shop = supplier[0].getElementsByTagName("a")[0];
            }else{
                supplier = document.querySelectorAll('div.shop-card');
                if (supplier.length > 0 && supplier != null) {
                    url_shop = supplier[0].getElementsByTagName("p")[0].getElementsByTagName("a")[0];
                }else{
                    supplier = document.querySelectorAll('a.slogo-shopname');
                    if(supplier.length > 0 && supplier != null){
                        url_shop = supplier[0];
                    }else{
                        supplier = document.querySelectorAll("div#side-shop-info");
                        if((typeof supplier !== 'object' && supplier != "" && supplier != null)
                            || (typeof supplier === 'object' && supplier.length > 0)){
                            supplier = supplier[0].getElementsByClassName('shop-intro')[0];
                            url_shop = supplier.getElementsByTagName("a");
                            url_shop = url_shop[0];
                        }
                    }
                }
            }
        }

        if(url_shop != null && url_shop != ""){
            url = url_shop.getAttribute('href');
            seller_id = url.split('.')[0];
            seller_id = seller_id.split('http://')[1];
        }

        return seller_id;
    };

    this.getProperties = function(){
        //mau sac
        var selected_props = document.getElementsByClassName('J_TSaleProp');

        var color_size = '';

        if(!((typeof selected_props != 'object' && selected_props != "" && selected_props != null)
            || (typeof selected_props === 'object' && selected_props.length > 0))){

            selected_props = document.querySelectorAll("ul.tb-cleafix");
        }
        if(selected_props.length > 0 && selected_props != null) {
            for(var i = 0; i < selected_props.length; i++) {
                var li_origin = selected_props[i].getElementsByClassName('tb-selected')[0];

                if(li_origin != null){
                    var c_s = li_origin.getElementsByTagName('span')[0].getAttribute("data-text");
                    if(c_s == "" || c_s == null || typeof c_s == "undefined"){
                        c_s = li_origin.getElementsByTagName('span')[0].textContent;
                    }
                    color_size+=c_s+';';
                }
            }
        }

        return color_size;
    };

    this.getPropertiesOrigin = function(){
        //mau sac
        var selected_props = document.getElementsByClassName('J_TSaleProp');
        var color_size = '';

        if(!((typeof selected_props !== 'object' && selected_props != "" && selected_props != null)
            || (typeof selected_props === 'object' && selected_props.length > 0))){
            selected_props = document.querySelectorAll("ul.tb-cleafix");
        }
        if(selected_props.length > 0) {
            for(var i = 0; i < selected_props.length; i++) {
                var li_origin = selected_props[i].getElementsByClassName('tb-selected')[0];
                if(li_origin != null){
                    var c_s = li_origin.getElementsByTagName('span')[0].getAttribute("data-text");
                    if(c_s == "" || c_s == null || typeof c_s == "undefined"){
                        c_s = li_origin.getElementsByTagName('span')[0].textContent;
                    }
                    color_size+=c_s+';';
                }
            }
        }
        return color_size;
    };

    this.getDataValue = function(){
        //mau sac
        var selected_props = document.getElementsByClassName('J_TSaleProp');
        var color_size = '';var data_value = '';
        if(selected_props.length > 0) {
            for(var i = 0; i < selected_props.length; i++) {
                var li_origin = selected_props[i].getElementsByClassName('tb-selected')[0];

                data_value+= ";"+li_origin.getAttribute('data-value');
            }
        }
        if(data_value.charAt(0) == ';'){
            data_value = data_value.substring(1,data_value.length);
        }
        return data_value;
    };


    this.getWangwang = function(){
        var span_wangwang = document.querySelectorAll("span.seller");

        var wangwang = "";

        if(span_wangwang.length > 0){
            if(span_wangwang.length > 0){
                span_wangwang = span_wangwang[0].getElementsByTagName("span");
                wangwang = decodeURIComponent(span_wangwang[0].getAttribute('data-nick'));
            }
        }
        return wangwang;
    };

    this.checkSelectFull = function(){
        var props = document.getElementsByClassName('J_TSaleProp');
        var full = true;
        if (props.length > 0) {
            //kiem tra so thuoc tinh da chon cua sp
            var count_selected = 0;
            for (var i = 0; i < props.length; i++) {
                var selected_props = props[i].getElementsByClassName('tb-selected');
                if (selected_props != null && selected_props != 'undefined')
                    count_selected += selected_props.length;
            }
            if (count_selected < props.length) {
                full = false;
            }

        }
        return full;
    };

    this.getQuantity = function(){
        var quantity = '';
        var element = document.getElementById("J_IptAmount");
        if (element) {
            quantity = element.value;
        } else quantity = '';
        // Get amount: 13/9/2013
        if (quantity == '') {
            try {
                quantity = document.getElementsByClassName('mui-amount-input')[0].value;
            } catch (e) {
                console.log(e);
            }
        }

        return quantity;
    };

    this.getImgLink = function() {
        var img_src = "";
        try {
            var img_obj = document.getElementById('J_ImgBooth');
            if (img_obj != null) { // Image taobao and t
                img_src = img_obj.getAttribute("src");
                img_src = GlobalTool.resizeImage(img_src);
                return encodeURIComponent(img_src);
            }

            img_obj = document.getElementById('J_ThumbView');

            if(img_obj != null && img_obj != ""){
                img_src = img_obj.getAttribute("src");
                img_src = GlobalTool.resizeImage(img_src);
                return encodeURIComponent(img_src);
            }

            if (document.getElementById('J_ImgBooth').tagName == "IMG") {
                // Find thumb image
                var thumbs_img_tag = document.getElementById('J_UlThumb');
                try {
                    if (thumbs_img_tag != null) {
                        img_src = thumbs_img_tag.getElementsByTagName("img")[0].src;
                    } else {
                        img_src = document.getElementById('J_ImgBooth').src;
                    }
                } catch (e) {
                    console.log(e);
                }
            } else {
                // Find thumb image
                var thumbs_a_tag = document.getElementById('J_UlThumb');
                if (thumbs_a_tag != null) {
                    img_src = thumbs_a_tag.getElementsByTagName("li")[0].style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
                } else {
                    img_src = document.getElementById('J_ImgBooth').style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
                }
            }

        } catch (e) {
            console.log("Image not found!" + e);
        }

        img_src = GlobalTool.resizeImage(img_src);
        return encodeURIComponent(img_src);
    };


    this.getItemID = function(){
        var home = window.location.href;
        var item_id = this.common.getParamsUrl('id',home);
        var dom_id = document.getElementsByName("item_id");
        if(item_id <= 0 || !$.isNumeric(item_id)){
            if (dom_id.length > 0) {
                dom_id = dom_id[0];
                item_id = dom_id.value;
            } else item_id = 0;

            if (item_id == 0 || item_id == null || item_id == '') {
                dom_id = document.getElementsByName("item_id_num");
                if (dom_id.length > 0) {
                    dom_id = dom_id[0];
                    item_id = dom_id.value;
                } else item_id = 0;
            }
        }

        if(parseInt(item_id) <= 0 || !$.isNumeric(item_id)){
            item_id = home.split('.htm')[0];
            item_id = item_id.split('item/')[1];
        }

        return item_id;
    };

    this.add_to_cart = function () {
        /* chuẩn bị dữ liệu */

        var check_select = this.checkSelectFull();

        if(!check_select){
            alert("Bạn chưa chọn đầy đủ thuộc tính sản phẩm");
            return false;
        }

        //color size
        var price_origin = this.getOriginPrice();
        var price_promotion = this.getPromotionPrice();

        if($.isArray(price_origin)){
            price_origin = price_origin[0];
        }

        if($.isArray(price_promotion)){
            price_promotion = price_promotion[0];
        }

        var property = this.getProperties();
        var properties_origin = this.getPropertiesOrigin();
        //
        var image_origin = this.getImgLink();


        var shop_id = this.getSellerId();
        var shop_name = this.getSellerName();

        var shop_wangwang = this.getWangwang();

        //so luong
        var stock = $('#J_SpanStock').text();
        var quantity = this.getQuantity();
        //title
        var title_origin = this.getTitleOrigin();
        var title_translate = this.getTitleTranslate();
        //comment
        var comment = $('.tb-action textarea').val();
        //item
        var link_origin = window.location.href;
        var item_id = this.getItemID();

        var data_value = this.getDataValue();
        var outer_id = this.getOuterId(data_value);

        if($.isArray(outer_id)){
            outer_id = outer_id[0];
        }

        var data = {
            title_origin: $.trim(title_origin),
            title_translated: $.trim(title_translate),
            price_origin:price_origin,
            price_promotion:price_promotion,
            property_translated:property,
            property:properties_origin,
            data_value:data_value,
            image_model:image_origin,
            image_origin:image_origin,
            shop_id:shop_id,
            shop_name:shop_name,
            wangwang:decodeURIComponent(shop_wangwang),
            quantity:quantity,
            stock:stock,
            site:this.source,
            comment:comment,
            item_id:item_id,
            link_origin:link_origin,
            outer_id:outer_id,
            weight:0,
            step:1,
            tool: "Addon"
        };

        /* check valid */
        if(data.shop_id == '') {
            alert("Chúng tôi không lấy được thông tin của Shop. Sếu Đỏ sẽ khắc phục tình trạng này sớm nhất");
            this.common.track(data.link_origin, 'shop_id is not detected !');
            return false;
        }
        if(data.shop_name == '') {
            alert("Chúng tôi không lấy được thông tin của Shop. Sếu Đỏ sẽ khắc phục tình trạng này sớm nhất");
            this.common.track(data.link_origin, 'shop_name is not detected !');
            return false;
        }
        /*if(data.property == '') {
         this.common.track(data.link_origin, 'property is not detected !');
         return false;
         }
         if(data.data_value == '') {
         this.common.track(data.link_origin, 'data_value is not detected !');
         return false;
         }*/

        /* add to cart */
        $.ajax({
            url:this.common.add_to_cart_url,
            data:data,
            type:'post',
            success:function(result) {
                $('body').append(result.html);
            }
        });
        return false;
    };
};