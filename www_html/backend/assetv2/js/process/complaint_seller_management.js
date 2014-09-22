var ajax_rq = null;
var complaint_seller_row_template, paging_tempate;

var currentdate = new Date();
var year = currentdate.getFullYear();
var month = currentdate.getMonth()+1;
var day = currentdate.getDate();
var _sub_time = currentdate.getHours() + ':' + currentdate.getMinutes() + ' ' + day + '/' + (month<10 ? '0' : '') + month;

$(function() {
    $( "#_start-date" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $( "#_end-date" ).datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $('.selectpicker').selectpicker();
});
$(window).on('load', function () {

});

$(document).ajaxComplete(function(){

});

$(document).ready(function(){
    complaint_seller_row_template = Handlebars.compile($("#_complaint-seller-row-template").html());
    paging_tempate = Handlebars.compile($("#_paging").html());
    ComplaintSeller.complaintSellerFilter('');

    $('._filter-status').click(function(e){
//        console.log('_filter-status');
        $('._filter-status').removeClass('active');
        var e = $(e.currentTarget);
        var type = e.data('type');
        e.addClass('active');

        $('input[name="status"]').val(type);
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('._from').change(function(){
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('._to').change(function(){
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('#_level').change(function(e){
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('._reason').click(function(e){
        $('#_current-page').val('1');
        var str_reason = "";
        $('._reason').each(function(i){
            if($(this).is(':checked')){
                str_reason += $(this).val() + ',';
            }
        });
        $('input[name="reason"]').val(str_reason);
        ComplaintSeller.complaintSellerFilter();
    });

    $('#_seller_homeland').change(function(e){
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('#_key1').keypress(function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            ComplaintSeller.complaintSellerFilter();
        }
    });

    $('#_key2').keypress(function(e){
        if(e.keyCode == 13){
            $('#_current-page').val('1');
            ComplaintSeller.complaintSellerFilter();
        }
    });

    $('#_btn-search').click(function(e){
        $('#_current-page').val('1');
        ComplaintSeller.complaintSellerFilter();
    });

    $('#_account-purchase-origin, #_order_buyer_id').change(function(e){
        var e = $(e.currentTarget);
        $('#_current-page').val('1');
        var text = $('#_account-purchase-origin option[value="' + e.val() + '"]').text();
        $('input[name="account_purchase_origin_name"]').val(text);
        ComplaintSeller.complaintSellerFilter();
    });
});

var ComplaintSeller = {
    createPaging: function(total_page, current_page){
        var html = '';
        if(total_page > 1){
            var j = 3;
            html += '<div class="row link-bottom">';
                html += '<div class="col-lg-12 col-md-12">';
                    html += '<ul class="pagination pull-right">';
                        if(current_page > 1){
                            html += '<li class="pre"><a id="_page-prev">&lt;</a></li>';
                        }

                        for(var i = j; i > 0; i--){
                            if(current_page - i > 0){
                                html += '<li>';
                                    html += '<a class="_paging" data-page="' + ( current_page - i ) + '">' + ( current_page - i ) + '</a>';
                                html += '</li>';
                            }
                        }

                        html += '<li class="active"><a>' + current_page + '</a></li>';

                        for(var i = 1; i <= j; i++){
                            if(current_page + i <= total_page){
                                html += '<li>';
                                    html += '<a class="_paging" data-page="' + ( current_page + i ) + '">' + ( current_page + i ) + '</a>';
                                html += '</li>';
                            }
                        }

                        if(current_page < total_page){
                            html += '<li class="next"><a id="_page-next">&gt;</a></li>';
                        }
                    html += '</ul>';
                html += '</div>';
            html += '</div>';
        }
        return html;
    },

    getList: function(search_data){
        $.ajax({
            url: linkGetListComplaintSellers,
            type: 'GET',
            data: search_data,
            success: function (response) {
//                console.log(response);
                if(response.type == 0){
                    $('#main-body').html(response.message);
                    return false;
                }

                $('#_list-complaints').empty();
                if(response.items.length > 0){
                    $.each(response.items, function(idx, item){
                        $('#_list-complaints').append(complaint_seller_row_template(item));
                    });

                    var html = "";
                        html += '<div class="module-float line-border">';
                            html += '<div class="item-border"></div>';
                            html += '<span class="uppercase font-gray">Chưa tới hạn xử lý</span>';
                        html += '</div>';
                    if(response.condition.status == 'PROCESSING' && $('._complaint-seller-row.delay:first').length > 0){
                        $('._complaint-seller-row.delay:first').before(html);
                    }

                }else{
                    $('#_list-complaints').html('<h3 class="text-center">Không tìm thấy khiếu nại người bán nào.</h3>');
                }

                $('#_total-status').html(response.total_record);

                ComplaintSeller.showTotalStatus(response);

                $('#_show-paging').html(ComplaintSeller.createPaging(response.total_page, response.current_page));

                //paging
                $('._paging').click(function(e){
                    console.log('paging');
                    var e = $(e.currentTarget);
                    if(!e.hasClass('clicked')){
                        var page = e.data('page');
                        $('#_current-page').val(page);
                        e.addClass('clicked');
                        ComplaintSeller.complaintSellerFilter();
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
                        ComplaintSeller.complaintSellerFilter();
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
                        ComplaintSeller.complaintSellerFilter();
//                        $('body').scrollTo($('._total_filter'));
                    }
                });
            }
        });
    },

    showTotalStatus: function(response){
//        if(response.total_by_status.all > 0){
//            $('._filter-status:eq(0)').find('._total-status').show().html('(' + response.total_by_status.all + ')');
//        }else{
//            $('._filter-status:eq(0)').find('._total-status').hide();
//        }

        if(response.total_by_status.pendding > 0){
            $('._filter-status:eq(1)').find('._total-status').show().html('(' + response.total_by_status.pendding + ')');
        }else{
            $('._filter-status:eq(1)').find('._total-status').hide();
        }

        if(response.total_by_status.processing > 0){
            $('._filter-status:eq(2)').find('._total-status').show().html('(' + response.total_by_status.processing + ')');
        }else{
            $('._filter-status:eq(2)').find('._total-status').hide();
        }

//        if(response.total_by_status.success > 0){
//            $('._filter-status:eq(3)').find('._total-status').show().html('(' + response.total_by_status.success + ')');
//        }else{
//            $('._filter-status:eq(3)').find('._total-status').hide();
//        }
//
//        if(response.total_by_status.failure > 0){
//            $('._filter-status:eq(4)').find('._total-status').show().html('(' + response.total_by_status.failure + ')');
//        }else{
//            $('._filter-status:eq(4)').find('._total-status').hide();
//        }
    },

    complaintSellerFilter : function(type){
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = ListBackendComplaintSellerUrl+'?'+search_data;
            ComplaintSeller.push_state(pageUrl);
        }
        ComplaintSeller.getList(search_data);
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    }
};