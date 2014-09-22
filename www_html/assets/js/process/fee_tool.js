$(document).ready(function(){
    //var calcFeeTool = $("#calcFeeTool");
    $(document).on('click','.totalAmount',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('keyup','.totalAmount',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('click','.normalItemCount',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('keyup','.normalItemCount',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('click','.targetCode',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('keyup','.targetCode',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('click','.totalWeight',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });

    $(document).on('keyup','.totalWeight',function() {
        if($(this).hasClass('inputBorderRed')){
            $(this).removeClass('inputBorderRed');
        }
    });



    $(document).on('click','.calcBtn',function () {

        var data = {};
        var totalAmount = $('.totalAmount'),
            normalItemCount = $('.normalItemCount'),
            targetCode = $('.targetCode'),
            totalWeight = $('.totalWeight');


        totalAmount.removeClass('inputBorderRed');
        normalItemCount.removeClass('inputBorderRed');
        targetCode.removeClass('inputBorderRed');
        totalWeight.removeClass('inputBorderRed');

        var error_box = $('.error_box');
        error_box.html('');

        if(totalAmount.val() == 0 || totalAmount.val() == undefined) {
            totalAmount.addClass('inputBorderRed');
            error_box.html('Chưa nhập tổng tiền hàng !'); return false;
        }
        var totalAmountVal = totalAmount.val().toLowerCase();
        if(totalAmountVal.indexOf('ndt') < 0 && totalAmountVal.indexOf('vnd') < 0) {

            totalAmount.val(parseInt(totalAmount.val()) + ' ndt');
        }

        data.totalAmount = totalAmount.val();

        data.normalItemCount = normalItemCount.val();
        data.accessItemCount = $('.accessItemCount').val();
        data.targetCode = targetCode.val();
        data.totalWeight = totalWeight.val();
        data.userRank = $('.rank').val();


        if( (data.normalItemCount == 0 || data.normalItemCount == undefined)
            && (data.accessItemCount == 0 || data.accessItemCount == undefined) ) {
            normalItemCount.addClass('inputBorderRed');
            error_box.html('Chưa nhập số lượng sản phẩm !'); return false;
        }

        if(data.targetCode == '' || data.targetCode == 0 || data.targetCode == undefined || data.targetCode == -1) {
            targetCode.addClass('inputBorderRed');

            error_box.html('Chưa chọn tỉnh, thành phố nhận hàng !'); return false;
        }

        if(data.totalWeight == '' || data.totalWeight == 0 || data.totalWeight == undefined) {
            totalWeight.addClass('inputBorderRed');

            error_box.html('Chưa nhập khối lượng đơn hàng !'); return false;
        }


        var services = {};
        var i = 1;
        $('.services').each(function () {
            if($(this).is(':checked')) {
                i++;
                services[i] = $(this).val();
            }
        });


        data.services = services;


        $.ajax({
            url: base_url+'/service/calc',
            type: 'POST',
            data: { data : data },
            success: function(result) {


                if(result.type == 0) {
                    error_box.html(result.message);
                    return false;
                } else {

                    var data = result.data;

                    var shippingFeeDetail = data.shippingFeeDetail;

                    if(data.buyingFee != undefined){
                        $('#buying_fee').html(number_format(data.buyingFee)+"<sup>đ</sup>");
                        $('#buying_fee_discount').html(number_format(data.buyingFeeDiscountPercent));
                    }

                    if(data.checkingFee != undefined){
                        $('#checking_fee').html(number_format(data.checkingFee)+"<sup>đ</sup>");
                        $('#checking_fee_discount').html(number_format(data.checkingFeeDiscountPercent));
                    }

                    if(shippingFeeDetail.inlandChina != undefined){
                        $('#inlandChina').html(number_format(shippingFeeDetail.inlandChina.avg)+"<sup>đ</sup>");
                        $('#inlandChinaFromTo').html(number_format(shippingFeeDetail.inlandChina.from)+"<sup>đ</sup>"+"-"+number_format(shippingFeeDetail.inlandChina.to)+"<sup>đ</sup>");
                    }


                    if(shippingFeeDetail.chinaVietnam != undefined){
                        $('#chinaVietnam').html(number_format(parseInt(shippingFeeDetail.chinaVietnam)+parseInt(data.packingFee))+"<sup>đ</sup>");
                        $('#chinaVietnamDiscount').html(shippingFeeDetail.chinaVietnamDiscountPercent);
                    }


                    if(shippingFeeDetail.inlandVietnam != undefined) {
                        $('#inlandVietnam').html(number_format(shippingFeeDetail.inlandVietnam)+"<sup>đ</sup>");
                    }

                    if(data.totalServiceFee != undefined){
                        $('#totalServiceFee').html(number_format(data.totalServiceFee)+"<sup>đ</sup>");
                        if(data.totalServiceFee != data.totalServiceFeeOrigin){
                            $('#totalServiceFeeOrigin').html(number_format(data.totalServiceFeeOrigin)+"<sup>đ</sup>");
                        }
                        $('#totalServiceFeeFromTo').html(number_format(data.totalServiceFeeFrom)+"<sup>đ</sup>"+"-"+number_format(data.totalServiceFeeTo)+"<sup>đ</sup>");
                    }

                    console.log(result);
                }
            }
        });

    });
});
