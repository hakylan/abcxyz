<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ComplaintSellerReason
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $complaint_seller_id complaint_seller_id type : int(11)
 * @property string $long_type long_type type : varchar(255) max_length : 255
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ComplaintSellerReason[] findById(integer $id) find objects in database by id
 * @method static \ComplaintSellerReason findOneById(integer $id) find object in database by id
 * @method static \ComplaintSellerReason retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setComplaintSellerId(integer $complaint_seller_id) set complaint_seller_id value
 * @method integer getComplaintSellerId() get complaint_seller_id value
 * @method static \ComplaintSellerReason[] findByComplaintSellerId(integer $complaint_seller_id) find objects in database by complaint_seller_id
 * @method static \ComplaintSellerReason findOneByComplaintSellerId(integer $complaint_seller_id) find object in database by complaint_seller_id
 * @method static \ComplaintSellerReason retrieveByComplaintSellerId(integer $complaint_seller_id) retrieve object from poll by complaint_seller_id, get it from db if not exist in poll

 * @method void setLongType(string $long_type) set long_type value
 * @method string getLongType() get long_type value
 * @method static \ComplaintSellerReason[] findByLongType(string $long_type) find objects in database by long_type
 * @method static \ComplaintSellerReason findOneByLongType(string $long_type) find object in database by long_type
 * @method static \ComplaintSellerReason retrieveByLongType(string $long_type) retrieve object from poll by long_type, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \ComplaintSellerReason[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \ComplaintSellerReason findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \ComplaintSellerReason retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class ComplaintSellerReasonBase extends ActiveRecord {
    protected static $_tableName = 'complaint_seller_reason';
    protected static $_phpName = 'ComplaintSellerReason';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaint_seller_reason';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'complaint_seller_id' => array('name' => 'complaint_seller_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'long_type' => array('name' => 'long_type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
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
    protected static $_cols = array('id','complaint_seller_id','long_type','create_time');

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