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

// ==/UserScript==

/* Utilities */
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
function addVersion(url) {
    return url + '&version=20130526&translate=1'; // nam-thang-ngay
}
function roundNumber(num, dec) {
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    return result;
}
function processPrice(price) {
    if (price == null) return 0;
    p = String(price).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
    return parseFloat(p);
}

/**
 * Addon function -----------------------------------------------------------
 */
function taobao(cart_url, url_save) {
    function addListener(element, type, expression, bubbling) {
        bubbling = bubbling || false;
        element.addEventListener(type, expression, bubbling);
    }
    /**
     * return parameters will be send to server
     */
    function getContent() {
        // Get main data by id of element
        var data = document.getElementById('detail').innerHTML;
        var link = window.location.href;
        var shop_info = document.getElementsByClassName('shop-summary');
        if(shop_info.length > 0) {
            shop_info = shop_info[0].innerHTML;
        }
        if(window.location.href.indexOf('tmall') > -1) {
            shop_info = document.getElementById('header');
            if(shop_info != undefined) {
                shop_info = shop_info.innerHTML;
            }
        }
        /**
         * @send keys
         * data: items detail container
         * link: current link
         * shop_info: shop container
         */
        return 'data=' + encodeURIComponent(data) + '&link=' + encodeURIComponent(link) + '&shop=' + encodeURIComponent(shop_info);
    }
    function xmlhttpPost(strURL) {
        var self = this;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            self.xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        var beforeHtml = document.getElementById("seudo_block_order").innerHTML;
        document.getElementById("seudo_block_order").innerHTML =
            '<img style="margin-top:12px;margin-right:50px" src="' + img_loading + '" alt="" />';
        self.xmlHttpReq.open('POST', strURL, true);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        self.xmlHttpReq.withCredentials = "true";

        self.xmlHttpReq.onreadystatechange = function () {
            if (self.xmlHttpReq.readyState == 4) {
                // Update notify to customer
                updatPpage(self.xmlHttpReq.responseText);

                document.getElementById("seudo_block_order").innerHTML = beforeHtml;
                addListener(document.getElementById("seudo_order"), 'click', linkClick);
                addListener(document.getElementById("seudo_save_item"), 'click', linkSave);
            } else {
                //console.log(self.xmlHttpReq.responseText);
            }
        }
        self.xmlHttpReq.send(getContent());
    }
    // Append response text to browser 
    function updatPpage(str) {
        var htmlObject = document.createElement('div');
        htmlObject.innerHTML = str;
        document.body.appendChild(htmlObject);
    }
    function linkClick() {
        xmlhttpPost(cart_url);
    }
    function linkSave() {
        xmlhttpPost(url_save);
    }
    this.htmlOnLoad = function () {
        update_html();
    }
    function update_html() {
        try {
            //html toan bo addon
            var s = '<span id="seudo_block_order"><a href="javascript:void(0);" id="seudo_order">Đặt hàng</a></span> | <a href="javascript:void(0);" id="seudo_save_item">Lưu lại</a>';

            var div = document.createElement('div');
            div.innerHTML = s;
            div.style.border = '1px solid #AAA';
            div.style.position = 'fixed';
            div.style.top = '0';
            div.style.right = '0';
            div.style.zIndex = '2147483647';
            div.style.padding = '5px';
            div.style.background = '#F1F1F1';
            document.body.appendChild(div);

            // Add cac su kien
            addListener(document.getElementById("seudo_order"), 'click', linkClick);
            addListener(document.getElementById("seudo_save_item"), 'click', linkSave);
        } catch (e) {
            console.log(e);
        }
    }
}
function alibaba(cart_url, url_save) {
    var beforeHtml = '';
    // Rate
    function rateMoney() {
        return 3300;
    }

    // Comment
    function getComment() {
        var comment = document.getElementById("txtBz_orderhang");
        if (comment != null) {
            comment = comment.value;
        } else {
            comment = "";
        }
        return encodeURIComponent(comment);
    }

    // Hàm lấy bảng giá
    function getPriceTable() {
        var div_prices = document.getElementById("mod-detail-price");
        if (div_prices == null) return '';

        var span_prices = div_prices.getElementsByTagName("span");
        if (span_prices == null) return '';

        var price_table = str = '';
        var inc = 0;
        var textPrice = '';

        for (var i = 0; i < span_prices.length; i++) {
            str = span_prices[i].textContent;
            // nếu span chứa thông tin số lượng sản phẩm mua:
            if ((str.indexOf('-') != -1) || (str.indexOf('≥') != -1)) {
                textPrice = span_prices[i + 1].textContent + '' + span_prices[i + 2].textContent;
                if (price_table == '')
                    price_table += str + ':' + processPrice(textPrice);
                else price_table += ';' + str + ':' + processPrice(textPrice);
            }
        }

        return encodeURIComponent(price_table);
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
        if (str.indexOf('≥') != -1) {
            return str.split('≥')[1];
        }
        return min_amount;
    }

    // Get price by item amout
    function getPrice(quantity) {

        quantity = parseInt(quantity);

        var price = 0;
        var span_price = document.getElementsByClassName('mod-detail-price-sku');
        if (span_price != null) {
            span_price = span_price[0];
        }
        // Một mức giá
        if (span_price != null) {
            //price=span_price.textContent;
            var e_num = document.getElementsByClassName('mod-detail-price-sku')[0].getElementsByTagName('span')[2].textContent;
            var p_num = document.getElementsByClassName('mod-detail-price-sku')[0].getElementsByTagName('span')[3].textContent;
            price = e_num + p_num;
            return processPrice(price);
        }

        // Nhiều mức giá
        var div_prices = document.getElementById("mod-detail-price");

        if (div_prices == null) {
            return processPrice(price);
        }

        var span_prices = div_prices.getElementsByTagName("span");
        if (span_prices == null) {
            return processPrice(price);
        }

        // Duyệt qua các mức giá
        var quan_compare = '';
        for (var i = 0; i < span_prices.length; i++) {
            var str = span_prices[i].textContent;
            if ((str.indexOf('-') != -1) || (str.indexOf('≥') != -1)) {
                if (str.indexOf('-') != -1) {
                    quan_compare = str.split('-');
                    price = span_prices[i + 1].textContent + '' + span_prices[i + 2].textContent;
                    if (quantity >= quan_compare[0] & quantity <= quan_compare[1]) {
                        break;
                    }
                }
                if (str.indexOf('≥') != -1) {
                    price = span_prices[i + 1].textContent + '' + span_prices[i + 2].textContent;
                }
            }
        }
        return processPrice(price);
    }

    // Seller id
    function getSellerId() {
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
            console.log('Không l\u1ea5y được thông tin người bán!');
        }

        return encodeURIComponent(seller_id);
    }

    // Item id
    function getItemId() {
        /*
         var elements =document.getElementsByName("offerId");

         var item_id=0;		
         if(elements.length > 0) {
         for(var i=0;i<elements.length;i++) {
         element=elements[i];
         if(element.value!="")
         item_id=element.value;
         }
         } else {
         // New 24/4/2013
         item_id = document.getElementById('feedbackInfoId');
         if(item_id != null) {
         item_id = item_id.value;
         } else {
         // check hidden field
         item_id = document.getElementsByClassName('d-tab-btn-add'); // New 27/04/2013
         if(item_id.length > 0) {
         var json_data = item_id[0].getElementsByTagName('a')[0].getAttribute('data-purchase');
         json_data = JSON.parse(json_data);
         item_id = json_data.offerId;
         } else {
         item_id = 0;
         }
         }
         }

         if(item_id == 0) {
         console.log('Không tìm th\u1ea5y ID sản phẩm!');
         }
         */
        var item_id = window.location.href.match(/[0-9]+/g);
        if (item_id.length > 1) {
            item_id = item_id[1];
        }

        return item_id;
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
        if (main_image != null) {
            var img_obj = main_image[0].getElementsByTagName("img");
            if (img_obj.length > 1) {
                item_image = img_obj[1].getAttribute('src');
            } else {
                // Large image
                item_image = img_obj[0].getAttribute('src');
            }
        }
        return encodeURIComponent(item_image);
    }

    // Item link
    function getItemLink() {
        return encodeURIComponent(window.location.href);
    }

    // VN Price
    function getVNDPrice(price_taobao) {
        var rate = rateMoney();
        var price_result = roundNumber(price_taobao * rate, 2);
        price_result = String(price_result).replace(/^\d+(?=.|$)/, function (_int) {
            return _int.replace(/(?=(?:\d{3})+$)(?!^)/g, ".");
        });

        return price_result;
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

        document.getElementById("block_button_orderhang").innerHTML
            = '<img style="margin-top:12px;margin-right:50px" src="http://orderhang.com/frontend/images/ajax-loader.gif" alt="" />';
        self.xmlHttpReq.open('POST', strURL, true);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        self.xmlHttpReq.withCredentials = "true";

        self.xmlHttpReq.onreadystatechange = function () {
            if (self.xmlHttpReq.readyState == 4) {
                if (strURL.indexOf('item_collect') != -1) {
                    //luu san pham
                    if (self.xmlHttpReq.responseText == 'OK')
                        console.log('Lưu sản phẩm thành công!');
                    else console.log(self.xmlHttpReq.responseText);
                } else {
                    updatepage(self.xmlHttpReq.responseText);
                }
                document.getElementById("block_button_orderhang").innerHTML = beforeHtml;
                document.getElementById("id_orderhang_add_cart").addEventListener('click', linkClick);
                document.getElementById("id_orderhang_save_item").addEventListener('click', linkSave);
            }
        }
        // Send data
        self.xmlHttpReq.send(getLink(item_data, pos));
    }

    function updatepage(str) {
        var htmlObject = document.createElement('div');
        htmlObject.innerHTML = str;
        document.body.appendChild(htmlObject);
    }

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
    function get_item_data() {

        var result = new Array();
        var input_data = new Array();
        var i = 0;
        var parent_obj = null;

        try {
            // Multi buy
            var tbl_wrap = document.getElementsByClassName('content-wrapper');
            var content = null;
            if (tbl_wrap.length > 0) {
                content = tbl_wrap[0].getElementsByClassName('content');
            }
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
                    var color = tbl_wrap[0].getElementsByClassName('leading');
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
                // Buy one by one
                result[0] = new Array();
                result[0][0] = document.getElementById('J_AmountInput').value;
                result[0][1] = 9999;
                result[0][2] = '';
                result[0][3] = '';
                result[0][4] = '';
            }
        } catch (e) {
            console.log("Error mesage: " + e);
        }
        return result;
    }

    // Get link
    /**
     * item_data: Array
     * keys: amount, color, size
     */
    function getLink(item_data, pos) {

        var params = 'type=alibaba';
        try {
            // Số thự tự lần gửi request
            if (pos == null) {
                pos = 1;
            }
            //lấy item_id
            var item_id = getItemId();
            //lấy item_title
            var item_title = getItemTitle();
            //lấy item_image
            var item_image = getItemImage();
            //lấy item_link
            var item_link = getItemLink();
            //lấy seller_id	
            var seller_id = getSellerId();
            var seller_name = seller_id;
            //lay comment
            var comment = getComment();
            //lay ban gia
            var price_table = getPriceTable();

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
            if (item_title != '') params += '&item_title=' + item_title;
            if (item_image != '') params += '&item_image=' + item_image;
            if (comment != '') params += '&comment=' + comment;
            if (item_link != '') params += '&item_link=' + item_link;
            if (item_price > 0) params += '&item_price=' + item_price;
            if (price_table != '') params += '&price_table=' + price_table;
            if (seller_id.length > 0) {
                params += '&seller_id=' + seller_id;
            }
            if (seller_name.length > 0) {
                params += '&seller_name=' + seller_name;
            }

            if (parseInt(item_data[0]) > 0)
                params += '&quantity=' + item_data[0];
            delete item_data[0];

            if (parseInt(item_data[1]) > 0)
                params += '&min=' + item_data[1];
            delete item_data[1];

            // Lay color_size_name
            if (item_data[2] != "") {
                params += '&size=' + item_data[2];
            }
            if (item_data[3] != "") {
                params += '&color=' + encodeURIComponent(item_data[3]);
            }
            var color_size_name = item_data[3] + ";" + item_data[2];
            if (color_size_name != '')
                params += '&color_size_name=' + encodeURIComponent(color_size_name);

            // Number post send
            params += '&pos=' + pos;
            params += '&length_pos=' + get_item_data().length;
        } catch (e) {
            console.log(e);
        }
        return addVersion(params);
    }

    // Click event
    function linkClick() {

        var data = get_item_data();
        var min_amount = getMinAmount();

        // Find color required and checked
        var tbl_wrap = document.getElementsByClassName('content-wrapper');
        var content = null;
        var color_selected = null;
        if (tbl_wrap.lenght > 0) {
            content = tbl_wrap[0].getElementsByClassName('content');
            if (content.length > 0) {
                color_selected = content[0].getElementsByClassName('leading');
                if (color_selected.length > 0) {
                    if (color_selected[0].getElementsByClassName('selected').length > 0) {
                        color_selected = color_selected[0].getElementsByClassName('selected')[0].textContent;
                    } else {
                        color_selected = false;
                    }
                }
            }
        }

        if (color_selected == false) {
            console.log('Bạn chưa chọn màu sắc!');
            return;
        }

        if (data.length == 0) {
            console.log('Bạn chưa chọn sản phẩm nào!');
            return;
        }
        for (var o in data) {
            if (isNaN(o)) {
                continue;
            }
            try {
                if (data[o]['amount'] == 0) {
                    console.log('Bạn chưa chọn số lượng');
                    return;
                }
                // Check min amount bought
                if (parseInt(data[o]['amount']) < parseInt(min_amount)) {
                    console.log('Số lượng tối thiểu không đủ!');
                    continue;
                }
                xmlhttpPost(cart_url, data[o], parseInt(o) + 1);
            } catch (e) {
                console.log('Error has found: ' + e);
            }
        }
    }

    // Save data
    function linkSave() {
        var full = true;
        //validate số lượng
        var stocks = document.getElementsByClassName('d-sku');
        var stocks_amount = stocks.length;
        //so luong nho nhat dc phep mua
        var min = getAllowedMinQuantity();
        //so luong kh nhap
        var quantity = getQuantity();

        if (stocks_amount > 0) {
            var buyArea = document.getElementsByClassName("d-sku-box");
            buyArea = buyArea[0];

            var selects = buyArea.getElementsByClassName('d-selected');
            if (selects.length != stocks_amount) {
                full = false;
            }
        }

        if (full == false) {
            console.log("Bạn chưa chọn đầy đủ thuộc tính sản phẩm!");
            document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0);');
            return;
        }
        var min_quantity = document.getElementById("min_quantity").value;
        if (parseInt(min_quantity) > 0 && parseInt(min_quantity) > parseInt(quantity)) {
            console.log("Số lượng sản phẩm không được bé hơn " + min_quantity + "!");
            document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0);');
            return;
        }

        if (parseInt(quantity) % parseInt(min_quantity) != 0) {
            console.log("Số lượng sản phẩm phải là bội số của " + min_quantity + "!");
            document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0);');
            return;
        }

        document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0)');

        document.getElementById("id_orderhang_save_item").setAttribute('target', "");

        xmlhttpPost(url_save);
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

        update_html();
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
        var price_result = getVNDPrice(price);

        var s = '<div class="g-group-member" style="overflow:auto;border: 2px solid green;margin:2px 0;padding:10px">'
            + '	<div style="font-weight:bold;color: blue; font-size: 24px;margin-bottom:10px;margin-left:41px" id="orderhang_price"><span style="font-weight:normal">Giá tạm tính</span>: ' + (price_result) + ' <i style="font-weight:normal;">VNĐ</i></div>'
            + '	<div>'
            + '		<span style="width:10%;float:left;margin-right:5px;font-size:13px;">Mô tả：</span><textarea cols="64" style="width: 85%;" id="txtBz_orderhang" name="txtBz_orderhang"></textarea>'
            + '	</div>'
            + '	<div class="clr" style="width:100%;float:right">'
            + '		<div>'
            + '			<span style="float:left;margin-left:41px" id="block_button_orderhang">'
            + '				<a id="id_orderhang_add_cart" href="javascript:;">'
            + '					<img src="' + src + '" alt="" />'
            + '				</a>'
            + '			</span>'
            + '			<span style="float:right;margin-right:40px;margin-top:10px">[<a id="id_orderhang_save_item" href="javascript:;">Lưu lại đặt hàng sau</a>]</span>'
            + '		</div>'
            + '	</div>'
            + '</div>';

        var div = document.createElement('div');
        div.innerHTML = s;
        div.style.clear = 'both';
        document.getElementsByClassName('d-property')[0].insertBefore(div, document.getElementsByClassName('d-property')[0].lastChild);
        document.getElementById("id_orderhang_add_cart").addEventListener('click', linkClick);
        document.getElementById("id_orderhang_save_item").addEventListener('click', linkSave);

        beforeHtml = document.getElementById("block_button_orderhang").innerHTML;
    }

    function translate(html_chinese, obj) {
        var self = this;

        self.xmlHttpReq = new XMLHttpRequest();
        self.xmlHttpReq.open('POST', url_translate + '?t=' + Math.random(), false);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // Send data
        var data = "data=" + encodeURIComponent(html_chinese);
        self.xmlHttpReq.send(data);

        var dom_original = document.createElement('div');
        dom_original.innerHTML = html_chinese;
        var dom_translate = document.createElement('div');
        dom_translate.innerHTML = self.xmlHttpReq.responseText;

        // Merge chinese and vietname string
        var tb_prop_original = dom_original.getElementsByClassName('leading');
        var tb_prop_translate = dom_translate.getElementsByClassName('leading');
        if (tb_prop_translate.length > 0) {
            for (var i in tb_prop_translate) {
                if (isNaN(i)) {
                    continue;
                }
                // Find property content
                var a_original = tb_prop_original[i].getElementsByClassName('value')[0].getElementsByTagName('a');
                var a_translate = tb_prop_translate[i].getElementsByClassName('value')[0].getElementsByTagName('a');
                var div_translate = tb_prop_translate[i].getElementsByClassName('value')[0].getElementsByClassName('unit-detail-spec-operator');
                for (var j in a_translate) {
                    if (isNaN(j)) {
                        continue;
                    }
                    var title = a_translate[j].getAttribute('title') + "["
                        + a_original[j].getAttribute('title') + "]";
                    a_translate[j].setAttribute('title', title);
                    // if color is text
                    if (a_translate[j].getElementsByTagName('span').length > 0) {
                        title = a_translate[j].getElementsByTagName('span')[0].innerHTML + "<t style='color:red;'>["
                            + a_original[j].getElementsByTagName('span')[0].innerHTML + "]</t>";
                        a_translate[j].getElementsByTagName('span')[0].innerHTML = title;
                        div_translate[j].style.width = "auto";
                    } else {
                        // Images
                        a_translate[j].getElementsByTagName('img')[0].src = a_translate[j].getElementsByTagName('img')[0].getAttribute('data-lazy-src');
                    }
                }
                tb_prop_translate[i].getElementsByClassName('value')[0].style.width = "455px";
            }
        }

        obj.innerHTML = decodeURIComponent(dom_translate.innerHTML);
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
    }

}
function paipai(cart_url, url_save) {
    function additionPrice() {
        return 0;
    }

    function rateMoney() {
        return 3550;
    }

    //hàm lấy danh sách màu sắc
    function getColor() {
        return -1;
    }

    //hàm lấy danh sách kích cỡ
    function getSize() {
        return -1;
    }

    //ham lay comment
    function getComment() {
        var comment = document.getElementById("txtBz_orderhang");
        if (comment != null) {
            comment = comment.value;
        } else {
            comment = "";
        }
        return encodeURIComponent(comment);
    }

    //ham lay thong tin color_size_name
    function getColorSizeName() {
        var stocks = document.getElementsByClassName('stock');
        var stocks_amount = stocks.length;

        var buyArea = document.getElementById("buyArea");
        var color_size_name = '';
        if (stocks_amount > 0) {
            var selects = buyArea.getElementsByClassName('select');
            if (selects.length > 0) {
                for (var i = 0; i < selects.length; i++) {
                    var element = selects[i];
                    if (color_size_name == '')
                        color_size_name += element.getAttribute('attrvalue');
                    else color_size_name += ';' + element.getAttribute('attrvalue');
                }
            }
        }
        return encodeURIComponent(color_size_name);
    }

    function getSellerId() {
        var element = document.getElementsByName("sellerUin");
        if (element.length > 0) {
            element = element[0];
            var seller_id = element.value;
        } else var seller_id = '';

        return encodeURIComponent(seller_id);
    }

    //ham lay item_id cua san pham
    function getItemId() {
        var element = document.getElementsByName("itemid");

        if (element.length > 0) {
            element = element[0];
            var item_id = element.value;
        } else var item_id = 0;

        return item_id;
    }

    //ham lay item_title cua san pham
    function getItemTitle() {
        var element = document.getElementsByName("sTitle");

        if (element.length > 0) {
            element = element[0];
            var item_title = element.value;
        } else var item_title = '';

        return encodeURIComponent(item_title);
    }

    //ham lay item_image cua san pham
    function getItemImage() {
        var element = document.getElementById("pfhlkd_smallImage");

        if (element != null) {
            var item_image = element.getAttribute('src');
        } else var item_image = '';

        return encodeURIComponent(item_image);
    }

    //ham lay item_link cua san pham
    function getItemLink() {
        return encodeURIComponent(window.location);
    }

    //hàm lấy số lượng mua
    function getQuantity() {
        return document.getElementById("selectNum").value;
    }

    //hàm lấy giá TQ của sản phẩm
    function getPrice() {

        var element = document.getElementById("commodityCurrentPrice");

        if (element != null) {
            var item_price = element.textContent;

        } else var item_price = '';
        if (isNaN(item_price)) {
            var element = document.getElementsByName("Price");
            if (element.length > 0) {
                element = element[0];
                item_price = element.value;
            }
        }
        return processPrice(item_price);

    }

    // Hàm lấy giá VND dựa vào giá TQ
    function getVNDPrice(price_taobao) {
        var rate = rateMoney();
        var price_result = price_taobao * rate;
        price_result = String(price_result).replace(/^\d+(?=.|$)/, function (_int) {
            return _int.replace(/(?=(?:\d{3})+$)(?!^)/g, ".");
        });

        return price_result;
    }

    function xmlhttpPost(strURL) {
        var xmlHttpReq = false;
        var self = this;
        // Mozilla/Safari
        if (window.XMLHttpRequest) {
            self.xmlHttpReq = new XMLHttpRequest();
        }
        // IE
        else if (window.ActiveXObject) {
            self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        var beforeHtml = document.getElementById("block_button_orderhang").innerHTML;
        document.getElementById("block_button_orderhang").innerHTML = '<img style="margin-top:12px;margin-right:50px" src="http://orderhang.com/frontend/images/ajax-loader.gif" alt="" />';
        self.xmlHttpReq.open('POST', strURL, true);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        self.xmlHttpReq.withCredentials = "true";

        self.xmlHttpReq.onreadystatechange = function () {
            if (self.xmlHttpReq.readyState == 4) {
                if (strURL.indexOf('item_collect') != -1) {
                    //luu san pham
                    if (self.xmlHttpReq.responseText == 'OK')
                        console.log('Lưu sản phẩm thành công!');
                    else console.log(self.xmlHttpReq.responseText);
                } else
                    updatepage(self.xmlHttpReq.responseText);
                document.getElementById("block_button_orderhang").innerHTML = beforeHtml;
                document.getElementById("id_orderhang_add_cart").addEventListener('click', linkClick);
                document.getElementById("id_orderhang_save_item").addEventListener('click', linkSave);
            }
        }
        //console.log(getLink());
        self.xmlHttpReq.send(getLink());
    }

    function updatepage(str) {
        //console.log(str);
        //var dv=document.createElement(str);
        var htmlObject = document.createElement('div');
        htmlObject.innerHTML = str;

        document.body.appendChild(htmlObject);
        //document.getElementById("result").innerHTML = str;
    }

    function getLink() {
        //lấy item_id
        var item_id = getItemId();
        //lấy item_title
        var item_title = getItemTitle();
        //lấy item_image
        var item_image = getItemImage();
        //lấy item_link
        var item_link = getItemLink();
        //lay gia
        var price_taobao = getPrice();
        var comment = getComment();

        var item_price = price_taobao;

        //lấy seller_id	
        var seller_id = getSellerId();
        var seller_name = seller_id;
        //lấy số lượng
        var quantity = getQuantity();
        //lấy màu sắc
        var color = getColor();
        // lay color_size_name
        var color_size_name = getColorSizeName();
        //lấy kích thước
        var size = getSize();

        var params = 'type=paipai';
        if (item_id != '')
            params += '&item_id=' + item_id;
        if (item_title != '')
            params += '&item_title=' + item_title;
        if (item_image != '')
            params += '&item_image=' + item_image;
        if (comment != '')
            params += '&comment=' + comment;
        if (item_link != '')
            params += '&item_link=' + item_link;
        if (item_price > 0)
            params += '&item_price=' + item_price;

        if (color_size_name != '')
            params += '&color_size_name=' + color_size_name;

        if (seller_id.length > 0) {
            params += '&seller_id=' + seller_id;
        }
        if (seller_name.length > 0) {
            params += '&seller_name=' + seller_name;
        }
        if (parseInt(quantity) > 0)
            params += '&quantity=' + quantity;

        if (parseInt(color) > -1)
            params += '&color=' + color;

        if (parseInt(size) > -1)
            params += '&size=' + size;
        return addVersion(params);
    }

    function linkHover() {
        var href = getLink();
        document.getElementById("id_orderhang_add_cart").setAttribute('href', href);
    }

    function linkClick() {
        var full = true;
        //validate số lượng
        var stocks = document.getElementById("buyArea").getElementsByClassName('stock');
        var stocks_amount = stocks.length;

        var buyArea = document.getElementById("buyArea");

        if (stocks_amount > 0) {
            var selects = buyArea.getElementsByClassName('select');
            if (selects.length != stocks_amount) {
                full = false;
            }
        }

        if (full == false) {
            console.log("Bạn chưa chọn đầy đủ thuộc tính sản phẩm!");
            document.getElementById("id_orderhang_add_cart").setAttribute('href', 'javascript:void(0);');
            return;
        }

        document.getElementById("id_orderhang_add_cart").setAttribute('href', 'javascript:void(0)');

        xmlhttpPost(cart_url);
    }

    function linkSave() {
        var href = getLink();
        var full = true;
        //validate số lượng
        var stocks = document.getElementsByClassName('stock');
        var stocks_amount = stocks.length;

        var buyArea = document.getElementById("buyArea");

        if (stocks_amount > 0) {
            var selects = buyArea.getElementsByClassName('select');
            if (selects.length != stocks_amount) {
                full = false;
            }
        }

        if (full == false) {
            console.log("Bạn chưa chọn đầy đủ thuộc tính sản phẩm!");
            document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0);');
            return;
        }

        document.getElementById("id_orderhang_save_item").setAttribute('href', 'javascript:void(0)');

        xmlhttpPost(url_save);
    }

    function quantityOnBlur() {
        var price_taobao = getPrice();
        var price = getVNDPrice(price_taobao);
        //thay lai text price theo so luong
        document.getElementById('orderhang_price').innerHTML = price + ' VNĐ';
    }

    this.htmlOnLoad = function () {
        // Translate
        get_html_translate();

        var src = 'http://orderhang.com/medias/shop-cart-icon.png';
        var price_taobao = getPrice();
        var price_result = getVNDPrice(price_taobao);
        var com_text = '<div><span style="width:15%;float:left;margin-right:5px;font-size:13px;">Mô tả：</span><textarea style="width: 82%;" id="txtBz_orderhang" name="txtBz_orderhang"></textarea></div>';
        var price_text = '<div style="font-weight:bold;color: blue; font-size: 24px;margin-left:44px" id="orderhang_price">'
            + '<span style="font-weight: normal">Giá tạm tính</span>: ' + (price_result) + ' <i style="font-weight: normal;">VNĐ</i></div>';
        var save_text = '<span style="float:right;margin-right:40px;margin-top:10px">[<a id="id_orderhang_save_item" href="javascript:;">Lưu lại đặt hàng sau</a>]</span>';
        var s = '<div class="g-group-member" style="overflow:auto;border: 2px solid green;margin:2px 0;padding:10px">'
            + price_text + com_text
            + '<div class="clr" style="width:100%; margin-top:10px;">';
        s += '<div><span style="float:left;margin-left:44px" id="block_button_orderhang"><a id="id_orderhang_add_cart" href="javascript:;"><img src="' + src + '" alt="" /></a></span>&nbsp; ' + save_text + '</div>';
        s += '</div></div>';

        var div = document.createElement('div');
        div.innerHTML = s;
        document.getElementById('buyArea').parentNode.insertBefore(div.firstChild, document.getElementById('buyArea').nextSibling);
        //document.getElementById("id_orderhang_add_cart").addEventListener( 'mouseover', linkHover);
        document.getElementById("id_orderhang_add_cart").addEventListener('click', linkClick);
        document.getElementById("id_orderhang_save_item").addEventListener('click', linkSave);
    };

    function get_html_translate() {
        // Translate
        var tbl_sku = null;
        var parent_prop = null;
        var tbl_prop = null;
        var obj_prop = new Array();
        var html_translate = "";

        tbl_sku = document.getElementById('buyArea');

        if (tbl_sku != null) {
            parent_prop = tbl_sku;
            tbl_prop = tbl_sku.getElementsByClassName('stock');
            if (tbl_prop != null) {
                for (var o in tbl_prop) {
                    if (isNaN(o)) {
                        continue;
                    }
                    html_translate += tbl_prop[o].outerHTML;
                    obj_prop[o] = tbl_prop[o];
                }
                // Remove current element
                for (var inc in obj_prop) {
                    parent_prop.removeChild(obj_prop[inc]);
                }
            }
        }
        // Translate
        html_translate += parent_prop.innerHTML;
        translate(html_translate, parent_prop);
    }

    function translate(html_chinese, obj) {
        var self = this;
        self.xmlHttpReq = new XMLHttpRequest();
        self.xmlHttpReq.open('POST', url_translate + '?t=' + Math.random(), false);
        self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // Send data
        var data = "data=" + encodeURIComponent(html_chinese);
        self.xmlHttpReq.send(data);

        var dom_original = document.createElement('div');
        dom_original.innerHTML = html_chinese;
        var dom_translate = document.createElement('div');
        dom_translate.innerHTML = self.xmlHttpReq.responseText;

        // Merge chinese and vietname string
        var tb_prop_original = dom_original.getElementsByClassName('stock');
        var tb_prop_translate = dom_translate.getElementsByClassName('stock');
        if (tb_prop_translate.length > 0) {
            for (var i in tb_prop_translate) {
                if (isNaN(i)) {
                    continue;
                }
                // Find property content
                var li_original = tb_prop_original[i].getElementsByTagName('li');
                var li_translate = tb_prop_translate[i].getElementsByTagName('li');
                for (var j in li_translate) {
                    if (isNaN(j)) {
                        continue;
                    }
                    // Check translate
                    if (li_translate[j].getAttribute('attrvalue') != li_original[j].getAttribute('attrvalue')) {
                        li_translate[j].setAttribute('attrvalue', li_translate[j].getAttribute('attrvalue') + "[" + li_original[j].getAttribute('attrvalue') + "]");
                        li_translate[j].getElementsByTagName('a')[0].setAttribute('title', li_translate[j].getAttribute('attrvalue'));
                    }
                }
            }
        }

        obj.innerHTML = decodeURIComponent(dom_translate.innerHTML);
    }

}

var host    = getHostname();
var ex      = null;
//var url             = "http://seudo.vn/addon_process";
var url             = 'http://localhost/tp_alimama/alimama_v2/test/addon.php';
var url_save        = 'http://seudo.vn/addon_process/add_fav'
var url_translate   = "http://old.alimama.vn/translate.php";
var button_bg       = 'http://orderhang.com/medias/shop-cart-icon.png?t=' + Math.random();
var img_loading     = 'http://orderhang.com/frontend/images/ajax-loader.gif?' + Math.random();

// Taobao (tmall)
if (host.indexOf('taobao') != -1 || host.indexOf('tmall') != -1) {
    ex = new taobao(url);
}
// Alibaba (1688)
if (host.indexOf('alibaba') != -1 || host.indexOf('detail.1688') != -1) {
    ex = new alibaba(url, url_save);
}
// Paipai (wanggou)
if (host.indexOf('paipai') != -1 || host.indexOf('wanggou') != -1) {
    ex = new paipai(url, url_save);
}

ex.htmlOnLoad();
