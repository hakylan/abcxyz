<?php 
/**
 * EmailActivities
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/EmailActivitiesBase.php';
class EmailActivities extends \EmailActivitiesBase {

    public static function setNewActivitis($email, $id){
        $now = new \Flywheel\Db\Type\DateTime();
        $expire = new \Flywheel\Db\Type\DateTime("+ 1 hour");
        $code = ( md5(uniqid() .mt_rand() ));
        $emailActivity = new \EmailActivities();
        $emailActivity->setNew(true);
        $emailActivity->setCode($code);
        $emailActivity->setEmail($email);
        $emailActivity->setActivity('VERIFY_EMAIL');
        $emailActivity->setParams(json_encode(array('user_id' =>$id )));
        $emailActivity->setCreatedTime($now);
        $emailActivity->setExpiredTime($expire);
        $check = $emailActivity->save();
        if($check == true){
            return $emailActivity;
        }
        return false;
    }
}