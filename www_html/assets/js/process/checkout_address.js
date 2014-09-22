/**
 * Created by Admin on 1/15/14.
 */

$(document).ready(function(){

    $.ajaxSetup({
        beforeSend:function(){
            $('._loading').show();
            //$('._send_cart').addClass('load-wait');
        },
        complete:function(){
            $('._loading').hide();
            //$('._send_cart').removeClass('load-wait').html('Tiếp tục<span class="arow-next"></span>');
        }
    });

    $('#load_address').loadingbar({
        done : function(data){
            $('._checkoutAddress').html(data);
            $('._checkoutAddress').slideDown("slow");
        }
    });

    $('#load_district').loadingbar({
        done : function(data){
            var address_id = $('#load_district').attr('data-address-id');
            $('#select_district'+address_id).removeAttr('disabled');
            $('#select_district'+address_id).html(data);
        }
    });

    Address.loadAddress();

    var province_id = $('#select_province0').val();
    Address.selectProvince(province_id);

    $(document).on('click','._accept_address',function(){
        var address_id = $(this).attr('data-address-id');
        Address.selectAddress(address_id);
    });

    $(document).on('change','._select_province',function(){
        var province_id = $(this).val();
        var address_id = $(this).attr('data-address-id');
        $('#load_district').attr('data-address-id',address_id);
        Address.selectProvince(province_id);
    });

    $(document).on('click','._save_address',function(){
        var address_id = $(this).attr('data-address-id');
        var data = {};
        var name = $('#contact_name'+address_id).val();
        var phone = $('#contact_phone'+address_id).val();
        var province = $('#select_province'+address_id).val();
        var district = $('#select_district'+address_id).val();
        var home = $('#home_number'+address_id).val();
        var note = $('#note'+address_id).val();
        $('p.note').hide();
        if(name == ''){
            $('._error_name').text("Bạn chưa điền tên người nhận");
            $('._error_name').fadeIn();
            $('#contact_name'+address_id).focus();
            return;
        }
        if(phone == ''){
            $('._error_phone').text("Bạn chưa điền số điện thoại");
            $('._error_phone').fadeIn();
            $('#contact_phone'+address_id).focus();
            return;
        }
        if(isNaN(phone) || phone.length < 9){
            $('._error_phone').text("Số điện thoại chưa chính xác");
            $('._error_phone').fadeIn();
            $('#contact_phone'+address_id).focus();
            return;
        }
        if(province == -1){
            $('._error_province').text("Bạn chưa chọn Tỉnh / Thành phố");
            $('._error_province').fadeIn();
            return;
        }

        if(district == -1){
            $('._error_district').text("Bạn chưa chọn Quận / Huyện");
            $('._error_district').fadeIn();
            return;
        }

        if(home == ''){
            $('._error_home').text("Bạn chưa điền số nhà");
            $('._error_home').fadeIn();
            $('#home_number'+address_id).focus();
            return;
        }

        data = {id:address_id,name:name,phone:phone,province:province,district:district,home:home,note:note};

        $(this).val('Đang xử lý...');
        $(this).attr("disabled");
        Address.saveAddress(data);
    })

    $(document).on('click','._delete_address',function(){
        var address_id = $(this).attr('data-address-id');
        $.ajax({
            url : checkout_address + "/delete_address",
            type : "POST",
            data : {
                address_id : address_id
            },
            success : function(data){
                $('body').removeClass('modal-open');
                $('div.modal-backdrop').remove();
                $('._li_address[data-address-id='+address_id+']').slideUp("slow");
//                    $('._li_address[data-address-id='+address_id+']').remove()
            }
        });
    });
});
var Address = {
    loadAddress : function(){
        var data_user = $('#load_address').attr('data-user');
        if(data_user == 1){
            $('#load_address').attr('data-href',checkout_address+"/load_address?is_user=1");
        }else{
            $('#load_address').attr('data-href',checkout_address+"/load_address");
        }
        $('#load_address').click();
    },

    saveAddress : function(data){

        var address_id = data.id;

        data = JSON.stringify(data);

        $.ajax({
            url: checkout_address+"/saveAddress",
            type: "POST",
            data : {
                data : data
            },
            success : function(result){
                $('p.note').fadeOut();
                if(result.type == 0){
                    if(result.element){
                        $("."+result.element+"[data-address-id]="+address_id+"").text(result.message);
                        $("."+result.element+"[data-address-id]="+address_id+"").fadeIn();
                    }else{
                        Global.sAlert(result.message); //alert(result.message);
                    }
                }else{
                    $('._text').val('');
//                    if(address_id == 0){
//                        $('#myModaladdadress').removeClass('in');
//                    }
//                    $('body').removeClass('modal-open');
//                    $('.modal-backdrop').remove();
                    $('._loading').hide();

                    $('._cancel_popup[data-address-id='+address_id+']').click();
                    var modal_box = $('.modal-backdrop');
                    if(modal_box != null && (typeof modal_box === 'object' && modal_box.length > 0)){
                        modal_box.remove();
                    }
                    
                    Address.loadAddress();
                }
            }
        })
    },

    selectAddress : function(address_id){
        $.ajax({
            url: checkout_address+"/select_address",
            type: "POST",
            data : {
                address_id : address_id
            },
            success : function(result){

                //var data = $.parseJSON(result);
                if(result.type == 0){
                    alert(data.message);
                    return;
                }else{
                    $('._li_address').removeClass('active');
                    $('._select_address').css("display","");
                    $('._li_address[data-address-id='+address_id+']').addClass('active');
                    $('._select_address[data-address-id='+address_id+']').css("display","none");
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
//                    $('#selectAddress'+address_id).removeClass('in');
//                    $('#selectAddress'+address_id).css('display','none');
//                    $('#selectAddress'+address_id).attr('aria-hidden','true');
                }

            }
        })
    },

    selectProvince : function(province_id){
        $('#load_district').attr('data-href',checkout_address+"/select_city?id="+province_id);
        $('#load_district').click();
    },

    deleteAddress : function(){

    }
}
