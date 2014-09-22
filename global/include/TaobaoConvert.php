<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 3/16/14
 * Time: 10:27 PM
 */
class TaobaoConvert{
    public static function convertTaobaoData($result) {
        $promotion = isset($result["promotion"]) ? $result["promotion"] : 0;
        $price = isset($result["price"]) ? $result["price"] : 0;
        $detail_desc = isset($result['detail_desc']) ? $result['detail_desc'] : 0;

        $_skuProps = array();
        if (isset($result['style']) & !empty($result['style'])) {
            $_skuProps[0]['prop'] = '颜色';
            foreach ($result['style'] as $k => $c) {
                $style = isset($c['style_title']) ? $c['style_title'] : "";
                $_skuProps[0]['value'][$k]['name'] = $style;
                if (!empty($c['image_url'])) {
                    $_skuProps[0]['value'][$k]['imageUrl'] = $c['image_url'];
                }
            }
        }
        if (isset($result['size']) & !empty($result['size'])) {
            $_skuProps[1]['prop'] = '适合身高';
            foreach ($result['size'] as $k => $c) {
                $_skuProps[1]['value'][$k]['name'] = isset($c['size_name']) ? $c['size_name'] : '';
            }
        }
        $inventory = 0;
        $_skuMap = array();
        if (isset($result['style']) & !empty($result['style'])) {
            foreach ($result['style'] as $v_c) {
                if (isset($result['size']) & !empty($result['size'])) {
                    foreach ($result['size'] as $v_s) {
                        $key = ';' . $v_c['code'] . ';' . $v_s['code'] . ';';
                        $key_path = $v_c['code'] . ';' . $v_s['code'];
                        $size_name = isset($v_s['size_name']) ? $v_s['size_name'] : '';
                        $style_title = isset($v_c['style_title']) ? $v_c['style_title'] : '';
                        $value = $style_title . '>' . $size_name;
                        if (!isset($result['skuMap']['skuMap'][$key])) {
                            $key = ';' . $v_s['code'] . ';' . $v_c['code'] . ';';
                            $value = $size_name . '>' . $style_title;
                        }
                        $skuId = isset($result['skuMap']['skuMap'][$key]['skuId']) ? $result['skuMap']['skuMap'][$key]['skuId'] : 0;

                        if($skuId == 0){
                            $skuId = isset($result['skuMap']['pathIdMap'][$key_path]) ? $result['skuMap']['pathIdMap'][$key_path] : 0;
                        }
                        //So luong ton kho va skuid
                        $_skuMap[$value] = array(
                            'canBookCount' => isset($result['skuMap']['skuMap'][$key]['stock']) ? $result['skuMap']['skuMap'][$key]['stock'] : 1,
                            'outerId' => $skuId
                        );
                        // Set price for each pattern
                        if(isset($result['skuMap']['skuMap'][$key]['price'])){
                            $_skuMap[$value]['price'] =
                                self::getPriceSkuTaobao($result['skuMap']['skuMap'][$key]['price'],$promotion,$price);
                        }else{
                            $_skuMap[$value]['price'] =
                                self::getPriceSkuTaobao(false,$promotion,$price);
                        }
                    }
                } else {
                    // No size
                    $key = ';' . $v_c['code'] . ';';
                    $key_path = $v_c['code'];
                    $value = $v_c['style_title'];

                    $stock = isset($result['skuMap']['skuMap'][$key]['stock']) ? $result['skuMap']['skuMap'][$key]['stock'] : 0;

                    $skuId = isset($result['skuMap']['skuMap'][$key]['skuId']) ? $result['skuMap']['skuMap'][$key]['skuId'] : 0;

                    if($skuId == 0){
                        $skuId = isset($result['skuMap']['pathIdMap'][$key_path]) ? $result['skuMap']['pathIdMap'][$key_path] : 0;
                    }
                    $_skuMap[$value] = array(
                        'canBookCount' => $stock,
                        'outerId' => $skuId
                    );

                    // Set price for each pattern
                    if(isset($result['skuMap']['skuMap'][$key]['price'])){
                        $_skuMap[$value]['price'] =
                            self::getPriceSkuTaobao($result['skuMap']['skuMap'][$key]['price'],$promotion,$price);
                    }else{
                        $_skuMap[$value]['price'] =
                            self::getPriceSkuTaobao(false,$promotion,$price);
                    }
                }
            }
        }else if (isset($result['size']) & !empty($result['size'])) {
            foreach ($result['size'] as $size) {
                // No size
                $key = ';' . $size['code'] . ';';
                $key_path = $size['code'];
                $value = $size['size_name'];

                $stock = isset($result['skuMap']['skuMap'][$key]['stock']) ? $result['skuMap']['skuMap'][$key]['stock'] : 0;

                $skuId = isset($result['skuMap']['skuMap'][$key]['skuId']) ? $result['skuMap']['skuMap'][$key]['skuId'] : 0;

                if($skuId == 0){
                    $skuId = isset($result['skuMap']['pathIdMap'][$key_path]) ? $result['skuMap']['pathIdMap'][$key_path] : 0;
                }
                $_skuMap[$value] = array(
                    'canBookCount' => $stock,
                    'outerId' => $skuId
                );

                // Set price for each pattern
                if(isset($result['skuMap']['skuMap'][$key]['price'])){
                    $_skuMap[$value]['price'] =
                        self::getPriceSkuTaobao($result['skuMap']['skuMap'][$key]['price'],$promotion,$price);
                }else{
                    $_skuMap[$value]['price'] =
                        self::getPriceSkuTaobao(false,$promotion,$price);
                }
            }
        }else{
            $inventory = isset($result["skuMap"]["inventory"]) ? $result["skuMap"]["inventory"] : 0;
        }

        if($promotion != 0){
            $is_promotion = 1;
            if(preg_match('/-/',$price)){
                $temp_price = explode('-',$price);
            }else{
                $temp_price = $price;
            }
//            if(preg_match('/-/',$promotion)){
//                $temp_price = explode('-',$promotion);
//            }else{
//                $temp_price = $promotion;
//            }
        }else{

            if(preg_match('/-/',$price)){
                $temp_price = explode('-',$price);
            }else{
                $temp_price = $price;
            }
            $is_promotion = 0;

        }

        $shipping = isset($result['shipping']) ? $result['shipping'] : array();

        $shipping = self::convertShippingTaobao($shipping);

        $_price = array(
            'begin' => 1,
            'end' => null,
            'price' => $temp_price
        );
        // -----------------------
        $result = array(
            'id' => $result['id'],
            'origin_url' => $result['origin_url'],
            'config' => array(
                'beginAmount' => 1
            ),
            'purchase' => array(
                'sku' => array(
                    'skuProps' => $_skuProps,
                    'skuMap' => $_skuMap,
                    "invetory" => $inventory
                ),
                'ordering_step_number' => 1,
                'invetory' => 1
            ),
            'carriage' => array('beginAmount' => 1),
            'price' => array($_price),
            'title' => $result['title'],
            'images' => $result['images'],
            'proxy' => '',
            'wangwang' => @$result['wangwang'],
            'supplier' => $result['supplier'],
            'transfer_calculator' => array( // Neu tinh duoc gia van chuyen noi dia
                'costs' => array(
                    array(
                        'cost' => 0
                    )
                )
            ),
            'detail_desc' => $detail_desc,
            'promotion' => $promotion,
            'price_origin' => $price,
            'is_promotion' => $is_promotion,
            'shipping' => $shipping
        );

//        print_r('<pre>');
//        print_r($result);
//        print_r('</pre>');
//        exit();


        /*echo '<style type="text/css">body{background:#FFF;}</style><pre>';
        print_r($result); die();*/
        return $result;
    }

    /**
     * Get Price Skumap Taobao
     * @param $price_sku
     * @param $promotion
     * @param $price
     * @return mixed
     */
    private static function getPriceSkuTaobao($price_sku,$promotion,$price){
        if ($price_sku) {
            if($promotion != 0 && !preg_match('/-/',$promotion)){
                $price_result = $promotion;
            }else{
                $price_result = $price_sku;
            }
        }else{
            if($promotion != 0){
                if(preg_match('/-/',$promotion)){
                    $pro = explode('-',$promotion);
                    if(isset($pro[1])){
                        $price_result = $pro[1];
                    }else{
                        $price_result = $pro[0];
                    }
                }else{
                    $price_result = $promotion;
                }
            }else{
                if(preg_match('/-/',$price)){
                    $pri = explode('-',$price);
                    if(isset($pri[1])){
                        $price_result = $pri[1];
                    }else{
                        $price_result = $pri[0];
                    }
                }else{
                    $price_result = $price;
                }
            }
        }
        return $price_result;
    }

    /**
     * @param $shipping
     * @return array
     */
    private static function convertShippingTaobao($shipping){
        $shipping_result = array();
        if($shipping){
            foreach ($shipping as $key=>$ship) {
                $key = trim($key);
                switch($key){
                    case "is_free":
                        $shipping_result['is_free'] = $ship;
                        break;
                    case "快递":
                        $shipping_result["CPN"] = array(
                            "text" => "CPN",
                            "ship" => $ship
                        );
                        break;
                    case "EMS":
                        $shipping_result["EMS"] = array(
                            "text" => "EMS",
                            "ship" => $ship
                        );
                        break;
                    case "平邮":
                        $shipping_result["BD"] = array(
                            "text" => "Chuyển hàng qua bưu điện",
                            "ship" => $ship
                        );
                        break;
                }
            }
        }
        return $shipping_result;
    }

    /**
     * @param $urlDetail
     * @return array
     */
    public static function getCrawlInfo($urlDetail){
        $file_name = '';
        $parse_url = parse_url($urlDetail);
        if (preg_match('/taobao.com/', $urlDetail)) {
            $website = 'taobao';
        } else {
            $website = 'tmall';
        }
        // Get id taobao
        $params = array();
        if(isset($parse_url['query']) && $parse_url['query'] != ''){
            $params = preg_split('/&/', $parse_url['query']);
        }else{
            if(preg_match("/a.m.t/",$urlDetail)){
                $params = explode('com/i',$urlDetail);

                if(isset($params[1])){
                    $params = str_replace(".htm","",$params[1]);
                    $params = array("id={$params}");
                }else{
                    $params = array("id=0");
                }
            }
        }

        $item_id = 0;
        $type = $website;
        if ($params) {
            foreach ($params as $param) {
                if (preg_match('/id=/', $param)) {
                    $item_id = preg_replace('/id=/', '', $param);

                    break;
                }
            }
        }

        $data = array('item_id' => $item_id,
            "type" => $type
        );

        return $data;
    }

}