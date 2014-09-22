<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderTracking
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property string $status status type : varchar(100) max_length : 100
 * @property string $action action type : varchar(255) max_length : 255
 * @property string $description description type : text max_length : 
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderTracking[] findById(integer $id) find objects in database by id
 * @method static \OrderTracking findOneById(integer $id) find object in database by id
 * @method static \OrderTracking retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \OrderTracking[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \OrderTracking findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \OrderTracking retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderTracking[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderTracking findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderTracking retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \OrderTracking[] findByStatus(string $status) find objects in database by status
 * @method static \OrderTracking findOneByStatus(string $status) find object in database by status
 * @method static \OrderTracking retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setAction(string $action) set action value
 * @method string getAction() get action value
 * @method static \OrderTracking[] findByAction(string $action) find objects in database by action
 * @method static \OrderTracking findOneByAction(string $action) find object in database by action
 * @method static \OrderTracking retrieveByAction(string $action) retrieve object from poll by action, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \OrderTracking[] findByDescription(string $description) find objects in database by description
 * @method static \OrderTracking findOneByDescription(string $description) find object in database by description
 * @method static \OrderTracking retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderTracking[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderTracking findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderTracking retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderTrackingBase extends ActiveRecord {
    protected static $_tableName = 'order_tracking';
    protected static $_phpName = 'OrderTracking';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_tracking';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'action' => array('name' => 'action',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'description' => array('name' => 'description',
                'not_null' => false,
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
    protected static $_cols = array('id','user_id','order_id','status','action','description','created_time');

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