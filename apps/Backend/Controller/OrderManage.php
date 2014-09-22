<?php

namespace Backend\Controller;

use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Event\Event;
use Flywheel\Exception;
use SeuDo\Event\Order;
use \SeuDo\SFS\Client;
class OrderManage extends BackendBase
{
    private $user;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();

    }

    public function executeDefault()
    {
        $is_view = $this->isAllowed(PERMISSION_ORDER_VIEW_ORDER_MANAGE);
        if(!$is_view){
            die("No Permission");
        }
        $document = $this->document();
        $document->addJsVar('LinkOrderLoad',$this->createUrl('OrderManage/load_list_order'));
        $document->addJsVar('LinkEditFre',$this->createUrl('OrderManage/update_fre'));
        $document->addJsVar('LinkEditInv',$this->createUrl('OrderManage/update_inv'));
        $document->addJsVar('LinkDelOrder',$this->createUrl('OrderManage/del_order'));
        $document->addJsVar('LinkLoadOrderHistory',$this->createUrl('OrderManage/order_history'));
        $document->addJsVar('OrderManageUrl',$this->createUrl('OrderManage/default'));
        $document->title = "Quản lý đơn hàng - Admin SếuĐỏ";
        $this->setView('OrderManage/default');
        return $this->renderComponent();
    }

    public function executeLoadListOrder(){

        $this->validAjaxRequest(true);
        $order_status = $this->request()->request('status');
        $keyword = $this->request()->request('keyword');
        $from_time = $this->request()->request('from_time');
        $to_time = $this->request()->request('to_time');
        $cn_gz = $this->request()->request('cn_gz',"STRING","");
        $cn_px = $this->request()->request('cn_px',"STRING","");
        $vn_hn = $this->request()->request('vn_hn',"STRING","");
        $vn_sg = $this->request()->request('vn_sg',"STRING","");
        $order_by = $this->request()->request('order_by',"STRING","DESC");
        $checking_user = $this->request()->request('checking_user',"INT",0);
        $tellers = $this->request()->request('tellers',"INT",0);
        $query = \Order::read();
        if($order_status != '' && $order_status != 'all'){
            if($order_status == 'khocn' || $order_status == 'khovn'){
                if($order_status == 'khocn'){
                    $query->andWhere("current_warehouse='CNGZ' or current_warehouse='CNPX'");
                }elseif($order_status == 'khovn'){
                    $query->andWhere("current_warehouse='VNHN' or current_warehouse='VNSG'");
                }
            }else{
                $query->andWhere("status='{$order_status}'");
            }
        }

        if($keyword != ''){
            $query->andWhere("code = '%{$keyword}%' OR freight_bill like '%{$keyword}%'");
        }

        if($from_time != '' && $to_time != ''){
            $from_time = new \DateTime($from_time); //($from_time);
            $from_time = $from_time->format("Y:m:d H:i:s");
            $to_time = new \DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("created_time >= '{$from_time}' and created_time<='{$to_time}'");
            $is_delete = 0;
        }

        if($cn_gz != ""){
            $query->andWhere("current_warehouse = '{$cn_gz}'");
        }

        if($cn_px != ""){
            $query->andWhere("current_warehouse = '{$cn_px}'");
        }

        if($vn_sg != ""){
            $query->andWhere("current_warehouse = '{$vn_sg}'");
        }

        if($vn_hn != ""){
            $query->andWhere("current_warehouse = '{$vn_hn}'");
        }

        if($checking_user != 0){
            //$query->andWhere("current_warehouse = '{$vn_hn}'");
        }

        if($tellers != 0){
            $query->andWhere("tellers_id = {$tellers}");
        }

        $query->addOrderBy("id",$order_by);

        $url_del_order = $this->createUrl('OrderManage/del_order');
        $this->view()->assign('url_del_order',$url_del_order);

        $order_list = \OrderPeer::getOrder($query);

        $this->view()->assign('order_list',$order_list);
        $this->setView('OrderManage/order_one');
        return $this->renderPartial();
    }

    public function executeUpdateFre(){
        $this->validAjaxRequest();
        $old_fre = $this->request()->post('old_fre',"STRING",'');
        $new_fre = $this->request()->post('new_fre',"STRING",'');
        $order_id = $this->request()->post('order_id',"INT",0);
        $is_edit_fre = $this->isAllowed(PERMISSION_ORDER_EDIT_FREIGHT_BILL);
        $new_fre = trim($new_fre);
        $this->view()->assign('fre',$new_fre);
        $this->view()->assign('order_id',$order_id);
        $this->setView("OrderManage/edit_fre");
        $order = \Order::retrieveById($order_id);
        if(empty($order)){
            $response['type'] = 0;
            $response['message'] = "Thất bại";
            return $this->renderText(json_encode($response));
        }

        $freight_bill = $order->freight_bill;
        $freight = explode(';',$freight_bill);
        $this->view()->assign('key',count($freight));
        $response = array();
        if($freight_bill == ''){
            if($is_edit_fre){
                $order->setFreightBill($new_fre);
                $order->save();
                $response['type'] = 1;
                $response['html'] = $this->renderPartial();
                    $this->dispatch('afterEditOrderFreightBill', new Event(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['message'] = "Bạn không có quyền thêm";
            }

            return $this->renderText(json_encode($response));
        }

        if($old_fre != ''){
            if($is_edit_fre){
                $freight_bill = str_replace($old_fre,$new_fre,$freight_bill);
                $freight_bill  = str_replace(';;',';',$freight_bill);
                $order->setFreightBill($freight_bill);
                $order->save();
                $response['type'] = 1;
                $response['message'] = "Thành công";
                $this->dispatch('afterEditOrderFreightBill', new \BackendEvent(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['message'] = "Bạn không có quyền sửa mã vận đơn";
            }

            return $this->renderText(json_encode($response));
        }else{
            if($is_edit_fre){
                $freight_bill = "{$freight_bill};{$new_fre}";
                $order->setFreightBill($freight_bill);
                $order->save();
                $response['type'] = 1;
                $response['html'] = $this->renderPartial();
                $this->dispatch('afterEditOrderFreightBill', new \BackendEvent(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['message'] = "Bạn không có quyền thêm mã VĐ";
            }


            return $this->renderText(json_encode($response));
        }
    }

    public function executeUpdateInv(){
        $this->validAjaxRequest();
        $old_inv = $this->request()->post('old_inv',"STRING",'');
        $new_inv = $this->request()->post('new_inv',"STRING",'');
        $order_id = $this->request()->post('order_id',"INT",0);
        $is_edit_inv = $this->isAllowed(PERMISSION_ORDER_EDIT_INVOICE);
        $new_inv = trim($new_inv);
        $this->view()->assign('inv',$new_inv);
        $this->view()->assign('order_id',$order_id);
        $this->setView("OrderManage/edit_inv");
        $order = \Order::retrieveById($order_id);
        $invoice = $order->invoice;
        $invoice_ = explode(';',$invoice);
            $this->view()->assign('key',count($invoice_));
        $response = array();
        if($invoice == ''){
            if($is_edit_inv){
                $order->setInvoice($new_inv);
                $order->save();
                $response['type'] = 1;
                $response['html'] = $this->renderPartial();
                $this->dispatch('afterEditOrderInvoice', new \BackendEvent(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['html'] = "Bạn không có quyền thêm hóa đơn";
            }
            return $this->renderText(json_encode($response));
        }

        if($old_inv != ''){
            if($is_edit_inv){
                $invoice = str_replace($old_inv,$new_inv,$invoice);
                $invoice  = str_replace(';;',';',$invoice);
                $order->setInvoice($invoice);
                $order->save();
                $response['type'] = 1;
                $response['message'] = "Thành công";
                $this->dispatch('afterEditOrderInvoice', new \BackendEvent(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['html'] = "Bạn không có quyền sửa hóa đơn";
            }

            return $this->renderText(json_encode($response));
        }else{
            if($is_edit_inv){
                $invoice = "{$invoice};{$new_inv}";
                $order->setInvoice($invoice);
                $order->save();
                $response['type'] = 1;
                $response['html'] = $this->renderPartial();
                $this->dispatch('afterEditOrderInvoice', new \BackendEvent(array('data' => $order)));
            }else{
                $response['type'] = 0;
                $response['html'] = "Bạn không có quyền thêm hóa đơn";
            }

            return $this->renderText(json_encode($response));
        }
    }

    public function executeDelOrder(){
        $this->validAjaxRequest();
        $is_delete = $this->isAllowed(PERMISSION_ORDER_DELETE_ORDER);
        if(!$is_delete){
            $response['type'] = 0;
            $response['message'] = "Bạn không có quyền để xóa đơn hàng này.";
            return $this->renderText(json_encode($response));
        }
        $order_id = $this->request()->post('order_id','INT',0);

        $response = array();
        if($order_id == 0){
            $response['type'] = 0;
            $response['message'] = "Không có đơn hàng nào được chọn";
            return $this->renderText(json_encode($response));
        }

        $order = \Order::retrieveById($order_id);

        $order->setIsDeleted(1);

        $result = $order->save();

        if($result){
            $this->dispatch('afterDelOrder', new Event(array('data' => $order)));
            $response['type'] = 1;
            $response['message'] = "Xóa thành công";
            return $this->renderText(json_encode($response));
        }else{
            $response['type'] = 0;
            $response['message'] = "Không thành công";
            return $this->renderText(json_encode($response));
        }
    }

    public function executeOrderHistory(){
        $this->validAjaxRequest();
        $order_id = $this->request()->get("order_id","INT",0);
        $is_view = $this->isAllowed(PERMISSION_ORDER_VIEW_HISTORY);


        if(!$is_view){
            $response['type'] = 0;
            $response['message'] = "Bạn không có quyền xem lịch sử đơn";
            return $this->renderText(json_encode($response));
        }
        $query = \OrderHistory::read()->andWhere("order_id = {$order_id}");
        $order_history_list = \OrderHistory::getOrderHistory($query);

        $this->setView('OrderManage/order_history');

        $this->view()->assign("order_history_list",$order_history_list);

        $response = array(
            "type" => \AjaxResponse::SUCCESS,
            "message" => "Thanh cong",
            "html" => $this->renderPartial()
        );

        return $this->renderText(json_encode($response));
    }
}
