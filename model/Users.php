<?php
use Flywheel\Db\Type\DateTime;
use Flywheel\Redis\Client as RedisClient;
use SeuDo\Main;
use SeuDo\Permission;

/**
 * Users
 * @version		$Id$
 * @package		Model
 */
require_once dirname(__FILE__) . '/Base/UsersBase.php';

class Users extends \UsersBase {

    const STATUS_ACTIVE = 'ACTIVE',
            STATUS_INACTIVE = 'INACTIVE',
            STATUS_BAN = 'BAN',
            STATUS_LOCK = 'LOCK',
            STATUS_DELETE = 'DELETE';
    const SECTION_CUSTOMER = 'CUSTOMER',
            SECTION_CRANE = 'CRANE';
    const
        SIZE_AVATAR160 = '160x160',
        SIZE_AVATAR128 = '128x128',
        SIZE_AVATAR64 = '64x64',
        SIZE_AVATAR48 = '48x48',
        SIZE_AVATAR32 = '32x32';

    // Verify user status
    const NOT_VERIFY_MOBILE = -1,
        NOT_HAS_MAIN_ADDRESS = -2,
        NOT_HAS_CODE = -3;

    /**
     * @var \UserMobiles[]
     */
    protected $_mobiles = array();

    /**
     * @var \UserProfiles[]
     */
    protected $_profile = array();

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'joined_time',
                'modify_attr' => 'modified_time'
            )
        );
    }

    /**
     * @return string
     */
    public function getOneMobileUsing(){
        $mobiles = \UserMobiles::read()->andWhere("user_id={$this->getId()}")
            ->orderBy("id","desc")
            ->setFirstResult(0)->setMaxResults(1)->execute()
            ->fetchAll(\PDO::FETCH_CLASS,\UserMobiles::getPhpName(),array(null,false));
        if(!empty($mobiles)){
            foreach ($mobiles as $mobile) {
                if($mobile instanceof \UserMobiles){
                    return $mobile->getMobile();
                }
            }
        }
        return "";
    }

    /**
     * get user's mobiles number
     *
     * return \UserMobiles[]
     */
    public function getMobiles() {
        if (empty($this->_mobiles) && $this->isVerifyMobile()) {
            $this->_mobiles = (array) \UserMobiles::getUserMobiles($this);
        }

        return $this->_mobiles;
    }


    public function getProfile(){
        if (empty($this->_profile)) {
            $this->_profile = \UserProfiles::getUserProfile($this);
        }

        return $this->_profile;
    }

    /**
     * check user have verified his mobile number
     * @return bool
     */
    public function isVerifyMobile() {
        return (bool) $this->getVerifyMobile();
    }

    /**
     * check user have verified his mobile number
     * @return bool
     */
    public function isVerifyEmail() {
        return (bool) $this->getVerifyEmail();
    }

    /**
     * god reborn, plz help us
     * @return bool
     */
    public function isGod() {
        return 1 == $this->getId();
    }

    protected function _beforeSave() {
        parent::_beforeSave();
        if (!$this->secret_key) {
            $this->setSecretKey(self::genSecretKey());
        }

        if (self::SECTION_CUSTOMER == $this->getSection() && !$this->code) {
            $this->setCode(self::genCustomerCode());
        }
    }

    protected function _afterSave() {
        parent::_afterSave();
        self::pushToRedis($this);
    }

    /**
     * @return \Flywheel\Event\Event|void
     */
    protected function _afterDelete() {
        $redis = RedisClient::getConnection(self::getTableName());
        $redis->delete(REDIS_USER .$this->getId(), REDIS_USER_USERNAME .$this->getUsername());
        if ($this->getVerifyEmail() && $this->getEmail()) {
            $redis->delete(REDIS_USER_EMAIL .$this->getEmail());
        }
    }
    
    
    ///////////////////// Level Discount.

    /**
     * Get object Level setting by object user -- Quyen
     * @return array|LevelSetting
     */
    public function getLevelSetting(){
        $level_setting = \LevelSetting::retrieveById($this->getLevelId());
        if($level_setting instanceof \LevelSetting){
            return $level_setting;
        }
        return array();
    }

    /**
     * Retrieve object by id
     * @param int $id
     * @return bool|\Users
     */
    public static function retrieveById($id) {
        if (!$id) {
            return false;
        }

        if (($obj = self::getInstanceFromPool($id))) {
            return $obj;
        }

        if (($data = self::retrieveFromRedis($id))) {
            $obj = new self($data, false);
            return $obj;
        }

        $obj = self::findOneById($id);
        if ($obj) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * retrieve object by username
     * @param string $username
     * @return bool|Users
     */
    public static function retrieveByUsername($username) {
        //check from pool
        /** @var \Users[] $objs */
        $objs = static::getInstancesFromPool();
        foreach($objs as $obj) {
            if ($obj->getUsername() == $username) {
                return $obj;
            }
        }

        if (($id = RedisClient::getConnection(self::getTableName())->get(REDIS_USER_USERNAME .$username))) {
            return self::retrieveById($id);
        }

        if (($obj = self::findOneByUsername($username))) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * retrieve by email
     * @param string $email
     * @return bool|Users
     */
    public static function retrieveByEmail($email) {
        //check from pool
        /** @var \Users[] $objs */
        $objs = static::getInstancesFromPool();
        foreach($objs as $obj) {
            if ($obj->getVerifyEmail() && $obj->getEmail() == $email) {
                return $obj;
            }
        }

        if (($id = RedisClient::getConnection(self::getTableName())->get(REDIS_USER_EMAIL .$email))) {
            return self::retrieveById($id);
        }

        if (($obj = self::findOneByVerifyEmailAndEmail(1, $email))) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * Retrieve a object by customer code
     * get from redis if stored
     * @param string $code
     * @return bool|Users
     */
    public static function retrieveByCode($code) {
        //check from pool
        /** @var \Users[] $objs */
        $objs = static::getInstancesFromPool();
        foreach($objs as $obj) {
            if ($obj->getVerifyEmail() && $obj->code == $code) {
                return $obj;
            }
        }

        if (($id = RedisClient::getConnection(self::getTableName())->get(REDIS_USER_CODE .$code))) {
            return self::retrieveById($id);
        }

        if (($obj = self::findOneByCode($code))) {
            self::addInstanceToPool($obj, $obj->getId());
            self::pushToRedis($obj);
            return $obj;
        }

        return false;
    }

    /**
     * @param \Users $obj
     * @return bool
     */
    public static function pushToRedis($obj) {
        if (!$obj || !($obj instanceof \Users)) {
            return false;
        }

        $data = $obj->toArray();
        foreach ($data as $k => $v) {
            if ($v instanceof DateTime) {
                $data[$k] = $v->toString();
            }
        }

        $redis = RedisClient::getConnection(self::getTableName());
        $redis->hMset(REDIS_USER .$obj->getId(), $data);
        $redis->set(REDIS_USER_USERNAME .$obj->getUsername(), $obj->getId());
        if ($obj->getVerifyEmail() && $obj->getEmail()) {
            $redis->set(REDIS_USER_EMAIL .$obj->getEmail(), $obj->getId());
        }

        if (self::SECTION_CUSTOMER == $obj->getSection() && $obj->getCode()) {
            $redis->set(REDIS_USER_CODE . $obj->getCode(), $obj->getId());
        }
    }

    /**
     * retrieve data from redis
     * @param $user_id
     * @return array|null
     */
    public static function retrieveFromRedis($user_id) {
        return RedisClient::getConnection(self::getTableName())->hGetAll(REDIS_USER .$user_id);
    }

    /**
     * generate password salt
     *
     * @param string $prefix
     * @return string
     */
    public static function generateSalt($prefix = '') {
        return sha1($prefix . uniqid(time(), true));
    }

    /**
     * hasting user's password
     * @param $plainText
     * @param null $salt
     * @return string
     */
    public static function hashPassword($plainText, $salt = null) {
        $salt = (null == $salt) ? \ModelUtil::randSha1(40) : substr($salt, 0, 40);
        return $salt . md5($salt . $plainText);
    }

    public function count($q = null) {
        $total = 0;
        if ($q != null && ($q instanceof \Flywheel\Db\Query)){
            $total =  $q->count()->execute();
        }
        return $total;
    }

    public function search($q = null) {
        if ($q != null && ($q instanceof \Flywheel\Db\Query)){
            return $q->execute()
                ->fetchAll(\PDO::FETCH_CLASS, 'Users', array(null, false));
        }
        return false;
    }

    /**
     * Register Account
     * @param array $profile
     * @return array|Users
     */
    public static function registerAccount($profile = array()) {
        if(!empty($profile)){
            $birthday = isset($profile['birthday']) ? $profile['birthday'] : '';
            $user_facebook = new Users();
            $user_facebook->setUsername($profile['username']);
            $user_facebook->setPassword($profile['password']);
            $user_facebook->setGender($profile['gender']);

            if($birthday != ''){
                $birthday = new DateTime($birthday);

                $birthday = $birthday->format('Y-m-d H:i:s');

                $user_facebook->setBirthday($birthday);
            }


            $user_facebook->setSection(Users::SECTION_CUSTOMER);
            if (isset($profile['id'])) {

                $url_Avatar_facebook = "https://graph.facebook.com/{$profile['id']}/picture?width=600&height=600";

                $user_facebook->setAvatar(self::setAvatarFacebook($user_facebook, $url_Avatar_facebook));

                $user_facebook->setFacebookId($profile['id']);
            }

            if (isset($profile['email_sd'])) {
                $profile['email'] = $profile['email_sd'];
                // Verify Email
                $user_facebook->setVerifyEmail(0);
            } elseif (isset($profile['email_fb'])) {
                $profile['email'] = $profile['email_fb'];
                // Khong can Verify Email
                $user_facebook->setStatus(Users::STATUS_ACTIVE);
                $user_facebook->setVerifyEmail(1);
            }else{
                $user_facebook->setStatus(Users::STATUS_INACTIVE);
                $user_facebook->setVerifyEmail(0);
            }

            $user_facebook->setVerifyMobile(0);
            $user_facebook->setEmail($profile['email']);

            $user_facebook->setFirstName($profile['first_name']);
            $user_facebook->setLastName($profile['last_name']);
            $user_facebook->setJoinedTime(new DateTime());
            $user_facebook->setLastLoginTime(new DateTime());

            $result = $user_facebook->save();
            if ($result) {
                \UserMailUtil::sendWelcomeEmail($user_facebook->getUsername() ,$user_facebook->getEmail()
                    ,Main::getHomeUrl(), $user_facebook->getFullName() );
                if (isset($profile['id'])) {
                    $profile['avatar'] = "https://graph.facebook.com/{$profile['id']}/picture?width=600&height=600";

                    Users::insertProfileFacebook($profile,$user_facebook);
                }
                return $user_facebook;
            } else {
                return array();
            }
        }else{
            return array();
        }

    }

    /**
     * // Inser profile Facebook
     * @param $profile
     * @param $user
     * @return bool
     */
    public static function insertProfileFacebook($profile,$user)
    {

        if(!$user || empty($profile)) return false;
        $user_profile = array();
        if(is_numeric($user)){
            $user_profile['user_id'] = $user;
        }else if($user instanceof \Users){
            $user_profile['user_id'] = $user->id;
        }

        $email = isset($profile['email']) ? $profile['email'] : '';

        $username = isset($profile['username']) ? $profile['username'] : '';

        $first_name = isset($profile['first_name']) ? $profile['first_name'] : '';
        $last_name = isset($profile['last_name']) ? $profile['last_name'] : '';
        $link = isset($profile['link']) ? $profile['link'] : '';

        $avatar = \Users::setAvatarFacebook($user,$profile['avatar']);

        $user_profile['key_value'] = array(
            0 => array(
                "profile_key" => "avatar_fb",
                "value" => $avatar
            ),
            1 => array(
                "profile_key" => "email_fb",
                "value" => $email
            ),
            2 => array(
                "profile_key" => "username_fb",
                "value" => $username
            ),
            3 => array(
                "profile_key" => "first_name_fb",
                "value" => $first_name
            ),
            4 => array(
                "profile_key" => "last_name_fb",
                "value" => $last_name,
            ),
            5 => array(
                "profile_key" => "link_fb",
                "value" => $link,
            )
        );

        return UserProfiles::insertUserProfile($user_profile);
    }

    /**
     * @param $fb_user_id
     * @return bool|Users
     */
    public static function checkRegisterFacebook($fb_user_id) {
        $user = Users::retrieveByFacebookId($fb_user_id);
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }


    //Delete url Avatar facebook for user.
    public static function deleteProfileFacebook($user) {
        try{
            if($user && ($user instanceof \Users)){

                $key_array = array(UserProfiles::USER_PROFILE_AVATAR_FB,UserProfiles::USER_PROFILE_EMAIL_FB,
                    UserProfiles::USER_PROFILE_FIRST_NAME_FB,UserProfiles::USER_PROFILE_LASTNAME_FB,UserProfiles::USER_PROFILE_LINK_FB,
                    UserProfiles::USER_PROFILE_USERNAME_FB
                );

                foreach ($key_array as $key) {
                    \UserProfiles::deleteUserProfiles($user->getId(), $key);
                }
            }
            return true;
        }catch (\Exception $e){
            return false;
        }

    }

    // End Quyen

    public static function getAvatarFacebook($user) {
        if(!$user || !($user instanceof Users)) return false;

        if($user->getFacebookId()>0){
            $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
            if(!$sfsConfig){
                throw new \Exception('Sfs Config is missing !');
            }
            $sfsUrl = $sfsConfig['service_url'];

            $avatarFace = \UserProfiles::findOneByUserIdAndProfileKey($user->getId(), UserProfiles::USER_PROFILE_AVATAR_FB);

            if($avatarFace) return $sfsUrl.'/'.$avatarFace->getValue();
        }
        return false;
    }


    public static function getAvatar160x($user){
        if(!$user || !($user instanceof Users)) return false;
        return Users::getAvatarSize($user, self::SIZE_AVATAR160);
    }

    public static function getAvatar128x($user){
        if(!$user || !($user instanceof Users)) return false;
        return Users::getAvatarSize($user, self::SIZE_AVATAR128);
    }

    public static function getAvatar48x($user){
        if(!$user || !($user instanceof Users)) return false;
        return Users::getAvatarSize($user, self::SIZE_AVATAR48);
    }
    /**/
    public static function getAvatar32x($user){
        if(!$user || !($user instanceof Users)) return false;

        return Users::getAvatarSize($user, self::SIZE_AVATAR32);
    }


    /**
     * @param $user
     * @param string $url_facebook
     * @return bool
     */
    public static function setAvatarFacebook($user, $url_facebook = '') {
        if(!$user || !($user instanceof \Users)) return false;
        $sfs = \SeuDo\SFS\Client::getInstance();

        $uploader = new \SeuDo\SFS\Upload('useravatar/'.$user->getId());

        $uploader->setUrl($url_facebook);
        $uploader->setFileName(uniqid().'_'.time().'.jpg');
        if($sfs->upload($uploader)) {
            $sfs->getHttpCode();
        }

        $avatar = json_decode($sfs->getResponse());
        if($avatar){
            return $avatar->file;
        }
        return "";
    }

    //32x32
    public static function getAvatarSize($user, $size = self::SIZE_AVATAR32) {
        $publicPath   = Main::getHomeUrl();
        $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
        if(!$sfsConfig){
            throw new \Exception('Sfs Config is missing !');
        }
        $sfsUrl = $sfsConfig['service_url'];

        if(!$user || !($user instanceof \Users)) return false;

        $getAvatar = $user->getAvatar();
        if($getAvatar==''){
            $getAvatar = \UsersPeer::getAvatarFacebookNotSFS($user);
            if($getAvatar==''){
                return $publicPath.'assets/images/imgdemo/avatardefault.png';
            }
        }
        $sizeArray = explode('x',$size);
        $width = $sizeArray[0];

        $relativeImagePath = isset($getAvatar)?$getAvatar:'';

        if($relativeImagePath==$getAvatar) return $sfsUrl.'/thumb/resize/w_'.$width.'/'.$relativeImagePath;

        return $publicPath.'assets/images/imgdemo/avatardefault.png';
    }

    public function getFullName() {
        return $this->getLastName().' '.$this->getFirstName();
    }

    /**
     * Method get user's full name as shorten string
     * The last word in name will be keep, other words will be display as abbreviation
     * @return string
     */
    public function getShortenFullName() {
        $full_name = '';

        $part = explode(' ', $this->getFullName());

        if( sizeof($part) >= 3 && $this->getFullName() != "" ){
            for($i = 0, $size = sizeof($part); $i < $size; ++$i) {
                if ($i != $size-1) {
                    if(isset($part[$i][0])){
                        $full_name .= mb_strtoupper(mb_substr($part[$i],0, 1, "UTF-8"), "UTF-8");
                    }
                } elseif(isset($part[$i])) {
                    $full_name .= '. ' .$part[$i];
                }
            }
        }
        $shorten_fullname = $this->getFullName();

        if( sizeof($part) >= 3 ) {
            $shorten_fullname = $full_name != "" ? $full_name : $this->getFullName();
        }

        $shorten_fullname = $shorten_fullname ? $shorten_fullname : $this->getUsername();

        return $shorten_fullname;
    }

    public function getSecretKey() {
        if (!$this->secret_key) {
            $this->setSecretKey($this->genSecretKey());
            $this->save(true);
        }

        return $this->secret_key;
    }

    public static function genSecretKey() {
        return \ModelUtil::randMd5(32);
    }

    /**
     * Get user's customer code. Create new if not existed
     * @return string|null
     */
    public function getCode() {
        if (!$this->code && self::SECTION_CUSTOMER == $this->getSection()) {
            $code = self::genCustomerCode();
            $this->setCode($code);
            $this->save();
        }

        return $this->code;
    }

    /**
     * generator a new customer code and check it has not used
     * @return string
     */
    public static function genCustomerCode() {
        $vowel      = array('A', 'E', 'I', 'O', 'U');
        $consonants = array('B', 'C', 'D', 'G', 'H', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'X');
        do {
            $char_part = $consonants[array_rand($consonants)]; //.$vowel[array_rand($vowel)];
            $number_part = \ModelUtil::getLuckyNumber(3);
            $code = "{$char_part}{$number_part}";
            $check = \Users::retrieveByCode($code);
        } while($check);

        return $code;
    }

    public function getAvatarFollowSize($size = self::SIZE_AVATAR32){
        $publicPath  = \Flywheel\Config\ConfigHandler::get('url.home');
        $sfsConfig = \Flywheel\Config\ConfigHandler::get('sfs');
        if(!$sfsConfig){
            throw new \Exception('Sfs Config is missing !');
        }
        $sfsUrl = $sfsConfig['service_url'];

        if($this->getAvatar() == '') return $publicPath.'assets/images/imgdemo/avatardefault.png';

        $sizeArray = explode('x',$size);
        $width = $sizeArray[0];

        $relativeImagePath = $this->getAvatar();

        if( $relativeImagePath !='' ) return $sfsUrl.'/thumb/resize/w_'.$width.'/'.$relativeImagePath;

        return $publicPath.'assets/images/imgdemo/avatardefault.png';
    }

    public function getAvatar16x (){
        return $this->getAvatarFollowSize('16x16');
    }

    /**
     * check is active account
     * @return bool
     */
    public function isActive() {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }
}
