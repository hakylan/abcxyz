<?php
/**
 * Created by PhpStorm.
 * User: minhquyen
 * Date: 2/10/14
 * Time: 12:53 PM
 */
namespace User\Controller;
class OrderConfirm extends UserBase
{

    protected $user;

    /**
     * Require login
     */
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault()
    {
        $document = $this->document();
        $document->addJsVar("ConfirmUrl",$this->createUrl("OrderDetail/BuyerConfirm"));
        $customer_confirm = \Order::CUSTOMER_CONFIRM_WAIT;
        $customer_confirmed = \Order::CUSTOMER_CONFIRM_CONFIRMED;
        $user_id = $this->user->getId();

        $query = \Order::read();
        $query->andWhere("buyer_id={$user_id}");
        $query->andWhere("customer_confirm='{$customer_confirm}'");
        $query->orderBy("confirm_created_time","DESC");

        $count_wait = $query;

        $order_list_wait = \OrderPeer::getOrder($query);

        $total_wait = $count_wait->count('id')->execute();

        $query = \Order::read();
        $query->andWhere("buyer_id={$user_id}");
        $query->andWhere("customer_confirm='{$customer_confirmed}'");
        $is_delete = 1;

        $query->orderBy("confirm_created_time","DESC");

        $count_confirmed = $query;

        $order_list_confirmed = \OrderPeer::getOrder($query);

        $total_confirmed = $count_confirmed->count('id')->execute();
        $this->setView("OrderConfirm/default");

        $this->view()->assign('order_list_wait',$order_list_wait);
        $this->view()->assign('order_list_confirmed',$order_list_confirmed);
        $this->view()->assign('total_confirmed',$total_confirmed);
        $this->view()->assign('total_wait',$total_wait);

        return $this->renderComponent();
    }

}