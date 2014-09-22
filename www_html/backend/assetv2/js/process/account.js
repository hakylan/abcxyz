$(document).ready(function(){

   $(document).on('click','._create_account',function(event){
       event.preventDefault();
       var result = account.create();
       //if(result)
       alert(' Tạo tài khoản thành công !');
       location.href = $("base").data('url')+'/account';
   });

   var account = {
       'create':function (){
           var data = {};

           data.accountType = $('#accountType').val();
           data.service = $('#service').val();
           data.fullName = $('#fullName').val();
           data.userName = $('#userName').val();
           data.status = $('#status').val();

           var message = this.valid(data);
           if(message.length > 0){
               alert(message); return false;
           }

           var url = $('#frm-add-account').attr('action');
           return this.save(data,url);

       },
       'valid':function (account) {

           var message = '';
           if(account.accountType == '' || account.accountType == undefined || account.accountType == -1){
               message+="Chưa chọn loại tài khoản \n";
           }
           if(account.service == '' || account.service == undefined || account.service == -1){
               message+="Chưa chọn loại dịch vụ \n";
           }
           if(account.userName == '' || account.userName == undefined){
               message+="Chưa điền tên tài khoản \n";
           }
           if(account.status == '' || account.status == undefined || account.status == -1){
               message+="Chưa chọn trạng thái tài khoản \n";
           }
           return message;
       },
       'save':function(account,url){
           var result = '';
           $.ajax({
               type: "POST",
               url: url,
               async:false,
               data: account,
               success: function($result){
                   result = $result;
               }
           });
           return result;
       }
   }
});
