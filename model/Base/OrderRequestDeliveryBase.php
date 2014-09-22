<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderRequestDelivery
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $order_info order_info type : varchar(500) max_length : 500
 * @property integer $user_id user_id type : int(11)
 * @property integer $address_id address_id type : int(11)
 * @property number $account_balance account_balance type : double
 * @property number $total_amount total_amount type : double
 * @property number $missing_amount missing_amount type : double
 * @property number $cod cod type : double
 * @property datetime $created_time created_time type : datetime
 * @property integer $status status type : smallint(4)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderRequestDelivery[] findById(integer $id) find objects in database by id
 * @method static \OrderRequestDelivery findOneById(integer $id) find object in database by id
 * @method static \OrderRequestDelivery retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderInfo(string $order_info) set order_info value
 * @method string getOrderInfo() get order_info value
 * @method static \OrderRequestDelivery[] findByOrderInfo(string $order_info) find objects in database by order_info
 * @method static \OrderRequestDelivery findOneByOrderInfo(string $order_info) find object in database by order_info
 * @method static \OrderRequestDelivery retrieveByOrderInfo(string $order_info) retrieve object from poll by order_info, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \OrderRequestDelivery[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \OrderRequestDelivery findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \OrderRequestDelivery retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setAddressId(integer $address_id) set address_id value
 * @method integer getAddressId() get address_id value
 * @method static \OrderRequestDelivery[] findByAddressId(integer $address_id) find objects in database by address_id
 * @method static \OrderRequestDelivery findOneByAddressId(integer $address_id) find object in database by address_id
 * @method static \OrderRequestDelivery retrieveByAddressId(integer $address_id) retrieve object from poll by address_id, get it from db if not exist in poll

 * @method void setAccountBalance(number $account_balance) set account_balance value
 * @method number getAccountBalance() get account_balance value
 * @method static \OrderRequestDelivery[] findByAccountBalance(number $account_balance) find objects in database by account_balance
 * @method static \OrderRequestDelivery findOneByAccountBalance(number $account_balance) find object in database by account_balance
 * @method static \OrderRequestDelivery retrieveByAccountBalance(number $account_balance) retrieve object from poll by account_balance, get it from db if not exist in poll

 * @method void setTotalAmount(number $total_amount) set total_amount value
 * @method number getTotalAmount() get total_amount value
 * @method static \OrderRequestDelivery[] findByTotalAmount(number $total_amount) find objects in database by total_amount
 * @method static \OrderRequestDelivery findOneByTotalAmount(number $total_amount) find object in database by total_amount
 * @method static \OrderRequestDelivery retrieveByTotalAmount(number $total_amount) retrieve object from poll by total_amount, get it from db if not exist in poll

 * @method void setMissingAmount(number $missing_amount) set missing_amount value
 * @method number getMissingAmount() get missing_amount value
 * @method static \OrderRequestDelivery[] findByMissingAmount(number $missing_amount) find objects in database by missing_amount
 * @method static \OrderRequestDelivery findOneByMissingAmount(number $missing_amount) find object in database by missing_amount
 * @method static \OrderRequestDelivery retrieveByMissingAmount(number $missing_amount) retrieve object from poll by missing_amount, get it from db if not exist in poll

 * @method void setCod(number $cod) set cod value
 * @method number getCod() get cod value
 * @method static \OrderRequestDelivery[] findByCod(number $cod) find objects in database by cod
 * @method static \OrderRequestDelivery findOneByCod(number $cod) find object in database by cod
 * @method static \OrderRequestDelivery retrieveByCod(number $cod) retrieve object from poll by cod, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderRequestDelivery[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderRequestDelivery findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderRequestDelivery retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \OrderRequestDelivery[] findByStatus(integer $status) find objects in database by status
 * @method static \OrderRequestDelivery findOneByStatus(integer $status) find object in database by status
 * @method static \OrderRequestDelivery retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll


 */
abstract class OrderRequestDeliveryBase extends ActiveRecord {
    protected static $_tableName = 'order_request_delivery';
    protected static $_phpName = 'OrderRequestDelivery';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_request_delivery';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_info' => array('name' => 'order_info',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(500)',
                'length' => 500),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'address_id' => array('name' => 'address_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'account_balance' => array('name' => 'account_balance',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'total_amount' => array('name' => 'total_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'missing_amount' => array('name' => 'missing_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'cod' => array('name' => 'cod',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'smallint(4)',
                'length' => 2),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','order_info','user_id','address_id','account_balance','total_amount','missing_amount','cod','created_time','status');

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