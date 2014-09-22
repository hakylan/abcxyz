<?php 
/**
 * UserAddress
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserAddressBase.php';
use Flywheel\Redis\Client as RedisClient;
use Flywheel\Db\Type\DateTime;
class UserAddress extends \UserAddressBase {
    const CONFIG_REDIS = "user_address";
    /**
     * @var \Locations
     */
    protected $_province;

    /**
     * @var \Locations
     */
    protected $_district;

    /**
     * @param int $id
     * @return bool|null|UserAddress|static
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
     * @param $user_address_id
     * @return array
     */
    public static function retrieveFromRedis($user_address_id) {
        return RedisClient::getConnection(self::getTableName())->hGetAll(REDIS_USER_ADDRESS .$user_address_id);
    }

    /**
     * @param \Order $obj
     * @return bool
     */
    public static function pushToRedis($obj) {
        if (!$obj || !($obj instanceof \UserAddress)) {
            return false;
        }

        $data = $obj->toArray();
        foreach ($data as $k => $v) {
            if ($v instanceof DateTime) {
                $data[$k] = $v->toString();
            }
        }
        $timeout = 15552000; //6 months
//        print_r($data);

        $redis = RedisClient::getConnection(\UserAddress::CONFIG_REDIS);
        $redis->hMset(REDIS_USER_ADDRESS .$obj->getId(), $data);
        $redis->expire(REDIS_USER_ADDRESS .$obj->getId(), $timeout);
    }

    /**
     * @return \Flywheel\Event\Event|void
     */
    protected function _afterSave() {
//        echo 'after save';
        parent::_afterSave();
        $this->reload();
        self::pushToRedis($this);
    }

    /**
     * @param $shipping_mobile
     * @return UserAddress[]
     */
    public static function searchByPhone($shipping_mobile) {
        return (array) self::select()->where('`reciver_phone` LIKE :phone')
            ->setParameter(':phone', "%{$shipping_mobile}%", \PDO::PARAM_STR)
            ->execute();
    }

    /**
     * @return string
     */
    public function getProvinceLabel(){
        $province = $this->getProvince();
        if($province instanceof \Locations){
            return $province->getLabel();
        }
        return "";
    }

    /**
     * @return string
     */
    public function getDistrictLabel(){
        $district = $this->getDistrict();
        if($district instanceof \Locations){
            return $district->getLabel();
        }
        return "";
    }

    /**
     * get address's province
     * @return \Locations
     */
    public function getProvince() {
        if (!$this->_province) {
            $this->_province = \Locations::retrieveById($this->getProvinceId());
        }

        return $this->_province;
    }

    /**
     * get address's district
     * @return \Locations
     */
    public function getDistrict() {
        if (!$this->_district) {
            $this->_district = \Locations::retrieveById($this->getDistrictId());
        }

        return $this->_district;
    }

    /**
     * manipulate object to array included province and district
     * @return array
     */
    public function toFullArray() {
        $data = $this->toArray();
        if ($p = $this->getProvince()) {
            $data['province'] = $p->toArray();
        }

        if ($d = $this->getDistrict()) {
            $data['district'] = $d->toArray();
        }

        return $data;
    }

    /**
     * hydrate object to JSON string included province and district
     * @return string
     */
    public function toFullJson() {
        return json_encode($this->toFullArray());
    }

    /**
     * @param \Users|int $user
     * @return \UserAddress[]
     */
    public static function getUserAddresses($user) {
        if ($user instanceof \Users) {
            $user = $user->getId();
        }

        return \UserAddress::select()
            ->where('`user_id` = :user_id AND is_delete = 0')
            ->orderBy('id', 'DESC')
            ->setParameter(':user_id', $user, \PDO::PARAM_INT)
            ->setMaxResults(5)
            ->setFirstResult(0)
            ->execute();
    }
}