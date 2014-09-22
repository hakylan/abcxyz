<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserMobiles
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property string $mobile mobile type : varchar(50) max_length : 50
 * @property string $coming_by coming_by type : varchar(50) max_length : 50
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserMobiles[] findById(integer $id) find objects in database by id
 * @method static \UserMobiles findOneById(integer $id) find object in database by id
 * @method static \UserMobiles retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \UserMobiles[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \UserMobiles findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \UserMobiles retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setMobile(string $mobile) set mobile value
 * @method string getMobile() get mobile value
 * @method static \UserMobiles[] findByMobile(string $mobile) find objects in database by mobile
 * @method static \UserMobiles findOneByMobile(string $mobile) find object in database by mobile
 * @method static \UserMobiles retrieveByMobile(string $mobile) retrieve object from poll by mobile, get it from db if not exist in poll

 * @method void setComingBy(string $coming_by) set coming_by value
 * @method string getComingBy() get coming_by value
 * @method static \UserMobiles[] findByComingBy(string $coming_by) find objects in database by coming_by
 * @method static \UserMobiles findOneByComingBy(string $coming_by) find object in database by coming_by
 * @method static \UserMobiles retrieveByComingBy(string $coming_by) retrieve object from poll by coming_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \UserMobiles[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \UserMobiles findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \UserMobiles retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class UserMobilesBase extends ActiveRecord {
    protected static $_tableName = 'user_mobiles';
    protected static $_phpName = 'UserMobiles';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_mobiles';
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
        'mobile' => array('name' => 'mobile',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'coming_by' => array('name' => 'coming_by',
                'default' => 'SMS_GATEWAY',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
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
    protected static $_cols = array('id','user_id','mobile','coming_by','created_time');

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