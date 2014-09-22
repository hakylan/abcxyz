<?php 
/**
 * ComplaintSeller
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ComplaintSellerBase.php';
class ComplaintSeller extends \ComplaintSellerBase {

    //Lý do
    const REASON_PRODUCT_NOT_RECEIVED = "PRODUCT_NOT_RECEIVED";//Không thấy hàng
    const REASON_LACK_DELIVERY = "LACK_DELIVERY";//Phát hàng thiếu
    const REASON_WRONG_DELIVERY = "WRONG_DELIVERY";//Phát hàng sai
    const REASON_DEFECTS = "DEFECTS";//Lỗi
    const REASON_CHANGE_PAY_DELIVERY = "CHANGE_PAY_DELIVERY";//Đổi, trả hàng
    const REASON_OTHER = "OTHER";//Khác

    //Trạng thái
    const STATUS_PENDING = "PEDDING";//Chưa tiếp nhận
    const STATUS_PROCESSING = "PROCESSING";//Đang xử lý
    const STATUS_SUCCESS = "SUCCESS";//Thành công
    const STATUS_FAILURE = "FAILURE";//Thất bại

    //Level
    const LEVEL_SELLER = "SELLER";//Khiếu nại người bán (taobao.com,...)
    const LEVEL_MANAGER = "MANAGER";//Khiếu nại với người quản lý
    const LEVEL_ARBITRATION = "ARBITRATION";//Khiếu nại với trung gian (alimama.vn, seudo.vn,...)

    //Site
    const SITE_1688 = "1688";
    const SITE_TAOBAO = "TAOBAO";
    const SITE_TMALL = "TMALL";
    const SITE_EELLY = "EELLY";

    public static $siteTitle = array(
        self::SITE_1688 => '1688.com(alibaba)',
        self::SITE_TAOBAO => 'Taobao.com',
        self::SITE_TMALL => 'Tmall.com',
        self::SITE_EELLY => 'Eelly.com'
    );

    public static $statusTitle = array(
        self::STATUS_PENDING => 'CHƯA TIẾP NHẬN',
        self::STATUS_PROCESSING => 'ĐANG XỬ LÝ',
        self::STATUS_SUCCESS => 'THÀNH CÔNG',
        self::STATUS_FAILURE => 'THẤT BẠI'
    );

    public static $reasonTitle = array(
        self::REASON_PRODUCT_NOT_RECEIVED => 'Không thấy hàng',
        self::REASON_LACK_DELIVERY => 'Phát hàng thiếu',
        self::REASON_WRONG_DELIVERY => 'Phát hàng sai',
        self::REASON_DEFECTS => 'Hàng lỗi',
        self::REASON_CHANGE_PAY_DELIVERY => 'Đổi (trả) hàng',
        self::REASON_OTHER => 'Khác'
    );

    public static $levelTitle = array(
        self::LEVEL_SELLER => 'Người bán',
        self::LEVEL_MANAGER => 'Người quản lý',
        self::LEVEL_ARBITRATION => 'Đơn vị trung gian (Taobao.com, Tmall.com, 1688.com, Eelly.com)'
    );

    /**
     * @param $site
     * @return mixed
     */
    public static function getSiteTitle($site){
        return static::$siteTitle[$site];
    }

    /**
     * @param $status
     * @return mixed
     */
    public static function getStatusTitle($status){
        return static::$statusTitle[$status];
    }

    /**
     * @param $reason
     * @return mixed
     */
    public static function getReasonTitle($reason){
        return static::$reasonTitle[$reason];
    }

    public function checkExistComplaintSellerDoing( $order_id ) {
        $query = \ComplaintSeller::select();
        $query->andWhere(" `order_id` = {$order_id} ");
        $query->andWhere(" `status` = '" . \ComplaintSeller::STATUS_PROCESSING . "' ");
        return $query->count('id')->execute() > 0;
    }

    /**
     * @param $reason
     * @return mixed
     */
    public static function getLevelTitle($reason){
        return static::$levelTitle[$reason];
    }

    public static function getTotalStatus(){
        $total = 0;
        try{
            $status_success = \ComplaintSeller::STATUS_SUCCESS;
            $status_failure = \ComplaintSeller::STATUS_FAILURE;
            $query = \ComplaintSeller::select();
            $query->select("id");
            $query->andWhere(" `status` != '{$status_success}' ");
            $query->andWhere(" `status` != '{$status_failure}' ");
            $total = $query->count('id')->execute();
            return $total;
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('get_total_complaint_seller_by_status')->addError('has error when try get total complaint seller by status',array($e->getMessage()));
            throw new \Flywheel\Exception('has error when try get total complaint seller by status');
            return $total;
        }
    }

    /*
     * DANH SÁCH KHIẾU NẠI NGƯỜI BÁN
     * input: $condition[], (int)$page, (int)$per_page
     * $condition['status']: Trạng thái khiếu nại
     * $condition['order_id']: ID đơn hàng
     * $condition['order_code']: Mã đơn hàng
     * $condition['order_seller_name']: Lọc theo người bán
     * $condition['order_seller_aliwang']: Lọc theo người bán
     * $condition['order_seller_homeland']: Lọc theo người bán
     * $condition['order_seller_info']: Lọc theo người bán
     * $condition['order_invoice']: Lọc theo mã đơn hàng site gốc
     * $condition['order_buyer_id']: Lọc theo user mua hàng
     * $condition['reason']: Lọc theo lý do đòi tiền
     * output: []
     *
     * */
    public static function getComplaintSeller($condition = array(), $page = 1, $per_page = 10){
        $offset = ($page - 1) * $per_page;
        $limit = $page * $per_page;

        $from_time = isset($condition['from_time']) ? $condition['from_time'] : "";
        $to_time = isset($condition['to_time']) ? $condition['to_time'] : "";
        $status = isset($condition['status']) ? $condition['status'] : "";
        $order_id = isset($condition['order_id']) ? $condition['order_id'] : "";
        $order_code = isset($condition['order_code']) ? $condition['order_code'] : "";
        $seller_name = isset($condition['seller_name']) ? $condition['seller_name'] : "";
        $seller_aliwang = isset($condition['seller_aliwang']) ? $condition['seller_aliwang'] : "";
        $seller_homeland = isset($condition['seller_homeland']) ? $condition['seller_homeland'] : "";
        $seller_info = isset($condition['seller_info']) ? $condition['seller_info'] : "";
        $order_invoice = isset($condition['order_invoice']) ? $condition['order_invoice'] : "";
        $order_buyer_id = isset($condition['order_buyer_id']) ? $condition['order_buyer_id'] : 0;
        $account_purchase_origin = isset($condition['account_purchase_origin']) ? $condition['account_purchase_origin'] : 0;
        $account_purchase_origin_name = isset($condition['account_purchase_origin_name']) ? $condition['account_purchase_origin_name'] : "";
        $reason = isset($condition['reason']) ? $condition['reason'] : "";
        $level = isset($condition['level']) ? $condition['level'] : "";
        $seller_homeland = isset($condition['seller_homeland']) ? $condition['seller_homeland'] : "";
        $key1 = isset($condition['key1']) ? $condition['key1'] : "";
        $key2 = isset($condition['key2']) ? $condition['key2'] : "";
        $all = isset($condition['all']) ? $condition['all'] : 0;

        $query = \ComplaintSeller::read();

        //Tìm kiếm theo key 1 (Nhập đơn hàng, mã đơn site gốc, người bán...)
        if($key1 != ""){
            $query->andWhere(" ( order_id LIKE '%{$key1}%'
                                        OR seller_name LIKE '%{$key1}%'
                                        OR order_code LIKE '%{$key1}%'
                                        OR order_invoice LIKE '%{$key1}%' ) ");
        }

        //Tìm kiếm theo key 2 (Nhập user khách, mã khách...)
        if($key2 != ""){
            $query->andWhere(" ( order_buyer_username LIKE '%{$key2}%'
                                        OR order_buyer_code LIKE '%{$key2}%' ) ");
        }

        //Tìm kiếm theo site
        if($seller_homeland != ""){
            $query->andWhere("seller_homeland = '{$seller_homeland}'");
        }

        //Tìm kiếm theo thời gian
        if($from_time != ''){
            $_from_time = new \DateTime($from_time); //($from_time);
            $from_time = $_from_time->format("Y-m-d H:i:s");
        }
        if($to_time != ''){
            $_to_time = new \DateTime($to_time);
            $to_time = $_to_time->format("Y-m-d 23:59:59");
        }

        if($from_time != '' && $to_time == ''){
            $query->andWhere("created_time >= '{$from_time}'");
        }

        if($from_time == '' && $to_time != ''){
            $query->andWhere("created_time<='{$to_time}'");
        }

        if($from_time != '' && $to_time != ''){
            $query->andWhere(" created_time >= '{$from_time}' AND created_time <= '{$to_time}' ");
        }

        //Tìm kiếm theo mức độ khiếu nại với
        if($level != ""){
            $query->andWhere("level = '{$level}'");
        }

        //Tìm kiếm theo trạng thái khiếu nại
        if($status != ''){
            $query->andWhere("status = '{$status}'");
        }

        //Tìm theo đơn hàng
        if($order_id > 0){
            $query->andWhere("order_id = {$order_id}");
        }

        //Tìm theo mã đơn hàng
        if($order_code != ""){
            $query->andWhere(" order_code LIKE '%{$order_code}%' ");
        }

        //Lọc theo seller_name (username shop gốc)
        if($seller_name != ""){
            $query->andWhere(" `seller_name` LIKE '%{$seller_name}%' ");
        }

        //Lọc theo seller_aliwang (nick chat trên site gốc)
        if($seller_aliwang != ""){
            $query->andWhere(" `seller_aliwang` LIKE '%{$seller_aliwang}%' ");
        }

        //Lọc theo seller_homeland (địa chỉ site gốc)
        if($seller_homeland != ""){
            $query->andWhere(" `seller_homeland` LIKE '%{$seller_homeland}%' ");
        }

        //Lọc theo seller_info (???)
        if($seller_info != ""){
            $query->andWhere(" `seller_info` LIKE '%{$seller_info}%' ");
        }

        //Lọc theo mã đơn hàng site gốc
        if($order_invoice != ""){
            $query->andWhere(" `order_invoice` LIKE '%{$order_invoice}%' ");
        }

        //Lọc theo user mua hàng
        if($order_buyer_id > 0){
            $query->andWhere("order_buyer_id = {$order_buyer_id}");
        }

        //Lọc theo lý do đòi tiền
        if($reason != ""){
            $tmp_reason = "";
            $reason = rtrim($reason, ",");
            $arrReason = explode(",", $reason);
            if(sizeof($arrReason) > 0){
                foreach((array)$arrReason as $r){
                    $tmp_reason .= "'" . $r . "',";
                }
            }
            $tmp_reason = rtrim($tmp_reason, ",");
//            echo '$tmp_reason: ' . $tmp_reason;
//            exit;
            $query->andWhere(" reason IN ({$tmp_reason}) ");
        }
//        echo $query->getSQL();
//        print_r($query);
//        exit;

        //Lọc theo acc mua hàng site gốc
        if($account_purchase_origin > 0 && $account_purchase_origin_name != ""){
            //Lấy ra toàn bộ đơn hàng theo user mua hàng này
            $q = \Order::read();
            $q->andWhere("account_purchase_origin = '{$account_purchase_origin_name}'");
            $orders = $q->execute()->fetchAll(\PDO::FETCH_CLASS, \Order::getPhpName(), array(null, false));
            $strOrders = "";
            foreach($orders as $key => $order){
                $strOrders .= $order->getId() . ",";
            }
            if($strOrders != ""){
                $strOrders = rtrim($strOrders, ",");
                $query->andWhere("order_id IN ({$strOrders})");
            }
        }

        $query_count = clone $query;
        //Điều kiện order
        switch ($status) {
            case \ComplaintSeller::STATUS_PENDING:
                $query->orderBy("created_time", "ASC");
                break;
            case \ComplaintSeller::STATUS_PROCESSING:
                $query->orderBy("refocus_time", "ASC");
                break;
            case \ComplaintSeller::STATUS_SUCCESS:
                $query->orderBy("accepted_time", "ASC");
                break;
            case \ComplaintSeller::STATUS_FAILURE:
                $query->orderBy("rejected_time", "ASC");
                break;
            default:
                $query->orderBy("refocus_time", "DESC");
                break;
        }

        if($all == 0){
            $query->setFirstResult($offset)->setMaxResults($per_page);
        }
//        echo $query->getSQL();
        $result = $query->execute()->fetchAll(\PDO::FETCH_CLASS, \ComplaintSeller::getPhpName(), array(null, false));
//        print_r($result);exit;


        $items = array();
        for($i = 0; $i < sizeof($result); $i++){
            $items[$i] = $result[$i]->toArray();
            $order = \Order::retrieveById($items[$i]['order_id']);
            if( !$order instanceof \Order ) {
                $order = new \Order();
            }

            //Get title
            $items[$i]['reason_title'] = self::getReasonTitle($items[$i]['reason']);
            $items[$i]['level_title'] = self::getLevelTitle($items[$i]['level']);
            $items[$i]['status_title'] = self::getStatusTitle($items[$i]['status']);
            //Format time
            $created_time = $accepted_time = $rejected_time = $refocus_time = $processed_time = "";
            if(strtotime($items[$i]['created_time']) && $items[$i]['created_time'] != "0000-00-00 00:00:00"){
                $_created_time = new \DateTime($items[$i]['created_time']);
                $created_time = $_created_time->format("d/m/Y");
            }
            if(strtotime($items[$i]['accepted_time']) && $items[$i]['accepted_time'] != "0000-00-00 00:00:00"){
                $_accepted_time = new \DateTime($items[$i]['accepted_time']);
                $accepted_time = $_accepted_time->format("d/m/Y");
            }
            if(strtotime($items[$i]['rejected_time']) && $items[$i]['rejected_time'] != "0000-00-00 00:00:00"){
                $_rejected_time = new \DateTime($items[$i]['rejected_time']);
                $rejected_time = $_rejected_time->format("d/m/Y");
            }

            if(strtotime($items[$i]['refocus_time']) && $items[$i]['refocus_time'] != "0000-00-00 00:00:00"){
                $_refocus_time = new \DateTime($items[$i]['refocus_time']);
                $refocus_time = $_refocus_time->format("d/m/Y");
            }

            if(strtotime($items[$i]['processed_time']) && $items[$i]['processed_time'] != "0000-00-00 00:00:00"){
                $_processed_time = new \DateTime($items[$i]['processed_time']);
                $processed_time = $_processed_time->format("d/m/Y");
            }

            //Hiển thị trạng thái màu sắc đối với những khiếu nại người bán đã đến hạn hoặc quá hạn xử lý
            $refocus_time_tmp = new \DateTime($items[$i]['refocus_time']);
            $refocus_time_tmp = $refocus_time_tmp->format("Y-m-d");
            $color = "";
            if($status == \ComplaintSeller::STATUS_PROCESSING){
                $color = strtotime($refocus_time_tmp) <= strtotime(date("Y-m-d")) ? "" : "delay";
            }
            $items[$i]['color'] = $color;

            $items[$i]['show_amount_seller_refund_by_status'] = $items[$i]['status'] == \ComplaintSeller::STATUS_SUCCESS ? true : false;
            //show num_day
            $items[$i]['diff_day'] = self::calDistanceBetweenDay($refocus_time_tmp, date("Y-m-d"));
            $items[$i]['show_diff_day'] = strtotime($refocus_time_tmp) > strtotime(date("Y-m-d")) ? true : false;

            $items[$i]['created_time'] = $created_time;
            $items[$i]['accepted_time'] = $accepted_time;
            $items[$i]['rejected_time'] = $rejected_time;
            $items[$i]['refocus_time'] = $refocus_time;
            $items[$i]['processed_time'] = $processed_time;

            //info order buyer (username khách)
            $buyer = \Users::retrieveById($items[$i]['order_buyer_id']);
            if($buyer){
                $items[$i]['buyer_info']['username'] = $buyer->getUsername();
            }
            //acc mua hàng
            $items[$i]['account_purchase_origin'] = $order->getAccountPurchaseOrigin();

            //icon site gốc
            $items[$i]['seller_favicon_site'] = \Common::getFaviconSite($order->getSellerHomeland());
            //Link site gốc
            $items[$i]['link_original_site'] = \Common::getItemLink($order->getSellerHomeland(), $items[$i]['order_invoice']);

            //Nếu thời gian cần xử lý
            $items[$i]['refocus_time_color'] = strtotime($refocus_time_tmp) <= strtotime(date("Y-m-d")) ? "font-red" : "";

            //Thông tin người phụ trách
            $items[$i]['show_info_processed'] = false;
            $processed = \Users::retrieveById($items[$i]['processed_by']);
            if($processed){
                $items[$i]['info_processed']['user_id'] = $processed->getId();
                $items[$i]['info_processed']['username'] = $processed->getUsername();
                $items[$i]['info_processed']['fullname'] = $processed->getFullName();
                $items[$i]['info_processed']['avatar'] = $processed->getAvatar32x($processed);
                $items[$i]['show_info_processed'] = true;
            }

            //Lấy link site gốc (TAOBAO, 1688)
            $dataSiteRoot = self::getLinkSiteRoot($order->getSellerHomeland(), $order->getInvoice());
            $items[$i]['show_link_site_root'] = $dataSiteRoot['show_link_site_root'];
            $items[$i]['arrLinkSiteRoot'] = $dataSiteRoot['arrLinkSiteRoot'];

            //status color
            $items[$i]['status_color'] = self::getStatusColor($items[$i]['status']);
        }

        $total_record = (int)$query_count->count('id')->execute();

        $total_page = $total_record % $per_page == 0 ? $total_record / $per_page
            : intval($total_record / $per_page) + 1;

        $total_status_all = $total_status_pendding = $total_status_processing = $total_status_success = $total_status_failure = 0;

        $q = \ComplaintSeller::select();
        $complaints_seller = $q->execute();

        if(sizeof($complaints_seller) > 0){
            foreach($complaints_seller as $item){
                if( $item instanceof \ComplaintSeller ) {
                    $total_status_all++;
                    switch ($item->getStatus()) {
                        case \ComplaintSeller::STATUS_PENDING:
                            $total_status_pendding++;
                            break;
                        case \ComplaintSeller::STATUS_PROCESSING:
                            $total_status_processing++;
                            break;
                        case \ComplaintSeller::STATUS_SUCCESS:
                            $total_status_success++;
                            break;
                        case \ComplaintSeller::STATUS_FAILURE:
                            $total_status_failure++;
                            break;
                    }
                }//end if
            }
        }

        return array('condition' => $condition,
                        'current_page' => $page,
                        'per_page' => $per_page,
                        'page' => $page,
                        'total_record' => $total_record,
                        'total_page' => $total_page,
                        'total_by_status' => array(
                            'all' => $total_status_all,
                            'pendding' => $total_status_pendding,
                            'processing' => $total_status_processing,
                            'success' => $total_status_success,
                            'failure' => $total_status_failure
                        ),
                        'items' => (array)$items);
    }

    public static function getStatusColor($status){
        $color = "";
        if($status == \ComplaintSeller::STATUS_PROCESSING
            || $status == \ComplaintSeller::STATUS_PENDING){
            $color = "font-red";
        }
        if($status == \ComplaintSeller::STATUS_SUCCESS){
            $color = "font-green";
        }
        if($status == \ComplaintSeller::STATUS_FAILURE){
            $color = "font-gray";
        }
        return $color;
    }

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

    public static function getBuyersByComplaintSeller(){
        $query = \ComplaintSeller::read();
        $query->select("order_buyer_id, order_buyer_code, order_buyer_username");
        $query->groupBy("order_buyer_id");
        $data = $query->execute()->fetchAll(\PDO::FETCH_CLASS, \ComplaintSeller::getPhpName(), array(null, false));
        for($i = 0; $i < sizeof($data); $i++){
            $data[$i] = $data[$i]->toArray();
            $order_buyer_username = "";
            $buyer = \Users::retrieveById($data[$i]['order_buyer_id']);
            if($buyer){
                $order_buyer_username = $buyer->getUsername();
            }
            $data[$i]['order_buyer_username'] = $order_buyer_username;
        }
        return $data;
    }

    public static function calDistanceBetweenDay($date1, $date2){
        //form datetime (2012-07-14)
        $first_date = strtotime($date1);
        $second_date = strtotime($date2);
        $datediff = abs($first_date - $second_date);
        return floor($datediff/(60*60*24));
    }

    /*
     * Thêm mới 1 khiếu nại người bán
     * input: $data[]
     * $data['reason']: Lý do khiếu nại
     * $data['level']: Mức độ khiếu nại với ( người bán, người quản lý hay trung gian )
     * $data['order_id']: ID đơn hàng
     * $data['description']: Mô tả khiếu nại
     * $data['refocus_time']: Thời gian "Hạn xử lý"
     * $data['created_by']: User tạo khiếu nại
     * output: (int)$complaint_seller_id
     * */
    public static function addComplaintSeller($data){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try{
            //format refocus_time
            if(strtotime($data['refocus_time'])){
                $refocus_time = new \DateTime($data['refocus_time']);
                $data['refocus_time'] = $refocus_time->format("Y-m-d H:i:s");
            }else{
                $data['refocus_time'] = date("Y-m-d H:i:s");
            }

            $order = false;
            $order = \Order::retrieveById($data['order_id']);
            if(!$order){
                return false;
            }

            $post = new \ComplaintSeller();
            $post->reason = $data['reason'];
            $post->level = $data['level'];
            $post->description = $data['description'];
            $post->refocus_time = $data['refocus_time'];
            $post->created_by = $data['created_by'];
            $post->status = $data['status'];
            $post->order_id = $data['order_id'];
            $post->order_code = $order->getCode();
            $post->order_invoice = $order->getInvoice();
            $post->order_buyer_id = $order->getBuyerId();
            $post->seller_name = $order->getSellerName();
            $post->seller_aliwang = $order->getSellerAliwang();
            $post->seller_homeland = $order->getSellerHomeland();
            $post->seller_info = $order->getSellerInfo();
            $post->created_time = date("Y-m-d H:i:s");

            $order_buyer_code = $order_buyer_username = "";
            $buyer = \Users::retrieveById($order->getBuyerId());
            if($buyer){
                $order_buyer_code = $buyer->getCode();
                $order_buyer_username = $buyer->getUsername();
            }
            $post->order_buyer_code = $order_buyer_code;
            $post->order_buyer_username = $order_buyer_username;

            /*
             * Khi tạo ở bước này mặc định người xử lý là người tạo. Về sau chúng ta có những KNNB tạo tự động
             * */
            $post->processed_by = $data['processed_by'];
            $post->processed_time = $data['processed_time'];

            if(!$post->save()){
                return false;
            }

            $complaint_seller_id = $post->getId();

            //Cập nhật trạng thái cho dơn hàng là dang khiếu nại người bán
            $put = \Order::retrieveById($data['order_id']);
            $put->setComplainSeller(1);
            $put->save();

            //Insert dữ liệu vào bảng complaint seller reason
            $reason = new \ComplaintSellerReason();
            $reason->complaint_seller_id = $complaint_seller_id;
            $reason->long_type = $data['reason'];
            $reason->create_time = date("Y-m-d H:i:s");
            $reason->save();

            $conn->commit();

            return $complaint_seller_id;
        }catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint_seller')->addError('has error when try to add complaint seller',array($e->getMessage()));
            $conn->rollBack();
            return false;
        }
    }

    /*
     * Hàm update thông tin khiếu nại người bán
     * input: $data[]
     * output: bool(true or false)
     * */
    public static function updateInfoComplaintSeller($data){
        $put = \ComplaintSeller::retrieveById($data['id']);
        $put->beginTransaction();
        try{
            if(isset($data['reason'])){
                $put->setReason($data['reason']);
            }
            if(isset($data['level'])){
                $put->setLevel($data['level']);
            }
            if(isset($data['status'])){
                $put->setStatus($data['status']);
            }
            if(isset($data['order_id'])){
                $put->setOrderId($data['order_id']);
            }
            if(isset($data['order_code'])){
                $put->setOrderCode($data['order_code']);
            }
            if(isset($data['order_invoice'])){
                $put->setOrderInvoice($data['order_invoice']);
            }
            if(isset($data['order_buyer_id'])){
                $put->setOrderBuyerId($data['order_buyer_id']);
            }
            if(isset($data['seller_name'])){
                $put->setSellerName($data['seller_name']);
            }
            if(isset($data['seller_aliwang'])){
                $put->setSellerAliwang($data['seller_aliwang']);
            }
            if(isset($data['seller_homeland'])){
                $put->setSellerHomeland($data['seller_homeland']);
            }
            if(isset($data['seller_info'])){
                $put->setSellerInfo($data['seller_info']);
            }
            if(isset($data['description'])){
                $put->setDescription($data['description']);
            }
            if(isset($data['amount_seller_refund'])){
                $put->setAmountSellerRefund($data['amount_seller_refund']);
            }
            if(isset($data['created_by'])){
                $put->setCreatedBy($data['created_by']);
            }
            if(isset($data['accepted_by'])){
                $put->setAcceptedBy($data['accepted_by']);
            }
            if(isset($data['rejected_by'])){
                $put->setRejectedBy($data['rejected_by']);
            }
            if(isset($data['created_time'])){
                $put->setCreatedTime($data['created_time']);
            }
            if(isset($data['accepted_time'])){
                $put->setAcceptedTime($data['accepted_time']);
            }
            if(isset($data['rejected_time'])){
                $put->setRejectedTime($data['rejected_time']);
            }
            if(isset($data['refocus_time'])){
                $put->setRefocusTime($data['refocus_time']);
            }
            if(isset($data['flag'])){
                $put->setFlag($data['flag']);
            }
            $put->save();
            $put->commit();
            return true;
        }catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('order')->addError('has error when try to update complaint seller',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update complaint seller');
        }
    }

    public static function checkExistComplaintSellerByOrderId($order_id){
        $query = \ComplaintSeller::read();
        $query->andWhere('order_id', $order_id);
        $total = $query->count('id')->execute();
        if($total > 0){
            return true;
        }else{
            return false;
        }
    }

    //Hàm khởi tạo lại toàn bộ dữ liệu lý do trong bảng KNNB
    public static function reBuildReasonComplaintSeller(){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try {
            $query = \ComplaintSeller::read();
            $data = $query->execute()->fetchAll(\PDO::FETCH_CLASS, \ComplaintSeller::getPhpName(), array(null, false));
            foreach($data as $item){
                if($item instanceof \ComplaintSeller){
                    $post = new \ComplaintSellerReason();
                    $post->complaint_seller_id = $item->getId();
                    $post->long_type = $item->getReason();
                    $post->create_time = date("Y-m-d H:i:s");
                    $post->save();
                }
            }
            $conn->commit();
            return true;
        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('order')->addError('has error when try to rebuild reason complaint seller',array($e->getMessage()));
            $conn->rollBack();
            throw new \Flywheel\Exception('has error when try to rebluid reason complaint seller');
        }
    }

    //Hàm tạo 1 vài dữ liệu mẫu test danh sách khiếu nại người bán
    public static function buildDataComplaintSeller(){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try {
            $record = 100;
            for($i = 1; $i <= $record; $i++){
                self::addComplaintSeller(array(
                    'reason' => 'PRODUCT_NOT_RECEIVED',//Không thấy hàng
                    'level' => 'SELLER',//Khiếu nại với người bán
                    'order_id' => $i,
                    'description' => 'nhap mo ta o day',
                    'refocus_time' => '11/06/2014',
                    'created_by' => 5
                ));
            }
            $conn->commit();
            return true;
        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('order')->addError('has error when try to build data complaint seller',array($e->getMessage()));
            $conn->rollBack();
            throw new \Flywheel\Exception('has error when try to build data complaint seller');
        }

    }
}