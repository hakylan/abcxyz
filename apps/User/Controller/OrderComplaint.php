<?php
namespace User\Controller;

use SeuDo\Main;

class OrderComplaint extends UserBase
{
    public $logger = null;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->logger = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $keyword = $this->request()->request("keyword","STRING",'');
        $order_status = $this->request()->request("status","STRING","OrderActive");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $document = $this->document();
        $document->title = "Đơn hàng đang khiếu nại";
        $this->setView("Order/order_get");
        $url = Main::getUserRouter()->createUrl('OrderActive/load_order_active');
        $document->addJsVar('UrlLoadOrderActive',$url);
        $this->view()->assign('UrlLoadOrderActive',$url);
        $this->view()->assign('keyword',$keyword);
        $this->view()->assign('from_time',$from_time);
        $this->view()->assign('to_time',$to_time);
        $this->view()->assign('status',$order_status);
        $this->view()->assign('page',$page);
        $this->view()->assign('is_complaint',1);
        $document->addJsVar('OrderActiveUrl',Main::getUserRouter()->createUrl('OrderActive'));

//        $que
        return $this->renderComponent();
    }
}