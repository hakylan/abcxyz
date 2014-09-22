<?php
namespace User\Controller;

use SeuDo\Main;

class OrderDeleted extends UserBase
{
    public $logger = null;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->logger = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $keyword = $this->request()->request("keyword","STRING",'');
        $order_status = $this->request()->request("status","STRING","DeletedOut");
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);
        $document = $this->document();
        $document->title = "Đơn hàng đã hủy hoặc hết hàng";
        $this->setView("Order/order_deleted");
        $url = Main::getUserRouter()->createUrl('OrderActive/load_order_active');
        $document->addJsVar('UrlLoadOrderActive',$url);
        $this->view()->assign('UrlLoadOrderActive',$url);
        $this->view()->assign('keyword',$keyword);
        $this->view()->assign('from_time',$from_time);
        $this->view()->assign('to_time',$to_time);
        $this->view()->assign('status',$order_status);
        $this->view()->assign('page',$page);
        $document->addJsVar('OrderDeletedUrl',Main::getUserRouter()->createUrl('OrderDeleted'));
        $document->addJsVar("STATUS_WAITING_FOR_DELIVERY",\Order::STATUS_WAITING_FOR_DELIVERY);
        return $this->renderComponent();
    }
}