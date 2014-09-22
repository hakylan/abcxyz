$(document).ready(function(){
    $(document).on('click','.delete',function(e){
        e.preventDefault();
        $href = $(this).data('href');
        if(confirm('Có chắc bạn muốn xóa ?')){
            window.location.href=$href;
        }
    });
});