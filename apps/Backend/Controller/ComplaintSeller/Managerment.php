<?php

namespace Backend\Controller\ComplaintSeller;

use Backend\Controller\BackendBase;
use Flywheel\Db\Query;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use SeuDo\Logger;
use SeuDo\Main;

class Managerment extends BackendBase
{
    private $number_show = 10;

    public function beforeExecute(){
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeGetListComplaints(){
        //TODO
    }

    public function executeDefault(){
        $page = $this->request()->request('page',"INT",1);
        $status = $this->request()->request("status","STRING", \ComplaintSeller::STATUS_PROCESSING);
        $order_id = $this->request()->request('order_id', "INT",0);
        $order_code = $this->request()->request('order_code',"STRING","");
        $order_seller_name = $this->request()->request('order_seller_name',"STRING", "");
        $order_seller_aliwang = $this->request()->request('order_seller_aliwang',"STRING", "");
        $order_seller_homeland = $this->request()->request('order_seller_homeland',"STRING", "");
        $order_seller_info = $this->request()->request('order_seller_info',"STRING", "");
        $order_invoice = $this->request()->request('order_invoice',"STRING", "");
        $order_buyer_id = $this->request()->request('order_buyer_id',"INT", 0);
        $account_purchase_origin = $this->request()->request('account_purchase_origin',"INT", 0);
        $account_purchase_origin_name = $this->request()->request('account_purchase_origin_name',"STRING", "");
        $reason = $this->request()->request('reason',"STRING", "");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $level = $this->request()->request('level',"STRING","");
        $seller_homeland = $this->request()->request("seller_homeland","STRING",'');
        $key1 = $this->request()->request("key1","STRING",'');
        $key2 = $this->request()->request("key2","STRING",'');

        $document = $this->document();
        $document->title = "Danh sách khiếu nại";
        $document->addJsVar("linkGetListComplaintSellers", $this->createUrl('ComplaintSeller/managerment/GetListComplaintSellers'));
        $document->addJsVar('ListBackendComplaintSellerUrl', $this->createUrl('ComplaintSeller/managerment'));

        $buyers = \ComplaintSeller::getBuyersByComplaintSeller();
        $q = \UserOriginSite::read();
        $user_origin_site = $q->execute()->fetchAll(\PDO::FETCH_CLASS, \ComplaintSeller::getPhpName(), array(null, false));

        $this->setView('ComplaintSeller/default');
        $this->view()->assign('page', $page);
        $this->view()->assign('status', $status);
        $this->view()->assign('order_id', $order_id);
        $this->view()->assign('level', $level);
        $this->view()->assign('order_code', $order_code);
        $this->view()->assign('order_seller_name', $order_seller_name);
        $this->view()->assign('order_seller_aliwang', $order_seller_aliwang);
        $this->view()->assign('order_seller_homeland', $order_seller_homeland);
        $this->view()->assign('order_seller_info', $order_seller_info);
        $this->view()->assign('order_invoice', $order_invoice);
        $this->view()->assign('order_buyer_id', $order_buyer_id);
        $this->view()->assign('reason', $reason);
        $this->view()->assign('seller_homeland', $seller_homeland);
        $this->view()->assign('from_time', $from_time);
        $this->view()->assign('to_time', $to_time);
        $this->view()->assign('key1', $key1);
        $this->view()->assign('key2', $key2);
        $this->view()->assign('buyers', $buyers);
        $this->view()->assign('user_origin_site', $user_origin_site);
        $this->view()->assign('account_purchase_origin', $account_purchase_origin);
        $this->view()->assign('account_purchase_origin_name', $account_purchase_origin_name);

        return $this->renderComponent();
    }

    public function executeGetListComplaintSellers(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        try{
            //check permission view list complaints
            if (!$this->isAllowed(PERMISSION_COMPLAINT_SELLER_VIEW_LIST)) {
                $ajax->message = self::t('Bạn không có quyền vào khu vực này');
                return $this->renderText($ajax->toString());
            }

            $page = $this->request()->request('page',"INT",1);
            $condition['status'] = $this->request()->request("status","STRING", \ComplaintSeller::STATUS_PROCESSING);
            $condition['order_id'] = $this->request()->request('order_id', "INT",0);
            $condition['order_code'] = $this->request()->request('order_code',"STRING","");
            $condition['level'] = $this->request()->request('level',"STRING","");
            $condition['order_seller_name'] = $this->request()->request('order_seller_name',"STRING", "");
            $condition['order_seller_aliwang'] = $this->request()->request('order_seller_aliwang',"STRING", "");
            $condition['order_seller_homeland'] = $this->request()->request('order_seller_homeland',"STRING", "");
            $condition['order_seller_info'] = $this->request()->request('order_seller_info',"STRING", "");
            $condition['order_invoice'] = $this->request()->request('order_invoice',"STRING", "");
            $condition['order_buyer_id'] = $this->request()->request('order_buyer_id',"INT", 0);
            $condition['account_purchase_origin'] = $this->request()->request('account_purchase_origin',"INT", 0);
            $condition['account_purchase_origin_name'] = $this->request()->request('account_purchase_origin_name',"STRING", "");
            $condition['reason'] = $this->request()->request('reason',"STRING", '');
            $condition['from_time'] = $this->request()->request("from_time","STRING",'');
            $condition['to_time'] = $this->request()->request("to_time","STRING",'');
            $condition['seller_homeland'] = $this->request()->request("seller_homeland","STRING",'');
            $condition['key1'] = $this->request()->request("key1","STRING",'');
            $condition['key2'] = $this->request()->request("key2","STRING",'');
            $condition['all'] = $this->request()->request("all","INT",0);
            $limit = $this->request()->request("limit","INT",$this->number_show);

            $data = \ComplaintSeller::getComplaintSeller($condition, $page, $this->number_show);

            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = 'OK';
            $ajax->condition = $data['condition'];
            $ajax->current_page = $data['current_page'];
            $ajax->per_page = $data['per_page'];
            $ajax->page = $data['page'];
            $ajax->total_page = $data['total_page'];
            $ajax->total_record = $data['total_record'];
            $ajax->total_by_status = $data['total_by_status'];
            $ajax->items = (array)$data['items'];

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