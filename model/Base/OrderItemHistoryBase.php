<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderItemHistory
 * @version		$Id$
 * @package		Model

 * @property integer $id id type : int(11)
 * @property integer $order_item_id order_item_id type : int(11)
 * @property integer $staff_id staff_id type : int(11)
 * @property string $type type type : varchar(30) max_length : 30
 * @property number $from from type : float
 * @property number $to to type : float
 * @property string $message message type : varchar(255) max_length : 255
 * @property integer $status status type : int(11)
 * @property integer $created_time created_time type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderItemHistory[] findById(integer $id) find objects in database by id
 * @method static \OrderItemHistory findOneById(integer $id) find object in database by id
 * @method static \OrderItemHistory retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderItemId(integer $order_item_id) set order_item_id value
 * @method integer getOrderItemId() get order_item_id value
 * @method static \OrderItemHistory[] findByOrderItemId(integer $order_item_id) find objects in database by order_item_id
 * @method static \OrderItemHistory findOneByOrderItemId(integer $order_item_id) find object in database by order_item_id
 * @method static \OrderItemHistory retrieveByOrderItemId(integer $order_item_id) retrieve object from poll by order_item_id, get it from db if not exist in poll

 * @method void setStaffId(integer $staff_id) set staff_id value
 * @method integer getStaffId() get staff_id value
 * @method static \OrderItemHistory[] findByStaffId(integer $staff_id) find objects in database by staff_id
 * @method static \OrderItemHistory findOneByStaffId(integer $staff_id) find object in database by staff_id
 * @method static \OrderItemHistory retrieveByStaffId(integer $staff_id) retrieve object from poll by staff_id, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \OrderItemHistory[] findByType(string $type) find objects in database by type
 * @method static \OrderItemHistory findOneByType(string $type) find object in database by type
 * @method static \OrderItemHistory retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setFrom(number $from) set from value
 * @method number getFrom() get from value
 * @method static \OrderItemHistory[] findByFrom(number $from) find objects in database by from
 * @method static \OrderItemHistory findOneByFrom(number $from) find object in database by from
 * @method static \OrderItemHistory retrieveByFrom(number $from) retrieve object from poll by from, get it from db if not exist in poll

 * @method void setTo(number $to) set to value
 * @method number getTo() get to value
 * @method static \OrderItemHistory[] findByTo(number $to) find objects in database by to
 * @method static \OrderItemHistory findOneByTo(number $to) find object in database by to
 * @method static \OrderItemHistory retrieveByTo(number $to) retrieve object from poll by to, get it from db if not exist in poll

 * @method void setMessage(string $message) set message value
 * @method string getMessage() get message value
 * @method static \OrderItemHistory[] findByMessage(string $message) find objects in database by message
 * @method static \OrderItemHistory findOneByMessage(string $message) find object in database by message
 * @method static \OrderItemHistory retrieveByMessage(string $message) retrieve object from poll by message, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \OrderItemHistory[] findByStatus(integer $status) find objects in database by status
 * @method static \OrderItemHistory findOneByStatus(integer $status) find object in database by status
 * @method static \OrderItemHistory retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(integer $created_time) set created_time value
 * @method integer getCreatedTime() get created_time value
 * @method static \OrderItemHistory[] findByCreatedTime(integer $created_time) find objects in database by created_time
 * @method static \OrderItemHistory findOneByCreatedTime(integer $created_time) find object in database by created_time
 * @method static \OrderItemHistory retrieveByCreatedTime(integer $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderItemHistoryBase extends ActiveRecord {
    protected static $_tableName = 'order_item_history';
    protected static $_phpName = 'OrderItemHistory';
    protected static $_pk = '';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_item_history';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_item_id' => array('name' => 'order_item_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'staff_id' => array('name' => 'staff_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(30)',
                'length' => 30),
        'from' => array('name' => 'from',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'to' => array('name' => 'to',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'message' => array('name' => 'message',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','order_item_id','staff_id','type','from','to','message','status','created_time');

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