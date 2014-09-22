<?php

namespace Backend\Controller\Complaint;

use Backend\Controller\BackendBase;
use Flywheel\Db\Query;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use SeuDo\Logger;
use SeuDo\Main;

class Managerment extends BackendBase
{
    private $number_show = 20;
    private $user;

    public function beforeExecute(){
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeGetListComplaints(){
        $this->validAjaxRequest();
        //check permission view list complaints
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        try{
            if (!$this->isAllowed(PERMISSION_COMPLAINT_VIEW_LIST)) {
                $ajax->message = self::t('Bạn không có quyền vào khu vực này');
                return $this->renderText($ajax->toString());
            }

            //get params
            $user_id = $this->user->getId();
            $condition = array();
            $condition['keyword'] = $this->request()->request("keyword","STRING",'');
            $condition['status'] = $this->request()->request("status","STRING",'');
            $condition['from_time'] = $this->request()->request("from_time","STRING",'');
            $condition['to_time'] = $this->request()->request("to_time","STRING",'');
            $condition['page'] = $this->request()->request('page',"INT",1);
            $condition['recipient_by'] = $this->request()->request('recipient_by',"INT",0);//Người tiếp nhận xử lý
            $condition['approval_by'] = $this->request()->request('approval_by',"INT",0);//Người duyệt tài chính
            $condition['item_code'] = $this->request()->request('item_code',"STRING", "");
            $condition['order_code'] = $this->request()->request('order_code',"STRING", "");
            $condition['item_id'] = $this->request()->request('item_id',"STRING", "");
            $condition['customer_code'] = $this->request()->request('customer_code',"STRING", "");
            $condition['damage'] = $this->request()->request('damage', 'STRING', '');

            $condition['reasons'] = $this->request()->request('reasons', 'STRING', '');

            $condition['error_division_company'] = $this->request()->request('error_division_company',"STRING", "");
            $condition['error_partner'] = $this->request()->request('error_partner',"STRING", "");
            $condition['error_seller'] = $this->request()->request('error_seller',"STRING", "");

            $condition['recipient_by'] = $this->request()->request('recipient_by',"INT", 0);
            $condition['approval_by'] = $this->request()->request('approval_by',"INT", 0);

            $condition['get_by_buyer'] = $this->request()->request('get_by_buyer',"INT", 0);
            $condition['order_id'] = $this->request()->request("order_id","INT",0);

            $offset = ($condition['page'] - 1) * $this->number_show;
            $limit = $condition['page'] * $this->number_show;

            $query = \Complaints::read();
            if($condition['get_by_buyer'] == 0){
                $query->andWhere("buyer_id = {$user_id}");
            }
            //1. Tìm kiếm theo thời gian
            $from_time = $to_time = "";

            if($condition['from_time'] != ''){

                $tmpDateFrom = DateTime::createFromFormat('d-m-Y', $condition['from_time']);
                if(!$tmpDateFrom){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Ngày bắt đầu không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }

                $arrFormTime = explode("-", $condition['from_time']);
                $from_time = $arrFormTime[2] . '-' . $arrFormTime[1] . '-' . $arrFormTime[0] . ' 00:00:00';
            }
            if($condition['to_time'] != ''){

                $tmpDateTo = DateTime::createFromFormat('d-m-Y', $condition['to_time']);
                if(!$tmpDateTo){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Ngày kết thúc không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }

                $arrToTime = explode("-", $condition['to_time']);
                $to_time = $arrToTime[2] . '-' . $arrToTime[1] . '-' . $arrToTime[0] . ' 23:59:59';
            }

            if($condition['from_time'] != '' && $condition['to_time'] == ''){
                $query->andWhere("create_time >= '{$from_time}'");
            }

            if($condition['from_time'] == '' && $condition['to_time'] != ''){
                $query->andWhere("create_time<='{$to_time}'");
            }

            if($condition['from_time'] != '' && $condition['to_time'] != ''){
                $query->andWhere(" create_time >= '{$from_time}' AND create_time <= '{$to_time}' ");
            }

            //2. Tìm kiếm theo key
            if($condition['keyword'] != ''){
                $query->andWhere(" `title` LIKE '%{$condition['keyword']}%' OR `order_code` LIKE '%{$condition['keyword']}%' ");
            }
            //3. Tìm kiếm theo trạng thái khiếu nại
            if($condition['status'] != ''){
                $query->andWhere("status = '{$condition['status']}'");
            }

            //4. Tìm theo nhân viên xử lý
            if($condition['recipient_by'] > 0){
                $query->andWhere("recipient_by = {$condition['recipient_by']}");
            }

            //5. Tìm theo nhân viên hoàn tiền
            if($condition['approval_by'] > 0){
                $query->andWhere("approval_by = {$condition['approval_by']}");
            }

            //6. Tìm kiếm theo mã sản phẩm
            if($condition['item_id'] != ""){
//                if( is_numeric( $condition['item_id'] ) ) {
//                    $item_id = (int)$condition['item_id'];
//                    $query->andWhere(" `item_id` = {$item_id} ");
//                } else {
//                    $query->andWhere(" `item_id` LIKE '%{$condition['item_id']}%' ");
//                }

                $query->andWhere(" `item_id` LIKE '%{$condition['item_id']}%' ");
            }

            //7. Tìm kiếm theo mã đơn hàng
            if($condition['order_code'] != ""){
                if( is_numeric( $condition['order_code'] ) ) {
                    $order_id = (int)$condition['order_code'];
                    $query->andWhere(" `order_id` = {$order_id} ");
                } else {
                    $query->andWhere(" `order_code` LIKE '%{$condition['order_code']}%' ");
                }
            }

            //8. Có gây thiệt hại cho công ty hay không?
            if($condition['damage'] != ""){
                $query->andWhere("damage = '{$condition['damage']}'");
            }

            //9. Lỗi của bộ phận công ty
            if($condition['error_division_company'] != ""){
                $query->andWhere("error_division_company = '{$condition['error_division_company']}'");
            }
            //10. Lỗi do đối tác
            if($condition['error_partner'] != ""){
                $query->andWhere("error_partner = '{$condition['error_partner']}'");
            }
            //11. Lỗi do người bán
            if($condition['error_seller'] != ""){
                $query->andWhere("error_seller = '{$condition['error_seller']}'");
            }

            //12. Nhân viên xử lý
            if($condition['recipient_by'] > 0){
                $query->andWhere("recipient_by = {$condition['recipient_by']}");
            }
            //13. Nhân viên hoàn tiền
            if($condition['approval_by'] > 0){
                $query->andWhere("approval_by = {$condition['approval_by']}");
            }
            //14. Order_id
            if($condition['order_id'] > 0){
                $query->andWhere("order_id = {$condition['order_id']}");
            }

            //Tìm theo mac khách
            if( $condition['customer_code'] != '' ) {
                $arrCustomer = array();
                //TH1: Tìm theo kiếm ID
                //TH2: Tìm theo mã hoặc username
                if( is_numeric( $condition['customer_code'] ) ){
                    $arrCustomer[] = (int)$condition['customer_code'];
                }else{
                    $users = \UsersPeer::searchByCodeOrUsername( $condition['customer_code'], $condition['customer_code'] );
                    if(sizeof($users) > 0){
                        for($u = 0; $u < sizeof($users); $u++){
                            $arrCustomer[] = $users[$u]->getId();
                        }
                    }
                }

//                print_r($arrCustomer);

                if(sizeof($arrCustomer) > 0){
                    $query->andWhere(" `buyer_id` IN (" . implode(",", $arrCustomer) . ") ");
                } else {
                    $query->andWhere(" `id` = 0 ");
                }
            }

            if( $condition['reasons'] != '' ) {
                $ids = array();
                $str_reason = '';
                $arrReason = explode(',', $condition['reasons']);
                if( sizeof($arrReason) > 0 ) {
                    foreach( $arrReason as $item ) {
                        $str_reason .= '"' . $item . '"';
                    }
                }

                if( $str_reason != '' ) {
                    $q = \ComplaintsReasons::select();
                    $q->addSelect("complaint_id");
                    $q->andWhere(" `long_type` IN (" . $str_reason . ") ");
                    $list_reasons = $q->execute();

                    if( sizeof( $list_reasons ) > 0 ) {
                        foreach( $list_reasons as $item ) {
                            if( $item instanceof \ComplaintsReasons ) {
                                $ids[] = '"' . $item->getComplaintId() . '"';
                            }
                        }
                    }

                    if( sizeof( $ids ) > 0 ) {
                        $query->andWhere(" `id` IN (" . implode(',', $ids) . ") ");
                    }
                }

            }

            $query_count = clone $query;

            switch ($condition['status']){
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
//                    $query->orderBy("recipient_time", "ASC");
                    //Sửa lại thành order theo refocus_time
                    $query->orderBy("refocus_time", "ASC");
                    break;
                case \Complaints::STATUS_REFUND:
                    $query->orderBy("approval_time", "ASC");
                    break;
                default:
                    $query->orderBy("id", "DESC");
                    break;
            }

            $query->setFirstResult($offset)->setMaxResults($this->number_show);
            $complaints = \Complaints::getComplaints($query);

            $total_record = (int)$query_count->count('id')->execute();

            $total_page = $total_record % $this->number_show == 0 ? $total_record / $this->number_show
                : intval($total_record / $this->number_show) + 1;

            //Số đếm theo từng trạng thái
            $total_status_all = $total_status_waiting_receive = $total_status_oustanding
                = $total_status_accept = $total_status_reject = $total_status_refund = 0;

            $q = \Complaints::select();
            $c = $q->execute();
//            print_r($c);
            if(sizeof($c) > 0){
                foreach($c as $item){
                    $total_status_all++;
                    if( $item instanceof \Complaints ) {
                        switch ( $item->getStatus() ) {
                            case \Complaints::STATUS_WAITING_RECEIVE:
                                $total_status_waiting_receive++;
                                break;
                            case \Complaints::STATUS_OUSTANDING:
                                $total_status_oustanding++;
                                break;
                            case \Complaints::STATUS_ACCEPT:
                                $total_status_accept++;
                                break;
                            case \Complaints::STATUS_REJECT:
                                $total_status_reject++;
                                break;
                            case \Complaints::STATUS_REFUND:
                                $total_status_refund++;
                                break;

                            default:
                                //TODO
                                break;
                        }
                    }//end if
                }
            }

            $ajax = new \AjaxResponse();
            $this->view()->assign('status',$condition['status']);
            $this->view()->assign('current_page',$condition['page']);
            $this->view()->assign('per_page',$this->number_show);
            $this->view()->assign('page',$condition['page']);
            $this->view()->assign('total_page',$total_page);
            $this->view()->assign('complaints',$complaints);
            $this->view()->assign('arrStatus', \Complaints::$statusTitle);

            $this->view()->assign('total_status_all', $total_status_all);
            $this->view()->assign('total_status_waiting_receive', $total_status_waiting_receive);
            $this->view()->assign('total_status_oustanding', $total_status_oustanding);
            $this->view()->assign('total_status_accept', $total_status_accept);
            $this->view()->assign('total_status_reject', $total_status_reject);
            $this->view()->assign('total_status_refund', $total_status_refund);

            $this->setView("Complaint/complaint_one");

            $response = array(
                "SQL" => $query->getSQL(),
                "total_record" => $total_record,
                "condition" => $condition,
                "html_result" =>$this->renderPartial()
            );

            return $this->renderText(json_encode($response));
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ' . $e->getMessage();
            return $this->renderText($ajax->toString());
        }
    }

    public function executeGetGirdComplaints(){
        $this->validAjaxRequest();
        //check permission view list complaints
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        try{
            if (!$this->isAllowed(PERMISSION_COMPLAINT_VIEW_LIST)) {
                $ajax->message = self::t('Bạn không có quyền vào khu vực này');
                return $this->renderText($ajax->toString());
            }

            //get params
            $user_id = $this->user->getId();
            $order_id = $this->request()->request("order_id","INT",0);
            $keyword = $this->request()->request("keyword","STRING",'');
            $status = $this->request()->request("status","STRING",'');
            $from_time = $this->request()->request("from_time","STRING",'');
            $to_time = $this->request()->request("to_time","STRING",'');
            $page = $this->request()->request('page',"INT",1);
            $recipient_by = $this->request()->request('recipient_by',"INT",0);//Người tiếp nhận xử lý
            $approval_by = $this->request()->request('approval_by',"INT",0);//Người duyệt tài chính
            $item_code = $this->request()->request('item_code',"STRING", "");
            $order_code = $this->request()->request('order_code',"STRING", "");
            $damage = $this->request()->request('damage', 'STRING', '');

            $error_division_company = $this->request()->request('error_division_company',"STRING", "");
            $error_partner = $this->request()->request('error_partner',"STRING", "");
            $error_seller = $this->request()->request('error_seller',"STRING", "");

            $recipient_by = $this->request()->request('recipient_by',"INT", 0);
            $approval_by = $this->request()->request('approval_by',"INT", 0);
            $all = $this->request()->request('all',"INT", 0);
            $not_in = $this->request()->request('not_in',"ARRAY", array());
            $get_by_buyer = $this->request()->request('get_by_buyer',"INT", 0);

            $limit = $this->request()->request('limit',"INT", $this->number_show);

            $response = \Complaints::GetGirdComplaints($user_id, $keyword, $status, $from_time, $to_time, $page,
                $recipient_by, $approval_by,
                $item_code, $order_code, $damage, $error_division_company,
                $error_partner, $error_seller,
                $recipient_by, $approval_by, $order_id, $limit, $get_by_buyer, $all, $not_in);

            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = 'OK';
            $ajax->status = $status;
            $ajax->current_page = $page;
            $ajax->page = $page;
            $ajax->total_page = $response['total_page'];
            $ajax->total_record = $response['total_record'];
            $ajax->items = $response['items'];
            $ajax->SQL = $response['SQL'];
            $ajax->arrStatus = \Complaints::$statusTitle;

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeDefault(){
        $keyword = $this->request()->request("keyword","STRING",'');
        $status = $this->request()->request("status","STRING","");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $damage = $this->request()->request('damage',"STRING", "NO");

        $error_division_company = $this->request()->request('error_division_company',"STRING", "NO");
        $error_partner = $this->request()->request('error_partner',"STRING", "NO");
        $error_seller = $this->request()->request('error_seller',"STRING", "NO");

        $item_code = $this->request()->request('item_code',"STRING", "");
        $item_id = $this->request()->request('item_id',"STRING", "");
        $order_code = $this->request()->request('order_code',"STRING", "");
        $customer_code = $this->request()->request('customer_code',"STRING", "");

        $recipient_by = $this->request()->request('recipient_by',"INT", 0);
        $approval_by = $this->request()->request('approval_by',"INT", 0);

        $get_by_buyer = $this->request()->request('get_by_buyer',"INT", 0);

        $reasons = $this->request()->request('reasons', 'STRING', '');

        $document = $this->document();
        $document->title = "Danh sách khiếu nại dịch vụ";
        $document->addJsVar("linkGetListComplaints", $this->createUrl('complaint/managerment/GetListComplaints'));
        $document->addJsVar("linkGetStatisticsComplaint", $this->createUrl('complaint/managerment/GetStatistics'));
        $document->addJsVar('ListBackendComplaintUrl', $this->createUrl('complaint/managerment'));
        $document->addJsVar('STATUS_OUSTANDING', \Complaints::STATUS_OUSTANDING);
        $this->setView('Complaint/default');

        $this->view()->assign('keyword', $keyword);
        $this->view()->assign('from_time', $from_time);
        $this->view()->assign('to_time', $to_time);
        $this->view()->assign('status', $status);
        $this->view()->assign('page', $page);
        $this->view()->assign('damage', $damage);
        $this->view()->assign('error_division_company', $error_division_company);
        $this->view()->assign('error_partner', $error_partner);
        $this->view()->assign('error_seller', $error_seller);
        $this->view()->assign('item_code', $item_code);
        $this->view()->assign('order_code', $order_code);
        $this->view()->assign('customer_code', $customer_code);
        $this->view()->assign('item_id', $item_id);
        $this->view()->assign('reasons', $reasons);

        $this->view()->assign('recipient_by', $recipient_by);
        $this->view()->assign('approval_by', $approval_by);

        $this->view()->assign('get_by_buyer', $get_by_buyer);

        return $this->renderComponent();
    }

    /**
     * Thống kê KNDV
     */
    public function executeGetStatistics(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        try{
            if (!$this->isAllowed(PERMISSION_COMPLAINT_VIEW_LIST)) {
                $ajax->message = self::t('Bạn không có quyền vào khu vực này');
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::statisticsComplaint();

            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = 'OK';
            $ajax->status = '';
            $ajax->data = $data;

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

}
?>