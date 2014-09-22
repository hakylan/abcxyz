<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * LogisticRoles
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $label label type : varchar(50) max_length : 50
 * @property string $state state type : enum('ACTIVE','INACTIVE') max_length : 8
 * @property string $description description type : varchar(250) max_length : 250
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modified_time modified_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \LogisticRoles[] findById(integer $id) find objects in database by id
 * @method static \LogisticRoles findOneById(integer $id) find object in database by id
 * @method static \LogisticRoles retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setLabel(string $label) set label value
 * @method string getLabel() get label value
 * @method static \LogisticRoles[] findByLabel(string $label) find objects in database by label
 * @method static \LogisticRoles findOneByLabel(string $label) find object in database by label
 * @method static \LogisticRoles retrieveByLabel(string $label) retrieve object from poll by label, get it from db if not exist in poll

 * @method void setState(string $state) set state value
 * @method string getState() get state value
 * @method static \LogisticRoles[] findByState(string $state) find objects in database by state
 * @method static \LogisticRoles findOneByState(string $state) find object in database by state
 * @method static \LogisticRoles retrieveByState(string $state) retrieve object from poll by state, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \LogisticRoles[] findByDescription(string $description) find objects in database by description
 * @method static \LogisticRoles findOneByDescription(string $description) find object in database by description
 * @method static \LogisticRoles retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \LogisticRoles[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \LogisticRoles findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \LogisticRoles retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \LogisticRoles[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \LogisticRoles findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \LogisticRoles retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll


 */
abstract class LogisticRolesBase extends ActiveRecord {
    protected static $_tableName = 'logistic_roles';
    protected static $_phpName = 'LogisticRoles';
    protected static $_pk = 'id';
    protected static $_alias = 'l';
    protected static $_dbConnectName = 'logistic_roles';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'label' => array('name' => 'label',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'state' => array('name' => 'state',
                'default' => 'ACTIVE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'INACTIVE\')',
                'length' => 8),
        'description' => array('name' => 'description',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(250)',
                'length' => 250),
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
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|INACTIVE',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|INACTIVE',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','label','state','description','created_time','modified_time');

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