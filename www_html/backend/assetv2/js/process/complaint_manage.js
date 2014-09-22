var ajax_rq = null;
$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#datepicker2" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });

//    $('.selectpicker').selectpicker({
//        'selectedText': 'cat'
//    });
});
$(window).on('load', function () {
//    $('.selectpicker').selectpicker({
//        'selectedText': 'cat'
//    });
});

$(document).ajaxComplete(function(){
    //TODO
});

$(document).ready(function(){
    console.log('complaint manage');

    $("._arrow-active").click(function(){
        $(this).toggleClass("open");
        $("._content-bottom").slideToggle();
    });

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });

    $( "#_start-date" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#_end-date" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
        },
        complete:function(){
            $('._loading').hide();
        }
    });

    $('._from').change(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._to').change(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._btn_filter').click(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._filter-status').click(function(e){
//        console.log('_filter-status');
        $('._filter-status').removeClass('active');
        var e = $(e.currentTarget);
        var type = e.data('type');
        e.addClass('active');

        $('input[name="status"]').val(type);
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('#_btn-search').click(function() {
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._chk-errors').click(function(e){
        var e = $(e.currentTarget);
        if(e.val() == 'NO'){
            e.val('YES');
        }else{
            e.val('NO');
        }

        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('input[name="order_code"]').keypress(function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            Complaint.complaintFilter();
        }
    });

    $('input[name="customer_code"]').keypress(function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            Complaint.complaintFilter();
        }
    });

    $('input[name="item_id"]').keypress(function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            Complaint.complaintFilter();
        }
    });

//    $('#_recipient-by').change(function(e){
//        $('#_current-page').val('1');
//        Complaint.complaintFilter();
//    });
//
//    $('#_approval-by').change(function(e){
//        $('#_current-page').val('1');
//        Complaint.complaintFilter();
//    });

//    $('#search').find('input').keypress(function (e) {
//        if(e.keyCode == 13) {
//            $('#_current-page').val('1');
//            Complaint.complaintFilter();
//        }
//    });

    $('#search').find('select').change(function (event) {
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('select[name="approval_by"]').change(function() {
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('select[name="recipient_by"]').change(function() {
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    var $reason = $('._reason');
    $('._reason').click(function () {
        var arrReason = [];
        var strReason = '';
        $reason.each(function(i) {
            if( $(this).is(':checked') ) {
                arrReason.push( $(this).val() );
            }
        });

        if( arrReason.length > 0 ) {
            strReason = arrReason.join(',');
        }

        $('input[name="reasons"]').val( strReason );
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    Complaint.complaintFilter('');

    Complaint.statistics();

});

var Complaint = {

    statistics: function() {
        $.ajax({
                url: linkGetStatisticsComplaint,
                type: 'GET',
                success: function( response ){
                    if( response.data ) {
                        $('#_total').html( response.data.total );
                        $('#_total_before_month').html( response.data.total_before_month );
                        $('#_total_current').html( response.data.total_current );
                        $('#_total_accept').html( response.data.total_accept );
                        $('#_total_refund').html( response.data.total_refund );
                        $('#_total_reject').html( response.data.total_reject );
                    }

                },
                error: function() {}
        });
    },

    complaints: function(search_data){
        $.ajax({
            url: linkGetListComplaints,
            type: 'POST',
            data: search_data,
            success: function (response) {
                if(response.type == 0){
                    Common.BSAlert(response.message);
                    return false;
                }

                $('#_list-complaints').html(response.html_result);
                $('#_total-complaints').html(response.total_record);

                var html = "";
                html += '<div class="module-float line-border">';
                html += '<div class="item-border"></div>';
                html += '<span class="uppercase font-gray">Chưa tới hạn xử lý</span>';
                html += '</div>';
                if(response.condition.status == STATUS_OUSTANDING && $('._item-complaint-view.delay:first').length > 0){
                    $('._item-complaint-view.delay:first').before(html);
                }

                Complaint.calTotalStatus();

                //paging
                $('._paging').click(function(e){
                    console.log('paging');
                    var e = $(e.currentTarget);
                    if(!e.hasClass('clicked')){
                        var page = e.data('page');
                        $('#_current-page').val(page);
                        e.addClass('clicked');
                        Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                    }
                });

                $('#_page-prev').click(function(e){
                    console.log('page prev');
                    var e = $(e.currentTarget);
                    if(!e.hasClass('clicked')){
                        var $page = $('#_current-page');
                        var current_page = parseInt($page.val());
                        current_page--;
                        $page.val(current_page);
                        e.addClass('clicked');
                        Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                    }
                });

                $('#_page-next').click(function(e){
                    console.log('page next');
                    var e = $(e.currentTarget);
                    if(!e.hasClass('clicked')){
                        var $page = $('#_current-page');
                        var current_page = parseInt($page.val());
                        current_page++;
                        $page.val(current_page);
                        e.addClass('clicked');
                        Complaint.complaintFilter();
//                        $('body').scrollTo($('._total_filter'));
                    }
                });
            }
        });
    },

    calTotalStatus: function(){
//        var $status_all = $('input[name="total_status_all"]');
//        if($status_all.val() > 0){
//            $('._filter-status[data-type="' + $status_all.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_all.val() + ')');
//        }else{
//            $('._filter-status[data-type="' + $status_all.attr('t') + '"]').find('._total-by-status').hide();
//        }

        var $status_waiting_receive = $('input[name="total_status_waiting_receive"]');
        if($status_waiting_receive.val() > 0){
            $('._filter-status[data-type="' + $status_waiting_receive.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_waiting_receive.val() + ')');
        }else{
            $('._filter-status[data-type="' + $status_waiting_receive.attr('t') + '"]').find('._total-by-status').hide();
        }

        var $status_oustanding = $('input[name="total_status_oustanding"]');
        if($status_oustanding.val() > 0){
            $('._filter-status[data-type="' + $status_oustanding.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_oustanding.val() + ')');
        }else{
            $('._filter-status[data-type="' + $status_oustanding.attr('t') + '"]').find('._total-by-status').hide();
        }

        var $status_accept = $('input[name="total_status_accept"]');
        if($status_accept.val() > 0){
            $('._filter-status[data-type="' + $status_accept.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_accept.val() + ')');
        }else{
            $('._filter-status[data-type="' + $status_accept.attr('t') + '"]').find('._total-by-status').hide();
        }

//        var $status_reject = $('input[name="total_status_reject"]');
//        if($status_reject.val() > 0){
//            $('._filter-status[data-type="' + $status_reject.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_reject.val() + ')');
//        }else{
//            $('._filter-status[data-type="' + $status_reject.attr('t') + '"]').find('._total-by-status').hide();
//        }

//        var $status_refund = $('input[name="total_status_refund"]');
//        if($status_refund.val() > 0){
//            $('._filter-status[data-type="' + $status_refund.attr('t') + '"]').find('._total-by-status').show().html('(' + $status_refund.val() + ')');
//        }else{
//            $('._filter-status[data-type="' + $status_refund.attr('t') + '"]').find('._total-by-status').hide();
//        }
    },

    complaintFilter : function(type){
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = ListBackendComplaintUrl+'?'+search_data;
            Complaint.push_state(pageUrl);
        }
        Complaint.complaints(search_data);
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    }
}