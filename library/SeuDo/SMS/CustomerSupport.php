<?php

namespace SeuDo\SMS;


use Flywheel\Config\ConfigHandler;
use Flywheel\Event\Event;
use Flywheel\Factory;
use SeuDo\Logger;
use SeuDo\Queue;
use SeuDo\SMS\Transporters\ITransporter;

class CustomerSupport {
    protected $_bandname;
    protected static $_transporters = array();

    /** @var CustomerSupport */
    protected static $_instance;

    protected $_enable;

    public function __construct() {
        ConfigHandler::import('root.config');
        $config = ConfigHandler::get('sms');
        $this->_enable = (bool) $config['enable'];
        $this->_bandname = $config['brandname'];
        if (!$this->_bandname) {
            throw new \RuntimeException('SMS Customer Support missing config "brandname"');
        }
        if ($this->_enable && empty($transporter)) {
            foreach($config['transporters'] as $transporter=>$params) {
                self::addTransporter($transporter, $params);
            }
        }

    }

    public function onOrderChangeStatus(Event $event) {
        $order = $event->params['order'];
        if (!($order instanceof \Order)) {
            throw new \RuntimeException("Something went wrong, event params 'order' is not instanceof Order");
        }

        if (\Order::STATUS_WAITING_FOR_DELIVERY == $order->getStatus()) {
            //check is delivery express
            $express = \OrderService::findOneByOrderIdAndServiceCode($order->getId(), \Services::TYPE_EXPRESS_CHINA_VIETNAM);
            if ($express) {
                $message = 'www.seudo.vn Don hang CPN ' .$order->getCode() .' da ve kho '
                    .(($order->getDestinationWarehouse() == 'VNHN')? "HN" : "HCM")
                    .', ';

                //check user's financial
                $order_wait_delivery_amount = 0;
                /** @var \Order[] $orders */
                $orders = \Order::findByBuyerIdAndStatus($order->getBuyerId(), \Order::STATUS_WAITING_FOR_DELIVERY);
                if (!empty($orders)) {
                    foreach($orders as $o) {
                        $order_wait_delivery_amount += $o->requestDeliveryMoney();
                    }
                }
                $payment_amount_required = $order->getBuyer()->getAccountBalance() - $order_wait_delivery_amount;
                if ($payment_amount_required < 0) {
                    $message .= 'Quy khach vui long thanh toan toi thieu ' .number_format(abs($payment_amount_required),0,'',' ') .' VND de chung toi chuyen hang cho quy khach.';
                } else {//I love this customer.
                    $message .= 'Quy khach truy cap www.seudo.vn de tao yeu cau giao hang';
                }

                $phone = self::getCustomerPhoneNumber($order->getBuyer());
                if (!$phone) {
                    throw new \RuntimeException("Something went wrong, customer not had any phone");
                }
                $this->sendTextSms($phone, $message);
            }
        }
    }

    /**
     * @return CustomerSupport
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function addTransporter($transporter, $params) {
        if (is_string($transporter)) {
            $transporter = new $transporter($params);
        }

        self::$_transporters[] = $transporter;
    }

    public function getBrandname() {
        return $this->_bandname;
    }

    /**
     * @return ITransporter[]
     */
    public function getTransporters() {
        return self::$_transporters;
    }

    /**
     * Get Customer's phone number
     *
     * @param $customer
     * @return null|string
     */
    public static function getCustomerPhoneNumber($customer) {
        if($customer instanceof \Users){
            $mobile = $customer->getOneMobileUsing();
            return $mobile;
        }

        return null;
    }

    /**
     * Send text message
     *
     * @param $to
     * @param $body
     * @param array $options
     */
    public function sendTextSms($to, $body, $options = array()) {
        if (!$this->_enable) {
            return;
        }

        $transporters = self::getTransporters();
        foreach($transporters as $transporter) {
            try {
                $from = $this->getBrandname();
                $transporter->sendSms($from, $to, $body, $options);
                $this->log($transporter->getProviderName(), $from, $to, $body, $options);
            } catch (\Exception $e) {
                Logger::factory('system')->error($e->getMessage() ."\Traces:\n" .$e->getTraceAsString());
            }
        }
    }

    /**
     * Logging
     *
     * @param $provider
     * @param $from
     * @param $to
     * @param $body
     * @param $options
     */
    public function log($provider, $from, $to, $body, $options) {
        Logger::factory('sms')->info("Sent SMS", array(
            'provider' => $provider,
            'from' => $from,
            'to' => $to,
            'body' => $body,
            'options' => $options,
            'length' => mb_strlen($body)
        ));
    }

    /**
     * @param \UserTransaction $ut
     */
    public function sendAccountBalanceChange(\UserTransaction $ut) {
        $user = \Users::retrieveById($ut->getUserId());
        $to = $this->getCustomerPhoneNumber($user);
        if (!$to) {
            return;
        }
        $username = $user->getUsername();
        $amount = number_format(abs($ut->getAmount()), 0, '', ' ');
        $sign = ($ut->getAmount() >= 0)? 'cong' : 'tru';
        $ending_balance = number_format($ut->getEndingBalance(),0,'',' ');
        $message = "www.seudo.vn Tai khoan {$username} da duoc {$sign} {$amount} VND, So du hien tai: {$ending_balance} VND.";
        $this->sendTextSms($to, $message);
    }

    /**
     * @param $user_id
     * @param $total_order
     * @param $total_weight
     * @param $warehouses
     * @throws \RuntimeException
     */
    public function sendWaitDeliveryNotification($user_id, $total_order, $total_weight, $warehouses) {
        $user = \Users::retrieveById($user_id);
        if (!$user) {
            throw new \RuntimeException("User with id {$user_id} not found!");
        }

        if (!($to = $this->getCustomerPhoneNumber($user))) {
            return;
        }

        $mapping_warehouse = array();
        foreach($warehouses as $warehouse) {
            $mapping_warehouse[] = ($warehouse == 'VNHN')? 'HN' : 'HCM';
        }

        $warehouses = implode(', ', $mapping_warehouse);
        $message = "www.seudo.vn Quy khach co {$total_order} don hang ($total_weight kg) ve kho {$warehouses}, vui long truy cap www.seudo.vn de tao yeu cau giao hang.";
        $this->sendTextSms($to, $message);
    }
} 