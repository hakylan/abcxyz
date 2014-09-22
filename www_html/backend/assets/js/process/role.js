$(document).ready(function() {
    $("form[name='add-role-frm']").submit(function (event) {
        var e = $(this), btn = $("#_save-role-btn");
        if (btn.hasClass('disabled')) {
            return;
        }

        btn.addClass('disabled');
        event.preventDefault();
        $.post(add_role_url, e.serialize(), function(response) {
            btn.removeClass('disabled');
            if (response.type) {
                if (response.detail_link) {
                    window.location.href = response.detail_link;
                } else {
                    location.reload();
                }
            } else {
                var error = response.error;

                //display eror
                for (var x in error) {
                    $("#" +x)
                        .addClass('parsley-error')
                        .parent()
                        .append('<span class="parsley-error-list">' + error[x] +'</span>');
                }

                $("#_role-form-error-placeholder").html('<p>' +response.message +'</p>').removeClass('hidden');
            }
        });
    })

    $("#_remove-row").click(function () {
        var e = $(this);
        if (e.hasClass('disabled')) {
            return;
        }

        e.addClass('disabled');
        $.post(remove_role_link, {'id' : e.data('role-id')}, function (response) {
            if (response.type) {
                $('#role-row-' + e.data('role-id')).fadeOut();
            } else {
                //error here
            }
        });
    });

    $("form[name='save-role']").submit(function (event) {
        var e = $(this), btn = $();
        event.preventDefault();

        if (btn.hasClass("disabled")) {
            return;
        }

        btn.addClass("disbaled");
        $.post(edit_role_url, e.serialize(), function(response) {
            if (response.type == 1) {
                role = response.role;
                $("#_role-detail-placeholder").html(role_template({"role": role}));

                //remove display error
                e.find('input').removeClass('parsley-error').find('span').remove();
                e.find('text').removeClass('parsley-error').find('span').remove();
                $("#_role-form-error-placeholder").addClass('hidden');

                bind_remove_confirm();
            } else {
                var error = response.error;

                //display eror
                for (var x in error) {
                    $("#" +x)
                        .addClass('parsley-error')
                        .parent()
                        .append('<span class="parsley-error-list">' + error[x] +'</span>');
                }

                $("#_role-form-error-placeholder").html('<p>' +response.message +'</p>').removeClass('hidden');
            }

            btn.removeClass("disbaled");
        });
    });

    $(document).on('click', 'a._add-member', function(event) {
        var e = $(this);
        $.post(add_member_url,
            {
                'user_id' : e.data('user-id'),
                'role_id' : e.data('role-id')
            },
            function (response) {
                if (response.type) {
                    if (members.length == 0) {
                        members = {};
                    }
                    var member = otherGuys[response.user.id];
                    members[response.user.id] = member;
                    delete(otherGuys[response.user.id]);

                    $("#_role-member-placeholder").html(members_template({"members": members}));
                    $("#_other-cranes-placeholder").html(other_guys_template({"otherGuys": otherGuys}));
                }
            });
    });

    $(document).on('click', 'a._remove-member', function(event) {
        var e = $(this);
        $.post(remove_member_url,
            {
                'user_id' : e.data('user-id'),
                'role_id' : e.data('role-id')
            },
            function (response) {
                if (response.type) {
                    var member = members[response.user_id];
                    if (otherGuys.length == 0) {
                        otherGuys = {};
                    }
                    otherGuys[response.user_id] = member;
                    delete(members[response.user_id]);

                    $("#_role-member-placeholder").html(members_template({"members": members}));
                    $("#_other-cranes-placeholder").html(other_guys_template({"otherGuys": otherGuys}));
                }
            });
    });

    $(document).on('click', 'a#_save-permission', function(event) {
        var data = $('form[name="permissions-frm"]').serialize();

        $.post(change_permission_url, data, function(response) {
            if (!response.type) {
                //error here
            }
        });
    });

    $("._remove-role").confirm({
        text: "Bạn có chắc xóa nhóm này không?",
        title: "XÁC NHẬN",
        confirm : function() {
            var e = this.button;
            remove_role(remove_role_url, e.data('role-id'), function(response) {
                if (response.type == 1) {
                    $("tr#role-row-" + e.data('role-id')).fadeOut();
                } else {
                    $('#_role-table-error-placeholder').removeClass('hidden').find('p').html(response.message);
                }
            });
        },
        confirmButton: "Có",
        cancelButton: "Không",
        post: true
    });
});

function bind_remove_confirm() {
    $('#_remove-role').confirm({
        text: "Bạn có chắc xóa nhóm này không?",
        title: "XÁC NHẬN",
        confirm : function() {
            //remove_role_url
            remove_role(remove_role_url, $('#_remove-role').data('role-id'), function(response) {
                if (response.type == 1) {
                    window.location.href = list_all_role_url;
                } else {
                    $('#_role-form-error-placeholder').removeClass('hidden').html(response.message);
                }
            })
        },
        confirmButton: "Có",
        cancelButton: "Không",
        post: true
    });
}

function remove_role(url, role_id, callback) {
    $.post(url, {id : role_id}, function(response) {
        callback(response);
    });
}