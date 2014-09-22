<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * EmailActivities
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $code code type : char(32) max_length : 32
 * @property string $email email type : varchar(255) max_length : 255
 * @property string $activity activity type : enum('RESET_PASSWORD','VERIFY_EMAIL') max_length : 14
 * @property string $params params type : text max_length : 
 * @property integer $finish finish type : tinyint(1)
 * @property datetime $created_time created_time type : datetime
 * @property datetime $expired_time expired_time type : datetime
 * @property datetime $finished_time finished_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \EmailActivities[] findById(integer $id) find objects in database by id
 * @method static \EmailActivities findOneById(integer $id) find object in database by id
 * @method static \EmailActivities retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCode(string $code) set code value
 * @method string getCode() get code value
 * @method static \EmailActivities[] findByCode(string $code) find objects in database by code
 * @method static \EmailActivities findOneByCode(string $code) find object in database by code
 * @method static \EmailActivities retrieveByCode(string $code) retrieve object from poll by code, get it from db if not exist in poll

 * @method void setEmail(string $email) set email value
 * @method string getEmail() get email value
 * @method static \EmailActivities[] findByEmail(string $email) find objects in database by email
 * @method static \EmailActivities findOneByEmail(string $email) find object in database by email
 * @method static \EmailActivities retrieveByEmail(string $email) retrieve object from poll by email, get it from db if not exist in poll

 * @method void setActivity(string $activity) set activity value
 * @method string getActivity() get activity value
 * @method static \EmailActivities[] findByActivity(string $activity) find objects in database by activity
 * @method static \EmailActivities findOneByActivity(string $activity) find object in database by activity
 * @method static \EmailActivities retrieveByActivity(string $activity) retrieve object from poll by activity, get it from db if not exist in poll

 * @method void setParams(string $params) set params value
 * @method string getParams() get params value
 * @method static \EmailActivities[] findByParams(string $params) find objects in database by params
 * @method static \EmailActivities findOneByParams(string $params) find object in database by params
 * @method static \EmailActivities retrieveByParams(string $params) retrieve object from poll by params, get it from db if not exist in poll

 * @method void setFinish(integer $finish) set finish value
 * @method integer getFinish() get finish value
 * @method static \EmailActivities[] findByFinish(integer $finish) find objects in database by finish
 * @method static \EmailActivities findOneByFinish(integer $finish) find object in database by finish
 * @method static \EmailActivities retrieveByFinish(integer $finish) retrieve object from poll by finish, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \EmailActivities[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \EmailActivities findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \EmailActivities retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setExpiredTime(\Flywheel\Db\Type\DateTime $expired_time) setExpiredTime(string $expired_time) set expired_time value
 * @method \Flywheel\Db\Type\DateTime getExpiredTime() get expired_time value
 * @method static \EmailActivities[] findByExpiredTime(\Flywheel\Db\Type\DateTime $expired_time) findByExpiredTime(string $expired_time) find objects in database by expired_time
 * @method static \EmailActivities findOneByExpiredTime(\Flywheel\Db\Type\DateTime $expired_time) findOneByExpiredTime(string $expired_time) find object in database by expired_time
 * @method static \EmailActivities retrieveByExpiredTime(\Flywheel\Db\Type\DateTime $expired_time) retrieveByExpiredTime(string $expired_time) retrieve object from poll by expired_time, get it from db if not exist in poll

 * @method void setFinishedTime(\Flywheel\Db\Type\DateTime $finished_time) setFinishedTime(string $finished_time) set finished_time value
 * @method \Flywheel\Db\Type\DateTime getFinishedTime() get finished_time value
 * @method static \EmailActivities[] findByFinishedTime(\Flywheel\Db\Type\DateTime $finished_time) findByFinishedTime(string $finished_time) find objects in database by finished_time
 * @method static \EmailActivities findOneByFinishedTime(\Flywheel\Db\Type\DateTime $finished_time) findOneByFinishedTime(string $finished_time) find object in database by finished_time
 * @method static \EmailActivities retrieveByFinishedTime(\Flywheel\Db\Type\DateTime $finished_time) retrieveByFinishedTime(string $finished_time) retrieve object from poll by finished_time, get it from db if not exist in poll


 */
abstract class EmailActivitiesBase extends ActiveRecord {
    protected static $_tableName = 'email_activities';
    protected static $_phpName = 'EmailActivities';
    protected static $_pk = 'id';
    protected static $_alias = 'e';
    protected static $_dbConnectName = 'email_activities';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'code' => array('name' => 'code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(32)',
                'length' => 32),
        'email' => array('name' => 'email',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'activity' => array('name' => 'activity',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'enum(\'RESET_PASSWORD\',\'VERIFY_EMAIL\')',
                'length' => 14),
        'params' => array('name' => 'params',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'finish' => array('name' => 'finish',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'expired_time' => array('name' => 'expired_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'finished_time' => array('name' => 'finished_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'activity' => array(
            array('name' => 'ValidValues',
                'value' => 'RESET_PASSWORD|VERIFY_EMAIL',
                'message'=> 'activity\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'activity' => array(
            array('name' => 'ValidValues',
                'value' => 'RESET_PASSWORD|VERIFY_EMAIL',
                'message'=> 'activity\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','code','email','activity','params','finish','created_time','expired_time','finished_time');

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