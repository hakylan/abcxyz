$(document).ready(function(){
    $(function() {
        $(".numbers-row").append('<div class="dec button">-</div><input class="quantity" type="text" value="3" name="french-hens"><div class="inc button">+</div>');
        $(".button").on("click", function() {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            if ($button.text() == "+") {
                var newVal = parseFloat(oldValue) + 1;
            } else {
            // Don't allow decrementing below zero
                if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 0;
                }
            }
            $button.parent().find("input").val(newVal);
            });
        });
        
        //    bôi đen
        $(".quantity").click(function () {
           $(this).select();
        }); 

      
}); 

