<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ComplaintComment
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $complaint_id complaint_id type : int(11)
 * @property string $complaint_code complaint_code type : varchar(100) max_length : 100
 * @property integer $item_id item_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property string $content content type : text max_length : 
 * @property string $type type type : enum('INTERNAL','EXTERNAL','SYSTEM') max_length : 8
 * @property integer $parent_id parent_id type : int(11)
 * @property integer $created_by created_by type : int(11)
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ComplaintComment[] findById(integer $id) find objects in database by id
 * @method static \ComplaintComment findOneById(integer $id) find object in database by id
 * @method static \ComplaintComment retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setComplaintId(integer $complaint_id) set complaint_id value
 * @method integer getComplaintId() get complaint_id value
 * @method static \ComplaintComment[] findByComplaintId(integer $complaint_id) find objects in database by complaint_id
 * @method static \ComplaintComment findOneByComplaintId(integer $complaint_id) find object in database by complaint_id
 * @method static \ComplaintComment retrieveByComplaintId(integer $complaint_id) retrieve object from poll by complaint_id, get it from db if not exist in poll

 * @method void setComplaintCode(string $complaint_code) set complaint_code value
 * @method string getComplaintCode() get complaint_code value
 * @method static \ComplaintComment[] findByComplaintCode(string $complaint_code) find objects in database by complaint_code
 * @method static \ComplaintComment findOneByComplaintCode(string $complaint_code) find object in database by complaint_code
 * @method static \ComplaintComment retrieveByComplaintCode(string $complaint_code) retrieve object from poll by complaint_code, get it from db if not exist in poll

 * @method void setItemId(integer $item_id) set item_id value
 * @method integer getItemId() get item_id value
 * @method static \ComplaintComment[] findByItemId(integer $item_id) find objects in database by item_id
 * @method static \ComplaintComment findOneByItemId(integer $item_id) find object in database by item_id
 * @method static \ComplaintComment retrieveByItemId(integer $item_id) retrieve object from poll by item_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \ComplaintComment[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \ComplaintComment findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \ComplaintComment retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \ComplaintComment[] findByContent(string $content) find objects in database by content
 * @method static \ComplaintComment findOneByContent(string $content) find object in database by content
 * @method static \ComplaintComment retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \ComplaintComment[] findByType(string $type) find objects in database by type
 * @method static \ComplaintComment findOneByType(string $type) find object in database by type
 * @method static \ComplaintComment retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setParentId(integer $parent_id) set parent_id value
 * @method integer getParentId() get parent_id value
 * @method static \ComplaintComment[] findByParentId(integer $parent_id) find objects in database by parent_id
 * @method static \ComplaintComment findOneByParentId(integer $parent_id) find object in database by parent_id
 * @method static \ComplaintComment retrieveByParentId(integer $parent_id) retrieve object from poll by parent_id, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \ComplaintComment[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \ComplaintComment findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \ComplaintComment retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ComplaintComment[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ComplaintComment findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ComplaintComment retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class ComplaintCommentBase extends ActiveRecord {
    protected static $_tableName = 'complaint_comment';
    protected static $_phpName = 'ComplaintComment';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaint_comment';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'complaint_id' => array('name' => 'complaint_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'complaint_code' => array('name' => 'complaint_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'item_id' => array('name' => 'item_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_id' => array('name' => 'order_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'content' => array('name' => 'content',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'type' => array('name' => 'type',
                'default' => 'EXTERNAL',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'INTERNAL\',\'EXTERNAL\',\'SYSTEM\')',
                'length' => 8),
        'parent_id' => array('name' => 'parent_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'created_by' => array('name' => 'created_by',
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
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'INTERNAL|EXTERNAL|SYSTEM',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'INTERNAL|EXTERNAL|SYSTEM',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','complaint_id','complaint_code','item_id','order_id','content','type','parent_id','created_by','created_time');

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