var fgb_template = Handlebars.compile($("#_orders_list").html()),
    fgb_partial = Handlebars.compile($("#_freight-bill").html());
orders = {};
Handlebars.registerPartial("list", $("#_list-partial").html());
Handlebars.registerPartial("freight_bill_placeholder", $("#_freight-bill").html());

function freight_bill_page_init() {
    //active default filter mode
    $("a._filter_mode").each(function() {
        var mode = $('input[name="filter_mode"]').val(),
            $this = $(this);

        if ($this.data('mode') == mode) {
            $(this).css({'font-weight': 'bold'});
        }
    });

    search_orders();
}

function draw_page_info(current_page) {
    current_page = parseInt(current_page);
    if (current_page > 1) {
        $("#_paging-info").removeClass('hidden').find('span#_current_page_no').text(current_page);
    } else {
        $("#_paging-info").addClass('hidden');
    }

}

function bind_ofs_confirm() {
    $('a._out-of-stock').confirm({
        text: "Bạn có chắc hết hàng đơn này không?",
        title: "XÁC NHẬN",
        confirm : function(o) {
            var order_id = o.data('order-id');
            $.post(ofs_url, {id: order_id}, function(response) {
                if (response.type == 1) {
                    $('#_order-item-' +order_id).fadeOut();
                } else {
                    /* @TODO make it report better */
                    alert(response.message);
                }
            });
        },
        confirmButton: "Có",
        cancelButton: "Không",
        post: true
    });
}

function search_orders(push_state, callback) {
    var data = $('form[name="search-frm"]').serialize(),
        page_url = fb_manage_page+'?'+data;

    if(push_state && page_url != window.location){
        window.history.pushState({'path' : page_url}, '', page_url);
    }

    $.get(get_orders_url, data, function(response) {
        if (callback) {
            callback(response);
        } else {
            if(response.type != 1) {
                $("#_error-placeholder").removeClass('hidden').find('p').html(response.message);
            } else {
                $("#_orders-list-placeholder").html(fgb_template({
                    'total': response.total,
                    'orders' : manipulation_orders(response.orders)
                }));
                for (var x in response.orders) {
                    orders[x] = response.orders[x];
                }
                $('.amount_vnd').moneyFormat({
                    positiveClass : 'font-blue',
                    negativeClass : 'font-red',
                    signal : false
                });
            }

            bind_ofs_confirm();
            draw_page_info($('input[name="page"]').val());
        }
    });
}

function manipulation_orders(data) {
    var temp = {};
    for (var x in data) {
        var t = data[x];

        if (!temp[t.seller_homeland]) {
            temp[t.seller_homeland] = {};
        }

        if (!temp[t.seller_homeland][t.account_purchase_origin]) {
            temp[t.seller_homeland][t.account_purchase_origin] = [];
        }

        temp[t.seller_homeland][t.account_purchase_origin].push(t);
    }

    return temp;
}

$('#search-frm').keyup(function (event) {
    if(event.keyCode == 13) {
        $('input[name="page"]').val(1);
        search_orders(true);
    }
});

$("#search-frm select").change(function (event) {
    search_orders(true);
});

function save_freight_bill(bill, order_id, callback) {
    $.post(save_freight_bill_url, {'bill': bill, 'order_id': order_id}, function(response) {
        callback(response);
    });
}

function reset_paging_button() {
    var $this = $("a._load-more-orders");
    $this.removeClass('disabled');
    $this.find('span.button-content').find('span').text('Tải thêm đơn');
    $this.attr('data-target', 2);
    draw_page_info(1);
}

$(document).ready(function() {
    var list_placeholder = $("#_orders-list-placeholder");
    list_placeholder.on('click', 'a._save-freight-bill', function() {
        var order_id = $(this).attr("data-order-id");//
        FreightBill.SaveBill(order_id);
    });

    list_placeholder.on('paste', 'input[name="add_freight_bill"]', function(event) {
        var e = $(this),
            btn = $('a[rel="' + e.attr('id') +'"]');

        btn.removeClass('disabled');
    });

    $("._add_freight_bill").bind({
        paste : function(){
            console.log("paste");
        }
    });
    list_placeholder.on('keyup', 'input[name="add_freight_bill"]', function(event) {
        var btn = $('a[rel="' + $(this).attr('id') +'"]');
        if($(this).val().trim() != ""){
            btn.removeClass('disabled');
            if (event.ctrlKey && event.keyCode == 86 || event.keyCode == 32 || event.keyCode == 13) {//enter and space
                var order_id = $(this).attr("data-order-id");//

                FreightBill.SaveBill(order_id);
            }
        }else{
            btn.addClass('disabled');
        }

    });

    var check_del_freight_bill= false;
    list_placeholder.on('click', 'button._remove-freight-bill', function() {
        if(check_del_freight_bill){
            return false;
        }
        check_del_freight_bill= true;
        $('._remove-freight-bill').html('Đang xác nhận...');
        var id = $(this).attr('data-id');
        var value = $(this).attr('data-value');
        var order_id = $(this).attr('data-order-id');
        $.ajax({
            url:  remove_freight_bill_url,
            type: "POST",
            data: { id:id },
            success: function (data) {
                check_del_freight_bill= false;
                $('._remove-freight-bill').html('Xác nhận');
                if(data.type !=0){
                    $('#myModal'+id).modal('hide');
                    $('ul').find('#_freight-bill-'+order_id+'-'+value).addClass('hidden');
                }else{
                    $('._error_delete_freight_bill').html(data.message);
                    setTimeout(function(){$('._error_delete_freight_bill').html('')}, 2000);
                }
            }
        });

    });

    $("a._prefix-select").click(function() {
        var e = $(this);
        $('input[name="page"]').val(e.data("1"));

        $('input[name="status"]').val(e.data("value"));

        if(e.data("value")=='' || e.data("value")=='TRANSPORTING' || e.data("value")=='WAITING_DELIVERY' || e.data("value")=='COMPLAINT'){
            $('input[name="ordering"]').val('bought_time');
        }else{
            $('input[name="ordering"]').val(e.data("value").toLowerCase()+"_time");
        }

        $('a._prefix-select.primary-bg').addClass('ui-state-default').removeClass('primary-bg');
        e.addClass('primary-bg').removeClass('ui-state-default');

        search_orders(true);
        reset_paging_button();
    });

    $("a._back-to-page-1").click(function() {
        var $this = $(this);
        $('input[name="page"]').val(1);
        search_orders(true);
    });

    $("a._load-more-orders").click(function() {
        var $this = $(this),
            page = parseInt($('input[name="page"]').val());

        if ($this.hasClass('disabled')) {
            return;
        }

        $this.addClass('disabled').find('span.button-content').find('span').text('Đợi tý nhé ...');

        $this.attr('data-target', page+1);
        $('#search-frm').find('input[name="page"]').val(page+1);

        search_orders(true, function(response) {
            if (response.type) {
                $("#_current_page_no").text(page);
                if (response.orders.length == 0) {
                    $this.find('span.button-content').find('span').text('Ngon, hết đơn rồi đấy');
                } else {
                    $this.removeClass('disabled');
                    $("#_orders-list-placeholder").append(fgb_template({'orders' : manipulation_orders(response.orders)}));
                    $this.find('span.button-content').find('span').text('Tải thêm đơn');
                }
                draw_page_info(page+1);
            }
        });
    });

    $("a._filter_mode").click(function() {
        var e = $(this).data('mode');

        $('._filter_mode').css({'font-weight': 'normal'});
        $(this).css({'font-weight': 'bold'});

        $('input[name="filter_mode"]').val(e);

        $('#search-frm').find('input[name="page"]').val(1);

        search_orders(true);
        reset_paging_button();
    });

    list_placeholder.on('click','._selectCopyAll',function(){
        $(this).select();
    });

    list_placeholder.on('click', 'a._filter-by-purchase-acc', function() {
        $("#search-frm").find('input[name="keyword"]').val($(this).data('account'));
        $('#search-frm').find('input[name="page"]').val(1);
        search_orders(true);
        reset_paging_button();
    })
});

var FreightBill = {
    SaveBill : function(order_id){
        var e = $('._add_freight_bill[data-order-id='+order_id+']'),
            btn = $('a[rel="' + e.attr('id') +'"]'),
            input_value = e.val();
        btn.addClass('disabled');
        if(input_value != ''){
            save_freight_bill(input_value, order_id, function(response) {
                var freight_bill = {"freight_bill" : []};
                if (response.type) {
                    e.val('');
                    e.removeClass('parsley-error');
                    e.parent().find('.parsley-error-list').addClass('font-yellow');
                    //e.parent().find('.parsley-error-list').html(response.message);
                    e.parent().find('.parsley-error-list').removeClass('parsley-error-list');
                    //                    e.parent().find('span.parsley-error-list').remove();
                    freight_bill.freight_bill = response.package;


                    console.log(freight_bill);
                    
                    console.log(freight_bill["freight_bill"]);

                    $("ul#_list-freight-bill-" + order_id).html(
                        fgb_partial({'freight_bill' : freight_bill})
                    );

                    if (response.type == 2) {
                        $('li#_freight-bill-' +order_id +'-' + input_value).append('<br><span class="font-orange">' +response.message +'</span>');
                    }
                } else {
                    e.addClass('parsley-error');
                    e.parent().find('.parsley-error-list').html(response.message);
                }
            });
        }
//        } else {
//            console.log("FUCK");
//            if (input_value.length > 0) {
//                btn.removeClass('disabled');
//            } else {
//                btn.addClass('disabled');
//            }
//        }
    }
};