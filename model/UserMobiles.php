<?php
use Flywheel\Db\Type\DateTime;

/**
 * UserMobiles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserMobilesBase.php';
class UserMobiles extends \UserMobilesBase {
    const COMING_BY_SMS_GATEWAY = 'SMS_GATEWAY',
        COMING_BY_CUSTOMER_CARE = 'CUSTOMER_CARE';

    /**
     * Refund SMS verify fee
     * @param UserMobiles $om
     * @return UserTransaction
     * @throws RuntimeException
     * @throws Exception
     */
    public static function refundVerifySmsFee(\UserMobiles $om) {
        $user = \Users::retrieveById($om->getUserId());
        if (!($user instanceof \Users)) {
            throw new \RuntimeException("User not found with id:" .$om->getUserId());
        }

        $refund_amount = 5000;

        $detail = array(
            'type' => \UserTransaction::TRANSACTION_TYPE_ADJUSTMENT,
            'message' => 'Trả lại phí SMS khi xác nhận số điện thoại ' .$om->getMobile()
        );

        $transfer = \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail), $detail['message']);

        try {
            $detail = json_encode($detail);
            $balance = $transfer['to_account']['balance'];
            \UsersPeer::changeAccountBalance($user, $balance);

            //save user transaction
            $history = new \UserTransaction();
            $history->setUserId($user->getId());
            $history->setAmount($transfer['receiving_transaction']['amount']);
            $history->setEndingBalance($transfer['to_account']['balance']);
            $history->setTransactionType(\UserTransaction::TRANSACTION_TYPE_ADJUSTMENT);
            $history->setTransactionDetail(json_encode($detail));
            $history->setTransactionNote($transfer['receiving_transaction']['note']);
            $history->setTransactionCode($transfer['receiving_transaction']['uid']);
            $history->setState(\UserTransaction::STATE_COMPLETED);
            if (is_array($transfer['receiving_transaction']['modified_time'])) {
                $closedTime = new DateTime($transfer['receiving_transaction']['modified_time']['date']);
            } elseif (is_scalar($transfer['receiving_transaction']['modified_time'])) {
                $closedTime = new DateTime($transfer['receiving_transaction']['modified_time']);
            } else {
                $closedTime = new DateTime();
            }
            $history->setClosedTime($closedTime);

            if (!$history->save()) {
                throw new \RuntimeException('Could not save user transaction:' . $history->getValidationFailuresMessage("\n"));
            }

            $user->commit();
            \SeuDo\Logger::factory('business')->info('Refund SMS free', array(
                'user' => $user->getUsername(),
                'accountant_transaction' => $history->getTransactionCode(),
                'user_transaction' => $history->getId()
            ));
            return $history;
        } catch (\Exception $e) {
            //charging back
            \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
                'type' => 'ROLLBACK',
                'detail' => 'Giao dịch hoàn tiền phí SMS không thành công. Trả lại tiền cho dịch vụ.'
            )));
            throw $e;
        }
    }

    /**
     * get Users mobile number
     * @param \Users|int $user
     * @return \UserMobiles[]
     */
    public static function getUserMobiles($user) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        return self::findByUserId($user);
    }

    /**
     * Get latest use mobile number
     *
     * @param $user
     * @return \UserMobiles
     */
    public static function getLatestMobileNo($user) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        $result = self::select()
            ->where('`user_id` = :user_id')
            ->setParameter(':user_id', $user, \PDO::PARAM_INT)
            ->orderBy('created_time', 'DESC')
            ->setMaxResults(1)
            ->execute();
        if (is_array($result) && !empty($result)) {
            return $result[0];
        }

        return null;
    }

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'created_time',
            )
        );
    }

    /**
     * get user had verified phone no
     * @param $phone
     * @return bool|Users
     */
    public static function getUserByPhone($phone) {
        $om = self::findOneByMobile($phone);
        if (!$om) {
            return false;
        }

        return \Users::retrieveById($om->getUserId());
    }

    /**
     * get total mobiles number user used
     * @param $user
     * @return int
     */
    public static function totalMobilesUsed($user) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        return self::read()
            ->count('id')
            ->where('`user_id` = ?')
            ->setParameter(0, $user, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * remove old phone number over quantity
     *
     * @param \Users|int $user
     * @param \SystemConfig $limit
     * @return int affected rows
     */
    public static function deleteOldPhone($user, $limit) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        $overdue = self::totalMobilesUsed($user) - $limit->getConfigValue();

        if ($overdue > 0) {
            $query = self::write();
            $result = $query->delete(self::getTableName())
                ->where('`user_id` = ?')
                ->setParameter(0, $user, \PDO::PARAM_INT)
                ->setMaxResults(abs($overdue))
                ->orderBy('id')
                ->execute();

            return $result;
        }
    }

    /**
     * Clear other usage
     *
     * @param $user
     * @param $phone
     * @return int affected rows
     */
    public static function clearOtherUsage($user, $phone) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        $result = self::write()
            ->delete(self::getTableName())
            ->where('`user_id` != :user_id')
            ->andWhere('`mobile` = :phone')
            ->setParameter(':user_id', $user, \PDO::PARAM_INT)
            ->setParameter(':phone', $phone, \PDO::PARAM_STR)
            ->execute();

        return $result;
    }

    public static function validPhoneNo($phone_number){
        $phone_number = preg_replace("/[^0-9\(\)]/", "", $phone_number);
        if(strpos($phone_number,'+') !==false){
            $phone_number = str_replace('+','',$phone_number);
        }
        $vncode = substr($phone_number,0,2);

        if($vncode == '84'){
            $plus_path = substr($phone_number,2,strlen($phone_number));
            $phone_number = '0'.$plus_path;
        }
        return $phone_number;
    }

    /**
     * add new user phone, update if it's existed
     * @param $user
     * @param $phone
     * @param string $coming_by
     * @throws InvalidArgumentException
     * @throws Exception
     * @return bool|UserMobiles
     */
    public static function addPhone($user, $phone, $coming_by = self::COMING_BY_SMS_GATEWAY) {
        if (!($user instanceof \Users)) {
            $user = \Users::retrieveById($user);
        }

        if (!($user instanceof \Users)) {
            throw new \InvalidArgumentException("Method require first parameter is Users instance or user's id number");
        }

        if (($om = self::findOneByUserIdAndMobile($user->getId(), $phone))) {
            $om->setCreatedTime(new DateTime());
        } else {
            $om = new self();
            $om->setUserId($user->getId());
            $om->setMobile($phone);
        }

        $om->setComingBy(self::COMING_BY_SMS_GATEWAY);

        $om->beginTransaction();
        try {
            if ($om->save()) {
                if (!$user->getVerifyMobile()) {
                    $user->setVerifyMobile(true);
                    $user->save(false);
                }
                //clear old Phone
                self::deleteOldPhone($user, \SystemConfig::retrieveByKey(\SystemConfig::MAX_USER_MOBILES_NO));
                self::clearOtherUsage($user, $phone);
                $om->dispatch('onAddUserMobileNumber', new \SeuDo\Event\User($om));
                $om->commit();
                return $om;
            } else {
                if ($om->isValid()) {
                    throw new \RuntimeException('Fail to save new user mobile' . $om->getValidationFailuresMessage("\n"));
                }
            }
        } catch (\Exception $e) {
            $om->rollBack();
            throw $e;
        }

        return false;
    }
}