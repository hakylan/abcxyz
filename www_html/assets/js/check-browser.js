$(document).ready(function(){

    function browserName(){
        var Browser = navigator.userAgent;
        if (Browser.indexOf('MSIE') >= 0){
            Browser = 'MSIE';
        }
        else if (Browser.indexOf('Firefox') >= 0){
            Browser = 'Firefox';
        }
        else if (Browser.indexOf('Chrome') >= 0){
            Browser = 'Chrome';
        }
        else if (Browser.indexOf('Safari') >= 0){
            Browser = 'Safari';
        }
        else if (Browser.indexOf('Opera') >= 0){
            Browser = 'Opera';
        }
        else{
            Browser = 'UNKNOWN';
        }
        return Browser;
    }
    if(browserName()=='Firefox'){
        $('._bookmarklet-chrome').hide();
        $('._bookmarklet-firefox').show();
    }

    function browserVersion(){
        var index;
        var version = 0;
        var name = browserName();
        var info = navigator.userAgent;
        index = info.indexOf(name) + name.length + 1;
        version = parseFloat(info.substring(index,index + 3));
        return version;
    }

    if (navigator.appVersion.indexOf("Win") != -1) {
        if(browserName()=='Firefox' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://appldnld.apple.com/Safari5/041-5487.20120509.INU8B/SafariSetup.exe' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=win&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Chrome' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://appldnld.apple.com/Safari5/041-5487.20120509.INU8B/SafariSetup.exe' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=win&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Safari' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://appldnld.apple.com/Safari5/041-5487.20120509.INU8B/SafariSetup.exe' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=win&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='MSIE'){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/ie.png'><br><br><span>"+"Bạn đang sử dụng trình duyệt Internet Explorer (không được Sếu Đỏ hỗ trợ). Trình duyệt được sử dụng nhiều nhất để download các trình duyệt dưới đây:"+"</span>"
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://appldnld.apple.com/Safari5/041-5487.20120509.INU8B/SafariSetup.exe' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=win&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            $('._modal_browser .modal-header').find('span').html('trình duyệt ie không được hỗ trợ');
            return error();
        }
        if(browserName()=='Opera' && browserVersion()<11){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://appldnld.apple.com/Safari5/041-5487.20120509.INU8B/SafariSetup.exe' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=win&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
    }
    if (navigator.appVersion.indexOf("Mac") != -1) {
        if(browserName()=='Firefox' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/DL1569/en_US/Safari5.1.10' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=osx&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Chrome' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/DL1569/en_US/Safari5.1.10' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=osx&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Safari' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/DL1569/en_US/Safari5.1.10' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=osx&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='MSIE'){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/ie.png'><br><br><span>"+"Bạn đang sử dụng trình duyệt Internet Explorer (không được Sếu Đỏ hỗ trợ). Trình duyệt được sử dụng nhiều nhất để download các trình duyệt dưới đây:"+"</span>"
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/DL1569/en_US/Safari5.1.10' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=osx&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            $('._modal_browser .modal-header').find('span').html('trình duyệt ie không được hỗ trợ');
            return error();
        }
        if(browserName()=='Opera' && browserVersion()<11){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/DL1569/en_US/Safari5.1.10' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=osx&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
    }
    if (navigator.appVersion.indexOf("Linux") != -1) {
        if(browserName()=='Firefox' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/#safari' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=linux&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Chrome' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/#safari' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=linux&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='Safari' && browserVersion()<4){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/#safari' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=linux&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
        if(browserName()=='MSIE'){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/ie.png'><br><br><span>"+"Bạn đang sử dụng trình duyệt Internet Explorer (không được Sếu Đỏ hỗ trợ). Trình duyệt được sử dụng nhiều nhất để download các trình duyệt dưới đây:"+"</span>"
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/#safari' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=linux&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            $('._modal_browser .modal-header').find('span').html('trình duyệt ie không được hỗ trợ');
            return error();
        }
        if(browserName()=='Opera' && browserVersion()<11){
            var text_body = "<img src='" + base_url + "/assets/images/check-browser/error.png'><br><br><span>"+
                "Bạn đang sử dụng trình duyệt "+browserName()+" "+ browserVersion()+
                " - Trình duyệt không tương thích với hệ thống Sếu Đỏ. " +
                "Hãy nâng cấp, cài đặt trình duyệt phiên bản mới nhất hoặc lựa chọn các trình duyệt chúng tôi gợi ý phía dưới:"+
                "</span>";
            var text_footer = "<div class='col-lg-12'>" +
                "<a href='https://www.google.com/intl/en/chrome/browser/' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/chrome.png'></a>&nbsp;&nbsp;"+
                "<a href='https://corom.vn/vi/thanks.html' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/corom.png'></a></div>"+
                "<div class='col-lg-12'><br></div>"+
                "<div class='col-lg-12'>" +
                "<a href='http://support.apple.com/downloads/#safari' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/safari.png'></a>&nbsp;&nbsp;"+
                "<a href='https://download.mozilla.org/?product=firefox-28.0&os=linux&lang=en-US' target='_blank'> " +
                "<img src='" + base_url + "/assets/images/check-browser/firefox.png'></a></div>";
            $('._text-body-brw').html(text_body);
            $('._text-footer-brw').html(text_footer);
            return error();
        }
    }

    function error(){
        $('._err_browser').show();
        $('._box-check-browser').show();
    }

    //Button Đặt hàng Seudo.
    $('._grab').click(function(e){
        e.preventDefault();
    });

});



