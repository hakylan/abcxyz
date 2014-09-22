<?php
namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;

class FreightBill extends BackendBase
{
    public $maxOrderPerPage = 50;

    public function executeDefault()
    {
        $this->setView('Order/freight_bill');

        $members = \UserRoles::getUserByRoles(\UserRoles::GET_ID_ROLE_PURCHASE_ORDER, true);

        $this->view()->assign(array(
            'filter_mode' => $this->get('filter_mode', 'STRING', 'empty_freight_bill'),
            'ordering' => $this->get('ordering', 'STRING', 'bought_time'),
            'page' => $this->get('page', 'INT', 1),
            'keyword' => $this->get('keyword'),
            'freight_bill' => $this->get('freight_bill'),
            'account_purchase_origin' => $this->get('account_purchase_origin'),
            'customer_code' => $this->get('customer_code'),
            'status' => $this->get('status'),
            'homeland' => $this->get('homeland'),
            'members' => $members
        ));

        return $this->renderComponent();
    }

    public function executeGetOrders()
    {
        $ajax = new \AjaxResponse();
        if (!$this->isAllowed(PERMISSION_ORDER_ADD_FREIGHT_BILL)
            && !$this->isAllowed(PERMISSION_ORDER_EDIT_FREIGHT_BILL)
        ) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền truy cập khu vực này");
            return $this->renderText($ajax->toString());
        }

        $not_found = false;

        /**
         * filter mode
         * 1: not have freight_bill
         * 2: find by external invoice
         */
        $mode = $this->get('filter_mode', 'STRING', 'empty_freight_bill');
        $page = $this->get('page', 'INT', 1);
        $keyword = $this->get('keyword');
        $freight_bill = $this->get('freight_bill');
        $customer_code = $this->get('customer_code');
        $homeland = $this->get('homeland');
        $status = $this->get('status');
        $tellers_id = $this->get('tellers_id');
        $ordering = $this->get('ordering', 'STRING', 'bought_time');
        $group_by = $this->get('group-by', 'STRING', 'seller_homeland');

        $query = \Order::select()
            ->where('`is_deleted` = 0');

        if ('empty_freight_bill' == $mode && $freight_bill == '') {
            $query->andWhere('`has_freight_bill` = 0');
        } elseif ($freight_bill != '') {
            $query->andWhere('`has_freight_bill` = 1');
        }

        $exceptStatus = array(
            \Order::STATUS_INIT,
            \Order::STATUS_DEPOSITED,
            \Order::STATUS_NEGOTIATING,
            \Order::STATUS_NEGOTIATED,
            \Order::STATUS_CANCELLED,
            \Order::STATUS_OUT_OF_STOCK,
            \Order::STATUS_DELIVERING,
            \Order::STATUS_RECEIVED
        );
        $query->andWhere('`status` NOT IN ("' . implode($exceptStatus, '","') . '")');

        if ($keyword) { //searching by order code or external freight_bill
            $query->andWhere('`code` LIKE :keyword')
                ->orWhere('`invoice` LIKE :keyword')
                ->orWhere('`seller_name` LIKE :keyword')
                ->orWhere('`seller_aliwang` LIKE :keyword')
                ->orWhere('`account_purchase_origin` LIKE :keyword')
                ->orWhere('`name_recipient_origin` LIKE :keyword')
                ->setParameter(':keyword', "%{$keyword}%", \PDO::PARAM_STR);
        }


        if ($tellers_id) {
            $query->andWhere('`tellers_id` = :tellers_id')
                ->setParameter(':tellers_id', $tellers_id, \PDO::PARAM_STR);
        }

        if ($freight_bill) {
            //select order_id from packages
            $packages = \Packages::searchByFreightBill($freight_bill);
            if ($packages) {
                $order_ids = array();
                foreach ($packages as $package) {
                    $order_ids[] = $package->getOrderId();
                }
                $query->andWhere('`id` IN (' . implode(',', $order_ids) . ')');
            } else {
                $not_found = true;
            }

        }

        if ($customer_code) { //searching by customer
            $buyer_ids = array();
            if (is_int($customer_code)) {
                $user = \Users::retrieveById($customer_code);
                $buyer_ids[] = $user->getId();
            } else {
                /** @var \Users[] $users */
                $users = \UsersPeer::searchByCodeOrUsername($customer_code, $customer_code);
                if (!empty ($users)) {
                    for ($i = 0, $size = sizeof($users); $i < $size; ++$i) {
                        $buyer_ids[] = $users[$i]->getId();
                    }
                }
            }

            if (!empty($buyer_ids)) {
                $query->andWhere('`buyer_id` IN (' . implode(',', $buyer_ids) . ')');
            } else {
                $not_found = true;
            }
        }

        if ($homeland) {
            $query->andWhere('`seller_homeland` = :homeland')
                ->setParameter(':homeland', $homeland, \PDO::PARAM_STR);
        }

        if ($status) {
            //set again ordering for null static_time
            if ($status == \Order::STATUS_SELLER_DELIVERY) {
                $ordering = 'seller_delivered_time';
            }
            if ($status == \Order::STATUS_DELIVERING) {
                $ordering = 'delivered_time';
            }
            //filter by status
            $query->andWhere('`status` = :status')
                ->setParameter(':status', $status, \PDO::PARAM_STR);
        }

        $totalQ = clone $query;

        //paging
        $query->setMaxResults($this->maxOrderPerPage)
            ->setFirstResult(($page - 1) * $this->maxOrderPerPage);

        if ('empty_freight_bill' == $mode) {
            $query->orderBy($ordering);
        } else {
            $query->orderBy($ordering, 'DESC');
        }


        if (!$not_found) {
            /** @var \Order[] $orders */
            $orders = $query->execute();
            $total = $totalQ->count('id')->execute();
        } else {
            $total = 0;
            $order = array();
        }

        $result = array();

        //manipulating response data
        if (isset($orders)) {
            foreach ($orders as $order) {
                if($order && $order instanceof \Order){
                    $data = $order->getAttributes(array(
                        'id','code','status',
                        'seller_name','seller_aliwang','seller_homeland','seller_info',
                        'invoice','freight_bill',
                        'current_warehouse', 'next_warehouse',
                        'transport_status',
                        'warning_score',
                        'direct_fill_amount_cny',
                        'total_amount',
                        'account_purchase_origin','name_recipient_origin'
                    ));

                    $data['bought_time'] = $order->getBoughtTime()->toString();
                    $data['deposit_time'] = $order->getDepositTime()->toString();

                    //customer
                    $customer = \Users::retrieveById($order->getBuyerId());
                    if ($customer) {
                        $data['customer'] = $customer->getAttributes('id, code, username, last_name, first_name, email');
                        $data['customer']['avatar'] = \Users::getAvatar32x($customer);
                        $data['customer']['detail_link'] = $this->createUrl('user/detail', array('id' => $customer->getId()));
                    }

                    //teller
                    $teller = \Users::retrieveById($order->getTellersId());
                    if ($teller) {
                        $data['teller'] = $teller->getAttributes('id, code, username, last_name, first_name, email');
                        $data['teller']['avatar'] = \Users::getAvatar32x($teller);
                        $data['teller']['detail_link'] = $this->createUrl('user/detail', array('id' => $teller->getId()));
                    }

                    //payment
                    $thePayment = \Users::retrieveById($order->getPaidStaffId());
                    if ($thePayment) {
                        $data['payment'] = $thePayment->getAttributes('id, code, username, last_name, first_name, email');
                        $data['payment']['avatar'] = \Users::getAvatar32x($thePayment);
                        $data['payment']['detail_link'] = $this->createUrl('user/detail', array('id' => $thePayment->getId()));
                    }

                    //detail link
                    $data['detail_link'] = $this->createUrl('order/detail', array('id' => $order->getId()));

                    //freight_bill
                    $freight_bills = array();
                    if ($order->getHasFreightBill()) {
                        $fbs = \Packages::retrieveByOrderId($order->getId());
                        foreach ($fbs as $bill) {
                            $freight_bills[$bill->getFreightBill()] = $bill->toArray();
                        }
                    }

                    if (empty($freight_bills)) {
                        $freight_bills = new \stdClass();
                    }
                    $data['freight_bill'] = $freight_bills;
                    $data['eligible_out_of_stock'] = false;

                    //Eligible to transit out of stock
                    if ($this->isAllowed(PERMISSION_ORDER_TRANSITION_OUT_OF_STOCK)
                        && ($order->getStatus() == \Order::STATUS_BOUGHT
                            || $order->getStatus() == \Order::STATUS_SELLER_DELIVERY)) {
                        $data['eligible_out_of_stock'] = true;
                    }

                    $result[$order->getId()] = $data;
                }
            }
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = 'OK';
        $ajax->total = $total;
        $ajax->orders = $result;
        $ajax->page = $page;
        return $this->renderText($ajax->toString());
    }

    public function executeSaveBill()
    {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();
        $order_id = $this->post('order_id', 'INT', 0);
        $bill = trim($this->post('bill'));

        if (!$order_id || !$bill || !($order = \Order::retrieveById($order_id)) || preg_match('/\s/', $bill)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t('Không tìm thấy đơn hàng, mã vận đơn trống hoặc không đúng định dạng');
            return $this->renderText($ajax->toString());
        }

        if (!($package = \Packages::retrieveByFreightBillAndOrderId($bill, $order))) {

            $order->beginTransaction();

            try {
                $package = new \Packages();
                $package->setFreightBill($bill);
                $package->setOrderId($order->getId());
                $package->setCreatedBy(\BackendAuth::getInstance()->getUserId());
                $package->setDistributionWarehouse($order->getDestinationWarehouse());
                $package->setSellerDeliveredTime(date('Y-m-d H:i:s', time()));
                $package->setStatus(\Packages::STATUS_SELLER_DELIVERY);
                if (!$package->save()) {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = self::t('Lỗi kỹ thuật, không lưu được mã vận đơn');
                    return $this->renderText($ajax->toString());
                } else {
                    if (!$order->getHasFreightBill()) {
                        if ($order->isBeforeStatus(\Order::STATUS_SELLER_DELIVERY)) {
                            $order->setStatus(\Order::STATUS_SELLER_DELIVERY);
                            $this->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new \BackendEvent($this, array(
                                'order' => $order
                            )));
                        }
                        $order->setHasFreightBill(1);
                        $order->save(false); //quick save
                    }
                }

                $this->dispatch('onAddFreightBill', new \BackendEvent($this, array(
                    'package' => $package,
                    "order" => $order
                )));
                $order->commit();
            } catch (\Exception $e) {
                $order->rollBack();
                throw $e;
            }
        }

        //check duplicate bill
        $duplicated_list = \Packages::getDuplicateFreightBill($package->getFreightBill(), $order->getId());
        $duplicated_orders = array();
        if (!empty($duplicated_list)) {
            foreach ($duplicated_list as $id) {
                $_t = \Order::retrieveById($id);
                if ($_t) {
                    $duplicated_orders[] = $_t->getCode();
                }
            }
        }

        if (!empty($duplicated_orders)) {
            $ajax->type = \AjaxResponse::WARNING;
            $ajax->message = self::t('Trùng với VĐ đơn hàng %orders%', array(
                '%orders%' => implode(', ', $duplicated_orders)
            ));
        } else {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = self::t('OK');
        }
        $ajax->package = $package->toArray();

        return $this->renderText($ajax->toString());
    }

    public function executeRemoveBill()
    {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        $id = $this->post('id');
        if (!$id || !($package = \Packages::retrieveById($id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t('Vận đơn không tồn tại');
            return $this->renderText($ajax->toString());
        }

        if ($package->getStatus() == \Packages::STATUS_SELLER_DELIVERY) {

            $package->beginTransaction();
            try {
                if ($package->delete()) {
                    if ($order = \Order::retrieveById($package->getOrderId())) {
                        //check other freight bill
                        $remainFBs = \Packages::retrieveByOrderId($order->getId());
                        if (empty($remainFBs)) {
                            $order->setHasFreightBill(0);
                            $order->save(false);
                        }
                    }
                    $package->commit();
                    $this->dispatch('onRemoveFreightBill', new \BackendEvent($this, array(
                        'package' => $package,
                        "order" => $order
                    )));
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = self::t('Đã xóa Mã vận đơn');
                    return $this->renderText($ajax->toString());
                }
            } catch (\Exception $e) {
                $package->rollBack();
                throw $e;
            }
        }else{
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t('Người bán đã giao vận đơn, xóa thất bại.');
            return $this->renderText($ajax->toString());
        }
    }
}