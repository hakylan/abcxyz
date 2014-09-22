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
    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
        },
        complete:function(){
            $('._loading').hide();
        }
    });

    Complaint.complaintFilter('');

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

});

var Complaint = {
    complaints: function(search_data){
        $.ajax({
            url: linkGetListComplaints,
            type: 'POST',
            data: search_data,
            success: function (response) {
                $('#_list-complaints').html(response.html_result);

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