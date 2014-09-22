<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Services
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $title title type : varchar(100) max_length : 100
 * @property string $code code type : varchar(50) max_length : 50
 * @property string $status status type : enum('ACTIVE','DISABLED') max_length : 8
 * @property number $fixed_fee fixed_fee type : double(20,2)
 * @property string $description description type : varchar(255) max_length : 255
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Services[] findById(integer $id) find objects in database by id
 * @method static \Services findOneById(integer $id) find object in database by id
 * @method static \Services retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \Services[] findByTitle(string $title) find objects in database by title
 * @method static \Services findOneByTitle(string $title) find object in database by title
 * @method static \Services retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setCode(string $code) set code value
 * @method string getCode() get code value
 * @method static \Services[] findByCode(string $code) find objects in database by code
 * @method static \Services findOneByCode(string $code) find object in database by code
 * @method static \Services retrieveByCode(string $code) retrieve object from poll by code, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Services[] findByStatus(string $status) find objects in database by status
 * @method static \Services findOneByStatus(string $status) find object in database by status
 * @method static \Services retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setFixedFee(number $fixed_fee) set fixed_fee value
 * @method number getFixedFee() get fixed_fee value
 * @method static \Services[] findByFixedFee(number $fixed_fee) find objects in database by fixed_fee
 * @method static \Services findOneByFixedFee(number $fixed_fee) find object in database by fixed_fee
 * @method static \Services retrieveByFixedFee(number $fixed_fee) retrieve object from poll by fixed_fee, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \Services[] findByDescription(string $description) find objects in database by description
 * @method static \Services findOneByDescription(string $description) find object in database by description
 * @method static \Services retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \Services[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \Services findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \Services retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ServicesBase extends ActiveRecord {
    protected static $_tableName = 'services';
    protected static $_phpName = 'Services';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'services';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'code' => array('name' => 'code',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'DISABLED\')',
                'length' => 8),
        'fixed_fee' => array('name' => 'fixed_fee',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'description' => array('name' => 'description',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLED',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLED',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','title','code','status','fixed_fee','description','created_time');

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