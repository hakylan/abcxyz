<script type="text/javascript">
    <?php
        include 'library/common.js';
        include 'library/tmall.js';
        include 'library/taobao.js';
        include 'library/alibaba.js';
    ?>
    $(document).ready(function(){
        GlobalTool.getExchangeRate();

        var str = window.location.href;
        if(!(str.match(/item.taobao/) || str.match(/detail.tmall/) || str.match(/detail.1688/)
            || str.match(/tmall.com\/item/) || str.match(/taobao.com\/item/))){
            console.log("false");
            return;
        }
        console.log("true");
        var detected = '';
        if(str.match(/1688.com/)) {
            detected = 'alibaba';
        }else if(str.match(/taobao.com/)){
            detected = 'taobao';
        }else if(str.match(/tmall.com/)){
            detected = 'tmall';
        }

        var object = new factory(detected);

        object.init();

        //onclick button add to cart
        $(document).on('click','#book-to-seudo', function () {
            object.add_to_cart();
        });

        $(document).on('click','#seudo-cart', function () {
            location.href = this.common.cart_url;
        });
    });
</script>

