<?php

namespace Backend\Controller\Complaint;

use Backend\Controller\BackendBase;
use SeuDo\Main;

class Detail extends BackendBase
{
    public function beforeExecute(){
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault(){
//        \Complaints::updateOrderCodeItemCodeInComplaint();
        $complaint_id = (int)$this->get('id');
        $page = $this->request()->get('page', 'INT', 1);

        $document = $this->document();
        $document->title = "Chi tiết khiếu nại";
        $this->setView('Complaint/detail');
        $document->addJsVar("linkUpdateRecipientAmountReimbursement", $this->createUrl('complaint/detail/UpdateRecipientAmountReimbursement'));
        $document->addJsVar("linkUpdateComplaintDamageAmount", $this->createUrl('complaint/detail/UpdateComplaintDamageAmount'));
        $document->addJsVar("linkUpdateStatusComplaint", $this->createUrl('complaint/detail/UpdateStatusComplaint'));
        $document->addJsVar("linkUpdateReasonError", $this->createUrl('complaint/detail/UpdateReasonError'));
        $document->addJsVar("linkUpdateComplaintDescription", $this->createUrl('complaint/detail/UpdateComplaintDescription'));
        $document->addJsVar("linkGetListComplaintSeller", $this->createUrl('ComplaintSeller/Managerment/GetListComplaintSellers'));
        $document->addJsVar('LinkListBackendComplaintUrl', $this->createUrl('Complaint/detail', array('id' => $complaint_id)));
        $document->addJsVar('linkUpdateStatusAcceptComplaint', $this->createUrl('complaint/detail/UpdateStatusAcceptComplaint'));
        $document->addJsVar('linkUpdateStatusRejectComplaint', $this->createUrl('complaint/detail/UpdateStatusRejectComplaint'));

        $document->addJsVar('linkUpdateStatusRefusedReceptionComplaint', $this->createUrl('complaint/detail/UpdateStatusRefusedReceptionComplaint'));

        $document->addJsVar('linkUpdateStatusRefundComplaint', $this->createUrl('complaint/detail/UpdateStatusRefundComplaint'));
        $document->addJsVar('linkUpdateStatusReceptionComplaint', $this->createUrl('complaint/detail/UpdateStatusReceptionComplaint'));

        $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
        if(!$sfsConfig){
            throw new \Exception('Sfs Config is missing !');
        }
        $sfsUrl = $sfsConfig['service_url'];
        $complaint = false;
        $complaint = \Complaints::retrieveById($complaint_id);

        if (!$complaint){
            $this->raise404(self::t('Khiếu nại bạn yêu cầu không tồn tại'));
        }

        $item = \OrderItem::retrieveById($complaint->getItemId());
        $order = \Order::retrieveById($complaint->getOrderId());
        $buyer = \Users::retrieveById($order->getBuyerId());

        //disabled form
        $disabled_form = false;
        if($complaint->getStatus() == \Complaints::STATUS_REFUND){
            $disabled_form = true;
        }

        //permission
        //1. TRAO ĐỔI VỚI KHÁCH HÀNG TRONG KHIẾU NẠI
        $permission_complaint_chat_external = $this->isAllowed(PERMISSION_COMPLAINT_CHAT_EXTERNAL);
        //2. XỬ LÝ KHIẾU NẠI
        $permission_complaint_can_processing = $this->isAllowed(PERMISSION_COMPLAINT_CAN_PROCESSING);
        $permission_complaint_can_accept = $this->isAllowed(PERMISSION_COMPLAINT_CAN_ACCEPT);
        $permission_complaint_can_reject = $this->isAllowed(PERMISSION_COMPLAINT_CAN_REJECT);
        //3. DUYỆT TÀI CHÍNH KHIẾU NẠI
        $permission_complaint_can_censorship_financical = $this->isAllowed(PERMISSION_COMPLAINT_CAN_CENSORSHIP_FINANCICAL);

        $this->view()->assign('complaint', $complaint);
        $this->view()->assign('item', $item);
        $this->view()->assign('order', $order);
        $this->view()->assign('buyer', $buyer);
        $this->view()->assign('sfsUrl', $sfsUrl);
        $this->view()->assign('complaint_id', $complaint_id);
        $this->view()->assign('page', $page);

        $teller_tmp = $payment_tmp = $checked_tmp = $delivery_staff_tmp = array();
        //STAFF
        $teller = \Users::retrieveById($order->getTellersId());
        if ($teller) {
            $teller_tmp = $teller->getAttributes('id,username,code,last_name,first_name');
            $teller_tmp['avatar'] = \Users::getAvatar32x($teller);
            $teller_tmp['detail_link'] = $this->createUrl('user/detail', array('id' => $teller->getId()));
        }

        $payment = \Users::retrieveById($order->getPaidStaffId());
        if ($payment) {
            $payment_tmp = $payment->getAttributes('id,username,code,last_name,first_name');
            $payment_tmp['avatar'] = \Users::getAvatar32x($payment);
            $payment_tmp['detail_link'] = $this->createUrl('user/detail', array('id' => $payment->getId()));
        }

        //USER CHECKED
        $checked = \Users::retrieveById($order->getCheckerId());
        if ($checked) {
            $checked_tmp = $checked->getAttributes('id,username,code,last_name,first_name');
            $checked_tmp['avatar'] = \Users::getAvatar32x($checked);
            $checked_tmp['detail_link'] = $this->createUrl('user/detail', array('id' => $checked->getId()));
        }

        //DELIVERY STAFF - delivery_staff_id
        $delivery_staff = \Users::retrieveById($order->getDeliveryStaffId());
        if ($delivery_staff) {
            $delivery_staff_tmp = $delivery_staff->getAttributes('id,username,code,last_name,first_name');
            $delivery_staff_tmp['avatar'] = \Users::getAvatar32x($delivery_staff);
            $delivery_staff_tmp['detail_link'] = $this->createUrl('user/detail', array('id' => $delivery_staff->getId()));
        }

        $total_pendding_quantity = $total_receive_quantity = $total_order_quantity = 0;
        $total_pendding_quantity = $order->getPendingQuantity();
        $total_receive_quantity = $order->getReciveQuantity();
        $total_order_quantity = $order->getOrderQuantity();

        $this->view()->assign('permission_complaint_chat_external', $permission_complaint_chat_external);
        $this->view()->assign('permission_complaint_can_processing', $permission_complaint_can_processing);
        $this->view()->assign('permission_complaint_can_accept', $permission_complaint_can_accept);
        $this->view()->assign('permission_complaint_can_reject', $permission_complaint_can_reject);
        $this->view()->assign('permission_complaint_can_censorship_financical', $permission_complaint_can_censorship_financical);
        $this->view()->assign('total_services_fee', $order->getServiceFee() + $order->getDomesticShippingFeeVnd());
        $this->view()->assign('order_services', \OrderService::buildOrderServicesArray($order));
        $this->view()->assign('missing_amount', $order->getMissingMoney());
        $this->view()->assign('teller', $teller_tmp);
        $this->view()->assign('payment', $payment_tmp);
        $this->view()->assign('user_checked', $checked_tmp);
        $this->view()->assign('delivery_staff', $delivery_staff_tmp);
        $this->view()->assign('total_pendding_quantity', $total_pendding_quantity);
        $this->view()->assign('total_receive_quantity', $total_receive_quantity);
        $this->view()->assign('total_order_quantity', $total_order_quantity);
        $this->view()->assign('need_checking', $order->needToChecking());
        $this->view()->assign('check_wood_crating', $order->needToWoodCrating());
        $this->view()->assign('check_fragile', $order->needToFragile());
        $this->view()->assign('is_cpn', $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM));
//        $this->view()->assign('order', $order->toArray());

        $document->addJsVar("complaint_id", $complaint_id);
        $document->addJsVar("order_id", $order->getId());
        $document->addJsVar("item_id", $item->getId());

        $arrComplaint = $complaint->toArray();

        if( $arrComplaint['refocus_time'] ) {
            if ( $arrComplaint['refocus_time'] == '0000-00-00 00:00:00' ) {
                $arrComplaint['refocus_time'] = '';
            } else {
                $arrComplaint['refocus_time'] = date("d-m-Y", strtotime( $arrComplaint['refocus_time'] ) );
            }
        }

        $document->addJsVar("complaint", $arrComplaint);
        $document->addJsVar("item", $item->toArray());
        $document->addJsVar("order", $order->toArray());

        $document->addJsVar("permission_complaint_chat_external", $permission_complaint_chat_external);
        $document->addJsVar("permission_complaint_can_processing", $permission_complaint_can_processing);
        $document->addJsVar("permission_complaint_can_accept", $permission_complaint_can_accept);
        $document->addJsVar("permission_complaint_can_reject", $permission_complaint_can_reject);
        $document->addJsVar("permission_complaint_can_censorship_financical", $permission_complaint_can_censorship_financical);
        $document->addJsVar("disabled_form", $disabled_form);

        //info current user
        $this->document()->addJsVar('_account', $this->user->getUsername());
        $this->document()->addJsVar('_first_name', $this->user->getFirstName());
        $this->document()->addJsVar('_user_id', $this->user->getId());
        $this->document()->addJsVar('_username', $this->user->getFullName());
        $this->document()->addJsVar('_img_path', \Users::getAvatar32x($this->user));

        return $this->renderComponent();

    }

    /**
     * Hàm cập nhật lại hạn xử lý cho khiếu nại
     * @return string
     */
    public function executeUpdateRefocusTime() {
        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();

        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->format = 'JSON';
            $ajax->message = "";

            $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $refocus_time = $this->request()->post('refocus_time', 'STRING', '');

            $complaint = \Complaints::retrieveById($camplaint_id);
            if( !$complaint ) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = 'Khiếu nại không tồn tại!';
                return $this->renderText($ajax->toString());
            }

            if( $refocus_time ) {
                $tmpDateFrom = \DateTime::createFromFormat('d-m-Y', $refocus_time);
                if(!$tmpDateFrom){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Thời gian không đúng định dạng. Vui lòng nhập lại cho đúng.';
                    return $this->renderText($ajax->toString());
                }
            }

            $complaint->setRefocusTime( date("Y-m-d", strtotime($refocus_time)) );
            $complaint->save();
            $conn->commit();

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $conn->rollBack();

            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    //Hàm cập nhật số tiền thiệt cho của công ty
    public function executeUpdateComplaintDamageAmount(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->format = 'JSON';
            $ajax->message = "";

            $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $damage_amount = $this->request()->post('damage_amount', 'FLOAT', 0);

            $put = \Complaints::retrieveById($camplaint_id);
            $put->setDamageAmount($damage_amount);
            $put->save();

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }

    }

    //Hàm cập nhật lại số tiền bồi hoàn cho khách
    public function executeUpdateRecipientAmountReimbursement(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->format = 'JSON';
            $ajax->message = "";

            $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $recipient_amount_reimbursement = $this->request()->post('recipient_amount_reimbursement', 'FLOAT', 0);
            $flag = \Complaints::updateRecipientAmountReimbursement($camplaint_id, $recipient_amount_reimbursement);

            //Cập nhật trạng thái khiếu nại về đang xử lý
            $put = \Complaints::retrieveById($camplaint_id);
            $put->setStatus(\Complaints::STATUS_OUSTANDING);
            $put->setRecipientBy($this->user->getId());
            $put->setRecipientTime(date('Y-m-d H:i:s'));
            $put->save();

            if(!$flag){
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->msg = 'Lỗi khi cập nhật dữ liệu!';
                $ajax->format = 'JSON';

                return $this->renderText($ajax->toString());
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateStatusReceptionComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Không tìm thấy khiếu nại";
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_CAN_RECEPTION)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Bạn không có quyền thực hiện thao tác này";
                return $this->renderText($ajax->toString());
            }

            if($complaint->getStatus() != \Complaints::STATUS_WAITING_RECEIVE){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Trạng thái không hợp lệ";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::UpdateStatusReceptionComplaint($complaint_id, $this->user->getId());
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data;
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateStatusRefundComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        try{
            $ajax->format = 'JSON';
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);

            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->transaction_code = "";
                $ajax->message = "Không tìm thấy khiếu nại";
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_CAN_CENSORSHIP_FINANCICAL)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->transaction_code = "";
                $ajax->message = "Bạn không có quyền thực hiện thao tác này";
                return $this->renderText($ajax->toString());
            }

            if($complaint->getStatus() != \Complaints::STATUS_ACCEPT){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->transaction_code = "";
                $ajax->message = "Trạng thái không hợp lệ";
                return $this->renderText($ajax->toString());
            }

            if($complaint->getRecipientAmountReimbursement() == 0){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->transaction_code = "";
                $ajax->message = "Bạn không thể hoàn tiền số tiền 0đ";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::updateStatusRefundComplaint($complaint_id, $this->user->getId());
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data['status'];
                $ajax->transaction_code = $data['transaction_code'];
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->transaction_code = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->transaction_code = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Hàm từ chối tiếp nhận khiếu nại ngay tại bước tiếp nhận
     */
    public function executeUpdateStatusRefusedReceptionComplaint() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{

            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);

            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Không tìm thấy khiếu nại";
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_CAN_REJECT)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Bạn không có quyền thực hiện thao tác này";
                return $this->renderText($ajax->toString());
            }

            if( $complaint->getStatus() != \Complaints::STATUS_WAITING_RECEIVE ){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Trạng thái không hợp lệ";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::updateStatusRejectComplaint($complaint_id, $this->user->getId());

            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data;
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateStatusRejectComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);

            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Không tìm thấy khiếu nại";
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_CAN_REJECT)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Bạn không có quyền thực hiện thao tác này";
                return $this->renderText($ajax->toString());
            }

            if($complaint->getStatus() != \Complaints::STATUS_OUSTANDING
                && $complaint->getStatus() != \Complaints::STATUS_ACCEPT){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Trạng thái không hợp lệ";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::updateStatusRejectComplaint($complaint_id, $this->user->getId());
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data;
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateStatusAcceptComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $flag_amount = $this->request()->post('flag_amount', 'INT', 0);

            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Không tìm thấy khiếu nại";
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_CAN_ACCEPT)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Bạn không có quyền thực hiện thao tác này";
                return $this->renderText($ajax->toString());
            }

            if($complaint->getStatus() != \Complaints::STATUS_OUSTANDING){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Trạng thái không hợp lệ";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::updateStatusAcceptComplaint($complaint_id, $this->user->getId(), $flag_amount);
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data;
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    //Hàm cập nhật trạng thái cho khiếu nại
    public function executeUpdateStatusComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $type = $this->request()->post('type');
            $data = \Complaints::updateStatusComplaint($complaint_id, $this->user->getId(), $type);
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = $data;
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    //Hàm cập nhật lý do ở bộ phận nào của công ty, hay cho người bán,...
    public function executeUpdateReasonError(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "";

            $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $status = $this->request()->post('status', 'STRING', "");
            $type = $this->request()->post('type', 'STRING', "");

            $put = \Complaints::retrieveById($camplaint_id);
            //Thiệt hại công ty
            if($type == "DAMAGE"){
                $put->setDamage($status);
                $put->save();
            }

            //Lỗi xuất phát - Từ bộ phận công ty
            if($type == "ERROR_DIVISION_COMPANY"){
                $put->setErrorDivisionCompany($status);
                $put->save();
            }
            //Lỗi xuất phát - Từ phía đối tác
            if($type == "ERROR_PARTNER"){
                $put->setErrorPartner($status);
                $put->save();
            }
            //Lỗi xuất phát - Từ phía người bán
            if($type == "ERROR_SELLER"){
                $put->setErrorSeller($status);
                $put->save();
            }

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateComplaintDescription(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "";

            $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $description = $this->request()->post('description', 'STRING', "");

            $put = \Complaints::retrieveById($camplaint_id);
            $put->setDescription($description);
            $put->save();

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