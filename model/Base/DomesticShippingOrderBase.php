<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * DomesticShippingOrder
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $order_code order_code type : varchar(20) max_length : 20
 * @property integer $domestic_shipping_id domestic_shipping_id type : int(11)
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \DomesticShippingOrder[] findById(integer $id) find objects in database by id
 * @method static \DomesticShippingOrder findOneById(integer $id) find object in database by id
 * @method static \DomesticShippingOrder retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \DomesticShippingOrder[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \DomesticShippingOrder findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \DomesticShippingOrder retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setDomesticShippingId(integer $domestic_shipping_id) set domestic_shipping_id value
 * @method integer getDomesticShippingId() get domestic_shipping_id value
 * @method static \DomesticShippingOrder[] findByDomesticShippingId(integer $domestic_shipping_id) find objects in database by domestic_shipping_id
 * @method static \DomesticShippingOrder findOneByDomesticShippingId(integer $domestic_shipping_id) find object in database by domestic_shipping_id
 * @method static \DomesticShippingOrder retrieveByDomesticShippingId(integer $domestic_shipping_id) retrieve object from poll by domestic_shipping_id, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \DomesticShippingOrder[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \DomesticShippingOrder findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \DomesticShippingOrder retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class DomesticShippingOrderBase extends ActiveRecord {
    protected static $_tableName = 'domestic_shipping_order';
    protected static $_phpName = 'DomesticShippingOrder';
    protected static $_pk = 'id';
    protected static $_alias = 'd';
    protected static $_dbConnectName = 'domestic_shipping_order';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'domestic_shipping_id' => array('name' => 'domestic_shipping_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
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
    protected static $_cols = array('id','order_code','domestic_shipping_id','created_time');

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