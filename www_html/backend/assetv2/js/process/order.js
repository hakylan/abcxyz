var order_html = $("#_orders_list").html(),
    order_template,
    orders = {},
    ajax_rq = null;

var str_customer = '';
var str_shipping_mobile = '';
var str_freight_bill = '';

var use_websql = checkBrowserUseWebSql();
use_websql = false;

var classActive = 'active';

Handlebars.registerPartial("list", $("#_list-partial").html());
Handlebars.registerPartial("fb_list", $("#_freight-bill").html());
Handlebars.registerPartial("customer_info", $("#_customer-info").html());

var item_filter_view = Handlebars.compile($("#_item-filter-view").html());

if (typeof order_html != 'undefined') {
    order_template = Handlebars.compile(order_html);
}

//DEMO WEB SQL
if(use_websql){
    var database = 'seudo';
    var table = 'orders';
    var db = openDatabase(database, '1.0', 'Danh sách đơn hàng', 200 * 1024 * 1024);

    db.transaction(function (tx) {
        tx.executeSql('CREATE TABLE IF NOT EXISTS ' + table + ' (id unique, code, avatar, status, seller_name, ' +
            'seller_aliwang, seller_homeland, seller_info, buyer_id, order_quantity, ' +
            'pending_quantity, recive_quantity, customer_confirm, note_customer_confirm, ' +
            'total_amount, order_amount, real_amount, deposit_amount, deposit_ratio, ' +
            'refund_amount, real_payment_amount, real_refund_amount, real_surcharge, ' +
            'real_service_amount, service_fee, domestic_shipping_fee, domestic_shipping_fee_vnd, ' +
            'direct_fill_amount_cny, direct_fill_amount_vnd, payment_link, exchange, weight, invoice, ' +
            'alipay, freight_bill, has_freight_bill, current_warehouse, next_warehouse, ' +
            'destination_warehouse, warehouse_status, transport_status, transport_vn_type, ' +
            'warning_score, user_address_id, tellers_id, paid_staff_id, delivery_staff_id, ' +
            'checker_id, account_purchase_origin, name_recipient_origin, is_deleted, complain_seller, ' +
            'confirm_created_time, confirm_approval_time, created_time, expire_time, deposit_time, ' +
            'real_payment_last_time, tellers_assigned_time, paid_staff_assigned_time, buying_time, ' +
            'negotiating_time, negotiated_time, bought_time, seller_delivered_time, received_from_seller_time, ' +
            'checking_time, checked_time, waiting_delivery_time, confirm_delivery_time, delivered_time, ' +
            'received_time, complaint_time, out_of_stock_time, warehouse_in_time, warehouse_out_time, ' +
            'current_warehouse_time, cancelled_time, packages, buyer, teller, payment, detail_link, ' +
            'need_checking, check_wood_crating)');
    });
}

//console.log('get_init_data: ' + get_init_data);
if(use_websql && get_init_data){
    console.log('Tải dữ liệu từ máy chủ về máy local');
    getDataToLocal();
}

function getDataToLocal(){
    console.log('getDataToLocal');
    $('#_warning-placeholder').show();
    var limit = 50;
    $.get(get_orders_url, { page: 1, limit: limit }, function(response) {
        if(response.total > 0){
            db.transaction(function (tx) {
                $.each(response.orders, function(idx, item){
                    insertDataToWebSql(tx, item);
                });
            });

            response.total = parseInt(response.total);
            var total_page = response.total / limit == 0 ? response.total / limit : parseInt(response.total / limit) + 1;
            for(var j = 2; j <= total_page; j++){
                $.get(get_orders_url, { page: j, limit: limit }, function(response) {
                    if(response.total > 0){
                        db.transaction(function (tx) {
                            $.each(response.orders, function(idx, item){
                                insertDataToWebSql(tx, item);
                            });
                        });
                        if(j == total_page){
                            $('#_warning-placeholder').hide();
                        }
                    }
                });
            }
        }
    });
}

function syncDataServer(){
//    $("#_warning-placeholder").hide();
    $.get(get_orders_url, { all: 1, sync: 1, last_modified_time: last_modified_time }, function(response) {
//        console.log(response);
        if(response.total > 0){
//            $("#_warning-placeholder").show();
            db.transaction(function (tx) {
                $.each(response.orders, function(idx, item){
                    //Kiểm tra xem bản ghi đã tồn tại hay chưa? Nếu chưa thì thêm mới, nếu rồi thì cập nhật
                    tx.executeSql('SELECT `id` FROM ' + table + ' WHERE `id` = ' + item.id, [], function (tx, results) {
                        var len = results.rows.length;
                        if(len > 0){
                            updateDataToWebSql(tx, item);
                            console.log('update to WEBSQL');
                            console.info(item);
                        }else{
                            insertDataToWebSql(tx, item);
                            console.log('insert to WEBSQL');
                            console.info(item);
                        }
                    });
                });
            });
//            $("#_warning-placeholder").hide();
        }
    });
}

function dbToObject($data)
{
    var data = {};

    for (var field in $data) {

        if (!$data.hasOwnProperty(field)) continue;

        if ($data[field] === '[object Object]')
        {
            continue;
        }

//        if ($data[field] == 'true')
//        {
//            $data[field] = true; continue;
//        }
//
//        if ($data[field] == 'false')
//        {
//            $data[field] = false; continue;
//        }

        if ($data[field] && ($data[field][0] == '{' || $data[field][0] == '['))
        {
            data[field] = JSON.parse($data[field]);
        }
        else
        {
            data[field] = $data[field];
        }
    }
    return data;
}

function stepFinal(push_state, page_url, condition){
    if(push_state && page_url != window.location){
        window.history.pushState({'path' : page_url}, '', page_url);
    }

    db.transaction(function (tx) {
        var arrWhere = [];
        var orderBy = '';
        var sql = 'SELECT * FROM ' + table;

        var tmpDateFrom = tmpDateTo = "";
        if(condition.date_from != ""){
            var arrDateFrom = condition.date_from.split('%2F');
            tmpDateFrom = arrDateFrom[2] + '-' + arrDateFrom[1] + '-' + arrDateFrom[0] + ' 00:00:00';
        }
        if(condition.date_to != ""){
            var arrDateTo = condition.date_to.split('%2F');
            tmpDateTo = arrDateTo[2] + '-' + arrDateTo[1] + '-' + arrDateTo[0] + ' 23:59:59';
        }

        if(condition.search_complain_seller != ""){
            arrWhere.push(' complain_seller = "1" ');
        }

        if(condition.homeland != ""){
            arrWhere.push(' seller_homeland = "' + condition.homeland + '" ');
        }

        if(condition.date_from != "" && condition.date_to == ""){
            arrWhere.push(' created_time >= "' + tmpDateFrom + '" ');
        }

        if(condition.date_to != "" && condition.date_from == ""){
            arrWhere.push(' created_time <= "' + tmpDateTo + '" ');
        }

        if(condition.date_from != "" && condition.date_to != ""){
            arrWhere.push(' created_time >= "' + tmpDateFrom + '" AND created_time <= "' + tmpDateTo + '" ');
        }

        if(condition.status != ""){
            var arrStatus = [];
            var tmpStatus = condition.status.split(',');
            var tmpStatus = condition.status.split('%2C');
            if(tmpStatus.length > 0){
                for(var s = 0; s < tmpStatus.length; s++){
                    arrStatus[s] = '"' + tmpStatus[s].trim() + '"';
                }
            }

            if(arrStatus.length > 0){
                arrWhere.push(' `status` IN (' + arrStatus.join() + ') ');
            }
        }

        if(condition.keyword != ""){
            //TH1: Tìm theo kiếm ID
            //TH2: Tìm theo ...
            if(!isNaN(condition.keyword)){
                var keyword = parseInt(condition.keyword);
                arrWhere.push(' id = ' + keyword + ' ');
            }else{
                var keyword = condition.keyword;
                var w = " ( `code` LIKE '%" + keyword + "%' " +
                    "OR `invoice` LIKE '%" + keyword + "%' " +
                    "OR `seller_name` LIKE '%" + keyword + "%' " +
                    "OR `name_recipient_origin` LIKE '%" + keyword + "%' " +
                    "OR `account_purchase_origin` LIKE '%" + keyword + "%' ) ";
                arrWhere.push(w);
            }
        }

        if(condition.search_bill != ""){
            var tmpSearchBill = [];
            var arrSearchBill = condition.search_bill.split('%2C');
            for(var v = 0; v < arrSearchBill.length; v++){
                tmpSearchBill[v] = '"' + arrSearchBill[v] + '"';
            }
            arrWhere.push(' `has_freight_bill` IN (' + tmpSearchBill.join() + ') ');
        }else{
            arrWhere.push(' `has_freight_bill` IN ("0", "1") ');
        }

        //Tìm kiếm trong bảng khác
        if(str_customer != ""){
            arrWhere.push(' `buyer_id` IN (' + str_customer + ') ');
        }
        if(str_freight_bill != ""){
            arrWhere.push(' `id` IN (' + str_freight_bill + ') ');
        }
        if(str_shipping_mobile != ""){
            arrWhere.push(' `user_address_id` IN (' + str_shipping_mobile + ') ');
        }

        var ordering = 'created_time';
        if(condition.ordering != ""){
            ordering = Ordering(condition.ordering);
        }

        if(condition.sort_order != ""){
            orderBy = " ORDER BY " + ordering + " " + condition.sort_order + " ";
        }

        //where by
        if(arrWhere.length > 0){
            sql += ' WHERE ';
            for(var k = 0; k < arrWhere.length; k++){
                if( ( k + 1 ) == arrWhere.length){
                    sql += arrWhere[k];
                }else{
                    sql += arrWhere[k] + ' AND ';
                }
            }
        }


        //order by
        if(orderBy != ''){
            sql += orderBy;
        }

        var q = sql;

        condition.page = parseInt(condition.page);
        var start = ( condition.page - 1 ) * PER_PAGE;
        sql += " LIMIT " + start + ", " + PER_PAGE + " ";

        console.log(sql);
        var total_page = 0;
        var orders = [];
        tx.executeSql(sql, [], function (tx, results) {
            var len = results.rows.length, i;
            for (i = 0; i < len; i++){
                var row = results.rows.item(i);
                /*
                if( row['buyer'] === '[object Object]' ){
                    //TODO
                }else{
                    var t = JSON.parse(row['buyer']);
                }

                if( row['teller'] === '[object Object]' ){
                    //TODO
                }else{
                    var k = JSON.parse(row['teller']);
                }

                if( row['packages'] === '[object Object]' ){
                    //TODO
                }else{
                    var l = JSON.parse(row['packages']);
                }

                if( row['payment'] === '[object Object]' ){
                    //TODO
                }else{
                    var m = JSON.parse(row['payment']);
                }
                */
                console.log(dbToObject(row));
                orders[i] = dbToObject(row);
            }
//            console.log(orders);
            var $html = order_template({ orders: orders });
            var $target = $("#_orders-list-placeholder");
            if(condition.page > 1){
                $target.append($html);
            }else{
                $target.html($html);
            }

            //format money
            $target.find('._money-amount').moneyFormat({
                positiveClass : 'font-blue',
                negativeClass : 'font-red',
                signal : false
            });

        }, null);

        tx.executeSql(q, [], function (tx, results) {
            var total = results.rows.length, i;
            $('#_result-count').text(total);

            total_page = total % PER_PAGE == 0 ? total / PER_PAGE
                : parseInt(total / PER_PAGE) + 1;

            //show hide load more
            showHideButonReadMore(total_page, condition.page);
            console.log('current_page: ' + condition.page);
            console.log('total_page: ' + total_page);
        }, null);

        //Chỉ sync dữ liệu khi ở trang đầu tiên, các trang tiếp theo không đồng bộ
        if(condition.page == 1){

        }else{

        }
        syncDataServer();
    });
}

function searchFreightBill(push_state, page_url, condition){
    if(condition.freight_bill == ""){
        searchCustomer(push_state, page_url, condition);
    }else{
        $.ajax({
            url: LinkSearchFreightBill,
            type : "GET",
            data: {
                freight_bill: condition.freight_bill
            },
            success: function (response) {
                str_freight_bill = response.data;
                searchCustomer(push_state, page_url, condition);
            }
        });
    }
}

function searchCustomer(push_state, page_url, condition){
    if(condition.customer == ""){
        searchShippingMobile(push_state, page_url, condition);
    }else{
        $.ajax({
            url: LinkSearchCustomer,
            type : "GET",
            data: {
                customer: condition.customer
            },
            success: function (response) {
                str_customer = response.data;
                searchShippingMobile(push_state, page_url, condition);
            }
        });
    }
}

function searchShippingMobile(push_state, page_url, condition){
    if(condition.shipping_mobile == ""){
        stepFinal(push_state, page_url, condition);
    }else{
        $.ajax({
            url: LinkSearchShippingMobile,
            type : "GET",
            data: {
                shipping_mobile: condition.shipping_mobile
            },
            success: function (response) {
                str_shipping_mobile = response.data;
                stepFinal(push_state, page_url, condition);
            }
        });
    }
}

function getDataWebSql(push_state){
    var condition = [];
    var params = [];
    var a = $('form[name="search-frm"]').serialize();
    var b = a.split('&');
    for(var c = 0; c < b.length; c++){
        var tmp = b[c].split('=');
        condition[tmp[0]] = tmp[1];

        if( tmp[1] != '' ) {
            params.push(b[c]);
        }
    }
    var page_url = order_management_page+'?' + params.join('&');
//    console.log(page_url);

    if(use_websql){
        searchFreightBill(push_state, page_url, condition);
    }else{

        if(push_state && page_url != window.location){
            window.history.pushState({'path' : page_url}, '', page_url);
        }

        var $loading = $('#_loading');
        $loading.show();
        $('._arrow-item').removeClass('_new');
        $.get(get_orders_url, a, function(response) {
            $('._load-more-orders').removeClass('loadding');
            if(response.type != 1) {//error
                $("#_error-placeholder").removeClass('hidden').find('p').html(response.message);
            } else {
                var $orders_list_placeholder = $("#_orders-list-placeholder");
                var $html = order_template({ orders: response.orders });
                $("#_result-count").text(response.total);

                if ( response.total > 0 ) {
                    if(condition.page > 1){
                        $orders_list_placeholder.append($html);
                    }else{
                        $orders_list_placeholder.html($html);
                    }
                } else {
                    $orders_list_placeholder.html('<div class="text-center" style="padding: 10px;">Không có đơn hàng nào!</div>');
                }
                
                $('img.lazy').lazyload();

                $('input[name="total_page"]').val(response.total_page);

                $('._arrow-item._new').click(function(){
                    var $this = $(this);
                    var $root = $this.parents('._item-order-view').toggleClass("open");
                });

                $orders_list_placeholder.find('._money-amount-k').moneyFormat({
                    useClass: false,
                    useThousand: true,
                    symbol: 'K',
                    signal: false
                });

                $orders_list_placeholder.find('._money-amount-cny').moneyFormat({
                    useClass: false,
                    symbol: '¥',
                    signal: false
                });

                showHideButonReadMore(response.total_page, condition.page);

                getComplaints(response);

                $loading.hide();

                $orders_list_placeholder.find('._btn-click-customer-confirm').click(function() {
                    if( $(this).hasClass('disabled') ) {
                        return false;
                    }

                    $('._btn-click-customer-confirm').removeClass('selected');
                    $(this).addClass('selected');

                    var message = 'Bạn có chắc chắn xác nhận mua đơn hàng này thay khách hàng?';

                    Common.BSConfirm( message );
                });

                $orders_list_placeholder.find('#_btn-agree-confirm').click(function(e){
                    var e = $(e.currentTarget);

                    if(e.hasClass('clicked')){
                        return false;
                    }

                    var message_public = ' xác nhận mua đơn hàng thay khách hàng.';
                    var message_private = ' xác nhận mua đơn hàng thay khách hàng.';

                    var tmp = $('._btn-click-customer-confirm.selected').parents('._item-order-view');
                    if( tmp.length == 0 ) {
                        return false;
                    }

                    var order_id = parseInt( tmp.data('order-id') );

                    $.ajax({
                        url : LinkManageConfirm,
                        type : "POST",
                        data : {
                            id : order_id,
                            status : CUSTOMER_CONFIRM_WAIT,
                            message_public: message_public,
                            message_private: message_private
                        },
                        success : function(response){
                            if( response.type == 1 ) {
                                var $target = $('._item-order-view[data-order-id="' + order_id + '"]');
                                $target.find('._show-btn-click-customer-confirm').html('· <span class="font-gray">Đã xác nhận</span>');
                            } else {
                                Common.BSAlert( 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu' );
                            }
                        }
                    });

                });

            }

        });
    }
}

function getComplaints(data){
//    console.log(data.arrOrderId);
//    $.ajax({
//        url: LinkSearchComplaintByOrderId,
//        type : "GET",
//        data: {
//            arrOrderId: data.arrOrderId
//        },
//        success: function (response) {
//            console.log(response);
//        }
//    });
}

function showHideButonReadMore(total_page, page){
    var $btn_load_more = $("._load-more-orders");
    if(total_page > 0 && page < total_page){
        $btn_load_more.show();
    }else{
        $btn_load_more.hide();
    }
}

getDataWebSql(false);

function Ordering(condition){
    var order_by = '';
    switch(condition) {
        case STATUS_DEPOSITED:
            order_by = 'deposit_time';
            break;
        case STATUS_BUYING:
            order_by = 'buying_time';
            break;
        case STATUS_NEGOTIATING:
            order_by = 'negotiating_time';
            break;
        case STATUS_WAITING_BUYER_CONFIRM:
        case STATUS_NEGOTIATED:
            order_by = 'negotiated_time';
            break;
        case STATUS_BOUGHT:
        case STATUS_SELLER_DELIVERY:
            order_by = 'bought_time';
            break;
        case STATUS_RECEIVED_FROM_SELLER:
            order_by = 'received_from_seller_time';
            break;
        case STATUS_CHECKING:
            order_by = 'checking_time';
            break;
        case STATUS_CHECKED:
            order_by = 'checked_time';
            break;
        case STATUS_TRANSPORTING:
        case STATUS_WAITING_FOR_DELIVERY:
            order_by = 'current_warehouse_time';
            break;
        case STATUS_RECEIVED:
            order_by = 'delivered_time';
            break;
        case STATUS_DELIVERING:
        case STATUS_COMPLAINT:
            order_by = 'delivered_time';
            break;
        case STATUS_OUT_OF_STOCK:
            order_by = 'out_of_stock_time';
            break;
        case STATUS_CANCELLED:
            order_by = 'cancelled_time';
            break;
        case STATUS_INIT:
            order_by = 'created_time';
            break;
    }

    return order_by;
}

function updateDataWebSqlById(id, data){
    /*
    var data = [{
        key: 'account_purchase_origin',
        value: 'DEMO',
        type: 'STRING'
    },{
        key: 'alipay',
        value: 'TEST',
        type: 'STRING'
    }];
    var data = [{
        key: 'account_purchase_origin',
        value: 'DEMO',
        type: 'STRING'
    }];
    */
    if(data.length > 0){
        db.transaction(function (tx) {
            var arr = [];
            $.each(data, function(key, item){
                if(item.type == 'INT'){
                    arr[key] = ' ' + item.key + ' = ' + item.value + ' ';
                }
                if(item.type == 'STRING'){
                    arr[key] = ' ' + item.key + ' = "' + item.value + '" ';
                }
            });
            if(arr.length > 0){
                var sql = 'UPDATE ' + table + ' SET ' + arr.join() + ' WHERE id = ' + id;
                tx.executeSql(sql);
            }
        });
    }
}

function updateDataToWebSql(tx, item){
    item.packages = item.packages == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.packages) + "'";
    item.buyer = item.buyer == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.buyer) + "'";
    item.teller = item.teller == undefined ? "'" + {} + "'" :  "'" + JSON.stringify(item.teller) + "'";
    item.payment = item.payment == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.payment) + "'";

    var sql = 'UPDATE ' + table + ' SET code = "' + item.code + '", avatar = "' + item.avatar + '", status = "' + item.status + '", seller_name = "'
        + item.seller_name + '", seller_aliwang = "' + item.seller_aliwang + '", seller_homeland = "' + item.seller_homeland + '", seller_info = "'
        + item.seller_info + '", buyer_id = "' + item.buyer_id + '", order_quantity = "' + item.order_quantity + '", pending_quantity = "'
        + item.pending_quantity + '", recive_quantity = "' + item.recive_quantity + '", customer_confirm = "' + item.customer_confirm + '", note_customer_confirm = "'
        + item.note_customer_confirm + '", total_amount = "' + item.total_amount + '", order_amount = "'
        + item.order_amount + '", real_amount = "' + item.real_amount + '", deposit_amount = "' + item.deposit_amount + '", deposit_ratio = "'
        + item.deposit_ratio + '", refund_amount = "' + item.refund_amount + '", real_payment_amount = "' + item.real_payment_amount + '", real_refund_amount = "'
        + item.real_refund_amount + '", real_surcharge = "' + item.real_surcharge + '", real_service_amount = "' + item.real_service_amount + '", service_fee = "'
        + item.service_fee + '", domestic_shipping_fee = "' + item.domestic_shipping_fee + '", domestic_shipping_fee_vnd = "' + item.domestic_shipping_fee_vnd + '", direct_fill_amount_cny = "'
        + item.direct_fill_amount_cny + '", direct_fill_amount_vnd = "' + item.direct_fill_amount_vnd + '", payment_link = "' + item.payment_link + '", exchange = "'
        + item.exchange + '", weight = "' + item.weight + '", invoice = "' + item.invoice + '", alipay = "' + item.alipay + '", freight_bill = "'
        + item.freight_bill + '", has_freight_bill = "' + item.has_freight_bill + '", current_warehouse = "' + item.current_warehouse + '", next_warehouse = "'
        + item.next_warehouse + '", destination_warehouse = "' + item.destination_warehouse + '", warehouse_status = "' + item.warehouse_status + '", transport_status = "'
        + item.transport_status + '", transport_vn_type = "' + item.transport_vn_type + '", warning_score = "' + item.warning_score + '", user_address_id = "'
        + item.user_address_id + '", tellers_id = "' + item.tellers_id + '", paid_staff_id = "' + item.paid_staff_id + '", delivery_staff_id = "'
        + item.delivery_staff_id + '", checker_id = "' + item.checker_id + '", account_purchase_origin = "' + item.account_purchase_origin + '", name_recipient_origin = "'
        + item.name_recipient_origin + '", is_deleted = "' + item.is_deleted + '", complain_seller = "' + item.complain_seller + '", confirm_created_time = "'
        + item.confirm_created_time + '", confirm_approval_time = "' + item.confirm_approval_time + '", created_time = "'
        + item.created_time + '", expire_time = "' + item.expire_time + '", deposit_time = "' + item.deposit_time + '", real_payment_last_time = "'
        + item.real_payment_last_time + '", tellers_assigned_time = "' + item.tellers_assigned_time + '", paid_staff_assigned_time = "'
        + item.paid_staff_assigned_time + '", buying_time = "' + item.buying_time + '", negotiating_time = "' + item.negotiating_time + '", negotiated_time = "'
        + item.negotiated_time + '", bought_time = "' + item.bought_time + '", seller_delivered_time = "' + item.seller_delivered_time + '", received_from_seller_time = "'
        + item.received_from_seller_time + '", checking_time = "' + item.checking_time + '", checked_time = "' + item.checked_time + '", waiting_delivery_time = "'
        + item.waiting_delivery_time + '", confirm_delivery_time = "' + item.confirm_delivery_time + '", delivered_time = "' + item.delivered_time + '", received_time = "'
        + item.received_time + '", complaint_time = "' + item.complaint_time + '", out_of_stock_time = "' + item.out_of_stock_time + '", warehouse_in_time = "'
        + item.warehouse_in_time + '", warehouse_out_time = "' + item.warehouse_out_time + '", current_warehouse_time = "' + item.current_warehouse_time + '", cancelled_time = "'
        + item.cancelled_time + '", packages = ' + item.packages + ', buyer = ' + item.buyer + ', teller = ' + item.teller + ', payment = '
        + item.payment + ', detail_link = "' + item.detail_link + '", need_checking = "' + item.need_checking + '", check_wood_crating = "' + item.check_wood_crating + '"' +
        ' WHERE id = ' + item.id;
    tx.executeSql(sql);
}

function insertDataToWebSql(tx, item){
    item.packages = item.packages == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.packages) + "'";
    item.buyer = item.buyer == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.buyer) + "'";
    item.teller = item.teller == undefined ? "'" + {} + "'" :  "'" + JSON.stringify(item.teller) + "'";
    item.payment = item.payment == undefined ? "'" + {} + "'" : "'" + JSON.stringify(item.payment) + "'";

    var sql = 'INSERT INTO ' + table + ' (id, code, avatar, ' +
        'status, seller_name, seller_aliwang, seller_homeland, seller_info, ' +
        'buyer_id, order_quantity, pending_quantity, recive_quantity, customer_confirm, ' +
        'note_customer_confirm, total_amount, order_amount, real_amount, deposit_amount, ' +
        'deposit_ratio, refund_amount, real_payment_amount, real_refund_amount, real_surcharge, ' +
        'real_service_amount, service_fee, domestic_shipping_fee, domestic_shipping_fee_vnd, ' +
        'direct_fill_amount_cny, direct_fill_amount_vnd, payment_link, exchange, weight, ' +
        'invoice, alipay, freight_bill, has_freight_bill, current_warehouse, next_warehouse, ' +
        'destination_warehouse, warehouse_status, transport_status, transport_vn_type, warning_score, ' +
        'user_address_id, tellers_id, paid_staff_id, delivery_staff_id, checker_id, account_purchase_origin, ' +
        'name_recipient_origin, is_deleted, complain_seller, confirm_created_time, confirm_approval_time, ' +
        'created_time, expire_time, deposit_time, real_payment_last_time, tellers_assigned_time, ' +
        'paid_staff_assigned_time, buying_time, negotiating_time, negotiated_time, bought_time, ' +
        'seller_delivered_time, received_from_seller_time, checking_time, checked_time, ' +
        'waiting_delivery_time, confirm_delivery_time, delivered_time, received_time, ' +
        'complaint_time, out_of_stock_time, warehouse_in_time, warehouse_out_time, ' +
        'current_warehouse_time, cancelled_time, packages, buyer, teller, payment, detail_link, ' +
        'need_checking, check_wood_crating) VALUES (' + item.id + ', "' + item.code + '", "' + item.avatar + '", "' + item.status + '", "'
        + item.seller_name + '", "' + item.seller_aliwang + '", "' + item.seller_homeland + '", "'
        + item.seller_info + '", "' + item.buyer_id + '", "' + item.order_quantity + '", "'
        + item.pending_quantity + '", "' + item.recive_quantity + '", "' + item.customer_confirm + '", "'
        + item.note_customer_confirm + '", "' + item.total_amount + '", "'
        + item.order_amount + '", "' + item.real_amount + '", "' + item.deposit_amount + '", "'
        + item.deposit_ratio + '", "' + item.refund_amount + '", "' + item.real_payment_amount + '", "'
        + item.real_refund_amount + '", "' + item.real_surcharge + '", "' + item.real_service_amount + '", "'
        + item.service_fee + '", "' + item.domestic_shipping_fee + '", "' + item.domestic_shipping_fee_vnd + '", "'
        + item.direct_fill_amount_cny + '", "' + item.direct_fill_amount_vnd + '", "' + item.payment_link + '", "'
        + item.exchange + '", "' + item.weight + '", "' + item.invoice + '", "' + item.alipay + '", "'
        + item.freight_bill + '", "' + item.has_freight_bill + '", "' + item.current_warehouse + '", "'
        + item.next_warehouse + '", "' + item.destination_warehouse + '", "' + item.warehouse_status + '", "'
        + item.transport_status + '", "' + item.transport_vn_type + '", "' + item.warning_score + '", "'
        + item.user_address_id + '", "' + item.tellers_id + '", "' + item.paid_staff_id + '", "'
        + item.delivery_staff_id + '", "' + item.checker_id + '", "' + item.account_purchase_origin + '", "'
        + item.name_recipient_origin + '", "' + item.is_deleted + '", "' + item.complain_seller + '", "'
        + item.confirm_created_time + '", "' + item.confirm_approval_time + '", "'
        + item.created_time + '", "' + item.expire_time + '", "' + item.deposit_time + '", "'
        + item.real_payment_last_time + '", "' + item.tellers_assigned_time + '", "'
        + item.paid_staff_assigned_time + '", "' + item.buying_time + '", "' + item.negotiating_time + '", "'
        + item.negotiated_time + '", "' + item.bought_time + '", "' + item.seller_delivered_time + '", "'
        + item.received_from_seller_time + '", "' + item.checking_time + '", "' + item.checked_time + '", "'
        + item.waiting_delivery_time + '", "' + item.confirm_delivery_time + '", "' + item.delivered_time + '", "'
        + item.received_time + '", "' + item.complaint_time + '", "' + item.out_of_stock_time + '", "'
        + item.warehouse_in_time + '", "' + item.warehouse_out_time + '", "' + item.current_warehouse_time + '", "'
        + item.cancelled_time + '", ' + item.packages + ', ' + item.buyer + ', ' + item.teller + ', '
        + item.payment + ', "' + item.detail_link + '", "' + item.need_checking + '", "' + item.check_wood_crating + '")';
//    console.log(sql);
    try{
        tx.executeSql(sql);
    }catch (e){

    }

}

function checkBrowserUseWebSql(){
    var flag = false;
    if (navigator.userAgent.search("MSIE") >= 0) {
        //TODO
    }
    //Check if browser is Chrome or not
    else if (navigator.userAgent.search("Chrome") >= 0) {
        flag = true;
    }
    //Check if browser is Firefox or not
    else if (navigator.userAgent.search("Firefox") >= 0) {
        //TODO
    }
    //Check if browser is Safari or not
    else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
        flag = true;
    }
    //Check if browser is Opera or not
    else if (navigator.userAgent.search("Opera") >= 0) {
        flag = true;
    }
    return flag;
}

function calNumDay(condition){
    var date = '';

    var arrToday = today.split('/');
    td = arrToday[1] + '/' + arrToday[0] + '/' + arrToday[2];

    var date = new Date(td);
    var newdate = new Date(date);

    switch(condition) {

        case 'BEFORE_1_DAY':
            newdate.setDate(newdate.getDate() - 1);
            break;
        case 'BEFORE_3_DAY':
            newdate.setDate(newdate.getDate() - 3);
            break;
        case 'BEFORE_7_DAY':
            newdate.setDate(newdate.getDate() - 7);
            break;
        case 'BEFORE_15_DAY':
            newdate.setDate(newdate.getDate() - 15);
            break;

//        case 'CURRENT_DAY':
//            newdate.setDate(newdate.getDate() + 0);
//            break;
//        case 'THREE_DAY':
//            newdate.setDate(newdate.getDate() - 3);
//            break;
//        case 'FIVE_DAY':
//            newdate.setDate(newdate.getDate() - 5);
//            break;
//        case 'FIFTEEN_DAY':
//            newdate.setDate(newdate.getDate() - 15);
//            break;
//        case 'LONGER_THREE_DAY':
//            newdate.setDate(newdate.getDate() + 3);
//            break;
//        case 'LONGER_FIVE_DAY':
//            newdate.setDate(newdate.getDate() + 5);
//            break;
//        case 'LONGER_FIFTEEN_DAY':
//            newdate.setDate(newdate.getDate() + 15);
//            break;
//        case 'LONGER_THIRTY_DAY':
//            newdate.setMonth(newdate.getMonth() + 1);
//            break;
        default:
            console.log('default');
    }

    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var y = newdate.getFullYear();

    date = ( dd < 10 ? '0' + dd : dd ) + '/' + ( mm < 10 ? '0' + mm : mm ) + '/' + y;
    return date;
}

$(document).ready(function () {

    //Auto scroll load more
    var $loadmoreButton = $('._load-more-orders').hide();
//    $(window).scroll(function(e) {
//        var currentOffset = window.pageYOffset,
//            pageHeight = $(document).height(), loadState;
//
//        if (currentOffset + 650 >= pageHeight) {
//            var total_page = $('input[name="total_page"]').val();
//            var current_page = $('input[name="page"]').val();
//
//            if( total_page > 1 && current_page <= 3 && !$loadmoreButton.is('hidden') ) {
//                $loadmoreButton.click();
//            }
//
//        }
//    });

    $(window).scroll(function() {
        if( $(window).scrollTop() == $(document).height() - $(window).height() ) {
            var total_page = parseInt ( $('input[name="total_page"]').val() );
            var current_page = parseInt( $('input[name="page"]').val() );

            if( total_page > 1 && current_page < 3 ) {
                $loadmoreButton.click();
            }
        }
    });

    $("#date_from").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    $("#date_to").datepicker({
        dateFormat: 'dd/mm/yy'
    });

    //begin js process on page

    var $sort_order = $('._sort_order');
    $sort_order.click(function(){
        var $this = $(this);
        $sort_order.removeClass(classActive);
        $this.addClass(classActive);
        $('input[name="sort_order"]').val($this.data('sort'));

        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $(".search-open .arrow-open").click(function(){
        $(".search-open .arrow-open").toggleClass("open");
        $(".search-step1").slideToggle();
    });

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });

    var $search_by_date = $('._search-by-date');
    $search_by_date.click(function(){
        var $this = $(this);
        var name = $this.data('name');
        var value = $this.data('value');
        var text = $this.data('text');
        var prefix = '-date';
        var classActive = 'font-bold';

        $('._clear-filter' + prefix + '.date').remove();
        if( $this.hasClass( classActive ) ) {//Đã click rồi
            $this.removeClass( classActive );
            $('input[name="date_to"]').val('');
            $('input[name="is_search_by_date"]').val('');
        } else {//Chưa click
            $('#_show-list-filter').append(item_filter_view({ name: 'date', value: '', text: text, prefix: prefix }));
            $('._clear-filter' + prefix + ':last').click(function(){
                $('._search-by-date[data-name="' + name + '"]').removeClass('font-bold');
                $('input[name="date_to"]').val('');
                $(this).remove();
            });

            $search_by_date.removeClass(classActive);
            $this.addClass(classActive);
            $('input[name="date_to"]').val( calNumDay($this.data('date')) );
            $('input[name="is_search_by_date"]').val( text );
        }

        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $('._item-status-order').click(function(){
        var $this = $(this);
        var value = $this.data('value');
        if($this.hasClass(classActive)){
            $this.removeClass(classActive);
        }else{
            $this.addClass(classActive);
        }

        //Nếu là 4 trạng thái cuối cùng [ĐANG GIAO HÀNG, KHÁCH NHẬN HÀNG, HỦY BỎ, HẾT HÀNG] thì hiển thị mới trước
        if( value == 'DELIVERING' || value == 'RECEIVED'
            || value == 'CANCELLED' || value == 'OUT_OF_STOCK' ) {
            //Chọn trạng thái order tương ứng
            $('select[name="ordering"] option[value="' + value + '"]').prop('selected', true);
            $('select[name="ordering"]').selectpicker('refresh');
            //order DESC
            $('._sort_order').removeClass( classActive );
            $('._sort_order[data-sort="DESC"]').addClass( classActive );
            $('input[name="sort_order"]').val('DESC');
        }

        //Lấy những trạng thái đã được chọn
        var strStatus = "";
        var arrStatus = [];
        $("._item-status-order." + classActive).each(function(i){
            var $t = $(this);
            arrStatus[i] = $t.data('value');
        });
        if(arrStatus.length > 0){
            strStatus = arrStatus.join();
        }

        $('input[name="status"]').val(strStatus);
        $('input[name="page"]').val('1');

        getDataWebSql(true);
    });

    $('._search-more').click(function(){
        var $this = $(this);
        var $delete = $this.find('.delete');
        var text = $this.data('text');
        var value = $this.data('value');
        var name = $this.data('name');

        if( $this.hasClass( classActive ) ) { // Trường hợp xóa
            $delete.addClass('hidden');

            $this.removeClass(classActive);
            $('._clear-filter').find('.item-step2[data-value="' + value + '"][data-name="' + name + '"]').parent().remove();
            $('input[name="' + name + '"]').val('');
        } else { // Trường hợp thêm mới
            $delete.removeClass('hidden');

            $('input[name="' + name + '"]').val(value);

            $this.addClass(classActive);
            $('#_show-list-filter').append(item_filter_view({ name: name, value: value, text: text }));
            $('._clear-filter:last').click(function(){
                clickClearFilter(name, value);
                $(this).remove();
            });
        }

        $('input[name="page"]').val('1');
        getDataWebSql(true);

    });

    $('._clear-filter').click(function(){
        var $this = $(this);
        var name = $this.find('.item-step2').data('name');
        clickClearFilter(name, '');
        $(this).remove();
    });

    $('._clear-filter-select').click(function(){
        var $this = $(this);
        var name = $this.find('.item-step2').data('name');
        var value = $this.find('.item-step2').data('value');

        $('select[name="' + name + '"] option[value=""]').prop('selected', true);
        $('select[name="' + name + '"]').selectpicker('refresh');
        $this.remove();

        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $('._clear-filter-input').click(function() {
        var $this = $(this);
        var name = $this.find('.item-step2').data('name');
        var value = $this.find('.item-step2').data('value');

        $('input[name="' + name + '"]').val('');
        $(this).remove();
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $('._clear-filter-date').click(function() {
        var $this = $(this);
        var name = $this.find('.item-step2').data('name');
        var value = $this.find('.item-step2').data('value');

        $('._search-by-date').removeClass('font-bold');
        $('input[name="date_to"]').val('');
        $('input[name="is_search_by_date"]').val('');

        $this.remove();
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    function clickClearFilter(name, value){
        $("._search-more[data-name='" + name + "']").removeClass(classActive);
        $("._search-more[data-name='" + name + "']").find('.delete').addClass('hidden');
        $('input[name="' + name + '"]').val('');

        $('input[name="page"]').val('1');
        getDataWebSql(true);
    }
    //end js process on page

    (function() {
        var $timeList = $('#_order-time').find('li');

        $timeList.find('a').click(function(e) {
            e.stopPropagation();
            var $this = $(this), $parent = $this.parent();

            if ($parent.hasClass('active')) {
                $timeList.removeClass('active');
                $timeList.find('a').removeClass('font-black');
                $timeList.find('i').remove();

                $('input[name="ordering"]').val('');
            } else {
                $timeList.removeClass('active');
                $timeList.find('a').removeClass('font-black');
                $timeList.find('i').remove();

                $('input[name="ordering"]').val($this.data('ordering'));

                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }

            $('input[name="page"]').val('1');
            getDataWebSql(true);
        });
    })();

    (function() {
        var $dateList = $('#_search-date').find('li'), $dateListLonger = $('#_search-date-longer').find('li');

        $dateList.find('a').each(function(){
            var $this = $(this), $parent = $this.parent();

            if ($this.data('date') == $('input[name="search_date"]').val()) {
                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }
        })

        $dateList.find('a').click(function(e){
            e.stopPropagation();

            var $this = $(this), $parent = $this.parent();

            if ($parent.hasClass('active')) {
                $dateList.removeClass('active');
                $dateList.find('a').removeClass('font-black');
                $dateList.find('i').remove();

                $('input[name="search_date"]').val('');

                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');
            } else {
                $dateList.removeClass('active');
                $dateList.find('a').removeClass('font-black');
                $dateList.find('i').remove();

                $dateListLonger.removeClass('active');
                $dateListLonger.find('a').removeClass('font-black');
                $dateListLonger.find('i').remove();
                $('input[name="longer_day"]').val('');

                $('input[name="search_date"]').val($this.data('date'));

                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');

                $('input[name="date_from"]').val(calNumDay($this.data('date')));
                $('input[name="date_to"]').val(today);
            }

            $('input[name="page"]').val('1');

            getDataWebSql(true);
        });
    })();

    (function() {
        var $dateListLonger = $('#_search-date-longer').find('li'), $dateList = $('#_search-date').find('li');

        $dateListLonger.find('a').each(function(){
            var $this = $(this), $parent = $this.parent();

            if ($this.data('longer-day') == $('input[name="longer_day"]').val()) {
                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }
        })

        $dateListLonger.find('a').click(function(e){
            e.stopPropagation();

            var $this = $(this), $parent = $this.parent();

            if ($parent.hasClass('active')) {
                $dateListLonger.removeClass('active');
                $dateListLonger.find('a').removeClass('font-black');
                $dateListLonger.find('i').remove();

                $('input[name="longer_day"]').val('');

                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');
            } else {
                $dateListLonger.removeClass('active');
                $dateListLonger.find('a').removeClass('font-black');
                $dateListLonger.find('i').remove();

                $dateList.removeClass('active');
                $dateList.find('a').removeClass('font-black');
                $dateList.find('i').remove();
                $('input[name="search_date"]').val('');

                $('input[name="longer_day"]').val($this.data('longer-day'));

                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');

                $('input[name="date_from"]').val(today);
                $('input[name="date_to"]').val(calNumDay($this.data('longer-day')));
            }

            $('input[name="page"]').val('1');

            getDataWebSql(true);
        });
    })();

    var $serachComplaint = $('#_search-complaint').find('li');

    $serachComplaint.find('a').each(function(){
        var $this = $(this), $parent = $this.parent(), complaint_seller = $('input[name="search_complain_seller"]').val();

        if(complaint_seller){
            if ($this.data('complaint-seller') == complaint_seller) {
                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }
        }
    })

    $serachComplaint.find('a').click(function(e){
        e.stopPropagation();

        var $this = $(this), $parent = $this.parent();

        if ($parent.hasClass('active')) {
            $serachComplaint.removeClass('active');
            $serachComplaint.find('a').removeClass('font-black');
            $serachComplaint.find('i').remove();

            $('input[name="search_complain_seller"]').val('');
        } else {
            $serachComplaint.removeClass('active');
            $serachComplaint.find('a').removeClass('font-black');
            $serachComplaint.find('i').remove();

            $('input[name="search_complain_seller"]').val("1");

            $parent.addClass('active');
            $this.addClass('font-black');
            $parent.append('&nbsp;<i class="fa fa-times"></i>');
        }

        $('input[name="page"]').val('1');

        getDataWebSql(true);
    });

    (function(){
        var $billList = $('#_search-bill').find('li');

        $billList.find('a').each(function(){
            var $this = $(this), $parent = $this.parent(), bill = $('input[name="search_bill"]').val();

            if(bill){
                if ($this.data('bill') == bill) {
                    $parent.addClass('active');
                    $this.addClass('font-black');
                    $parent.append('&nbsp;<i class="fa fa-times"></i>');
                }
            }
        })

        $billList.find('a').click(function(e){
            e.stopPropagation();

            var $this = $(this), $parent = $this.parent();

            if ($parent.hasClass('active')) {
                $billList.removeClass('active');
                $billList.find('a').removeClass('font-black');
                $billList.find('i').remove();

                $('input[name="search_bill"]').val('');
                $('input[name="search_complain_seller"]').val('');
            } else {
                $billList.removeClass('active');
                $billList.find('a').removeClass('font-black');
                $billList.find('i').remove();

                $('input[name="search_bill"]').val($this.data('bill'));
                $('input[name="search_complain_seller"]').val("1");

                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }

            $('input[name="page"]').val('1');

            getDataWebSql(true);
        });
    })();

    (function(){
        $sortList = $('#_sort_order').find('li');

        $sortList.find('a').click(function(e){
            e.stopPropagation();
            var $this = $(this), $parent = $this.parent();

            if($parent.hasClass('active')){
                $sortList.removeClass('active');
                $sortList.find('a').removeClass('font-black');
                $sortList.find('i').remove();

                $('input[name="sort_order"]').val('');
            } else {
                $('input[name="sort_order"]').val($this.data('sort'));

                $sortList.removeClass('active');
                $sortList.find('a').removeClass('font-black');
                $sortList.find('i').remove();

                $parent.addClass('active');
                $this.addClass('font-black');
                $parent.append('&nbsp;<i class="fa fa-times"></i>');
            }

            $('input[name="page"]').val('1');
            getDataWebSql(true);
        });
    })();

    //Lọc đơn hàng theo trạng thái
    var $tabsSelect = '._tabs-select';
    $($tabsSelect).click(function() {
        var $this = $(this);
        var classSelected = 'primary-bg';
        var strStatus = "";
        var arrStatus = [];
        if($this.hasClass(classSelected)){
            $this.removeClass(classSelected);
        }else{
            $this.addClass(classSelected);

            //Nếu chọn trạng thái tất cả thì xóa toàn bộ trạng thái đã chọn khác
            if($this.data('value') == ""){
                $($tabsSelect + ":not(:first)").removeClass(classSelected);
            }
        }

        //Lấy những trạng thái đã được chọn
        $("._tabs-select." + classSelected + ":not(:first)").each(function(i){
            var $t = $(this);
            arrStatus[i] = $t.data('value');
        });
        if(arrStatus.length > 0){
            strStatus = arrStatus.join();
        }
        console.log('strStatus: ' + strStatus);

        $('input[name="status"]').val(strStatus);
        $('input[name="page"]').val('1');

        getDataWebSql(true);
    });

    $('#search-frm input').keyup(function (event) {
        if(event.keyCode == 13) {
            var $this = $(this);
            var classActiveInput = 'background-active-input-order';
            var value = $this.val();
            var name = $this.attr('name');
            var prefix = '-input';
            $('._clear-filter' + prefix + '.' + name).remove();

            $this.removeClass( classActiveInput );

            if(  name == 'seller_aliwang' && value != '' ) {
                $('#_show-list-filter').append(item_filter_view({ name: name, value: value, text: 'Aliwangwang người bán: ' + value, prefix: prefix }));
                $('._clear-filter' + prefix + ':last').click(function() {
                    $('input[name="' + name + '"]').val('');
                    $(this).remove();
                    $('input[name="page"]').val('1');
                    getDataWebSql(true);
                });
            }

            if( value != '' ) {
                $this.addClass( classActiveInput );
            }

            $(this).find('input[name="page"]').val(1);
            getDataWebSql(true);
        }
    });

    $('#date_from').change(function(e){
        $('input[name="page"]').val('1');
        $('input[name="is_search_by_date"]').val('');
        $('._search-by-date').removeClass('font-bold');
        getDataWebSql(true);
    });

    $('#date_to').change(function(e){
        $('input[name="page"]').val('1');
        $('input[name="is_search_by_date"]').val('');
        $('._search-by-date').removeClass('font-bold');
        getDataWebSql(true);
    });

    $('select').change(function (e) {
        var $e = $(e.currentTarget);
        var name = $e.attr('name');
        var value = $e.val();
        var text = $e.find('option[value="' + value + '"]').text();
        var title = $e.find('option[value=""]').text();
        var prefix = '-select';
        $('._clear-filter' + prefix + '.' + name).remove();
        if( value != '' ) {
            $('#_show-list-filter').append(item_filter_view({ name: name, value: value, text: title + ': ' + text, prefix: prefix }));
            $('._clear-filter' + prefix + ':last').click(function() {
                $('select[name="' + $(this).find('.item-step2').data('name') + '"] option[value=""]').prop('selected', true);
                $('select[name="' + $(this).find('.item-step2').data('name') + '"]').selectpicker('refresh');
                $(this).remove();

                $('input[name="page"]').val('1');
                getDataWebSql(true);
            });
        }
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $('select[name="longer_day"]').change(function (event) {
        $('input[name="page"]').val('1');

        var $dateList = $('#_search-date').find('li');
        $dateList.removeClass('active');
        $dateList.find('a').removeClass('font-black');
        $dateList.find('i').remove();
        $('input[name="search_date"]').val('');

        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    //Click scroll load more
    $("._load-more-orders").click(function() {
        var $this = $(this);
        $this.hide();
        if( !$this.hasClass('loadding') ) {
            $('input[name="page"]').val( parseInt( $('input[name="page"]').val() ) + 1 );

            $this.addClass('loadding');
            getDataWebSql(true);
        }
    });



});
