<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderComment
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property string $order_code order_code type : varchar(100) max_length : 100
 * @property string $content content type : text max_length : 
 * @property string $type type type : enum('INTERNAL','EXTERNAL','SYSTEM') max_length : 8
 * @property integer $parent_id parent_id type : int(11)
 * @property integer $created_by created_by type : int(11)
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderComment[] findById(integer $id) find objects in database by id
 * @method static \OrderComment findOneById(integer $id) find object in database by id
 * @method static \OrderComment retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderComment[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderComment findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderComment retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \OrderComment[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \OrderComment findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \OrderComment retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \OrderComment[] findByContent(string $content) find objects in database by content
 * @method static \OrderComment findOneByContent(string $content) find object in database by content
 * @method static \OrderComment retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \OrderComment[] findByType(string $type) find objects in database by type
 * @method static \OrderComment findOneByType(string $type) find object in database by type
 * @method static \OrderComment retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setParentId(integer $parent_id) set parent_id value
 * @method integer getParentId() get parent_id value
 * @method static \OrderComment[] findByParentId(integer $parent_id) find objects in database by parent_id
 * @method static \OrderComment findOneByParentId(integer $parent_id) find object in database by parent_id
 * @method static \OrderComment retrieveByParentId(integer $parent_id) retrieve object from poll by parent_id, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \OrderComment[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \OrderComment findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \OrderComment retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderComment[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderComment findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderComment retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderCommentBase extends ActiveRecord {
    protected static $_tableName = 'order_comment';
    protected static $_phpName = 'OrderComment';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_comment';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
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
    protected static $_cols = array('id','order_id','order_code','content','type','parent_id','created_by','created_time');

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