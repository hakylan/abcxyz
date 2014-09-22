/**
 * Created by Tan on 2/15/14.
 */


/*
 var nowTemp = new Date();
 var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

 var checkin = $('#datepicker').datepicker({
 onRender: function (date) {
 return date.valueOf() < now.valueOf() ? 'disabled' : '';
 }
 }).on('changeDate',function (ev) {
 if (ev.date.valueOf() > checkout.date.valueOf()) {
 var newDate = new Date(ev.date)
 newDate.setDate(newDate.getDate() + 1);
 checkout.setValue(newDate);
 }
 checkin.hide();
 $('#datepicker2')[0].focus();
 }).data('datepicker');
 var checkout = $('#datepicker2').datepicker({
 onRender: function (date) {
 return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
 }
 }).on('changeDate',function (ev) {
 checkout.hide();
 }).data('datepicker');*/

$('.datepicker').datepicker( "option", "dateFormat", 'yy-mm-dd');


function loadItems() {

    var $seller_username = $('#_sellerName').val();
    var $target_code = $('#_targetCode').val();
    var $dateFrom = $('#datepicker').val();
    var $dateTo = $('#datepicker2').val();
    var $code = '';
    if ($seller_username.replace(/[^-]/g, "").length == 3) {
        $code = $seller_username;
        $seller_username = '';
    }

    $.get(ORDER_DEPOSITED_URL + 'Search', {'seller_username': $seller_username,
        //'code': $code,
        'target_code': $target_code,
        'date_from': $dateFrom,
        'date_to': $dateTo}, function (result) {

        console.log(result.count);
        $txt = '';
        $.each(result.result, function (k, v) {
            console.log(v);
            $txt += '<li>';
            $txt += '<div class="col-lg-4 col-md-4 col-sm-4">';
            $txt += '   <div class="checkbox-deposit">';
            $txt += '<span class="uppercase">' + v.id + '</span>';
            $txt += '</div>';
            $txt += '<p class="normal checkbox"><a href="#">' + v.code + '</a></p>';
            $txt += '<p class="normal-blod checkbox">' + v.seller_name + '</p>';
            $txt += '</div>';
            $txt += '<div class="col-lg-2 col-md-2 col-sm-2 right-top-deposit">';
            $txt += '<p class="normal">' + v.status + '</p>';
            $txt += '</div>';
            $txt += '<div class="col-lg-6 col-md-6 col-sm-6 right-top-deposit">';
            $txt += '<div class="col-lg-4 col-md-4 col-sm-4">';
            $txt += '<p class="normal">' + v.order_quantity + '</p>';
            $txt += '</div>';
            $txt += '<div class="col-lg-4 col-md-4 col-sm-4">';
            $txt += '<p class="normal">' + number_format(v.order_amount) + '<sup>đ</sup></p>';
            $txt += '</div>';
            $txt += '<div class="col-lg-4 col-md-4 col-sm-4">';
            $txt += '<p class="red-normal">' + number_format(v.total_amount) + '<sup>đ</sup>';
            //$txt += '<span class="icon-cham"></span>'
            $txt += '</p>';
            $txt += '</div>';
            $txt += '</div>';

            $txt += '</li>';
        });
        $('#_resultCount').text(result.count);

        $('#_displayOrders').html($txt);

    }, 'json');

};

loadItems();

$('#_submitSearch').click(function () {
    loadItems();
});

$('._changeDate').click(function (e) {
    e.preventDefault();
    var $Obj = $(this);
    $today = $Obj.data('today');
    $next = $Obj.data('range');
    $('#datepicker').val($next);
    $('#datepicker2').val($today);
    loadItems();
});