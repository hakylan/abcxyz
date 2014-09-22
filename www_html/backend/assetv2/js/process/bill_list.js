/**
 * Created by Admin on 6/13/14.
 */
$(document).ready(function(){
    $(document).on('click','._btn_search',function(){
        BillList.SearchBill();
    });

    $(document).on('keyup','._domestic_code',function(e){
        if(e.keyCode == 13){
            BillList.SearchBill();
        }
    });

    $(document).on('change','._select_location',function(e){
        BillList.SearchBill();
    });

    $(document).on('change','._warehouse',function(e){
        BillList.SearchBill();
    });

    $(document).on('keyup','#start_date',function(e){
        if(e.keyCode == 13){
            BillList.SearchBill();
        }
    });

    $(document).on('keyup','#end_date',function(e){
        if(e.keyCode == 13){
            BillList.SearchBill();
        }
    });

    $(document).on('keyup','._user',function(e){
        if(e.keyCode == 13){
            BillList.SearchBill();
        }
    });

    $(document).on('keyup','._order_code',function(e){
        if(e.keyCode == 13){
            BillList.SearchBill();
        }
    });

    $(document).on('click','._cod',function(e){
        BillList.SearchBill();
    });

    $(document).on('click','._domestic_shipping',function(e){
        BillList.SearchBill();
    });
    $(document).on('click','._page_bill',function(e){
        $('._page').val($(this).attr("data-page"));
        BillList.SearchBill();
    });

    BillList.SearchBill();
});

var BillList = {
    SearchBill : function(){
        var data = $('#_frm_search').serialize();
        var url = BillManage+"?"+data;
        Common.push_state(url);
        $.ajax({
            url : SearchBill,
            type : "GET",
            data : data,
            success : function(data){
                $('._main_content').html(bill_template(data));
            }
        })
    }
};
