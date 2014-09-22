<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServiceBuying
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property number $fee_percent fee_percent type : decimal(4,1)
 * @property number $min_fee min_fee type : double(20,2)
 * @property number $begin begin type : double(20,2)
 * @property number $end end type : double(20,2)
 * @property string $status status type : enum('ACTIVE','DISABLED') max_length : 8
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServiceBuying[] findById(integer $id) find objects in database by id
 * @method static \ServiceBuying findOneById(integer $id) find object in database by id
 * @method static \ServiceBuying retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setFeePercent(number $fee_percent) set fee_percent value
 * @method number getFeePercent() get fee_percent value
 * @method static \ServiceBuying[] findByFeePercent(number $fee_percent) find objects in database by fee_percent
 * @method static \ServiceBuying findOneByFeePercent(number $fee_percent) find object in database by fee_percent
 * @method static \ServiceBuying retrieveByFeePercent(number $fee_percent) retrieve object from poll by fee_percent, get it from db if not exist in poll

 * @method void setMinFee(number $min_fee) set min_fee value
 * @method number getMinFee() get min_fee value
 * @method static \ServiceBuying[] findByMinFee(number $min_fee) find objects in database by min_fee
 * @method static \ServiceBuying findOneByMinFee(number $min_fee) find object in database by min_fee
 * @method static \ServiceBuying retrieveByMinFee(number $min_fee) retrieve object from poll by min_fee, get it from db if not exist in poll

 * @method void setBegin(number $begin) set begin value
 * @method number getBegin() get begin value
 * @method static \ServiceBuying[] findByBegin(number $begin) find objects in database by begin
 * @method static \ServiceBuying findOneByBegin(number $begin) find object in database by begin
 * @method static \ServiceBuying retrieveByBegin(number $begin) retrieve object from poll by begin, get it from db if not exist in poll

 * @method void setEnd(number $end) set end value
 * @method number getEnd() get end value
 * @method static \ServiceBuying[] findByEnd(number $end) find objects in database by end
 * @method static \ServiceBuying findOneByEnd(number $end) find object in database by end
 * @method static \ServiceBuying retrieveByEnd(number $end) retrieve object from poll by end, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ServiceBuying[] findByStatus(string $status) find objects in database by status
 * @method static \ServiceBuying findOneByStatus(string $status) find object in database by status
 * @method static \ServiceBuying retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ServiceBuying[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ServiceBuying findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ServiceBuying retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ServiceBuyingBase extends ActiveRecord {
    protected static $_tableName = 'service_buying';
    protected static $_phpName = 'ServiceBuying';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_buying';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'fee_percent' => array('name' => 'fee_percent',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(4,1)',
                'length' => 4),
        'min_fee' => array('name' => 'min_fee',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'begin' => array('name' => 'begin',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'end' => array('name' => 'end',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'DISABLED\')',
                'length' => 8),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLED',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLED',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','fee_percent','min_fee','begin','end','status','created_time');

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