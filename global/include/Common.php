<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 1/20/14
 * Time: 3:37 PM
 */
class Common{

    const ROUNDING = 10;
    /**
     * Number Format
     * @param $number
     * @param bool $round
     * @param int $rounding
     * @return float|mixed|string
     */
    public static function numberFormat($number,$round=false, $rounding = Common::ROUNDING){

        $rounding = Common::ROUNDING;
        if(!is_numeric($number)){
            return $number;
        }
        if(!$round){
            $number = number_format($number, 2, ',', '.');
            $number = str_replace(',00', '', $number);
            return $number;
        }

        $number = doubleval($number);

        $number = $number < $rounding ?
            $number : ceil($number / $rounding) * $rounding;
        if (intval($number) >= 1000) {
            $number = number_format($number, 2, ',', '.');
            $number = str_replace(',00', '', $number);
        }

        return $number;
    }

    /**
     * Rounding Money
     * @param $number
     * @param int $rounding
     * @return float
     */
    public static function roundingMoney($number,$rounding = Common::ROUNDING){
        $rounding = Common::ROUNDING;
        if(!is_numeric($number)){
            return $number;
        }
        $number = doubleval($number);
        $number = $number < $rounding ?
            $number : ceil($number / $rounding) * $rounding;

        return $number;
    }



    /**
     * Get Favicon Site
     * @param string $site
     * @return string
     */
    public static function getFaviconSite($site = "ALIBABA"){
        $site = strtoupper($site);
        switch($site){
            case CartItem::TAOBAO_SITE:
                $fav = \SeuDo\Main::getHomeUrl()."assets/images/icon-footer/taobao.png";
                break;
            case CartItem::TMALL_SITE:
                $fav = \SeuDo\Main::getHomeUrl()."assets/images/icon-footer/tmal.png";
                break;
            case CartItem::ALIBABA_SITE:
                $fav = \SeuDo\Main::getHomeUrl()."assets/images/icon-footer/1688.png";
                break;
            case CartItem::EELLY_SITE:
                $fav = \SeuDo\Main::getHomeUrl()."assets/images/icon-footer/elly.png";
                break;
            default:
                $fav = \SeuDo\Main::getHomeUrl()."assets/images/icon-footer/1688.png";
                break;
        }
        return $fav;
    }

    /**
     * Get Item Link
     * @param $site
     * @param $id
     * @return string
     */
    public static function getItemLink($site,$id){
        $site = strtoupper($site);
        switch($site){
            case CartItem::TAOBAO_SITE:
                $link = "http://item.taobao.com/item.htm?id=".$id;
                break;
            case CartItem::TMALL_SITE:
                $link = "http://detail.tmall.com/item.htm?id=".$id;
                break;
            case CartItem::ALIBABA_SITE:
                $link = "http://detail.1688.com/offer/{$id}.html";
                break;
            case CartItem::EELLY_SITE:
                $link = "http://www.eelly.com/goods/{$id}.html";
                break;
            default:
                $link = "";
                break;
        }
        return $link;
    }


    /**
     * get working year sequence for order code
     * @return int
     */
    public static function getWorkingYearSequence(){
        $year = date("Y");
        return intval($year) - 2014;
    }

    /**
     * @param string $order_seller_home_land
     * @param string $order_invoice
     * @return array
     */

    public static function getLinkSiteRoot($order_seller_home_land = "", $order_invoice = ""){
        $show_link_site_root = false;
        $arrLinkSiteRoot = array();

        if($order_seller_home_land == "" || $order_invoice == ""){
            return array('show_link_site_root' => $show_link_site_root,
                'arrLinkSiteRoot' => $arrLinkSiteRoot);
        }

        if($order_seller_home_land == \ComplaintSeller::SITE_1688
            || $order_seller_home_land == \ComplaintSeller::SITE_TAOBAO
            || $order_seller_home_land == \ComplaintSeller::SITE_TMALL){
            $arrOrderInvoice = array();
            $arrOrderInvoice = explode(";", $order_invoice);
            for($v = 0; $v < sizeof($arrOrderInvoice); $v++){
                $last_index = ( ( $v + 1 ) == sizeof($arrOrderInvoice) ) ? false : true;
                switch($order_seller_home_land){
                    case \ComplaintSeller::SITE_1688:
                        $show_link_site_root = true;
                        $arrLinkSiteRoot[$v]['link'] = "http://trade.1688.com/order/unify_buyer_detail.htm?orderId=" . $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['order_invoice'] = $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['last_index'] = $last_index;
                        break;
                    case \ComplaintSeller::SITE_TAOBAO:
                        $show_link_site_root = true;
                        $arrLinkSiteRoot[$v]['link'] = "http://trade.taobao.com/trade/detail/trade_item_detail.htm?bizOrderId=" . $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['order_invoice'] = $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['last_index'] = $last_index;
                        break;
                    case \ComplaintSeller::SITE_TMALL:
                        $show_link_site_root = true;
                        $arrLinkSiteRoot[$v]['link'] = "http://trade.tmall.com/detail/orderDetail.htm?bizOrderId=" . $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['order_invoice'] = $arrOrderInvoice[$v];
                        $arrLinkSiteRoot[$v]['last_index'] = $last_index;
                        break;
                    default:
                        //                            $show_link_site_root = false;
                        //                            $arrLinkSiteRoot = array();
                }
            }
        }

        return array('show_link_site_root' => $show_link_site_root,
            'arrLinkSiteRoot' => $arrLinkSiteRoot);
    }


    /**
     * @todo cut string, example title ...
     * @param $str
     * @param int $sub_length
     * @param string $pad
     * @param bool $strip
     * @return mixed|string
     */
    public static function subString($str, $sub_length = 100, $pad='...', $strip=FALSE)
    {
        $str = stripcslashes($str);

        $str = strip_tags($str);

        while (strpos($str,"&nbsp;")) $str = str_replace("&nbsp;"," ",$str);

        while (strpos($str,"\\n")) $str = str_replace("\\n"," ",$str);

        while (strpos($str,"  ")) $str = str_replace("  "," ",$str);

        if(strlen($str) > $sub_length){
            return mb_substr($str,0,$sub_length,'UTF-8').$pad;
        }else{
            return $str;
        }
    }

    /**
     * @param $time
     * @param string $format
     * @return string
     * @throws InvalidArgumentException
     */
    public static function validDateTime($time,$format = 'Y-m-d H:i:s'){
        if (is_string($time)) {
            if (!\Flywheel\Validator\Util::validateDate($time, $format)) {
                throw new \InvalidArgumentException("Invalid 'time' format!");
            }

            $time = \DateTime::createFromFormat("Y-m-d H:i:s", $time);
        } elseif (is_int($time)) {
            $time = \DateTime::createFromFormat('U', $time);
        }

        if (!($time instanceof \DateTime)) {
            throw new \InvalidArgumentException("Invalid 'time' format!");
        }

        return $time->format($format);
    }

    public static function getTimeStamp($time){
        try{
            $datetime = new DateTime($time);
            $datetime = $datetime->format("Y-m-d H:i:s");
            $time_stamp = strtotime($datetime);
        }catch (\Exception $e){
            try{
                if($time instanceof DateTime || $time instanceof \Flywheel\Db\Type\DateTime){
                    $time_stamp =  strtotime($time->format("Y-m-d H:i:s"));
                }else{
                    $time_stamp = 0;
                }
            }catch (\Exception $e){
                try{
                    $time_stamp = strtotime($time);
                }catch (\Exception $e){
                    $time_stamp = 0;
                }
            }
        }

        return intval($time_stamp);
    }


    /**
     * @param $html
     * @param int $key
     * @param array $image_list
     * @return array
     */
    public static function getImageUrlFromHtml($html,$key=0,$image_list =array()){

        return $image_list;
//        return array();
//        try{
//            $dom = new domDocument;
//
//            $html = stripcslashes($html);
//
//            $dom->loadHTML($html);
//
//            /*** discard white space ***/
//            $dom->preserveWhiteSpace = false;
//
//            $images = $dom->getElementsByTagName('img');
//            foreach($images as $value){
//
//                $url = $value->getAttribute('src');
//
//                if($url == ''){
//                    continue;
//                }
//
////            $size = getimagesize($url);
////
////            if(is_array($size) && $size[0] < 200 || $size[1] < 200){
////                continue;
////            }
//
//                $image_list[$key] = $url;
//                $key ++;
//            }
//
//            return $image_list;
//        }catch (\Flywheel\Exception $e){
//            return array();
//        }

    }

    public static function formatTimeNotification( $time )
    {
        $time = intval( $time );
        $current_time = time();
        if ( date( 'Y', $current_time ) == date( 'Y', $time ) && date( 'm', $current_time ) == date( 'm', $time )
            && date( 'd', $current_time ) == date( 'd', $time )
        ) {
            $string_time = date( 'h:i d/m/Y', $time );
        } else {
            $string_time = date( 'd/m/Y', $time );
        }

        return $string_time;
    }

    public static function formatDateNotification( $time )
    {
        $time = intval( $time );
        $current_time = time();
        if ( date( 'Y', $current_time ) == date( 'Y', $time ) && date( 'm', $current_time ) == date( 'm', $time )
            && date( 'd', $current_time ) == date( 'd', $time )
        ) {
            $string_time = 'Hôm nay';
        } elseif ( date( 'Y', $current_time ) == date( 'Y', $time ) ) {
            $string_time = date( 'd', $time ) . " tháng " . date( 'm', $time );
        } else {
            $string_time = date( 'd', $time ) . " tháng " . date( 'm', $time ) . " năm " . date( 'Y', $time );
        }

        return $string_time;
    }

    public static function formatPhone($phone){
        $phone = preg_replace("/[^0-9]/", "", $phone);

        if(strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif(strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $phone);
        else
            return $phone;
    }

    public static function calDistanceBetweenDay($date1, $date2){
        //form datetime (2012-07-14)
        $first_date = strtotime($date1);
        $second_date = strtotime($date2);
        $datediff = abs($first_date - $second_date);
        return floor($datediff/(60*60*24));
    }

    /**
     * Return link to homeland page
     * TODO: now we only add ".com" to it
     * @param $homeland string
     * @return string
     */
    public static function getHomelandUrl($homeland)
    {
        return $homeland.'.com';
    }


    /**
     * get Memory use
     */
    public static function getMemoryUse(){
        $memory = memory_get_peak_usage()/1048576;
        return $memory;
    }


    /**
     * @param string $file_name
     */
    public static function writeFileMemoryLog($file_name = "memory_log.html"){

        $memory = Common::getMemoryUse();

        $action = $_SERVER['REQUEST_URI'];

        $file = PUBLIC_DIR.$file_name;

        $content = file_get_contents($file);

        $array = array(
            "</table>"
        );

        $content = str_replace($array,"",$content);

        $tr_new =
            "<tr>
                <td style=\"width: 500px; word-break: break-all;\">{$action}</td>
                <td style=\"width: 500px; word-break: break-all;text-align: center\">{$memory}</td>
                <td style=\"width: 200px; text-align: center;\">".date('H:i d/m/Y')."</td>
            </tr>";

        $content = $content.$tr_new."</table>";

        file_put_contents($file,$content);
    }


    /**
     * @param string $start_date
     * @param string $end_date
     * @return float
     */
    public static function getDateDiff( $start_date = "", $end_date = "" ) {
        $first_date = strtotime($start_date);
        $second_date = strtotime($end_date);
        $datediff = abs($first_date - $second_date);
        return floor($datediff/(60*60*24));
    }

    /**
     * Converting string with accented characters to non-accented equivalent
     * @param $string_utf8
     * @return mixed
     */
    public static function convertAccentedToNonAccented($string_utf8){
        $str = trim($string_utf8,' ');

        $accented_array = array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă",
            "ằ","ắ","ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề",
            "ế","ệ","ể","ễ",
            "ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ",
            "ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ","đ",
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă",
            "Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ","Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
            "Đ","̃","ễ","á","ặ","ạ","ứ","ú","ầ","à","̀","ế","é","ị","ạ"
        );

        /**
         * Mảng chứa tất cả ký tự không dấu tương ứng với mảng $accented_array bên trên
         */
        $non_accented_array = array("a","a","a","a","a","a","a","a","a","a","a",
            "a","a","a","a","a","a",
            "e","e","e","e","e","e","e","e","e","e","e",
            "i","i","i","i","i",
            "o","o","o","o","o","o","o","o","o","o","o","o",
            "o","o","o","o","o",
            "u","u","u","u","u","u","u","u","u","u","u",
            "y","y","y","y","y","d",
            "A","A","A","A","A","A","A","A","A","A","A","A",
            "A","A","A","A","A",
            "E","E","E","E","E","E","E","E","E","E","E",
            "I","I","I","I","I",
            "O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O",
            "U","U","U","U","U","U","U","U","U","U","U",
            "Y","Y","Y","Y","Y",
            "D","","e","a","a","a","u","u","a","a","","e","e","i","a"
        );

        $str_convert = str_replace($accented_array,$non_accented_array,$str);

        return $str_convert;
    }


    /**
     * Get Client Ip - Create by Quyen
     * @return string
     */
    public static function getClientIp(){
        if (isset($_SERVER)) {

            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            }

            if (isset($_SERVER["HTTP_CLIENT_IP"])){

                return $_SERVER["HTTP_CLIENT_IP"];

            }else{
                return $_SERVER["REMOTE_ADDR"];
            }
        }

        if (getenv('HTTP_X_FORWARDED_FOR')){

            return getenv('HTTP_X_FORWARDED_FOR');

        }
        if (getenv('HTTP_CLIENT_IP')){

            return getenv('HTTP_CLIENT_IP');

        }else{

            return getenv('REMOTE_ADDR');

        }
    }
}