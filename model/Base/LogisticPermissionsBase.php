<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * LogisticPermissions
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $code code type : varchar(50) max_length : 50
 * @property string $label label type : varchar(255) max_length : 255
 * @property integer $role_id role_id type : int(11)
 * @property integer $on on type : tinyint(4)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \LogisticPermissions[] findById(integer $id) find objects in database by id
 * @method static \LogisticPermissions findOneById(integer $id) find object in database by id
 * @method static \LogisticPermissions retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCode(string $code) set code value
 * @method string getCode() get code value
 * @method static \LogisticPermissions[] findByCode(string $code) find objects in database by code
 * @method static \LogisticPermissions findOneByCode(string $code) find object in database by code
 * @method static \LogisticPermissions retrieveByCode(string $code) retrieve object from poll by code, get it from db if not exist in poll

 * @method void setLabel(string $label) set label value
 * @method string getLabel() get label value
 * @method static \LogisticPermissions[] findByLabel(string $label) find objects in database by label
 * @method static \LogisticPermissions findOneByLabel(string $label) find object in database by label
 * @method static \LogisticPermissions retrieveByLabel(string $label) retrieve object from poll by label, get it from db if not exist in poll

 * @method void setRoleId(integer $role_id) set role_id value
 * @method integer getRoleId() get role_id value
 * @method static \LogisticPermissions[] findByRoleId(integer $role_id) find objects in database by role_id
 * @method static \LogisticPermissions findOneByRoleId(integer $role_id) find object in database by role_id
 * @method static \LogisticPermissions retrieveByRoleId(integer $role_id) retrieve object from poll by role_id, get it from db if not exist in poll

 * @method void setOn(integer $on) set on value
 * @method integer getOn() get on value
 * @method static \LogisticPermissions[] findByOn(integer $on) find objects in database by on
 * @method static \LogisticPermissions findOneByOn(integer $on) find object in database by on
 * @method static \LogisticPermissions retrieveByOn(integer $on) retrieve object from poll by on, get it from db if not exist in poll


 */
abstract class LogisticPermissionsBase extends ActiveRecord {
    protected static $_tableName = 'logistic_permissions';
    protected static $_phpName = 'LogisticPermissions';
    protected static $_pk = 'id';
    protected static $_alias = 'l';
    protected static $_dbConnectName = 'logistic_permissions';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'code' => array('name' => 'code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'label' => array('name' => 'label',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'role_id' => array('name' => 'role_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'on' => array('name' => 'on',
                'default' => 1,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(4)',
                'length' => 1),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','code','label','role_id','on');

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