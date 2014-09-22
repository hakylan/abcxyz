<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserOrderFilter
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $uid uid type : int(11)
 * @property string $order_code order_code type : varchar(50) max_length : 50
 * @property datetime $init_time init_time type : datetime
 * @property datetime $finish_time finish_time type : datetime
 * @property datetime $reject_time reject_time type : datetime
 * @property string $reject_comment reject_comment type : varchar(500) max_length : 500
 * @property integer $status status type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserOrderFilter[] findById(integer $id) find objects in database by id
 * @method static \UserOrderFilter findOneById(integer $id) find object in database by id
 * @method static \UserOrderFilter retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUid(integer $uid) set uid value
 * @method integer getUid() get uid value
 * @method static \UserOrderFilter[] findByUid(integer $uid) find objects in database by uid
 * @method static \UserOrderFilter findOneByUid(integer $uid) find object in database by uid
 * @method static \UserOrderFilter retrieveByUid(integer $uid) retrieve object from poll by uid, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \UserOrderFilter[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \UserOrderFilter findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \UserOrderFilter retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setInitTime(\Flywheel\Db\Type\DateTime $init_time) setInitTime(string $init_time) set init_time value
 * @method \Flywheel\Db\Type\DateTime getInitTime() get init_time value
 * @method static \UserOrderFilter[] findByInitTime(\Flywheel\Db\Type\DateTime $init_time) findByInitTime(string $init_time) find objects in database by init_time
 * @method static \UserOrderFilter findOneByInitTime(\Flywheel\Db\Type\DateTime $init_time) findOneByInitTime(string $init_time) find object in database by init_time
 * @method static \UserOrderFilter retrieveByInitTime(\Flywheel\Db\Type\DateTime $init_time) retrieveByInitTime(string $init_time) retrieve object from poll by init_time, get it from db if not exist in poll

 * @method void setFinishTime(\Flywheel\Db\Type\DateTime $finish_time) setFinishTime(string $finish_time) set finish_time value
 * @method \Flywheel\Db\Type\DateTime getFinishTime() get finish_time value
 * @method static \UserOrderFilter[] findByFinishTime(\Flywheel\Db\Type\DateTime $finish_time) findByFinishTime(string $finish_time) find objects in database by finish_time
 * @method static \UserOrderFilter findOneByFinishTime(\Flywheel\Db\Type\DateTime $finish_time) findOneByFinishTime(string $finish_time) find object in database by finish_time
 * @method static \UserOrderFilter retrieveByFinishTime(\Flywheel\Db\Type\DateTime $finish_time) retrieveByFinishTime(string $finish_time) retrieve object from poll by finish_time, get it from db if not exist in poll

 * @method void setRejectTime(\Flywheel\Db\Type\DateTime $reject_time) setRejectTime(string $reject_time) set reject_time value
 * @method \Flywheel\Db\Type\DateTime getRejectTime() get reject_time value
 * @method static \UserOrderFilter[] findByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) findByRejectTime(string $reject_time) find objects in database by reject_time
 * @method static \UserOrderFilter findOneByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) findOneByRejectTime(string $reject_time) find object in database by reject_time
 * @method static \UserOrderFilter retrieveByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) retrieveByRejectTime(string $reject_time) retrieve object from poll by reject_time, get it from db if not exist in poll

 * @method void setRejectComment(string $reject_comment) set reject_comment value
 * @method string getRejectComment() get reject_comment value
 * @method static \UserOrderFilter[] findByRejectComment(string $reject_comment) find objects in database by reject_comment
 * @method static \UserOrderFilter findOneByRejectComment(string $reject_comment) find object in database by reject_comment
 * @method static \UserOrderFilter retrieveByRejectComment(string $reject_comment) retrieve object from poll by reject_comment, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \UserOrderFilter[] findByStatus(integer $status) find objects in database by status
 * @method static \UserOrderFilter findOneByStatus(integer $status) find object in database by status
 * @method static \UserOrderFilter retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll


 */
abstract class UserOrderFilterBase extends ActiveRecord {
    protected static $_tableName = 'user_order_filter';
    protected static $_phpName = 'UserOrderFilter';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_order_filter';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'uid' => array('name' => 'uid',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'init_time' => array('name' => 'init_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'finish_time' => array('name' => 'finish_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'reject_time' => array('name' => 'reject_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'reject_comment' => array('name' => 'reject_comment',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(500)',
                'length' => 500),
        'status' => array('name' => 'status',
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
    protected static $_cols = array('id','uid','order_code','init_time','finish_time','reject_time','reject_comment','status');

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