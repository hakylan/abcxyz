<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * UserRoles
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $role_id role_id type : int(11)
 * @property datetime $assign_time assign_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \UserRoles[] findById(integer $id) find objects in database by id
 * @method static \UserRoles findOneById(integer $id) find object in database by id
 * @method static \UserRoles retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \UserRoles[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \UserRoles findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \UserRoles retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setRoleId(integer $role_id) set role_id value
 * @method integer getRoleId() get role_id value
 * @method static \UserRoles[] findByRoleId(integer $role_id) find objects in database by role_id
 * @method static \UserRoles findOneByRoleId(integer $role_id) find object in database by role_id
 * @method static \UserRoles retrieveByRoleId(integer $role_id) retrieve object from poll by role_id, get it from db if not exist in poll

 * @method void setAssignTime(\Flywheel\Db\Type\DateTime $assign_time) setAssignTime(string $assign_time) set assign_time value
 * @method \Flywheel\Db\Type\DateTime getAssignTime() get assign_time value
 * @method static \UserRoles[] findByAssignTime(\Flywheel\Db\Type\DateTime $assign_time) findByAssignTime(string $assign_time) find objects in database by assign_time
 * @method static \UserRoles findOneByAssignTime(\Flywheel\Db\Type\DateTime $assign_time) findOneByAssignTime(string $assign_time) find object in database by assign_time
 * @method static \UserRoles retrieveByAssignTime(\Flywheel\Db\Type\DateTime $assign_time) retrieveByAssignTime(string $assign_time) retrieve object from poll by assign_time, get it from db if not exist in poll


 */
abstract class UserRolesBase extends ActiveRecord {
    protected static $_tableName = 'user_roles';
    protected static $_phpName = 'UserRoles';
    protected static $_pk = 'id';
    protected static $_alias = 'u';
    protected static $_dbConnectName = 'user_roles';
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
        'role_id' => array('name' => 'role_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'assign_time' => array('name' => 'assign_time',
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
    protected static $_cols = array('id','user_id','role_id','assign_time');

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