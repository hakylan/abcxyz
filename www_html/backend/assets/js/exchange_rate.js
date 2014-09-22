/**
 * Created by Admin on 1/11/14.
 */
$(document).ready(function(){
    $('._addNewExchange').click(function(){
        $('#exchange_rate').focus();
        var data_show = $('._addExchange').attr('data-show');
        if(data_show == 0){
            $('._addExchange').slideDown();
            $('._addExchange').attr('data-show',1);
        }else{
            $('._addExchange').slideUp();
            $('._addExchange').attr('data-show',0);
        }
    });

    $('#closeAdd').click(function(){
        $('._addExchange').slideUp();
        $('._addExchange').attr('data-show',0);
    })
    $('#addNewExchange').click(function(){
        var exchange_rate = $('#exchange_rate').val();

        if(exchange_rate == ''){
            alert("Không được để trống tỷ giá");
            $('#exchange_rate').focus();
            return;
        }

        if(!$.isNumeric(exchange_rate)){
            alert("Tỷ giá phải là kiểu số");
            $('#exchange_rate').focus();
            return;
        }
        $.ajax({
            url:link_add,
            type:'post',
            data:{ exchange_rate:exchange_rate },
            success:function(result) {
                var data = $.parseJSON(result);
                if(data.type == 1){
                    $('#exchange_rate').val('');
                    $('._addExchange').slideUp();
                    var tr = '<tr class="odd _tr'+data.id+'">' +
                        '<td class="sorting_1">' + data.id +
                        '</td>' +
                        '<td class="center">1</td> <td class="center"> ' + exchange_rate +
                        '</td>' +
                        ' <td class="center "> </td>' + data.time+
                        '<td class="center ">' + data.username +
                        '</td></tr>';
                    $('#_exchangeRate').prepend(tr);
                    $('._tr'+data.id).slideDown();
                }else{
                    alert(data.message);
                    $('#exchange_rate').focus();
                    return;
                }
            }
        });
    })
})
