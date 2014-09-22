<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserAddress
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $district_id district_id type : int(11)
 * @property integer $province_id province_id type : int(11)
 * @property string $detail detail type : varchar(255) max_length : 255
 * @property string $note note type : varchar(255) max_length : 255
 * @property string $reciver_name reciver_name type : varchar(100) max_length : 100
 * @property string $reciver_phone reciver_phone type : varchar(100) max_length : 100
 * @property integer $is_default is_default type : int(11)
 * @property integer $is_delete is_delete type : tinyint(4)
 * @property datetime $updated_time updated_time type : datetime
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserAddress[] findById(integer $id) find objects in database by id
 * @method static \UserAddress findOneById(integer $id) find object in database by id
 * @method static \UserAddress retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \UserAddress[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \UserAddress findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \UserAddress retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setDistrictId(integer $district_id) set district_id value
 * @method integer getDistrictId() get district_id value
 * @method static \UserAddress[] findByDistrictId(integer $district_id) find objects in database by district_id
 * @method static \UserAddress findOneByDistrictId(integer $district_id) find object in database by district_id
 * @method static \UserAddress retrieveByDistrictId(integer $district_id) retrieve object from poll by district_id, get it from db if not exist in poll

 * @method void setProvinceId(integer $province_id) set province_id value
 * @method integer getProvinceId() get province_id value
 * @method static \UserAddress[] findByProvinceId(integer $province_id) find objects in database by province_id
 * @method static \UserAddress findOneByProvinceId(integer $province_id) find object in database by province_id
 * @method static \UserAddress retrieveByProvinceId(integer $province_id) retrieve object from poll by province_id, get it from db if not exist in poll

 * @method void setDetail(string $detail) set detail value
 * @method string getDetail() get detail value
 * @method static \UserAddress[] findByDetail(string $detail) find objects in database by detail
 * @method static \UserAddress findOneByDetail(string $detail) find object in database by detail
 * @method static \UserAddress retrieveByDetail(string $detail) retrieve object from poll by detail, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \UserAddress[] findByNote(string $note) find objects in database by note
 * @method static \UserAddress findOneByNote(string $note) find object in database by note
 * @method static \UserAddress retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setReciverName(string $reciver_name) set reciver_name value
 * @method string getReciverName() get reciver_name value
 * @method static \UserAddress[] findByReciverName(string $reciver_name) find objects in database by reciver_name
 * @method static \UserAddress findOneByReciverName(string $reciver_name) find object in database by reciver_name
 * @method static \UserAddress retrieveByReciverName(string $reciver_name) retrieve object from poll by reciver_name, get it from db if not exist in poll

 * @method void setReciverPhone(string $reciver_phone) set reciver_phone value
 * @method string getReciverPhone() get reciver_phone value
 * @method static \UserAddress[] findByReciverPhone(string $reciver_phone) find objects in database by reciver_phone
 * @method static \UserAddress findOneByReciverPhone(string $reciver_phone) find object in database by reciver_phone
 * @method static \UserAddress retrieveByReciverPhone(string $reciver_phone) retrieve object from poll by reciver_phone, get it from db if not exist in poll

 * @method void setIsDefault(integer $is_default) set is_default value
 * @method integer getIsDefault() get is_default value
 * @method static \UserAddress[] findByIsDefault(integer $is_default) find objects in database by is_default
 * @method static \UserAddress findOneByIsDefault(integer $is_default) find object in database by is_default
 * @method static \UserAddress retrieveByIsDefault(integer $is_default) retrieve object from poll by is_default, get it from db if not exist in poll

 * @method void setIsDelete(integer $is_delete) set is_delete value
 * @method integer getIsDelete() get is_delete value
 * @method static \UserAddress[] findByIsDelete(integer $is_delete) find objects in database by is_delete
 * @method static \UserAddress findOneByIsDelete(integer $is_delete) find object in database by is_delete
 * @method static \UserAddress retrieveByIsDelete(integer $is_delete) retrieve object from poll by is_delete, get it from db if not exist in poll

 * @method void setUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) setUpdatedTime(string $updated_time) set updated_time value
 * @method \Flywheel\Db\Type\DateTime getUpdatedTime() get updated_time value
 * @method static \UserAddress[] findByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) findByUpdatedTime(string $updated_time) find objects in database by updated_time
 * @method static \UserAddress findOneByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) findOneByUpdatedTime(string $updated_time) find object in database by updated_time
 * @method static \UserAddress retrieveByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) retrieveByUpdatedTime(string $updated_time) retrieve object from poll by updated_time, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \UserAddress[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \UserAddress findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \UserAddress retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class UserAddressBase extends ActiveRecord {
    protected static $_tableName = 'user_address';
    protected static $_phpName = 'UserAddress';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_address';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'district_id' => array('name' => 'district_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'province_id' => array('name' => 'province_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'detail' => array('name' => 'detail',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'reciver_name' => array('name' => 'reciver_name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'reciver_phone' => array('name' => 'reciver_phone',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'is_default' => array('name' => 'is_default',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'is_delete' => array('name' => 'is_delete',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(4)',
                'length' => 1),
        'updated_time' => array('name' => 'updated_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','district_id','province_id','detail','note','reciver_name','reciver_phone','is_default','is_delete','updated_time','created_time');

    public function setTableDefinition() {
    }

    /**
     * save object model
     * @return boolean
     * @throws \Exception
     */
    public function save($validate = true) {
        $conn = Manager::getConnection(self::getDbConnectName());
        $conn->beginTransaction();
        try {
            $this->_beforeSave();
            $status = $this->saveToDb($validate);
            $this->_afterSave();
            $conn->commit();
            self::addInstanceToPool($this, $this->getPkValue());
            return $status;
        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * delete object model
     * @return boolean
     * @throws \Exception
     */
    public function delete() {
        $conn = Manager::getConnection(self::getDbConnectName());
        $conn->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->deleteFromDb();
            $this->_afterDelete();
            $conn->commit();
            self::removeInstanceFromPool($this->getPkValue());
            return true;
        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}