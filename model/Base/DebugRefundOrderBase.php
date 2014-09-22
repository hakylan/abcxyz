<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * DebugRefundOrder
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $user_id user_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property number $discount_money discount_money type : double
 * @property string $note note type : text max_length : 

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \DebugRefundOrder[] findById(integer $id) find objects in database by id
 * @method static \DebugRefundOrder findOneById(integer $id) find object in database by id
 * @method static \DebugRefundOrder retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \DebugRefundOrder[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \DebugRefundOrder findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \DebugRefundOrder retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \DebugRefundOrder[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \DebugRefundOrder findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \DebugRefundOrder retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setDiscountMoney(number $discount_money) set discount_money value
 * @method number getDiscountMoney() get discount_money value
 * @method static \DebugRefundOrder[] findByDiscountMoney(number $discount_money) find objects in database by discount_money
 * @method static \DebugRefundOrder findOneByDiscountMoney(number $discount_money) find object in database by discount_money
 * @method static \DebugRefundOrder retrieveByDiscountMoney(number $discount_money) retrieve object from poll by discount_money, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \DebugRefundOrder[] findByNote(string $note) find objects in database by note
 * @method static \DebugRefundOrder findOneByNote(string $note) find object in database by note
 * @method static \DebugRefundOrder retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll


 */
abstract class DebugRefundOrderBase extends ActiveRecord {
    protected static $_tableName = 'debug_refund_order';
    protected static $_phpName = 'DebugRefundOrder';
    protected static $_pk = 'id';
    protected static $_alias = 'd';
    protected static $_dbConnectName = 'debug_refund_order';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
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
        'discount_money' => array('name' => 'discount_money',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','order_id','discount_money','note');

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