<?php 
/**
 * Complaints
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ComplaintsBase.php';
class Complaints extends \ComplaintsBase {

    //Lỗi
    const REASON_FALSE_COLOR_SIZE = 'FALSE_COLOR_SIZE';//Sai màu sắc / kích thước
    const REASON_SAI_PATTERN_DESIGN = 'SAI_PATTERN_DESIGN';//Sai hoa văn / kiểu dáng
    const REASON_TEAR_HOLE = 'TEAR_HOLE';//Rách / thủng
    const REASON_DENTED_OR_BENT_BLUCKLED = 'DENTED_OR_BENT_BLUCKLED';//Bị móp hoặc cong, vênh
    const REASON_WASHING_DIRTY_OR_NOT = 'WASHING_DIRTY_OR_NOT';//Dính bẩn hoặc không giặt được
    const REASON_MOLD = 'MOLD';//Bị mốc
    const REASON_ORDER_REASON = 'ORDER_REASON';//Lí do khác

    //Trạng thái của khiếu nại
    const STATUS_WAITING_RECEIVE = 'WAITING_RECEIVE';//Chờ tiếp nhận
    const STATUS_OUSTANDING = 'OUSTANDING';//Đang giải quyết
    const STATUS_ACCEPT = 'ACCEPT';//Chấp nhận
    const STATUS_REJECT = 'REJECT';//Từ chối
    const STATUS_REFUND = 'REFUND';//Hoàn tiền

    //Loại khiếu nại
    const TYPE_PRODUCT_ERROR = 'PRODUCT_ERROR';//Sản phẩm lỗi
    const TYPE_PRODUCT_NOT_RECEIVED = 'PRODUCT_NOT_RECEIVED';//Không nhận được hàng

    const MAX_FILE_UPLOAD = 6;

    const YES = 'YES';
    const NO = 'NO';
    const DELETED = 'DELETED';
    const NONE = 'NONE';

    public static $typeTitle = array(
        self::TYPE_PRODUCT_NOT_RECEIVED => 'Không nhận được hàng',
        self::TYPE_PRODUCT_ERROR => 'Sản phẩm lỗi'
    );

    public static $statusTitle = array(
        self::STATUS_WAITING_RECEIVE => 'CHỜ TIẾP NHẬN',
        self::STATUS_OUSTANDING => 'ĐANG GIẢI QUYẾT',
        self::STATUS_ACCEPT => 'ĐÃ CHẤP NHẬN',
        self::STATUS_REFUND => 'ĐÃ HOÀN TIỀN',
        self::STATUS_REJECT => 'ĐÃ TỪ CHỐI'
    );

    public static $reasonTitle = array(
        self::REASON_FALSE_COLOR_SIZE => 'Sai màu sắc / kích thước',
        self::REASON_SAI_PATTERN_DESIGN => 'Sai hoa văn / kiểu dáng',
        self::REASON_TEAR_HOLE => 'Rách / thủng',
        self::REASON_DENTED_OR_BENT_BLUCKLED => 'Bị móp hoặc cong, vênh',
        self::REASON_WASHING_DIRTY_OR_NOT => 'Dính bẩn hoặc không giặt được',
        self::REASON_MOLD => 'Bị mốc',
        self::REASON_ORDER_REASON => 'Lí do khác'
    );

    public static function getTotalComplaintByStatus(){
        $total = 0;
        try{
            $status_reject = \Complaints::STATUS_REJECT;
            $status_refund = \Complaints::STATUS_REFUND;
            $query = \Complaints::select();
            $query->addSelect("id");
            $query->andWhere(" `status` != '{$status_reject}' ");
            $query->andWhere(" `status` != '{$status_refund}' ");
            $total = $query->count('id')->execute();
//            echo $query->getSQL();
            return $total;
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('get_total_complaint_by_status')->addError('has error when try get total complaint by status',array($e->getMessage()));
            throw new \Flywheel\Exception('has error when try get total complaint by status');
            return $total;
        }
    }

    public static function updateRecipientAmountReimbursement($camplaint_id, $recipient_amount_reimbursement){
        $put = \Complaints::retrieveById($camplaint_id);
        $put->beginTransaction();
        try {
            $put->setRecipientAmountReimbursement($recipient_amount_reimbursement);
            $put->setRecipientAmountReimbursementTime(date('Y-m-d H:i:s'));
            $put->save();
            $put->commit();
        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update recipient amount reimbursement',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update recipient amount reimbursement');
        }
    }

    public static function getStatusTitle($status){
        return static::$statusTitle[$status];
    }

    public static function getStatusColor($status){
        $color = "";
        if($status == \Complaints::STATUS_WAITING_RECEIVE || $status == \Complaints::STATUS_OUSTANDING){
            $color = "font-red";
        }
        if($status == \Complaints::STATUS_ACCEPT || $status == \Complaints::STATUS_REFUND){
            $color = "font-green";
        }
        if($status == \Complaints::STATUS_REJECT){
            $color = "font-gray";
        }
        return $color;
    }

    public static function getReasonTitle($reason){
        if($reason){
            return static::$reasonTitle[$reason];
        }else{
            return "";
        }
    }

    public static function getResultsTitle($status){
        $title = "";
        if($status == Complaints::STATUS_ACCEPT){
            $title = 'Thành công';
        }
        if($status == Complaints::STATUS_REJECT){
            $title = 'Thất bại';
        }
        return $title;
    }

    public static function getOneComplaint($order_id, $item_id){
        $query = \Complaints::read();
        $query->andWhere("order_id = {$order_id} AND item_id = {$item_id}");
        return $query->execute()->fetch();
    }

    public static function getListFileComplaint($complaint_id){
        $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
        if(!$sfsConfig){
            throw new \Exception('Sfs Config is missing !');
        }
        $sfsUrl = $sfsConfig['service_url'];

        $query = \ComplaintsFiles::read();
        $data = $query->andWhere("complaint_id = {$complaint_id} AND invalid = 'NONE'")->execute()->fetchAll();
        for($i = 0; $i < sizeof($data); $i++){
            $data[$i]['service_url'] = $sfsUrl;
        }
        return $data;
    }

    public static function changeStatusComplaint($complaint_id, $status){
        $put = \Complaints::retrieveById($complaint_id);
        $put->beginTransaction();
        try{
            $put->setStatus($status);
            $put->save();
            $put->commit();
        } catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update status complaint',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update status complaint');
        }
    }

    public static function checkMaxFileUpload($complaint_id){
        $query = \ComplaintsFiles::read();
        $rows = $query->andWhere("complaint_id = {$complaint_id} AND invalid = 'NONE'")->execute()->rowCount();
        return $rows == 6 ? false : true;
    }

    public static function checkItemComplaint($order_id = 0, $item_id = 0){
        if($order_id == 0 || $item_id == 0){
            return false;
        }
        $query = \Complaints::read();
        $rows = $query->select('id')->andWhere("order_id = {$order_id} AND item_id = {$item_id}")->execute()->rowCount();
        return $rows > 0 ? true : false;
    }

    public static function getComplaintIdByOrderIdAndItemId($order_id = 0, $item_id = 0){
        if($order_id == 0 || $item_id == 0){
            return false;
        }
        try{
            $complaint = false;
            $complaint = \Complaints::findOneByOrderIdAndItemId($order_id, $item_id);
            if($complaint instanceof \Complaints){
                return $complaint->getId();
            }
            return false;
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('get_complaint_id_by_order_id_and_item_id')->addError('has error when try get complaint_id by order_id and item_id',array($e->getMessage()));
            throw new \Flywheel\Exception('has error when try get complaint_id by order_id and item_id');
            return false;
        }
    }

    public static function getListComplaints($condition){
        //TODO
    }

    public static function getComplaints($query = null){
        if ($query == null) $query = Complaints::read();
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \Complaints::getPhpName(), array(null, false));
    }

    public static function getInfoComplaint($id){
        $query = \Complaints::read();
        $query->andWhere("id = {$id}");
        return $query->execute()->fetch();
    }

    public static function updateOrderCodeItemCodeInComplaint(){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        try{
            $query = \Complaints::read();
            $data = $query->execute()->fetchAll();

            foreach((array)$data as $item){
                $put = \Complaints::retrieveById($item['id']);
                $order = \Order::retrieveById($item['order_id']);
                $item = \OrderItem::retrieveById($item['item_id']);
                $put->setItemCode($item->getId());
                $put->setOrderCode($order->getCode());
                $put->save();
            }

            $put->commit();
            return true;
        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update order code, item code complaint',array($e->getMessage()));
            $conn->rollBack();
            return false;
        }
    }

    /**
     * Hàm thống kê KNDV
     */
    public static function statisticsComplaint(){

        try{

            $data = array();

            $data['total'] = 0;//Tổng số khiếu nại đến thời điểm hiện tại
            $data['total_before_month'] = 0;//Tổng số khiếu nại của tháng trước
            $data['total_current'] = 0;//Tổng số khiếu nại hiện tại

            $data['total_accept'] = 0;//Tống khiếu nại được chấp nhận
            $data['total_refund'] = 0;//Tổng khiếu nại được hoàn tiền
            $data['total_reject'] = 0;//Tổng khiếu nại bị từ chối

            $query = \Complaints::select();
            $obj = $query->execute();

            //Tháng hiện tại
            $time_current = date('Y') . '-' . date('m') . '-01';

            $newdate = strtotime ( '-1 month' , strtotime ( $time_current ) ) ;
            $newdate = date ( 'Y-m-d' , $newdate );

            list($year_before, $month_before, $day_before) = explode('-', $newdate);

            $num = cal_days_in_month(CAL_GREGORIAN, $month_before, $year_before); // 31

            $time_before_start = $newdate;
            $time_before_end = $year_before . '-' . $month_before . '-' . $num;

            if( sizeof($obj) > 0 ) {
                foreach( $obj as $o ) {
                    if( $o instanceof \Complaints ) {
                        $data['total']++;


                        if( strtotime( $o->getCreateTime() ) <= strtotime( $time_before_end ) && strtotime( $o->getCreateTime() ) >= strtotime( $time_before_start ) ) {
                            $data['total_before_month']++;
                        }

                        if( strtotime( $o->getCreateTime() ) >= strtotime( $time_current ) ) {
                            $data['total_current']++;
                        }

                        if( $o->getStatus() == \Complaints::STATUS_ACCEPT ) {
                            $data['total_accept']++;
                        }

                        if( $o->getStatus() == \Complaints::STATUS_REFUND ) {
                            $data['total_refund']++;
                        }

                        if( $o->getStatus() == \Complaints::STATUS_REJECT ) {
                            $data['total_reject']++;
                        }
                    }
                }
            }

            $data['time_current'] = $time_current;
            $data['time_before_start'] = $time_before_start;
            $data['time_before_end'] = $time_before_end;

            return $data;

        } catch (\Flywheel\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint_statistics')->addError('has error when try to get statistics complaints',array($e->getMessage()));
            return false;
        }
    }

    public static function GetGirdComplaints($user_id, $keyword, $status, $from_time, $to_time, $page, $recipient_by, $approval_by,
                                             $item_code, $order_code, $damage, $error_division_company, $error_partner, $error_seller,
                                             $recipient_by, $approval_by, $order_id, $per_page, $get_by_buyer, $all, $not_in){

        $offset = ($page - 1) * $per_page;
        $limit = $page * $per_page;

//        echo '$offset: ' . $offset;
//        echo '$limit: ' . $limit;

        $query = \Complaints::read();

        if( sizeof($not_in) > 0 ) {
            $query->andWhere(" `id` NOT IN (" . implode(',', $not_in) . ") ");
        }

        if($get_by_buyer == 0){
            $query->andWhere("buyer_id = {$user_id}");
        }
        //1. Tìm kiếm theo thời gian

        if($from_time != ''){
            $from_time = new \DateTime($from_time); //($from_time);
            $from_time = $from_time->format("Y-m-d H:i:s");
        }
        if($to_time != ''){
            $to_time = new \DateTime($to_time);
            $to_time = $to_time->format("Y-m-d 23:59:59");
        }

        if($from_time != '' && $to_time == ''){
            $query->andWhere("create_time >= '{$from_time}'");
        }

        if($from_time == '' && $to_time != ''){
            $query->andWhere("create_time<='{$to_time}'");
        }

        if($from_time != '' && $to_time != ''){
            $query->andWhere(" create_time >= '{$from_time}' AND create_time <= '{$to_time}' ");
        }

        //2. Tìm kiếm theo key
        if($keyword != ''){
            $query->andWhere(" `title` LIKE '%{$keyword}%' OR `order_code` LIKE '%{$keyword}%' ");
        }
        //3. Tìm kiếm theo trạng thái khiếu nại
        if($status != ''){
            $query->andWhere("status = '{$status}'");
        }

        //4. Tìm theo nhân viên xử lý
        if($recipient_by > 0){
            $query->andWhere("recipient_by = {$recipient_by}");
        }

        //5. Tìm theo nhân viên hoàn tiền
        if($approval_by > 0){
            $query->andWhere("approval_by = {$approval_by}");
        }

        //6. Tìm kiếm theo mã sản phẩm
        if($item_code != ""){
            $query->andWhere(" `item_id` LIKE '%{$item_code}%' ");
        }

        //7. Tìm kiếm theo mã đơn hàng
        if($order_code != ""){
            $query->andWhere(" `order_code` LIKE '%{$order_code}%' ");
        }

        //8. Có gây thiệt hại cho công ty hay không?
        if($damage != ""){
            $query->andWhere("damage = '{$damage}'");
        }

        //9. Lỗi của bộ phận công ty
        if($error_division_company != ""){
            $query->andWhere("error_division_company = '{$error_division_company}'");
        }
        //10. Lỗi do đối tác
        if($error_partner != ""){
            $query->andWhere("error_partner = '{$error_partner}'");
        }
        //11. Lỗi do người bán
        if($error_seller != ""){
            $query->andWhere("error_seller = '{$error_seller}'");
        }

        //12. Nhân viên xử lý
        if($recipient_by > 0){
            $query->andWhere("recipient_by = {$recipient_by}");
        }
        //13. Nhân viên hoàn tiền
        if($approval_by > 0){
            $query->andWhere("approval_by = {$approval_by}");
        }
        //14. Order_id
        if($order_id > 0){
            $query->andWhere("order_id = {$order_id}");
        }

        $query_count = clone $query;

        switch ($status){
            case \Complaints::STATUS_WAITING_RECEIVE:
                $query->orderBy("create_time", "ASC");
                break;
            case \Complaints::STATUS_REJECT:
                $query->orderBy("reject_time", "ASC");
                break;
            case \Complaints::STATUS_ACCEPT:
                $query->orderBy("accept_time", "ASC");
                break;
            case \Complaints::STATUS_OUSTANDING:
                $query->orderBy("recipient_time", "ASC");
                break;
            case \Complaints::STATUS_REFUND:
                $query->orderBy("approval_time", "ASC");
                break;
            default:
                $query->orderBy("id", "DESC");
                break;
        }

        if($all == 0){
            $query->setFirstResult($offset)->setMaxResults($per_page);
        }
//        echo $query->getSQL();
        $items = \Complaints::getComplaints($query);
        for($i = 0; $i < sizeof($items); $i++){
            $items[$i] = $items[$i]->toArray();
            $items[$i]['type_title'] = self::$typeTitle[$items[$i]['type']];
            $items[$i]['status_title'] = self::$statusTitle[$items[$i]['status']];

            $create_time = $recipient_time = $approval_time = $reject_time = "";

            if($items[$i]['create_time']){
                $create_time = new \DateTime($items[$i]['create_time']);
                $create_time = $create_time->format("d/m/Y");
            }

            if($items[$i]['recipient_time']){
                $recipient_time = new \DateTime($items[$i]['recipient_time']);
                $recipient_time = $recipient_time->format("d/m/Y");
            }

            if($items[$i]['approval_time']){
                $approval_time = new \DateTime($items[$i]['approval_time']);
                $approval_time = $approval_time->format("d/m/Y");
            }

            if($items[$i]['reject_time']){
                $reject_time = new \DateTime($items[$i]['reject_time']);
                $reject_time = $reject_time->format("d/m/Y");
            }

            $items[$i]['create_time'] = $create_time;
            $items[$i]['recipient_time'] = $recipient_time;
            $items[$i]['approval_time'] = $approval_time;
            $items[$i]['reject_time'] = $reject_time;

            //Người tiếp nhận
            $items[$i]['show_info_recipient_by'] = false;
            $recipient_by = \Users::retrieveById($items[$i]['recipient_by']);
            if($recipient_by){
                $items[$i]['info_recipient_by']['user_id'] = $recipient_by->getId();
                $items[$i]['info_recipient_by']['username'] = $recipient_by->getUsername();
                $items[$i]['info_recipient_by']['fullname'] = $recipient_by->getFullName();
                $items[$i]['info_recipient_by']['avatar'] = $recipient_by->getAvatar32x($recipient_by);
                $items[$i]['show_info_recipient_by'] = true;
            }
        }

        $total_record = (int)$query_count->count('id')->execute();

        $total_page = $total_record % $per_page == 0 ? $total_record / $per_page
            : intval($total_record / $per_page) + 1;
        return array('items' => $items, 'total_record' => $total_record, 'total_page' => $total_page, 'SQL' => $query->getSQL());
    }

    public static function updateStatusAcceptComplaint($complaint_id = 0, $user_id = 0, $flag_amount){
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();

        try {
            $put = \Complaints::retrieveById($complaint_id);
            $customer_amount_reimbursement = $put->getCustomerAmountReimbursement();
            $recipient_amount_reimbursement = $put->getRecipientAmountReimbursement();

            //1. Nếu chấp nhận mức bồi hoàn do khách đưa ra
            if($recipient_amount_reimbursement == 0){
                $put->setStatus(\Complaints::STATUS_ACCEPT);
                $put->setAcceptBy($user_id);
                $put->setAcceptTime(date('Y-m-d H:i:s'));
                $put->setRecipientAmountReimbursement($customer_amount_reimbursement);//Cập nhật số tiền đề xuất bằng chính mức tiền khách yêu cầu
                $put->setRecipientAmountReimbursementTime(date('Y-m-d H:i:s'));
                $put->save();
            }
            //2. Nếu chấp nhận mức bồi hoàn do chính mình đề xuất
            if($recipient_amount_reimbursement > 0){
                $put->setStatus(\Complaints::STATUS_ACCEPT);
                $put->setAcceptBy($user_id);
                $put->setAcceptTime(date('Y-m-d H:i:s'));
                $put->save();
            }

            $conn->commit();
            return \Complaints::getStatusTitle($put->getStatus());
        } catch (\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update status accept complaint',array($e->getMessage()));
            $conn->rollBack();
            throw new \Flywheel\Exception('has error when try to update status accept complaint');
        }
    }

    public static function UpdateStatusReceptionComplaint($complaint_id = 0, $user_id = 0){
        $put = \Complaints::retrieveById($complaint_id);
        try {
            $put->setStatus(\Complaints::STATUS_OUSTANDING);
            $put->setRecipientBy($user_id);
            $put->setRecipientTime(date('Y-m-d H:i:s'));

            $flag = $put->save();
            $put->commit();
        } catch (\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update status reception complaint',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update status reception complaint');
        }
        if($flag){
            return \Complaints::getStatusTitle($put->getStatus());
        }else{
            return false;
        }
    }

    public static function updateStatusRejectComplaint($complaint_id = 0, $user_id = 0){
        $put = \Complaints::retrieveById($complaint_id);
        try {
            $put->setStatus(\Complaints::STATUS_REJECT);
            $put->setRejectBy($user_id);
            $put->setRejectTime(date('Y-m-d H:i:s'));
            $flag = $put->save();
            $put->commit();
        } catch (\Exception $e) {
            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update status reject complaint',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update status reject complaint');
        }
        if($flag){
            return \Complaints::getStatusTitle($put->getStatus());
        }else{
            return false;
        }
    }

    public static function updateStatusRefundComplaint($complaint_id = 0, $user_id = 0){
        $put = \Complaints::retrieveById($complaint_id);

        $put->beginTransaction();
        try {
            //Change status first
            $put->setStatus(\Complaints::STATUS_REFUND);
            $put->setApprovalBy($user_id);
            $put->setApprovalTime(date('Y-m-d H:i:s'));

            //refund order
            $amount = $put->getRecipientAmountReimbursement();
            if ($amount == 0) {
                throw new \RuntimeException('Refund amount could not be zero!');
            }

            $history = \OrderPeer::refundOrder($put->getOrderId(), $amount, 'Hoàn tiền khiếu nại số ' .$put->getId() . ' - ' .$put->getTitle());
            $put->setTransaction($history->getTransactionCode());

            if($put->save()){
                $put->commit();
                return array('status' => \Complaints::getStatusTitle($put->getStatus()),
                                'transaction_code' => $history->getTransactionCode());
            }else{
                $put->rollBack();
                return false;
            }

        } catch (\Exception $e) {

            \SeuDo\Logger::factory('backend_complaint')->addError('has error when try to update status refund complaint '.$e->getMessage(),array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try to update status refund complaint');
        }
    }

    /**
     * Update complaint state
     *
     * @param int $complaint_id
     * @param int $user_id
     * @param string $type
     * @return bool
     * @throws Exception
     */

    //Hàm này dùng ngoài frontend
    public static function updateStatusComplaint($complaint_id = 0, $user_id = 0, $type = ""){
        try{
            $put = \Complaints::retrieveById($complaint_id);
            //Click đã chấp nhận
            if($type == 'ACCEPTED'){
                $put->setStatus(\Complaints::STATUS_ACCEPT);
                $put->setAcceptBy($user_id);
                $put->setAcceptTime(date('Y-m-d H:i:s'));
                $put->save();
            }
            //Click đã từ chối
            if($type == 'REFUSED'){
                $put->setStatus(\Complaints::STATUS_REJECT);
                $put->setRecipientBy($user_id);
                $put->setRecipientTime(date('Y-m-d H:i:s'));
                $put->save();
            }
            $put->commit();
            return \Complaints::getStatusTitle($put->getStatus());
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('complaint_update_status_frontend')->addError('has error when try update status complaint frontend',array($e->getMessage()));
            $put->rollBack();
            throw new \Flywheel\Exception('has error when try update status complaint frontend');
            return false;
        }
    }

    /**
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public static function checkOrderIsExistComplaint( $order_id = 0, $status = \Complaints::STATUS_OUSTANDING ) {
        if( $order_id == 0 ) {
            return false;
        }

        try {
            $query = \Complaints::select();
            $query->select("id");
            $query->andWhere(" `order_id` = {$order_id} ");
            $query->andWhere(" `status` = '{$status}' ");
            $count = $query->count('id')->execute();

            return $count > 0;
        } catch ( \Flywheel\Exception $e ) {
            return false;
        }
    }

    /**
     * @return array
     */
    public static function getOrdersByComplaint () {
        $arrStatus = $arrOrderId = array();
        try {
            $arrStatus[] = "'" . \Complaints::STATUS_OUSTANDING . "'";
            $query = \Complaints::select();
            $query->addSelect("order_id");
            $query->andWhere(" `status` IN (" . implode(',', $arrStatus) . ") ");
            $data = $query->execute();
//            echo $query->getSQL();
            if( sizeof($data) > 0 ) {
                foreach ( $data as $item ) {
                    if( $item instanceof \Complaints ) {
                        $arrOrderId[] = $item->getOrderId();
                    }
                }
            }

            return $arrOrderId;
        } catch ( \Flywheel\Exception $e ) {
            return $arrOrderId;
        }
    }

}