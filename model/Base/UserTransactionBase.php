<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserTransaction
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $user_id user_id type : int(11)
 * @property string $state state type : enum('PENDING','COMPLETED','CANCELED','REJECTED') max_length : 9
 * @property string $transaction_code transaction_code type : varchar(50) max_length : 50
 * @property string $transaction_type transaction_type type : enum('DEPOSIT','WITHDRAWAL','REFUND','ORDER_DEPOSIT','ORDER_PAYMENT','ADJUSTMENT','CHARGE_FEE') max_length : 13
 * @property number $amount amount type : double(20,2)
 * @property number $ending_balance ending_balance type : double(20,2)
 * @property string $object_id object_id type : varchar(255) max_length : 255
 * @property string $object_type object_type type : char(20) max_length : 20
 * @property string $transaction_detail transaction_detail type : varchar(255) max_length : 255
 * @property string $transaction_note transaction_note type : varchar(255) max_length : 255
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modified_time modified_time type : datetime
 * @property datetime $closed_time closed_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserTransaction[] findById(integer $id) find objects in database by id
 * @method static \UserTransaction findOneById(integer $id) find object in database by id
 * @method static \UserTransaction retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \UserTransaction[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \UserTransaction findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \UserTransaction retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setState(string $state) set state value
 * @method string getState() get state value
 * @method static \UserTransaction[] findByState(string $state) find objects in database by state
 * @method static \UserTransaction findOneByState(string $state) find object in database by state
 * @method static \UserTransaction retrieveByState(string $state) retrieve object from poll by state, get it from db if not exist in poll

 * @method void setTransactionCode(string $transaction_code) set transaction_code value
 * @method string getTransactionCode() get transaction_code value
 * @method static \UserTransaction[] findByTransactionCode(string $transaction_code) find objects in database by transaction_code
 * @method static \UserTransaction findOneByTransactionCode(string $transaction_code) find object in database by transaction_code
 * @method static \UserTransaction retrieveByTransactionCode(string $transaction_code) retrieve object from poll by transaction_code, get it from db if not exist in poll

 * @method void setTransactionType(string $transaction_type) set transaction_type value
 * @method string getTransactionType() get transaction_type value
 * @method static \UserTransaction[] findByTransactionType(string $transaction_type) find objects in database by transaction_type
 * @method static \UserTransaction findOneByTransactionType(string $transaction_type) find object in database by transaction_type
 * @method static \UserTransaction retrieveByTransactionType(string $transaction_type) retrieve object from poll by transaction_type, get it from db if not exist in poll

 * @method void setAmount(number $amount) set amount value
 * @method number getAmount() get amount value
 * @method static \UserTransaction[] findByAmount(number $amount) find objects in database by amount
 * @method static \UserTransaction findOneByAmount(number $amount) find object in database by amount
 * @method static \UserTransaction retrieveByAmount(number $amount) retrieve object from poll by amount, get it from db if not exist in poll

 * @method void setEndingBalance(number $ending_balance) set ending_balance value
 * @method number getEndingBalance() get ending_balance value
 * @method static \UserTransaction[] findByEndingBalance(number $ending_balance) find objects in database by ending_balance
 * @method static \UserTransaction findOneByEndingBalance(number $ending_balance) find object in database by ending_balance
 * @method static \UserTransaction retrieveByEndingBalance(number $ending_balance) retrieve object from poll by ending_balance, get it from db if not exist in poll

 * @method void setObjectId(string $object_id) set object_id value
 * @method string getObjectId() get object_id value
 * @method static \UserTransaction[] findByObjectId(string $object_id) find objects in database by object_id
 * @method static \UserTransaction findOneByObjectId(string $object_id) find object in database by object_id
 * @method static \UserTransaction retrieveByObjectId(string $object_id) retrieve object from poll by object_id, get it from db if not exist in poll

 * @method void setObjectType(string $object_type) set object_type value
 * @method string getObjectType() get object_type value
 * @method static \UserTransaction[] findByObjectType(string $object_type) find objects in database by object_type
 * @method static \UserTransaction findOneByObjectType(string $object_type) find object in database by object_type
 * @method static \UserTransaction retrieveByObjectType(string $object_type) retrieve object from poll by object_type, get it from db if not exist in poll

 * @method void setTransactionDetail(string $transaction_detail) set transaction_detail value
 * @method string getTransactionDetail() get transaction_detail value
 * @method static \UserTransaction[] findByTransactionDetail(string $transaction_detail) find objects in database by transaction_detail
 * @method static \UserTransaction findOneByTransactionDetail(string $transaction_detail) find object in database by transaction_detail
 * @method static \UserTransaction retrieveByTransactionDetail(string $transaction_detail) retrieve object from poll by transaction_detail, get it from db if not exist in poll

 * @method void setTransactionNote(string $transaction_note) set transaction_note value
 * @method string getTransactionNote() get transaction_note value
 * @method static \UserTransaction[] findByTransactionNote(string $transaction_note) find objects in database by transaction_note
 * @method static \UserTransaction findOneByTransactionNote(string $transaction_note) find object in database by transaction_note
 * @method static \UserTransaction retrieveByTransactionNote(string $transaction_note) retrieve object from poll by transaction_note, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \UserTransaction[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \UserTransaction findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \UserTransaction retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \UserTransaction[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \UserTransaction findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \UserTransaction retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll

 * @method void setClosedTime(\Flywheel\Db\Type\DateTime $closed_time) setClosedTime(string $closed_time) set closed_time value
 * @method \Flywheel\Db\Type\DateTime getClosedTime() get closed_time value
 * @method static \UserTransaction[] findByClosedTime(\Flywheel\Db\Type\DateTime $closed_time) findByClosedTime(string $closed_time) find objects in database by closed_time
 * @method static \UserTransaction findOneByClosedTime(\Flywheel\Db\Type\DateTime $closed_time) findOneByClosedTime(string $closed_time) find object in database by closed_time
 * @method static \UserTransaction retrieveByClosedTime(\Flywheel\Db\Type\DateTime $closed_time) retrieveByClosedTime(string $closed_time) retrieve object from poll by closed_time, get it from db if not exist in poll


 */
abstract class UserTransactionBase extends ActiveRecord {
    protected static $_tableName = 'user_transaction';
    protected static $_phpName = 'UserTransaction';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_transaction';
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
        'state' => array('name' => 'state',
                'default' => 'PENDING',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'PENDING\',\'COMPLETED\',\'CANCELED\',\'REJECTED\')',
                'length' => 9),
        'transaction_code' => array('name' => 'transaction_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'transaction_type' => array('name' => 'transaction_type',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'enum(\'DEPOSIT\',\'WITHDRAWAL\',\'REFUND\',\'ORDER_DEPOSIT\',\'ORDER_PAYMENT\',\'ADJUSTMENT\',\'CHARGE_FEE\')',
                'length' => 13),
        'amount' => array('name' => 'amount',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'ending_balance' => array('name' => 'ending_balance',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'object_id' => array('name' => 'object_id',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'object_type' => array('name' => 'object_type',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'char(20)',
                'length' => 20),
        'transaction_detail' => array('name' => 'transaction_detail',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'transaction_note' => array('name' => 'transaction_note',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'modified_time' => array('name' => 'modified_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'closed_time' => array('name' => 'closed_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'PENDING|COMPLETED|CANCELED|REJECTED',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
        'transaction_type' => array(
            array('name' => 'ValidValues',
                'value' => 'DEPOSIT|WITHDRAWAL|REFUND|ORDER_DEPOSIT|ORDER_PAYMENT|ADJUSTMENT|CHARGE_FEE',
                'message'=> 'transaction type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'PENDING|COMPLETED|CANCELED|REJECTED',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
        'transaction_type' => array(
            array('name' => 'ValidValues',
                'value' => 'DEPOSIT|WITHDRAWAL|REFUND|ORDER_DEPOSIT|ORDER_PAYMENT|ADJUSTMENT|CHARGE_FEE',
                'message'=> 'transaction type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','state','transaction_code','transaction_type','amount','ending_balance','object_id','object_type','transaction_detail','transaction_note','created_time','modified_time','closed_time');

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