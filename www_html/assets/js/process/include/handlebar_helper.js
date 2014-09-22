/**
 * Created by Admin on 4/21/14.
 */
Handlebars.registerHelper("formatKCurrency", function(value,rouding) {

    if(!$.isNumeric(value)){
        return value;
    }

    var p = "";
    if(parseInt(value) > 0){
        p = "+";
    }
    var number_round = Global.roundToTwo((parseFloat(value) / 1000));
    number_round = number_round.toString().replace(".00","");
    return p+number_round+"k";

//    return p+value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
});
Handlebars.registerHelper("formatCurrency", function(value) {
    var p = "";
    if(parseInt(value) > 0){
        p = "+";
    }
    return p+value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");

//    return p+value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
});
Handlebars.registerHelper("classPrice", function(value) {
    if(parseInt(value) >= 0){
        var _class = "bold-blue";
    }else{
        var _class = "red-bold";
    }
    return _class;
});
Handlebars.registerHelper("classPriceNormal", function(value) {
    if(parseInt(value) > 0){
        var _class = "normal-blue";
    }else{
        var _class = "red-normal";
    }
    return _class;
});
