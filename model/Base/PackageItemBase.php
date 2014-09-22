<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * PackageItem
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $package_id package_id type : int(11)
 * @property integer $order_item_id order_item_id type : int(11)
 * @property integer $total_item total_item type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \PackageItem[] findById(integer $id) find objects in database by id
 * @method static \PackageItem findOneById(integer $id) find object in database by id
 * @method static \PackageItem retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setPackageId(integer $package_id) set package_id value
 * @method integer getPackageId() get package_id value
 * @method static \PackageItem[] findByPackageId(integer $package_id) find objects in database by package_id
 * @method static \PackageItem findOneByPackageId(integer $package_id) find object in database by package_id
 * @method static \PackageItem retrieveByPackageId(integer $package_id) retrieve object from poll by package_id, get it from db if not exist in poll

 * @method void setOrderItemId(integer $order_item_id) set order_item_id value
 * @method integer getOrderItemId() get order_item_id value
 * @method static \PackageItem[] findByOrderItemId(integer $order_item_id) find objects in database by order_item_id
 * @method static \PackageItem findOneByOrderItemId(integer $order_item_id) find object in database by order_item_id
 * @method static \PackageItem retrieveByOrderItemId(integer $order_item_id) retrieve object from poll by order_item_id, get it from db if not exist in poll

 * @method void setTotalItem(integer $total_item) set total_item value
 * @method integer getTotalItem() get total_item value
 * @method static \PackageItem[] findByTotalItem(integer $total_item) find objects in database by total_item
 * @method static \PackageItem findOneByTotalItem(integer $total_item) find object in database by total_item
 * @method static \PackageItem retrieveByTotalItem(integer $total_item) retrieve object from poll by total_item, get it from db if not exist in poll


 */
abstract class PackageItemBase extends ActiveRecord {
    protected static $_tableName = 'package_item';
    protected static $_phpName = 'PackageItem';
    protected static $_pk = 'id';
    protected static $_alias = 'p';
    protected static $_dbConnectName = 'package_item';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'package_id' => array('name' => 'package_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_item_id' => array('name' => 'order_item_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'total_item' => array('name' => 'total_item',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','package_id','order_item_id','total_item');

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