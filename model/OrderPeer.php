<?php
use Flywheel\Db\Query;
use Flywheel\Db\Type\DateTime;
use Flywheel\Redis\Client;

/**
 * Order
 * @version        $Id$
 * @package        Model
 */

//require_once dirname(__FILE__) . '/Base/OrderBase.php';

class OrderPeer //extends \OrderBase
{
    const REDIS_COD = "order_cod";
    const STATUS_INIT = 'INIT', // => Khởi tạo
        STATUS_DEPOSITED = 'DEPOSITED', // => đã đặt cọc
        STATUS_BUYING = 'BUYING', // -  đang mua
        STATUS_NEGOTIATING = 'NEGOTIATING', // - đang đàm phán
        STATUS_NEGOTIATED = 'NEGOTIATED', // - đã đàm phán
        STATUS_BOUGHT = 'BOUGHT', // - đã mua
        STATUS_SELLER_DELIVERED = 'SELLER_DELIVERED', // - người bán đã giao
        STATUS_CHECKING = 'CHECKING', // - đang kiểm hàng
        STATUS_CHECKED = 'CHECKED', // -  đã kiểm
        STATUS_DELIVERED = 'DELIVERED', // - đã giao
        STATUS_GET = 'GET', // - đã nhận
        STATUS_OUT_OF_STOCK = 'OUT_OF_STOCK';

    public static $button_purchase = array(
        \Order::STATUS_BUYING => 'Đang mua',
        \Order::STATUS_NEGOTIATING => 'Đã mua hàng',
        \Order::STATUS_NEGOTIATED => 'Thanh toán',
    );

    /**
     * @param $that
     * @param array $condition
     * @return array
     * @throws Exception
     * @throws Flywheel\Exception
     */
    public static function getListOrders($that, $condition = array()){
        $tmpDateFrom = $tmpDateTo = "";
        if($condition['limit'] == 0){
            $per_page = \Order::PER_PAGE;
        }else{
            $per_page = $condition['limit'];
        }
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try{
            $ordering = "created_time";
            if($condition['ordering'] != ""){
                $ordering = self::executeOrdering($condition['ordering']);
            }

            $where_group = [];
            $query = \Order::select();
            $query->addSelect("IFNULL(account_purchase_origin, '') AS account_purchase_origin,
                                IFNULL(avatar, '') AS avatar,
                                IFNULL(note_customer_confirm, '') AS note_customer_confirm,
                                IFNULL(deposit_ratio, '') AS deposit_ratio,
                                IFNULL(real_surcharge, '') AS real_surcharge,
                                IFNULL(direct_fill_amount_cny, '') AS direct_fill_amount_cny,
                                IFNULL(direct_fill_amount_vnd, '') AS direct_fill_amount_vnd,
                                IFNULL(payment_link, '') AS payment_link,
                                IFNULL(invoice, '') AS invoice,
                                IFNULL(alipay, '') AS alipay,
                                IFNULL(current_warehouse, '') AS current_warehouse,
                                IFNULL(next_warehouse, '') AS next_warehouse,
                                IFNULL(warehouse_status, '') AS warehouse_status,
                                IFNULL(transport_status, '') AS transport_status,
                                IFNULL(warning_score, '') AS warning_score,
                                IFNULL(name_recipient_origin, '') AS name_recipient_origin,
                                IFNULL(code, '') AS code");

            if($condition['homeland'] != ""){
                $query->andWhere(" `seller_homeland` = '{$condition['homeland']}' ");
            }

            if($condition['date_from'] != ""){
                $arrDateFrom = explode("/", $condition['date_from']);
                $tmpDateFrom = $arrDateFrom[2] . "-" . $arrDateFrom[1] . "-" . $arrDateFrom[0] . " 00:00:00";
            }

            if($condition['date_to'] != ""){
                $arrDateTo = explode("/", $condition['date_to']);
                $tmpDateTo = $arrDateTo[2] . "-" . $arrDateTo[1] . "-" . $arrDateTo[0] . " 23:59:59";
            }

            if($condition['date_from'] != "" && $condition['date_to'] == ""){
                $query->andWhere($ordering . " >= '{$tmpDateFrom}'");
            }

            if($condition['date_to'] != "" && $condition['date_from'] == ""){
                $query->andWhere($ordering . " <= '{$tmpDateTo}'");
            }

            if($condition['date_from'] != "" && $condition['date_to'] != ""){
                $query->andWhere($ordering . " >= '{$tmpDateFrom}' AND " . $ordering . " <= '{$tmpDateTo}'");
            }

            if($condition['status'] != ""){
                $arrStatus = array();
                $tmpStatus = explode(",", $condition['status']);
                if(sizeof($tmpStatus) > 0){
                    foreach($tmpStatus as $s){
                        $arrStatus[] = '"' . $s . '"';
                    }
                }

                if(sizeof($arrStatus) > 0){
                    $query->andWhere("`status` IN (" . implode(",", $arrStatus) . ")");
                }
            }

            if($condition['customer'] != ""){
                $arrCustomer = array();
                //TH1: Tìm theo kiếm ID
                //TH2: Tìm theo mã hoặc username
                if( is_numeric( $condition['customer'] ) ){
                    $arrCustomer[] = (int)$condition['customer'];
                }else{
                    $users = \UsersPeer::searchByCodeOrUsername( $condition['customer'], $condition['customer'] );
                    if(sizeof($users) > 0){
                        for($u = 0; $u < sizeof($users); $u++){
                            $arrCustomer[] = $users[$u]->getId();
                        }
                    }
                }
                if(sizeof($arrCustomer) > 0){
                    $query->andWhere(" `buyer_id` IN (" . implode(",", $arrCustomer) . ") ");
                } else {
                    $query->andWhere(" `id` = 0 ");
                }
            }

            //Không có cân nặng
            if( $condition['no_weight'] != '' ) {
                $query->andWhere( " weight = 0 " );
            }

            //Tìm theo acc mua hàng
            if($condition['user_origin_site'] != ""){
                $condition['user_origin_site'] = trim($condition['user_origin_site']);
                $query->andWhere( " account_purchase_origin = '{$condition['user_origin_site']}' " );
            }

            //Vận chuyển
            if( $condition['current_warehouse'] != "" ) {
                $condition['current_warehouse'] = trim($condition['current_warehouse']);
                $query->andWhere( " `current_warehouse` = '{$condition['current_warehouse']}' " );
            }

            if( $condition['destination_warehouse'] != "" ) {
                $condition['destination_warehouse'] = trim($condition['destination_warehouse']);
                $query->andWhere( " `destination_warehouse` = '{$condition['destination_warehouse']}' " );
            }

            //Trạng thái nhập kho / xuất kho
            if( $condition['warehouse_status_in'] != "" && $condition['warehouse_status_out'] == "" ) {
                $where_group[] = " `warehouse_status` = '{$condition['warehouse_status_in']}' ";
//                $query->andWhere( " `warehouse_status` = '{$condition['warehouse_status_in']}' " );
            }

            if( $condition['warehouse_status_in'] == "" && $condition['warehouse_status_out'] != "" ) {
                $where_group[] = " `warehouse_status` = '{$condition['warehouse_status_out']}' ";
//                $query->andWhere( " `warehouse_status` = '{$condition['warehouse_status_out']}' " );
            }

            if( $condition['warehouse_status_in'] != "" && $condition['warehouse_status_out'] != "" ) {
                $where_group[] = " `warehouse_status` = '{$condition['warehouse_status_in']}' ";
                $where_group[] = " `warehouse_status` = '{$condition['warehouse_status_out']}' ";
//                $warehouse_status_in = \Order::WAREHOUSE_STATUS_IN;
//                $warehouse_status_out = \Order::WAREHOUSE_STATUS_OUT;
//                $query->andWhere( " `warehouse_status` IN ('{$warehouse_status_in}', '{$warehouse_status_out}') " );
            }

            //Vận đơn
            if( $condition['is_have_bill_lading'] != "" ) {
                $where_group[] = " `has_freight_bill` = 1 ";
            }
            if( $condition['is_no_bill_lading'] != "" ) {
                $where_group[] = " `has_freight_bill` = 0 ";
            }

            //KNNB
            if($condition['is_complaint_seller'] != ""){
                $where_group[] = " `complain_seller` = 1 ";
//                $query->andWhere(" `complain_seller` = 1 ");
            }

            //KNDV
            if ( $condition['is_complaint'] != "" ) {
                //Lọc theo những khiếu nại đang được xử lý
                $oIds = \Complaints::getOrdersByComplaint();
                if( sizeof($oIds) > 0 ) {
                    $where_group[] = " `id` IN (" . implode(',', $oIds) . ") ";
                }
            }

            //CPN
            $arrServiceCode = array();
            if ( $condition['is_cpn'] != '' ) {
                $arrServiceCode[] = "'" . \Services::TYPE_EXPRESS_CHINA_VIETNAM . "'";
            }

            //Có kiểm
            if ( $condition['is_need_checking'] != '' ) {
                $arrServiceCode[] = "'" . \Services::TYPE_CHECKING . "'";
            }

            //Đóng gỗ
            if ( $condition['is_check_wood_crating'] != '' ) {
                $arrServiceCode[] = "'" . \Services::TYPE_WOOD_CRATING . "'";
            }

            if ( sizeof($arrServiceCode) > 0 ) {
                $orderIds = \OrderService::getOrdersByOrderServiceCode( $arrServiceCode );
                if( sizeof($orderIds) > 0 ) {
                    $where_group[] = " `id` IN (" . implode(',', $orderIds) . ") ";
                }
            }

            if( $condition['freight_bill'] != "" ){
                $packages = \Packages::searchByFreightBill( $condition['freight_bill'] );
                $arrPackage = array();
                if( sizeof($packages) > 0 ){
                    for( $p = 0; $p < sizeof($packages); $p++ ) {
                        $arrPackage[] = $packages[$p]->getOrderId();

                    }
                }

                if( sizeof($arrPackage) > 0 ){
                    $query->andWhere(" `id` IN (" . implode(",", $arrPackage) . ") ");
                } else {
                    //Không show kết quả nào cả
                    $query->andWhere(" `id` = 0 ");
                }
            }

            if( sizeof($where_group) > 0 ) {
                $query->andWhere( " ( " . implode(" OR ", $where_group) . " ) " );
            }

            if($condition['keyword'] != ""){
                //TH1: Tìm theo kiếm ID
                //TH2: Tìm theo ...
                $keyword = $condition['keyword'];
                if( is_numeric( $keyword ) ){
                    $keyword = (int)$keyword;
                    $query->andWhere("id = {$keyword}");
                } else {
                    $query->andWhere("`code` LIKE '%{$keyword}%'
                        OR `account_purchase_origin` LIKE '%{$keyword}%'
                        OR `invoice` LIKE '%{$keyword}%'");
                }
            }

            //Tìm theo aliwang
            if( $condition['seller_aliwang'] != "" ) {
                $query->andWhere( " `seller_aliwang` LIKE '%{$condition['seller_aliwang']}%' " );
            }

            //Chờ khách xác nhận mua
            if( $condition['wait_customer_confirm'] != "" ) {
                $query->andWhere( " `customer_confirm` = '{$condition['wait_customer_confirm']}' " );
            }

            //Đồng bộ dữ liệu
            if($condition['sync'] == 1 && $condition['last_modified_time'] != ""){
                $query->andWhere(" `modified_time` >= '{$condition['last_modified_time']}'");
            }

            if($condition['sort_order'] != ""){
                $query->orderBy($ordering, $condition['sort_order']);
            }

            $q = clone $query;
            $total = (int)$q->count()->execute();

            if($condition['all'] == 0){
                $start = ($condition['page'] - 1) * $per_page;
                $query->setFirstResult($start)->setMaxResults($per_page);
            }

            $orders = $query->execute();

            $result = $arrOrderId = array();

            foreach( $orders as $order ) {
                if( $order instanceof \Order ) {
                    $arrOrderId[] = $order->getId();
                    $weight = $order->getWeight();
                    $need_checking = $order->needToChecking();
                    $check_wood_crating = $order->needToWoodCrating();

                    $data = $order->toArray();

                    $data['packages'] = array();
                    $packages = \Packages::findByOrderId($order->getId());
                    if(sizeof($packages) > 0){
                        foreach($packages as $package){
                            $data['packages'][] = $package->toArray();
                        }
                    }

                    if(sizeof($data['packages']) == 0){
                        $data['packages'] = new \stdClass();
                    }

                    if($that->isAllowed(PERMISSION_USER_VIEW)){
                        $buyer = \Users::retrieveById($order->getBuyerId());
                        if($buyer){
                            $data['buyer'] = $buyer->getAttributes('id, username, code, last_name, first_name');
                            $data['buyer']['shorten_fullname'] = $buyer->getShortenFullName();
                            $data['buyer']['avatar'] = \Users::getAvatar32x($buyer);
                            $data['buyer']['detail_link'] = $that->createUrl( 'user/detail', array('id' => $buyer->getId()) );
                        }
                    }

                    $teller = \Users::retrieveById($order->getTellersId());
                    if($teller){
                        $data['teller'] = $teller->getAttributes('id, username, code, last_name, first_name');
                        $data['teller']['shorten_fullname'] = $teller->getShortenFullName();
                        $data['teller']['avatar'] = \Users::getAvatar32x($teller);
                        $data['teller']['detail_link'] = $that->createUrl( 'user/detail', array('id' => $teller->getId()) );
                    }

                    $payment = \Users::retrieveById($order->getPaidStaffId());
                    if ($payment) {
                        $data['payment'] = $payment->getAttributes('id, username, code, last_name, first_name');
                        $data['payment']['shorten_fullname'] = $payment->getShortenFullName();
                        $data['payment']['avatar'] = \Users::getAvatar32x($payment);
                        $data['payment']['detail_link'] = $that->createUrl( 'user/detail', array('id' => $payment->getId()) );
                    }

                    $data['detail_link'] = $that->createUrl( 'order/detail', array('id' => $order->getId()) );

                    if($weight){
                        $data['weight'] = number_format($weight, 2);
                    }
                    $data['need_checking'] = $need_checking;
                    $data['check_wood_crating'] = $check_wood_crating;

                    $data['seller_favicon_site'] = \Common::getFaviconSite($order->getSellerHomeland());
                    $data["is_cpn"] = $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);

                    //need checking ?
                    $data['need_checking'] = $order->needToChecking();

                    $data['check_wood_crating'] = $order->needToWoodCrating();

                    $data['check_fragile'] = $order->needToFragile();

                    $data['is_warehouse_status_in'] = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_IN;
                    $data['is_warehouse_status_out'] = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_OUT;

                    $data['is_deposit_time'] = $order->getDepositTime() != '0000-00-00 00:00:00';
                    $data['is_paid_staff_assigned_time'] = $order->getPaidStaffAssignedTime() != '0000-00-00 00:00:00';
                    $data['is_negotiated_time'] = $order->getNegotiatedTime() != '0000-00-00 00:00:00';
                    $data['is_bought_time'] = $order->getBoughtTime() != '0000-00-00 00:00:00';
                    $data['is_seller_delivered_time'] = $order->getSellerDeliveredTime() != '0000-00-00 00:00:00';
                    $data['is_received_from_seller_time'] = $order->getReceivedFromSellerTime() != '0000-00-00 00:00:00';
                    $data['is_transporting_time'] = $order->getSellerDeliveredTime() != '0000-00-00 00:00:00';
                    $data['is_checked_time'] = $order->getCheckedTime() != '0000-00-00 00:00:00';
                    $data['is_waiting_delivery_time'] = $order->getWaitingDeliveryTime() != '0000-00-00 00:00:00';
                    $data['is_confirm_delivery_time'] = $order->getConfirmDeliveryTime() != '0000-00-00 00:00:00';
                    $data['is_delivered_time'] = $order->getDeliveredTime() != '0000-00-00 00:00:00';
                    $data['is_received_time'] = $order->getReceivedTime() != '0000-00-00 00:00:00';

                    $data['order_status_color'] = $order->getColorByOrderStatus();
                    $total_amount_cny = $order->getExchange() > 0 ? $order->getTotalAmount() / $order->getExchange() : 0;
                    $data['total_amount_cny'] = round($total_amount_cny, 2);

                    $total_amount = $order->getTotalAmount();
                    $data['total_amount'] = $total_amount;

                    $data['is_complaint'] = \Complaints::checkOrderIsExistComplaint( $order->getOrderId() );

                    $data['show_customer_confirm'] = $order->getCustomerConfirm() == \Order::CUSTOMER_CONFIRM_WAIT;

                    $time_left_customer_confirm = 0;
                    if( $order->getConfirmCreatedTime() != '0000-00-00 00:00:00' ) {
                        $time_left_customer_confirm = \Common::getDateDiff( $order->getConfirmCreatedTime(), date('Y-m-d H:i:s') );
                    }
                    $data['time_left_customer_confirm'] = $time_left_customer_confirm;
                    $data['show_time_left_customer_confirm'] = $time_left_customer_confirm > 0;
                    $data['show_note_customer_confirm'] = $order->getNoteCustomerConfirm() != "";

                    //avatar
                    $data['avatar'] = $order->getAvatar() ? $order->getAvatar() :  \SeuDo\Main::getBackendUrl() . 'assetv2/images/noimg.gif';
                    //Nếu chưa tồn tại avatar thì tạo mới và cập nhật vào cơ sở dữ liệu
                    if( !$order->getAvatar() ) {
                        $order->getOrderAvatar();
                    }

                    $result[] = $data;
                }
            }

            $total_page = 0;
            if($total > 0){
                $total_page = $total % $per_page == 0 ? $total / $per_page
                    : intval($total / $per_page) + 1;
            }

            $conn->commit();
            return array('orders' => $result,
                'total' => $total,
                'total_page' => $total_page,
                'arrOrderId' => $arrOrderId,
                'SQL' => $query->getSQL());
        }catch (\Flywheel\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @param $order
     * @return string
     */
    public static function executeOrdering($order){
        $ordering = "";
        switch($order) {
            case \Order::STATUS_DEPOSITED:
                $ordering = 'deposit_time';
                break;
            case \Order::STATUS_BUYING:
                $ordering = 'buying_time';
                break;
            case \Order::STATUS_NEGOTIATING:
                $ordering = 'negotiating_time';
                break;
            case \Order::STATUS_WAITING_BUYER_CONFIRM:
            case \Order::STATUS_NEGOTIATED:
                $ordering = 'negotiated_time';
                break;
            case \Order::STATUS_BOUGHT:
            case \Order::STATUS_SELLER_DELIVERY:
                $ordering = 'bought_time';
                break;
            case \Order::STATUS_RECEIVED_FROM_SELLER:
                $ordering = 'received_from_seller_time';
                break;
            case \Order::STATUS_CHECKING:
                $ordering = 'checking_time';
                break;
            case \Order::STATUS_CHECKED:
                $ordering = 'checked_time';
                break;
            case \Order::STATUS_TRANSPORTING:
            case \Order::STATUS_WAITING_FOR_DELIVERY:
                $ordering = 'current_warehouse_time';
                break;
            case \Order::STATUS_RECEIVED:
                $ordering = 'received_time';
                break;
            case \Order::STATUS_DELIVERING:
                $ordering = 'delivered_time';
                break;
            case \Order::STATUS_OUT_OF_STOCK:
                $ordering = 'out_of_stock_time';
                break;
            case \Order::STATUS_CANCELLED:
                $ordering = 'cancelled_time';
                break;
            case \Order::STATUS_INIT:
                $ordering = 'created_time';
                break;
        }

        return $ordering;
    }

    /**
     * Get Between Status
     * @param $start
     * @param $end
     * @return array
     */
    public static function getBetweenStatus($start,$end){
        if($start == '' || $end == ''){
            return array();
        }

        $status_array = array();

        $key_start = array_search($start,\Order::$statusLevel);
        $key_end = array_search($end,\Order::$statusLevel);

        if($key_start < $key_end){
            for ($i = $key_start ; $i <= $key_end ;$i++) {
                $status_array[] = \Order::$statusLevel[$i];
            }

            return $status_array;
        }
        return array();
    }

    /**
     * get Left Status
     * @param $status
     * @return array Status
     */
    public static function getBeforeStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status,\Order::$statusLevel);

        for ($i = $key-1 ; $i >= 0 ;$i--) {
            $status_array[] = \Order::$statusLevel[$i];
        }

        return $status_array;
    }

    /**
     * get Right Status
     * @param $status
     * @return array Status
     */
    public static function getAfterStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status,\Order::$statusLevel);

        for ($i = $key+1 ; $i < count(\Order::$statusLevel) ;$i++) {
            $status_array[] = \Order::$statusLevel[$i];
        }

        return $status_array;
    }

    // - hết hàng

    /**
     * @param $order
     * @param Query $query
     * @return \OrderComment[]
     */
    public static function getOrderComment($order, Query $query = null)
    {
        if ($query == null) $query = \OrderComment::read();
        if (is_object($order) && $order instanceof \Order) {

            $query->andWhere('order_id=' . $order->getId());
        } else if (is_numeric($order)) {
            $query->andWhere('order_id=' . $order);
        }
        $query->orderBy('id', 'DESC');
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \OrderComment::getPhpName(), array(null, false));
    }

    /**
     * Get order items
     * @param $order
     * @param Query $query
     * @return \OrderItem[]
     */
    public static function getOrderItem($order, Query $query = null) {
        if ($order instanceof \Order) {
            $order = $order->getId();
        }

        if (null == $query) {
            return \OrderItem::findByOrderId($order);
        } else {
            $query->setSelectQueryCallback(array('Order', 'selectQueryCallback'));
            return $query->andWhere('`order_id` = :order_id')
                ->setParameter(':order_id', $order, \PDO::PARAM_INT)
                ->execute();
        }
    }

    /**
     * @param $order
     * @param Query $query
     * @return array
     */
    public static function getOrderServices($order, Query $query = null)
    {
        if ($query == null) $query = \OrderService::read();

        if (is_object($order) && $order instanceof \Order) {
            $query->andWhere('order_id=' . $order->getId());
        } else if (is_numeric($order)) {
            $query->andWhere('order_id=' . $order);
        }
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \OrderService::getPhpName(), array(null, false));
    }

    public static function addOrderComment(\Order $order, $user, $content, $type = OrderComment::TYPE_EXTERNAL)
    {

        $orderComment = new OrderComment();
        $orderComment->setNew(true);
        $orderComment->setContent($content);
        $orderComment->setOrderId($order->getId());
        $orderComment->setType($type);
        if(is_object($user) && ($user instanceof \Users)){
            $orderComment->setCreatedBy($user->getId());
        }else{
            $orderComment->setCreatedBy($user);
        }
        $orderComment->setCreatedTime(new DateTime());
        $orderComment->save();

        return ($orderComment->getId() > 0);

    }

    /**
     * @param Query $query
     * @param int $is_deleted
     * @return \Order[]
     */
    public static function getOrder(Query $query = null, $is_deleted = 0)
    {
        if ($query == null) $query = Order::read();

        if ($is_deleted == 0) {
            $query->andWhere('is_deleted=0');
        }
//        echo $query;
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \Order::getPhpName(), array(null, false));
    }


    public static function getOrderByUser($user, Query $query = null)
    {

        if ($user instanceof \Users) {
            $userId = $user->getId();
        } else {
            $userId = $user;
        }
        if (!$query) $query = \Order::read();
        $query->andWhere('buyer_id=' . $userId);
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \Order::getPhpName(), array(null, false));

    }


    public static function countOrder(Query $query = null,$is_deleted = false)
    {
        if ($query == null) $query = Order::read();

        if($is_deleted == false){
            $query->andWhere('is_deleted=0');
        }
        $data = $query->count("id")->execute();
        return $data;
    }

    public static function countOrderByStatus($status = array(), Query $query = null)
    {
        $result = array();
        if (!empty($status)) {
            foreach ($status as $st) {

                if ($query == null) $query = \Order::read();

                $query->andWhere('status="' . $st . '"');

                $result[$st] = static::countOrder($query);
            }
        }
        return $result;
    }

    public static function countOrderIsDelete(Query $query = null)
    {
        if ($query == null) $query = Order::read();
        $query->andWhere('is_deleted=1');
        $data = $query->count()->execute()->fetch();
        return $data['total'];
    }


    /**
     * @param array $orders
     * @return array
     */
    public static function buildOrderData($orders = array())
    {
        $result = array();
        if (!empty($orders)) :
            foreach ($orders as $order) {
                if ($order instanceof \Order) {
                    $orderItem = self::getOrderItem($order);
                    $orderComment = self::getOrderComment($order);

                    $result[$order->getId()] = array(
                        'order' => $order,

                        'buyer' => \Users::retrieveById($order->getBuyerId()),
                        'teller' => \Users::retrieveById($order->getTellersId()),
                        'paid_staff' => \Users::retrieveById($order->getPaidStaffId()),
                        'orderItem' => $orderItem,
                        'orderComment' => $orderComment,
                    );
                }
            }
        endif;

        return $result;
    }

    public static function buildArrayOrderPurchase($orders = array()){
        $result = array();
        if (!empty($orders)) :
            foreach ($orders as $order) {
                if ($order instanceof \Order) {
                    $order_array = self::buildArrayOrderData($order);
                    $orderItem = self::getOrderItem($order);

                    $order_item = array();

                    if($order->getCustomerConfirm() == \Order::CUSTOMER_CONFIRM_WAIT){
                        $is_customer_confirm = 1;
                    }else{
                        $is_customer_confirm = 0;
                    }

                    if(!empty($orderItem)){
                        foreach ($orderItem as $item) {
                            // dau's
//                        load orderItemComments
                            $item_comment = OrderItemComment::loadOrderItemComments($order->getId(), $item->getId(), 1, 3);
                            if ($item_comment['page'] < $item_comment['pages']) {
                                $item_comment['page_next'] = $item_comment['page'] + 1;
                            }

                            $array = $item->toArray();
                            $array["item_comment"] = $item_comment;
                            $array["is_customer_confirm"] = $is_customer_confirm;


                            $price_cny = $item->getPricePromotion() > 0 ? $item->getPricePromotion() :
                                $item->getPriceOrigin();
                            if(floatval($price_cny) <= 0){
                                $price_cny = floatval($item->getPricePromotion()) > 0 ? $item->getPricePromotion() : floatval($item->getPriceOrigin());
                                $price_cny = $price_cny > 0 ? $price_cny : $item->getPrice() / \ExchangeRate::getExchange();
                            }

                            $array["total_price_ndt"] = number_format($price_cny*$item->getPendingQuantity(),2,',','.');
                            $array["total_price_vnd"] = Common::numberFormat($item->getPrice()*$item->getPendingQuantity());

                            $array["unit_price_ndt"] = is_numeric($price_cny) ? number_format($price_cny,2,',','.') : 0;
                            $array["unit_price_vnd"] = Common::numberFormat($item->getPrice());
                            $array["title_sub"] = Common::subString($item->getTitleShow(),80);
                            $array["is_autopaid"] = $order->getSellerHomeland() == CartItem::TAOBAO_SITE ||
                            $order->getSellerHomeland() == CartItem::TMALL_SITE ? 1 : 0;
                            $array["link"] = $item->getItemLink();
                            $order_item[] = $array;
                        }
                    }

                    /*
                    $external_comments = OrderComment::loadOrderComments($order->getId(), OrderItemComment::TYPE_EXTERNAL,
                        \mongodb\OrderCommentResource\BaseContext::TYPE_CHAT);
                    $internal_comments = OrderComment::loadOrderComments($order->getId(), OrderItemComment::TYPE_INTERNAL,
                        \mongodb\OrderCommentResource\BaseContext::TYPE_CHAT);
                    */

                    $paid_staff = \Users::retrieveById($order->getPaidStaffId());

                    $avatar_paid_staff = \Users::getAvatar32x($paid_staff);

                    if($paid_staff instanceof \Users){
                        $paid_staff = $paid_staff->toArray();
                        $paid_staff['avatar'] = $avatar_paid_staff;
                    }

                    $buyer = \Users::retrieveById($order->getBuyerId());

                    $avatar_buyer = \Users::getAvatar32x($buyer);

                    if($buyer instanceof \Users){
                        $link_detail_backend = \SeuDo\Main::getBackendRouter()->createUrl("user/detail",array("id"=>$buyer->getId()));
                        $buyer = $buyer->toArray();
                        $buyer["avatar"] = $avatar_buyer;
                        $buyer["link_detail_backend"] = $link_detail_backend;
                    }

                    $teller = \Users::retrieveById($order->getTellersId());

                    $avatar_teller = \Users::getAvatar32x($teller);

                    if($teller instanceof \Users){
                        $teller = $teller->toArray();
                        $teller["avatar"] = $avatar_teller;
                    }

                    $account_origin = UserOriginSite::findBySite(strtolower($order->getSellerHomeland()));

                    if(empty($account_origin) && $order->getSellerHomeland() == "1688"){
                        $account_origin = UserOriginSite::findBySite("alibaba");
                    }

                    $account_origin_list =array();

                    if(!empty($account_origin)){
                        foreach ($account_origin as $account) {
                            if($account instanceof UserOriginSite){
                                $array = $account->toArray();
                                if($account->getUsername() == $order->getAccountPurchaseOrigin()){
                                    $array["is_selected"] = 1;
                                }else{
                                    $array["is_selected"] = 0;
                                }
                                $account_origin_list[] = $array;
                            }
                        }
                    }

                    if($account_origin_list instanceof UserOriginSite){
                        $account_origin_list->toArray();
                    }

                    $result[$order->getNameRecipientOrigin()][] = array(
                        'order' => $order_array,
                        'buyer' => $buyer,
                        'tellers' => $teller,
                        'paid_staff' => $paid_staff,
                        'order_items' => $order_item,
                        // dau's
//                        'internal_comments' => $internal_comments,
//                        'external_comments' => $external_comments,
                        'account_origin_list' => $account_origin_list
                    );
                }
            }
        endif;

        return $result;
    }

    /**
     * Build Array Order Data Using Handlebars js
     * @param Order $order
     * @internal param \Order $orders
     * @return array
     */
    public static function buildArrayOrderData(\Order $order){
        if ($order instanceof \Order) {
            $services_list = \OrderService::findByOrderId($order->getId());

            $text_show_button = "";

            if(array_key_exists($order->getStatus(), self::$button_purchase)) {
                $is_show_button = 1;
                $text_show_button =  self::$button_purchase[$order->getStatus()];
            }else{
                $is_show_button = 0;
            }
            if($order->getCustomerConfirm() == \Order::CUSTOMER_CONFIRM_WAIT){
                $is_show_button = 0;
                $is_customer_confirm = 1;
            }else{
                $is_customer_confirm = 0;
            }

            $status_title = $order->getStatusTitle();

            $total_item = $order->getCountItem();

            $total_quantity = $order->getTotalPendingQuantity();

            $order_price_ndt = number_format($order->getRealAmountNdt(),2,',','.');

            $not_nagotiated = $order->getStatus() == \Order::STATUS_NEGOTIATED ? 0 : 1;
            $is_autopaid = $order->getSellerHomeland() == CartItem::TAOBAO_SITE ||
            $order->getSellerHomeland() == CartItem::TMALL_SITE ? 1 : 0;
            $is_checking = $order->needToChecking();
            $is_wood_crating = $order->mappingToService(\Services::TYPE_WOOD_CRATING);
            $is_fragile = $order->mappingToService(\Services::TYPE_FRAGILE);
            $is_cpn = $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM);
            $is_high = $order->mappingToService(\Services::TYPE_HIGH_VALUE);
            $address_receive = $order->createAddressReceive();
            $missing_amount = -(($order->getTotalAmount() + $order->getRealRefundAmount())
                - $order->getRealPaymentAmount());
            if($order->getNameRecipientOrigin() == ""){
                $order->updateOrderRecipientName();
            }

            $detail_link = \SeuDo\Main::getUserRouter()->createUrl("order_detail/default",array("id"=>$order->getId()));
            $backend_detail_link = \SeuDo\Main::getRouter("backend")->createUrl("order/detail",array("id"=>$order->getId()));

            $warehouse_status = $order->getWarehouseStatusTitle();
            $order = $order->toArray();

            $order["warehouse_status_title"] = $warehouse_status;
            $order["link"] = $warehouse_status;

            $order['favicon_site'] = Common::getFaviconSite($order["seller_homeland"]);

            $order['status_title'] = $status_title;
            $order['address_receive'] = $address_receive;
            $order['order_price_vnd'] = $order['real_amount'];
            $order['detail_link'] = $detail_link;
            $order['backend_detail_link'] = $backend_detail_link;
            $order['order_price_ndt'] = $order_price_ndt;
            $order["total_item"] = $total_item;
            $order["total_quantity"] = $total_quantity;
            $order["is_show_button"] = $is_show_button;
            $order["is_customer_confirm"] = $is_customer_confirm;
            if($order["status"] == Order::STATUS_NEGOTIATING){
                $order["is_negotiating"] = 1;
            }else{
                $order["is_negotiating"] = 0;
            }
            $order["text_show_button"] = $text_show_button;
            $order["is_autopaid"] = $is_autopaid;
            $order["not_nagotiated"] = $not_nagotiated;
            $order["is_checking"] = $is_checking;
            $order["is_wood_crating"] = $is_wood_crating;
            $order["is_fragile"] = $is_fragile;
            $order["is_cnp"] = $is_cpn;
            $order["is_high"] = $is_high;
            $date = new DateTime($order["checked_time"]);
            $order['checked_time_format'] = $date->format("d/m/Y");
//            $order["total_services_format"] = Common::numberFormat($order['service_fee'] + $order["domestic_shipping_fee_vnd"]);
            $order["total_services"] = $order['service_fee'] + $order["domestic_shipping_fee_vnd"];
            $order["real_payment_amount_format"] = Common::numberFormat($order["real_payment_amount"]);
            $order["missing_amount"] = $missing_amount;
            $order["missing_amount_format"] = $missing_amount;
            $order["order_services"] = array(
            );
            $total_services_fee = 0;
            $order["order_services"] =  array();
            if(!empty($services_list)){
                foreach ($services_list as $services) {
                    if($services instanceof \OrderService){
                        $array = $services->toArray();
                        $array["code_title"] = $services->getServicesCodeTitle();
                        $total_services_fee += $services->getMoney();
                        $order["order_services"][] = $array;
                    }
                }
            }

            $total_services_fee += $order["domestic_shipping_fee_vnd"];

            $order["total_services_fee"] = floatval($total_services_fee);
            $order["total_services_fee_format"] = Common::numberFormat(floatval($total_services_fee));

            return $order;
        }
        return array();
    }

    /**
     * calculate order deposit amount
     * @param $totalAmount
     * @param Users $user
     * @return float
     */
    public static function calculateDepositAmount($totalAmount) {
        $sysConfig = new \SystemConfig();
//        if($user instanceof \Users){
//            //        $deposit_percent = \ServiceDiscount::getDepositPercent($user->getLevelId());
//            //        $deposit = $totalAmount * ($deposit_percent / 100);
//        }else{
//            $deposit = $totalAmount * ($sysConfig->retrieveByKey(SystemConfig::ORDER_DEPOSIT_PERCENT_REQUIRE)->config_value / 100);
//        }
        $deposit = $totalAmount * ($sysConfig->retrieveByKey(SystemConfig::ORDER_DEPOSIT_PERCENT_REQUIRE)->config_value / 100);

        return Common::roundingMoney($deposit);
    }

    /**
     * @param Query $query
     * @param int $is_delete
     * @return mixed
     */
    public static function countOrderByQuery(Query $query = null, $is_delete = 0) {
        if ($query == null) {
            $query = Order::read();
        }

        $query->count('id');
        if ($is_delete == 0) {
            $query->andWhere('is_deleted=0');
        }
        $data = $query->execute();
        if(is_array($data) && isset($data['total'])){
            return $data['total'];
        }else if(is_numeric($data)){
            return intval($data);
        }
        return 0;
    }

    /**
     * Refund when cancelling or order was out of stock
     * @param Order $order
     * @param $message
     * @return UserTransaction
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function _refundWhenCancelingOrder(\Order $order, $message) {
        $user = \Users::retrieveById($order->getBuyerId());

        if (!($user instanceof \Users)) {
            throw new \InvalidArgumentException('$user parameter must be instance of \Users or user\'s id');
        }

        $refund_amount = $order->getRealPaymentAmount();

        $detail = array(
            'order_code' => $order->getCode(),
            'type' => 'REFUND',
            'message' => $message
        );

        $user->beginTransaction();
        try {
//            print_r($user);exit;
            $transfer = \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail), $detail['message']);
//            print_r($transfer); exit;
            try {
                $detail = json_encode($detail);
                $balance = $transfer['to_account']['balance'];
                \UsersPeer::changeAccountBalance($user, $balance);
                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $refund_amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_REFUND,
                    $transfer['receiving_transaction'],
                    $detail);

                $user->commit();
                \SeuDo\Logger::factory('business')->info('Refund order completed', array(
                    'order_code' => $order->getCode(),
                    'user' => $user->getUsername(),
                    'accountant_transaction' => $userTransaction->getTransactionCode(),
                    'user_transaction' => $userTransaction->getId()
                ));
                return $userTransaction;
            } catch (\Exception $e) {
                //charging back
                \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
                    'order_code' => $order->getCode(),
                    'type' => 'ROLLBACK',
                    'detail' => 'Giao dịch hoàn tiền đơn hàng ' . $order->getCode() . ' không thành công. Trả lại tiền cho dịch vụ.'
                )));
                throw $e;
            }
        } catch(\Exception $e) {//accountant's transaction success need rollback and recharge
            $user->rollBack();
            \SeuDo\Logger::factory('system')->error($e->getMessage() .".\nTrances:\n" .$e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Cancel Order
     *
     * @param Order $order
     * @param null $message
     * @return UserTransaction
     * @throws Exception
     */
    public static function cancelOrder(\Order $order, $message = null) {
        $order->beginTransaction();
        try {
            $refund_amount = $order->getRealPaymentAmount() - $order->getRealRefundAmount();
            //save order out of stock state
            $order->setStatus(\Order::STATUS_CANCELLED);
            $order->setIsDeleted(true);
            $order->setRealRefundAmount(new \Flywheel\Db\Expression('`real_refund_amount` + ' .$refund_amount));
            if (!$order->save()) {//quick save
                throw new \RuntimeException('Could not save order after cancellation');
            }

            return self::_refundWhenCancelingOrder($order, $message);
        } catch (\Exception $e) {
            $order->rollBack();
            throw $e;
        }
    }

    /**
     * change order OUT_OF_STOCK
     *
     * @param Order $order
     * @param null $message
     * @return UserTransaction
     * @throws \Exception
     */
    public static function transitOutOfStock(\Order $order, $message = null) {
        $order->beginTransaction();
        try {
            $refund_amount = $order->getRealPaymentAmount() - $order->getRealRefundAmount();
            //save order out of stock state
            $order->setStatus(\Order::STATUS_OUT_OF_STOCK);
            $order->setOutOfStockTime(new DateTime());
            $order->setRealRefundAmount(new \Flywheel\Db\Expression('`real_refund_amount` + ' .$refund_amount));
            $order->setCancelledTime(new DateTime());
            if (!$order->save()) {//quick save
                throw new \RuntimeException('Could not save order after change out of stock');
            }

            $transaction = self::_refundWhenCancelingOrder($order,  $message);
            if ($transaction) {
                $order->commit();
                return $transaction;
            } else {
                $order->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $order->rollBack();
            echo 'loi transitOutOfStock';
            throw $e;
        }
    }

    /**
     * @param Order $order
     * @param Users $user
     * @param $amount
     * @param string $detail
     * @return UserTransaction
     * @throws Exception
     */
    public static function depositOrder(\Order $order, \Users $user, $amount,$detail = "")
    {
        if($detail == ""){
            $detail = 'Đặt cọc cho đơn hàng ' . $order->getCode();
        }
        $user->beginTransaction();
        try {
            $detail = array(
                'order_code' => $order->getCode(),
                'type' => 'DEPOSIT',
                'detail' => $detail
            );
            //first step charge free
            $transfer = \SeuDo\Accountant\Util::charge($user, $amount, json_encode($detail), $detail['detail']);
            $detail = json_encode($detail);

            try {
                //save ending account balance;
                $balance = $transfer['from_account']['balance'];
                \UsersPeer::changeAccountBalance($user, $balance);

                //change order state and deposit data
                $order->changeStatus(\Order::STATUS_DEPOSITED);
                $order->setDepositAmount($amount);
                $order->setDepositTime(new DateTime());
                $order->setRealPaymentAmount($amount);
                $order->setRealPaymentLastTime(new DateTime());
                if (!$order->save()) {
                    throw new \RuntimeException('Could not save order after deposit');
                }

                $order->updateInfo();

                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_ORDER_DEPOSIT,
                    $transfer['transfer_transaction'],
                    $detail);

                $user->commit();
                \SeuDo\Logger::factory('business')->info('Depositing order completed', array(
                    'order_code' => $order->getCode(),
                    'user' => $user->getUsername(),
                    'accountant_transaction' => $userTransaction->getTransactionCode(),
                    'user_transaction' => $userTransaction->getId()
                ));
                return $userTransaction;

            } catch (\Exception $e) {
                \SeuDo\Accountant\Util::refund($user, $amount, json_encode(array(
                    'order_code' => $order->getCode(),
                    'type' => 'REFUND',
                    'detail' => 'Giao dịch đặt cọc đơn hàng ' . $order->getCode() . ' không thành công. Trả lại tiền đặt cọc.'
                )));
                throw $e;
            }

        } catch (\Exception $e) {
            $user->rollBack();
            //log
            \SeuDo\Logger::factory('system')->error($e->getMessage() . "\nTraces:\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * process when customer request delivery
     * @param Order $order
     * @param array $package
     * @param bool $is_package_first
     * @return bool|UserTransaction
     * @throws RuntimeException
     * @throws Exception
     * @throws SeuDo\Accountant\Exception
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function requestDeliveryOrder(\Order $order,$package = array(),$is_package_first = true) {

        if($is_package_first){
            if($order->checkRequestDeliver() == false) {//check delivery conditions first
                throw new \RuntimeException("Order status can not be change to request delivery");
            }
        }else{
            if(!$order->isAfterStatus(\Order::STATUS_WAITING_FOR_DELIVERY)){
                throw new \RuntimeException("Order status not is after status waiting for delivery");
            }
        }

        $order->updateInfo();

        $amount = $order->requestDeliveryMoney();

        if($amount == 0){
            return true;
        }
        /**
         * @TODO allow amount lower than zero, in this case we refund customer
         */

        if($is_package_first){
            $message = 'Tất toán đơn hàng   '.$order->getCode().' với số tiền '.$amount;
        }else{
            if($package instanceof \Packages){
                $message = 'Truy thu PVC Quốc Tế TQ - VN với kiện hàng '.$package->getId().' của đơn '
                    .$order->getCode().' với số tiền: '.$amount;
            }else{
                throw new InvalidArgumentException("Package variable not instanof \\Packages");
            }

        }

        //charge fee first
        $detail = json_encode(array(
            'order_code' => $order->getCode(),
            'type' => 'PAYMENT',
            'detail' => $message
        ));

        $buyer = $order->getBuyer();

//        $order->beginTransaction();
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try {
            $transfer = \SeuDo\Accountant\Util::charge($buyer, $amount, $detail, $message);

            try {
                $balance = $transfer['from_account']['balance'];
                \UsersPeer::changeAccountBalance($buyer, $balance);//change buyer account balance

                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                    $transfer['transfer_transaction'],
                    $detail);

                //change order status late
                $order->setRealPaymentAmount($order->getRealPaymentAmount() + $amount);

                if($package instanceof \Packages){
                    $package->changeStatus(\Packages::STATUS_CUSTOMER_CONFIRM_DELIVERY);
                }else{
                    $packages = $order->getPackagesList();
                    if($packages){
                        foreach ($packages as $pack) {
                            if($pack instanceof \Packages){
                                $pack->changeStatus(\Packages::STATUS_CUSTOMER_CONFIRM_DELIVERY);
                            }
                        }
                    }
                }

                if($is_package_first){
                    $result = $order->changeStatus(\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY);
                }else{
                    $result = $order->save();
                }

                if($result){
                    $conn->commit();

                    //logging
                    \SeuDo\Logger::factory('business')->info('Completed payment orders', array(
                        'order_code' => $order->getCode(),
                        'user' => $buyer->getUsername(),
                        'accountant_transaction' => $userTransaction->getTransactionCode(),
                        'user_transaction' => $userTransaction->getId()
                    ));

                    return $userTransaction;
                }else{
                    $conn->rollBack();
                    throw new \Flywheel\Exception("Can't save order");
                }


            } catch(\Exception $e) {
                $conn->rollBack();
                if($is_package_first){
                    \SeuDo\Accountant\Util::refund($buyer, $amount, json_encode(array(
                            'order_code' => $order->getCode(),
                            'type' => 'REFUND',
                            'detail' => 'Giao dịch tất toán đơn hàng ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.'
                        )),
                        'Giao dịch tất toán đơn hàng ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.');

                    throw $e;
                }else{
                    \SeuDo\Accountant\Util::refund($buyer, $amount, json_encode(array(
                            'order_code' => $order->getCode(),
                            'type' => 'REFUND',
                            'detail' => 'Truy thu PVC Quốc Tế TQ - VN với kiện hàng '.$package->getId().' của đơn '
                                .$order->getCode() . ' không thành công. Hoàn lại tiền.'
                        )),
                        'Truy thu PVC Quốc Tế TQ - VN với kiện hàng '.$package->getId().' của đơn '
                        .$order->getCode() . ' không thành công. Hoàn lại tiền.');

                    throw $e;
                }


            }
        } catch(\SeuDo\Accountant\Exception $sae) {//transfer not success not need refund
            $conn->rollBack();

            //log error
            \SeuDo\Logger::factory('system')->error($sae->getMessage() ."\nTraces:\n" .$sae->getTraceAsString());
            throw $sae;
        }
    }


    /**
     * Charge PVC với những kiện > 1 yêu cầu giao
     * @param Order $order
     * @return UserTransaction
     * @throws RuntimeException
     * @throws Exception
     * @throws SeuDo\Accountant\Exception
     * @throws Exception
     */
    public static function chargeFeePackageNotFirstRequestDelivery(\Order $order,\Packages $package){
        if($order->isAfterStatus(\Order::STATUS_WAITING_FOR_DELIVERY)) {//check delivery conditions first
            throw new \RuntimeException("Order status not is after status waiting for delivery");
        }

        $order->updateInfo();

        $amount = $order->requestDeliveryMoney();
        /**
         * @TODO allow amount lower than zero, in this case we refund customer
         */

        $message = 'Truy thu PVC Quốc Tế TQ - VN với kiện hàng '.$package->getId().' của đơn '
            .$order->getCode().' với số tiền: '.$amount;

        //charge fee first
        $detail = json_encode(array(
            'order_code' => $order->getCode(),
            'type' => 'PAYMENT',
            'detail' => $message
        ));

        $buyer = $order->getBuyer();

        $order->beginTransaction();
        try {
            $transfer = \SeuDo\Accountant\Util::charge($buyer, $amount, $detail, $message);

            try {
                $balance = $transfer['from_account']['balance'];
                \UsersPeer::changeAccountBalance($buyer, $balance);//change buyer account balance

                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                    $transfer['transfer_transaction'],
                    $detail);

                //change order status late
                $order->setRealPaymentAmount($order->getRealPaymentAmount() + $amount);

                $order->changeStatus(\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY);
                $order->commit();

                //logging
                \SeuDo\Logger::factory('business')->info('Completed payment orders', array(
                    'order_code' => $order->getCode(),
                    'user' => $buyer->getUsername(),
                    'accountant_transaction' => $userTransaction->getTransactionCode(),
                    'user_transaction' => $userTransaction->getId()
                ));

                return $userTransaction;
            } catch(\Exception $e) {
                \SeuDo\Accountant\Util::refund($buyer, $amount, json_encode(array(
                        'order_code' => $order->getCode(),
                        'type' => 'REFUND',
                        'detail' => 'Giao dịch tất toán đơn hàng ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.'
                    )),
                    'Giao dịch tất toán đơn hàng ' . $order->getCode() . ' không thành công. Hoàn lại tiền tất toán.');

                throw $e;
            }
        } catch(\SeuDo\Accountant\Exception $sae) {//transfer not success not need refund
            $order->rollBack();

            //log error
            \SeuDo\Logger::factory('system')->error($sae->getMessage() ."\nTraces:\n" .$sae->getTraceAsString());
            throw $sae;
        }
    }

    /**
     * Kiem tra danh sach don hang co duoc giong khong, <-> duoc giao khi trang thai kho phai la OUT
      * @param $order_list
     * @return bool
     */
    public static function checkDeliveryOrder($order_list){
        if(!empty($order_list)){
            $flag = false;
            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    if($order->getWarehouseStatus() != \Order::WAREHOUSE_STATUS_OUT){
                        return false;
                    }else{
                        $flag = true;
                    }
                }
            }
            return $flag;
        }else{
            return false;
        }
    }

    /**
     * Tính tổng giá trị của những đơn chờ giao
     * @param Users $user
     */
    public static function getTotalAmountWaitingDelivery(\Users $user){
        $total_amount = 0;
        if($user instanceof \Users){
            $query = \Order::read()->andWhere("buyer_id={$user->getId()}")
                ->andWhere("status='".\Order::STATUS_WAITING_FOR_DELIVERY."'");
            $order_list = \OrderPeer::getOrder($query);

            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    $total_amount += $order->getMissingMoney();
                }
            }

        }
        return $total_amount;
    }

    /**
     * Tính tổng giá trị của các đơn theo điều kiện truyền vào
     * @param Query $query
     * @return int|number
     */
    public static function getTotalAmountByCondition(Query $query){

        $total_amount = $query->sum("total_amount")->execute();

        return $total_amount > 0 ? $total_amount : 0;
    }


    /**
     * Tính tổng giá trị của các đơn hàng chưa về (Từ đã mua hàng tới chờ giao)
     * @param $user
     * @return int|number
     */
    public static function getTotalAmountNotAbout($user){
        $user_id = 0;
        if(is_numeric($user)){
            $user_id = $user;
        }elseif($user instanceof \Users){
            $user_id = $user->getId();
        }
        $status_before_waiting_delivery = \OrderPeer::getBetweenStatus(\Order::STATUS_NEGOTIATED,\Order::STATUS_WAITING_FOR_DELIVERY);

        $status_before_waiting_delivery = implode('","', $status_before_waiting_delivery);

        $status_before_waiting_delivery = '"'.$status_before_waiting_delivery.'"';

        $query = \Order::read()->andWhere("buyer_id={$user_id}")->andWhere("status IN ({$status_before_waiting_delivery})");
        $total_amount_before_delivery = \OrderPeer::getTotalAmountByCondition($query);

        return $total_amount_before_delivery;
    }


    /**
     * Đếm số đơn hàng chưa về theo User
     * @param $user
     * @return int|PDOStatement
     */
    public static function countOrderNotAbout($user){
        $user_id = 0;
        if(is_numeric($user)){
            $user_id = $user;
        }elseif($user instanceof \Users){
            $user_id = $user->getId();
        }
        $status_before_waiting_delivery = \OrderPeer::getBetweenStatus(\Order::STATUS_NEGOTIATED,\Order::STATUS_WAITING_FOR_DELIVERY);

        $status_before_waiting_delivery = implode('","', $status_before_waiting_delivery);

        $status_before_waiting_delivery = '"'.$status_before_waiting_delivery.'"';

        $query = \Order::read()->andWhere("buyer_id={$user_id}")->andWhere("status IN ({$status_before_waiting_delivery})");

        $total = $query->count("id")->execute();

        return $total > 0 ? $total : 0;
    }

    /**
     * @param $order_list
     * @return int|number
     */
    public static function getWeightByOrderList($order_list){
        $weight = 0;
        if(!empty($order_list)){
            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    $weight += $order->getWeight();
                }
            }
        }
        return $weight;
    }

    /**
     * @todo Refund for order with money and message is parameter - Chú ý khi sử dụng hàm này.
     * @param $order
     * @param $refund_amount
     * @param $message
     * @return UserTransaction
     * @throws RuntimeException
     * @throws Exception
     */
    public static function refundOrder($order, $refund_amount, $message) {
        if (is_numeric($order)) {
            $order = \Order::retrieveById($order);
        }

        if (!($order instanceof \Order)) {
            throw new \RuntimeException("Order not found!");
        }

        if($refund_amount <= 0){
            throw new \InvalidArgumentException('$refund_amount less than 0');
        }

        $user = $order->getBuyer();
        if (!($user instanceof \Users)) {
            throw new \InvalidArgumentException('$user parameter must be instance of \Users or user\'s id');
        }

        //save order out of stock state
        $order->setRealRefundAmount(new \Flywheel\Db\Expression('`real_refund_amount` + ' .$refund_amount));
        if (!$order->save()) {//quick save
            throw new \RuntimeException('Could not save order when refunding order');
        }

        $detail = array(
            'order_code' => $order->getCode(),
            'type' => 'REFUND',
            'message' => $message
        );

        $user->beginTransaction();
        try {
            $transfer = \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail), $detail['message']);

            try {
                $detail = json_encode($detail);
                $balance = $transfer['to_account']['balance'];
                \UsersPeer::changeAccountBalance($user, $balance);
                //save user transaction
                $userTransaction = \UserTransaction::createOrderTransactionHistory($order,
                    $refund_amount,
                    $balance,
                    \UserTransaction::TRANSACTION_TYPE_REFUND,
                    $transfer['receiving_transaction'],
                    $detail);

                $user->commit();
                \SeuDo\Logger::factory('refund_order')->info('Refund order completed', array(
                    'order_code' => $order->getCode(),
                    'user' => $user->getUsername(),
                    'accountant_transaction' => $userTransaction->getTransactionCode(),
                    'user_transaction' => $userTransaction->getId()
                ));
                return $userTransaction;
            } catch (\Exception $e) {
                //charging back
                \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
                    'order_code' => $order->getCode(),
                    'type' => 'ROLLBACK',
                    'detail' => 'Giao dịch hoàn tiền đơn hàng ' . $order->getCode() . ' không thành công. Trả lại tiền cho dịch vụ.'
                )));
                throw $e;
            }
        } catch(\Exception $e) {//accountant's transaction success need rollback and recharge
            $user->rollBack();
            \SeuDo\Logger::factory('refund_order')->error($e->getMessage() .".\nTrances:\n" .$e->getTraceAsString());
            throw $e;
        }
    }


    /**
     * Hàm lấy ra đơn hàng đã mua nhưng chưa có mã vận đơn với
     * thời gian lâu hơn số ngày truyền vào.  - created by Quyen
     * @param int $day_number
     * @return Order[]
     * @throws InvalidArgumentException
     */
    public static function getOrdersBoughtNotExistFreight($day_number = 3){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("bought_time <= '{$time}' ");
            $query->andWhere("status='".\Order::STATUS_BOUGHT."'");
            $query->andWhere("has_freight_bill = 0");
            $query->andWhere("is_deleted = 0");
            $order_list = \OrderPeer::getOrder($query);

            return $order_list;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }

    /**
     * Đơn hàng đã mua lớn hơn 7 ngày chưa chuyển trạng thái "Nhận hàng từ người bán". - STATUS_RECEIVED_FROM_SELLER
     * Số ngày truyền vào - created By Quyền
     * @param int $day_number
     * @return Order[]
     * @throws InvalidArgumentException
     */
    public static function getOrderNotChangeStatusReceivedFromSeller($day_number = 7){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("bought_time < '{$time}' AND bought_time > '0000-00-00 00:00:00'");
            $query->orWhere("seller_delivered_time < '{$time}' AND seller_delivered_time > '0000-00-00 00:00:00'");
            $query->andWhere("status='".\Order::STATUS_BOUGHT."' OR status = '".\Order::STATUS_SELLER_DELIVERY."'");
            $query->andWhere("has_freight_bill = 0");
            $query->andWhere("is_deleted = 0");
            $order_list = \OrderPeer::getOrder($query);

            return $order_list;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }

    /**
     *  Đơn hàng đã "Nhận hàng từ người bán", không kiểm sau 1 ngày chưa thấy xuất kho tiếp nhận.
     * (Chuyển trạng thái "Vận chuyển") (Thời gian tùy chọn) - created by Quyen
     * @param int $day_number
     * @param bool $is_checking
     * @param bool $is_cpn
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getOrdersReceivedFromSellerNotYetWarehouseOut($day_number = 1,$is_checking = false,$is_cpn = false){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("received_from_seller_time <= '{$time}' ");
            $query->andWhere("status='".\Order::STATUS_RECEIVED_FROM_SELLER."'");
            $query->andWhere("warehouse_status != '".\Order::WAREHOUSE_STATUS_OUT."'");
            $query->andWhere("is_deleted = 0");
            $order_list = \OrderPeer::getOrder($query);
            $order_obj = array();
            if(!empty($order_list) ){
                foreach ($order_list as $order) {
                    if($order instanceof \Order){
                        if(!$is_cpn){
                            if(!$order->needToChecking() && !$is_checking){
                                $order_obj[] = $order;
                            }elseif($order->needToChecking() && $is_checking){
                                $order_obj[] = $order;
                            }
                        } elseif(
                            $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
                            $order_obj[] = $order;
                        }
                    }
                }
            }
            return $order_obj;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }

    /**
     * * Đơn hàng đã chuyển trạng thái "Vận chuyển", nếu là hàng (Theo Services truyen vao) sau (ngày truyen vao) chưa thấy nhập kho HN,
     * sau 4 ngày chưa thấy nhập kho HCM.
     * (Số ngày và kho tự truyền vào) - created by Quyen
     * @param int $day_number
     * @param string $warehouse
     * @param string $services
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getOrdersTransportingNotInWarehouseVN($day_number = 3,$warehouse = \Order::WAREHOUSE_VNHN,
                                                                 $services = \Services::TYPE_EXPRESS_CHINA_VIETNAM){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("transporting_time <= '{$time}' ");
            $query->andWhere("status='".\Order::STATUS_TRANSPORTING."'");
            $query->andWhere("destination_warehouse = '{$warehouse}'");
            $order_list = \OrderPeer::getOrder($query);
            $order_obj = array();
            if(!empty($order_list)){
                foreach ($order_list as $order) {
                    if($order instanceof \Order){
                        if($order->mappingToService($services)){
                            $order_obj[] = $order;
                        }
                    }
                }
            }
            return $order_obj;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }

    /**
     * Đơn hàng ở trạng thái "Chờ giao", sau 10 ngày vẫn
     * chưa thấy chuyển trạng thái "Yêu cầu giao". - Số ngày truyền vào - created by Quyen
     * @param int $day_number
     * @return Order[]
     * @throws InvalidArgumentException
     */
    public static function getOrdersWaitingDeliveryNotDelivery($day_number = 10){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("waiting_delivery_time <= '{$time}' ");
            $query->andWhere("status='".\Order::STATUS_WAITING_FOR_DELIVERY."'");
            $query->andWhere("is_deleted = 0");
            $order_list = \OrderPeer::getOrder($query);
            return $order_list;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }

    public static function getUserFromOrderInit()
    {
        $order_init = \Order::findByStatus(\Order::STATUS_INIT);
        $user_array = array();
        foreach ($order_init as $order) {
            if($order instanceof \Order){
                $user = \Users::retrieveById($order->getBuyerId());
                if($user instanceof \Users){
                    $user_array[$user->getId()]["user"] = $user;
                    $user_array[$user->getId()]["order"][] = array("id" => $order->getId(),
                        "code" => $order->getCode(),
                        "time" => $order->getCreatedTime()
                    );
                }
            }

        }

        return $user_array;
    }

    /**
     * - Đơn hàng ở trạng thái "Yêu cầu giao", hơn 10 ngày chưa thấy chuyển trạng thái "Đang giao".
     * - Số ngày truyền vào - created by Quyen
     * @param int $day_number
     * @return Order[]
     * @throws InvalidArgumentException
     */
    public static function getOrdersDeliveryNotDelivering($day_number = 10){
        if(is_numeric($day_number)){
            $query = \Order::read();
            $time =  date('Y-m-d 00:00:00', strtotime("-{$day_number} days"));
            $query->andWhere("confirm_delivery_time <= '{$time}' ");
            $query->andWhere("status='".\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY."'");
            $query->andWhere("is_deleted = 0");
            $order_list = \OrderPeer::getOrder($query);
            return $order_list;
        }else{
            throw new InvalidArgumentException("Variable not is numeric");
        }
    }
}