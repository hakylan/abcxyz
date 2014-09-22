<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServiceShipping
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $title title type : varchar(50) max_length : 50
 * @property string $type type type : varchar(20) max_length : 20
 * @property string $sub_type sub_type type : varchar(100) max_length : 100
 * @property string $vehicle vehicle type : varchar(100) max_length : 100
 * @property number $weight_from weight_from type : decimal(4,1)
 * @property number $weight_to weight_to type : decimal(4,1)
 * @property number $weight_fee weight_fee type : double(20,2)
 * @property string $description description type : text max_length : 
 * @property string $status status type : enum('ACTIVE','DISABLED') max_length : 8
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServiceShipping[] findById(integer $id) find objects in database by id
 * @method static \ServiceShipping findOneById(integer $id) find object in database by id
 * @method static \ServiceShipping retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \ServiceShipping[] findByTitle(string $title) find objects in database by title
 * @method static \ServiceShipping findOneByTitle(string $title) find object in database by title
 * @method static \ServiceShipping retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \ServiceShipping[] findByType(string $type) find objects in database by type
 * @method static \ServiceShipping findOneByType(string $type) find object in database by type
 * @method static \ServiceShipping retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setSubType(string $sub_type) set sub_type value
 * @method string getSubType() get sub_type value
 * @method static \ServiceShipping[] findBySubType(string $sub_type) find objects in database by sub_type
 * @method static \ServiceShipping findOneBySubType(string $sub_type) find object in database by sub_type
 * @method static \ServiceShipping retrieveBySubType(string $sub_type) retrieve object from poll by sub_type, get it from db if not exist in poll

 * @method void setVehicle(string $vehicle) set vehicle value
 * @method string getVehicle() get vehicle value
 * @method static \ServiceShipping[] findByVehicle(string $vehicle) find objects in database by vehicle
 * @method static \ServiceShipping findOneByVehicle(string $vehicle) find object in database by vehicle
 * @method static \ServiceShipping retrieveByVehicle(string $vehicle) retrieve object from poll by vehicle, get it from db if not exist in poll

 * @method void setWeightFrom(number $weight_from) set weight_from value
 * @method number getWeightFrom() get weight_from value
 * @method static \ServiceShipping[] findByWeightFrom(number $weight_from) find objects in database by weight_from
 * @method static \ServiceShipping findOneByWeightFrom(number $weight_from) find object in database by weight_from
 * @method static \ServiceShipping retrieveByWeightFrom(number $weight_from) retrieve object from poll by weight_from, get it from db if not exist in poll

 * @method void setWeightTo(number $weight_to) set weight_to value
 * @method number getWeightTo() get weight_to value
 * @method static \ServiceShipping[] findByWeightTo(number $weight_to) find objects in database by weight_to
 * @method static \ServiceShipping findOneByWeightTo(number $weight_to) find object in database by weight_to
 * @method static \ServiceShipping retrieveByWeightTo(number $weight_to) retrieve object from poll by weight_to, get it from db if not exist in poll

 * @method void setWeightFee(number $weight_fee) set weight_fee value
 * @method number getWeightFee() get weight_fee value
 * @method static \ServiceShipping[] findByWeightFee(number $weight_fee) find objects in database by weight_fee
 * @method static \ServiceShipping findOneByWeightFee(number $weight_fee) find object in database by weight_fee
 * @method static \ServiceShipping retrieveByWeightFee(number $weight_fee) retrieve object from poll by weight_fee, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \ServiceShipping[] findByDescription(string $description) find objects in database by description
 * @method static \ServiceShipping findOneByDescription(string $description) find object in database by description
 * @method static \ServiceShipping retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ServiceShipping[] findByStatus(string $status) find objects in database by status
 * @method static \ServiceShipping findOneByStatus(string $status) find object in database by status
 * @method static \ServiceShipping retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ServiceShipping[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ServiceShipping findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ServiceShipping retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ServiceShippingBase extends ActiveRecord {
    protected static $_tableName = 'service_shipping';
    protected static $_phpName = 'ServiceShipping';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_shipping';
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
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'sub_type' => array('name' => 'sub_type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'vehicle' => array('name' => 'vehicle',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'weight_from' => array('name' => 'weight_from',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(4,1)',
                'length' => 4),
        'weight_to' => array('name' => 'weight_to',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(4,1)',
                'length' => 4),
        'weight_fee' => array('name' => 'weight_fee',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'description' => array('name' => 'description',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'status' => array('name' => 'status',
                'default' => 'ACTIVE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'DISABLED\')',
                'length' => 8),
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
    protected static $_cols = array('id','title','type','sub_type','vehicle','weight_from','weight_to','weight_fee','description','status','created_time');

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