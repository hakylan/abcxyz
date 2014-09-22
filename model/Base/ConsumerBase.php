<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Consumer
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $name name type : varchar(255) max_length : 255
 * @property string $description description type : varchar(255) max_length : 255
 * @property string $status status type : enum('ACTIVE','INACTIVE') max_length : 8
 * @property string $consumer_key consumer_key type : char(16) max_length : 16
 * @property string $consumer_secret consumer_secret type : char(32) max_length : 32
 * @property string $allowed_ips allowed_ips type : varchar(255) max_length : 255
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modified_time modified_time type : datetime
 * @property datetime $active_time active_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Consumer[] findById(integer $id) find objects in database by id
 * @method static \Consumer findOneById(integer $id) find object in database by id
 * @method static \Consumer retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setName(string $name) set name value
 * @method string getName() get name value
 * @method static \Consumer[] findByName(string $name) find objects in database by name
 * @method static \Consumer findOneByName(string $name) find object in database by name
 * @method static \Consumer retrieveByName(string $name) retrieve object from poll by name, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \Consumer[] findByDescription(string $description) find objects in database by description
 * @method static \Consumer findOneByDescription(string $description) find object in database by description
 * @method static \Consumer retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Consumer[] findByStatus(string $status) find objects in database by status
 * @method static \Consumer findOneByStatus(string $status) find object in database by status
 * @method static \Consumer retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setConsumerKey(string $consumer_key) set consumer_key value
 * @method string getConsumerKey() get consumer_key value
 * @method static \Consumer[] findByConsumerKey(string $consumer_key) find objects in database by consumer_key
 * @method static \Consumer findOneByConsumerKey(string $consumer_key) find object in database by consumer_key
 * @method static \Consumer retrieveByConsumerKey(string $consumer_key) retrieve object from poll by consumer_key, get it from db if not exist in poll

 * @method void setConsumerSecret(string $consumer_secret) set consumer_secret value
 * @method string getConsumerSecret() get consumer_secret value
 * @method static \Consumer[] findByConsumerSecret(string $consumer_secret) find objects in database by consumer_secret
 * @method static \Consumer findOneByConsumerSecret(string $consumer_secret) find object in database by consumer_secret
 * @method static \Consumer retrieveByConsumerSecret(string $consumer_secret) retrieve object from poll by consumer_secret, get it from db if not exist in poll

 * @method void setAllowedIps(string $allowed_ips) set allowed_ips value
 * @method string getAllowedIps() get allowed_ips value
 * @method static \Consumer[] findByAllowedIps(string $allowed_ips) find objects in database by allowed_ips
 * @method static \Consumer findOneByAllowedIps(string $allowed_ips) find object in database by allowed_ips
 * @method static \Consumer retrieveByAllowedIps(string $allowed_ips) retrieve object from poll by allowed_ips, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \Consumer[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \Consumer findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \Consumer retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \Consumer[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \Consumer findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \Consumer retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll

 * @method void setActiveTime(\Flywheel\Db\Type\DateTime $active_time) setActiveTime(string $active_time) set active_time value
 * @method \Flywheel\Db\Type\DateTime getActiveTime() get active_time value
 * @method static \Consumer[] findByActiveTime(\Flywheel\Db\Type\DateTime $active_time) findByActiveTime(string $active_time) find objects in database by active_time
 * @method static \Consumer findOneByActiveTime(\Flywheel\Db\Type\DateTime $active_time) findOneByActiveTime(string $active_time) find object in database by active_time
 * @method static \Consumer retrieveByActiveTime(\Flywheel\Db\Type\DateTime $active_time) retrieveByActiveTime(string $active_time) retrieve object from poll by active_time, get it from db if not exist in poll


 */
abstract class ConsumerBase extends ActiveRecord {
    protected static $_tableName = 'consumer';
    protected static $_phpName = 'Consumer';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'consumer';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'name' => array('name' => 'name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'description' => array('name' => 'description',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'status' => array('name' => 'status',
                'default' => 'ACTIVE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'INACTIVE\')',
                'length' => 8),
        'consumer_key' => array('name' => 'consumer_key',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(16)',
                'length' => 16),
        'consumer_secret' => array('name' => 'consumer_secret',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(32)',
                'length' => 32),
        'allowed_ips' => array('name' => 'allowed_ips',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
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
        'active_time' => array('name' => 'active_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|INACTIVE',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
        'consumer_key' => array(
            array('name' => 'Unique',
                'message'=> 'consumer key\'s was used'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|INACTIVE',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
        'consumer_key' => array(
            array('name' => 'Unique',
                'message'=> 'consumer key\'s was used'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','name','description','status','consumer_key','consumer_secret','allowed_ips','created_time','modified_time','active_time');

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