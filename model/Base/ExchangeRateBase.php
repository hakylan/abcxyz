<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ExchangeRate
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $yuan yuan type : tinyint(11)
 * @property number $exchange_rate exchange_rate type : float
 * @property integer $status status type : int(11)
 * @property integer $user_create_id user_create_id type : int(11)
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ExchangeRate[] findById(integer $id) find objects in database by id
 * @method static \ExchangeRate findOneById(integer $id) find object in database by id
 * @method static \ExchangeRate retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setYuan(integer $yuan) set yuan value
 * @method integer getYuan() get yuan value
 * @method static \ExchangeRate[] findByYuan(integer $yuan) find objects in database by yuan
 * @method static \ExchangeRate findOneByYuan(integer $yuan) find object in database by yuan
 * @method static \ExchangeRate retrieveByYuan(integer $yuan) retrieve object from poll by yuan, get it from db if not exist in poll

 * @method void setExchangeRate(number $exchange_rate) set exchange_rate value
 * @method number getExchangeRate() get exchange_rate value
 * @method static \ExchangeRate[] findByExchangeRate(number $exchange_rate) find objects in database by exchange_rate
 * @method static \ExchangeRate findOneByExchangeRate(number $exchange_rate) find object in database by exchange_rate
 * @method static \ExchangeRate retrieveByExchangeRate(number $exchange_rate) retrieve object from poll by exchange_rate, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \ExchangeRate[] findByStatus(integer $status) find objects in database by status
 * @method static \ExchangeRate findOneByStatus(integer $status) find object in database by status
 * @method static \ExchangeRate retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setUserCreateId(integer $user_create_id) set user_create_id value
 * @method integer getUserCreateId() get user_create_id value
 * @method static \ExchangeRate[] findByUserCreateId(integer $user_create_id) find objects in database by user_create_id
 * @method static \ExchangeRate findOneByUserCreateId(integer $user_create_id) find object in database by user_create_id
 * @method static \ExchangeRate retrieveByUserCreateId(integer $user_create_id) retrieve object from poll by user_create_id, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \ExchangeRate[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \ExchangeRate findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \ExchangeRate retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class ExchangeRateBase extends ActiveRecord {
    protected static $_tableName = 'exchange_rate';
    protected static $_phpName = 'ExchangeRate';
    protected static $_pk = 'id';
    protected static $_alias = 'e';
    protected static $_dbConnectName = 'exchange_rate';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'yuan' => array('name' => 'yuan',
                'default' => 1,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(11)',
                'length' => 1),
        'exchange_rate' => array('name' => 'exchange_rate',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_create_id' => array('name' => 'user_create_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
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
    protected static $_cols = array('id','yuan','exchange_rate','status','user_create_id','create_time');

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