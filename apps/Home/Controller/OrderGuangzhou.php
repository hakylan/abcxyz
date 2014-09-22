<?php
/**
 * Created by PhpStorm.
 * User: vanhs
 * Date: 16/05/2014
 * Time: 14h45
 * Step: Danh sách đơn hàng ở quảng châu
 */
namespace Home\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;
use Flywheel\Session\Session;
use SeuDo\Event\Order;
use SeuDo\Main;
use SeuDo\Logger;

class OrderGuangzhou extends HomeBase {

    public function beforeExecute() {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault() {
        $this->user = \BaseAuth::getInstance()->getUser();
        $this->document()->title = 'Danh sách đơn hàng - khách hàng ở quảng châu';
        $html = '';

        /** @var \Order[] $orders */
//        $orders = \Order::select()->execute();

        $orders = \Order::select()->where("current_warehouse = 'CNGZ' AND warehouse_status = 'IN'")->orderBy("current_warehouse_time", "ASC")->execute();
        $html .= '<div class="container">';
        $html .= '<table class="table table-bordered">';
        $html .= '<thead><tr><th align="center">Đơn hàng</th><th align="center">Khách hàng</th><th align="center">Ngày đặt hàng</th><th align="center">Email khách hàng</th><th align="center">Kho</th><th align="center">Tình trạng kho</th></tr></thead>';
        $html .= '<tbody>';
        foreach($orders as $order) {
            //Người bán
            $buyer = \Users::retrieveById($order->getBuyerId());
            $info_buyer = $buyer->getAttributes('id,username,code,last_name,first_name, email, phone, section, status');

            //Nhân viên nhận hàng
            $teller = \Users::retrieveById($order->getTellersId());
            if ($teller) {
                $data['teller'] = $teller->getAttributes('id, username, code, last_name, first_name, section, status');
                $data['teller']['avatar'] = \Users::getAvatar32x($teller);
                $data['teller']['detail_link'] = $this->createUrl('user/detail', array('id' => $teller->getId()));
            }

            if($info_buyer['section'] == \Users::SECTION_CUSTOMER && $info_buyer['status'] == \Users::STATUS_ACTIVE){
                $date = new \DateTime($order->getDepositTime());
                $deposit_time = $date->format("H:i d/m");
                $date = new \DateTime($order->getCurrentWarehouseTime());
                $current_warehouse_time = $date->format("H:i d/m");

                $html .= '<tr>';
                $html .= '<td>' . $order->getCode() . '</td>';
                $html .= '<td>' . $info_buyer['username'] . ' ( ' . $info_buyer['code'] . ' )</td>';
                $html .= '<td>' . $deposit_time . '</td>';
                $html .= '<td>' . $info_buyer['email'] . '</td>';

                $html .= '<td align="center">' . $order->getCurrentWarehouse() . ' ( ' . $current_warehouse_time . ' )</td>';
                $html .= '<td align="center">' . $order->getWarehouseStatusTitle() . '</td>';
                $html .= '</tr>';
            }

        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;

    }

}