<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ServiceDiscount
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $level_id level_id type : int(11)
 * @property string $service service type : varchar(255) max_length : 255
 * @property number $value value type : float
 * @property string $type type type : enum('PERCENT','FIX') max_length : 7
 * @property integer $active active type : tinyint(1)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ServiceDiscount[] findById(integer $id) find objects in database by id
 * @method static \ServiceDiscount findOneById(integer $id) find object in database by id
 * @method static \ServiceDiscount retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setLevelId(integer $level_id) set level_id value
 * @method integer getLevelId() get level_id value
 * @method static \ServiceDiscount[] findByLevelId(integer $level_id) find objects in database by level_id
 * @method static \ServiceDiscount findOneByLevelId(integer $level_id) find object in database by level_id
 * @method static \ServiceDiscount retrieveByLevelId(integer $level_id) retrieve object from poll by level_id, get it from db if not exist in poll

 * @method void setService(string $service) set service value
 * @method string getService() get service value
 * @method static \ServiceDiscount[] findByService(string $service) find objects in database by service
 * @method static \ServiceDiscount findOneByService(string $service) find object in database by service
 * @method static \ServiceDiscount retrieveByService(string $service) retrieve object from poll by service, get it from db if not exist in poll

 * @method void setValue(number $value) set value value
 * @method number getValue() get value value
 * @method static \ServiceDiscount[] findByValue(number $value) find objects in database by value
 * @method static \ServiceDiscount findOneByValue(number $value) find object in database by value
 * @method static \ServiceDiscount retrieveByValue(number $value) retrieve object from poll by value, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \ServiceDiscount[] findByType(string $type) find objects in database by type
 * @method static \ServiceDiscount findOneByType(string $type) find object in database by type
 * @method static \ServiceDiscount retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setActive(integer $active) set active value
 * @method integer getActive() get active value
 * @method static \ServiceDiscount[] findByActive(integer $active) find objects in database by active
 * @method static \ServiceDiscount findOneByActive(integer $active) find object in database by active
 * @method static \ServiceDiscount retrieveByActive(integer $active) retrieve object from poll by active, get it from db if not exist in poll


 */
abstract class ServiceDiscountBase extends ActiveRecord {
    protected static $_tableName = 'service_discount';
    protected static $_phpName = 'ServiceDiscount';
    protected static $_pk = 'id';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'service_discount';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'level_id' => array('name' => 'level_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'service' => array('name' => 'service',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'value' => array('name' => 'value',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'PERCENT\',\'FIX\')',
                'length' => 7),
        'active' => array('name' => 'active',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
     );
    protected static $_validate = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'PERCENT|FIX',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'PERCENT|FIX',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','level_id','service','value','type','active');

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