
function search_user(push_state) {
    var formData = $('#_search-user-frm').serialize(),
        page_url = user_page+'?'+formData;

    if(push_state && page_url != window.location){
        window.history.pushState({'path' : page_url}, '', page_url);
    }

    $.get(search_user_url,
        formData,
        function(response) {
            $("#_user-list-container").html(users_template(response));
            if (response.type) {
                $("#_result-paragraph").html(response.total);
            }

            pagination(response.page_size , response.total, response.page);

            $('._money-amount').moneyFormat({
                positiveClass : 'font-blue',
                negativeClass : 'font-red'
            })
    });
}

function pagination(item_per_page, total, current_page) {
    if (!current_page) {
        current_page = 1;
    }

    var no_page = Math.ceil(total/item_per_page),
        html = '',
        disable_class = '';
    for (var i = 1; i <= no_page; ++i) {
        disable_class = (i == current_page)? 'disabled' : '';
        html += '<a href="javascript:;" class="btn large ' + disable_class +' primary-bg" data-page="' +i +'">' +i +'</a> ';
    }

    $("#_user-pagination").html(html);
}

/** JS FOR DETAIL PAGE */

function user_detail_page_ini() {
    init_add_mobile_form();
}

function init_add_mobile_form() {
    $(document).ready(function() {
        if ($('#_user-mobile-list > ul > li').length < 3) {
            $('#_add-user-mobile').show();
        } else {
            $('#_add-user-mobile').hide();
        }
    });
}

/** end js for detail page */

function user_form_display_error(error) {
    $(document).ready(function () {
        if (error) {
            for (var key in error) {
                $("#" + key.replace(/\./g, "-")).addClass('parsley-error').parent().append('<span class="parsley-error-list col-lg-12 row">'+ error[key] +'</span>');
            }
        }
    });
}

$( document ).ready(function() {
    $('#_user-pagination').on('click', 'a', function() {
        var e = $(this);
        $('input[name="page"]').val(e.data('page'));

        $('#_user-pagination a.disabled').removeClass('disabled');
        e.addClass('disabled');

        search_user(true);
    });

    $("a._prefix-select").click(function() {
        var e = $(this);

        $('input[name="page"]').val(e.data("1"));

        $('input[name="pre"]').val(e.data("value"));

        $('a._prefix-select.primary-bg').addClass('ui-state-default').removeClass('primary-bg');
        e.addClass('primary-bg').removeClass('ui-state-default');

        search_user(true);
    });

    $('#_search-user-frm').keyup(function (event) {
        $('input[name="pre"]').val('');
        if(event.keyCode == 13) {
            search_user(true);
        }
    });

    $("#_search-user-frm select").change(function (event) {
        search_user(true);
    });


    /** JS FOR DETAIL PAGE */
    var mobileForm = $("input#_add-user-mobile-input");
    if (mobileForm) {
        mobileForm.on('keypress', function() {
            delay(function() {
                var mobile = mobileForm.val();
                console.log(mobile);
                if (mobile.length > 8) {
                    $('#_add-user-mobile-btn').removeClass('disabled');
                } else {
                    $('#_add-user-mobile-btn').addClass('disabled');
                }
            }, 300);
        });

        $('#_add-user-mobile-btn').click(function() {
            var e = $(this);
            if (e.hasClass('disabled')) {
                return;
            }

            e.addClass('disabled');
            mobileForm.removeClass('parsley-error');
            $.post(add_mobile_url, {
                'mobile' : mobileForm.val(),
                'user_id' : mobileForm.data('user-id')
            }, function (response) {
                if (response.type) {
                    mobiles[response.mobile[id]] = response.mobile;
                    $("#_user-mobile-list").html(add_mobile_template(mobiles));
                    mobileForm.val('');
                    init_add_mobile_form();
                } else {
                    mobileForm.addClass('parsley-error');
                    e.removeClass('disabled');
                    $("#_add-mobile-error > li").html(response.message);
                }
            })
        });
    }

    $("#_user-mobile-list").on('click', '._remove-user-mobile-btn', function() {
        var id = $(this).data('user-mobile');
        $.post(remove_mobile_url, {id : id}, function(res) {
            delete(mobiles[id]);
            $("#_user-mobile-list").html(add_mobile_template(mobiles));
            init_add_mobile_form();
        });
    });
    /** end js for detail page */
});