<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderItemComment
 * @version		$Id$
 * @package		Model

 * @property integer $id id type : int(11)
 * @property integer $order_item_id order_item_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property string $content content type : varchar(255) max_length : 255
 * @property string $type type type : varchar(100) max_length : 100
 * @property integer $created_by created_by type : int(11)
 * @property datetime $created_time created_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderItemComment[] findById(integer $id) find objects in database by id
 * @method static \OrderItemComment findOneById(integer $id) find object in database by id
 * @method static \OrderItemComment retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setOrderItemId(integer $order_item_id) set order_item_id value
 * @method integer getOrderItemId() get order_item_id value
 * @method static \OrderItemComment[] findByOrderItemId(integer $order_item_id) find objects in database by order_item_id
 * @method static \OrderItemComment findOneByOrderItemId(integer $order_item_id) find object in database by order_item_id
 * @method static \OrderItemComment retrieveByOrderItemId(integer $order_item_id) retrieve object from poll by order_item_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderItemComment[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderItemComment findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderItemComment retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \OrderItemComment[] findByContent(string $content) find objects in database by content
 * @method static \OrderItemComment findOneByContent(string $content) find object in database by content
 * @method static \OrderItemComment retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \OrderItemComment[] findByType(string $type) find objects in database by type
 * @method static \OrderItemComment findOneByType(string $type) find object in database by type
 * @method static \OrderItemComment retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \OrderItemComment[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \OrderItemComment findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \OrderItemComment retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderItemComment[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderItemComment findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderItemComment retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class OrderItemCommentBase extends ActiveRecord {
    protected static $_tableName = 'order_item_comment';
    protected static $_phpName = 'OrderItemComment';
    protected static $_pk = '';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_item_comment';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
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
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'content' => array('name' => 'content',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
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
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','order_item_id','order_id','content','type','created_by','created_time');

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