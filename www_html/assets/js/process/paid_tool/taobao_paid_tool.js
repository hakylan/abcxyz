/**
 * Created by Quyen on 3/24/14.
 */

function taobao(cart_url, url_save) {
    function addGlobalStyle(css) {
        var head, style;
        head = document.getElementsByTagName('head')[0];
        if (!head) {
            return;
        }
        style = document.createElement('style');
        style.type = 'text/css';
        style.innerHTML = css;
        head.appendChild(style);
    }

    // Cross-browser implementation of element.addEventListener()
    function addListener(element, type, expression, bubbling) {
        bubbling = bubbling || false;
        if (window.addEventListener) { // Standard
            element.addEventListener(type, expression, bubbling);
            return true;
        } else if (window.attachEvent) { // IE
            element.attachEvent('on' + type, expression);
            return true;
        } else return false;
    }

    addGlobalStyle('.tahoma { font-family: tahoma,arial,verdana ! important; }');

    function getPriceTaobao(site) {
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
                price = processPrice(price,site);

                if(price == 0) { // Try if price not found
                    try{
                        price = normal_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }catch(e){
                        price = normal_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);

                    }
                    price = processPrice(price,site);
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
                price = processPrice(price,site);
            }
            return price;
        }catch(ex){
            throw Error(ex.message+ " Line:" +ex.lineNumber + " function getPriceTaobao");
        }

    }

    function getOriginPrice(){
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

                if(tb_rmb_num != null && typeof tb_rmb_num === 'object' && tb_rmb_num.length > 0){
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

            return processPrice(price);
        }catch(ex){
            throw Error(ex.message+ " Can't get origin price function getOriginPrice");
        }

    }

    function getDomTitle(){
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
    }

    function getTitleTranslate(){
        var _title = getDomTitle();
        var title_translate = _title.textContent;
        if(title_translate == ""){
            title_translate = _title.getAttribute("data-text");
        }
        return title_translate;
    }

    function getTitleOrigin(){
        var _title = getDomTitle();
        var title_origin = _title.getAttribute("data-text");
        if(title_origin == "" || typeof title_origin == "undefined" || title_origin == null){
            title_origin = _title.textContent;
        }
        return title_origin;
    }

    function getTitle() {
        var title_translate = getTitleTranslate();
        var title_origin = getTitleOrigin();

        title_origin = title_origin.replace(/<\/?[^>]+(>|$)/g, "");
        title_translate = title_translate.replace(/<\/?[^>]+(>|$)/g, "");

        title_origin = title_origin.trim();
        title_translate = title_translate.trim();
        return  'title_origin=' + encodeURIComponent(title_origin) + '&title_translated=' + encodeURIComponent(title_translate);

    }

    function getImgLink() {
        var img_src = "";
        try {
            var img_obj = document.getElementById('J_ImgBooth');
            if (img_obj != null) { // Image taobao and t
                img_src = img_obj.getAttribute("src");
                img_src = GlobalPaid.resizeImage(img_src);
                return encodeURIComponent(img_src);
            }

            img_obj = document.getElementById('J_ThumbView');

            if(img_obj != null && img_obj != ""){
                img_src = img_obj.getAttribute("src");
                img_src = GlobalPaid.resizeImage(img_src);
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

        img_src = GlobalPaid.resizeImage(img_src);
        return encodeURIComponent(img_src);
    }

    function getSellerName() {
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
    }

    //ham lay comment
    function getComment() {
        var comment = document.getElementById("_comment_item");
        if (comment != null) {
            comment = comment.value;
        } else {
            comment = "";
        }
        return encodeURIComponent(comment);
    }

    function getProperties(){
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
    }

    /**
     * Lay thuoc tinh chua duoc dich
     * @returns {string}
     */
    function getPropertiesOrigin(){
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
    }

    /**
     * get data value
     * @returns {string}
     */
    function getDataValue(){
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
    }

    function getOuterId(data_value){
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
    }

    function getSellerId(){
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
            console.log(6);
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
            console.log(url_shop);
            url = url_shop.getAttribute('href');
            seller_id = url.split('.')[0];
            seller_id = seller_id.split('http://')[1];
        }

        return seller_id;
    }

    function getItemId(){
        var home = window.location.href;
        var dom_id = document.getElementsByName("item_id");
        var item_id = 0;
        if (dom_id.length > 0) {
            dom_id = dom_id[0];
            item_id = dom_id.value;
        } else item_id = 0;

        if (item_id <= 0  || isNaN(item_id)) {
            dom_id = document.getElementsByName("item_id_num");
            if (dom_id.length > 0) {
                dom_id = dom_id[0];
                item_id = dom_id.value;
            } else item_id = 0;
        }

        if(item_id <= 0 || isNaN(item_id)){
            item_id = home.split('.htm')[0];
            item_id = item_id.split('item/')[1];
        }

        if(item_id <= 0 || isNaN(item_id)){
            item_id = GlobalPaid.getParamsUrl("id",home);
        }

        return item_id;
    }

    function getLink() {
        var params = "tool=Bookmarklet";
        try {
            if(window.location.href.indexOf('tmall') > -1){
                params += "&site=tmall";
            }else if(window.location.href.indexOf('taobao') > -1){
                params += "&site=taobao";
            }else{
                return false;
            }
            //lay title
            var title = getTitle();
            var stock_id = document.getElementById('J_EmStock');
            var stock = 99;
            if(stock_id == null || stock_id == 'undefined'){
                stock_id = document.getElementById("J_SpanStock");
            }

            if(stock_id != null && stock_id != 'undefined'){
                stock = stock_id.textContent;
                stock = parseInt(stock.replace(/[^\d.]/g, ''));
            }

            var item_id = getItemId();
            var seller_id = getSellerId();

            var quantity = '';
            var ipt_amount = document.getElementById("J_IptAmount");
            if (ipt_amount) {
                quantity = ipt_amount.value;
            } else quantity = '';
            // Get amount: 13/9/2013
            if (quantity == '') {
                try {
                    quantity = document.getElementsByClassName('mui-amount-input')[0].value;
                } catch (e) {
                    console.log(e);
                }
            }

            //lay img
            var img = getImgLink();
            //lay seller name
            var seller_name = getSellerName();
            var allow_quantity = document.getElementsByName("allow_quantity");
            var amount = 0;
            if (allow_quantity.length > 0) {
                allow_quantity = allow_quantity[0];
                amount = allow_quantity.value;
            } else amount = 0;

            var data_value = getDataValue();

            var property_translate = getProperties();
            var property_origin = getPropertiesOrigin();

            var outer_id = getOuterId(data_value);

            var item_price = getPriceTaobao();
            var price_origin = getOriginPrice();
            //lay comment
            var comment = getComment();

            params += '&item_id=' + item_id;
            if (item_price > 0) params += '&price_promotion=' + item_price;
            if (price_origin > 0) params += '&price_origin=' + price_origin;
            if (title != ""){
                params += '&' + title;
            }
            if (img != ""){
                params += '&image_model=' + img;
                params += '&image_origin=' + img;

            }

            if (seller_name != "" || seller_id != ""){
                params += '&shop_id=' + seller_id;
                params += '&shop_name=' + seller_name;
            }
            if (parseInt(quantity) > 0) params += '&quantity=' + quantity;
            if (parseInt(amount) > 0) params += '&amount=' + amount;
            if (property_origin != ""){
                params += '&property=' + property_origin;
                if(property_translate != ''){
                    params += '&property_translated=' + property_translate;
                }else{
                    params += '&property_translated=' + property_origin;
                }
            }

            // Lay comment
            if (comment != "") {
                params += '&comment=' + comment;
            }

            if (data_value.length > 0) {
                params += '&data_value=' + data_value;
            }

            params += '&link_origin=' + encodeURIComponent(window.location.href);

            params += "&stock="+stock;
            params += "&outer_id="+outer_id;

            var wangwang = getWangWang();
            if(wangwang != ""){
                params += '&wangwang=' + wangwang;
            }
            return params;

        } catch (e) {
            GlobalPaid.trackError(window.location.href, e.message + " Line: " + e.lineNumber);
            alert("Co su co xay ra khong the dat hang voi san pham nay. Chung toi se khac phuc van de nay som nhat");
            console.log("Co su co xay ra do web site thay doi! [" + e.message + " Line: " + e.lineNumber + "]");
            return "";

        }
        // return href+params;
    }

    function getWangWang(){
        var span_wangwang = document.querySelectorAll("span.seller");

        var wangwang = "";

        if(span_wangwang.length > 0){
            if(span_wangwang.length > 0){
                span_wangwang = span_wangwang[0].getElementsByTagName("span");
                wangwang = decodeURIComponent(span_wangwang[0].getAttribute('data-nick'));
            }
        }
        return wangwang;
    }

    function xmlhttpPost(strURL) {
        var xmlHttpReq = this;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            self.xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        var beforeHtml = document.getElementById("block_button_sd").innerHTML;
        document.getElementById("block_button_sd").innerHTML =
            '<img style="margin-top:12px;margin-right:50px" src="http://orderhang.com/frontend/images/ajax-loader.gif" alt="" />';
        self.xmlHttpReq.open('POST', strURL, true);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        self.xmlHttpReq.withCredentials = "true";

        self.xmlHttpReq.onreadystatechange = function () {
            if (self.xmlHttpReq.readyState == 4) {
                if (strURL.indexOf('item_collect') != -1) {
                    //luu san pham
                    if (self.xmlHttpReq.responseText == 'OK'){
                        console.log('Luu san pham thanh cong!');

                    } else{
                        console.log(self.xmlHttpReq.responseText);
                    }
                } else{
                    var result = JSON.parse(self.xmlHttpReq.responseText);
                    updatepage(result.html);
                }

                document.getElementById("block_button_sd").innerHTML = beforeHtml;
                addListener(document.getElementById("sd_add_cart"), 'click', linkClick);
//                addListener(document.getElementById("id_orderhang_save_item"), 'click', linkSave);
            }
        };
        var param = getLink();
        if(param != ""){
            self.xmlHttpReq.send(param);
        }
    }

    function updatepage(str) {
        //console.log(str);
        //var dv=document.createElement(str);
        var htmlObject = document.createElement('div');
        htmlObject.style.position = "relative";
        htmlObject.style.zIndex = "2147483647";
        htmlObject.innerHTML = str;

        document.body.appendChild(htmlObject);
        //document.getElementById("result").innerHTML = str;
    }

    function linkClick() {
//        try {
        //tong thuoc tinh
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
        if (full == true)
            document.getElementById("sd_add_cart").setAttribute('target', "_blank");
        else {
            document.getElementById("sd_add_cart").setAttribute('target', "");
            alert("Ban chua chon day du thuoc tinh san pham");
//                console.log("Bạn chưa chọn đầy đủ thuộc tính sản phẩm!");
            document.getElementById("sd_add_cart").setAttribute('href', 'javascript:void(0);');
            return;
        }
        // Check amount input not found
        // 13/9/2013
        var out_of_stock = true;
        if (document.getElementById("J_IptAmount") != null || document.getElementById("J_Amount") != null) {
            out_of_stock = false;
        }

        if (out_of_stock) {
            document.getElementById("sd_add_cart").setAttribute('target', "");
            alert("San pham het hang");
//                console.log("Hết hàng!");
            document.getElementById("sd_add_cart").setAttribute('href', 'javascript:void(0);');
            return;
        }
        //document.getElementById("sd_add_cart").setAttribute('href',href);
        document.getElementById("sd_add_cart").setAttribute('href', 'javascript:void(0)');
        document.getElementById("sd_add_cart").setAttribute('target', "");

        xmlhttpPost(cart_url);
//        } catch (e) {
//            console.log(e);
//        }
    }

    this.htmlOnLoad = function () {
//        translate_title();
//        translate_properties();

//        try {
//            // Translate ------------
//            get_html_translate();
//             var wrapper_content = null;
//             if (host.indexOf('tmall') != -1) {
//             wrapper_content = document.getElementsByClassName('tb-sku')[0];
//             } else {
//             wrapper_content = document.getElementById('J_isku');
//             }
//             after_translate(wrapper_content);
//        } catch (e) { }
        // -------------

        try{
            //  Add class  notranslate
            if (window.location.href.indexOf('tmall') > 0) {
                var shop_info = document.getElementById('J_StrPriceModBox');
                var shop_info_2 = document.getElementById('J_PromoBox');
                if(shop_info != null) {
                    shop_info.className = shop_info.className + ' notranslate';
                }
                if(shop_info_2 != null) {
                    shop_info_2.className = shop_info_2.className + ' notranslate';
                }
            }

            var sd_add_cart = document.getElementById("sd_add_cart");

            if(sd_add_cart == null){
                update_html();
                //setTimeout(,2000);
            }else{
                setTimeout(hideLoading(),3000);
            }
//        wait_by_call_ajax(1);
        }catch(ex){
            console.log(ex.message);
            alert("Trang web nay dang co chut thay doi ve giao dien, chung toi se khac phuc som de quy khach co the dat duoc hang.");
        }


    };



    /**
     * Update Html
     */
    function update_html() {

        try {

            var tb_detail_bd = document.getElementsByClassName("tb-detail-bd");

            var J_DetailMeta = document.getElementById("J_DetailMeta");

            var style_box = "background: none repeat scroll 0px 0px rgb(255, 255, 255); " +
                "position: relative; " +
                "z-index: 999; " +
                "margin-left: -5px; " +
                "margin-right: -5px; " +
                "border: 3px solid rgb(207, 36, 64); " +
                "margin-top: 15px; " +
                "box-shadow: 0px 0px 15px 3px rgb(204, 204, 204);";

            if(tb_detail_bd.length > 0){
                tb_detail_bd[0].setAttribute("style",style_box);
            }else if(J_DetailMeta != null){

                var tm_clear = J_DetailMeta.getElementsByClassName("tm-clear");

                if(tm_clear != null){
                    tm_clear[0].setAttribute("style",style_box);
                }
            }

            var img_url = GlobalPaid.imgUrl();

            var homeUrl = GlobalPaid.baseUrl();

            var src = img_url+'/icon-bkv1.png?t=' + Math.random();
            var rate = GlobalPaid.getExchangeRate();
            var site = "TAOBAO"
            if(window.location.href.indexOf('tmall') > -1){
                site = "TMALL";
            }else if(window.location.href.indexOf('taobao') > -1){
                site = "TAOBAO";
            }
            var price_taobao = getPriceTaobao(site);

            var price_result = GlobalPaid.currency_format(price_taobao * rate);

            var com_text = '<div style="clear:both;"><span id="span_title_note" style="width:15%;float:left;margin-right:5px;font-size:13px;">Chu Thich:' +
                ' </span><textarea cols="60" style=" height: 40px; padding: 5px; font-size: 13px; width: 95%; margin-top: 5px;" ' +
                'id="_comment_item" placeholder="Chu thich cho san pham" name="_comment_item"></textarea></div>';
            //html vung luu lai
            var save_text = "";

            //html toan bo addon
            var s = '<li class="clearfix" id="li_sd_price" style="padding-top:10px;overflow:auto;">' + com_text +
                '<div class="xbTipBlock tahoma"><div style="width:100%;float:right">' +
                ' <div style="float: left; margin-top: 10px;" id="block_button_sd">' +
                '<a id="sd_add_cart" href="javascript:;"></a><a href="'+homeUrl+'/gio-hang" class="cart" target="_blank" ' +
                'style="display: inline-block; margin-left: 20px; color: rgb(0, 114, 188);">Vao gio hang</a>' +
                '<p style="color: red; font-size: 17px">De nghi khong su dung Google Translate. Dieu nay lam chung toi mua nham hang cho ban</p>' +
                '</div></div></div></li>';

            var div = document.createElement('div');
            div.innerHTML = s;

            var wrapper_content = null;
            var J_juValid = null;
            if (host.indexOf('tmall') != -1) {
                J_juValid = document.getElementsByClassName("tb-action");
                if(J_juValid != null){
                    try{
                        J_juValid.remove();
                    }catch (e){
                        J_juValid[0].remove();
                    }
                }
                wrapper_content = document.getElementsByClassName('tb-sku')[0];
            } else {
                J_juValid = document.getElementById("J_juValid");
                if(J_juValid != null){
                    try{
                        J_juValid.remove();
                    }catch (e){
                        J_juValid[0].remove();
                    }
                }
                //wrapper_content = document.getElementById('J_isku');
                wrapper_content = document.getElementsByClassName('tb-wrap');
                if(wrapper_content != null && typeof wrapper_content === 'object' && wrapper_content.length > 0){
                    wrapper_content = wrapper_content[0];
                }else{
                    wrapper_content = document.getElementById('J_isSku');
                    if(wrapper_content != null){
                        var box_buy_cart = document.getElementById('J_box_buycart');
                        if(box_buy_cart != null){
                            try{
                                box_buy_cart.remove();
                            }catch (e){
                                box_buy_cart[0].remove();
                            }
                        }
                    }
                }
            }
//            wrapper_content.style.border = "2px solid blue";
            wrapper_content.appendChild(div.firstChild);

            // Add cac su kien
            addListener(document.getElementById("sd_add_cart"), 'click', linkClick);
//            addListener(document.getElementById("id_orderhang_save_item"), 'click', linkSave);
        } catch (e) {
            throw Error(e.message);
        }

        setTimeout(hideLoading(),3000);

    }
    function translate_title() {
        var host_name = window.location.hostname;
        var title = '';
        var _title = null;
        if (document.getElementsByClassName("tb-detail-hd").length > 0) {
            var h = document.getElementsByClassName("tb-detail-hd")[0];
            if (h.getElementsByTagName('h3').length > 0) {
                _title = h.getElementsByTagName('h3');
                title = h.getElementsByTagName('h3')[0].textContent;
            }
        }

        if (title == "" && document.getElementsByClassName("tb-tit").length > 0) {
            _title = document.getElementsByClassName("tb-tit");
            title = document.getElementsByClassName("tb-tit")[0].textContent;
        }

        // 27/7/2013
        if (title == "") {
            _title = document.querySelectorAll('h3.tb-item-title');
            if (_title != null) {
                title = _title[0].textContent;
            }else{
                _title = document.getElementsByClassName('tb-item-title');
                if(_title.length > 0){
                    title = _title[0].textContent;
                }
            }
        }
        title = title.trim();

        GlobalPaid.translate(title,"title",_title[0]);
    }

    function translate_properties(){
        var host_name = window.location.hostname;
        var tbl_sku = null;
        tbl_sku = document.getElementsByClassName('J_TSaleProp');
        if(tbl_sku.length == 0 || tbl_sku == null){
//            tbl_sku
        }

        var span_text = null;
        for (var i = 0; i < tbl_sku.length; i++) {
            var li_prop = tbl_sku[i].getElementsByTagName("li");
            for (var j = 0; j < li_prop.length; j++) {
                var $this = li_prop[j];
                var span = $this.getElementsByTagName("span");
                if(span != null){
                    span_text = span[0];
                    var text = span[0].textContent;
                    text = text.trim();
                    GlobalPaid.translate(text,"properties",span_text);
                }
            }
        }
    }
}