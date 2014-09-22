$(document).ready(function () {

    if ($('.article').length) {

        $('.article').readmore({
            speed: 75,
            maxHeight: 300,
            moreLink: '<a class="click-block" href="#">Xem thêm</a>',
            lessLink: '<a class="click-block" href="#">Đóng</a>'
        });
    }
    if($('.show_data').length){
        $('html').click(function() {
            $('.rate_data').hide();
        });
        $('.show_data').click(function(e){
            e.stopPropagation();
            $('.rate_data').toggle();
            console.log('click');
        });
    }
    $('div.rv_only').raty({
        path: BASE_URL + '/assets/img/raty',
        readOnly: true,
        score: function () {
            return $(this).attr('data-score');
        } });

    $('div.rate').raty({
        path: BASE_URL + '/assets/img/raty',
        readOnly: false,
        score: function () {
            return $(this).attr('data-score');
        },
        click: function (score, evt) {
            $.post(BASE_URL + '/community/rate', {'p': score, 'pid': $(this).data('id')}, function (r) {
                $.notify(r.msg,{globalPosition:'top center'});
            }, 'json');
        }
    });
});