<?php
/**
 * Users
 * @version		$Id$
 * @package		Model
 */
require_once dirname(__FILE__) .'/Base/UsersBase.php';
class UsersPeer extends \UsersBase {

    const DEPOSIT_ERR_LEVEL_4 = 'Tài khoản chưa xác thực';
    const DEPOSIT_ERR_LEVEL_7 = 'Email chưa xác thực';
    const DEPOSIT_ERR_LEVEL_8 = 'Số điện thoại chưa xác thực';
    const DEPOSIT_ERR_LEVEL_9 = 'Bạn chưa có mã khách hàng';

    /**
     * Sync Balance for Account
     * @param int|\Users $user user's id or user instance
     * @return bool
     */
    public static function syncAccountBalance($user){
        if (!($user instanceof \Users)) {
            $user = \Users::retrieveById($user);
        }

        if (!$user) {
            return false;
        }

        if (!$user->getAccountNo()) {
            return false;
        }

        $logger = \SeuDo\Logger::factory('accountant');

        $accountInfo = \SeuDo\Accountant\Util::getUserAccountDetail($user);

        if ($accountInfo['balance'] != $user->getAccountBalance()) {
            $user->setAccountBalance($accountInfo['balance']);
            if ($user->save()) {
                $user->dispatch('onAccountBalanceChanged', new \SeuDo\Event\User($user));
                $logger->info('Sync user accountant balance. Account no: ' .$user->getAccountNo());
                return true;
            } else {
                $context = (!$user->isValid())? $user->getValidationFailuresMessage("\n") : '';
                $logger->error('Fail to save user account balance. ' .$context);
                return false;
            }
        }

        return true;
    }

    /**
     * Change user account balance
     * @param Users $user
     * @param $new_balance
     * @return bool
     */
    public static function changeAccountBalance(\Users $user, $new_balance) {
        if ($user->getAccountBalance() != $new_balance) {
            $user->setAccountBalance($new_balance);
            return $user->save(false);
        }

        return true;
    }

    /**
     * validate
     * @author binhnt
     * @param Users $user
     * @return boolean
     */
    public static function isEligibleToOrder(\Users $user) {
        if(!$user instanceof \Users){
            return false;
        }
        try{
            if(\Users::SECTION_CUSTOMER == $user->getSection() && $user->getVerifyEmail() == 1){
                if($user->getStatus() != \Users::STATUS_ACTIVE){
                    $user->setStatus(\Users::STATUS_ACTIVE);
                    $user->save();
                }
                return true;
            }else{
                return false;
            }
        }catch (\Flywheel\Exception $e){
            return false;
        }

    }

    public static function getAvatarFacebookNotSFS($user) {
        if(!$user || !($user instanceof Users)) return false;

        if($user->getFacebookId()>0){

            $avatarFace = \UserProfiles::findOneByUserIdAndProfileKey($user->getId(), UserProfiles::USER_PROFILE_AVATAR_FB);

            if($avatarFace) return $avatarFace->getValue();
        }
        return false;
    }

    /**
     * Get email's current owner
     * @param $email
     * @return bool|Users
     */
    public static function getEmailOwner($email) {
        return \Users::retrieveByEmail($email);
    }

    /**
     * Check email's is taken and has been verified
     * @param $email
     * @return bool
     */
    public static function checkIsTakenEmail($email) {
        $owner = self::getEmailOwner($email);
        return ($owner && $owner->isVerifyEmail());
    }

    /**
     * search by user's code or user's username
     * use searching in redis first, if not found searching again in database
     *
     * @param $code
     * @param $username
     * @param bool $assoc return associate array with $assocKey
     * @param string $assocKey
     * @return \Users[]
     */
    public static function searchByCodeOrUsername($code = null, $username = null, $assoc = false, $assocKey = 'username') {
        $redis = \Flywheel\Redis\Client::getConnection(Users::getTableName());
        $id_list = array();

        if (!$code && !$username) {
            return array();
        }

        if ($username) {//search by username
            $keys = $redis->keys(REDIS_USER_USERNAME .$username .'*');
            if (!empty($keys)) {
                foreach($keys as $key) {
                    $id_list[] = $redis->get($key);
                }
            }
        }

        if ($code) { //search by code
            $keys = $redis->keys(REDIS_USER_CODE .$code .'*');
            if (!empty($keys)) {
                foreach($keys as $key) {
                    $id_list[] = $redis->get($key);
                }
            }
        }

        $result = array();

        if (!empty($id_list)) {
//            echo 'vao 1';
            foreach($id_list as $id) {
                if ($r = \Users::retrieveById($id)) {
                    if ($assoc) {
                        $result[$r->$assocKey] = $r;
                    } else {
                        $result[] = $r;
                    }
                }
            }
        } else {//fuck, maybe not found
//            echo 'vao 2';
            $q = \Users::select();

            if( $username && !$code ) {
                $q->andWhere('`username` LIKE :username')
                    ->setParameter(':username', "{$username}%", \PDO::PARAM_STR);
            }

            if( !$username && $code ) {
                $q->andWhere('`code` LIKE :code')
                    ->setParameter(':code', "{$code}%", \PDO::PARAM_STR);
            }

            if( $username && $code ) {
                $q->andWhere( " ( `username` LIKE '%{$username}%' OR `code` LIKE '%{$code}%' ) " );
            }

            /** @var \Users[] $users */
//            echo $q->getSQL();
            $users = (array) $q->execute();
            for($i = 0, $size = sizeof($users); $i < $size; ++$i) {
                if ($assoc) {
                    $result[$users[$i]->$assocKey] = $users[$i];
                } else  {
                    $result[] = $users[$i];
                }
            }
        }

        return $result;
    }

    /**
     * Get list user not exist phone to CSKH request phone number
     * @return array
     */
    public static function getUserNotExistPhone(){
        $users = \Users::read()->andWhere("status = '".\Users::STATUS_ACTIVE."'")
            ->andWhere("section='".\Users::SECTION_CUSTOMER."'")
            ->orderBy("id","desc")->execute()
            ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
        $user_missing_phone = array();
        foreach ($users as $user) {
            if($user instanceof \Users){
                $user_address = \UserAddress::read()->andWhere("reciver_phone != ''")
                    ->andWhere("user_id={$user->getId()}")
                    ->execute()->fetchAll(\PDO::FETCH_CLASS,\UserAddress::getPhpName(),array(null,false));
                $user_mobile = \UserMobiles::read()->andWhere("user_id={$user->getId()}")
                    ->execute()->fetchAll(\PDO::FETCH_CLASS,\UserMobiles::getPhpName(),array(null,false));
                if(!$user_address && !$user_mobile){
                    $user_missing_phone[] = $user;
                }
            }
        }
        return $user_missing_phone;
    }
}