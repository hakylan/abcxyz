<?php
namespace Background\Task;
use Background\Library\EmailHelper;
use Background\Library\OrderHelper;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use Flywheel\Util\Folder;
use SeuDo\Logger;
use SeuDo\Logistic;
use SeuDo\Logistic\Client;

class Order extends BackgroundBase {
    protected $limitExecuteTime = 20, $limit = 100;

    public function executeSynchronizeLogistic() {
        $queue = \SeuDo\Queue::factory(\SeuDo\Queue::ORDER_PUSH_LOGISTIC);

        $count = 1;
        try {
            for( $i = 0; $i < $count; $i++) {
                //$orderId = $queue->pop();

                $orderId = 34;
                $data = OrderHelper::buildData($orderId);

                $order = new Logistic\Order($data);

                $result = $order->synchronize();

                if( $result && ( ($client = $result) instanceof Logistic\Client) ) {
                    Logger::factory('order_synchronize')->addAlert('Send sychronize success !',array(
                        $client->getResponse()
                    ));
                    echo 'SUCCESS !\n';
                } else {
                    Logger::factory('order_synchronize')->addError('Has error when ', array($result));
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
//php background/scheduler.php email push_notification

    /**
     *
     */
    public function executeChangeToReceived(){
        $day = $this->getParam("day");
        print("START\n");
        $day = $day == "" ? 15 : $day;
        $from_time = $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $query = \Order::read()->andWhere("status='".\Order::STATUS_DELIVERING."' AND delivered_time < '{$from_time}'");
        $orders = \OrderPeer::getOrder($query);
        foreach ($orders as $order) {
            if($order instanceof \Order && $order->getStatus() == \Order::STATUS_DELIVERING){
                $order->changeStatus(\Order::STATUS_RECEIVED);
                //tinh diem tich luy cho khach hang
                $user = \Users::retrieveById($order->getBuyerId());
                if($user instanceof \Users){
                    $result_calculate_point_member = \OrderService::CalculatePointMemberByOrderService($order,$user);
                }else{
                    \SeuDo\Logger::factory('calculate_point_member_by_order_service')->addError('$user not instanceof \User at Task\Order.php');
                }
                //update packages status of order
                $package_list = \Packages::retrieveByOrderId($order->getId());
                if ($package_list) {
                    foreach ($package_list as $package) {
                        if ($package instanceof \Packages) {
                            if ($package->isStatus(\Packages::STATUS_DELIVERING)) {
                                $package->changeStatus(\Packages::STATUS_RECEIVED);
                            }
                        }
                    }
                }
                print("Order {$order->getCode()} change to received success\n");
            }
        }

        print("END\n");
    }

    /**
     * Task kiểm tra đơn Tổng giá trị đơn hàng có chính xác bằng real_amount + real_services_amount hay không.
     */
    public function executeCheckTotalAmount(){
        $od = $this->getParam("order");
        $day = $this->getParam("day");

        $day = intval($day) > 0 ? $day : 1;

        $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $to_time = date('Y-m-d H:i:s');

        $query = \Order::read();
        $query->andWhere("'$from_time' <= created_time AND created_time <= '{$to_time}'");

        if($od != ''){
            if(is_numeric($od)){
                $query->andWhere("id={$od}");
            }else{
                $query->andWhere("code like '%{$od}%'");
            }
        }
    }

    /**
     *
     */
    public function executeCheckedMissing(){
        $logger = Logger::factory("missing_quantity");
//        $logger->addInfo("quyen");
        $hour = $this->getParam("hour");
        $hour = $hour == "" ? 28 : $hour;
        $from = date("H:i d/m/Y",time() - $hour * 60 * 60);
        $to = date("H:i d/m/Y",time());
        $query = \Order::read()->andWhere("DATE(checked_time) > CURDATE() - INTERVAL $hour HOUR");
        $query->andWhere("pending_quantity != recive_quantity");
        $query->andWhere("checker_id != '' OR checker_id != 0");
        $query->setFirstResult(0)->setMaxResults(10);
        $order_list = \OrderPeer::getOrder($query);
        foreach ($order_list as $order) {
            $checker = \Users::retrieveById($order->getCheckerId());
            $logger->addInfo("Mã đơn hàng:{$order->getId()}. Số lượng kiểm:{$order->getReciveQuantity()} / SL mua {$order->getPendingQuantity()} / Người kiểm:  {$checker->getFullName()} - {$checker->getEmail()} ");
        }
        $this->_sendMailMissing($order_list,$from,$to);
    }

    public function executeUpdateBoughtAmount(){
        $start_month = $this->getParam("start");
        $end_month = $this->getParam("end");
        $status_list = \OrderPeer::getAfterStatus(\Order::STATUS_NEGOTIATED);
        $status = implode('","', $status_list);
        $status = '"'.$status.'"';
//        ->andWhere("is_deleted == 0")->andWhere("status != '".\Order::STATUS_OUT_OF_STOCK."'")->andWhere("status != '".\Order::STATUS_CANCELLED."'")

        $order_list = \Order::read()
            ->andWhere("created_time > '2014-0{$start_month}-01 00:00:00'")
            ->andWhere("created_time < '2014-0{$end_month}-01 00:00:00'")
            ->andWhere("bought_amount = 0")
            ->andWhere("status in ({$status})")->execute()
            ->fetchAll(\PDO::FETCH_CLASS,\Order::getPhpName(),array(null,false));
        if(!empty($order_list)){
            $conn = \Flywheel\Db\Manager::getConnection();
            $conn->beginTransaction();
            $flag = false;
            try{
                print "start with ".count($order_list)." Order\n";
                foreach ($order_list as $order) {
                    if($order instanceof \Order){
                        $bought_amount = $order->calcBoughtAmount();
                        $order->setBoughtAmount($bought_amount);
                        $result = $order->save();
                        if($result){
                            print "order {$order->getCode()} ok \n";
                            $flag = true;
                        }else{
                            $flag = false;
                            print "order {$order->getCode()} not ok \n";
                            break;
                        }
                    }
                    sleep(0.5);
                }
                print "end with ".count($order_list)." Order\n";
                if($flag){
                    $conn->commit();
                    print "Commit success\n";
                }else{
                    $conn->rollBack();
                    print "Commit not success\n";
                }
            }catch (\Exception $e){
                print "Exception {$e->getMessage()}\n";
                throw $e;
            }

        }
    }

    public function _sendMailMissing($order_list,$from,$to){
        if(!empty($order_list)){
            $array_email = array(
                "luongthithanhtam@alimama.vn",
                "nguyenthibichthuy@alimama.vn",
                "chuminhquyen@alimama.vn"
            );
            $template = GLOBAL_TEMPLATES_PATH.'/email/MissingQuantity';
            $subject = "Thông tin những đơn hàng kiểm thiếu từ {$from} đến {$to}";
            $params = array(
                'order_list' => $order_list
            );
            foreach ($array_email as $email) {
                $sendMail= \MailHelper::mailHelperWithBody($template,$params);
                $sendMail->setReciver($email);
                $sendMail->setSubject($subject);
                $sendMail->sendMail();
            }
            var_dump(true);

            return true;

        }
        var_dump(false);
        return false;
    }

    /**
     * Tổng hợp các đơn hàng đang hoạt động gửi hàng tuần cho khách hàng
     * @return bool
     * @throws \Flywheel\Exception
     */
    public function executeSendEmailActiveOrders() {
        try{

            $subject = 'Bảng tổng hợp đơn hàng ngày ' . date('d/m') . ' lúc ' . date('H') . 'h';
            $template = GLOBAL_TEMPLATES_PATH.'/email/SendEmailActiveOrders';

            print "START\n";
            //Lấy toàn bộ user theo đk ACTIVE, CUSTOMER
            $query = \Users::select();
            $query->andWhere(" `section` = '" . \Users::SECTION_CUSTOMER . "' ");
            $query->andWhere(" `status` = '" . \Users::STATUS_ACTIVE . "' ");
            $query->andWhere(" `email` != '' ");
            $users = $query->execute();

            if( sizeof($users) > 0 ) {
                foreach( $users as $user ) {
                    if( $user instanceof \Users ) {
                        $email = $user->getEmail();
                        $user_id = $user->getId();

//                        if( $user_id == 135 ) {
                            //Lấy toàn bộ đơn hàng đang hoạt động (từ khi đặt hàng -> đang giao) thuộc về khách hiện tại
                            $arrStatus = \OrderPeer::getBetweenStatus( \Order::STATUS_BOUGHT, \Order::STATUS_DELIVERING );
                            $tmpStatus = array();

                            $q = \Order::select();
                            $q->andWhere(" `buyer_id` = {$user_id} ");

                            if( sizeof($arrStatus) > 0 ) {
                                foreach ( $arrStatus as $status ) {
                                    $tmpStatus[] = '"' . $status . '"';
                                }
                            }

                            if( sizeof($tmpStatus) > 0 ) {
                                $q->andWhere(" `status` IN (" . implode(",", $tmpStatus) . ") ");
                            }

                            $orders = $q->execute();

                            if( sizeof($orders) > 0 ) {
                                $sendMail= \MailHelper::mailHelperWithBody($template, array(
                                    'orders' => $orders
                                ));

                                $sendMail->setReciver($email);
                                $sendMail->setSubject($subject);
                                $sendMail->sendMail();

                                print "Send " . $email . "\n";
                            } else {
                                print "No result to send " . $email . "\n";
                            }


//                        }

                    }
                }//end foreach users
            }//end if size users

            print "END\n";
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('order_send_mail_active_orders')->addError('has error when send mail active orders', array($e->getMessage()));
            throw new \Flywheel\Exception('has error when send mail active orders');
        }
    }

    /*
     * Task gửi email daily report các vấn đề cho quản lý vận hành về đơn hàng
     * created_at: 05/07/2014
     * created_by: vanhs
     * return bool
     * @throws \Flywheel\Exception
     * */

    public function executeSendEmailDailyReport(){
        try{
            print("START\n");
            $data = array();

            //1. Đơn hàng đã mua lớn hơn 3 ngày không có mã vận đơn.
            $data['order1'] = \OrderPeer::getOrdersBoughtNotExistFreight(3);

            //2. Đơn hàng đã mua lớn hơn 5 ngày không có mã vận đơn
            $data['order2'] = \OrderPeer::getOrdersBoughtNotExistFreight(5);

            //3. Đơn hàng đã mua lớn hơn 7 ngày chưa chuyển trạng thái "Nhận hàng từ người bán".
            $data['order3'] = \OrderPeer::getOrderNotChangeStatusReceivedFromSeller(7);

            //4. Đơn hàng đã "Nhận hàng từ người bán", không kiểm sau 1 ngày chưa thấy xuất kho tiếp nhận. (Chuyển trạng thái "Vận chuyển")
            $data['order4'] = \OrderPeer::getOrdersReceivedFromSellerNotYetWarehouseOut(1, false);

            //5. Đơn hàng đã "Nhận hàng từ người bán", có kiểm sau 3 ngày chưa thấy xuất kho tiếp nhận (chuyển trạng thái "Vận chuyển")
            //(Nếu đơn hàng có CPN thì sau 1 ngày bất kể kiểm hay không nếu chưa xuất kho tiếp nhận phải thông báo)
            $data['order5'] = \OrderPeer::getOrdersReceivedFromSellerNotYetWarehouseOut(3, true);
            // cpn bat ke kiem hay ko
            $data['order6'] = \OrderPeer::getOrdersReceivedFromSellerNotYetWarehouseOut(1, false, true);

            //6. Đơn hàng đã chuyển trạng thái "Vận chuyển", nếu là hàng CPN sau 3 ngày chưa thấy nhập kho HN, sau 4 ngày chưa thấy nhập kho HCM
            $data['order7'] = \OrderPeer::getOrdersTransportingNotInWarehouseVN(3, \Order::WAREHOUSE_VNHN, \Services::TYPE_EXPRESS_CHINA_VIETNAM);
            $data['order8'] = \OrderPeer::getOrdersTransportingNotInWarehouseVN(4, \Order::WAREHOUSE_VNSG, \Services::TYPE_EXPRESS_CHINA_VIETNAM);

            //7. Đơn hàng đã chuyển trạng thái "Vận chuyển", sau 7 ngày chưa thấy nhập kho HN. Sau 10 ngày chưa thấy nhập kho HCM.
            $data['order9'] = \OrderPeer::getOrdersTransportingNotInWarehouseVN(7, \Order::WAREHOUSE_VNHN, \Services::TYPE_SHIPPING_CHINA_VIETNAM);
            $data['order10'] = \OrderPeer::getOrdersTransportingNotInWarehouseVN(10, \Order::WAREHOUSE_VNSG, \Services::TYPE_SHIPPING_CHINA_VIETNAM);

            //8. Đơn hàng ở trạng thái "Chờ giao", sau 10 ngày vẫn chưa thấy chuyển trạng thái "Yêu cầu giao"
            $data['order11'] = \OrderPeer::getOrdersWaitingDeliveryNotDelivery(3);

            //9. Đơn hàng ở trạng thái "Yêu cầu giao", hơn 10 ngày chưa thấy chuyển trạng thái "Đang giao"
            $data['order12'] = \OrderPeer::getOrdersDeliveryNotDelivering(10);

            $attach_file = $this->getOrderDailyReportExcelFile($data);

            $array_email = \SystemConfig::getAddressSendEmailDailyReport();
            $template = GLOBAL_TEMPLATES_PATH.'/email/EmailDailyReport';
            $subject = "Daily report các đơn hàng " . date('d-m-Y H:i');
            $params = array('data' => $data);

            $attach_file = $this->getOrderDailyReportExcelFile($data);
//            print('$attach_file: ' . $attach_file . "\n");
            foreach ($array_email as $email) {
                $sendMail= \MailHelper::mailHelperWithBody($template, $params);
                $sendMail->setReciver($email);
                $sendMail->setSubject($subject);
                $sendMail->setAttachFile($attach_file);
                if($sendMail->sendMail()){
                    print("{$email} Success\n");
                }else{
                    print("{$email} Not Success\n");
                }
            }

            print("END\n");
            return true;
        }catch (\Flywheel\Exception $e){
            \SeuDo\Logger::factory('order_send_mail_daily_report')->addError('has error when send mail daily report', array($e->getMessage()));
            throw new \Flywheel\Exception('has error when send mail daily report');
            return false;
        }

    }

    private function renderRowExcel($objPHPExcel, $title, $data, $sheet_index, $cel_index){
        $problem = $title . " (" . sizeof($data) . ")";
        $cel_index++;

        $objPHPExcel->setActiveSheetIndex( $sheet_index )
            ->setCellValue( 'A' . $cel_index, $problem )
            ->setCellValue( 'B' . $cel_index, '' )
            ->setCellValue( 'C' . $cel_index, '' )
            ->setCellValue( 'D' . $cel_index, '' )
            ->setCellValue( 'E' . $cel_index, '' );

        $cel_index++;
        $objPHPExcel->setActiveSheetIndex( $sheet_index )
            ->setCellValue( 'A' . $cel_index, 'STT' )
            ->setCellValue( 'B' . $cel_index, 'Mã đơn hàng' )
            ->setCellValue( 'C' . $cel_index, 'Trạng thái' )
            ->setCellValue( 'D' . $cel_index, 'Thời gian mua hàng' )
            ->setCellValue( 'E' . $cel_index, 'Link đơn hàng' );

        $count = 0;
        foreach((array)$data as $order){
            if($order instanceof \Order){
                $count++;
                print("ITEM EXPORT EXCEL " . $order->getId() . "\n");
                $cel_index++;
                print($cel_index . "\n");
                $order_id = $order->getId() ? (int)$order->getId() : 0;
                $order_code = $order->getCode() ? $order->getCode() : "";
                $order_status = $order->getStatus() ? $order->getStatus() : "";

                $time = "";
                if($order->getBoughtTime()){
                    $time = date('d/m/Y', strtotime($order->getBoughtTime()));
                }
                $link_order = "http://seudo.vn/backend/order/detail/" . $order_id;

                $objPHPExcel->setActiveSheetIndex( $sheet_index )
                    ->setCellValue( 'A' . $cel_index, $count )
                    ->setCellValue( 'B' . $cel_index, $order_code )
                    ->setCellValue( 'C' . $cel_index, \Order::$statusTitle[$order_status] )
                    ->setCellValue( 'D' . $cel_index, $time )
                    ->setCellValue( 'E' . $cel_index, $link_order );
            }
        }
        return $cel_index;
    }

    private function getOrderDailyReportExcelFile($need_to_export){
        if ( is_array( $need_to_export ) && sizeof( $need_to_export ) > 0 ) {
            print("BEGIN EXPORT EXCEL\n");
            $objPHPExcel = new \PHPExcel();
            $cel_index = 0;
            $sheet_index = 0;

            $problem = "Đơn hàng đã mua lớn hơn 3 ngày không có mã vận đơn (" . sizeof($need_to_export['order1']) . ")";
            $objPHPExcel->setActiveSheetIndex( $sheet_index )
                ->setCellValue( 'A1', $problem )
                ->setCellValue( 'B1', '' )
                ->setCellValue( 'C1', '' )
                ->setCellValue( 'D1', '' )
                ->setCellValue( 'E1', '' );
            $cel_index = 1;

            $objPHPExcel->setActiveSheetIndex( $sheet_index )
                ->setCellValue( 'A2', 'STT' )
                ->setCellValue( 'B2', 'Mã đơn hàng' )
                ->setCellValue( 'C2', 'Trạng thái' )
                ->setCellValue( 'D2', 'Thời gian mua hàng' )
                ->setCellValue( 'E2', 'Link đơn hàng' );
            $cel_index = 2;

            $count = 0;
            foreach((array)$need_to_export['order1'] as $order){
                if($order instanceof \Order){
                    $count++;
                    print("ITEM EXPORT EXCEL " . $order->getId() . "\n");
                    $cel_index++;

                    $order_id = $order->getId() ? (int)$order->getId() : 0;
                    $order_code = $order->getCode() ? $order->getCode() : "";
                    $order_status = $order->getStatus() ? $order->getStatus() : "";

                    $time = "";
                    if($order->getBoughtTime()){
                        $time = date('d/m/Y', strtotime($order->getBoughtTime()));
                    }
                    $link_order = "http://seudo.vn/backend/order/detail/" . $order_id;

                    $objPHPExcel->setActiveSheetIndex( $sheet_index )
                        ->setCellValue( 'A' . $cel_index, $count )
                        ->setCellValue( 'B' . $cel_index, \Order::$statusTitle[$order_status] )
                        ->setCellValue( 'C' . $cel_index, $order_code )
                        ->setCellValue( 'D' . $cel_index, $time )
                        ->setCellValue( 'E' . $cel_index, $link_order );
                }

            }

            $cel_index = $this->renderRowExcel($objPHPExcel, "Đơn hàng đã mua lớn hơn 5 ngày không có mã vận đơn", $need_to_export['order2'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, "Đơn hàng đã mua lớn hơn 7 ngày chưa chuyển trạng thái 'Nhận hàng từ người bán'", $need_to_export['order3'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng đã "Nhận hàng từ người bán", không kiểm sau 1 ngày chưa thấy xuất kho tiếp nhận. (Chuyển trạng thái "Vận chuyển")', $need_to_export['order4'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng đã "Nhận hàng từ người bán", có kiểm sau 3 ngày chưa thấy xuất kho tiếp nhận (chuyển trạng thái "Vận chuyển")', $need_to_export['order5'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng chuyển phát nhanh đã "Nhận hàng từ người bán", chưa thấy xuất kho tiếp nhận (chuyển trạng thái "Vận chuyển")', $need_to_export['order6'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng chuyển phát nhanh đã chuyển trạng thái "Vận chuyển", sau 3 ngày chưa thấy nhập kho HN ', $need_to_export['order7'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng đã chuyển trạng thái "Vận chuyển", sau 4 ngày chưa thấy nhập kho HCM', $need_to_export['order8'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng đã chuyển trạng thái "Vận chuyển", sau 7 ngày chưa thấy nhập kho HN', $need_to_export['order9'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng đã chuyển trạng thái "Vận chuyển", sau 10 ngày chưa thấy nhập kho HCM', $need_to_export['order10'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng ở trạng thái "Chờ giao", sau 10 ngày vẫn chưa thấy chuyển trạng thái "Yêu cầu giao"', $need_to_export['order11'], $sheet_index, $cel_index);
            $cel_index = $this->renderRowExcel($objPHPExcel, 'Đơn hàng ở trạng thái "Yêu cầu giao", hơn 10 ngày chưa thấy chuyển trạng thái "Đang giao"', $need_to_export['order12'], $sheet_index, $cel_index);

            $folder_path = ROOT_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'order_daily_report' . DIRECTORY_SEPARATOR;
            Folder::create($folder_path);
            $path_file_excel = $folder_path . 'Daily_report_cac_don_hang' . "_" . date( 'dmYHi' ) . '.xls';

            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = \PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
            $objWriter->save( $path_file_excel );

            print("END EXPORT EXCEL\n");
            return $path_file_excel;
        }else{
            print("EXPORT EXCEL NONE\n");
            \SeuDo\Logger::factory('send_mail_order_daily_report')->addError('Không có dữ liệu excel');
            return "";
        }
    }
}
