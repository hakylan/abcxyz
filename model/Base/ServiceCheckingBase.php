<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServiceChecking
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $begin begin type : int(11)
 * @property integer $end end type : int(11)
 * @property number $normal_item normal_item type : double(20,2)
 * @property number $accessory_item accessory_item type : double(20,2)
 * @property string $status status type : enum('ACTIVE','DISABLED') max_length : 8
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServiceChecking[] findById(integer $id) find objects in database by id
 * @method static \ServiceChecking findOneById(integer $id) find object in database by id
 * @method static \ServiceChecking retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setBegin(integer $begin) set begin value
 * @method integer getBegin() get begin value
 * @method static \ServiceChecking[] findByBegin(integer $begin) find objects in database by begin
 * @method static \ServiceChecking findOneByBegin(integer $begin) find object in database by begin
 * @method static \ServiceChecking retrieveByBegin(integer $begin) retrieve object from poll by begin, get it from db if not exist in poll

 * @method void setEnd(integer $end) set end value
 * @method integer getEnd() get end value
 * @method static \ServiceChecking[] findByEnd(integer $end) find objects in database by end
 * @method static \ServiceChecking findOneByEnd(integer $end) find object in database by end
 * @method static \ServiceChecking retrieveByEnd(integer $end) retrieve object from poll by end, get it from db if not exist in poll

 * @method void setNormalItem(number $normal_item) set normal_item value
 * @method number getNormalItem() get normal_item value
 * @method static \ServiceChecking[] findByNormalItem(number $normal_item) find objects in database by normal_item
 * @method static \ServiceChecking findOneByNormalItem(number $normal_item) find object in database by normal_item
 * @method static \ServiceChecking retrieveByNormalItem(number $normal_item) retrieve object from poll by normal_item, get it from db if not exist in poll

 * @method void setAccessoryItem(number $accessory_item) set accessory_item value
 * @method number getAccessoryItem() get accessory_item value
 * @method static \ServiceChecking[] findByAccessoryItem(number $accessory_item) find objects in database by accessory_item
 * @method static \ServiceChecking findOneByAccessoryItem(number $accessory_item) find object in database by accessory_item
 * @method static \ServiceChecking retrieveByAccessoryItem(number $accessory_item) retrieve object from poll by accessory_item, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ServiceChecking[] findByStatus(string $status) find objects in database by status
 * @method static \ServiceChecking findOneByStatus(string $status) find object in database by status
 * @method static \ServiceChecking retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ServiceChecking[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ServiceChecking findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ServiceChecking retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ServiceCheckingBase extends ActiveRecord {
    protected static $_tableName = 'service_checking';
    protected static $_phpName = 'ServiceChecking';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_checking';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'begin' => array('name' => 'begin',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'end' => array('name' => 'end',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'normal_item' => array('name' => 'normal_item',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'accessory_item' => array('name' => 'accessory_item',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
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
    protected static $_cols = array('id','begin','end','normal_item','accessory_item','status','created_time');

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