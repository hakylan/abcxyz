var alibaba = function () {
    this.source = 'alibaba';
    this.common = new common();
    this.init = function () {
        $('#J_DetailInside').css('border', 'solid 1px blue;');

        this.parse();
    };
    this.parse = function () {
        //parse label description
        var common = this.common;
        $('.content-wrapper table thead th').each(function () {
            var text = $.trim($(this).text());
            $(this).text(common.key_translate_lib(text));
        });
        var prop_single = $('.prop-single');
        var text_single = $.trim(prop_single.text());
        prop_single.text(common.key_translate_lib(text_single));

        var content_wrapper = $('div.content-wrapper-spec');

        content_wrapper.css("height", "300px");

        var summary = content_wrapper.find(".summary");
        var content = content_wrapper.find(".content");
        var unit_detail = content_wrapper.find(".unit-detail-order-action");

        summary.css("height", "100% !important");
        content.css("height", "100%");
        unit_detail.css("width", "230px");

        var comment = "";

        //parse price
        var item_price = this.getPrice(1);
        var table_wrap = $('.table-wrap');

        var price_html = '<div style="font-size: 24px;color: #c00;height: 100px">' +
            '<p>Tỉ giá : ' + exchangeRate + 'Đ</p><br/>' +
            '<span style="font-weight:normal">Giá tạm tính: ' + common.format_currency(item_price * exchangeRate) + ' VNĐ</span></div><br/>';
        if (table_wrap != null && (typeof table_wrap === 'object' && table_wrap.length > 0)) {
            table_wrap.append(price_html);
        } else {
            comment += price_html;
        }

        //parse button
        comment += '<textarea placeholder="Chú thích cho sản phẩm này" style="margin: 0; width: 96%; height: 85px;padding:4px;"></textarea>';
        var button_html = '<a id="book-to-seudo" href="javascript:;" ' +
            'style="background: url(\'http://seudo.vn/assets/img/small/icon-bkv1.png\') no-repeat scroll 0 0 rgba(0, 0, 0, 0);' +
            'display: inline-block;height: 42px;width: 220px;"></a>';
        button_html += '<a target="_blank" style="display: inline-block;margin-left: -67px;' +
            'color: rgb(0, 114, 188);" href="' + common.cart_url + '">Vào giỏ hàng</a>';
        $('.unit-detail-order-action').html(comment + button_html);


        //translate
        var title_content = $('.mod-detail-hd h1');
        title_content.attr('data-origin-title', title_content.text());

        this.common.translate_title(title_content.text(), 'title', this);
        //translate prop
        /*var translate_url = this.common.translate_url;
         $('.J_TSaleProp li span').each(function(index, value) {
         var that = $(this);
         var context = $(this).text();

         $.post(translate_url, {text:context,type:'properties'}, function (data) {
         var result = $.parseJSON(data);
         if(result['data_translate'] && result['data_translate'] !=null) {
         that.text(result['data_translate']);
         }
         });
         });*/
        return false;

    };
    this.set_translate = function (data) {
        var title_content = $('.mod-detail-hd h1');
        title_content.html(data['title']);
        return true;
    };
    this.add_to_cart = function () {

        var data = this.get_item_data();

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
            alert("Bạn chưa chọn màu sắc");
            return;
        }

        if (data.length == 0) {
            alert("Bạn chưa chọn số lượng sản phẩm");
            return;
        }
        for (var o in data) {
            if (!$.isNumeric(o)) {
                continue;
            }
            try {
                if (data[o]['amount'] == 0) {
                    alert("Bạn chưa chọn số lượng của sản phẩm")
                    return;
                }

                var data_send = this.getDataSend(data[o]);

                if (data_send != null && typeof data_send != "undefined" && data_send != "") {
                    this.ajaxAddToCart(data_send);
                } else {
                    this.common.track(window.location.href, "Lỗi không lấy được data với function : alibaba.getDataSend");
                    alert("Xảy ra lỗi khi đặt hàng, Sếu Đỏ sẽ khắc phục vấn đề này sớm nhất");
                }

            } catch (e) {
                alert("Xảy ra lỗi khi đặt hàng, Sếu Đỏ sẽ khắc phục vấn đề này sớm nhất");
                this.common.track(window.location.href, e.message + "add_to_cart line number:"+ e.lineNumber);
                console.log('Error has found: ' + e);
            }
        }
        return true;
    };

    this.ajaxAddToCart = function (data) {
        $.ajax({
            url: this.common.add_to_cart_url,
            data: data,
            type: 'post',
            success: function (result) {
                $('body').append(result.html);
            }
        });
        return true;
    };

    /**
     * Lấy dữ liệu send
     * return Array 2 chiều
     *  result[i]['amount'] = 0;
     result[i]['min_amount'] = 0;
     result[i]['size'] = 0;
     result[i]['color'] = 0;
     result[i]['price'] = 0;
     * data gồm amount, color, size, min_amount
     **/
    this.get_item_data = function () {

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
                console.log("length = " + tbl_wrap.length);
                content = tbl_wrap[0].getElementsByClassName('content');
            }
            /**
             * Chú thích mảng Result:
             * [0] => Quantity
             * [1] => Stock
             * [2] => Site
             * [3] => Màu sắc
             * [4] => price
             */
            if (content != null) { // New 22/5/2013
                content = content[0];
                input_data = content.getElementsByClassName('amount-input'); // Get số lượng đặt
                if (input_data.length > 0) {

                    i = 0;
                    /**
                     * Có class 'leading': màu sắc nằm trong class leading
                     * danh sách phía dưới là kích thước
                     * Nếu không có class 'leading', không có kích thước, chỉ có màu sắc
                     */
                    color = tbl_wrap[0].getElementsByClassName('leading');
                    if (color.length > 0) { // Has color, and size
                        color = color[0].getElementsByClassName('selected')[0].getAttribute('title').replace(/\n+/, '').replace(/\s+/, '');
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = new Array();
                            // Add data to arrayn
                            result[i][0] = input_data[inc].value;
                            result[i][1] = parent_obj.getElementsByClassName('count')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, "");
                            result[i][2] = color == "" ? "" : parent_obj.getElementsByClassName('name')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, '').replace(/\n+/, '');
                            result[i][3] = color == "" ? parent_obj.getElementsByClassName('name')[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, '').replace(/\n+/, '') : color;
                            result[i][4] = parent_obj.getElementsByClassName('price')[0].getElementsByTagName('em')[0].textContent.replace(/\s+/, "");
                            i++;
                        }
                    } else { // Có màu sắc, ko có size

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
            } else {
                var obj_sku = document.getElementsByClassName('obj-sku');
                var obj_amount = document.getElementsByClassName('obj-amount');
                if (obj_sku != null && (typeof obj_sku === 'object' && obj_sku.length > 0)) {
                    input_data = obj_sku[0].getElementsByClassName("amount-input");
                } else if (obj_amount != null && (typeof obj_amount === 'object' && obj_amount.length > 0)) {
                    input_data = obj_amount[0].getElementsByClassName("amount-input");
                }

                if (input_data.length > 0) {

                    i = 0;
                    /**
                     * Có class 'leading': màu sắc nằm trong class leading
                     * danh sách phía dưới là kích thước
                     * Nếu không có class 'leading', không có kích thước, chỉ có màu sắc
                     */
                    color = document.getElementsByClassName('obj-leading');
                    if (color.length > 0) { // Has color, and size
                        color = color[0].querySelectorAll('a.selected'); //
                        if (color != null) {
                            color = color[0].getAttribute('title').replace(/\n+/, '').replace(/\s+/, '');
                        }
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = this.getProperties(parent_obj, input_data[inc], color);

                            i++;
                        }
                    } else { // Có màu sắc, ko có size
                        for (var inc in input_data) {
                            if (isNaN(input_data[inc].value) || input_data[inc].value == 0) {
                                continue;
                            }
                            parent_obj = input_data[inc].parentNode.parentNode.parentNode.parentNode; // Find tr node
                            result[i] = this.getProperties(parent_obj, input_data[inc], "");
                            i++;
                        }
                    }
                }
            }
            return result;
        } catch (e) {
            throw Error(e + "Error function get_item_data()");
        }
    };

    this.getProperties = function (tr_prop, input_data, color) {
        try{
            var content = null;
            var count_span = null;
            var size_span = null;
            var price_span = null;
            var result = new Array();
            result[0] = input_data.value;
            count_span = tr_prop.getElementsByClassName('count');
            if (count_span != null && (typeof count_span === 'object' && count_span.length > 0)) {
                result[1] = count_span[0].getElementsByTagName('span')[0].textContent.replace(/\s+/, "");
            } else {
                result[1] = 9999;
            }
            size_span = tr_prop.getElementsByClassName('name');
            if (size_span != null && (typeof size_span === 'object' && size_span.length > 0 && color != "")) {
                var span = size_span[0].getElementsByTagName('span')[0];
                if (GlobalTool.hasClass(span, "image")) {
                    result[2] = span.getAttribute("title").
                        replace(/\s+/, '').replace(/\n+/, '');
                } else {
                    result[2] = span.textContent.replace(/\s+/, '').replace(/\n+/, '');
                }
            } else {
                result[2] = "";
            }

            if (size_span != null && (typeof size_span === 'object' && size_span.length > 0) && color == "") {
                var span = size_span[0].getElementsByTagName('span')[0];
                if (GlobalTool.hasClass(span, "image")) {
                    result[3] = span.getAttribute("title").
                        replace(/\s+/, '').replace(/\n+/, '');
                } else {
                    result[3] = span.textContent.replace(/\s+/, '').replace(/\n+/, '');
                }
            } else {
                result[3] = color;
            }

            price_span = tr_prop.getElementsByClassName('price');

            if (price_span != null && (typeof price_span === 'object' && price_span.length > 0)) {
                result[4] = price_span[0].getElementsByTagName('em')[0].textContent.replace(/\s+/, "");
            } else {
                result[4] = 0;
            }

            return result;
        }catch(ex){
            throw Error(ex + "Error function getProperties()");
        }

    };

    // Comment
    this.getComment = function () {
        var comment = document.getElementById("_comment_sd");
        if (comment != null) {
            comment = comment.value;
        } else {
            comment = "";
        }
        return comment;
    };


    // Get link
    /**
     * item_data: Array
     * keys: amount, color, size
     */
    this.getDataSend = function (item_data) {
        try {
            //lấy item_id
            var item_id = this.getItemId();
            //lấy item_title
            var item_title = this.getItemTitle();
            //lấy item_image
            var item_image = this.getItemImage();
            //lấy item_link
            var item_link = this.getItemLink();
            //lấy seller_id
            var seller_id = this.getShopId();
            var seller_name = this.getShopName();
            //lay comment
            var price_table = this.getPriceTable();

            var step = this.getStep();

            var require_min = this.getRequireMin();

            var stock = this.getStock();

            if (stock <= 0) {
                stock = item_data[1];
            }

            var wangwang = this.getWangwang();

            var weight = this.getWeight();

            var item_price = this.getPrice(item_data[0]);

            // Multi buy
            var tbl_wrap = document.getElementsByClassName('content-wrapper');
            if (tbl_wrap.length > 0) {
                if (tbl_wrap[0].getElementsByClassName('content').length > 0) {
                    item_price = item_data[4];
                }
            }

            var comment = $('.unit-detail-order-action textarea').val();

            var color_size_name = item_data[3] + ";" + item_data[2];

            return {
                title_origin: $.trim(item_title),
                title_translated: $.trim(item_title),
                price_origin: item_price,
                price_promotion: item_price,
                price_table: price_table,
                property_translated: color_size_name,
                property: color_size_name,
                data_value: "",
                image_model: item_image,
                image_origin: item_image,
                shop_id: seller_id,
                shop_name: seller_name,
                wangwang: wangwang,
                quantity: item_data[0],
                require_min: require_min,
                stock: stock,
                site: "1688",
                comment: comment,
                item_id: item_id,
                link_origin: item_link,
                outer_id: '',
                weight: weight,
                step: step,
                tool: "Addon"
            };
        } catch (e) {
            throw Error(e + "Error function getDataSend()");
        }
    };

    // Hàm l?y b?ng giá
    this.getPriceTable = function () {
        //-- get price amount
        var price_table = [];

        var price_range = null;

        var pri = [];

        var detail_price = null;

        var tr_price = null;

        var i = 0;

        try {
            detail_price = document.getElementById("mod-detail-price");
            if (detail_price != null) { //price by amount

                var price_container = detail_price.getElementsByClassName("unit-detail-price-amount");

                if (price_container != null && price_container.length > 0) {
                    tr_price = price_container[0].getElementsByTagName("tr");

                    if (tr_price.length > 0) {
                        for (i = 0; i < tr_price.length; i++) {
                            pri = tr_price[i];
                            price_range = JSON.parse(pri.getAttribute("data-range"));
                            price_table.push(price_range);
                        }
                    }
                } else {
                    tr_price = detail_price.querySelectorAll("tr.price td");
                    if (tr_price != null && tr_price.length > 0) {
                        for (var j = 0; j < tr_price.length; j++) {
                            try {
                                pri = tr_price[j];
                                var range = pri.getAttribute("data-range");
                                if (range !== "") {
                                    price_range = JSON.parse(range);
                                    price_table.push(price_range);
                                }
                            } catch (e) {

                            }

                        }
                    }
                }
            } else {
                var price = {};
                var price_common = document.getElementsByClassName("offerdetail_common_beginAmount");

                // One price
                if (price_common.length > 0) {
                    price.begin = price_common[0].getElementsByTagName('p')[0].textContent;

                    price.begin = price.begin.match(/[0-9]+/)[0];
                    // get prices
                    detail_price = document.getElementsByClassName("unit-detail-price-display")[0].textContent.split('-');
                    var price_display = {};
                    for (i = 0; i < detail_price.length; i++) {
                        price_display[i] = detail_price[i].match(/[0-9]*[\.]?[0-9]+/g).join('');
                    }
                    price.price = price_display;
                    price.end = "";
                }
                price_table.push(price);
            }
        } catch (ex) {
            console.log("Fuck " + ex);
            throw Error(e + "Error function getPriceTable()");
        }
        return JSON.stringify(price_table);
    };

    this.getRequireMin = function () {
        var require_min = 1;
        try {
            require_min = iDetailConfig.beginAmount;
        } catch (e) {
            require_min = 1;
        }
        return require_min;
    };

    /**
     * get Step item
     * @returns {number}
     */
    this.getStep = function () {
        try{
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
            if (step == '' || step == null) {
                step = 1;
            }

            return step;
        }catch(ex){
            throw Error(ex + "Error function getStep()");
        }

    };

    // Get min amount
    this.getMinAmount = function () {

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
    };

    // Get price by item amout
    this.getPrice = function (quantity) {

        try {
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
                if (this.common.processPrice(price) > 0) {
                    return this.common.processPrice(price)
                }
            }

            // Nhi?u m?c giá
            var div_prices = document.getElementById("mod-detail-price");
            if (div_prices == null || div_prices.length <= 0) {
                if (this.common.processPrice(price) > 0) {
                    return this.common.processPrice(price)
                }
            } else {
                var span_prices = div_prices.getElementsByTagName("span");

                if (span_prices != null) {

                    if (this.common.processPrice(price) <= 0) {
                        price_table = this.getPriceTable();

                        price = this.getPriceByPriceTable(price_table, quantity);
                    }
                } else {
                    price_table = this.getPriceTable();
                    price = this.getPriceByPriceTable(price_table, quantity);
                }
            }
            price = this.common.processPrice(price);


            if (price <= 0) {
                try {
                    div_price = document.getElementsByClassName("obj-price");
                    if (div_price != null) {
                        span_price = div_price[0].getElementsByClassName("price");
                        price = span_price[0].textContent;
                    }
                } catch (ex) {
                    price = 0;
                }

            }
            price = this.common.processPrice(price);

            if (price <= 0) {
                try {
                    div_price = document.getElementsByClassName("price-discount-sku");
                    if (div_price != null) {
                        span_price = div_price[0].getElementsByClassName("value");
                        price = span_price[0].textContent;
                    }
                } catch (ex) {
                    price = 0;
                }
            }

            price = this.common.processPrice(price);

            if (price <= 0) {
                try {
                    var tr_price = document.querySelectorAll("tr.price");
                    if (tr_price != null) {
                        span_price = tr_price[0].querySelectorAll("span.value");
                        price = span_price[0].textContent;
                    }
                } catch (ex) {
                    price = 0;
                }
            }
            return this.common.processPrice(price);
        }
        catch (e) {
            throw Error(e + "Error function getPrice()");
        }
    };


    this.getPriceByPriceTable = function (price_table, quantity) {
        var price = 0;
        try {
            price_table = JSON.parse(price_table);
            if (typeof price_table === 'object') {
                for (var o in price_table) {
                    if (price_table[o] != null) {
                        var begin = price_table[o].begin;
                        var end = price_table[o].end;

                        if ((begin <= quantity && quantity <= end) ||
                            (begin <= quantity && (parseInt(end) == 0 || end == null || end == "")) || quantity <= begin) {
                            price = price_table[o].price;
                            break;
                        } else {
                            price = price_table[o].price;
                        }
                    }
                }
            }
        } catch (e) {
            price = 0;
        }

        return price;
    };

    // Seller id
    this.getShopName = function () {
        var shop_name = '';
        try {
            var element = document.getElementsByName("sellerId");
            if (element.length > 0) {
                element = element[0];
                shop_name = element.value;
            } else {
                // New 24/4/2013
                element = document.getElementsByClassName('contact-div');
                if (element.length > 0) {
                    shop_name = element[0].getElementsByTagName('a')[0].innerHTML;
                }
            }
        } catch (e) {
            console.log('Khong lay duoc thong tin nguoi ban!');
        }

        return shop_name;
    };

    this.getShopId = function () {
        var shop_id = "";
        try{
            var WolfSmoke = WolfSmoke;
            console.log(WolfSmoke);
            shop_id = WolfSmoke.acookieAdditional.member_id;
            console.log(shop_id);
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
            }else{
                var div_logo = document.querySelectorAll('div.logo');
                if(div_logo != null && div_logo.length > 0){
                    a_shop = div_logo[0].getElementsByTagName("a");
                    if(a_shop != null && a_shop.length > 0){
                        href = a_shop[0].getAttribute("href");
                        shop_id = href.split('.')[0];
                        shop_id = shop_id.split('http://')[1];
                    }
                }
            }
        }

        return shop_id;
    };

    /**
     * Get Item Id
     * @returns {number}
     */
    this.getItemId = function () {

        var offerid = 0;
        try {
            offerid = iDetailConfig.offerid;
        } catch (e) {
            var link = window.location.href;
            var item_id = link.split('.html')[0];
            offerid = item_id.split('offer/')[1];
        }
        return offerid;

    };

    this.getStock = function () {
        var stock = 0;
        try {
            stock = iDetailData.sku.canBookCount;
        } catch (e) {
            stock = 0;
        }
        return stock;
    };

    // Item title
    this.getItemTitle = function () {
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

        return item_title;
    };

    // Item image
    this.getItemImage = function () {
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
        item_image = GlobalTool.resizeImage(item_image);
        return item_image;
    };

    // Item link
    this.getItemLink = function () {
        return window.location.href;
    };

    // VN Price
    this.getVNDPrice = function (price_taobao) {
        var rate = GlobalTool.exchangeRate();
        var price_result = price_taobao * rate;
        price_result = this.common.format_currency(price_result);

        return price_result;
    };

    this.getWangwang = function () {
        var wangwang = "";
        try {
            wangwang = eService.contactList[0].name;
        } catch (e) {
            wangwang = "";
        }

        return wangwang;
    };

    this.getWeight = function () {
        var weight = 0;
        try {
            var unit_detail = document.getElementsByClassName("unit-detail-freight-cost");
            if (unit_detail.length > 0) {
                var carriage = JSON.parse(unit_detail[0].getAttribute("data-unit-config"));
                weight = !isNaN(carriage.unitWeight) ? carriage.unitWeight : 0;
            }
        } catch (e) {
            weight = 0;
        }
        return parseFloat(weight);
    };
    return true;
};