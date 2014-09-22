/**
 * Created by Admin on 2/15/14.
 */
$(function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker2" ).datepicker();
});
$(window).on('load', function () {

    $('.selectpicker').selectpicker({
        'selectedText': 'cat'
    });
//            $('.fileinput').fileinput();

    // $('.selectpicker').selectpicker('hide');
});