<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * FavoriteItem
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $item_id item_id type : int(11)
 * @property string $homeland homeland type : enum('EELLY','TAOBAO','1688','') max_length : 6
 * @property string $title title type : char(150) max_length : 150
 * @property string $link link type : varchar(150) max_length : 150
 * @property string $image image type : char(150) max_length : 150
 * @property string $props props type : char(255) max_length : 255
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \FavoriteItem[] findById(integer $id) find objects in database by id
 * @method static \FavoriteItem findOneById(integer $id) find object in database by id
 * @method static \FavoriteItem retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \FavoriteItem[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \FavoriteItem findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \FavoriteItem retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setItemId(integer $item_id) set item_id value
 * @method integer getItemId() get item_id value
 * @method static \FavoriteItem[] findByItemId(integer $item_id) find objects in database by item_id
 * @method static \FavoriteItem findOneByItemId(integer $item_id) find object in database by item_id
 * @method static \FavoriteItem retrieveByItemId(integer $item_id) retrieve object from poll by item_id, get it from db if not exist in poll

 * @method void setHomeland(string $homeland) set homeland value
 * @method string getHomeland() get homeland value
 * @method static \FavoriteItem[] findByHomeland(string $homeland) find objects in database by homeland
 * @method static \FavoriteItem findOneByHomeland(string $homeland) find object in database by homeland
 * @method static \FavoriteItem retrieveByHomeland(string $homeland) retrieve object from poll by homeland, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \FavoriteItem[] findByTitle(string $title) find objects in database by title
 * @method static \FavoriteItem findOneByTitle(string $title) find object in database by title
 * @method static \FavoriteItem retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setLink(string $link) set link value
 * @method string getLink() get link value
 * @method static \FavoriteItem[] findByLink(string $link) find objects in database by link
 * @method static \FavoriteItem findOneByLink(string $link) find object in database by link
 * @method static \FavoriteItem retrieveByLink(string $link) retrieve object from poll by link, get it from db if not exist in poll

 * @method void setImage(string $image) set image value
 * @method string getImage() get image value
 * @method static \FavoriteItem[] findByImage(string $image) find objects in database by image
 * @method static \FavoriteItem findOneByImage(string $image) find object in database by image
 * @method static \FavoriteItem retrieveByImage(string $image) retrieve object from poll by image, get it from db if not exist in poll

 * @method void setProps(string $props) set props value
 * @method string getProps() get props value
 * @method static \FavoriteItem[] findByProps(string $props) find objects in database by props
 * @method static \FavoriteItem findOneByProps(string $props) find object in database by props
 * @method static \FavoriteItem retrieveByProps(string $props) retrieve object from poll by props, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \FavoriteItem[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \FavoriteItem findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \FavoriteItem retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class FavoriteItemBase extends ActiveRecord {
    protected static $_tableName = 'favorite_item';
    protected static $_phpName = 'FavoriteItem';
    protected static $_pk = 'id';
    protected static $_alias = 'f';
    protected static $_dbConnectName = 'favorite_item';
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
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'item_id' => array('name' => 'item_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'homeland' => array('name' => 'homeland',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'EELLY\',\'TAOBAO\',\'1688\',\'\')',
                'length' => 6),
        'title' => array('name' => 'title',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(150)',
                'length' => 150),
        'link' => array('name' => 'link',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(150)',
                'length' => 150),
        'image' => array('name' => 'image',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(150)',
                'length' => 150),
        'props' => array('name' => 'props',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(255)',
                'length' => 255),
        'create_time' => array('name' => 'create_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'homeland' => array(
            array('name' => 'ValidValues',
                'value' => 'EELLY|TAOBAO|1688|',
                'message'=> 'homeland\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'homeland' => array(
            array('name' => 'ValidValues',
                'value' => 'EELLY|TAOBAO|1688|',
                'message'=> 'homeland\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','item_id','homeland','title','link','image','props','create_time');

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