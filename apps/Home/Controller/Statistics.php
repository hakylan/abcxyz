<?php
namespace Home\Controller;

use Flywheel\Redis\Client;
use Flywheel\Session;
use SeuDo\Main;

class Statistics extends HomeBase
{
    public function executeDefault() {

        $this->setLayout("default");
        $query = \Order::read();
        $query->andWhere("created_time > '2014-01-01 00:00:00'");
        $order_list = \OrderPeer::getOrder($query);//::getOrder($query);
        $statistic = array();
        foreach ($order_list as $order) {

            if($order instanceof \Order){
                if(!isset($statistic[$order->buyer_id])){
                    if($order->buyer_id == 1){
                        continue;
                    }
                    $user = $order->getBuyer();
                    if(!$user instanceof \Users){
                        continue;
                    }
                    if(!\UsersPeer::isEligibleToOrder($user)){
                        continue;
                    }
                    $array = array();
                    $array['username'] = $user->getUsername();
                    $array['email'] = $user->getEmail();
                    $mobiles = $user->getMobiles();
                    if(!empty($mobiles)){
                        foreach ($mobiles as $mobile) {
                            if($mobile instanceof \UserMobiles){
                                $array['mobile'][] = $mobile->getMobile();
                            }
                        }
                    }
                    $array['address'] = $user->getDetailAddress() . " - {$user->getQhAddress()} {$user->getTtAddress()}";
                    $array['yahoo'] = \UserProfiles::getOneUserProfile($user,"yahoo");
                    $array['skype'] = \UserProfiles::getOneUserProfile($user,"skype");
                    $array['balance'] = \Common::numberFormat($user->getAccountBalance());
                    $array['seller_id'][$order->getSellerName()] = $order->getSellerName();

                    $statistic[$order->buyer_id] = $array;

//                    $statistic[$order->buyer_id]
                }else{
                    $statistic[$order->buyer_id]['seller_id'][$order->getSellerName()] = $order->getSellerName();
                }

            }
        }

        $this->setView("Order/export_excel");
        $this->view()->assign("order_list",$order_list);

        print_r('<pre>');
        print_r($statistic);
        print_r('</pre>');
        exit();
        return $this->renderComponent();

        print_r('<pre>');
        print_r($order_list);
        print_r('</pre>');
        exit();
    }
}