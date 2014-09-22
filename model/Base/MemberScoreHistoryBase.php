<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * MemberScoreHistory
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $user_id user_id type : int(11)
 * @property integer $object_id object_id type : int(11)
 * @property string $object_type object_type type : varchar(100) max_length : 100
 * @property integer $level_id level_id type : int(11)
 * @property number $point point type : double
 * @property number $total_point total_point type : double
 * @property string $note note type : text max_length : 
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \MemberScoreHistory[] findById(integer $id) find objects in database by id
 * @method static \MemberScoreHistory findOneById(integer $id) find object in database by id
 * @method static \MemberScoreHistory retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \MemberScoreHistory[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \MemberScoreHistory findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \MemberScoreHistory retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setObjectId(integer $object_id) set object_id value
 * @method integer getObjectId() get object_id value
 * @method static \MemberScoreHistory[] findByObjectId(integer $object_id) find objects in database by object_id
 * @method static \MemberScoreHistory findOneByObjectId(integer $object_id) find object in database by object_id
 * @method static \MemberScoreHistory retrieveByObjectId(integer $object_id) retrieve object from poll by object_id, get it from db if not exist in poll

 * @method void setObjectType(string $object_type) set object_type value
 * @method string getObjectType() get object_type value
 * @method static \MemberScoreHistory[] findByObjectType(string $object_type) find objects in database by object_type
 * @method static \MemberScoreHistory findOneByObjectType(string $object_type) find object in database by object_type
 * @method static \MemberScoreHistory retrieveByObjectType(string $object_type) retrieve object from poll by object_type, get it from db if not exist in poll

 * @method void setLevelId(integer $level_id) set level_id value
 * @method integer getLevelId() get level_id value
 * @method static \MemberScoreHistory[] findByLevelId(integer $level_id) find objects in database by level_id
 * @method static \MemberScoreHistory findOneByLevelId(integer $level_id) find object in database by level_id
 * @method static \MemberScoreHistory retrieveByLevelId(integer $level_id) retrieve object from poll by level_id, get it from db if not exist in poll

 * @method void setPoint(number $point) set point value
 * @method number getPoint() get point value
 * @method static \MemberScoreHistory[] findByPoint(number $point) find objects in database by point
 * @method static \MemberScoreHistory findOneByPoint(number $point) find object in database by point
 * @method static \MemberScoreHistory retrieveByPoint(number $point) retrieve object from poll by point, get it from db if not exist in poll

 * @method void setTotalPoint(number $total_point) set total_point value
 * @method number getTotalPoint() get total_point value
 * @method static \MemberScoreHistory[] findByTotalPoint(number $total_point) find objects in database by total_point
 * @method static \MemberScoreHistory findOneByTotalPoint(number $total_point) find object in database by total_point
 * @method static \MemberScoreHistory retrieveByTotalPoint(number $total_point) retrieve object from poll by total_point, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \MemberScoreHistory[] findByNote(string $note) find objects in database by note
 * @method static \MemberScoreHistory findOneByNote(string $note) find object in database by note
 * @method static \MemberScoreHistory retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \MemberScoreHistory[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \MemberScoreHistory findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \MemberScoreHistory retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class MemberScoreHistoryBase extends ActiveRecord {
    protected static $_tableName = 'member_score_history';
    protected static $_phpName = 'MemberScoreHistory';
    protected static $_pk = 'id';
    protected static $_alias = 'm';
    protected static $_dbConnectName = 'member_score_history';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'object_id' => array('name' => 'object_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'object_type' => array('name' => 'object_type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'level_id' => array('name' => 'level_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'point' => array('name' => 'point',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'total_point' => array('name' => 'total_point',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'created_time' => array('name' => 'created_time',
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
    protected static $_cols = array('id','user_id','object_id','object_type','level_id','point','total_point','note','created_time');

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