<?php
/**
 * Created by PhpStorm.
 * User: binhnt
 * Date: 2/12/14
 * Time: 9:14 AM
 * Step: Ket đơn hàng
 */
namespace Home\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;
use Flywheel\Session\Session;
use SeuDo\Event\Order;
use SeuDo\Main;
use SeuDo\Logger;

class OrderDeposit extends HomeBase {

    /**
     * @var \Users
     */
    protected $_user;
    const DEPOSIT_ERR_LEVEL_1 = 'Không tìm thấy order';
    const DEPOSIT_ERR_LEVEL_2 = 'Đơn hàng không thuộc trạng thái chờ thanh toán';
    const DEPOSIT_ERR_LEVEL_3 = 'Tài khoản không đủ thanh toán';
    const DEPOSIT_ERR_LEVEL_4 = 'Bạn không đủ điều kiện đặt hàng';
    const DEPOSIT_ERR_LEVEL_5 = 'Xác thực mật khẩu không đúng';
    const DEPOSIT_ERR_LEVEL_6 = 'Chưa xác thực mật khẩu';

    public function beforeExecute() {

        $auth = \HomeAuth::getInstance();
        if (!$auth->isAuthenticated() || !($this->_user = $auth->getUser())) {
            $this->redirect($this->createUrl('login', array(
                'url' => base64_encode($this->request()->getUri())
            )));
        }

        if(!\UsersPeer::isEligibleToOrder($auth->getUser())) {
            $this->redirect(
                Main::getHomeRouter()->createUrl("Home/ask_confirm", array('url' => $this->request()->getUri()))
            );
        }
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('afterOrderDeposit', array(new \HomeEvent(), 'afterOrderDeposit'));
    }

    /**
     *  Charge fee for order
     */
    public function executeDeposit() {
        // Validate ajax request
        $this->validAjaxRequest();

        $order_id = $this->request()->post('id', 'INT', 0);

        //check token
        if (!$this->_checkDepositToken()) {
            return $this->renderText(json_encode(array(
                'state' => false,
                'msg'  => self::DEPOSIT_ERR_LEVEL_6
            )));
        }

        $order = \Order::retrieveById($order_id);

        //check order
        if (!($order instanceof \Order)) {
            return $this->renderText(json_encode(array(
                'status' => false,
                'msg'   => 'Không tìm thấy đơn hàng .Vui lòng thử lại!'
            )));
        }

        if($order->getOrderAmount() == 0) {
            return $this->renderText(
                json_encode(
                    array('state' => false, 'msg' => 'Tiền đơn hàng = 0 <sup>đ</sup>. Giao dịch không được thực hiện.')
                )
            );
        }

        if($order->getStatus() != \Order::STATUS_INIT) {
            return $this->renderText(json_encode(array(
                'state' => false,
                'msg'   => self::DEPOSIT_ERR_LEVEL_2 . '. Mã đơn hàng: ' . $order->getStatus()
            )));
        }

        // Check can pay with order deposit
        $deposit = \OrderPeer::calculateDepositAmount($order->getOrderAmount());
        if( $deposit > $this->_user->getAccountBalance() || $this->_user->getAccountBalance() == 0) {
            return $this->renderText(json_encode(array(
                'state' => false,
                'msg'   => self::DEPOSIT_ERR_LEVEL_3,
            )));
        }
        // End validate ------------

        try {
            if(\OrderPeer::depositOrder($order, $this->_user, $deposit)) {
                Logger::factory("deposit_order")->info(
                    "User {$this->_user->getUsername()} - {$this->_user->getFullName()} Deposit Order Success",
                    array(
                        "username" => $this->_user->getUsername(),
                        "order" => $order
                    )
                );
                return $this->renderText(json_encode(array(
                    'state' => true,
                )));
            }
        } catch (\Exception $e) {
            Logger::factory("deposit_order")->addError(
                "Can't Deposit Order ".$e->getMessage(),
                array(
                    "username" => $this->_user->getUsername(),
                    "order" => $order
                )
            );
            return $this->renderText(json_encode(array(
                'state' => false,
                'msg' => 'Giao dịch thực hiện lỗi! Xin thử lại.'
            )));
        }

        return $this->renderText(json_encode(array(
            'state' => false,
            'msg' => 'Giao dịch thực hiện lỗi! Xin thử lại.'
        )));
    }

    public function executeDefault() {

        Session::getInstance()->remove("order_deposit_id");

        $this->setLayout('checkout');

        $this->setView('OrderDeposit/default');

        if(!($this->_user instanceof \Users)) {
            $this->_user = new \Users();
        }

        if (!$this->_user->getAccountNo()) {
            $this->_user = \SeuDo\Accountant\Util::createUserAccount($this->_user);
        }

        \UsersPeer::syncAccountBalance($this->_user);

        if ($hash = $this->get('hash')) {
            $cid = Session::getInstance()->get($hash);
        } else {
            $cid = json_decode(base64_decode($this->request()->get('cid')));
        }


        $orders = null;
        if(is_array($cid) & !empty($cid)) {
            $cid = implode(',', $cid);
            // Load current list order with status 'INIT'
            $condition =
                \Order::read()->select('*')->andWhere("`buyer_id` = {$this->_user->getId()} AND `status`='" . \Order::STATUS_INIT."' AND `id` IN ({$cid})");
            $orders = \OrderPeer::getOrder($condition);
        }

        $this->document()->title = "Đặt cọc đơn hàng";
        // Add js process file
        $this->document()->addJs($this->document()->getPublicPath() . 'assets/js/process/order_deposit.js');

        $this->view()->assign(array(
            'orders' => $orders
        ));

        return $this->renderComponent();
    }

    /**
     * Synchronize account balance account by ajax
     * @return string
     */
    public function executeSynBalance() {

        // Validate ajax
        $this->validAjaxRequest();

        if ($this->_user && !$this->_user->getAccountNo()) {
            $this->_user = \SeuDo\Accountant\Util::createUserAccount($this->_user);
        }

        if (!$this->_user->getAccountNo()) {
            $this->_user = \SeuDo\Accountant\Util::createUserAccount($this->_user);
        }

        $result = array();
        // Synchronize account balance
        if(!\UsersPeer::syncAccountBalance($this->_user)) {
            $result['status'] = false;
            $result['msg'] = "Đồng bộ tài khoản không thành công!";
        }
        $result['balance'] = $this->_user->getAccountBalance();

        return $this->renderText(json_encode($result));
    }

    public function executeAuthDeposit() {

        // Validate ajax
        $this->validAjaxRequest();

        if(!($this->_user instanceof \Users)) {
            $this->_user = new \Users();
        }
        $userValidate = \UsersPeer::isEligibleToOrder($this->_user);
        if(!$userValidate) {
            return $this->renderText(json_encode(array(
                'state' => $userValidate,
                'msg' => self::DEPOSIT_ERR_LEVEL_4
            )));
        }

        $pwdConfirm = $this->request()->post('confirm');

        if ($this->_user->getPassword() == \Users::hashPassword($pwdConfirm, $this->_user->getPassword())) {
            $this->_createDepositToken();
            return $this->renderText(json_encode(array(
                'state' => true
            )));
        }

        return $this->renderText(json_encode(array(
            'state' => false,
            'msg'  => self::DEPOSIT_ERR_LEVEL_5
        )));
    }

    /**
     * Ending deposit order
     */
    public function executeFinish() {
        $this->setLayout('checkout');
        $this->setView('OrderDeposit/finish');

        return $this->renderComponent();
    }


    private function _createDepositToken() {
        $session = Session::getInstance();
        $token = $session->createToken();
        $session->set('token_deposit', $token);

        // Set cookie
        $cookie = Factory::getCookie()->write('__crsfDeposit', $token);
    }

    private function _checkDepositToken() {
        return (Session::getInstance()->get('token_deposit') == Factory::getCookie()->read('__crsfDeposit'));
    }
}