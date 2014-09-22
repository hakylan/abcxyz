<?php

namespace Backend\Controller\ComplaintSeller;

use Backend\Controller\BackendBase;
use Flywheel\Db\Query;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use SeuDo\Logger;
use SeuDo\Main;
use Flywheel\Event\Event;

class Init extends BackendBase
{

    /**
     * @var \Users
     */
    private $user = null;
    public function beforeExecute(){
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('logComplaintSellerComment', array(new \BackendEvent(), 'logComplaintSellerComment'));
        $eventDispatcher->addListener('logOrderComment', array(new \BackendEvent(), 'logOrderComment'));
    }

    public function executeDefault(){

    }

    /**
     * Hàm chuyển trạng thái KNNB sang "TIẾP NHẬN"
     * @return string
     */
    public function executeUpdateStatusProcess(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $status = $this->request()->request("status","STRING", "");
            $description = $this->request()->request('description',"STRING", "");
            $refocus_time = $this->request()->request('refocus_time',"STRING", "");
            $level = $this->request()->request('level',"STRING", \ComplaintSeller::LEVEL_SELLER);
            $reason = $this->request()->request('reason',"STRING", "");
            $complaint_seller_id = $this->request()->request('complaint_seller_id',"INT", 0);
            $order_id = $this->request()->request('order_id',"INT", 0);
            $order = \Order::retrieveById($order_id);

            $complaint_seller = \ComplaintSeller::retrieveById($complaint_seller_id);

            if(!$complaint_seller){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Khiếu nại người bán không tồn tại';
                return $this->renderText($ajax->toString());
            }

            if(!$this->isAllowed(PERMISSION_COMPLAINT_SELLER_CAN_PROCESSING)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Bạn không có quyền thực hiện thao tác này';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller->getStatus() != \ComplaintSeller::STATUS_PENDING){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình xử lý dữ liệu';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller_id == 0){
//            $complaint_seller = \ComplaintSeller::retrieveByOrderId($order_id);
                $complaint_seller = \ComplaintSeller::findOneByOrderIdAndFlag($order_id, 0);
                $complaint_seller_id = $complaint_seller->getId();
            }

            //update thông tin khiếu nại
            $flag = \ComplaintSeller::updateInfoComplaintSeller(array('status' => $status,
//                'description' => $description,
//                'refocus_time' => $refocus_time,
//                'reason' => $reason,
//                'level' => $level,
                'processed_time' => date("Y-m-d H:i:s"),
                'processed_by' => $this->user->getId(),
                'id' => $complaint_seller_id));

            if($flag){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'OK';
                $ajax->status = \ComplaintSeller::STATUS_PROCESSING;

                //if success
                //ACTIVITY EXTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'complaint_seller_id' => $complaint_seller_id,
//                'order' => $order,
//                'message' => "Khiếu nại người bán đã được tiếp nhận",
//                "is_public" => false,
//                "is_external" => true,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

                //ACTIVITY INTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'complaint_seller_id' => $complaint_seller_id,
//                'order' => $order,
//                'message' => "Chuyển trạng thái khiếu nại người bán sang đã tiếp nhận",
//                "is_public" => true,
//                "is_external" => false,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

//            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
//                'order' => $order,
//                'sender_id'=>$this->user->getId(),
//                'message_content'=> "Khiếu nại người bán đã được tiếp nhận",
//                'type_chat'=>'activity'
//            )));
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu';
            }

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeUpdateInfo(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $complaint_seller_id = $this->request()->request('complaint_seller_id',"INT", 0);

            $level = $this->request()->request('level',"STRING", "");
            $reason = $this->request()->request('reason',"STRING", "");
            $refocus_time = $this->request()->request('refocus_time',"STRING", "");
            $description = $this->request()->request('description',"STRING", "");

            $complaint_seller = \ComplaintSeller::retrieveById($complaint_seller_id);

            if(!$complaint_seller){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Khiếu nại người bán không tồn tại';
                return $this->renderText($ajax->toString());
            }

            //update thông tin khiếu nại
            $flag = \ComplaintSeller::updateInfoComplaintSeller(array(
                'description' => $description,
                'refocus_time' => $refocus_time,
                'reason' => $reason,
                'level' => $level,
                'id' => $complaint_seller_id));

            if($flag){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'OK';

            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu';
            }

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Hàm cập nhật trạng thái thất bại KNNB
     * @return string
     */
    public function executeUpdateStatusFailure(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $insert_log = $this->request()->request("insert_log","INT", 0);
            $status = $this->request()->request("status","STRING", "");
            $complaint_seller_id = $this->request()->request('complaint_seller_id',"INT", 0);
            $order_id = $this->request()->request('order_id',"INT", 0);

            $level = $this->request()->request('level',"STRING", \ComplaintSeller::LEVEL_SELLER);
            $reason = $this->request()->request('reason',"STRING", "");
            $refocus_time = $this->request()->request('refocus_time',"STRING", "");
            $description = $this->request()->request('description',"STRING", "");

            $order = \Order::retrieveById($order_id);

            if(!$this->isAllowed(PERMISSION_COMPLAINT_SELLER_CAN_REJECT)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Bạn không có quyền thực hiện thao tác này';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller_id == 0){
//            $complaint_seller = \ComplaintSeller::retrieveByOrderId($order_id);
                $complaint_seller = \ComplaintSeller::findOneByOrderIdAndFlag($order_id, 0);
                $complaint_seller_id = $complaint_seller->getId();
            }

            $complaint_seller = \ComplaintSeller::retrieveById($complaint_seller_id);

            if(!$complaint_seller){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Khiếu nại người bán không tồn tại';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller->getStatus() != \ComplaintSeller::STATUS_PROCESSING){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình xử lý dữ liệu';
                return $this->renderText($ajax->toString());
            }

            //update thông tin khiếu nại
            $flag = \ComplaintSeller::updateInfoComplaintSeller(array('status' => $status,
//                'description' => $description,
//                'refocus_time' => $refocus_time,
//                'reason' => $reason,
//                'level' => $level,
                'id' => $complaint_seller_id,
                'rejected_by' => $this->user->getId(),
                'flag' => 1,//Đánh dấu là đã xử lý rồi
                'rejected_time' => date("Y-m-d H:i:s")));

            if( $complaint_seller->checkExistComplaintSellerDoing( $order_id ) == 0 ) {
                $put = \Order::retrieveById($order_id);
                $put->setComplainSeller(0);
                $put->save();
            }

            if($flag){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'OK';
                $ajax->status = \ComplaintSeller::STATUS_FAILURE;

                if($insert_log == 1){
                    //ACTIVITY INTERNAL
                    $this->dispatch('logComplaintSellerComment', new Event($this, array(
                        'order' => $order,
                        'complaint_seller_id' => $complaint_seller_id,
                        'message' => "Chuyển trạng thái khiếu nại người bán sang thất bại",
                        "is_public" => true,
                        "is_external" => false,
                        "is_activity" => true,
                        'is_chat' => false
                    )));
                }

                //ACTIVITY EXTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Khiếu nại người bán thất bại",
                    "is_public" => false,
                    "is_external" => true,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //ACTIVITY INTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Chuyển trạng thái khiếu nại người bán sang thất bại",
                    "is_public" => true,
                    "is_external" => false,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //if success
                //ACTIVITY EXTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'order' => $order,
//                'complaint_seller_id' => $complaint_seller_id,
//                'message' => "Khiếu nại người bán thất bại",
//                "is_public" => false,
//                "is_external" => true,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

                //ACTIVITY INTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'order' => $order,
//                'complaint_seller_id' => $complaint_seller_id,
//                'message' => "Chuyển trạng thái khiếu nại người bán sang thất bại",
//                "is_public" => true,
//                "is_external" => false,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

//            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
//                'order' => $order,
//                'sender_id'=>$this->user->getId(),
//                'message_content'=> "Khiếu nại người bán thất bại",
//                'type_chat'=>'activity'
//            )));
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu';
            }

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Hàm cập nhật trạng thái thành công KNNB
     * @return string
     */
    public function executeUpdateStatusSuccess(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        try{
            $insert_log = $this->request()->request("insert_log","INT", 0);
            $status = $this->request()->request("status","STRING", "");
            $amount_seller_refund = $this->request()->request("amount_seller_refund","FLOAT", 0);
            $description = $this->request()->request('description',"STRING", "");
            $complaint_seller_id = $this->request()->request('complaint_seller_id',"INT", 0);
            $order_id = $this->request()->request('order_id',"INT", 0);

            $level = $this->request()->request('level',"STRING", \ComplaintSeller::LEVEL_SELLER);
            $reason = $this->request()->request('reason',"STRING", "");
            $refocus_time = $this->request()->request('refocus_time',"STRING", "");
            $description = $this->request()->request('description',"STRING", "");

            $order = \Order::retrieveById($order_id);

            if(!$this->isAllowed(PERMISSION_COMPLAINT_SELLER_CAN_ACCEPT)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Bạn không có quyền thực hiện thao tác này';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller_id == 0){
//            $complaint_seller = \ComplaintSeller::retrieveByOrderId($order_id);
                $complaint_seller = \ComplaintSeller::findOneByOrderIdAndFlag($order_id, 0);
                $complaint_seller_id = $complaint_seller->getId();
            }

            $complaint_seller = \ComplaintSeller::retrieveById($complaint_seller_id);

            if(!$complaint_seller){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Khiếu nại người bán không tồn tại';
                return $this->renderText($ajax->toString());
            }

            if($complaint_seller->getStatus() != \ComplaintSeller::STATUS_PROCESSING){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình xử lý dữ liệu';
                return $this->renderText($ajax->toString());
            }

            //update thông tin khiếu nại
            $flag = \ComplaintSeller::updateInfoComplaintSeller(array('status' => $status,
//                'refocus_time' => $refocus_time,
//                'reason' => $reason,
//                'level' => $level,
//                'description' => $description,
                'amount_seller_refund' => $amount_seller_refund,
                'accepted_time' => date("Y-m-d H:i:s"),
                'accepted_by' => $this->user->getId(),
                'flag' => 1,//Đánh dấu là đã xử lý rồi
                'id' => $complaint_seller_id));

            //Nếu không còn khiếu nại nào đang xử lý thì cập nhật trạng thái đơn hàng
            if( $complaint_seller->checkExistComplaintSellerDoing( $order_id ) == 0 ) {
                $put = \Order::retrieveById($order_id);
                $put->setComplainSeller(0);
                $put->save();
            }

            if($flag){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'OK';
                $ajax->status = \ComplaintSeller::STATUS_SUCCESS;

                if($insert_log == 1){
                    $this->dispatch('logComplaintSellerComment', new Event($this, array(
                        'complaint_seller_id' => $complaint_seller_id,
                        'order' => $order,
                        'message' => "Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " . $amount_seller_refund . " NDT",
                        "is_public" => true,
                        "is_external" => false,
                        "is_activity" => true,
                        'is_chat' => false
                    )));
                }

                //ACTIVITY EXTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Khiếu nại người bán thành công",
                    "is_public" => false,
                    "is_external" => true,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //ACTIVITY INTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => "Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " . $amount_seller_refund . " NDT",
                    "is_public" => true,
                    "is_external" => false,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //if success
                //ACTIVITY EXTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'complaint_seller_id' => $complaint_seller_id,
//                'order' => $order,
//                'message' => "Khiếu nại người bán thành công",
//                "is_public" => false,
//                "is_external" => true,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

                //ACTIVITY INTERNAL
//            $this->dispatch('logComplaintSellerComment', new Event($this, array(
//                'complaint_seller_id' => $complaint_seller_id,
//                'order' => $order,
//                'message' => "Chuyển trạng thái khiếu nại người bán sang thành công, số tiền đòi được là " . $amount_seller_refund . " NDT",
//                "is_public" => true,
//                "is_external" => false,
//                "is_activity" => true,
//                'is_chat' => false
//            )));

//            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
//                'order' => $order,
//                'sender_id'=>$this->user->getId(),
//                'message_content'=> "Khiếu nại người bán thành công",
//                'type_chat'=>'activity'
//            )));
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu';
            }

            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi xảy ra trong quá trình cập nhật dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            return $this->renderText($ajax->toString());
        }
    }

    /**
     * Hàm thêm mới KNNB
     * @return string
     */
    public function executeAddComplaintSeller(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        try{
            $data = array();
            $data['reason'] = $this->request()->request('reason',"STRING", "");
            $data['status'] = $this->request()->request("status","STRING", \ComplaintSeller::STATUS_PENDING);
            $data['level'] = $this->request()->request('level', "STRING", "SELLER");
            $data['order_id'] = $this->request()->request('order_id',"INT",0);
            $data['description'] = $this->request()->request('description',"STRING", "");
            $data['refocus_time'] = $this->request()->request('refocus_time',"STRING", "");
            $data['created_by'] = $this->user->getId();
            $data['processed_by'] = 0;
            $data['processed_time'] = '';

            /*
            //check exist
            $check = \ComplaintSeller::checkExistComplaintSellerByOrderId($data['order_id']);
            if($check){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Đơn hàng này đã được tạo khiếu nại rồi!';
                $ajax->complaint_seller_id = 0;
                return $this->renderText($ajax->toString());
            }
            */

            if(!$this->isAllowed(PERMISSION_COMPLAINT_SELLER_CREATE_NEW)){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Bạn không có quyền thực hiện thao tác này';
                $ajax->complaint_seller_id = 0;
                return $this->renderText($ajax->toString());
            }

            if( $data['order_id'] == 0 ) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Không tìm thấy đơn hàng';
                $ajax->complaint_seller_id = 0;
                return $this->renderText($ajax->toString());
            }

            //Kiểm tra xem người tạo khiếu nại có quyền
            $order = \Order::retrieveById($data['order_id']);

            $reason_title = \ComplaintSeller::$reasonTitle[ $data['reason'] ];
            $logComplaintSeller = "Đơn hàng " . $order->getCode() . " đang được khiếu nại với người bán";
            $logOrder = "Đơn hàng " . $order->getCode() . " đang được khiếu nại với người bán. Lý do: " . $reason_title;

            if( $this->isAllowed(PERMISSION_COMPLAINT_SELLER_CAN_PROCESSING) ) {
                $data['status'] = \ComplaintSeller::STATUS_PROCESSING;
                $logComplaintSeller = "Tạo khiếu nại người bán và đang xử lý khiếu nại này. Đơn hàng " . $order->getCode() . " đang được khiếu nại với người bán";

                $data['processed_by'] = $this->user->getId();
                $data['processed_time'] = date("Y-m-d H:i:s");
            }

            $complaint_seller_id = \ComplaintSeller::addComplaintSeller($data);

            if($complaint_seller_id){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = 'OK';
                $ajax->complaint_seller_id = $complaint_seller_id;

                //ACTIVITY EXTERNAL
                $this->dispatch('logComplaintSellerComment', new Event($this, array(
                    'order' => $order,
                    'complaint_seller_id' => $complaint_seller_id,
                    'message' => $logComplaintSeller,
                    "is_public" => false,
                    "is_external" => true,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //ACTIVITY INTERNAL
                $this->dispatch('logComplaintSellerComment', new Event($this, array(
                    'order' => $order,
                    'complaint_seller_id' => $complaint_seller_id,
                    'message' => $logComplaintSeller,
                    "is_public" => true,
                    "is_external" => false,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //ACTIVITY EXTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => $logOrder,
                    "is_public" => false,
                    "is_external" => true,
                    "is_activity" => true,
                    'is_chat' => false
                )));

                //ACTIVITY INTERNAL
                $this->dispatch('logOrderComment', new Event($this, array(
                    'order' => $order,
                    'message' => $logOrder,
                    "is_public" => true,
                    "is_external" => false,
                    "is_activity" => true,
                    'is_chat' => false
                )));

//            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
//                'order' => $order,
//                'sender_id'=>$this->user->getId(),
//                'message_content'=> "Đơn hàng " . $order->getCode() . " đang được khiếu nại với người bán",
//                'type_chat'=>'activity'
//            )));

            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu';
                $ajax->complaint_seller_id = 0;
            }
            return $this->renderText($ajax->toString());
        }catch (\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi xảy ra trong quá trình lưu dữ liệu.Liên hệ kĩ thuật để được hỗ trợ';
            $ajax->complaint_seller_id = 0;
            return $this->renderText($ajax->toString());
        }
    }
}
?>