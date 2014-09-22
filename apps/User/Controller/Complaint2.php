<?php
/**
 * Created by PhpStorm.
 * User: hosi
 * Date: 5/28/14
 * Time: 4:23 PM
 */

namespace User\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Filesystem\Uploader;
use SeuDo\Main;
use Flywheel\Util\Folder;
use \SeuDo\SFS\Client;
use \SeuDo\SFS\Upload;
use \mongodb\ComplaintCommentResource\BaseContext;
use \mongodb\ComplaintCommentResource\Chat;
use \mongodb\ComplaintCommentResource\Activity;
use \mongodb\ComplaintCommentResource\Log;

class Complaint2 extends UserBase
{

    private $number_show = 10;

    public function beforeExecute() {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault() {
//        echo 'vao day'; exit;
        $order_item_id = (int)$this->get('item_id');
        $order_id = (int)$this->get('order_id');
        $step = $this->get('step') ? (int)$this->get('step') : 1;

        $this->setLayout('complaint');
        $current_user_avatar = \Users::getAvatar32x($this->user);
        $document = $this->document();
        $document->title = "Khách hàng khiếu nại";
        $document->addJsVar("linkgetOneOrderItem", Main::getHomeRouter()->createUrl('user/OrderInit/LoadOneOrderItem'));
        $document->addJsVar("linkgetComplaintReasons", Main::getHomeRouter()->createUrl('user/Complaint2/GetListReasons'));
        $document->addJsVar("linkAddComplaintOrderItemComment", Main::getHomeRouter()->createUrl('user/test'));
        $document->addJsVar("linkMoreComplaintItemComment", Main::getHomeRouter()->createUrl('user/Complaint2/ListMoreComments'));

        $document->addJsVar("linkAddComplaint", Main::getHomeRouter()->createUrl('user/Complaint2/AddComplaint'));
        $document->addJsVar("linkGetInfoComplaint", Main::getHomeRouter()->createUrl('user/Complaint2/GetInfoComplaint'));
        $document->addJsVar("linkDeleteFileComplaint", Main::getHomeRouter()->createUrl('user/Complaint2/DeleteFileComplaint'));
        $document->addJsVar("linkGetListFileComplaint", Main::getHomeRouter()->createUrl('user/Complaint2/GetListFileComplaint'));
        $document->addJsVar("linkUpdateCustomerAmountReimbursement", Main::getHomeRouter()->createUrl('user/Complaint2/UpdateCustomerAmountReimbursement'));
        $document->addJsVar("linkGetListComplaints", Main::getHomeRouter()->createUrl('user/Complaint2/GetListComplaints'));

        $document->addJsVar("linkUpdateStatusComplaint", Main::getHomeRouter()->createUrl('user/Complaint2/UpdateStatusComplaint'));
        $document->addJsVar("linkAddComplaintItemComment", Main::getHomeRouter()->createUrl('user/Complaint2/AddMessage'));

        $complaint_info = \Complaints::getOneComplaint($order_id, $order_item_id);

        // step1: add new complaint; step2: PRODUCT_NOT_RECEIVED; step3: PRODUCT_ERROR
        if(isset($complaint_info['id']) && $complaint_info['id'] > 0){
            $step = $complaint_info['type'] == \Complaints::TYPE_PRODUCT_ERROR ? 3 : 2;
        }

        $complaint = false;
        $complaint = \Complaints::retrieveById($complaint_info['id']);

        if($complaint && $this->user->getId() != (int)$complaint->getBuyerId()){
            $this->redirect($this->createUrl('error/not_found'));
        }

        $document->addJsVar("order_item_id", $order_item_id);
        $document->addJsVar("order_id", $order_id);
        $document->addJsVar("step", $step);
        $document->addJsVar('first_name', $this->user->getFirstName());
        $document->addJsVar('current_user_id', $this->user->getId());
        $document->addJsVar("current_username", $this->user->getFullName());
        $document->addJsVar("current_user_avatar", $current_user_avatar);
        $document->addJsVar("complaint_id", (int)$complaint_info['id']);
        if($complaint){
            $document->addJsVar("complaint", \Complaints::retrieveById($complaint_info['id'])->toArray());
        }else{
            $document->addJsVar("complaint", array());
        }

        $this->view()->assign(array('current_user_avatar' => $current_user_avatar,
            'complaint_info' => $complaint_info));

        $this->setView('Complaint/default');
        return $this->renderComponent();
    }

    //Hàm cập nhật trạng thái cho khiếu nại
    public function executeUpdateStatusComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
            $type = $this->request()->post('type', 'STRING', "");

            $complaint = false;
            $complaint = \Complaints::retrieveById($complaint_id);
            if(!$complaint){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->recipient_amount_reimbursement = 0;
                $ajax->message = "Khiếu nại không tồn tại!";
                return $this->renderText($ajax->toString());
            }

            $data = \Complaints::updateStatusComplaint($complaint_id, $this->user->getId(), $type);
            if($data){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->status = \Complaints::updateStatusComplaint($complaint_id, $this->user->getId(), $type);
                $ajax->recipient_amount_reimbursement = $complaint->getRecipientAmountReimbursement();
                $ajax->message = "Thành công";
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->status = "";
                $ajax->recipient_amount_reimbursement = 0;
                $ajax->message = "Có lỗi xảy ra trong quá trình cập nhật dữ liệu!";
            }
            return $this->renderText($ajax->toString());
        }catch (\Flywheel\Exception $e){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->status = "";
            $ajax->recipient_amount_reimbursement = 0;
            $ajax->message = "Lỗi kỹ thuật. Vui lòng liên hệ kỹ thuật để được hỗ trợ.";
            return $this->renderText($ajax->toString());
        }
    }

    public function executeList(){


        /*
        $this->setView('Complaint/list');
        $document = $this->document();
        $document->addJsVar("linkGetListComplaints", Main::getHomeRouter()->createUrl('user/Complaint2/GetListComplaints'));
        $this->view()->assign(array('root' => Main::getHomeUrl()));
        return $this->renderComponent();
        */

        $keyword = $this->request()->request("keyword","STRING",'');
        $order_status = $this->request()->request("status","STRING","DeletedOut");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $document = $this->document();
        $document->title = "Danh sách khiếu nại";
        $document->addJsVar('ListComplaintUrl',Main::getUserRouter()->createUrl('danh-sach-khieu-nai'));
        $document->addJsVar("linkGetListComplaints", Main::getHomeRouter()->createUrl('user/Complaint2/GetListComplaints'));
        $this->setView("Complaint/list");

        $this->view()->assign('keyword', $keyword);
        $this->view()->assign('from_time', $from_time);
        $this->view()->assign('to_time', $to_time);
        $this->view()->assign('status', $order_status);
        $this->view()->assign('page', $page);

        return $this->renderComponent();
    }

    public function executeListMoreComments(){
        $this->validAjaxRequest();

        $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $page = $this->request()->post('page', 'INT', 0);
        $page_size = $this->request()->post('page_size', 'INT', 0);
        $scope = $this->request()->post('scope', 'STRING', 'EXTERNAL');

//        echo '$complaint_id: ' . $complaint_id;
//        echo '$page: ' . $page;
//        echo '$page_size: ' . $page_size;

        if ($complaint_id > 0 && $page > 0 && $page_size > 0) {
            // default show chat infor external
            $results = \ComplaintComment::loadComplaintItemComments($complaint_id, $page, $page_size, $scope);
            $pages = $results['pages'];
            // Condition load item comments when has page load
            if ($page <= $pages) {
                $page_next = $page + 1;
                $ajax = new \AjaxResponse();
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->order_id = $order_id;
                $ajax->item_id = $item_id;
                $ajax->page_next = $page_next;
                $ajax->pages = $pages;
                $ajax->format = 'JSON';
                $ajax->info = $results['data'];

                return $this->renderText($ajax->toString());
            }
        }
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->msg = 'Lỗi không lấy được dữ liệu!';

        return $this->renderText($ajax->toString());
    }

    public function executeAddMessage() {
        $this->validAjaxRequest();
        $message = $this->request()->post('message');
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);
        $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
        $type = $this->request()->post('type');
        $context = $this->request()->post('context', 'STRING', 'CHAT');

        // Check chat channel
        if ($type==\mongodb\OrderComment::TYPE_EXTERNAL) { // external
            //TODO
        }
        $username = \OrderComment::USER_SYSTEM;
        $is_public_profile = true;

        $img_path = \Users::getAvatar32x($this->user);
        $time = "";
        $message = \ComplaintComment::convertToText($message);
        $ok = false;
        if(strlen($message) > 0 and $order_id > 0) {
            if ($this->user instanceof \Users) {
                $user_id = $this->user->getId();
                if ($is_public_profile) {
                    $username = $this->user->getFullName();
                }
                if($context == 'CHAT'){
                    $context = new Chat($message);
                }
                if($context == 'ACTIVITY'){
                    $context = new Activity($message);
                }
                if($context == 'LOG'){
                    $context = new Log($message);
                }

                $created_time = new \MongoDate();
                $time = date('h:i:s d/m/Y', $created_time->sec);
                $type_context = BaseContext::TYPE_CHAT;
                $ok = \ComplaintComment::addComment($user_id, $order_id, $item_id, $complaint_id, $type, $context, $is_public_profile,
                    $type_context);
            }
        }
        if($ok){
            $info = array('username' => $username, 'message' => $message, 'time' => $time,
                'user_id' => $user_id, 'img_path' => $img_path);
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->type_chat = $type;
            $ajax->info = $info;
            $ajax->format = 'JSON';

            return $this->renderText($ajax->toString());
        } else {
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->msg = 'Lỗi khi lưu dữ liệu!';
            $ajax->format = 'JSON';

            return $this->renderText($ajax->toString());
        }
    }

//    public function executeAddMessage() {
//        $this->validAjaxRequest();
//
//        $message = $this->request()->post('message');
//        $complaint_id = $this->request()->post('complaint_id', 'INT', 0);
//        $order_id = $this->request()->post('order_id', 'INT', 0);
//        $item_id = $this->request()->post('item_id', 'INT', 0);
//        $type = $this->request()->post('type') ? $this->request()->post('type') : '';
//
//        $message = \ComplaintComment::convertToText($message);
//        $ok = false;
//        if(strlen($message) > 0 && $complaint_id > 0 && $order_id > 0 && $item_id > 0) {
//            if ($this->user instanceof \Users) {
//                $user_id = $this->user->getId();
//                $username = $this->user->getFullName();
//                $created_time = new \MongoDate();
//                $time = date('h:i:s d/m/Y', $created_time->sec);
//                $ok = \ComplaintComment::addComment($user_id, $order_id, $item_id, $message, $created_time, $type);
//            }
//        }
//        if($ok){
//            $data = array('user_id' => $user_id, 'username' => $username, 'message' => $message, 'time' => $time);
//            $ajax = new \AjaxResponse();
//            $ajax->type = \AjaxResponse::SUCCESS;
//            $ajax->info = $data;
//            $ajax->format = 'JSON';
//
//            return $this->renderText($ajax->toString());
//        } else {
//            $ajax = new \AjaxResponse();
//            $ajax->type = \AjaxResponse::ERROR;
//            $ajax->msg = 'Lỗi khi lưu dữ liệu!';
//            $ajax->format = 'JSON';
//
//            return $this->renderText($ajax->toString());
//        }
//    }

    public function executeGetListFileComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->format = 'JSON';
        $ajax->message = "";
        $order_id = $this->request()->get('order_id', 'INT', 0);
        $item_id = $this->request()->get('item_id', 'INT', 0);
        $complaint = \Complaints::getOneComplaint($order_id, $item_id);
        $complaint_id = (int)$complaint['id'];

        $items = \Complaints::getListFileComplaint($complaint_id);
        $ajax->items = (array)$items;

        return $this->renderText($ajax->toString());
    }

    //Hàm cập nhật lại số tiền mà khách yêu cầu bồi hoàn trong khiếu nại
    public function executeUpdateCustomerAmountReimbursement(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->format = 'JSON';
        $ajax->message = "";

        $camplaint_id = $this->request()->post('complaint_id', 'INT', 0);
        $customer_amount_reimbursement = $this->request()->post('customer_amount_reimbursement', 'FLOAT', 0);

        $put = \Complaints::retrieveById($camplaint_id);
        $put->setCustomerAmountReimbursement($customer_amount_reimbursement);
        $put->setCustomerAmountReimbursementTime(date('Y-m-d H:i:s'));
        $put->save();

        return $this->renderText($ajax->toString());
    }

    public function executeDeleteFileComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->format = 'JSON';
        $ajax->message = "";

        $file_id = $this->request()->post('file_id', 'INT', 0);
        $put = \ComplaintsFiles::retrieveById($file_id);
        $put->setInvalid("DELETED");
        $put->save();

        return $this->renderText($ajax->toString());
    }

    public function executeUploadImage()
    {

        $error = '';
        $order_id = $this->request()->post('order_id', 'INT', 0);
        $item_id = $this->request()->post('item_id', 'INT', 0);

        $host = Main::getHomeUrl();

        $path = PUBLIC_DIR . "public" . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR;

        if(!Folder::exists($path)){
            Folder::create($path);
        }

        //info complaint
        $complaint = \Complaints::getOneComplaint($order_id, $item_id);
        $complaint_id = (int)$complaint['id'];

        //Kiểm tra xem đã upload quá 6 ảnh hay chưa
        if(\Complaints::checkMaxFileUpload($complaint_id) == false){
            echo json_encode(array(
                'flag' => false,
                'message' => 'Bạn chỉ được phép upload tối đa 6 file ảnh!'
            ));
            exit;
        }

        if ($this->request()->isPostRequest()) {
            $fileUpload = new Uploader($path,'photoimg');

            $fileUpload->setMaximumFileSize(8);//8mb
            $fileUpload->setFilterType('.jpg, .jpeg, .png, .bmp, .gif');
            $fileUpload->setIsEncryptFileName(true);

            if ($fileUpload->upload('photoimg')) {
                $data = $fileUpload->getData();

                $urlImage = $host . "public/temp/" . $data['file_name'];

                $ext = strrchr($urlImage, '.');
                $ext = strtolower($ext);
                $filename = uniqid() . '_' . time() . $ext;

                $sfs = Client::getInstance();

                $uploader = new Upload('complaint');
                $uploader->setUrl($urlImage);
                $uploader->setFileName($filename);

                if ($sfs->upload($uploader)) {
                    $sfs->getHttpCode();
                }

                //insert file
                $post = new \ComplaintsFiles();
                $post->name = $filename;
                $post->path = 'media' . DIRECTORY_SEPARATOR . 'complaint' . DIRECTORY_SEPARATOR . $filename;
                $post->complaint_id = $complaint_id;
                $post->file_type = '';
                $post->create_time = date('Y-m-d H:i:s');
                $post->save();
                $file_id = $post->getId();

                $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
                if(!$sfsConfig){
                    throw new \Exception('Sfs Config is missing !');
                }
                $sfsUrl = $sfsConfig['service_url'];

                echo json_encode(array(
                    'file_name' => $data['file_name'],
                    'path' => 'media' . DIRECTORY_SEPARATOR . 'complaint' . DIRECTORY_SEPARATOR . $filename,
                    'id' => $file_id,
                    'flag' => true,
                    'message' => '',
                    'service_url' => $sfsUrl,
                    'test' => $sfs->getResponse()
                ));
                exit;
            } else {
                $error = $fileUpload->getError();
                echo json_encode(array(
                    'flag' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình upload file!',
                    'error' => $error[0]
                ));
                exit;
            }
        }
    }

    public function executeGetInfoComplaint(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->format = 'JSON';
        $ajax->message = "";

        $id = $this->request()->get('id', 'INT', 0);
        $query = \Complaints::read();
        $query->andWhere("id = {$id}");
        $item = $query->execute()->fetch();

        $ajax->item = (array)$item;

        return $this->renderText($ajax->toString());
    }

    public function executeAddComplaint(){
        $this->user = \BaseAuth::getInstance()->getUser();
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        $data['item_id'] = (int)$this->request()->post('item_id');
        $data['order_id'] = (int)$this->request()->post('order_id');
        $data['type'] = $this->request()->post('type');
        $data['quantity'] = (float)$this->request()->post('quantity');
        $data['reasons'] = (array)$this->request()->post('reasons', "ARRAY", array());

        //Kiểm tra xem đã tồn tại khiếu nại hay chưa?
        $complaint = \Complaints::getOneComplaint($data['order_id'], $data['item_id']);
        $complaint_id = (int)$complaint['id'];
        if($complaint_id > 0){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Sản phẩm này đã được khiếu nại!!!";
            $ajax->complaint_id = $complaint_id;
            return $this->renderText($ajax->toString());
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = "";
        $item = \OrderItem::retrieveById($data['item_id']);
        $order = \Order::retrieveById($data['order_id']);

        $title = 'Khiếu nại sản phẩm ' . $item->getId() . ', đơn hàng ' . $order->getCode();
        //1. Lưu dữ liệu vào bảng complaints

        //1.1. Nếu là trường hợp không nhận được hàng thì số tiền bồi hoàn cho được tính = Số lượng thiếu * Đơn giá SP

        $customer_amount_reimbursement = 0;//Mức bồi hoàn cho khách
        if($data['type'] == \Complaints::TYPE_PRODUCT_NOT_RECEIVED){
            if($data['quantity'] < $item->getOrderQuantity()){
                $customer_amount_reimbursement = (float)( ( $item->getOrderQuantity() - $data['quantity'] ) * $item->getPrice() );
            }
        }

        $post = new \Complaints();
        $post->item_id = $data['item_id'];
        $post->order_id = $data['order_id'];
        $post->order_code = $order->getCode();
        $post->type = $data['type'];
        $post->title = $title;
        $post->quantity = $data['quantity'];
        $post->create_time = date('Y-m-d H:i:s');
        $post->buyer_id = $this->user->getId();
        $post->customer_amount_reimbursement = $customer_amount_reimbursement;
        $post->save();
        $complaint_id = $post->getId();

        //2. Nếu là sản phẩm lỗi (PRODUCT_ERROR) insert dữ liệu vào bảng complaints_reasons

        if($data['type'] == \Complaints::TYPE_PRODUCT_ERROR){
            foreach((array)$data['reasons'] as $r){
                if($r){
                    $reason = new \ComplaintsReasons();
                    $reason->complaint_id = $complaint_id;
                    $reason->create_time = date('Y-m-d H:i:s');
                    $reason->long_type = $r;
                    $reason->save();
                }
            }
        }

        $ajax->complaint_id = $complaint_id;

        return $this->renderText($ajax->toString());
    }

    public function executeGetListReasons(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = "";

        $ajax->reasons = \Complaint::$reasons;
        return $this->renderText($ajax->toString());
    }

    public function executeGetListComplaints(){
        $this->validAjaxRequest();
        //get params
        $user_id = $this->user->getId();
//        echo '$user_id: ' . $user_id;
//        exit;
        $keyword = $this->request()->request("keyword","STRING",'');
        $status = $this->request()->request("status","STRING",'');
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $recipient_by = $this->request()->request('recipient_by',"INT",0);//Người tiếp nhận xử lý
        $approval_by = $this->request()->request('approval_by',"INT",0);//Người duyệt tài chính
        $item_code = $this->request()->request('item_code',"STRING", "");
        $damage = $this->request()->request('damage', 'STRING', '');
        $errors = $this->request()->request('errors', 'ARRAY', array());

        $offset = ($page - 1) * $this->number_show;
        $limit = $page * $this->number_show;

//        echo '$offset: ' . $offset;
//        echo '$limit: ' . $limit;

        $query = \Complaints::read();
//        echo '$user_id: ' . $user_id;
        $query->andWhere("buyer_id = {$user_id}");
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
            $query->andWhere(" `item_code` LIKE '%{$item_code}%' ");
        }

        //7. Có gây thiệt hại cho công ty hay không?
        if($damage != ""){
            $query->andWhere("damage = " . \Complaints::YES);
        }

        //8. Lỗi xuất phát từ đâu
        if(sizeof($errors) > 0){
            foreach((array)$errors as $key => $value){
                $query->andWhere("{$key} = " . \Complaints::YES);
            }
        }
//        echo $query->getSQL();
        $query_count = clone $query;

        $query->orderBy("id", "DESC");
        $query->setFirstResult($offset)->setMaxResults($this->number_show);
        $complaints = \Complaints::getComplaints($query);

        $total_record = (int)$query_count->count('id')->execute();

        $total_page = $total_record % $this->number_show == 0 ? $total_record / $this->number_show
            : intval($total_record / $this->number_show) + 1;

        $ajax = new \AjaxResponse();
        $this->view()->assign('status',$status);
        $this->view()->assign('current_page',$page);
        $this->view()->assign('per_page',$this->number_show);
        $this->view()->assign('page',$page);
        $this->view()->assign('total_page',$total_page);
        $this->view()->assign('complaints',$complaints);
        $this->view()->assign('root', Main::getHomeRouter());
        $this->view()->assign('arrStatus', \Complaints::$statusTitle);
        $this->setView("Complaint/complaint_one");

        $response = array(
            "total_record" => $total_record,
            "html_result" =>$this->renderPartial()
        );

        return $this->renderText(json_encode($response));
    }

}

?>