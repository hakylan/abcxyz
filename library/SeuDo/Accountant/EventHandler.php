<?php

namespace SeuDo\Accountant;


use Flywheel\Db\Type\DateTime;
use Psr\Log\LogLevel;
use SeuDo\Logger;
use SeuDo\SMS\CustomerSupport;

class EventHandler {

    public static function transactionCommitCallback($transaction, $history = null) {
        $logger = Logger::factory('accountant');

        $log_transaction = Logger::factory('transaction');

        $log_transaction->log(LogLevel::DEBUG,"Log Transaction",$transaction);

        try{
            $userTransaction = \UserTransaction::retrieveByTransactionCode($transaction['uid']);
            if ($userTransaction) {
                $logger->warning(
                    "Transaction code already exists :{$transaction['uid']}",
                    $transaction
                );
                return true;
            }
            $user = \Users::retrieveByAccountNo($transaction['account']);

            if (!$user) {
                $logger->warning(
                    'User not found in with account no: ' .$transaction['account'] .' and transaction code : '.$transaction['uid']
                );
                return false;
            }
            $ending_balance = ($history)? $history['acc_balance'] : 0;
            $type = null;
            if ('IN' == $transaction['type']) {
                $type = \UserTransaction::TRANSACTION_TYPE_DEPOSIT;
            } elseif ('OUT' == $transaction['type']) {
                $type = \UserTransaction::TRANSACTION_TYPE_WITHDRAWAL;
            } else {
                $detail = ($transaction['detail']) ? json_decode($transaction['detail'], true) : null;
                if ($detail && isset($detail['type']) && $detail['type']) {
                    $type = $detail['type'];
                }
            }

            if ($type && isset(\UserTransaction::$transaction_type[$type])) {
                $userTransaction = self::_createUserTransaction($user, $ending_balance, $transaction, $type);
                if(!$userTransaction){
                    $logger->warning(
                        "Can't create transaction type with transaction code : {$transaction['uid']} and Type : {$type}"
                    );
                    return false;
                }
                $logger->info(
                    "Create transaction type with transaction code : {$transaction['uid']} and Type : {$type} success"
                );
                \UsersPeer::syncAccountBalance($user);

                if ('IN' == $transaction['type']
                    && $userTransaction->getTransactionType() == \UserTransaction::TRANSACTION_TYPE_DEPOSIT) {
                    //Deposit, send SMS
                    CustomerSupport::getInstance()->sendAccountBalanceChange($userTransaction);
                }

                return $userTransaction;
            }else{
                $logger->warning(
                    "Can't get transaction type with transaction code : {$transaction['uid']} and Type : {$type}"
                );
                return false;
            }
        }catch (\Exception $e){
            $logger->error(
                "Throw Exception with transaction code: {$transaction['uid']} -- {$e->getMessage()}",$transaction
            );
            throw $e;
        }
    }

    public static function depositFinishCallback($depositVoucher, $transaction, $history) {
        $userTransaction = \UserTransaction::retrieveByTransactionCode($transaction['uid']);
        if ($userTransaction) {
            return true;
        }

        $logger = Logger::factory('accountant');

        $user = \Users::retrieveByAccountNo($depositVoucher['account']);
        if (!$user) {
            $logger->warning(
                'User not found in with account no: ' .$depositVoucher['account']
            );

            return false;
        }

        $userTransaction = self::_createUserTransaction($user, $history['acc_balance'], $transaction, \UserTransaction::TRANSACTION_TYPE_DEPOSIT);
        \UsersPeer::syncAccountBalance($user);
        return $userTransaction;
    }

    /**
     * @param $withdrawalVoucher
     * @param $transaction
     * @param $history
     * @return bool|\UserTransaction
     */
    public static function withdrawalFinishCallback($withdrawalVoucher, $transaction, $history) {
        $userTransaction = \UserTransaction::retrieveByTransactionCode($transaction['uid']);
        if ($userTransaction) {
            return true;
        }

        $logger = Logger::factory('accountant');

        $user = \Users::retrieveByAccountNo($withdrawalVoucher['account']);
        if (!$user) {
            $logger->warning(
                'User not found in with account no: ' .$withdrawalVoucher['account']
            );
            return false;
        }

        $userTransaction = self::_createUserTransaction($user, $history['acc_balance'], $transaction, \UserTransaction::TRANSACTION_TYPE_WITHDRAWAL);
        \UsersPeer::syncAccountBalance($user);
        return $userTransaction;
    }

    /**
     * @param \Users $user
     * @param $ending_balance
     * @param $transaction
     * @param $type
     * @return bool|\UserTransaction
     */
    private static function _createUserTransaction($user, $ending_balance, $transaction, $type) {
        $logger = Logger::factory('transaction_debug');
        try{
            $detail = ($transaction['detail']) ? json_decode($transaction['detail'], true) : null;
            if (is_array($detail) && isset($detail['detail'])) {
                $detail = $detail['detail'];
            }else{
                $detail = $transaction['detail'];
            }

            $transaction_check = \UserTransaction::retrieveByTransactionCode($transaction['uid']);

            if($transaction_check instanceof \UserTransaction){
                $logger->warning("Already exists user transaction with transaction code {$transaction_check->getTransactionCode()}", array(
                    'user' => $user->getId(),
                    "amount" => $transaction_check->getAmount(),
                    'ending_balance' => $transaction_check->ending_balance,
                    'transaction_type' => $transaction_check->getTransactionType(),
                ));
                return false;
            }

            $userTransaction = new \UserTransaction();
            $userTransaction->setUserId($user->getId());
            $userTransaction->setState(\UserTransaction::STATE_COMPLETED);
            $userTransaction->setTransactionCode($transaction['uid']);
            $userTransaction->setTransactionType($type);
            $userTransaction->setEndingBalance($ending_balance);
            $userTransaction->setTransactionNote($transaction['note']);
            $userTransaction->setTransactionDetail($detail);
            $userTransaction->setAmount($transaction['amount']);

            if (is_array($transaction['modified_time']) && isset($transaction['modified_time']['date'])) {
                $closed_time = new DateTime($transaction['modified_time']['date']);
            } elseif (is_scalar($transaction['modified_time'])) {
                $closed_time = new DateTime($transaction['modified_time']);
            } else {
                $closed_time = new DateTime();
            }
            $userTransaction->setClosedTime($closed_time);

            if(!$userTransaction->save()) {
                $logger->error('Could not save user transaction callback from accountant layer', array(
                    'user' => $user->getId(),
                    'ending_balance' => $ending_balance,
                    'transaction_type' => $type,
                    'transaction' => $transaction,
                ));
                return false;
            }

            $logger->info("Saved user's transaction[{$userTransaction->getId()}] from accountant event callback");

            return $userTransaction;
        }catch (\Exception $e){
            $logger->error('Could not save user transaction callback from accountant layer '.$e->getMessage(), array(
                'user' => $user->getId(),
                'ending_balance' => $ending_balance,
                'transaction_type' => $type,
                'transaction' => $transaction,
            ));
            return false;
        }

    }
} 