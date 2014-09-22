<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * WarehouseMapping
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $city_id city_id type : int(11)
 * @property integer $method method type : tinyint(1)
 * @property number $start_weight start_weight type : double
 * @property number $end_weight end_weight type : double
 * @property number $start_fee start_fee type : double
 * @property number $end_fee end_fee type : double
 * @property string $warehouse warehouse type : varchar(10) max_length : 10
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \WarehouseMapping[] findById(integer $id) find objects in database by id
 * @method static \WarehouseMapping findOneById(integer $id) find object in database by id
 * @method static \WarehouseMapping retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCityId(integer $city_id) set city_id value
 * @method integer getCityId() get city_id value
 * @method static \WarehouseMapping[] findByCityId(integer $city_id) find objects in database by city_id
 * @method static \WarehouseMapping findOneByCityId(integer $city_id) find object in database by city_id
 * @method static \WarehouseMapping retrieveByCityId(integer $city_id) retrieve object from poll by city_id, get it from db if not exist in poll

 * @method void setMethod(integer $method) set method value
 * @method integer getMethod() get method value
 * @method static \WarehouseMapping[] findByMethod(integer $method) find objects in database by method
 * @method static \WarehouseMapping findOneByMethod(integer $method) find object in database by method
 * @method static \WarehouseMapping retrieveByMethod(integer $method) retrieve object from poll by method, get it from db if not exist in poll

 * @method void setStartWeight(number $start_weight) set start_weight value
 * @method number getStartWeight() get start_weight value
 * @method static \WarehouseMapping[] findByStartWeight(number $start_weight) find objects in database by start_weight
 * @method static \WarehouseMapping findOneByStartWeight(number $start_weight) find object in database by start_weight
 * @method static \WarehouseMapping retrieveByStartWeight(number $start_weight) retrieve object from poll by start_weight, get it from db if not exist in poll

 * @method void setEndWeight(number $end_weight) set end_weight value
 * @method number getEndWeight() get end_weight value
 * @method static \WarehouseMapping[] findByEndWeight(number $end_weight) find objects in database by end_weight
 * @method static \WarehouseMapping findOneByEndWeight(number $end_weight) find object in database by end_weight
 * @method static \WarehouseMapping retrieveByEndWeight(number $end_weight) retrieve object from poll by end_weight, get it from db if not exist in poll

 * @method void setStartFee(number $start_fee) set start_fee value
 * @method number getStartFee() get start_fee value
 * @method static \WarehouseMapping[] findByStartFee(number $start_fee) find objects in database by start_fee
 * @method static \WarehouseMapping findOneByStartFee(number $start_fee) find object in database by start_fee
 * @method static \WarehouseMapping retrieveByStartFee(number $start_fee) retrieve object from poll by start_fee, get it from db if not exist in poll

 * @method void setEndFee(number $end_fee) set end_fee value
 * @method number getEndFee() get end_fee value
 * @method static \WarehouseMapping[] findByEndFee(number $end_fee) find objects in database by end_fee
 * @method static \WarehouseMapping findOneByEndFee(number $end_fee) find object in database by end_fee
 * @method static \WarehouseMapping retrieveByEndFee(number $end_fee) retrieve object from poll by end_fee, get it from db if not exist in poll

 * @method void setWarehouse(string $warehouse) set warehouse value
 * @method string getWarehouse() get warehouse value
 * @method static \WarehouseMapping[] findByWarehouse(string $warehouse) find objects in database by warehouse
 * @method static \WarehouseMapping findOneByWarehouse(string $warehouse) find object in database by warehouse
 * @method static \WarehouseMapping retrieveByWarehouse(string $warehouse) retrieve object from poll by warehouse, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \WarehouseMapping[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \WarehouseMapping findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \WarehouseMapping retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class WarehouseMappingBase extends ActiveRecord {
    protected static $_tableName = 'warehouse_mapping';
    protected static $_phpName = 'WarehouseMapping';
    protected static $_pk = 'id';
    protected static $_alias = 'w';
    protected static $_dbConnectName = 'warehouse_mapping';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'city_id' => array('name' => 'city_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'method' => array('name' => 'method',
                'default' => 1,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'start_weight' => array('name' => 'start_weight',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'end_weight' => array('name' => 'end_weight',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'start_fee' => array('name' => 'start_fee',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'end_fee' => array('name' => 'end_fee',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'warehouse' => array('name' => 'warehouse',
                'default' => 'HANOI',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(10)',
                'length' => 10),
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
    protected static $_cols = array('id','city_id','method','start_weight','end_weight','start_fee','end_fee','warehouse','created_time');

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