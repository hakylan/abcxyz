<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * HallOfFame
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $lock_by lock_by type : int(11)
 * @property string $locked_reason locked_reason type : text max_length : 
 * @property integer $unlock_by unlock_by type : int(11)
 * @property string $unlock_note unlock_note type : text max_length : 
 * @property datetime $effect_time effect_time type : datetime
 * @property datetime $expire_time expire_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \HallOfFame[] findById(integer $id) find objects in database by id
 * @method static \HallOfFame findOneById(integer $id) find object in database by id
 * @method static \HallOfFame retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setLockBy(integer $lock_by) set lock_by value
 * @method integer getLockBy() get lock_by value
 * @method static \HallOfFame[] findByLockBy(integer $lock_by) find objects in database by lock_by
 * @method static \HallOfFame findOneByLockBy(integer $lock_by) find object in database by lock_by
 * @method static \HallOfFame retrieveByLockBy(integer $lock_by) retrieve object from poll by lock_by, get it from db if not exist in poll

 * @method void setLockedReason(string $locked_reason) set locked_reason value
 * @method string getLockedReason() get locked_reason value
 * @method static \HallOfFame[] findByLockedReason(string $locked_reason) find objects in database by locked_reason
 * @method static \HallOfFame findOneByLockedReason(string $locked_reason) find object in database by locked_reason
 * @method static \HallOfFame retrieveByLockedReason(string $locked_reason) retrieve object from poll by locked_reason, get it from db if not exist in poll

 * @method void setUnlockBy(integer $unlock_by) set unlock_by value
 * @method integer getUnlockBy() get unlock_by value
 * @method static \HallOfFame[] findByUnlockBy(integer $unlock_by) find objects in database by unlock_by
 * @method static \HallOfFame findOneByUnlockBy(integer $unlock_by) find object in database by unlock_by
 * @method static \HallOfFame retrieveByUnlockBy(integer $unlock_by) retrieve object from poll by unlock_by, get it from db if not exist in poll

 * @method void setUnlockNote(string $unlock_note) set unlock_note value
 * @method string getUnlockNote() get unlock_note value
 * @method static \HallOfFame[] findByUnlockNote(string $unlock_note) find objects in database by unlock_note
 * @method static \HallOfFame findOneByUnlockNote(string $unlock_note) find object in database by unlock_note
 * @method static \HallOfFame retrieveByUnlockNote(string $unlock_note) retrieve object from poll by unlock_note, get it from db if not exist in poll

 * @method void setEffectTime(\Flywheel\Db\Type\DateTime $effect_time) setEffectTime(string $effect_time) set effect_time value
 * @method \Flywheel\Db\Type\DateTime getEffectTime() get effect_time value
 * @method static \HallOfFame[] findByEffectTime(\Flywheel\Db\Type\DateTime $effect_time) findByEffectTime(string $effect_time) find objects in database by effect_time
 * @method static \HallOfFame findOneByEffectTime(\Flywheel\Db\Type\DateTime $effect_time) findOneByEffectTime(string $effect_time) find object in database by effect_time
 * @method static \HallOfFame retrieveByEffectTime(\Flywheel\Db\Type\DateTime $effect_time) retrieveByEffectTime(string $effect_time) retrieve object from poll by effect_time, get it from db if not exist in poll

 * @method void setExpireTime(\Flywheel\Db\Type\DateTime $expire_time) setExpireTime(string $expire_time) set expire_time value
 * @method \Flywheel\Db\Type\DateTime getExpireTime() get expire_time value
 * @method static \HallOfFame[] findByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) findByExpireTime(string $expire_time) find objects in database by expire_time
 * @method static \HallOfFame findOneByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) findOneByExpireTime(string $expire_time) find object in database by expire_time
 * @method static \HallOfFame retrieveByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) retrieveByExpireTime(string $expire_time) retrieve object from poll by expire_time, get it from db if not exist in poll


 */
abstract class HallOfFameBase extends ActiveRecord {
    protected static $_tableName = 'hall_of_fame';
    protected static $_phpName = 'HallOfFame';
    protected static $_pk = 'id';
    protected static $_alias = 'h';
    protected static $_dbConnectName = 'hall_of_fame';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'lock_by' => array('name' => 'lock_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'locked_reason' => array('name' => 'locked_reason',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'unlock_by' => array('name' => 'unlock_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'unlock_note' => array('name' => 'unlock_note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'effect_time' => array('name' => 'effect_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'expire_time' => array('name' => 'expire_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','lock_by','locked_reason','unlock_by','unlock_note','effect_time','expire_time');

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