<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * CommunicationPosts
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
 * @property string $rate_detail rate_detail type : varchar(200) max_length : 200
 * @property string $rate_voters rate_voters type : varchar(500) max_length : 500
 * @property string $rate_data rate_data type : varchar(300) max_length : 300
 * @property integer $comment_count comment_count type : int(11)
 * @property integer $status status type : tinyint(1)
 * @property datetime $created_date created_date type : datetime
 * @property datetime $modified_date modified_date type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \CommunicationPosts[] findById(integer $id) find objects in database by id
 * @method static \CommunicationPosts findOneById(integer $id) find object in database by id
 * @method static \CommunicationPosts retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \CommunicationPosts[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \CommunicationPosts findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \CommunicationPosts retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setCategoryId(integer $category_id) set category_id value
 * @method integer getCategoryId() get category_id value
 * @method static \CommunicationPosts[] findByCategoryId(integer $category_id) find objects in database by category_id
 * @method static \CommunicationPosts findOneByCategoryId(integer $category_id) find object in database by category_id
 * @method static \CommunicationPosts retrieveByCategoryId(integer $category_id) retrieve object from poll by category_id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \CommunicationPosts[] findByTitle(string $title) find objects in database by title
 * @method static \CommunicationPosts findOneByTitle(string $title) find object in database by title
 * @method static \CommunicationPosts retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setAlias(string $alias) set alias value
 * @method string getAlias() get alias value
 * @method static \CommunicationPosts[] findByAlias(string $alias) find objects in database by alias
 * @method static \CommunicationPosts findOneByAlias(string $alias) find object in database by alias
 * @method static \CommunicationPosts retrieveByAlias(string $alias) retrieve object from poll by alias, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \CommunicationPosts[] findByContent(string $content) find objects in database by content
 * @method static \CommunicationPosts findOneByContent(string $content) find object in database by content
 * @method static \CommunicationPosts retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setImages(string $images) set images value
 * @method string getImages() get images value
 * @method static \CommunicationPosts[] findByImages(string $images) find objects in database by images
 * @method static \CommunicationPosts findOneByImages(string $images) find object in database by images
 * @method static \CommunicationPosts retrieveByImages(string $images) retrieve object from poll by images, get it from db if not exist in poll

 * @method void setRateAvg(integer $rate_avg) set rate_avg value
 * @method integer getRateAvg() get rate_avg value
 * @method static \CommunicationPosts[] findByRateAvg(integer $rate_avg) find objects in database by rate_avg
 * @method static \CommunicationPosts findOneByRateAvg(integer $rate_avg) find object in database by rate_avg
 * @method static \CommunicationPosts retrieveByRateAvg(integer $rate_avg) retrieve object from poll by rate_avg, get it from db if not exist in poll

 * @method void setRateTotal(integer $rate_total) set rate_total value
 * @method integer getRateTotal() get rate_total value
 * @method static \CommunicationPosts[] findByRateTotal(integer $rate_total) find objects in database by rate_total
 * @method static \CommunicationPosts findOneByRateTotal(integer $rate_total) find object in database by rate_total
 * @method static \CommunicationPosts retrieveByRateTotal(integer $rate_total) retrieve object from poll by rate_total, get it from db if not exist in poll

 * @method void setRateScore(integer $rate_score) set rate_score value
 * @method integer getRateScore() get rate_score value
 * @method static \CommunicationPosts[] findByRateScore(integer $rate_score) find objects in database by rate_score
 * @method static \CommunicationPosts findOneByRateScore(integer $rate_score) find object in database by rate_score
 * @method static \CommunicationPosts retrieveByRateScore(integer $rate_score) retrieve object from poll by rate_score, get it from db if not exist in poll

 * @method void setRateDetail(string $rate_detail) set rate_detail value
 * @method string getRateDetail() get rate_detail value
 * @method static \CommunicationPosts[] findByRateDetail(string $rate_detail) find objects in database by rate_detail
 * @method static \CommunicationPosts findOneByRateDetail(string $rate_detail) find object in database by rate_detail
 * @method static \CommunicationPosts retrieveByRateDetail(string $rate_detail) retrieve object from poll by rate_detail, get it from db if not exist in poll

 * @method void setRateVoters(string $rate_voters) set rate_voters value
 * @method string getRateVoters() get rate_voters value
 * @method static \CommunicationPosts[] findByRateVoters(string $rate_voters) find objects in database by rate_voters
 * @method static \CommunicationPosts findOneByRateVoters(string $rate_voters) find object in database by rate_voters
 * @method static \CommunicationPosts retrieveByRateVoters(string $rate_voters) retrieve object from poll by rate_voters, get it from db if not exist in poll

 * @method void setRateData(string $rate_data) set rate_data value
 * @method string getRateData() get rate_data value
 * @method static \CommunicationPosts[] findByRateData(string $rate_data) find objects in database by rate_data
 * @method static \CommunicationPosts findOneByRateData(string $rate_data) find object in database by rate_data
 * @method static \CommunicationPosts retrieveByRateData(string $rate_data) retrieve object from poll by rate_data, get it from db if not exist in poll

 * @method void setCommentCount(integer $comment_count) set comment_count value
 * @method integer getCommentCount() get comment_count value
 * @method static \CommunicationPosts[] findByCommentCount(integer $comment_count) find objects in database by comment_count
 * @method static \CommunicationPosts findOneByCommentCount(integer $comment_count) find object in database by comment_count
 * @method static \CommunicationPosts retrieveByCommentCount(integer $comment_count) retrieve object from poll by comment_count, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \CommunicationPosts[] findByStatus(integer $status) find objects in database by status
 * @method static \CommunicationPosts findOneByStatus(integer $status) find object in database by status
 * @method static \CommunicationPosts retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreatedDate(\Flywheel\Db\Type\DateTime $created_date) setCreatedDate(string $created_date) set created_date value
 * @method \Flywheel\Db\Type\DateTime getCreatedDate() get created_date value
 * @method static \CommunicationPosts[] findByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) findByCreatedDate(string $created_date) find objects in database by created_date
 * @method static \CommunicationPosts findOneByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) findOneByCreatedDate(string $created_date) find object in database by created_date
 * @method static \CommunicationPosts retrieveByCreatedDate(\Flywheel\Db\Type\DateTime $created_date) retrieveByCreatedDate(string $created_date) retrieve object from poll by created_date, get it from db if not exist in poll

 * @method void setModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) setModifiedDate(string $modified_date) set modified_date value
 * @method \Flywheel\Db\Type\DateTime getModifiedDate() get modified_date value
 * @method static \CommunicationPosts[] findByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) findByModifiedDate(string $modified_date) find objects in database by modified_date
 * @method static \CommunicationPosts findOneByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) findOneByModifiedDate(string $modified_date) find object in database by modified_date
 * @method static \CommunicationPosts retrieveByModifiedDate(\Flywheel\Db\Type\DateTime $modified_date) retrieveByModifiedDate(string $modified_date) retrieve object from poll by modified_date, get it from db if not exist in poll


 */
abstract class CommunicationPostsBase extends ActiveRecord {
    protected static $_tableName = 'communication_posts';
    protected static $_phpName = 'CommunicationPosts';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'communication_posts';
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
                'db_type' => 'varchar(200)',
                'length' => 200),
        'rate_voters' => array('name' => 'rate_voters',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(500)',
                'length' => 500),
        'rate_data' => array('name' => 'rate_data',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(300)',
                'length' => 300),
        'comment_count' => array('name' => 'comment_count',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'status' => array('name' => 'status',
                'default' => 1,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
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
    protected static $_cols = array('id','user_id','category_id','title','alias','content','images','rate_avg','rate_total','rate_score','rate_detail','rate_voters','rate_data','comment_count','status','created_date','modified_date');

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