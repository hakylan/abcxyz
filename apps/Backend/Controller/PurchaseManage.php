<?php

namespace Backend\Controller;

use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Event\Event;
use Flywheel\Exception;
use SeuDo\Event\Order;
use \SeuDo\SFS\Client;
class PurchaseManage extends BackendBase
{
    public $auth = null;
    public $user = null;

    public function beforeExecute()
    {
        parent::beforeExecute();

        $this->auth = \BaseAuth::getInstance();
        $this->user = $this->auth->getUser();

    }

    public function executeDefault()
    {

        $is_view = $this->isAllowed(PERMISSION_ORDER_VIEW_PURCHASE_MANAGE);
        if(!$is_view){
            die("No Permission");
        }

        $document = $this->document();
        $document->addJsVar('LinkPurchaseLoad',$this->createUrl('PurchaseManage/load_list_Purchase'));
        $document->addJsVar('LinkSelectPurchasers',$this->createUrl('PurchaseManage/select_purchasers'));
        $document->addJsVar('LinkSkipsPurchasers',$this->createUrl('PurchaseManage/skips_purchasers'));
        $document->addJsVar('LinkSaveConfig',$this->createUrl('PurchaseManage/save_config'));
        $document->title = "Quản lý đơn hàng dành cho Quản Lý - Admin Sếu Đỏ";
        $paid_staff_number = \UserRoles::getTotalUserByRoles(\Roles::RolesOrderPaymentsStaffId);
        $teller_number = \UserRoles::getTotalUserByRoles(\Roles::RolesPurchasersId);
        $status_negotiating = \Order::STATUS_NEGOTIATING;
        $status_negotiated = \Order::STATUS_NEGOTIATED;
        $query = \Order::read()->andWhere("status='{$status_negotiated}' OR status='{$status_negotiating}'");
        $total_order = \OrderPeer::countOrderByQuery($query);
        $this->view()->assign("paid_staff_number",$paid_staff_number);
        $this->view()->assign("teller_number",$teller_number);
        $this->view()->assign("total_order",$total_order);
        $this->setView('PurchaseManage/default');
        return $this->renderComponent();
    }

    public function executeLoadListPurchase(){

        $this->validAjaxRequest(true);
        $order_status = $this->request()->request('status');
        $query = \Order::read();
        $page = $this->request()->request('page',"INT",1);

        $num_show = 15;
        $offset = ($page - 1) * $num_show;
        $limit = $page * $num_show;
        if($order_status != '' && $order_status != 'all'){
            if($order_status != 'all'){
                if($order_status == 'khocn' || $order_status == 'khovn'){
                    if($order_status == 'khocn'){
                        $query->andWhere("current_warehouse='CNGZ' or current_warehouse='CNPX'");
                    }elseif($order_status == 'khovn'){
                        $query->andWhere("current_warehouse='VNHN' or current_warehouse='VNSG'");
                    }
//                $condition = array(
//                    "current_warehouse"
//                );
                }else{
                    if($order_status == \Order::CUSTOMER_CONFIRM_WAIT){
                        $query->andWhere("customer_confirm='".\Order::CUSTOMER_CONFIRM_WAIT."'");
                    }else{
                        $query->andWhere("status='{$order_status}'");
                    }
                }
            }
        }else{
            $status_before_bought = \OrderPeer::getBeforeStatus(\Order::STATUS_BOUGHT);
            $status_condition = "(";
            foreach ($status_before_bought as $status) {
                $status_condition .= "status = '{$status}' OR ";
            }
            $status_condition = substr($status_condition,0,strlen($status_condition) - 3);

            $status_condition .= ")";
            $query->andWhere($status_condition);
        }
        $status_init = \Order::STATUS_INIT;
        $query->andWhere("status != '{$status_init}'");

        $query->setFirstResult($offset)->setMaxResults($limit);

        $order_list = \OrderPeer::getOrder($query);
        $query_count = \Order::read();
        if($order_status != '' && $order_status != 'all'){
            if($order_status != 'all'){
                if($order_status == 'khocn' || $order_status == 'khovn'){
                    if($order_status == 'khocn'){
                        $query_count->andWhere("current_warehouse='CNGZ' or current_warehouse='CNPX'");
                    }elseif($order_status == 'khovn'){
                        $query_count->andWhere("current_warehouse='VNHN' or current_warehouse='VNSG'");
                    }
//                $condition = array(
//                    "current_warehouse"
//                );
                }else{
                    if($order_status == \Order::CUSTOMER_CONFIRM_WAIT){
                        $query_count->andWhere("customer_confirm='".\Order::CUSTOMER_CONFIRM_WAIT."'");
                    }else{
                        $query_count->andWhere("status='{$order_status}'");
                    }
                }
            }
        }else{
            $status_before_bought = \OrderPeer::getBeforeStatus(\Order::STATUS_BOUGHT);
            $status_condition = "(";
            foreach ($status_before_bought as $status) {
                $status_condition .= "status = '{$status}' OR ";
            }
            $status_condition = substr($status_condition,0,strlen($status_condition) - 3);

            $status_condition .= ")";
            $query_count->andWhere($status_condition);
        }
        $status_init = \Order::STATUS_INIT;
        $query_count->andWhere("status != '{$status_init}'");

        $total = \OrderPeer::countOrder($query_count);

        $total_page = ceil($total/$num_show);
        $this->view()->assign('total_page',$total_page);
        $this->view()->assign('page',$page);

        $this->view()->assign('order_list',$order_list);
        $this->setView('PurchaseManage/purchase_one');
        return $this->renderPartial();
    }

    public function executeSelectPurchasers(){
        $this->validAjaxRequest();
        $is_select_purchasers = $this->isAllowed(PERMISSION_PURCHASE_SELECT_TELLERS);
        if(!$is_select_purchasers){
            $response = array("type"=>0,"message"=>"Bạn không có quyền chọn người mua hàng");
            return $this->renderText(json_encode($response));
        }
        $order_id = $this->request()->post('order_id','INT',0);//();

        $teller_id = $this->request()->post('teller_id','INT',0);

        if($teller_id == 0){
            $response = array("type"=>0,"message"=>"Không có nhân viên nào được chọn");
            return $this->renderText(json_encode($response));
        }

        $user = \Users::retrieveById($teller_id);

        if(!$user){
            $response = array("type"=>0,"message"=>"Nhân viên không tồn tại trên hệ thống");
            return $this->renderText(json_encode($response));
        }

        $order = \Order::retrieveById($order_id);

        if(!$order){
            $response = array("type"=>0,"message"=>"Đơn hàng bạn yêu cầu không tồn tại");
            return $this->renderText(json_encode($response));
        }

        if($order->status != \Order::STATUS_DEPOSITED){
            $response = array("type"=>0,"message"=>"Đơn hàng đang không phải trạng thái chờ đặt hàng");
            return $this->renderText(json_encode($response));
        }

        $order->setTellersId($teller_id);
        $order->setTellersAssignedTime(date('Y-m-d H:i:s'));
        $order->setStatus(\Order::STATUS_BUYING);
        $result = $order->save();

        if($result){
            $this->dispatch('afterSelectOrderPurchasers', new Event(array('data' => $order)));
            $this->setView('PurchaseManage/select_purchasers');
            $this->view()->assign('paid_staff',$user);
            $this->view()->assign('order',$order);
            $response = array("type"=>1,"message"=>"Chọn người mua hàng thành công",'html'=>$this->renderPartial());
            return $this->renderText(json_encode($response));
        }else{
            $response = array("type"=>0,"message"=>"Chọn người mua hàng không thành công. Xin thử lại ");
            return $this->renderText(json_encode($response));
        }
    }

    public function executeSkipsPurchasers(){

        $this->validAjaxRequest();

        $is_skip_tellers = $this->isAllowed(PERMISSION_PURCHASE_SKIPS_TELLERS);

        if(!$is_skip_tellers){
            $response = array("type"=>0,"message"=>"Bạn không có quyền thu hồi người mua");
            return $this->renderText(json_encode($response));
        }

        $order_id = $this->request()->post('order_id','INT',0);//();

        $type = $this->request()->post('type','STRING',"tellers");
        $order = \Order::retrieveById($order_id);
        if(!$order){
            $response = array("type"=>0,"message"=>"Đơn hàng bạn yêu cầu không tồn tại");
            return $this->renderText(json_encode($response));
        }
        if($type == 'tellers'){
            $order->setTellersId(0);
            $order->setStatus(\Order::STATUS_DEPOSITED);
        }else if($type == 'paid'){
            $order->setPaidStaffId(0);
            $order->setStatus(\Order::STATUS_NEGOTIATING);
        }

        $result = $order->save();

        if($result){
            $this->dispatch('afterSkipsOrderPurchasers', new Event(array('data' => $order)));
            $response = array("type"=>1,"message"=>"Thu hồi thành công");
            return $this->renderText(json_encode($response));
        }

    }

    public function executeSaveConfig(){
        $this->validAjaxRequest();

        $is_change_config = $this->isAllowed(PERMISSION_PURCHASE_CHANGE_CONFIG);

        if(!$is_change_config){
            $response = array("type"=>0,"message"=>"Bạn không có quyền sửa config");
            return $this->renderText(json_encode($response));
        }

        $key = $this->request()->post("key","STRING","");

        $value = $this->request()->post("value","INT",0);

        $result = \SystemConfig::updateConfig($key,$value);

        if($result){
            $response = array("type"=>\AjaxResponse::SUCCESS,"message"=>"Thành công");
            return $this->renderText(json_encode($response));
        }else{
            $response = array("type"=>0,"message"=>"Thất bại, xin thử lại");
            return $this->renderText(json_encode($response));
        }
    }
}
