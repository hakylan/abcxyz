<?php
/**
 * Created by PhpStorm.
 * User: binhnt
 * Date: 1/21/14
 * Time: 11:30 AM
 */
use Flywheel\Config\ConfigHandler;
use Flywheel\Filesystem\Filesystem;

require_once(GLOBAL_INCLUDE_PATH."/TaobaoConvert.php");
require_once(GLOBAL_INCLUDE_PATH."/AliConvert.php");

class GlobalHelper
{

    const CRAWL_TAOBAO_URL = 'api/v2/Taobaosupplier/CrawlItem';
    const CRAWL_EELLY_URL = 'api/v2/Eelly/CrawlItem';
    const CRAWL_NAHUO_URL = 'api/v2/Nahuo/CrawlItem';
    const CRAWL_ALIBABA_URL = 'api/v2/Alibaba/CrawlItem';
    static $mongoDb = 'seudo_vn';

    /**/
    public static function translate($title = null, $resource = null)
    {
//        if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
//            return $title;
//        }
        try{
            if (empty($resource)) {
                $resource = self::resourceKeyToTranslate(self::$mongoDb);
            }

            foreach ($resource as $r) {
                $title = strpos($title, $r['keyword_china']) !== false ?
                    preg_replace("/{$r['keyword_china']}/", $r['keyword_vi'] . ' ', $title) : $title;
            }
            return self::mb_ucfirst($title);
        }catch (\Exception $e){
            return $title;
        }

    }

    public static $list_position = array(-1 => 'O1', 1 => 'S1', 2 => 'S2', 3 => 'Adj1', 4 => 'Adj2', 5 => 'Adj3');

    public static function translateTitle($title, $resource = null)
    {
//        if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
//            return $title;
//        }
        $resource = self::resourceKeyToTranslate(null, 'title_translate');
        $keywords = $resource;

        $found_words = array();
        $found_values = array();
        $exist_groups = array();

        foreach (self::$list_position as $key => $name) {
            $found_words[$key] = array();
        }

        if (!empty($keywords)) {
            foreach ($keywords as $keyword) {
                if (stripos($title, $keyword['keyword_china']) !== false) {
                    $title = str_replace($keyword['keyword_china'], ' ', $title);
                    $tags = $keyword['tags'];
                    if (trim($tags) != '') {
                        $tags = explode(',', $tags);
                    }
                    $lay = true;
                    $processed_tags = array();
                    if (!empty($tags)) {
                        foreach ($tags as $tag) {
                            if (in_array(trim(mb_strtolower($tag, 'UTF-8')), $exist_groups)) {
                                $processed_tags[] = mb_strtolower($tag, 'UTF-8');
                                $lay = false;
                            }
                            $exist_groups[] = mb_strtolower($tag, 'UTF-8');
                        }
                    }

                    if (!in_array(trim(mb_strtolower($keyword['keyword_vi'], 'UTF-8')), $found_values) && $lay) {
                        $found_words[$keyword['vi_position']][] = $keyword['keyword_vi'];
                        $found_values[] = trim(mb_strtolower($keyword['keyword_vi'], 'UTF-8'));

                        if (!empty($processed_tags))
                            $exist_groups = array_merge($processed_tags, $exist_groups);
                    }
                }
            }
        }

        $str_new = '';
        if (!empty($found_words)) {
            foreach ($found_words as $key => $values) {
                // lap qua danh sach cac tu lay dc
                if (!empty($values)) {
                    if ($key == 1 || $key == 2 || $key == -1) { //khong dung dau , giua S1 va S2
                        if ($key == 2) {
                            $sub_str = $values[0];
                        } else {
                            $sub_str = implode('/', $values);
                        }
                    } else {
                        $sub_str = implode(', ', $values);
                    }

                    if ($str_new == '') {
                        $str_new .= $sub_str;
                    } elseif ($key == 1 || $key == 2 || $key == -1) {
                        $str_new .= ' ' . $sub_str;
                    } else
                        $str_new .= ', ' . $sub_str;
                }
            }
        }

        $result = self::mb_ucfirst($str_new);
        if (preg_replace('/\s/', '', $result) == '') {
            $result = $title;
        }
        return $result;
    }

    public static function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false)
    {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }

    public static function resourceKeyToTranslate($db = null, $tbName = null)
    {
//        if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
//            return null;
//        }
        static $resources;
        if (null == $resources) {
            $resources = array();
        }

        $tbName = $tbName ? $tbName : 'keyword_search';
        if (!isset($resources[$tbName])) {
            $conn = \SeuDo\MongoDB::getConnection('keyword_search');
            $resources[$tbName] = $conn->orderBy(array(
                'weighted' => -1
            ))->get($tbName);
        }

        return $resources[$tbName];
    }

    /**
     * Save locations to redis - string json_encode
     * @param Redis $redis
     * @return array
     */
    public static function loadLocations(\Redis $redis)
    {
        $result = $redis->get('locations');
        if (empty($result)) {
            $locations = Locations::read()->select('`id`, `label`, `key_code`')->execute()->fetchAll(); //, `key_code`
            if ($locations) {
                foreach ($locations as $l) {
                    $result[$l['id']] = $l;
                }
            }
            $redis->set('locations', ($result = json_encode($result)));
        }
        return json_decode($result, true);
    }

    /**
     * Get Cawl Data - ket qua tra ve la array data đã convert
     * @param $url
     * @param $website
     * @param $file
     * @param $content_file
     * @return array|mixed|string
     */
    public static function getCrawlData($url, &$website, &$file, &$content_file)
    {
        try{
            $urlCrawlService = ConfigHandler::get('service.crawl_item');

            $urlDetail = urldecode($url);
            $parse_url = parse_url($urlDetail);
            $fileName = '';
            $website = '1688';
            $data = array();
            $content_file = null;
            if (preg_match('/1688.com|alibaba.com/', $urlDetail)) {
                $urlCrawlService .= self::CRAWL_ALIBABA_URL;
                $data = array('item_url' => $urlDetail);
                if (array_key_exists('path', $parse_url)) {
                    $fileName = '1688_' . preg_replace('/[^0-9]+/', '', $parse_url['path']) . '.txt';
                }
            }
            if (preg_match('/taobao.com|tmall.com/', $urlDetail)) {
                $urlCrawlService .= self::CRAWL_TAOBAO_URL;

                $data = TaobaoConvert::getCrawlInfo($urlDetail);

                if (preg_match('/taobao.com/', $urlDetail)) {
                    $website = 'taobao';
                } else {
                    $website = 'tmall';
                }

                if ($website == 'taobao') {
                    $fileName = 'TAOBAO_' . $data['item_id'] . '.txt';
                } else if ($website == 'tmall') {
                    $fileName = 'TMALL_' . $data['item_id'] . '.txt';
                }
            }
            if (preg_match('/nahuo.com/', $urlDetail)) {
                $urlCrawlService .= self::CRAWL_NAHUO_URL;
                $website = 'nahuo';
                // Get id nahuo
                $item_id = preg_replace('/[^0-9]+/', '', $parse_url['path']);
                $fileName = 'NAHUO_' . $item_id . '.txt';
                $data = array('item_url' => $urlDetail);
            }
            if (preg_match('/eelly.com/', $urlDetail)) {
                $urlCrawlService .= self::CRAWL_EELLY_URL;
                $website = 'eelly';
                // Get id nahuo
                $item_id = preg_replace('/[^0-9]+/', '', $parse_url['path']);
                $fileName = 'EELLY_' . $item_id . '.txt';
                $data = array('item_id' => $item_id);
            }

            // Cache file
//            file_exists($file = )

            $path_root = ConfigHandler::get('caching.crawl');

            $path = ConfigHandler::get('caching.crawl').'05'.'/'.$website;

            \Flywheel\Util\Folder::create($path);
            $file = $path .'/'. $fileName;

            $filesystem = new Filesystem();
            $flag = true;


            if($filesystem->exists($path_root .'/'. $fileName)){
                $file = $path_root .'/'. $fileName;
                $flag = true;
            }elseif($filesystem->exists($path .'/'. $fileName)){
                $flag = true;
            }else{
                $flag = false;
            }
            $result = "";
            if ($flag) {
                try{
                    $handle = fopen($file, "r");
                    if(filesize($file) > 0){
                        $result = fread($handle, filesize($file));
                        fclose($handle);
                    }else{
                        $result = self::curlCrawler($urlCrawlService,$data,$content_file);
                    }
                }catch (\Flywheel\Exception $e){
                    $result = self::curlCrawler($urlCrawlService,$data,$content_file);
                }
            } else {
                $result = self::curlCrawler($urlCrawlService,$data,$content_file);
            }

            $result = json_decode($result, true);

            switch ($website) {
                case 'taobao':
                case 'tmall':
                    $result = TaobaoConvert::convertTaobaoData($result);//elf::convertTaobaoData($result);
                    break;
                case 'nahuo':
                    $result = self::convertNahuoData($result);
                    break;
                case 'eelly':
                    $result = self::convertEellyData($result);
                    break;
                default:
                    $result = $result['data'];
                    break;
            }
            return $result;
        }catch (\Flywheel\Exception $e){
            return array();
        }

    }

    /**
     * @param $urlCrawlService
     * @param $data
     * @param $content_file
     * @return mixed
     */
    private static function curlCrawler($urlCrawlService,$data,&$content_file){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $urlCrawlService . '?' . http_build_query($data));

        $result = curl_exec($ch);
        $content_file = $result;
        return $result;
    }

    // Convert dữ liệu trả về của site giống alibaba
    static function convertNahuoData($result) {
        $result = $result['data'];

        $_skuProps = $_skuMap = $_price = array();
        if (isset($result['detail_attributes']['Colors'])) {
            $_skuProps[0]['prop'] = '颜色';
            $_skuProps[1]['prop'] = 'size';
            foreach ($result['detail_attributes']['Colors'] as $k => $v) {
                $colorName = $v['Name'];
                $_skuProps[0]['value'][$k]['name'] = $colorName;
                if (array_key_exists('Sizes', $v)) {
                    foreach ($v['Sizes'] as $k_s => $v_s) {
                        $sizeName = $v_s['Name'];
                        $_skuProps[1]['value'][$k_s]['name'] = $sizeName;
                        $_skuMap[$colorName . '>' . $sizeName] = array(
                            'canBookCount' => $v_s['Stock'],
                            'outerId' => $v_s['PID']
                        );
                    }
                } else { // No size
                    $_skuMap[$colorName] = array('canBookCount' => 999); // Can't get can book amount
                }
            }
        }
        $_price = array(
            'begin' => 1,
            'end' => null,
            'price' => array_key_exists('member_price', $result) ?
                    $result['member_price'] : $result['detail_attributes']['Price']
        );

        $result = array(
            'id' => $result['id'],
            'origin_url' => $result['origin_url'],
            'config' => array(
                'beginAmount' => 1
            ),
            'purchase' => array(
                'sku' => array(
                    'skuProps' => $_skuProps,
                    'skuMap' => $_skuMap
                ),
                'ordering_step_number' => 1,
                'invetory' => 1
            ),
            'carriage' => array('beginAmount' => 1),
            'price' => array($_price),
            'title' => $result['title'],
            'images' => $result['images'],
            'wangwang' => isset($result['wangwang']) ? $result['wangwang'] : '',
            'proxy' => '',
            'supplier' => $result['supplier'],
            'transfer_calculator' => array( // Neu tinh duoc gia van chuyen noi dia
                'costs' => array(
                    array(
                        'cost' => 0
                    )
                )
            ),
            'detail_desc' => $result['detail_desc']
        );
        return $result;
    }

    // Convert dữ liệu trả về của site giống alibaba
    static function convertEellyData($result) {
        $_skuProps = $_skuMap = $_price = array();
        // Filter sku map
        if (isset($result['purchase']['skuMap'])) {
            // Set properties here
            $_skuProps[0]['prop'] = '颜色';
            $_skuProps[1]['prop'] = 'size';

            foreach ($result['purchase']['skuMap'] as $k => $v) {
                $_skuMap[$v['style'] . '>' . $v['size']] = array(
                    'canBookCount' => $v['inventory']
                );

                // Color
                if(isset($_skuProps[0]['value']) && is_array($_skuProps[0]['value'])) {
                    $exist = false;
                    foreach($_skuProps[0]['value'] as $_v) {
                        if($_v['name'] == $v['style']) {
                            $exist = true;
                            break;
                        }
                    }
                    if(!$exist) $_skuProps[0]['value'][$k]['name'] = $v['style'];
                } else {
                    $_skuProps[0]['value'][$k]['name'] = $v['style'];
                }

                // Size
                if(isset($_skuProps[1]['value']) && is_array($_skuProps[1]['value'])) {
                    $exist = false;
                    foreach($_skuProps[1]['value'] as $_v) {
                        if($_v['name'] == $v['size']) {
                            $exist = true;
                            break;
                        }
                    }
                    if(!$exist) $_skuProps[1]['value'][$k]['name'] = $v['size'];
                } else {
                    $_skuProps[1]['value'][$k]['name'] = $v['size'];
                }
            }
        }
        // find sku prop
        /*if (array_key_exists('style', $result)) {
            $_skuProps[0]['prop'] = '颜色';
            $_skuProps[1]['prop'] = 'size';
            foreach ($result['style'] as $k => $v) {
                $_skuProps[0]['value'][$k]['name'] = $v;
                if (array_key_exists('size', $result)) {
                    foreach ($result['size'] as $k_s => $v_s) {
                        $_skuProps[1]['value'][$k_s]['name'] = $v_s;
                    }
                }
            }
        }*/

        if (array_key_exists('price', $result)) {
            foreach ($result['price'] as $k => $p) {
                if (count(explode('-', $p['number'])) > 1) {
                    list($begin, $end) = explode('-', $p['number']);
                } else {
                    $begin = $p['number'];
                    $end = null;
                }
                $_price[$k] = array(
                    'begin' => !empty($begin) ? preg_replace('/≥/', '', $begin) : 1,
                    'end' => !empty($end) ? preg_replace('/≥/', '', $end) : null,
                    'price' => $p['price']
                );
            }
        }
        $result = array(
            'id' => $result['id'],
            'origin_url' => $result['origin_url'],
            'config' => array(
                'beginAmount' => 1
            ),
            'purchase' => array(
                'sku' => array(
                    'skuProps' => $_skuProps,
                    'skuMap' => $_skuMap
                ),
                'ordering_step_number' => 1,
                'invetory' => 1
            ),
            'carriage' => array('beginAmount' => 1),
            'price' => $_price,
            'title' => $result['title'],
            'wangwang' => isset($result['wangwang']) ? $result['wangwang'] : '',
            'images' => $result['images'],
            'proxy' => '',
            'supplier' => $result['supplier'],
            'transfer_calculator' => array( // Neu tinh duoc gia van chuyen noi dia
                'costs' => array(
                    array(
                        'cost' => 0
                    )
                )
            ),
            'detail_desc' => $result['detail_desc']
        );
        return $result;
    }


//    public static function getRoundingNum($money = 0, $source = array(), $round = 100)
//    {
//        $round = 100;
//        if (empty($source)) {
//            return $round;
//        }
//        foreach ($source as $v) {
//            if (empty($v['end'])) {
//                $round = $v['round'];
//                break;
//            }
//            if ($money >= $v['begin'] & $money < $v['end']) {
//                $round = $v['round'];
//                break;
//            }
//        }
//        return $round;
//    }
//
    public static function rounding($amount, $round = 100)
    {
        $round = 10;
        $value = intval($amount);
        $value = $value < $round ?
            $value : ceil($value / $round) * $round;
        return $value;
    }
//
//    public static function currencyFormat($value, $min_money = 1000, $symbol = null, $round = true)
//    {
//        if ($round) {
//            $value = floatval($value);
//            $value = $value < $min_money ?
//                $value : ceil($value / $min_money) * $min_money;
//        }
//
//        if (intval($value) >= 1000) {
//            if ($value != '' and is_numeric($value)) {
//                $value = number_format($value, 2, ',', '.');
//                $value = str_replace(',00', '', $value);
//            }
//        }
//        if ($symbol)
//            $value .= ' ' . $symbol;
//        return $value;
//    }

}