<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserOriginSite
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $username username type : varchar(100) max_length : 100
 * @property string $site site type : varchar(25) max_length : 25
 * @property integer $status status type : tinyint(4)
 * @property datetime $create_time create_time type : datetime
 * @property datetime $update_time update_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserOriginSite[] findById(integer $id) find objects in database by id
 * @method static \UserOriginSite findOneById(integer $id) find object in database by id
 * @method static \UserOriginSite retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUsername(string $username) set username value
 * @method string getUsername() get username value
 * @method static \UserOriginSite[] findByUsername(string $username) find objects in database by username
 * @method static \UserOriginSite findOneByUsername(string $username) find object in database by username
 * @method static \UserOriginSite retrieveByUsername(string $username) retrieve object from poll by username, get it from db if not exist in poll

 * @method void setSite(string $site) set site value
 * @method string getSite() get site value
 * @method static \UserOriginSite[] findBySite(string $site) find objects in database by site
 * @method static \UserOriginSite findOneBySite(string $site) find object in database by site
 * @method static \UserOriginSite retrieveBySite(string $site) retrieve object from poll by site, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \UserOriginSite[] findByStatus(integer $status) find objects in database by status
 * @method static \UserOriginSite findOneByStatus(integer $status) find object in database by status
 * @method static \UserOriginSite retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \UserOriginSite[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \UserOriginSite findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \UserOriginSite retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll

 * @method void setUpdateTime(\Flywheel\Db\Type\DateTime $update_time) setUpdateTime(string $update_time) set update_time value
 * @method \Flywheel\Db\Type\DateTime getUpdateTime() get update_time value
 * @method static \UserOriginSite[] findByUpdateTime(\Flywheel\Db\Type\DateTime $update_time) findByUpdateTime(string $update_time) find objects in database by update_time
 * @method static \UserOriginSite findOneByUpdateTime(\Flywheel\Db\Type\DateTime $update_time) findOneByUpdateTime(string $update_time) find object in database by update_time
 * @method static \UserOriginSite retrieveByUpdateTime(\Flywheel\Db\Type\DateTime $update_time) retrieveByUpdateTime(string $update_time) retrieve object from poll by update_time, get it from db if not exist in poll


 */
abstract class UserOriginSiteBase extends ActiveRecord {
    protected static $_tableName = 'user_origin_site';
    protected static $_phpName = 'UserOriginSite';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_origin_site';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'username' => array('name' => 'username',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'site' => array('name' => 'site',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(25)',
                'length' => 25),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(4)',
                'length' => 1),
        'create_time' => array('name' => 'create_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'update_time' => array('name' => 'update_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','username','site','status','create_time','update_time');

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