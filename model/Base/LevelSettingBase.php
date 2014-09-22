<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * LevelSetting
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $level level type : varchar(100) max_length : 100
 * @property string $level_name level_name type : varchar(255) max_length : 255
 * @property number $from_score from_score type : double
 * @property number $to_score to_score type : double
 * @property integer $active active type : tinyint(1)
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modified_time modified_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \LevelSetting[] findById(integer $id) find objects in database by id
 * @method static \LevelSetting findOneById(integer $id) find object in database by id
 * @method static \LevelSetting retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setLevel(string $level) set level value
 * @method string getLevel() get level value
 * @method static \LevelSetting[] findByLevel(string $level) find objects in database by level
 * @method static \LevelSetting findOneByLevel(string $level) find object in database by level
 * @method static \LevelSetting retrieveByLevel(string $level) retrieve object from poll by level, get it from db if not exist in poll

 * @method void setLevelName(string $level_name) set level_name value
 * @method string getLevelName() get level_name value
 * @method static \LevelSetting[] findByLevelName(string $level_name) find objects in database by level_name
 * @method static \LevelSetting findOneByLevelName(string $level_name) find object in database by level_name
 * @method static \LevelSetting retrieveByLevelName(string $level_name) retrieve object from poll by level_name, get it from db if not exist in poll

 * @method void setFromScore(number $from_score) set from_score value
 * @method number getFromScore() get from_score value
 * @method static \LevelSetting[] findByFromScore(number $from_score) find objects in database by from_score
 * @method static \LevelSetting findOneByFromScore(number $from_score) find object in database by from_score
 * @method static \LevelSetting retrieveByFromScore(number $from_score) retrieve object from poll by from_score, get it from db if not exist in poll

 * @method void setToScore(number $to_score) set to_score value
 * @method number getToScore() get to_score value
 * @method static \LevelSetting[] findByToScore(number $to_score) find objects in database by to_score
 * @method static \LevelSetting findOneByToScore(number $to_score) find object in database by to_score
 * @method static \LevelSetting retrieveByToScore(number $to_score) retrieve object from poll by to_score, get it from db if not exist in poll

 * @method void setActive(integer $active) set active value
 * @method integer getActive() get active value
 * @method static \LevelSetting[] findByActive(integer $active) find objects in database by active
 * @method static \LevelSetting findOneByActive(integer $active) find object in database by active
 * @method static \LevelSetting retrieveByActive(integer $active) retrieve object from poll by active, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \LevelSetting[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \LevelSetting findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \LevelSetting retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \LevelSetting[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \LevelSetting findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \LevelSetting retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll


 */
abstract class LevelSettingBase extends ActiveRecord {
    protected static $_tableName = 'level_setting';
    protected static $_phpName = 'LevelSetting';
    protected static $_pk = 'id';
    protected static $_alias = 'l';
    protected static $_dbConnectName = 'level_setting';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'level' => array('name' => 'level',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'level_name' => array('name' => 'level_name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'from_score' => array('name' => 'from_score',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'to_score' => array('name' => 'to_score',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'active' => array('name' => 'active',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'modified_time' => array('name' => 'modified_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','level','level_name','from_score','to_score','active','created_time','modified_time');

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