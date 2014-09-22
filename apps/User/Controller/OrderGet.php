<?php
namespace User\Controller;

use SeuDo\Main;

class OrderGet extends UserBase
{
    /**
     * @var \Users
     */
    public $logger = null;

    private $number_show = 25;

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
        $document->title = "Đơn hàng kết thúc";
        $this->setView("Order/order_get");
        $url = Main::getUserRouter()->createUrl('OrderActive/load_order_active');

        $query = \Order::read();
        $query->andWhere("status='".\Order::STATUS_RECEIVED."'");
        $query->andWhere("buyer_id={$this->logger->getId()}");
        if($from_time != '' && $to_time != ''){
            $from_time = new \DateTime($from_time); //($from_time);
            $from_time = $from_time->format("Y:m:d H:i:s");
            $to_time = new \DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("created_time >= '{$from_time}' and created_time<='{$to_time}'");
        }
        if($keyword != ''){
            $query->andWhere("seller_name like '%{$keyword}%' or `code` like '%{$keyword}%'");
        }
        $query->andWhere("is_deleted=0");

        $total = $query->count('id')->execute();

        $total_page = ceil($total/$this->number_show);

        $document->addJsVar('UrlLoadOrderActive',$url);
        $this->view()->assign('UrlLoadOrderActive',$url);
        $this->view()->assign('keyword',$keyword);
        $this->view()->assign('from_time',$from_time);
        $this->view()->assign('to_time',$to_time);
        $this->view()->assign('status',$order_status);
        $this->view()->assign('total_page',$total_page);
        $this->view()->assign('page',$page);
        $this->view()->assign('is_get',1);
        $document->addJsVar('OrderDeletedUrl',Main::getUserRouter()->createUrl('OrderDeleted'));
        return $this->renderComponent();
    }
}