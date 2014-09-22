/**
 * Created by hosi on 5/21/14.
 */


/**
 * Created by hosi on 5/21/14.
 */

$(document).ready(function(){
    //fix primary

    var height = $(window).height() - 98;
    var w_primary = $('.box-primary').width();

    console.log('height: ' + height);
    console.log('w_primary: ' + w_primary);

    $('.primary').css({'height': height, 'overflow': 'hidden'});
    $('.sidebar-left').css({'height': height});
    $('.sidebar-right').css({'height': height});

    $('.list-box-primary').slimscroll({
        height: height - 110
    });

    $('.list-box-primary').parent().css('z-index', 9);


    $('.custom-panel-select-box').click(function(){
        $(this).parent().find('.custom-content-select-box').removeClass('hidden');
    });

    $('.custom-content-select-box').mouseleave(function(){
        $(this).addClass('hidden');
    });

    $('.custom-list-group-item li').click(function(e){
        var value = $(this).data('value');
        var target = $(this).parents('.custom-select-box');
        target.find('.title-select-box').html(value);
        target.find('.custom-content-select-box').addClass('hidden');
        $('#type_warehouse').val($(this).data('type'));
        $('._input-barcode').removeAttr('disabled', 'disabled');
        $('._input-barcode').focus();
        $('._input-barcode').addClass('custom-input-active');
    });

    $('.clear-result').click(function(){
        if(confirm('Bạn có chắc chắn?')){
            $('.total-barcode').html('0');
            $('.list-box-primary li.item-box-primary').remove();
        }
    });

    $('._input-barcode').hover(function(){
//        console.log('hover barcode');
//        console.log($(this).hasClass('custom-input-active'));
        if(!$(this).hasClass('custom-input-active')){
            $(this).css('cursor', 'not-allowed');
        }else{
            $(this).css('cursor', '');
        }
    });

    $('._sub-title-bag').click(function(){
        var $this = $(this).parents('.panel-list-bag');
        if($this.find('.list-bag').is(':hidden')){
            $this.find('.list-bag').slideDown();
            $(this).find('i').removeClass('fa-caret-right');
            $(this).find('i').addClass('fa-caret-down');
        }else{
            $this.find('.list-bag').slideUp();
            $(this).find('i').addClass('fa-caret-right');
            $(this).find('i').removeClass('fa-caret-down');
        }
    });

    /*export excel*/
    $('._export_excel').click(function(e) {
        e.preventDefault();
        var string_data = '';
        $('._count_li').each(function () {
            string_data+=$(this).attr('data-code')+',';
        });
        string_data = encodeURI(string_data);
        console.log(string_data);
        var warehouse_activity = $('#type_warehouse').val();
        location.href=export_excel_url+"?need_to_export="+string_data+'&type='+warehouse_activity;
        //window.open('data:application/vnd.ms-excel,' + string_data);
    });
    /* */
    var barcode = $("._input-barcode");
    barcode.keyup(function(event) {
        if(event.keyCode == 13) {
            var that = $(this);
            barcode = that.val().trim().toUpperCase();
            if(barcode==''){
                return false;
            }
            var type = $('#type_warehouse').val();
            var data = {
                barcode:barcode,
                type:type,
                order:$('._count_li').length
            };
            that.val(''); that.focus();
            //fill data before call ajax
            $('.list-box-primary li:first-child .code').removeClass('font-color-ec0423');
            var new_item_barcode_scanning = Handlebars.compile($("#new_item_barcode_scanning").html());
            var website_item_barcode_scanning = Handlebars.compile($("#website_item_barcode_scanning").html());
            var error_web_item_barcode_scanning = Handlebars.compile($("#error_web_item_barcode_scanning").html());

            var render_data ={barcode:barcode};
            $('.list-box-primary').prepend(new_item_barcode_scanning(render_data));
            /* cộng vào chỗ đếm */
            var count = $('._count_li').length;
            $('.total-barcode').html(count);
            $('.list-box-primary li:first-child ').addClass('_li_'+count);
            $('.list-box-primary li:first-child ._div_order_barcode').html(count);
            $('.list-box-primary li:first-child ._warehouse_name').html($('._select_warehouse_name').html());

            $('.list-box-primary').slimscroll({
                height: height - 110,
                width: 313,
                scrollTo: '0px'
            });

            $.ajax({
                url:barcode_url,
                data:data,
                type:'post',
                success:function(response) {
                    /* append data liên quan vào */
                    if(response.type == 1){
                        $('._li_'+count +' ._div_web_time').html(website_item_barcode_scanning(response.data));
                    }else{
                        $('._li_'+count).addClass('border-radius-red');
                        $('._li_'+count +' ._div_web_time').html(error_web_item_barcode_scanning());
                    }

                }
            });
        }
    });

});
