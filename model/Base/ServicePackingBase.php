<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServicePacking
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property number $fee fee type : double(20,2)
 * @property datetime $created_time created_time type : datetime
 * @property string $status status type : enum('ACTIVE','DISABLED') max_length : 8

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServicePacking[] findById(integer $id) find objects in database by id
 * @method static \ServicePacking findOneById(integer $id) find object in database by id
 * @method static \ServicePacking retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setFee(number $fee) set fee value
 * @method number getFee() get fee value
 * @method static \ServicePacking[] findByFee(number $fee) find objects in database by fee
 * @method static \ServicePacking findOneByFee(number $fee) find object in database by fee
 * @method static \ServicePacking retrieveByFee(number $fee) retrieve object from poll by fee, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ServicePacking[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ServicePacking findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ServicePacking retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ServicePacking[] findByStatus(string $status) find objects in database by status
 * @method static \ServicePacking findOneByStatus(string $status) find object in database by status
 * @method static \ServicePacking retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll


 */
abstract class ServicePackingBase extends ActiveRecord {
    protected static $_tableName = 'service_packing';
    protected static $_phpName = 'ServicePacking';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_packing';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'fee' => array('name' => 'fee',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'status' => array('name' => 'status',
                'default' => 'ACTIVE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'DISABLED\')',
                'length' => 8),
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
    protected static $_cols = array('id','fee','created_time','status');

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