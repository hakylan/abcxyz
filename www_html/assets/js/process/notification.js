/**
 * Created by ha bau on 5/9/14.
 */
var first_check_notify = false;
var load_more_notify = false;
var page_notify = 1;
$(document).ready(function () {
    $.ajaxSetup({
        beforeSend: function () {
            $('#header_loading_notify').show();
        },
        complete: function () {
            $('#header_loading_notify').hide();
        }
    });

    $('#div_header_notify').click(function (e) {
        e.preventDefault();
        if (first_check_notify == false) {
            first_check_notify = true;
            load_notification(1);
        }
    });

    $("#a_notify_all").click(function(){
        var url = $(this).attr('href');
        window.location = url;
    });
    paging_notification();

});
function paging_notification(){
    //page thong-bao
    $(".paging_notify").click(function (e) {
        e.preventDefault();
        var page = $(this).attr('rel');
        var current_page = $("#current_page").val();
        if (page == 'pre') {
            page = parseInt(current_page) - 1;
        } else if (page == 'next') {
            page = parseInt(current_page) + 1;
        }
        $.ajax({
            type: "POST",
            url: LoadNotify,
            data: {
                page: page
            },
            success: function (response) {

                if (response.type == 1) {
                    var notify_all_order_status = Handlebars.compile($("#notify_all_order_status").html());
                    var notify_all_confirm_order = Handlebars.compile($("#notify_all_confirm_order").html());
                    var notify_all_chat_order = Handlebars.compile($("#notify_all_chat_order").html());
                    var notify_title_date = Handlebars.compile($("#notify_title_date").html());
                    var notify_item = Handlebars.compile($("#notify_item").html());
                    var notify_all_paging = Handlebars.compile($("#notify_all_paging").html());
                    $('#content_notify').html('');

                    jQuery.each(response.notifications, function (i, data_notify) {
                        var title = notify_title_date(data_notify);
                        Handlebars.registerPartial("item_title", title);
                        var content = '';

                        jQuery.each(data_notify.data, function (i, item) {
                            if (item.type == 'CONFIRM_ORDER') {
                                content = content + notify_all_confirm_order(item);
                            } else if (item.type == 'ORDER_STATUS') {
                                content = content + notify_all_order_status(item);
                            } else if (item.type == 'CHAT_ORDER') {
                                content = content + notify_all_chat_order(item);
                            }


                        });
                        Handlebars.registerPartial("item_content", content);
                        $('#content_notify').append(notify_item());
                    });
                    $('#content_notify').append(notify_all_paging(response));
                    paging_notification();
                    return true;
                } else {

                }

            }
        });
    });
}
function load_notification(page) {
    $.ajax({
        type: "POST",
        url: NotifyLinkUrl,
        data: {
            page: page
        },
        success: function (response) {

            if (response.type == 1) {
                var notify_header_order_status = Handlebars.compile($("#notify_header_order_status").html());
                var notify_header_confirm_order = Handlebars.compile($("#notify_header_confirm_order").html());
                var notify_header_chat_order = Handlebars.compile($("#notify_header_chat_order").html());
                $('#span_notify').hide();
                var count = 0
                jQuery.each(response.notifications, function (i, data_notify) {
                    count++;
                    if (data_notify.type == 'CONFIRM_ORDER') {
                        $('#header_loading_notify').before(notify_header_confirm_order(data_notify));
                    } else if (data_notify.type == 'ORDER_STATUS') {
                        $('#header_loading_notify').before(notify_header_order_status(data_notify));
                    } else if (data_notify.type == 'CHAT_ORDER') {
                        $('#header_loading_notify').before(notify_header_chat_order(data_notify));
                    }

                });
                load_more_notify = response.load_more;
                if (count > 5) {
                    $('.overview').slimscroll({
                        alwaysVisible: true
                    }).bind('slimscroll', function (e, pos) {
                        if (pos == 'bottom') {
                            if (load_more_notify == true) {
                                page_notify = page_notify + 1;
                                load_notification(page_notify);
                            }
                        }

                    });
                }
                $('.url_notify').bind('click', function () {
                    var url = $(this).attr('href');
                    window.location = url;
                });
                return true;
            } else {
                if(response.message !=''){
                    $('#header_loading_notify').before('<li class=""><p class="alert alert-alert">' + response.message + '</p></li>');
                    $('#div_notification_more').hide();
                }


            }

        }
    });
}
