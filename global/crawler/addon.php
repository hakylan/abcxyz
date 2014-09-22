<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12/26/13
 * Time: 2:25 PM
 */
$arr = array(
    "http://item.tmall.com",
    "http://detail.tmall.com",
    "http://item.taobao.com",
    "http://item.lp.taobao.com",
    "http://item.beta.taobao.com",
    "http://auction.taobao.com",
    "http://detailp4p.china.alibaba.com",
    "http://detail.china.alibaba.com",
    "http://detail.1688.com",
    "http://auction1.paipai.com"
);

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $arr)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: X-PINGARUNER');
    header('Access-Control-Max-Age: 1728000');
    header('Access-Control-Allow-Credentials: true');
    header("Content-Length: 10000000");
    header("Content-Type: text/plain");
    //exit(0);
}
require 'simple_html_dom.php';
$a = new ContentEngine();
print_r($a->result);

class ContentEngine
{
    protected $html = null;
    protected $shop = null;
    protected $url = null;
    public $result = array();

    public function __construct()
    {
        $html = html_entity_decode($_REQUEST['data']);
        $shop = @html_entity_decode($_REQUEST['shop']);
        // Parse HTML DOM
        $this->html = str_get_html($html);
        $this->shop = str_get_html($shop);
        $this->url = @html_entity_decode($_REQUEST['link']);

        // Call all method find data
        $this->__findTitle();
        $this->__selectProps();
        $this->__findImg();
        $this->__findItemLink();
        $this->__findPrice();
        $this->__findShopName();
        $this->__findItemId();
        $this->__findShopNumId();

        return $this->result;
    }

    protected function __selectProps()
    {
        $selectProps = true;
        $color_size = $coma = "";
        if (preg_match('/taobao/', $this->url)) {
            $props = $this->html->find('dl[class^=tb-prop]');
            if (!empty($props)) {
                foreach ($props as $k => $v) {
                    if (!$v->find('li[class^=tb-selected]')) {
                        $selectProps = false;
                        break;
                    }
                    $color_size .= $coma . $v->find('li[class^=tb-selected]', 0)->find('span', 0)->innertext;
                    $coma = ';';
                }
            }
        } else {
            $props = $this->html->find('dl[class^=tb-prop]');
            if (!empty($props)) {
                foreach ($props as $k => $v) {
                    if (!$v->find('li[class^=tb-selected]')) {
                        $selectProps = false;
                        break;
                    }
                    $color_size .= $coma . $v->find('li[class^=tb-selected]', 0)->find('span', 0)->innertext;
                    $coma = ';';
                }
            }
        }
        if ($selectProps & !empty($selectProps)) {
            $this->result['color_size'] = $color_size;
        }
    }

    protected function __findImg()
    {
        try {
            if (preg_match('/tmall/', $this->url)) {
                $this->result['img'] = $this->html->getElementById('J_ImgBooth')->getAttribute('src');
            } else
                $this->result['img'] = $this->html->getElementById('img[id=J_ImgBooth]')->getAttribute('data-src');
        } catch (Exception $e) {
            echo 'Cannot find img. ' . $e;
        }
    }

    protected function __findTitle()
    {
        try {
            if (preg_match('/taobao/', $this->url)) { // Taobao
                $this->result['title'] = $this->html->find('h3[class=tb-item-title]', 0)->innertext;
            } else { // Tmall
                $this->result['title'] = $this->html->find('div[class=tb-detail-hd]', 0)->find('h3', 0)->innertext;
            }
        } catch (Exception $e) {
            echo 'Cannot find title. ' . $e;
        }
    }

    protected function __findItemLink()
    {
        $this->result['item_link'] = $this->url;
    }

    protected function __findPrice()
    {
        if (preg_match('/taobao/', $this->url)) {
            $price = $this->html->getElementById('J_StrPriceModBox');
            if ($price) {
                $this->result['price'] = preg_replace('/[^0-9\.]/', '', $price->find('em[class=tb-rmb-num]', 0)->innertext);
            }
            // Promotion price
            $pPrice = $this->html->getElementById('J_PromoPrice');
            if ($pPrice) {
                $this->result['promotion_price'] = preg_replace('/[^0-9\.]/', '', $pPrice->find('strong[class=tb-rmb-num]', 0)->innertext);
            }
        } else {
            $price = $this->html->getElementById('J_StrPriceModBox');
            if ($price) {
                $this->result['price'] = $price->find('span', 0)->innertext;
            }
            $pPrice = $this->html->getElementById('J_PromoPrice');
            if ($pPrice) {
                $this->result['promotion_price'] = $pPrice->find('span', 0)->innertext;
            }
        }
    }

    protected function __findShopName()
    {
        try {
            if (preg_match('/taobao/', $this->url)) {
                // : in site with 3 characters
                $this->result['seller_name'] = substr(preg_replace('/掌柜/', '', $this->shop->find('a[class^=seller-name]', 0)->innertext), 3);
            } else {
                $this->result['seller_name'] = $this->shop->find('span[class=slogo]', 0)->find('a', 0)->innertext;
            }
        } catch (Exception $e) {
            throw new $e;
        }
    }

    protected function __findShopNumId()
    {
        if (preg_match('/taobao/', $this->url)) {
            if (count($this->shop->find('a[class^=shop-collect]')) > 0) {
                $shop_collect = $this->shop->find('a[class^=shop-collect]', 0)->href;
                $parse = parse_url($shop_collect);
                if (isset($parse['query'])) {
                    $params = preg_split('/&/', $parse['query']);
                    foreach ($params as $param) {
                        $data = preg_split('/=/', $param);
                        if ($data[0] == 'sellerid') {
                            $this->result['shop_num_id'] = $data[1];
                            break;
                        }
                    }
                }
            } else {
                $weitao = $this->shop->find('a[class^=weitao-follow]', 0)->href;
                $parse = parse_url($weitao);
                if (isset($parse['query'])) {
                    $params = preg_split('/&/', $parse['query']);
                    foreach ($params as $param) {
                        $data = preg_split('/=/', $param);
                        if ($data[0] == 'user_id') {
                            $this->result['shop_num_id'] = $data[1];
                            break;
                        }
                    }
                }
            }
        } else {
            $this->result['shop_num_id'] = $this->shop->getElementById('dsr-userid')->value;
        }
    }

    protected function __findItemId()
    {
        $parse = parse_url($this->url);
        if (isset($parse['query'])) {
            $params = preg_split('/&/', $parse['query']);
            foreach ($params as $param) {
                $data = preg_split('/=/', $param);
                if ($data[0] == 'id') {
                    $this->result['item_id'] = $data[1];
                    break;
                }
            }
        }
    }
}
