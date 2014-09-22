<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServiceChinaDomestic
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $title title type : varchar(22) max_length : 22
 * @property string $type type type : varchar(50) max_length : 50
 * @property string $first_fee first_fee type : varchar(100) max_length : 100
 * @property string $next_fee next_fee type : varchar(100) max_length : 100
 * @property string $status status type : enum('ACTIVE','DISABLE') max_length : 7
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServiceChinaDomestic[] findById(integer $id) find objects in database by id
 * @method static \ServiceChinaDomestic findOneById(integer $id) find object in database by id
 * @method static \ServiceChinaDomestic retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \ServiceChinaDomestic[] findByTitle(string $title) find objects in database by title
 * @method static \ServiceChinaDomestic findOneByTitle(string $title) find object in database by title
 * @method static \ServiceChinaDomestic retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \ServiceChinaDomestic[] findByType(string $type) find objects in database by type
 * @method static \ServiceChinaDomestic findOneByType(string $type) find object in database by type
 * @method static \ServiceChinaDomestic retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setFirstFee(string $first_fee) set first_fee value
 * @method string getFirstFee() get first_fee value
 * @method static \ServiceChinaDomestic[] findByFirstFee(string $first_fee) find objects in database by first_fee
 * @method static \ServiceChinaDomestic findOneByFirstFee(string $first_fee) find object in database by first_fee
 * @method static \ServiceChinaDomestic retrieveByFirstFee(string $first_fee) retrieve object from poll by first_fee, get it from db if not exist in poll

 * @method void setNextFee(string $next_fee) set next_fee value
 * @method string getNextFee() get next_fee value
 * @method static \ServiceChinaDomestic[] findByNextFee(string $next_fee) find objects in database by next_fee
 * @method static \ServiceChinaDomestic findOneByNextFee(string $next_fee) find object in database by next_fee
 * @method static \ServiceChinaDomestic retrieveByNextFee(string $next_fee) retrieve object from poll by next_fee, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ServiceChinaDomestic[] findByStatus(string $status) find objects in database by status
 * @method static \ServiceChinaDomestic findOneByStatus(string $status) find object in database by status
 * @method static \ServiceChinaDomestic retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ServiceChinaDomestic[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ServiceChinaDomestic findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ServiceChinaDomestic retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ServiceChinaDomesticBase extends ActiveRecord {
    protected static $_tableName = 'service_china_domestic';
    protected static $_phpName = 'ServiceChinaDomestic';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_china_domestic';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(22)',
                'length' => 22),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'first_fee' => array('name' => 'first_fee',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'next_fee' => array('name' => 'next_fee',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'ACTIVE\',\'DISABLE\')',
                'length' => 7),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLE',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'status' => array(
            array('name' => 'ValidValues',
                'value' => 'ACTIVE|DISABLE',
                'message'=> 'status\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','title','type','first_fee','next_fee','status','created_time');

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