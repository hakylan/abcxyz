var order_html = $("#_orders_list").html(),
    order_template,
    orders = {},
    ajax_rq = null;

var str_customer = '';
var str_shipping_mobile = '';
var str_freight_bill = '';

var use_websql = checkBrowserUseWebSql();
use_websql = false;

Handlebars.registerPartial("list", $("#_list-partial").html());
Handlebars.registerPartial("fb_list", $("#_freight-bill").html());
Handlebars.registerPartial("customer_info", $("#_customer-info").html());

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
            $('#_result-count strong').text(total);

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
    var a = $('form[name="search-frm"]').serialize();
    var b = a.split('&');
    for(var c = 0; c < b.length; c++){
        var tmp = b[c].split('=');
        condition[tmp[0]] = tmp[1];
    }
    var page_url = order_management_page+'?' + a;

    if(use_websql){
        searchFreightBill(push_state, page_url, condition);
    }else{

        if(push_state && page_url != window.location){
            window.history.pushState({'path' : page_url}, '', page_url);
        }

        var $loading = $('#_loading');

        $loading.show();
        $.get(get_orders_url, a, function(response) {

            if(response.type != 1) {//error
                $("#_error-placeholder").removeClass('hidden').find('p').html(response.message);
            } else {
                var $orders_list_placeholder = $("#_orders-list-placeholder");
                var $html = order_template({ orders: response.orders });
                $("#_result-count strong").text(response.total);

                if(condition.page > 1){
                    $orders_list_placeholder.append($html);
                }else{
                    $orders_list_placeholder.html($html);
                }

                $orders_list_placeholder.find('._money-amount').moneyFormat({
                    positiveClass : 'font-blue',
                    negativeClass : 'font-red',
                    signal : false
                });

                showHideButonReadMore(response.total_page, condition.page);
                $loading.hide();
            }

        });
    }
}

function showHideButonReadMore(total_page, page){
    $btn_load_more = $("a._load-more-orders");
    if(total_page > 0 && page < total_page){
        $btn_load_more.show();
    }else{
        $btn_load_more.hide();
    }

    $btn_load_more.removeClass('loadding');
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
        case 'CURRENT_DAY':
            newdate.setDate(newdate.getDate() + 0);
            break;
        case 'THREE_DAY':
            newdate.setDate(newdate.getDate() - 3);
            break;
        case 'FIVE_DAY':
            newdate.setDate(newdate.getDate() - 5);
            break;
        case 'FIFTEEN_DAY':
            newdate.setDate(newdate.getDate() - 15);
            break;
        case 'LONGER_THREE_DAY':
            newdate.setDate(newdate.getDate() + 3);
            break;
        case 'LONGER_FIVE_DAY':
            newdate.setDate(newdate.getDate() + 5);
            break;
        case 'LONGER_FIFTEEN_DAY':
            newdate.setDate(newdate.getDate() + 15);
            break;
        case 'LONGER_THIRTY_DAY':
            newdate.setMonth(newdate.getMonth() + 1);
            break;
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
    $("#date_from").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    $("#date_to").datepicker({
        dateFormat: 'dd/mm/yy'
    });

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

    $('#search-frm').keyup(function (event) {
        if(event.keyCode == 13) {
            $(this).find('input[name="page"]').val(1);
            getDataWebSql(true);
        }
    });

    $("#search-frm").find('#date_from').change(function(e){
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $("#search-frm").find('#date_to').change(function(e){
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $("#search-frm").find('select[name="homeland"]').change(function (event) {
        $('input[name="page"]').val('1');
        getDataWebSql(true);
    });

    $("#search-frm").find('select[name="longer_day"]').change(function (event) {
        $('input[name="page"]').val('1');

        var $dateList = $('#_search-date').find('li');
        $dateList.removeClass('active');
        $dateList.find('a').removeClass('font-black');
        $dateList.find('i').remove();
        $('input[name="search_date"]').val('');

        getDataWebSql(true);
    });

    //Click scroll load more
    $("a._load-more-orders").click(function() {
        var current_page = $('input[name="page"]').val();
        current_page = parseInt(current_page) + 1;
        $('input[name="page"]').val(current_page);
        if(!$(this).hasClass('loadding')){
            $(this).addClass('loadding');
            getDataWebSql(true);
        }
    });

});
