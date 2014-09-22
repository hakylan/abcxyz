var item_complaint_template;
var page = 1;

parseInt()

var ajax_rq = null;
$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#datepicker2" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });

    console.log('init');
});
$(window).on('load', function () {
    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
    console.log('window load');
});

$(document).ajaxComplete(function(){
    console.log('ajaxComplete');


});

$(document).ready(function(){
    $('img').lazyload();
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
        },
        complete:function(){
            $('._loading').hide();
        }
    });

    $(document).on('click','._page_order',function(){
        var page = $(this).attr('data-page-id');
        $('._li_page').removeClass("active");
        $(this).parent().addClass("active");
        $('._page').val(page);
        Complaint.complaintFilter();
    });

    $(document).on('click','._time_before',function(event){
        var data_time = $(this).attr('data-time');
        data_time = parseInt(data_time);
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth()+1;
        var y = date.getFullYear();
        var _time_before = date.getTime() - (24*60*60*1000*data_time);
        var date_before = new Date(_time_before);
        var to = d+'-'+m+'-'+y;
        var month = date_before.getMonth() + 1;
        var from = date_before.getDate()+'-'+month+'-'+date_before.getFullYear();
        console.log(from);
        $('._from').val(from);
        $('._to').val(to);
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('#_search').on('keyup','._keyword',function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            Complaint.complaintFilter();
        }
    });

    $(document).on('click','._btn_filter',function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._from').change(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('._btn_filter').change(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });

    $('#_sel-status').change(function(){
        $('#_current-page').val('1');
        Complaint.complaintFilter();
    });



    Complaint.complaintFilter('');

});

var Complaint = {
    complaints: function(search_data){
        $.ajax({
            url: linkGetListComplaints,
            type: 'POST',
            data: search_data,
            success: function (response) {
                $('._list-complaints').html(response.html_result);
                $('._total_filter').text(response.total_record);

                //paging
                $('._paging').click(function(e){
                    console.log('paging');
                    var e = $(e.currentTarget);
                    if(!e.hasClass('clicked')){
                        var page = e.data('page');
                        $('#_current-page').val(page);
                        e.addClass('clicked');
                        Complaint.complaintFilter();
                        $('body').scrollTo($('._total_filter'));
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
                        $('body').scrollTo($('._total_filter'));
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
                        $('body').scrollTo($('._total_filter'));
                    }
                });
            }
        });
    },

    complaintFilter : function(type){
        console.log('complaintFilter');
        var search_data = $('#_search').serialize();
        console.log(search_data);
        if(type == null){
            var pageUrl = ListComplaintUrl+'?'+search_data;
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

