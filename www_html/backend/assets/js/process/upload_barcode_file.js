function active_upload(warehouse) {
    if (!warehouse) {
        warehouse = $("#_chose-warehouse").val();
    }

    var active = warehouse,
        e = $("#barcode_file");

    e.fileupload(active? 'enable' : 'disable');
    if (!active) {
        e.parent().addClass('disabled');
    } else {
        e.parent().removeClass('disabled');
    }
}

jQuery(document).ready(function($) {
    var error_container = $("#_error-container");
    var source   = $("#_upload-result").html();
    var template = Handlebars.compile(source);

    $("#barcode_file").fileupload({
        url: bar_code_upload_file_url,
        dataType: 'json',
        formatData: {
            warehouse : $('select[name="warehouse"]').val(),
            working_date : $('input[name="working_date"]').val(),
            description : $('textarea[name="description"]').val()
        },
        done: function (e, data) {
            if (data.result.type) {
                error_container.addClass("hidden");
                $("#barcode-file-upload").hide();
                var content = data.result.barcode_file.content.split(',');
                $("#_upload-result-container")
                    .html(template({'uploader' : data.result.uploader, 'content' : content}))
                    .fadeIn();
            } else {
                error_container.removeClass("hidden");
                var error = "";
                for (var x in data.result.error) {
                    error += data.result.error[x] + "<br>";
                }

                error = data.result.message +'<br>' + error;
                error_container.html('<p>' +error +'</p>');
            }
        }
    });

    active_upload();

    $("#_chose-warehouse").change(function() {
        active_upload();
    });
});