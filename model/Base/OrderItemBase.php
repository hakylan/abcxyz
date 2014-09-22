<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * OrderItem
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $item_id item_id type : varchar(100) max_length : 100
 * @property integer $order_id order_id type : int(11)
 * @property string $title title type : varchar(255) max_length : 255
 * @property string $link link type : varchar(255) max_length : 255
 * @property string $image image type : text max_length : 
 * @property string $property property type : varchar(200) max_length : 200
 * @property string $property_translated property_translated type : varchar(255) max_length : 255
 * @property string $outer_id outer_id type : varchar(50) max_length : 50
 * @property number $price price type : double(20,2)
 * @property number $price_origin price_origin type : float
 * @property number $price_promotion price_promotion type : float
 * @property string $price_table price_table type : text max_length : 
 * @property number $weight weight type : decimal(5,2)
 * @property integer $step step type : int(11)
 * @property integer $require_min require_min type : int(11)
 * @property integer $stock stock type : int(11)
 * @property string $source source type : varchar(50) max_length : 50
 * @property string $note note type : varchar(255) max_length : 255
 * @property string $note_system note_system type : varchar(255) max_length : 255
 * @property integer $is_paied is_paied type : tinyint(2)
 * @property string $tool tool type : char(50) max_length : 50
 * @property integer $order_quantity order_quantity type : int(11)
 * @property integer $pending_quantity pending_quantity type : int(11)
 * @property integer $recive_quantity recive_quantity type : int(11)
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modify_time modify_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \OrderItem[] findById(integer $id) find objects in database by id
 * @method static \OrderItem findOneById(integer $id) find object in database by id
 * @method static \OrderItem retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setItemId(string $item_id) set item_id value
 * @method string getItemId() get item_id value
 * @method static \OrderItem[] findByItemId(string $item_id) find objects in database by item_id
 * @method static \OrderItem findOneByItemId(string $item_id) find object in database by item_id
 * @method static \OrderItem retrieveByItemId(string $item_id) retrieve object from poll by item_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \OrderItem[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \OrderItem findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \OrderItem retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \OrderItem[] findByTitle(string $title) find objects in database by title
 * @method static \OrderItem findOneByTitle(string $title) find object in database by title
 * @method static \OrderItem retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setLink(string $link) set link value
 * @method string getLink() get link value
 * @method static \OrderItem[] findByLink(string $link) find objects in database by link
 * @method static \OrderItem findOneByLink(string $link) find object in database by link
 * @method static \OrderItem retrieveByLink(string $link) retrieve object from poll by link, get it from db if not exist in poll

 * @method void setImage(string $image) set image value
 * @method string getImage() get image value
 * @method static \OrderItem[] findByImage(string $image) find objects in database by image
 * @method static \OrderItem findOneByImage(string $image) find object in database by image
 * @method static \OrderItem retrieveByImage(string $image) retrieve object from poll by image, get it from db if not exist in poll

 * @method void setProperty(string $property) set property value
 * @method string getProperty() get property value
 * @method static \OrderItem[] findByProperty(string $property) find objects in database by property
 * @method static \OrderItem findOneByProperty(string $property) find object in database by property
 * @method static \OrderItem retrieveByProperty(string $property) retrieve object from poll by property, get it from db if not exist in poll

 * @method void setPropertyTranslated(string $property_translated) set property_translated value
 * @method string getPropertyTranslated() get property_translated value
 * @method static \OrderItem[] findByPropertyTranslated(string $property_translated) find objects in database by property_translated
 * @method static \OrderItem findOneByPropertyTranslated(string $property_translated) find object in database by property_translated
 * @method static \OrderItem retrieveByPropertyTranslated(string $property_translated) retrieve object from poll by property_translated, get it from db if not exist in poll

 * @method void setOuterId(string $outer_id) set outer_id value
 * @method string getOuterId() get outer_id value
 * @method static \OrderItem[] findByOuterId(string $outer_id) find objects in database by outer_id
 * @method static \OrderItem findOneByOuterId(string $outer_id) find object in database by outer_id
 * @method static \OrderItem retrieveByOuterId(string $outer_id) retrieve object from poll by outer_id, get it from db if not exist in poll

 * @method void setPrice(number $price) set price value
 * @method number getPrice() get price value
 * @method static \OrderItem[] findByPrice(number $price) find objects in database by price
 * @method static \OrderItem findOneByPrice(number $price) find object in database by price
 * @method static \OrderItem retrieveByPrice(number $price) retrieve object from poll by price, get it from db if not exist in poll

 * @method void setPriceOrigin(number $price_origin) set price_origin value
 * @method number getPriceOrigin() get price_origin value
 * @method static \OrderItem[] findByPriceOrigin(number $price_origin) find objects in database by price_origin
 * @method static \OrderItem findOneByPriceOrigin(number $price_origin) find object in database by price_origin
 * @method static \OrderItem retrieveByPriceOrigin(number $price_origin) retrieve object from poll by price_origin, get it from db if not exist in poll

 * @method void setPricePromotion(number $price_promotion) set price_promotion value
 * @method number getPricePromotion() get price_promotion value
 * @method static \OrderItem[] findByPricePromotion(number $price_promotion) find objects in database by price_promotion
 * @method static \OrderItem findOneByPricePromotion(number $price_promotion) find object in database by price_promotion
 * @method static \OrderItem retrieveByPricePromotion(number $price_promotion) retrieve object from poll by price_promotion, get it from db if not exist in poll

 * @method void setPriceTable(string $price_table) set price_table value
 * @method string getPriceTable() get price_table value
 * @method static \OrderItem[] findByPriceTable(string $price_table) find objects in database by price_table
 * @method static \OrderItem findOneByPriceTable(string $price_table) find object in database by price_table
 * @method static \OrderItem retrieveByPriceTable(string $price_table) retrieve object from poll by price_table, get it from db if not exist in poll

 * @method void setWeight(number $weight) set weight value
 * @method number getWeight() get weight value
 * @method static \OrderItem[] findByWeight(number $weight) find objects in database by weight
 * @method static \OrderItem findOneByWeight(number $weight) find object in database by weight
 * @method static \OrderItem retrieveByWeight(number $weight) retrieve object from poll by weight, get it from db if not exist in poll

 * @method void setStep(integer $step) set step value
 * @method integer getStep() get step value
 * @method static \OrderItem[] findByStep(integer $step) find objects in database by step
 * @method static \OrderItem findOneByStep(integer $step) find object in database by step
 * @method static \OrderItem retrieveByStep(integer $step) retrieve object from poll by step, get it from db if not exist in poll

 * @method void setRequireMin(integer $require_min) set require_min value
 * @method integer getRequireMin() get require_min value
 * @method static \OrderItem[] findByRequireMin(integer $require_min) find objects in database by require_min
 * @method static \OrderItem findOneByRequireMin(integer $require_min) find object in database by require_min
 * @method static \OrderItem retrieveByRequireMin(integer $require_min) retrieve object from poll by require_min, get it from db if not exist in poll

 * @method void setStock(integer $stock) set stock value
 * @method integer getStock() get stock value
 * @method static \OrderItem[] findByStock(integer $stock) find objects in database by stock
 * @method static \OrderItem findOneByStock(integer $stock) find object in database by stock
 * @method static \OrderItem retrieveByStock(integer $stock) retrieve object from poll by stock, get it from db if not exist in poll

 * @method void setSource(string $source) set source value
 * @method string getSource() get source value
 * @method static \OrderItem[] findBySource(string $source) find objects in database by source
 * @method static \OrderItem findOneBySource(string $source) find object in database by source
 * @method static \OrderItem retrieveBySource(string $source) retrieve object from poll by source, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \OrderItem[] findByNote(string $note) find objects in database by note
 * @method static \OrderItem findOneByNote(string $note) find object in database by note
 * @method static \OrderItem retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setNoteSystem(string $note_system) set note_system value
 * @method string getNoteSystem() get note_system value
 * @method static \OrderItem[] findByNoteSystem(string $note_system) find objects in database by note_system
 * @method static \OrderItem findOneByNoteSystem(string $note_system) find object in database by note_system
 * @method static \OrderItem retrieveByNoteSystem(string $note_system) retrieve object from poll by note_system, get it from db if not exist in poll

 * @method void setIsPaied(integer $is_paied) set is_paied value
 * @method integer getIsPaied() get is_paied value
 * @method static \OrderItem[] findByIsPaied(integer $is_paied) find objects in database by is_paied
 * @method static \OrderItem findOneByIsPaied(integer $is_paied) find object in database by is_paied
 * @method static \OrderItem retrieveByIsPaied(integer $is_paied) retrieve object from poll by is_paied, get it from db if not exist in poll

 * @method void setTool(string $tool) set tool value
 * @method string getTool() get tool value
 * @method static \OrderItem[] findByTool(string $tool) find objects in database by tool
 * @method static \OrderItem findOneByTool(string $tool) find object in database by tool
 * @method static \OrderItem retrieveByTool(string $tool) retrieve object from poll by tool, get it from db if not exist in poll

 * @method void setOrderQuantity(integer $order_quantity) set order_quantity value
 * @method integer getOrderQuantity() get order_quantity value
 * @method static \OrderItem[] findByOrderQuantity(integer $order_quantity) find objects in database by order_quantity
 * @method static \OrderItem findOneByOrderQuantity(integer $order_quantity) find object in database by order_quantity
 * @method static \OrderItem retrieveByOrderQuantity(integer $order_quantity) retrieve object from poll by order_quantity, get it from db if not exist in poll

 * @method void setPendingQuantity(integer $pending_quantity) set pending_quantity value
 * @method integer getPendingQuantity() get pending_quantity value
 * @method static \OrderItem[] findByPendingQuantity(integer $pending_quantity) find objects in database by pending_quantity
 * @method static \OrderItem findOneByPendingQuantity(integer $pending_quantity) find object in database by pending_quantity
 * @method static \OrderItem retrieveByPendingQuantity(integer $pending_quantity) retrieve object from poll by pending_quantity, get it from db if not exist in poll

 * @method void setReciveQuantity(integer $recive_quantity) set recive_quantity value
 * @method integer getReciveQuantity() get recive_quantity value
 * @method static \OrderItem[] findByReciveQuantity(integer $recive_quantity) find objects in database by recive_quantity
 * @method static \OrderItem findOneByReciveQuantity(integer $recive_quantity) find object in database by recive_quantity
 * @method static \OrderItem retrieveByReciveQuantity(integer $recive_quantity) retrieve object from poll by recive_quantity, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \OrderItem[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \OrderItem findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \OrderItem retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifyTime(\Flywheel\Db\Type\DateTime $modify_time) setModifyTime(string $modify_time) set modify_time value
 * @method \Flywheel\Db\Type\DateTime getModifyTime() get modify_time value
 * @method static \OrderItem[] findByModifyTime(\Flywheel\Db\Type\DateTime $modify_time) findByModifyTime(string $modify_time) find objects in database by modify_time
 * @method static \OrderItem findOneByModifyTime(\Flywheel\Db\Type\DateTime $modify_time) findOneByModifyTime(string $modify_time) find object in database by modify_time
 * @method static \OrderItem retrieveByModifyTime(\Flywheel\Db\Type\DateTime $modify_time) retrieveByModifyTime(string $modify_time) retrieve object from poll by modify_time, get it from db if not exist in poll


 */
abstract class OrderItemBase extends ActiveRecord {
    protected static $_tableName = 'order_item';
    protected static $_phpName = 'OrderItem';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order_item';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'item_id' => array('name' => 'item_id',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'link' => array('name' => 'link',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'image' => array('name' => 'image',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'property' => array('name' => 'property',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(200)',
                'length' => 200),
        'property_translated' => array('name' => 'property_translated',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'outer_id' => array('name' => 'outer_id',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'price' => array('name' => 'price',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'price_origin' => array('name' => 'price_origin',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'price_promotion' => array('name' => 'price_promotion',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'price_table' => array('name' => 'price_table',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'weight' => array('name' => 'weight',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(5,2)',
                'length' => 5),
        'step' => array('name' => 'step',
                'default' => 1,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'require_min' => array('name' => 'require_min',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'stock' => array('name' => 'stock',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'source' => array('name' => 'source',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'note_system' => array('name' => 'note_system',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'is_paied' => array('name' => 'is_paied',
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(2)',
                'length' => 1),
        'tool' => array('name' => 'tool',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(50)',
                'length' => 50),
        'order_quantity' => array('name' => 'order_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'pending_quantity' => array('name' => 'pending_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'recive_quantity' => array('name' => 'recive_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'modify_time' => array('name' => 'modify_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','item_id','order_id','title','link','image','property','property_translated','outer_id','price','price_origin','price_promotion','price_table','weight','step','require_min','stock','source','note','note_system','is_paied','tool','order_quantity','pending_quantity','recive_quantity','created_time','modify_time');

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