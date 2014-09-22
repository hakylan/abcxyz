<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderHistory
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property string $order_code order_code type : varchar(100) max_length : 100
 * @property string $status status type : varchar(50) max_length : 50
 * @property integer $quantity quantity type : int(11)
 * @property number $money money type : double(20,2)
 * @property string $current_warehouse current_warehouse type : varchar(255) max_length : 255
 * @property string $transport_status transport_status type : varchar(200) max_length : 200
 * @property string $detail detail type : text max_length : 
 * @property datetime $voucher_in_time voucher_in_time type : datetime
 * @property datetime $voucher_out_time voucher_out_time type : datetime
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderHistory[] findById(integer $id) find objects in database by id
 * @method static \OrderHistory findOneById(integer $id) find object in database by id
 * @method static \OrderHistory retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderHistory[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderHistory findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderHistory retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \OrderHistory[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \OrderHistory findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \OrderHistory retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \OrderHistory[] findByStatus(string $status) find objects in database by status
 * @method static \OrderHistory findOneByStatus(string $status) find object in database by status
 * @method static \OrderHistory retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setQuantity(integer $quantity) set quantity value
 * @method integer getQuantity() get quantity value
 * @method static \OrderHistory[] findByQuantity(integer $quantity) find objects in database by quantity
 * @method static \OrderHistory findOneByQuantity(integer $quantity) find object in database by quantity
 * @method static \OrderHistory retrieveByQuantity(integer $quantity) retrieve object from poll by quantity, get it from db if not exist in poll

 * @method void setMoney(number $money) set money value
 * @method number getMoney() get money value
 * @method static \OrderHistory[] findByMoney(number $money) find objects in database by money
 * @method static \OrderHistory findOneByMoney(number $money) find object in database by money
 * @method static \OrderHistory retrieveByMoney(number $money) retrieve object from poll by money, get it from db if not exist in poll

 * @method void setCurrentWarehouse(string $current_warehouse) set current_warehouse value
 * @method string getCurrentWarehouse() get current_warehouse value
 * @method static \OrderHistory[] findByCurrentWarehouse(string $current_warehouse) find objects in database by current_warehouse
 * @method static \OrderHistory findOneByCurrentWarehouse(string $current_warehouse) find object in database by current_warehouse
 * @method static \OrderHistory retrieveByCurrentWarehouse(string $current_warehouse) retrieve object from poll by current_warehouse, get it from db if not exist in poll

 * @method void setTransportStatus(string $transport_status) set transport_status value
 * @method string getTransportStatus() get transport_status value
 * @method static \OrderHistory[] findByTransportStatus(string $transport_status) find objects in database by transport_status
 * @method static \OrderHistory findOneByTransportStatus(string $transport_status) find object in database by transport_status
 * @method static \OrderHistory retrieveByTransportStatus(string $transport_status) retrieve object from poll by transport_status, get it from db if not exist in poll

 * @method void setDetail(string $detail) set detail value
 * @method string getDetail() get detail value
 * @method static \OrderHistory[] findByDetail(string $detail) find objects in database by detail
 * @method static \OrderHistory findOneByDetail(string $detail) find object in database by detail
 * @method static \OrderHistory retrieveByDetail(string $detail) retrieve object from poll by detail, get it from db if not exist in poll

 * @method void setVoucherInTime(\Flywheel\Db\Type\DateTime $voucher_in_time) setVoucherInTime(string $voucher_in_time) set voucher_in_time value
 * @method \Flywheel\Db\Type\DateTime getVoucherInTime() get voucher_in_time value
 * @method static \OrderHistory[] findByVoucherInTime(\Flywheel\Db\Type\DateTime $voucher_in_time) findByVoucherInTime(string $voucher_in_time) find objects in database by voucher_in_time
 * @method static \OrderHistory findOneByVoucherInTime(\Flywheel\Db\Type\DateTime $voucher_in_time) findOneByVoucherInTime(string $voucher_in_time) find object in database by voucher_in_time
 * @method static \OrderHistory retrieveByVoucherInTime(\Flywheel\Db\Type\DateTime $voucher_in_time) retrieveByVoucherInTime(string $voucher_in_time) retrieve object from poll by voucher_in_time, get it from db if not exist in poll

 * @method void setVoucherOutTime(\Flywheel\Db\Type\DateTime $voucher_out_time) setVoucherOutTime(string $voucher_out_time) set voucher_out_time value
 * @method \Flywheel\Db\Type\DateTime getVoucherOutTime() get voucher_out_time value
 * @method static \OrderHistory[] findByVoucherOutTime(\Flywheel\Db\Type\DateTime $voucher_out_time) findByVoucherOutTime(string $voucher_out_time) find objects in database by voucher_out_time
 * @method static \OrderHistory findOneByVoucherOutTime(\Flywheel\Db\Type\DateTime $voucher_out_time) findOneByVoucherOutTime(string $voucher_out_time) find object in database by voucher_out_time
 * @method static \OrderHistory retrieveByVoucherOutTime(\Flywheel\Db\Type\DateTime $voucher_out_time) retrieveByVoucherOutTime(string $voucher_out_time) retrieve object from poll by voucher_out_time, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderHistory[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderHistory findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderHistory retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderHistoryBase extends ActiveRecord {
    protected static $_tableName = 'order_history';
    protected static $_phpName = 'OrderHistory';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_history';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'quantity' => array('name' => 'quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'money' => array('name' => 'money',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'current_warehouse' => array('name' => 'current_warehouse',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'transport_status' => array('name' => 'transport_status',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(200)',
                'length' => 200),
        'detail' => array('name' => 'detail',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'voucher_in_time' => array('name' => 'voucher_in_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'voucher_out_time' => array('name' => 'voucher_out_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
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
    protected static $_cols = array('id','order_id','order_code','status','quantity','money','current_warehouse','transport_status','detail','voucher_in_time','voucher_out_time','created_time');

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