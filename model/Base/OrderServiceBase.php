<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderService
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property integer $service_id service_id type : int(11)
 * @property string $service_code service_code type : varchar(100) max_length : 100
 * @property number $money money type : double
 * @property number $discounted_money discounted_money type : double
 * @property number $before_payment_money before_payment_money type : float
 * @property string $status status type : varchar(50) max_length : 50
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderService[] findById(integer $id) find objects in database by id
 * @method static \OrderService findOneById(integer $id) find object in database by id
 * @method static \OrderService retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderService[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderService findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderService retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setServiceId(integer $service_id) set service_id value
 * @method integer getServiceId() get service_id value
 * @method static \OrderService[] findByServiceId(integer $service_id) find objects in database by service_id
 * @method static \OrderService findOneByServiceId(integer $service_id) find object in database by service_id
 * @method static \OrderService retrieveByServiceId(integer $service_id) retrieve object from poll by service_id, get it from db if not exist in poll

 * @method void setServiceCode(string $service_code) set service_code value
 * @method string getServiceCode() get service_code value
 * @method static \OrderService[] findByServiceCode(string $service_code) find objects in database by service_code
 * @method static \OrderService findOneByServiceCode(string $service_code) find object in database by service_code
 * @method static \OrderService retrieveByServiceCode(string $service_code) retrieve object from poll by service_code, get it from db if not exist in poll

 * @method void setMoney(number $money) set money value
 * @method number getMoney() get money value
 * @method static \OrderService[] findByMoney(number $money) find objects in database by money
 * @method static \OrderService findOneByMoney(number $money) find object in database by money
 * @method static \OrderService retrieveByMoney(number $money) retrieve object from poll by money, get it from db if not exist in poll

 * @method void setDiscountedMoney(number $discounted_money) set discounted_money value
 * @method number getDiscountedMoney() get discounted_money value
 * @method static \OrderService[] findByDiscountedMoney(number $discounted_money) find objects in database by discounted_money
 * @method static \OrderService findOneByDiscountedMoney(number $discounted_money) find object in database by discounted_money
 * @method static \OrderService retrieveByDiscountedMoney(number $discounted_money) retrieve object from poll by discounted_money, get it from db if not exist in poll

 * @method void setBeforePaymentMoney(number $before_payment_money) set before_payment_money value
 * @method number getBeforePaymentMoney() get before_payment_money value
 * @method static \OrderService[] findByBeforePaymentMoney(number $before_payment_money) find objects in database by before_payment_money
 * @method static \OrderService findOneByBeforePaymentMoney(number $before_payment_money) find object in database by before_payment_money
 * @method static \OrderService retrieveByBeforePaymentMoney(number $before_payment_money) retrieve object from poll by before_payment_money, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \OrderService[] findByStatus(string $status) find objects in database by status
 * @method static \OrderService findOneByStatus(string $status) find object in database by status
 * @method static \OrderService retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderService[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderService findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderService retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderServiceBase extends ActiveRecord {
    protected static $_tableName = 'order_service';
    protected static $_phpName = 'OrderService';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_service';
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
        'service_id' => array('name' => 'service_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'service_code' => array('name' => 'service_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'money' => array('name' => 'money',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'discounted_money' => array('name' => 'discounted_money',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'before_payment_money' => array('name' => 'before_payment_money',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','order_id','service_id','service_code','money','discounted_money','before_payment_money','status','created_time');

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