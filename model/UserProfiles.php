<?php 
/**
 * UserProfiles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserProfilesBase.php';
class UserProfiles extends \UserProfilesBase {

    const USER_PROFILE_AVATAR_FB = "avatar_fb",
        USER_PROFILE_EMAIL_FB = "email_fb",
        USER_PROFILE_USERNAME_FB = "username_fb",
        USER_PROFILE_FIRST_NAME_FB = "first_name_fb",
        USER_PROFILE_LASTNAME_FB = "last_name_fb",
        USER_PROFILE_LINK_FB = "link_fb";

    /**
     * Insert User Profile create by quyen
     * @param $user_profile
     * @return bool
     */
    public static function insertUserProfile($user_profile){

        if($user_profile['user_id'] && $user_profile['key_value']){
            foreach ($user_profile['key_value'] as $value) {
                $user = \UserProfiles::findOneByUserIdAndProfileKey($user_profile['user_id'],$value['profile_key']);

                if(isset($user) && ($user instanceof \UserProfiles)){
                    $now = new \DateTime();
                    $user->setNew(false);
                    $user->setCreatedTime($now);
                    $user->setValue($value['value']);
                    $result = $user->save();
                }else{
                    $now = new \DateTime();
                    $user = new \UserProfiles();
                    $user->setNew(true);
                    $user->setUserId($user_profile['user_id']);
                    $user->setProfileKey($value['profile_key']);
                    $user->setCreatedTime($now);
                    $user->setValue($value['value']);
                    $result = $user->save();
                }
            }
            return $result;

        }else{
            return false;
        }
    }
    public static function setUserProfile($userId, $key, $value){
        if(isset($userId) || isset($key) || isset($value)){
            $result = array();
            $result['user_id'] = $userId;
            $result['key_value'] = array(
                0 => array(
                    "profile_key" =>$key,
                    "value" => $value
                )
            );
            return self::insertUserProfile($result);
        }else{
            return false;
        }
    }

    public static function deleteUserProfiles($user_id, $key){
        if(isset($userId) || isset($key)){
            $delProfile = \UserProfiles::findOneByUserIdAndProfileKey($user_id,$key);
            if($delProfile instanceof \UserProfiles){
                if($delProfile->delete())return true;
            }
        }
        return false;
    }

    public static function getOneUserProfile($user, $profileKey){
        if(!$user instanceof \Users){
            return "";
        }else{

            $profile = self::findOneByProfileKeyAndUserId($profileKey, $user->getId());
            if(!$profile || !($profile instanceof \UserProfiles)) return "";
            
            return $profile->getValue();
        }
    }

    public static function getUserProfile(\Users $user){
        if(!$user instanceof \Users){
            throw new \Flywheel\Exception("user Not instanceof Users");
        }
        $profile_list = UserProfiles::findByUserId($user->getId());

        return $profile_list;
    }

}