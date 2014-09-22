var page = 1;
var ajax_rq = null;
var item_row_tpl;
var level_tpl;

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
});
$(window).on('load', function () {
    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
});

$(document).ajaxComplete(function(){


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

    item_row_tpl = Handlebars.compile($("#_item-row-view-tpl").html());
    level_tpl = Handlebars.compile($("#_level-tpl").html());

    Level.getInfoLevel();
    AccumulationScore.filter('');
});

var AccumulationScore = {
    getList: function(search_data) {
        $.ajax({
            url:  linkGetListAccumulationScore,
            type: "GET",
            data: search_data,
            success: function (response) {
                if( response.type == 1 ){
                    if( response.data.total > 0 ) {
                        $('#_list').html('');

                        $.each(response.data.items, function(idx, item) {
                            $('#_list').append(item_row_tpl(item));
                        });

                        $('#_show-paging').html(AccumulationScore.createPaging(response.data.total_page, response.data.current_page));

                        var $page = $('input[name="page"]');

                        //paging
                        $('._paging').click(function(e){
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var page = e.data('page');
                                $page.val(page);
                                e.addClass('clicked');
                                AccumulationScore.filter();
                            }
                        });

                        $('#_page-prev').click(function(e){
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var current_page = parseInt($page.val());
                                current_page--;
                                $page.val(current_page);
                                e.addClass('clicked');
                                AccumulationScore.filter();
                            }
                        });

                        $('#_page-next').click(function(e){
                            var e = $(e.currentTarget);
                            if(!e.hasClass('clicked')){
                                var current_page = parseInt($page.val());
                                current_page++;
                                $page.val(current_page);
                                e.addClass('clicked');
                                AccumulationScore.filter();
                            }
                        });
                    } else {
                        $('#_list').html('<div style="text-align: center; padding: 5px 0;">Hiện chưa có bản ghi nào.</div>');
                    }
                }else{
                    Common.BSAlert(response.message);
                }
            },
            error: function() {}
        });
    },

    filter : function(type){
        var search_data = $('#_search').serialize();
        if(type == null){
            var pageUrl = ListAccumulationScoreUrl+'?'+search_data;
            AccumulationScore.push_state(pageUrl);
        }

        $('#_list').html('<div class="text-center" style="padding: 5px 0;"><img src="../assets/img/small/loading31.gif"></div>');
        AccumulationScore.getList(search_data);
    },

    push_state:function(pageurl){
        if(pageurl!=window.location){
            window.history.pushState({path:pageurl},'',pageurl);
        }
    },

    createPaging: function(total_page, current_page){
        var html = '';
        if(total_page > 1){
            var j = 5;
            html += '<div class="pagination-page">';
            html += '<ul class="pagination">';
            if(current_page > 1){
                html += '<li class="_li_page"><a id="_page-prev">&lt;</a></li>';
            }

            for(var i = j; i > 0; i--){
                if(current_page - i > 0){
                    html += '<li class="_li_page">';
                    html += '<a class="_paging" data-page="' + ( current_page - i ) + '">' + ( current_page - i ) + '</a>';
                    html += '</li>';
                }
            }

            html += '<li class="active"><a>' + current_page + '</a></li>';

            for(var i = 1; i <= j; i++){
                if(current_page + i <= total_page){
                    html += '<li class="_li_page">';
                    html += '<a class="_paging" data-page="' + ( current_page + i ) + '">' + ( current_page + i ) + '</a>';
                    html += '</li>';
                }
            }

            if(current_page < total_page){
                html += '<li class="next"><a id="_page-next">&gt;</a></li>';
            }
            html += '</ul>';
            html += '</div>';
        }
        return html;
    }
};

var Level = {
    getInfoLevel: function() {
        $.ajax({
            url:  linkGetInfoLevel,
            type: "GET",
            data: {  },
            success: function (response) {
                if( response.type == 1 ){
                    $('#_level').html(level_tpl(response.data));

                    //format money
                    $('._money-amount').moneyFormat({
                        positiveClass : 'font-black',
                        negativeClass : 'font-red',
                        signal : false,
                        useZeroNumber: true
                    });
                }else{
                    Common.BSAlert(response.message);
                }
            },
            error: function() {}
        });
    }
};

