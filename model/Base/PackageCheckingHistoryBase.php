<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * PackageCheckingHistory
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $created_by created_by type : int(10)
 * @property datetime $created_time created_time type : datetime
 * @property integer $package_id package_id type : int(11)
 * @property integer $total_product total_product type : int(11)
 * @property integer $ordering_check ordering_check type : tinyint(2)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \PackageCheckingHistory[] findById(integer $id) find objects in database by id
 * @method static \PackageCheckingHistory findOneById(integer $id) find object in database by id
 * @method static \PackageCheckingHistory retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \PackageCheckingHistory[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \PackageCheckingHistory findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \PackageCheckingHistory retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \PackageCheckingHistory[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \PackageCheckingHistory findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \PackageCheckingHistory retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setPackageId(integer $package_id) set package_id value
 * @method integer getPackageId() get package_id value
 * @method static \PackageCheckingHistory[] findByPackageId(integer $package_id) find objects in database by package_id
 * @method static \PackageCheckingHistory findOneByPackageId(integer $package_id) find object in database by package_id
 * @method static \PackageCheckingHistory retrieveByPackageId(integer $package_id) retrieve object from poll by package_id, get it from db if not exist in poll

 * @method void setTotalProduct(integer $total_product) set total_product value
 * @method integer getTotalProduct() get total_product value
 * @method static \PackageCheckingHistory[] findByTotalProduct(integer $total_product) find objects in database by total_product
 * @method static \PackageCheckingHistory findOneByTotalProduct(integer $total_product) find object in database by total_product
 * @method static \PackageCheckingHistory retrieveByTotalProduct(integer $total_product) retrieve object from poll by total_product, get it from db if not exist in poll

 * @method void setOrderingCheck(integer $ordering_check) set ordering_check value
 * @method integer getOrderingCheck() get ordering_check value
 * @method static \PackageCheckingHistory[] findByOrderingCheck(integer $ordering_check) find objects in database by ordering_check
 * @method static \PackageCheckingHistory findOneByOrderingCheck(integer $ordering_check) find object in database by ordering_check
 * @method static \PackageCheckingHistory retrieveByOrderingCheck(integer $ordering_check) retrieve object from poll by ordering_check, get it from db if not exist in poll


 */
abstract class PackageCheckingHistoryBase extends ActiveRecord {
    protected static $_tableName = 'package_checking_history';
    protected static $_phpName = 'PackageCheckingHistory';
    protected static $_pk = 'id';
    protected static $_alias = 'p';
    protected static $_dbConnectName = 'package_checking_history';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'created_by' => array('name' => 'created_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(10)',
                'length' => 4),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'package_id' => array('name' => 'package_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'total_product' => array('name' => 'total_product',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'ordering_check' => array('name' => 'ordering_check',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(2)',
                'length' => 1),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','created_by','created_time','package_id','total_product','ordering_check');

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