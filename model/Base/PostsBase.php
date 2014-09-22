<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Posts
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $category_id category_id type : int(11)
 * @property string $title title type : varchar(250) max_length : 250
 * @property string $alias alias type : varchar(250) max_length : 250
 * @property string $content content type : text max_length : 
 * @property string $images images type : varchar(1000) max_length : 1000
 * @property integer $rate_avg rate_avg type : int(5)
 * @property integer $rate_total rate_total type : int(5)
 * @property integer $rate_score rate_score type : int(5)
 * @property string $rate_detail rate_detail type : varchar(20) max_length : 20
 * @property datetime $created_date created_date type : datetime
 * @property datetime $modified_date modified_date type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Posts[] findById(integer $id) find objects in database by id
 * @method static \Posts findOneById(integer $id) find object in database by id
 * @method static \Posts retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \Posts[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \Posts findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \Posts retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setCategoryId(integer $category_id) set category_id value
 * @method integer getCategoryId() get category_id value
 * @method static \Posts[] findByCategoryId(integer $category_id) find objects in database by category_id
 * @method static \Posts findOneByCategoryId(integer $category_id) find object in database by category_id
 * @method static \Posts retrieveByCategoryId(integer $category_id) retrieve object from poll by category_id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \Posts[] findByTitle(string $title) find objects in database by title
 * @method static \Posts findOneByTitle(string $title) find object in database by title
 * @method static \Posts retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setAlias(string $alias) set alias value
 * @method string getAlias() get alias value
 * @method static \Posts[] findByAlias(string $alias) find objects in database by alias
 * @method static \Posts findOneByAlias(string $alias) find object in database by alias
 * @method static \Posts retrieveByAlias(string $alias) retrieve object from poll by alias, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \Posts[] findByContent(string $content) find objects in database by content
 * @method static \Posts findOneByContent(string $content) find object in database by content
 * @method static \Posts retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setImages(string $images) set images value
 * @method string getImages() get images value
 * @method static \Posts[] findByImages(string $images) find objects in database by images
 * @method static \Posts findOneByImages(string $images) find object in database by images
 * @method static \Posts retrieveByImages(string $images) retrieve object from poll by images, get it from db if not exist in poll

 * @method void setRateAvg(integer $rate_avg) set rate_avg value
 * @method integer getRateAvg() get rate_avg value
 * @method static \Posts[] findByRateAvg(integer $rate_avg) find objects in database by rate_avg
 * @method static \Posts findOneByRateAvg(integer $rate_avg) find object in database by rate_avg
 * @method static \Posts retrieveByRateAvg(integer $rate_avg) retrieve object from poll by rate_avg, get it from db if not exist in poll

 * @method void setRateTotal(integer $rate_total) set rate_total value
 * @method integer getRateTotal() get rate_total value
 * @method static \Posts[] findByRateTotal(integer $rate_total) find objects in database by rate_total
 * @method static \Posts findOneByRateTotal(integer $rate_total) find object in database by rate_total
 * @method static \Posts retrieveByRateTotal(integer $rate_total) retrieve object from poll by rate_total, get it from db if not exist in poll

 * @method void setRateScore(integer $rate_score) set rate_score value
 * @method integer getRateScore() get rate_score value
 * @method static \Posts[] findByRateScore(integer $rate_score) find objects in database by rate_score
 * @method static \Posts findOneByRateScore(integer $rate_score) find object in database by rate_score
 * @method static \Posts retrieveByRateScore(integer $rate_score) retrieve object from poll by rate_score, get it from db if not exist in poll

 * @method void setRateDetail(string $rate_detail) set rate_detail value
 * @method string getRateDetail() get rate_detail value
 * @method static \Posts[] findByRateDetail(string $rate_detail) find objects in database by rate_detail
 * @method static \Posts findOneByRateDetail(string $rate_detail) find object in database by rate_detail
 * @method static \Posts retrieveByRateDetail(string $rate_detail) retrieve object from poll by rate_detail, get it from db if not exist in poll

 * @method void setCreatedDate(\Flywheel\Db\Type\DateTime $created_date) setCreatedDate(string $created_date) set created_date value
 * @method \Flywheel\Db\Type\DateTime getCreatedDate() get created_date value
 * @method static \Posts[] findByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) findByCreatedDate(string $created_date) find objects in database by created_date
 * @method static \Posts findOneByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) findOneByCreatedDate(string $created_date) find object in database by created_date
 * @method static \Posts retrieveByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) retrieveByCreatedDate(string $created_date) retrieve object from poll by created_date, get it from db if not exist in poll

 * @method void setModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) setModifiedDate(string $modified_date) set modified_date value
 * @method \Flywheel\Db\Type\DateTime getModifiedDate() get modified_date value
 * @method static \Posts[] findByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) findByModifiedDate(string $modified_date) find objects in database by modified_date
 * @method static \Posts findOneByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) findOneByModifiedDate(string $modified_date) find object in database by modified_date
 * @method static \Posts retrieveByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) retrieveByModifiedDate(string $modified_date) retrieve object from poll by modified_date, get it from db if not exist in poll


 */
abstract class PostsBase extends ActiveRecord {
    protected static $_tableName = 'posts';
    protected static $_phpName = 'Posts';
    protected static $_pk = 'id';
    protected static $_alias = 'p';
    protected static $_dbConnectName = 'posts';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'category_id' => array('name' => 'category_id',
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(250)',
                'length' => 250),
        'alias' => array('name' => 'alias',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(250)',
                'length' => 250),
        'content' => array('name' => 'content',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'images' => array('name' => 'images',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(1000)',
                'length' => 1000),
        'rate_avg' => array('name' => 'rate_avg',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(5)',
                'length' => 4),
        'rate_total' => array('name' => 'rate_total',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(5)',
                'length' => 4),
        'rate_score' => array('name' => 'rate_score',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(5)',
                'length' => 4),
        'rate_detail' => array('name' => 'rate_detail',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'created_date' => array('name' => 'created_date',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'modified_date' => array('name' => 'modified_date',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','category_id','title','alias','content','images','rate_avg','rate_total','rate_score','rate_detail','created_date','modified_date');

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