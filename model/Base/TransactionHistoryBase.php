<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * TransactionHistory
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $transaction_uid transaction_uid type : char(24) max_length : 24
 * @property string $type type type : enum('IN','OUT','TRANSFER','ADJUSTMENT') max_length : 10
 * @property string $account account type : char(16) max_length : 16
 * @property string $note note type : text max_length : 
 * @property string $detail detail type : text max_length : 
 * @property number $amount amount type : double(20,2)
 * @property number $acc_balance acc_balance type : double(20,2)
 * @property datetime $completed_time completed_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \TransactionHistory[] findById(integer $id) find objects in database by id
 * @method static \TransactionHistory findOneById(integer $id) find object in database by id
 * @method static \TransactionHistory retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setTransactionUid(string $transaction_uid) set transaction_uid value
 * @method string getTransactionUid() get transaction_uid value
 * @method static \TransactionHistory[] findByTransactionUid(string $transaction_uid) find objects in database by transaction_uid
 * @method static \TransactionHistory findOneByTransactionUid(string $transaction_uid) find object in database by transaction_uid
 * @method static \TransactionHistory retrieveByTransactionUid(string $transaction_uid) retrieve object from poll by transaction_uid, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \TransactionHistory[] findByType(string $type) find objects in database by type
 * @method static \TransactionHistory findOneByType(string $type) find object in database by type
 * @method static \TransactionHistory retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setAccount(string $account) set account value
 * @method string getAccount() get account value
 * @method static \TransactionHistory[] findByAccount(string $account) find objects in database by account
 * @method static \TransactionHistory findOneByAccount(string $account) find object in database by account
 * @method static \TransactionHistory retrieveByAccount(string $account) retrieve object from poll by account, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \TransactionHistory[] findByNote(string $note) find objects in database by note
 * @method static \TransactionHistory findOneByNote(string $note) find object in database by note
 * @method static \TransactionHistory retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setDetail(string $detail) set detail value
 * @method string getDetail() get detail value
 * @method static \TransactionHistory[] findByDetail(string $detail) find objects in database by detail
 * @method static \TransactionHistory findOneByDetail(string $detail) find object in database by detail
 * @method static \TransactionHistory retrieveByDetail(string $detail) retrieve object from poll by detail, get it from db if not exist in poll

 * @method void setAmount(number $amount) set amount value
 * @method number getAmount() get amount value
 * @method static \TransactionHistory[] findByAmount(number $amount) find objects in database by amount
 * @method static \TransactionHistory findOneByAmount(number $amount) find object in database by amount
 * @method static \TransactionHistory retrieveByAmount(number $amount) retrieve object from poll by amount, get it from db if not exist in poll

 * @method void setAccBalance(number $acc_balance) set acc_balance value
 * @method number getAccBalance() get acc_balance value
 * @method static \TransactionHistory[] findByAccBalance(number $acc_balance) find objects in database by acc_balance
 * @method static \TransactionHistory findOneByAccBalance(number $acc_balance) find object in database by acc_balance
 * @method static \TransactionHistory retrieveByAccBalance(number $acc_balance) retrieve object from poll by acc_balance, get it from db if not exist in poll

 * @method void setCompletedTime(\Flywheel\Db\Type\DateTime $completed_time) setCompletedTime(string $completed_time) set completed_time value
 * @method \Flywheel\Db\Type\DateTime getCompletedTime() get completed_time value
 * @method static \TransactionHistory[] findByCompletedTime(\Flywheel\Db\Type\DateTime $completed_time) findByCompletedTime(string $completed_time) find objects in database by completed_time
 * @method static \TransactionHistory findOneByCompletedTime(\Flywheel\Db\Type\DateTime $completed_time) findOneByCompletedTime(string $completed_time) find object in database by completed_time
 * @method static \TransactionHistory retrieveByCompletedTime(\Flywheel\Db\Type\DateTime $completed_time) retrieveByCompletedTime(string $completed_time) retrieve object from poll by completed_time, get it from db if not exist in poll


 */
abstract class TransactionHistoryBase extends ActiveRecord {
    protected static $_tableName = 'transaction_history';
    protected static $_phpName = 'TransactionHistory';
    protected static $_pk = 'id';
    protected static $_alias = 't';
    protected static $_dbConnectName = 'transaction_history';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'transaction_uid' => array('name' => 'transaction_uid',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(24)',
                'length' => 24),
        'type' => array('name' => 'type',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'enum(\'IN\',\'OUT\',\'TRANSFER\',\'ADJUSTMENT\')',
                'length' => 10),
        'account' => array('name' => 'account',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(16)',
                'length' => 16),
        'note' => array('name' => 'note',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'detail' => array('name' => 'detail',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'amount' => array('name' => 'amount',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'acc_balance' => array('name' => 'acc_balance',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'completed_time' => array('name' => 'completed_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'transaction_uid' => array(
            array('name' => 'Unique',
                'message'=> 'transaction uid\'s was used'
            ),
        ),
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'IN|OUT|TRANSFER|ADJUSTMENT',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'transaction_uid' => array(
            array('name' => 'Unique',
                'message'=> 'transaction uid\'s was used'
            ),
        ),
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'IN|OUT|TRANSFER|ADJUSTMENT',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','transaction_uid','type','account','note','detail','amount','acc_balance','completed_time');

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