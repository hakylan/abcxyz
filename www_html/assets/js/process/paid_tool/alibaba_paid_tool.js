function alibaba(cart_url, url_save) {
    var beforeHtml = '';
    // Rate
    function rateMoney() {
        return 3300;
    }

    // Comment
    function getComment() {
        var comment = document.getElementById("_comment_sd");
        if (comment != null) {
            comment = comment.value;
        } else {
            comment = "";
        }
        return encodeURIComponent(comment);
    }

    function getRequireMin(){
        var require_min = 1;
        try{
            require_min = iDetailConfig.beginAmount;
        }catch (e){
            require_min = 1;
        }
        return require_min;
    }

    /**
     * get Step item
     * @returns {number}
     */
    function getStep(){
        var step = 1;
        var purchasing_multiple = document.getElementsByClassName('mod-detail-purchasing-multiple');
        var purchasing_single = document.getElementsByClassName('mod-detail-purchasing-single');
        var purchasing_quotation = document.getElementsByClassName('mod-detail-purchasing-quotation');

        var purchasing = null;

        if (purchasing_multiple.length > 0 && purchasing_multiple != null) {
            purchasing = JSON.parse(purchasing_multiple[0].getAttribute("data-mod-config"));
            step = purchasing.wsRuleNum;
        } else if (purchasing_single.length > 0 && purchasing_single != null) { //SINGLE MODE
            purchasing = JSON.parse(purchasing_single[0].getAttribute("data-mod-config"));
            step = purchasing.wsRuleNum;
        } else if (purchasing_quotation.length > 0 && purchasing_quotation != null) {
            step = 0;
        } else {
            step = 1;
        }
        if(step == '' || step == null){
            step = 1;
        }

        return step;
    }

    // Get min amount
    function getMinAmount() {

        var min_amount = 1;

        var list_amount = document.getElementById("mod-detail-price");
        if (list_amount == null) {
            return min_amount;
        }

        var span_amount = list_amount.getElementsByTagName("span");
        if (span_amount == null) {
            return min_amount;
        }

        var str = span_amount[0].textContent;

        // Find range of amount
        if (str.indexOf('-') != -1) {
            return str.split('-')[0];
        }
        // Less than
        if (str.indexOf('<') != -1) {
            return min_amount;
        }
        // Greater than
        if (str.indexOf('>') != -1) {
            return str.split('>')[0];
        }
        if (str.indexOf('?') != -1) {
            return str.split('?')[1];
        }
        return min_amount;
    }


    // Hàm l?y b?ng giá
    function getPriceTable() {
        //-- get price amount
        var price_table = [];

        var price_range = null;

        var pri = [];

        var detail_price = null;

        var tr_price = null;

        var i = 0;

        try{
            detail_price = document.getElementById("mod-detail-price");
            if (detail_price != null) { //price by amount

                var price_container = detail_price.getElementsByClassName("unit-detail-price-amount");

                if(price_container != null && price_container.length > 0){
                    tr_price = price_container[0].getElementsByTagName("tr");

                    if(tr_price.length > 0){
                        for (i = 0; i < tr_price.length; i++) {
                            pri = tr_price[i];
                            price_range = JSON.parse(pri.getAttribute("data-range"));
                            price_table.push(price_range);
                        }
                    }
                }else{
                    tr_price = detail_price.querySelectorAll("tr.price td");
                    if(tr_price != null && tr_price.length > 0){
                        for (var j = 0; j < tr_price.length; j++) {
                            try{
                                pri = tr_price[j];
                                var range = pri.getAttribute("data-range");
                                if(range !== ""){
                                    price_range = JSON.parse(range);
                                    price_table.push(price_range);
                                }
                            }catch(e){

                            }

                        }
                    }
                }
            } else {
                var price = {};
                var price_common = document.getElementsByClassName("offerdetail_common_beginAmount");

                // One price
                if(price_common.length > 0) {
                    price.begin = price_common[0].getElementsByTagName('p')[0].textContent;

                    price.begin = price.begin.match(/[0-9]+/)[0];
                    // get prices
                    detail_price = document.getElementsByClassName("unit-detail-price-display")[0].textContent.split('-');
                    var price_display = {};
                    for(i = 0; i < detail_price.length; i++) {
                        price_display[i] = detail_price[i].match(/[0-9]*[\.]?[0-9]+/g).join('');
                    }
                    price.price = price_display;
                    price.end = "";
                }
                price_table.push(price);
            }
        }catch(ex){
            price_table = [];
            console.log("Fuck "+ex);
        }

        return JSON.stringify(price_table);
    }

    // Get price by item amout
    function getPrice(quantity) {

        try{
            quantity = parseInt(quantity);

            var price = 0;
            var div_price = null;
            var price_table = null;
            var span_price = document.getElementsByClassName('mod-detail-price-sku');
            if (span_price != null) {
                span_price = span_price[0];
            }
            // M?t m?c giá
            if (span_price != null) {
                //price=span_price.textContent;
                var e_num = document.getElementsByClassName('mod-detail-price-sku')[0].getElementsByTagName('span')[2].textContent;
                var p_num = document.getElementsByClassName('mod-detail-price-sku')[0].getElementsByTagName('span')[3].textContent;
                price = e_num + p_num;
                if(processPrice(price) > 0){
                    return processPrice(price)
                }
            }

            // Nhi?u m?c giá
            var div_prices = document.getElementById("mod-detail-price");
            if (div_prices == null || div_prices.length <= 0) {
                if(processPrice(price) > 0){
                    return processPrice(price)
                }
            }else{
                var span_prices = div_prices.getElementsByTagName("span");

                if(span_prices != null){
//                    // Duy?t qua các m?c giá
//                    var quan_compare = '';
//                    for (var i = 0; i < span_prices.length; i++) {
//                        var str = span_prices[i].textContent;
//                        if ((str.indexOf('-') != -1) || (str.indexOf('?') != -1)) {
//                            if (str.indexOf('-') != -1) {
//                                quan_compare = str.split('-');
//                                price = span_prices[i + 1].textContent + '' + span_prices[i + 2].textContent;
//                                if (quantity >= quan_compare[0] && quantity <= quan_compare[1]) {
//                                    break;
//                                }
//                            }
//                            if (str.indexOf('?') != -1) {
//                                price = span_prices[i + 1].textContent + '' + span_prices[i + 2].textContent;
//                            }
//                        }
//                    }

                    if(processPrice(price) <= 0){
                        price_table = getPriceTable();

                        price = getPriceByPriceTable(price_table,quantity);
                    }
                }else{
                    price_table = getPriceTable();
                    price = getPriceByPriceTable(price_table,quantity);
                }
            }
            price = processPrice(price);


            if(price <= 0){
                try{
                    div_price = document.getElementsByClassName("obj-price");
                    if(div_price != null){
                        span_price = div_price[0].getElementsByClassName("price");
                        price = span_price[0].textContent;
                    }
                }catch(ex){
                    price = 0;
                }

            }
            price = processPrice(price);

            if(price <= 0){
                try{
                    div_price = document.getElementsByClassName("price-discount-sku");
                    if(div_price != null){
                        span_price = div_price[0].getElementsByClassName("value");
                        price = span_price[0].textContent;
                    }
                }catch(ex){
                    price = 0;
                }
            }

            price = processPrice(price);

            if(price <= 0){
                try{
                    var tr_price = document.querySelectorAll("tr.price");
                    if(tr_price != null){
                        span_price = tr_price[0].querySelectorAll("span.value");
                        price = span_price[0].textContent;
                    }
                }catch(ex){
                    price = 0;
                }
            }
            return processPrice(price);
        }
        catch(e){
//            this.common.track(window.location.href, e);
            GlobalPaid.trackError(window.location.href,e);
            console.log("Error mesage: " + e);
            return 0;
        }
    }

    function getPriceByPriceTable(price_table,quantity){
        var price = 0;
        try{
            price_table = JSON.parse(price_table);
            if(typeof price_table === 'object' ) {
                for (var o in price_table) {
                    if(price_table[o] != null){
                        var begin = price_table[o].begin;
                        var end = price_table[o].end;

                        if ((begin <= quantity && quantity <= end) ||
                            (begin <= quantity && (parseInt(end) == 0 || end == null || end == "")) || quantity <= begin) {
                            price = price_table[o].price;
                            break;
                        }else{
                            price = price_table[o].price;
                        }
                    }
                }
            }
        }catch (e){
            price = 0;
        }

        return price;
    }

    // Seller id
    function getShopName() {
        var seller_id = '';
        try {
            var element = document.getElementsByName("sellerId");
            if (element.length > 0) {
                element = element[0];
                seller_id = element.value;
            } else {
                // New 24/4/2013
                element = document.getElementsByClassName('contact-div');
                if (element.length > 0) {
                    seller_id = element[0].getElementsByTagName('a')[0].innerHTML;
                }
            }
        } catch (e) {
            console.log('Khong lay duoc thong tin nguoi ban!');
        }

        return encodeURIComponent(seller_id);
    }

    function getShopId(){
        var shop_id = "";
        try{
            var WolfSmoke = WolfSmoke;
            shop_id = WolfSmoke.acookieAdditional.member_id;
        }catch (e){
            try{
                shop_id = iDetailConfig.feedbackUid;
            }catch (err){
                shop_id = "";
            }
        }

        if(shop_id == ""){
            var a_shop = document.getElementsByClassName("tplogo");
            if(a_shop != null && a_shop.length > 0){
                var href = a_shop[0].getAttribute("href");
                shop_id = href.split('.')[0];
                shop_id = shop_id.split('http://')[1];
            }
        }

        return shop_id;
    }

    /**
     * Get Item Id
     * @returns {number}
     */
    function getItemId() {

        var offerid = 0;
        try{
            offerid = iDetailConfig.offerid;
        }catch (e){
            offerid = 0;
        }
        return offerid;

    }

    function getStock(){
        var stock = 0;
        try{
            stock = iDetailData.sku.canBookCount;
        }catch (e){
            stock = 0;
        }
        return stock;
    }

    // Item title
    function getItemTitle() {
        var element = document.getElementsByName("offerTitle");
        var item_title = '';
        if (element.length > 0) {
            element = element[0];
            item_title = element.value;
        } else {
            // New 24/4/2013
            if (document.getElementById('mod-detail-hd') != null) {
                item_title = document.getElementById('mod-detail-hd').getElementsByTagName('h1')[0].innerHTML;
            } else {
                item_title = '';
            }
        }

        return encodeURIComponent(item_title);
    }

    // Item image
    function getItemImage() {
        var main_image = document.getElementsByClassName("mod-detail-gallery");
        var item_image = "";
        if (main_image != null) {
            var img_obj = main_image[0].getElementsByTagName("img");
            if (img_obj.length > 1) {
                item_image = img_obj[1].getAttribute('src');
            } else {
                // Large image
                item_image = img_obj[0].getAttribute('src');
            }
        }
        item_image = GlobalPaid.resizeImage(item_image);
        return encodeURIComponent(item_image);
    }

    // Item link
    function getItemLink() {
        return encodeURIComponent(window.location.href);
    }

    // VN Price
    function getVNDPrice(price) {
        var price_result = roundNumber(price * GlobalPaid.getExchangeRate(), 2);

        return GlobalPaid.currency_format(price_result);
    }

    function getWangwang(){
        var wangwang = "";
        try{
            wangwang = eService.contactList[0].name;
        }catch (e){
            wangwang = "";
        }

        return wangwang;
    }

    function getWeight(){
        var weight = 0;
        try{
            var unit_detail = document.getElementsByClassName("unit-detail-freight-cost");
            if(unit_detail.length > 0){
                var carriage = JSON.parse(unit_detail[0].getAttribute("data-unit-config"));
                weight = !isNaN(carriage.unitWeight) ? carriage.unitWeight : 0;
            }
        }catch (e){
            weight = 0;
        }
        return parseFloat(weight);
    }

    // Post data to cart
    // Url: url posted
    // item_data: data of item (amount, min_amount, size, color)
    function xmlhttpPost(strURL, item_data, pos) {
        var self = this;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            self.xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }

        document.getElementById("block_button_sd").innerHTML
            = '<img style="margin-top:12px;margin-right:50px" src="http://orderhang.com/frontend/images/ajax-loader.gif" alt="" />';
        self.xmlHttpReq.open('POST', strURL, true);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        self.xmlHttpReq.withCredentials = "true";

        self.xmlHttpReq.onreadystatechange = function () {
            if (self.xmlHttpReq.readyState == 4) {
                if (strURL.indexOf('item_collect') != -1) {
                    //luu san pham
                    if (self.xmlHttpReq.responseText == 'OK')
                        console.log('Lưu s?n ph?m thành công!');
                    else console.log(self.xmlHttpReq.responseText);
                } else {
                    var result = JSON.parse(self.xmlHttpReq.responseText);
                    updatepage(result.html);
                }
                document.getElementById("block_button_sd").innerHTML = beforeHtml;
                document.getElementById("sd_add_cart").addEventListener('click', linkClick);
            }
        };
        // Send data
        self.xmlHttpReq.send(getLink(item_data, pos));
        return true;
    }

    function updatepage(str) {
        var htmlObject = document.createElement('div');
        htmlObject.innerHTML = str;
        document.body.appendChild(htmlObject);
        return true;
    }

    /**
     * L?y d? li?u send
     * return Array 2 chi?u
     *  result[i]['amount'] = 0;
     result[i]['min_amount'] = 0;
     result[i]['size'] = 0;
     result[i]['color'] = 0;
     result[i]['price'] = 0;
     * data g?m amount, color, size, min_amount
     **/
    function get_item_data() {

        var result = new Array();
        var input_data = new Array();
        var i = 0;
        var parent_obj = null;

        try {
            // Multi buy
            var tbl_wrap = document.getElementsByClassName('content-wrapper');
            var content = null;
            var color = null;
            if (tbl_wrap.length > 0) {
                console.log("length = "+tbl_wrap.length);
                content = tbl_wrap[0].getElementsByClassName('content');
            }
            var d_content = document.getElementsByClassName('d-content');
            /**
             * Chú thích m?ng Result:
             * [0] => Quantity
             * [1] => Stock
             * [2] => Site
             * [3] => Màu s?c
             * [4] => price
             */
            if (content != null) { // New 22/5/2013
                content = content[0];
                input_data = content.getElementsByClassName('amount-input'); // Get s? lư?ng đ?t
                if (input_data.length > 0) {
                    i = 0;
                    /**
                     * Có class 'leading': màu s?c n?m trong class leading
                     * danh sách phía dư?i là kích thư?c
                     * N?u không có class 'leading', không có kích thư?c, ch? có màu s?c
                     */
                    color = tbl_wrap[0].getElementsByClassName('leading');
                    if (color.length > 0) { // Has color, and size
                        color = color[0].getElementsByClassName('selected')[0].getAttribute('title').replace(/\n+/, '').replace(/\s+/, '');
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            console.log(parent_obj);
                            result[i] = new Array();
                            // Add data to arrayn
                            result[i][0] = input_data[inc].value;
                            result[i][1] = parent_obj.getElementsByClassName('count')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, "");
                            result[i][2] = color == "" ? "" : parent_obj.getElementsByClassName('name')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, '').replace(/\n+/, '');
                            result[i][3] = color == "" ? parent_obj.getElementsByClassName('name')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, '').replace(/\n+/, '') : color;
                            result[i][4] = parent_obj.getElementsByClassName('price')[0].getElementsByTagName('em')[0].textContent.replace(/\s+/, "");
                            i++;
                        }
                    } else { // Có màu s?c, ko có size

                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = new Array();
                            // Add data to arrayn
                            result[i][0] = input_data[inc].value;
                            result[i][1] = parent_obj.getElementsByClassName('count')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, "");
                            result[i][2] = "";

                            var span_color = parent_obj.getElementsByClassName('name')[0].getElementsByTagName('span');
                            var img_color = parent_obj.getElementsByClassName('name')[0].getElementsByClassName('image');
                            result[i][3] = img_color.length > 0 ?
                                (img_color[0].getAttribute('title'))
                                :
                                span_color[0].textContent.replace(/\s+/, '').replace(/\n+/, '');
                            result[i][4] = parent_obj.getElementsByClassName('price')[0].getElementsByTagName('em')[0].textContent.replace(/\s+/, "");
                            i++;
                        }
                    }
                }
            } else{
                var obj_sku = document.getElementsByClassName('obj-sku');
                var obj_amount = document.getElementsByClassName('obj-amount');
                if(obj_sku != null && (typeof obj_sku === 'object' && obj_sku.length > 0)){
                    input_data = obj_sku[0].getElementsByClassName("amount-input");
                }else if(obj_amount != null && (typeof obj_amount === 'object' && obj_amount.length > 0)){
                    input_data = obj_amount[0].getElementsByClassName("amount-input");
                }

                if (input_data.length > 0) {
                    i = 0;
                    /**
                     * Có class 'leading': màu s?c n?m trong class leading
                     * danh sách phía dư?i là kích thư?c
                     * N?u không có class 'leading', không có kích thư?c, ch? có màu s?c
                     */
                    color = document.getElementsByClassName('obj-leading');
                    if (color.length > 0) { // Has color, and size
                        color = color[0].querySelectorAll('a.selected'); //
                        if(color != null){
                            color = color[0].getAttribute('title').replace(/\n+/, '').replace(/\s+/, '');
                        }
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = getProperties(parent_obj,input_data[inc],color);

                            i++;
                        }
                    } else { // Có màu s?c, ko có size
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = getProperties(parent_obj,input_data[inc],"");
                            i++;
                        }
                    }
                }
            }
            return result;
        } catch (e) {
            console.log("Error mesage: " + e);
            return "";
        }
    }

    function getProperties(tr_prop,input_data,color){
        var content = null;
        var count_span = null;
        var size_span = null;
        var price_span = null;
        var result = new Array();
        result[0] = input_data.value;
        count_span = tr_prop.getElementsByClassName('count');
        if(count_span != null && (typeof count_span === 'object' && count_span.length > 0)){
            result[1] = count_span[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, "");
        }else{
            result[1] = 9999;
        }
        size_span = tr_prop.getElementsByClassName('name');
        if(size_span != null && (typeof size_span === 'object' && size_span.length > 0 && color != "")){
            var span = size_span[0].getElementsByTagName('span')[0];
            if(GlobalPaid.hasClass(span,"image")){
                result[2] = span.getAttribute("title").
                    replace(/\s+/, '').replace(/\n+/, '');
            }else{
                result[2] = span.textContent.replace(/\s+/, '').replace(/\n+/, '');
            }
        }else{
            result[2] = "";
        }

        if(size_span != null && (typeof size_span === 'object' && size_span.length > 0) && color == ""){
            var span = size_span[0].getElementsByTagName('span')[0];
            if(GlobalPaid.hasClass(span,"image")){
                result[3] = span.getAttribute("title").
                    replace(/\s+/, '').replace(/\n+/, '');
            }else{
                result[3] = span.textContent.replace(/\s+/, '').replace(/\n+/, '');
            }
        }else{
            result[3] = color;
        }

        price_span = tr_prop.getElementsByClassName('price');

        if(price_span != null && (typeof price_span === 'object' && price_span.length > 0)){
            result[4] = price_span[0].getElementsByTagName('em')[0].textContent.replace(/\s+/, "");
        }else{
            result[4] = 0;
        }

        return result;
    }

    // Get link
    /**
     * item_data: Array
     * keys: amount, color, size
     */
    function getLink(item_data, pos) {

        var params = 'site=1688&tool=Bookmarklet';
        try {
            // S? th? t? l?n g?i request
            if (pos == null) {
                pos = 1;
            }
            //l?y item_id
            var item_id = getItemId();
            //l?y item_title
            var item_title = getItemTitle();
            //l?y item_image
            var item_image = getItemImage();
            //l?y item_link
            var item_link = getItemLink();
            //l?y seller_id
            var seller_id = getShopId();
            var seller_name = getShopName();
            //lay comment
            var comment = getComment();
            //lay ban gia
            var price_table = getPriceTable();

            var step = getStep();

            var require_min = getRequireMin();

            var stock = getStock();

            var wangwang = getWangwang();

            var weight = getWeight();

            params+="&wangwang="+wangwang;
            params+="&weight="+weight;
            params += "&step=" + step;
            params += "&require_min=" + require_min;

            //lay gia
            /*
             var item_price  = getPrice(item_data[0]);
             var price_distance = document.getElementById('mod-detail-price-sku');
             if(price_distance != null & (item_data[4] != "" || item_data[4] != undefined)) {
             item_price = item_data[4];
             }
             */
            var item_price = getPrice(item_data[0]);
            // Multi buy
            var tbl_wrap = document.getElementsByClassName('content-wrapper');
            if (tbl_wrap.length > 0) {
                if (tbl_wrap[0].getElementsByClassName('content').length > 0) {
                    item_price = item_data[4];
                }
            }
            if (parseInt(item_id) > 0) params += '&item_id=' + item_id;
            if (item_title != ''){
                params += '&title_origin=' + item_title;
                params += '&title_translated=' + item_title;
            }
            if (item_image != ''){
                params += '&image_origin=' + item_image;
                params += '&image_model=' + item_image;
            }
            if (comment != ''){
                params += '&comment=' + comment;
            }
            if (item_link != ''){
                params += '&link_origin=' + item_link;
            }
            if (item_price > 0){
                params += '&price_origin=' + item_price;
                params += '&price_promotion=' + item_price;
            }
            if (price_table != ''){
                params += '&price_table=' + price_table;
            }
            if (seller_id.length > 0) {
                params += '&shop_id=' + seller_id;
            }
            if (seller_name.length > 0) {
                params += '&shop_name=' + seller_name;
            }

            if (parseInt(item_data[0]) > 0)
                params += '&quantity=' + item_data[0];
            delete item_data[0];

            if (stock > 0){
                params += '&stock=' + stock;
            }else if(parseInt(item_data[1]) > 0){
                params += '&stock=' + item_data[1];
            }
            delete item_data[1];

            var color_size_name = item_data[3] + ";" + item_data[2];
            if (color_size_name != ''){
                params += '&property=' + encodeURIComponent(color_size_name);
                params += '&property_translated=' + encodeURIComponent(color_size_name);
            }


            // Number post send
            params += '&pos=' + pos;
            params += '&length_post=' + get_item_data().length;
        } catch (e) {
            GlobalPaid.trackError(window.location.href,e);
            console.log(e);
        }
        return addVersion(params);
    }

    // Click event
    function linkClick() {

        var expire = document.getElementsByClassName("mod-detail-expireinfo");

        if(expire != null && (typeof expire === 'object' && expire.length > 0)){
            alert("San pham nay da het hang, khong the dat duoc hang");
            return;
        }

        var data = get_item_data();

        // Find color required and checked
        var tbl_wrap = document.getElementsByClassName('content-wrapper');
        var content = null;
        var color_selected = true;
        if (tbl_wrap.length > 0) {
            content = tbl_wrap[0].getElementsByClassName('content');
            if (content.length > 0) {
                var color_dom = content[0].getElementsByClassName('leading');
                if(!(color_dom != null && (typeof color_dom === 'object' && color_dom.length > 0))){
                    color_selected = false;
                }
            }
        }else{
            var tag_ul_color = document.getElementsByClassName('list-leading');
            if (tag_ul_color.length > 0) {
                var tag_a_color = tag_ul_color[0].getElementsByClassName('selected');
                if (tag_a_color.length > 0) {
                    color_selected = true;
                }
            }
        }


        if (color_selected == false) {
            alert("Ban chua chon mau sac");
            return;
        }

        if (data.length == 0) {
            alert("Chon so luong san pham");
            return;
        }
        for (var o in data) {
            if (isNaN(o)) {
                continue;
            }
            try {
                if (data[o]['amount'] == 0) {
                    alert("Chon so luong san pham")
                    return;
                }

                xmlhttpPost(cart_url, data[o], parseInt(o) + 1);
            } catch (e) {
                console.log('Error has found: ' + e);
            }
        }
        return true;
    }
    // Load data
    this.htmlOnLoad = function () {
        /*
         // Get data of properties need translate
         var tbl_wrap = document.getElementsByClassName('content-wrapper');
         if(tbl_wrap.length > 0) {
         var leading = tbl_wrap[0].getElementsByClassName('leading');
         if(leading.length > 0) {
         // Add event click to select color
         select_color(leading[0]);
         }

         var obj = tbl_wrap[0].getElementsByClassName('content')[0];
         translate(obj.innerHTML, obj);
         }
         // End translate properties

         var tbl_price = document.getElementById('mod-detail-price');
         if(tbl_price == null) {
         tbl_price = document.getElementsByClassName('mod-detail-price-sku')[0];
         }

         if(tbl_price != null) {
         translate(tbl_price.innerHTML, tbl_price);
         }
         */
        var sd_add_cart = document.getElementById("sd_add_cart");
        if(sd_add_cart == null){
            setTimeout(update_html(),2000);
        }else{
            setTimeout(hideLoading(),2000);
        }
        return true;
    };

    // Load able
    var loaded = false;
    var loaded_time = 0; // Max 20
    var loaded_max = 5;

    function html_load() {
        loaded_time++;
        if (loaded_time > loaded_max || loaded) {
            update_html();
        } else {
            setTimeout(html_load, 1000);
        }
        return true;
    }

    function update_html() {
        // No translate by google chrome
        var tbl_price = document.getElementById('mod-detail-price');
        if (tbl_price != null) {
            tbl_price.className = tbl_price.className + ' notranslate';
        } else {
            // One price
            var price_primary = document.getElementsByClassName('mod-detail-price-sku')[0];
            price_primary.className = price_primary.className + ' notranslate';
        }
        var tbl_pros = document.getElementsByClassName('table-con');
        if (tbl_pros.length != 0) {
            tbl_pros[0].className = tbl_pros[0].className + ' notranslate';
        }
        // ----------

        var src = 'http://orderhang.com/medias/shop-cart-icon.png';
        var price = getPrice(1);
        console.log(price);
        var price_result = getVNDPrice(price);
        var homeUrl = GlobalPaid.baseUrl();

        var s = '<div class="g-group-member" style="overflow: auto; margin: 2px 0px; padding: 10px;background-color: white">'
            + '	<div style="font-weight: bold; color: blue; font-size: 24px; margin-bottom: 10px; margin-left: 0px;" id="_sd_price"><span style="font-weight:normal">Gia tam tinh:</span> ' + (price_result) + ' VND</div>'
            + '	<div>'
            + '		<span style="float: left; margin-right: 5px; font-size: 13px;">Chu thich:</span>' +
            '<textarea cols="64" style="width: 95%;padding:5px " placeholder="Chu thich cho san pham nay" id="_comment_sd" name="_comment_sd"></textarea>'
            + '	</div>'
            + '	<div class="clr" style="width:100%">'
            + '			<span style="float: left; margin-top: 17px;" id="block_button_sd">' +
            ''
            + '				<a id="sd_add_cart" href="javascript:;">'
            + '				</a> <a href="'+homeUrl+'/gio-hang" class="cart" target="_blank" ' +
            'style="display: inline-block; margin-left: 20px; color: rgb(0, 114, 188);">Vao gio hang</a>' +
            '<p style="color: red; font-size: 20px">De nghi khong su dung Google Translate. Dieu nay lam chung toi mua nham hang cho ban</p>'
            + '</span>'
//            + '			<span style="float:right;margin-right:40px;margin-top:10px">[<a id="_sd_save_item" href="javascript:;">Lưu l?i đ?t hàng sau</a>]</span>'
            + '	</div>'
            + '</div>';

        var div = document.createElement('div');
        div.innerHTML = s;
        div.style.clear = 'both';
        if(document.getElementsByClassName('d-property').length > 0) {
            document.getElementsByClassName('d-property')[0].insertBefore(div, document.getElementsByClassName('d-property')[0].lastChild);
        }
        if(document.getElementsByClassName('obj-order').length > 0) {
            document.getElementsByClassName('obj-order')[0].style.paddingLeft = '0';
            document.getElementsByClassName('obj-order')[0].insertBefore(div, document.getElementsByClassName('obj-order')[0].lastChild);
        }
        if(document.getElementsByClassName('table-wrap').length > 0) {
            document.getElementsByClassName('table-wrap')[0].style.paddingLeft = '0';
            document.getElementsByClassName('table-wrap')[0].insertBefore(div, document.getElementsByClassName('table-wrap')[0].lastChild);
        }
        // Single price
        if(document.getElementsByClassName('region-detail-property').length > 0) {
            document.getElementsByClassName('region-detail-property')[0].style.paddingLeft = '0';
            document.getElementsByClassName('region-detail-property')[0].insertBefore(div,
                document.getElementsByClassName('region-detail-property')[0].lastChild);
        }

        if(document.getElementById("sd_add_cart") != null) {
            document.getElementById("sd_add_cart").addEventListener('click', linkClick);
//            document.getElementById("_sd_save_item").addEventListener('click', linkSave);
        }
        beforeHtml = document.getElementById("block_button_sd").innerHTML;

        setTimeout(hideLoading(),2000);
        return true;
    }


    function select_color(obj) {
        var a_tag = obj.getElementsByTagName('a');
        if (a_tag.length > 0) {
            for (var o in a_tag) {
                if (isNaN(o)) {
                    continue;
                }
                a_tag[o].setAttribute('onclick', "execute_selected_object(this)");
                a_tag[o].setAttribute('href', 'javascript:;');
            }
        }

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.text = "function execute_selected_object(obj) {"
            + " var tbl_wrap = document.getElementsByClassName('content-wrapper');"
            + " var parent_obj = tbl_wrap[0].getElementsByClassName('leading')[0].getElementsByTagName('a');"
            + " if(obj.className.indexOf('selected') != -1) {"
            + "    return;"
            + " }"
            + " for(var o in parent_obj) {"
            + "     if(isNaN(o)) {"
            + "         continue;"
            + "     }"
            + "     if(parent_obj[o].className.indexOf('selected') != -1) {"
            + "         parent_obj[o].className = parent_obj[o].className.replace('selected', '');"
            + "         break;"
            + "     }"
            + " }"
            + " obj.className = obj.className + ' selected';"
            + "}";
        // Add script element
        document.getElementsByClassName('d-wrap')[0].insertBefore(script, document.getElementsByClassName('d-wrap')[0].lastChild);
        return true;
    }

    return true;
}