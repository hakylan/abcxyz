<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * CommunicationPostCategories
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $parent_id parent_id type : int(3)
 * @property string $title title type : varchar(250) max_length : 250
 * @property string $slug slug type : varchar(250) max_length : 250
 * @property string $description description type : text max_length : 
 * @property integer $post_count post_count type : int(5)
 * @property integer $order order type : int(3)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \CommunicationPostCategories[] findById(integer $id) find objects in database by id
 * @method static \CommunicationPostCategories findOneById(integer $id) find object in database by id
 * @method static \CommunicationPostCategories retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setParentId(integer $parent_id) set parent_id value
 * @method integer getParentId() get parent_id value
 * @method static \CommunicationPostCategories[] findByParentId(integer $parent_id) find objects in database by parent_id
 * @method static \CommunicationPostCategories findOneByParentId(integer $parent_id) find object in database by parent_id
 * @method static \CommunicationPostCategories retrieveByParentId(integer $parent_id) retrieve object from poll by parent_id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \CommunicationPostCategories[] findByTitle(string $title) find objects in database by title
 * @method static \CommunicationPostCategories findOneByTitle(string $title) find object in database by title
 * @method static \CommunicationPostCategories retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setSlug(string $slug) set slug value
 * @method string getSlug() get slug value
 * @method static \CommunicationPostCategories[] findBySlug(string $slug) find objects in database by slug
 * @method static \CommunicationPostCategories findOneBySlug(string $slug) find object in database by slug
 * @method static \CommunicationPostCategories retrieveBySlug(string $slug) retrieve object from poll by slug, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \CommunicationPostCategories[] findByDescription(string $description) find objects in database by description
 * @method static \CommunicationPostCategories findOneByDescription(string $description) find object in database by description
 * @method static \CommunicationPostCategories retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setPostCount(integer $post_count) set post_count value
 * @method integer getPostCount() get post_count value
 * @method static \CommunicationPostCategories[] findByPostCount(integer $post_count) find objects in database by post_count
 * @method static \CommunicationPostCategories findOneByPostCount(integer $post_count) find object in database by post_count
 * @method static \CommunicationPostCategories retrieveByPostCount(integer $post_count) retrieve object from poll by post_count, get it from db if not exist in poll

 * @method void setOrder(integer $order) set order value
 * @method integer getOrder() get order value
 * @method static \CommunicationPostCategories[] findByOrder(integer $order) find objects in database by order
 * @method static \CommunicationPostCategories findOneByOrder(integer $order) find object in database by order
 * @method static \CommunicationPostCategories retrieveByOrder(integer $order) retrieve object from poll by order, get it from db if not exist in poll


 */
abstract class CommunicationPostCategoriesBase extends ActiveRecord {
    protected static $_tableName = 'communication_post_categories';
    protected static $_phpName = 'CommunicationPostCategories';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'communication_post_categories';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'parent_id' => array('name' => 'parent_id',
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(3)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(250)',
                'length' => 250),
        'slug' => array('name' => 'slug',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(250)',
                'length' => 250),
        'description' => array('name' => 'description',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'post_count' => array('name' => 'post_count',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(5)',
                'length' => 4),
        'order' => array('name' => 'order',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(3)',
                'length' => 4),
     );
    protected static $_validate = array(
        'slug' => array(
            array('name' => 'Unique',
                'message'=> 'slug\'s was used'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'slug' => array(
            array('name' => 'Unique',
                'message'=> 'slug\'s was used'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','parent_id','title','slug','description','post_count','order');

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