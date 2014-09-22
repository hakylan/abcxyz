<?php
namespace User\Controller;

use Flywheel\Db\Type\DateTime;
use SeuDo\Main;

class UserTransaction extends UserBase
{
    /**
     * @var \Users
     */
    public $user = null;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault(){

        $user_id = $this->user->getId();
        $keyword = $this->request()->request("keyword","STRING",'');
        $transaction_type = $this->request()->request("transaction_type","STRING",'');
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);


        $num_show = 30;
        $offset = ($page - 1) * $num_show;
        $limit = $page * $num_show;

        $query = \UserTransaction::read();
        $query->andWhere("user_id={$user_id}");

        if($from_time != '' && $to_time != ''){
            $from_time = new DateTime($from_time);

            $from_time = $from_time->format("Y:m:d H:i:s");

            $to_time = new DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("closed_time >= '{$from_time}' and closed_time<='{$to_time}'");
        }

        if($keyword != ''){
            $query->andWhere("transaction_code like '%{$keyword}%' or `object_id` like '%{$keyword}%'");
        }

        if($transaction_type != '' && $transaction_type != "0"){
            $query->andWhere("transaction_type='{$transaction_type}'");
        }

        $total = $query->count('id')->execute();
        $total_page = ceil($total/$num_show);

        $this->view()->assign('total_page',$total_page);
        $this->view()->assign('page',$page);

        $document = $this->document();
        $document->title = "Lịch sử giao dịch";
        $this->setView("Transaction/default");
        $this->setLayout("transaction");
        $url = Main::getUserRouter()->createUrl('UserTransaction/load_transaction');
        $total_amount_waiting_delivery = \OrderPeer::getTotalAmountWaitingDelivery($this->user);

        $total_amount_before_delivery = \OrderPeer::getTotalAmountNotAbout($this->user);

        $missing_waiting_delivery = ($this->user->getAccountBalance() - $total_amount_waiting_delivery) > 0 ? 0 : $total_amount_waiting_delivery - $this->user->getAccountBalance();
        $document->addJsVar('UrlLoadTransaction',$url);
        $this->view()->assign('UrlLoadTransaction',$url);
        $this->view()->assign('missing_waiting_delivery',$missing_waiting_delivery);
        $this->view()->assign('total_amount_before_delivery',$total_amount_before_delivery);
        $this->view()->assign('keyword',$keyword);
        $this->view()->assign('from_time',$from_time);
        $this->view()->assign('to_time',$to_time);
        $this->view()->assign('transaction_type',$transaction_type);
        $this->view()->assign('page',$page);
        $this->view()->assign('user',$this->user);
        $document->addJsVar('UserTransactionUrl',Main::getUserRouter()->createUrl('UserTransaction/default'));
        $document->addJsVar('OrderActiveCount',Main::getUserRouter()->createUrl('OrderActive/count_order'));

//        $que
        return $this->renderComponent();
    }

    public function executeLoadTransaction(){

        $this->validAjaxRequest();
        $user_id = $this->user->getId();
        $keyword = $this->request()->request("keyword","STRING",'');
        $transaction_type = $this->request()->request("transaction_type","STRING",'');
        $from_time = $this->request()->request("from_time","STRING",'');
        $to_time = $this->request()->request("to_time","STRING",'');
        $page = $this->request()->request('page',"INT",1);


        $num_show = 30;
        $offset = ($page - 1) * $num_show;
        $limit = $page * $num_show;

        $query = \UserTransaction::read();
        $query->andWhere("user_id={$user_id}");

        if($from_time != '' && $to_time != ''){
            $from_time = new DateTime($from_time);

            $from_time = $from_time->format("Y:m:d H:i:s");

            $to_time = new DateTime($to_time);
            $to_time = $to_time->format("Y:m:d 23:59:59");
            $query->andWhere("closed_time >= '{$from_time}' and closed_time<='{$to_time}'");
        }

        if($keyword != ''){
            $query->andWhere("transaction_code like '%{$keyword}%' or `object_id` like '%{$keyword}%'");
        }

        if($transaction_type != '' && $transaction_type != "0"){
            $query->andWhere("transaction_type='{$transaction_type}'");
        }

        $query_count = clone $query;

        $query->setFirstResult($offset)
            ->setMaxResults($num_show)
            ->orderBy("closed_time",'DESC');

        $transaction_list = \UserTransaction::getUserTransaction($query);

        $this->view()->assign('transaction_list',$transaction_list);
        $this->setView("Transaction/transaction_one");


        $total = $query_count->count('id')->execute();
        $total_page = ceil($total/$num_show);

        $this->view()->assign('total_page',$total_page);
        $this->view()->assign('page',$page);
        $this->view()->assign('user',$this->user);

        $response = array(
            "total" => intval($total),
            "html_result" =>$this->renderPartial()
        );

        return $this->renderText(json_encode($response));
    }

    public function executeFilterOrder(){


    }

    public function executeTransactionForm(){
        $this->setView('Transaction/tran_charge_form');
        $this->setLayout("transaction");
        return $this->renderComponent();
    }

    public function executeModalBox(){
        $order_code = $this->request()->get("order_code","STRING",'');
        $app = $this->request()->get("app","STRING",'user');
        if($order_code == ''){
            $response = array(
                "type" => \AjaxResponse::ERROR,
                "message" => "Không tồn tại đơn hàng"

            );
            return $this->renderText(json_encode($response));
        }
        $this->setView("Transaction/modal_box");
        $transaction = \UserTransaction::retrieveByObjectId($order_code);
        $user_id = !empty($transaction) && $transaction instanceof \UserTransaction ? $transaction->getUserId() : 0;

        $user = \Users::retrieveById($user_id);

//        print_r('<pre>');
//        print_r($user);
//        print_r('</pre>');
//        exit();

        $query = \UserTransaction::read()->andWhere("object_id='{$order_code}'");
        $query->orderBy("id",'ASC');
        $query->orderBy("closed_time",'ASC');
        $transaction_list = \UserTransaction::getUserTransaction($query);
        $this->assign('transaction_list',$transaction_list);
        $this->assign('order_code',$order_code);
        $this->assign('app',$app);
        $this->assign('user',$user);
        $response = array(
            "type" => \AjaxResponse::SUCCESS,
            "message" => "Thành công",
            "html" => $this->renderPartial()
        );

        return $this->renderText(json_encode($response));
    }

}