<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * RefundLog
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property number $refund_amount refund_amount type : float
 * @property string $note note type : varchar(255) max_length : 255
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \RefundLog[] findById(integer $id) find objects in database by id
 * @method static \RefundLog findOneById(integer $id) find object in database by id
 * @method static \RefundLog retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \RefundLog[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \RefundLog findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \RefundLog retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \RefundLog[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \RefundLog findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \RefundLog retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setRefundAmount(number $refund_amount) set refund_amount value
 * @method number getRefundAmount() get refund_amount value
 * @method static \RefundLog[] findByRefundAmount(number $refund_amount) find objects in database by refund_amount
 * @method static \RefundLog findOneByRefundAmount(number $refund_amount) find object in database by refund_amount
 * @method static \RefundLog retrieveByRefundAmount(number $refund_amount) retrieve object from poll by refund_amount, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \RefundLog[] findByNote(string $note) find objects in database by note
 * @method static \RefundLog findOneByNote(string $note) find object in database by note
 * @method static \RefundLog retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \RefundLog[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \RefundLog findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \RefundLog retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class RefundLogBase extends ActiveRecord {
    protected static $_tableName = 'refund_log';
    protected static $_phpName = 'RefundLog';
    protected static $_pk = 'id';
    protected static $_alias = 'r';
    protected static $_dbConnectName = 'refund_log';
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
        'refund_amount' => array('name' => 'refund_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'create_time' => array('name' => 'create_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','order_id','refund_amount','note','create_time');

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