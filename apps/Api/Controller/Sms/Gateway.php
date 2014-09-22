<?php
namespace Api\Controller\Sms;

use Flywheel\Event\Dispatcher;
use SeuDo\Event\User;
use SeuDo\Logger;

class Gateway extends Base {
    public function postVerifyMobile() {

        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SMA\s([a-zA-Z0-9_-]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $username = $matches[1];
            if (!($user = \Users::retrieveByUsername($username))) {
                return $this->sendResponse(200, array(
                    'reply' => 'Ten dang nhap ' .$username .' khong ton tai',
                    'isSuccess' => false
                ));
            }

            //convert phone number and add it
            $this->_phone = \UserMobiles::validPhoneNo($this->_phone);
            $user->beginTransaction();
            try {
                $om = \UserMobiles::addPhone($user, $this->_phone, \UserMobiles::COMING_BY_SMS_GATEWAY);
                if ($om) {
                    //return verify SMS fee
                    \UserMobiles::refundVerifySmsFee($om);
                    $logger->info("Add user mobile number. Username: {$user->getUsername()}, mobile: {$this->_phone}");
                    return $this->sendResponse(200, array(
                        'reply' => 'SDT ' .$this->_phone .' da duoc xac minh voi tai khoan ' .$user->getUsername(),
                        'isSuccess' => true
                    ));
                }
            } catch (\Exception $e) {
                $user->rollBack();
                throw $e;
            }

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }

    public function postDepositNotice() {
        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SNT\s(TCB|VCB|VIETIN)\s([-0-9.,]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check verified phone number
            if (!($user = \UserMobiles::getUserByPhone($this->_phone))) {
                return $this->sendResponse(200, array(
                    'reply' => 'SDT ' .$this->_phone .' chua duoc xac minh. Vui long soan tin SMA <TEN DANG NHAP> gui 6589',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $bank_code = strtoupper($matches[1]);
            $amount = $matches[2];
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '', $amount);

            if ($amount < 0) {
                return $this->sendResponse(200, array(
                    'reply' => 'So tien ' .$amount .' khong hop le',
                    'isSuccess' => false
                ));
            }

            if (!($bank_code == 'TCB' || $bank_code == 'VCB' || $bank_code == 'VIETIN')) {
                return $this->sendResponse(200, array(
                    'reply' => "Ma ngan hang {$bank_code} khong khop",
                    'isSuccess' => false
                ));
            }

            /**
             * @FIXME call API create deposit voucher
             */

            $res = \SeuDo\Accountant\Util::createDeposit($user, $amount, 'BANK_TRANSFER',
                $bank_code,
                "Tạo form nạp tiền qua nhắn tin. SDT " .$this->_phone);

            $response = $res->getResponse();

            if ($res->getHttpCode() == 200) {
                return $this->sendResponse(200, array(
                    'reply' => 'Thong bao nap tien ' .$response['uid'] .' cua ban da duoc gui toi SEUDO.VN chung toi se kiem tra thong tin chuyen khoan va cong tien vao tai khoan cua ban',
                    'isSuccess' => true
                ));
            }

            return $this->sendResponse(200, array(
                'reply' => 'Co loi xay ra, vui long thu lai hoac lien he voi CSKH cua SeuDo de duoc tro giup.',
                'isSuccess' => false
            ));

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }

    public function postWithdrawalConfirm() {
        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SRT\s([a-zA-Z0-9-_]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check verified phone number
            if (!($user = \UserMobiles::getUserByPhone($this->_phone))) {
                return $this->sendResponse(200, array(
                    'reply' => 'SDT ' .$this->_phone .' chua duoc xac minh. Vui long soan tin theo cu phap SMA <TEN DANG NHAP> gui 6589',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $trans_code = $matches[1];

            //check existed transaction

            /**
             * @FIXME
             */

            return $this->sendResponse(200, array(
                'reply' => 'Yeu cau rut tien ' /* add withdrawal voucher code here*/ .' da duoc xac nhan nhan tu khach hang ' .$user->getUsername(),
                'isSuccess' => true
            ));

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }

    public function postTransactionConfirm() {
        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SGD\s([a-zA-Z0-9-_]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check verified phone number
            if (!($user = \UserMobiles::getUserByPhone($this->_phone))) {
                return $this->sendResponse(200, array(
                    'reply' => 'SDT ' .$this->_phone .' chua duoc xac minh. Vui long soan tin theo cu phap SMA <TEN DANG NHAP> gui 6589',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $trans_code = $matches[1];

            //check existed transaction

            /**
             * @FIXME
             */

            return $this->sendResponse(200, array(
                'reply' => 'Giao dich ' /* add transaction code code here*/ .' da duoc xac nhan tu phia khach hang ' .$user->getUsername(),
                'isSuccess' => true
            ));

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }

    public function postOrderConfirm() {
        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SOK\s([a-zA-Z0-9-_]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check verified phone number
            if (!($user = \UserMobiles::getUserByPhone($this->_phone))) {
                return $this->sendResponse(200, array(
                    'reply' => 'SDT ' .$this->_phone .' chua duoc xac minh. Vui long soan tin theo cu phap SMA <TEN DANG NHAP> gui 6589',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $order_code = $matches[1];

            //check existed order

            /**
             * @FIXME
             */

            return $this->sendResponse(200, array(
                'reply' => 'Don hang ' /* add order code here*/ .' da duoc xac nhan boi khach hang ' .$user->getUsername(),
                'isSuccess' => true
            ));

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }

    public function postReceivedOrder() {
        $logger = Logger::factory('sms');
        try {
            $this->_verifyRequest();

            $logger->info("Gateway", array(
                'message' => $this->_message,
                'short_code' => $this->_shortCode,
                'phone' => $this->_phone,
                'time' => $this->_time
            ));

            //check SMS syntax
            if (!preg_match("/^SNH\s([a-zA-Z0-9-_]+)$/i", $this->_message, $matches)) {
                return $this->sendResponse(200, array(
                    'reply' => 'Tin nhan sai cu phap. Vui long thu lai!',
                    'isSuccess' => false
                ));
            }

            //check verified phone number
            if (!($user = \UserMobiles::getUserByPhone($this->_phone))) {
                return $this->sendResponse(200, array(
                    'reply' => 'SDT ' .$this->_phone .' chua duoc xac minh. Vui long soan tin theo cu phap SMA <TEN DANG NHAP> gui 6589',
                    'isSuccess' => false
                ));
            }

            //check existed user
            $order_code = $matches[1];

            //check existed order

            /**
             * @FIXME
             */

            return $this->sendResponse(200, array(
                'reply' => 'Don hang ' /* add order code here*/ .' da duoc xac nhan gui toi khach hang ' .$user->getUsername(),
                'isSuccess' => true
            ));

        } catch (\InvalidArgumentException $iae) {
            return $this->sendResponse(200, array(
                'error' => $iae->getMessage()
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
            return $this->sendResponse(500, array(
                'error' => 'Internal server error'
            ));
        }
    }
} 