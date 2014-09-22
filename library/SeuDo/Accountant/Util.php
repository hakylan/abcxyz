<?php
namespace SeuDo\Accountant;


use SeuDo\Event\User;
use SeuDo\Logger;

class Util {
    /**
     * return SeuDo account number
     * @return \SystemConfig
     */
    public static function getServiceAccountNo() {
        if (($sdAccountCfg = \SystemConfig::retrieveByConfigKey(\SystemConfig::SD_ACCOUNTANT_UK))) {
            return $sdAccountCfg->getConfigValue();
        }
        return null;
    }
    /**
     * @param $name
     * @param $username
     * @param string $section
     * @param string $status
     * @throws \Exception
     * @return Client
     */
    public static function apiCreateAccount($name, $username, $section = 'CUSTOMER', $status = 'ACTIVE') {
        $client = Client::getClient();
        try {
            $params = array(
                'name' => $name,
                'username' => 'seudovn-' .$username,
                'service_id' => 'seudovn',
                'section' => $section,
                'status' => $status
            );

            $client->post('account/new', $params);

            self::log(Logger::INFO, 'Create new account', array(
                'params' => $params,
                'httpCode' => $client->getHttpCode(),
                'response' => $client->getResponse()
            ));

            $client->dispatch('onAfterCallingApiCreatedAccount', new Event(null, $client));
            return $client;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * call api get account's detail
     * @param $accountNo
     * @return Client
     * @throws \Exception
     */
    public static function apiGetAccountDetail($accountNo) {
        $client = Client::getClient();
        try {
            $client->get("account/detail/{$accountNo}");

            //logging
            /*
             * Luu Hieu turn off logging from 15/06 for reduce log size
             self::log(Logger::INFO, 'Create new account', array(
                'account_no' => $accountNo,
                'httpCode' => $client->getHttpCode(),
                'response' => $client->getResponse()
            ));*/

            $client->dispatch('onAfterCallingApiGetAccountDetail', new Event(null, $client));
            return $client;
        } catch(\Exception $e) {
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }

    /**
     * get user's account detail
     * @param $user
     * @return mixed
     * @throws \RuntimeException
     * @throws \Exception
     */
    public static function getUserAccountDetail($user) {
        if (is_int($user)) {
            $user = \Users::retrieveById($user);
        }

        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new \RuntimeException("{$user} not instance of Users or user not found");
        }

        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new \RuntimeException("{$user} must be instance of Users");
        }

        try {
            $client = self::apiGetAccountDetail($user->getAccountNo());
            $response = $client->getResponse();
            if ($client->getHttpCode() != 200) {
                throw new \RuntimeException("Something went wrong when calling api: " .$client->getApiUrl() .".  Error:" .$client->getRawResponse());
            }

            return $response['account'];
        } catch (\Exception $e) {
            //logging
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }

    /**
     * create user account
     *
     * @param \Users|int $user
     * @throws \Exception
     * @return \Users
     */
    public static function createUserAccount($user) {
        if (is_int($user)) {
            $user = \Users::retrieveById($user);
        }
        if($user->isNew()) {
            throw new \RuntimeException("Not create account from isNew user !");
        }
        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new \RuntimeException("{$user} not instance of Users or user not found");
        }

        if ($user->getAccountNo()) {
            return $user;
        }

        $user->beginTransaction();

        try {
            $apiClient = self::apiCreateAccount($user->getLastName(). ' ' .$user->getFirstName(), $user->getUsername());
            $response = $apiClient->getResponse();

            if ($apiClient->getHttpCode() == 200) {//everything ok
                $user->setAccountNo($response['account']['uid']);
                if($user->save()) {
                    //dispatch event when after creating account number by Accountant Service.
                    $user->dispatch('onAfterCreateAccountNumber', new User($user, $response));
                    $user->commit();

                    //logging
                    self::log(Logger::INFO, 'Create user accountant number', array(
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'account_no' => $user->getAccountNo(),
                    ));
                } else {
                    $failures = array();
                    if (!$user->isValid()) {
                        foreach($user->getValidationFailures() as $validationFailure) {
                            $failures[$validationFailure->getColumn()] = $validationFailure->getMessage();
                        }
                    }
                    //logging
                    self::log(Logger::WARNING, 'Can not create user account number', $failures);
                }

                //logging
                return $user->getAccountNo() != null;
            } else {
                throw new \RuntimeException('ACCOUNTANT ' .$apiClient->getHttpCode() .':' .$apiClient->getRawResponse());
            }
        } catch (\Exception $e) {
            $user->rollBack();
            //logging
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }

    /**
     * calling api and make transfer
     * @param $toAcc
     * @param $fromAcc
     * @param $amount
     * @param null $detail
     * @param null $note
     * @return Client
     * @throws \RuntimeException
     * @throws \Exception
     */
    public static function apiCreateTransfer($toAcc, $fromAcc, $amount, $detail = null, $note = null) {
        if (!$amount && !is_float($amount)) {
            throw new \RuntimeException("Invalid amount value");
        }

        $params = array(
            'to_acc' => $toAcc,
            'from_acc' => $fromAcc,
            'amount' => $amount,
            'detail' => $detail,
            'note' => $note
        );

        $client = Client::getClient();
        try {
            $client->post('transfer_application/transfer', $params);
            //logging
            self::log(Logger::INFO, 'Create new transfer transaction', array(
                'params' => $params,
                'httpCode' => $client->getHttpCode(),
                'response' => $client->getResponse()
            ));

            $client->dispatch('onAfterCallingApiCreatedTransferTransaction', new Event(null, $client));
            return $client;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return user's money
     * @param $user
     * @param $amount
     * @param null $detail
     * @param null $note
     * @return mixed
     * @throws \RuntimeException
     * @throws \Exception
     */
    public static function refund($user, $amount, $detail = null, $note = null) {
        if (!$amount && !is_float($amount)) {
            throw new Exception("Invalid amount value");
        }

        if (is_int($user)) {
            $user = \Users::retrieveById($user);
        }

        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new Exception("{$user} must be instance of Users");
        }

        if (!$user->getAccountNo()) {
            throw new Exception("{$user->getUsername()}[{$user->getId()}] has not had account number");
        }

        if (!($sdAccountNo = self::getServiceAccountNo())) {
            throw new Exception("Config SeuDo account number empty");
        }

        try {
            $client = self::apiCreateTransfer($user->getAccountNo(), $sdAccountNo, $amount, $detail, $note);
            if ($client->getHttpCode() != 200) {
                throw new Exception("Accountant: " .$client->getHttpCode() .'.' .$client->getRawResponse());
            }

            $response = $client->getResponse();

            //transaction history,

            return $response;
        } catch (\Exception $e) {
            $user = array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            );
            //logging
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'params' => compact('user', 'amount', 'detail', 'note'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));

            throw $e;
        }
    }

    /**
     * Charging fee user
     *
     * @param $user
     * @param $amount
     * @param null $detail
     * @param null $note
     * @return mixed
     * @throws Exception
     * @throws \Exception
     */
    public static function charge($user, $amount, $detail = null, $note = null) {
        if (!$amount && !is_float($amount)) {
            throw new Exception("Invalid amount value");
        }

        if (is_int($user)) {
            $user = \Users::retrieveById($user);
        }

        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new Exception("{$user} must be instance of Users");
        }

        if (!$user->getAccountNo()) {
            throw new Exception("{$user->getUsername()}[{$user->getId()}] has not had account number");
        }

        if (!($sdAccountNo = self::getServiceAccountNo())) {
            throw new Exception("Config SeuDo account number empty");
        }

        try {
            $client = self::apiCreateTransfer($sdAccountNo, $user->getAccountNo(), $amount, $detail, $note);
            if ($client->getHttpCode() != 200) {
                throw new Exception("Accountant: " .$client->getHttpCode() .'.' .$client->getRawResponse());
            }

            $response = $client->getResponse();

            //transaction history,

            return $response;
        } catch (\Exception $e) {
            $user = array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            );
            //logging
            self::log(Logger::ERROR, 'Fail to create new account', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => compact('user', 'amount', 'detail', 'note')
            ));
            throw $e;
        }
    }

    /**
     * @param $user
     * @param $amount
     * @param $tran_type
     * @param null $bank
     * @param null $note
     * @param null $detail
     * @param null $bank_number
     * @param null $bank_journal_entry
     * @return Client
     * @throws Exception
     * @throws \Exception
     */
    public static function createDeposit($user, $amount, $tran_type,
                                         $bank = null,
                                         $note=null, $detail = null,
                                         $bank_number=null, $bank_journal_entry=null) {
        if (!$amount && !is_float($amount)) {
            throw new Exception("Invalid amount value");
        }

        if (is_int($user)) {
            $user = \Users::retrieveById($user);
        }

        if (!($user instanceof \Users)) {
            if (!is_scalar($user)) {
                $user = json_encode($user);
            }
            throw new Exception("{$user} must be instance of Users");
        }

        if (!$user->getAccountNo()) {// create account number
            self::createUserAccount($user);
//            throw new Exception("{$user->getUsername()}[{$user->getId()}] has not had account number");
        }

        try {

            $params = array(
                'tran_type' => $tran_type,
                'amount' => $amount ,
                'bank_name' => $bank,
                'account_no' => $user->getAccountNo(),
                'bank_number' => $bank_number,
                'bank_journal_entry' => $bank_journal_entry,
                'note'=> $note,
                'detail' => $detail
            );

            $client = Client::getClient();

            $client->post('deposit_voucher/new', $params);
            //logging
            self::log(Logger::INFO, 'Create new deposit voucher !', array(
                'params' => $params,
                'httpCode' => $client->getHttpCode(),
                'response' => $client->getResponse()
            ));

            $client->dispatch('onAfterCallingApiCreatedDepositVoucher', new Event(null, $client));
            return $client;

        } catch (\Exception $e) {
            $user = array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            );
            //logging
            self::log(Logger::ERROR, 'Fail to create new deposit voucher', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => compact('user', 'amount', 'tran_type',
                    'bank', 'note', 'detail',
                    'bank_number', 'bank_journal_entry')
            ));

            throw $e;
        }

    }

    /**
     * @todo get Transaction detail from Accountant's transaction
     * @param $uid
     * @return Client
     * @throws Exception
     * @throws \Exception
     */
    public static function getTransactionDetail($uid) {
        if (!$uid) {
            throw new Exception("Invalid amount value");
        }
        try {
            $client = Client::getClient();
            $client->get('transaction/transaction_info', array( 'uid' => $uid ));
            if ($client->getHttpCode() != 200) {
                throw new Exception("Accountant: " .$client->getHttpCode() .'.' .$client->getRawResponse());
            }
            $res = $client->getResponse();
            return $res;
        } catch (\Exception $e) {
            self::log(Logger::ERROR, 'Fail to call to get transaction info', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'uid' => $uid
            ));
            throw $e;
        }
    }

    /**
     * @param $account_no
     * @param \DateTime|string $from_date
     * @param \DateTime|string $to_date
     * @param int $offset
     * @param int $limit
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array|\SeuDo\Accountant\Client
     */
    public static function apiTransactionHistory($account_no, $from_date, $to_date, $offset = 0, $limit = 30) {
        $limit = abs($limit);

        if (is_string($from_date)) {
            if (!\Flywheel\Validator\Util::validateDate($from_date, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'from_date' format!");
            }

            $from_date = \DateTime::createFromFormat('Y-m-d H:i:s', $from_date);
        }

        if (is_string($to_date)) {
            if (!\Flywheel\Validator\Util::validateDate($to_date, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'to_date' format!");
            }

            $to_date = \DateTime::createFromFormat('Y-m-d H:i:s', $to_date);
        }

        if (!($from_date instanceof \DateTime) || !($to_date instanceof \DateTime)) {
            throw new \InvalidArgumentException("Invalid date range format!");
        }

        try {
            $client = Client::getClient();
            $client->get("transactions_history/account/{$account_no}", array(
                'max_record' => $limit,
                'offset_begin' => $offset,
                'from_date' => $from_date->format('Y-m-d H:i:s'),
                'to_date' => $to_date->format('Y-m-d H:i:s')
            ));

            return $client;
        } catch (\Exception $e) {
            self::log(Logger::ERROR, 'Fail to call to get transaction history', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }

    /**
     * @param \Users $user
     * @param $from_date
     * @param $to_date
     * @param int $offset
     * @param int $limit
     * @return array|mixed
     * @throws Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public static function getUserTransactionHistory(\Users $user, $from_date, $to_date, $offset = 0, $limit = 30) {
        if (0 == $limit) {//are u trolling me
            return array();
        }

        if (!$user->getAccountNo()) {
            throw new Exception("{$user->getUsername()}[{$user->getId()}] has not had account number");
        }

        try {
            $client = self::apiTransactionHistory($user->getAccountNo(), $from_date, $to_date, $offset, $limit);
            if ($client->getHttpCode() != 200) {
                throw new Exception("Accountant: " .$client->getHttpCode() .'.' .$client->getRawResponse());
            }
            $res = $client->getResponse();
            return $res;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $level the log level follow Monolog level
     * @param string $message
     * @param array $context
     */
    public static function log($level, $message, $context = array()) {
        try {
            $logger = Logger::factory('accountant');
            $logger->addRecord($level, $message, $context);
        } catch (\Exception $e) {
            Logger::factory('system')->error("Fail when create mongo logging: {$message}", $context);
            Logger::factory('system')->error($e->getMessage() . "\nTraces:\n" .$e->getTraceAsString());
        }
    }
} 