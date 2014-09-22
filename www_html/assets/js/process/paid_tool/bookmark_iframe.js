var BookmarkLet = {
    getCookie: function (CookieName) {
        var name = CookieName + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    },
    'taobao': function () {
        var normal_price = document.getElementById('J_StrPrice');
        if (normal_price == null) {
            normal_price = document.getElementById('J_StrPriceModBox');
        }
        var promotion_price = document.getElementById('J_PromoPrice');

        var price = 0;
        if (promotion_price != null) { // Promotion price
            if (window.location.href.indexOf('tmall') > -1) {
                if (promotion_price.getElementsByClassName('tm-price').length > 0) {
                    price = promotion_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                }
            } else {
                if (promotion_price.getElementsByClassName('tb-rmb-num').length > 0) {
                    price = promotion_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                } else {
                    // 12/12/2013
                    if (promotion_price.className = 'tb-rmb-num') {
                        if (promotion_price.getElementsByClassName('tb-promo-price-type').length > 0) {
                            if ((price == 0 || price == null) & document.getElementsByClassName('tb-rmb-num').length > 0) {
                                price = document.getElementsByClassName('tb-rmb-num')[0].innerHTML.match(/[0-9]*[\.,]?[0-9]+/g);
                            }
                        } else {
                            price = promotion_price.innerHTML.match(/[0-9]*[\.,]?[0-9]+/g);
                        }
                    }
                }
            }
            price = BookmarkLet.processPrice(price);

            if (price == 0) { // Try if price not found
                price = normal_price.getElementsByClassName('tm-price');
                if (price.length > 0) {
                    price = price[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
                } else {
                    price = document.getElementById('J_StrPriceModBox');
                    if (price != 0) {
                        price = price.getElementsByClassName('tb-rmb-num');
                        if (price.length > 0) {
                            price = price[0].innerHTML.match(/[0-9]*[\.,]?[0-9]+/g);
                        }
                    }
                }

                price = BookmarkLet.processPrice(price);
            }
        } else {
            if (window.location.href.indexOf('tmall') > -1) {
                price = normal_price.getElementsByClassName('tm-price')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
            } else { // taobao
                price = normal_price.getElementsByClassName('tb-rmb-num')[0].textContent.match(/[0-9]*[\.,]?[0-9]+/g);
            }
            price = BookmarkLet.processPrice(price);
        }
        return price;
    },
    'processPrice': function (price) {
        if (price == null)
            return 0;
        p = String(price).replace(',', '.').match(/[0-9]*[\.]?[0-9]+/g);
        return parseFloat(p);
    }
}

var url = encodeURIComponent(window.location.href);

// Validate url
var decodeUrl = decodeURIComponent(url);
var valid = false;
if (decodeUrl.match(/detail.1688.com|http:\/\/item.taobao.com\/item.htm|http:\/\/detail.tmall.com\/item.htm|shop.nahuo.com\/product-([0-9]+).html|eelly.com\/goods\/([0-9]+).html/)) {
    valid = true;
}

// Load price promotion


// check iframe existed
if (valid) {
    var seudo_existed = document.getElementById('seudo_bound');
    if (seudo_existed == null) { // Not existed
        // Print iframe data
        if (decodeUrl.match(/detail\.tmall\.com|item\.taobao\.com/)) {
            url = encodeURIComponent(decodeURIComponent(url) + '&promotion_price=' + BookmarkLet.taobao());
        }
        makeFrame(url);
    }
} else {
    alert('Khong dat hang duoc tu duong dan nay, hay lien he voi chung toi de duoc giup do!');
    close_bookmark();
}

//function

function getContent(url) {
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('GET', url, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.withCredentials = "true";

    self.xmlHttpReq.onreadystatechange = function () {
        if (self.xmlHttpReq.readyState == 4) {
            // Process response here
            alert(self.xmlHttpReq.responseText);
        }
    }
    // Send data
    self.xmlHttpReq.send();
}

function makeFrame(url_detail) {
    //    var t = BookmarkLet.getCookie('t');
    //    getContent("");
    //    console.log(t);

    var ifrm = document.createElement("IFRAME"),
        div_bound = document.createElement('div'),
        div_header = document.createElement('header');

    ifrm.setAttribute("src", seudo_host + "/pai_tool/?url=" + url_detail);
    ifrm.style.width = "100%";
    ifrm.style.height = (window.innerHeight) + 'px';
    ifrm.style.position = 'fixed';
    ifrm.style.top = '0';
    ifrm.style.left = 0;
    ifrm.style.border = 0;
    ifrm.style.zIndex = 2147483646;

    div_bound.innerHTML =

            '<div id="seudo_loading" style="position: fixed;top: 0px;right: 0;width: 120px ;height: 20px;z-index: 2147483647;text-align: center;" class="alert-trans">'
            + '<p style="color: #fff;background: #111;padding-top: 5px;padding-bottom: 5px;border-radius: 0 0 0 3px ;opacity: 0.8;" class="alert"> Dang tai du lieu.... </p>'
            + '</div>';

    div_bound.setAttribute('id', 'seudo_bound');
    ifrm.setAttribute('id', 'seudo_ifrm');
    div_bound.appendChild(ifrm)
    document.body.appendChild(div_bound);

    document.getElementById('seudo_ifrm').onload = function () {
        setTimeout(function () {
            document.getElementById('seudo_loading').parentNode.removeChild(document.getElementById('seudo_loading'));
            // Close element
            var close_element = document.createElement('div');
            //close_element.innerHTML = '<span onclick="close_bookmark()" style="position: fixed; right: 0px; z-index: 2147483647; display: inline-block; border-radius: 5px; cursor: pointer; font-size: 25px; border: 1px solid rgb(203, 64, 64); color: rgb(203, 64, 64); top: -8px; padding: 0px 23px 28px;" title="Close"> x </span>';
            var close_html = '<div onclick="close_bookmark()" class="menuitem close-book" style=" background-position:center;cursor: pointer;float: left;height: 60px;position: fixed;width: 60px;top:0;right:0;background: url(\'' + seudo_host + '/assets/images/iconbookmarklet/close-book.png\') no-repeat #CB4040 center ; z-index: 9999999999999;">'
                + '<div class="tooltipmenu" style="display: none;height: 20px;left: -58px;width: 65px; position: absolute;top: 30%;background: url(\'' + seudo_host + '/assets/images/iconbookmarklet/close-toltipbk.png\') no-repeat ;"> </div>'
                + '</div>';
            close_element.innerHTML = close_html;
            document.getElementById('seudo_bound').appendChild(close_element);
            //            document.getElementById('seudo_bound').attribute("data-cookie",t);
        }, 1000)
    }

    setTimeout(hideLoading(),3000);

}

function close_bookmark() {
    var obj = document.getElementById('seudo_bound');
    if (obj) {
        obj.parentNode.removeChild(obj);
    }
    // Remove bookmark script
    var bookMark = document.getElementById('seudo_script');
    if (bookMark) {
        bookMark.parentNode.removeChild(bookMark);
    }
}

// Resize window
window.onresize = function (e) {
    // change height of box bookmark
    if (document.getElementById('seudo_bound') != null)
        document.getElementById('seudo_bound').getElementsByTagName('iframe')[0].style.height = window.innerHeight + 'px';
}
