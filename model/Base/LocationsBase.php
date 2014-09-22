<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Locations
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(10) unsigned
 * @property string $label label type : varchar(150) max_length : 150
 * @property string $key_code key_code type : char(5) max_length : 5
 * @property string $type type type : enum('COUNTRY','STATE','DISTRICT','VILLAGE') max_length : 8
 * @property integer $parent_id parent_id type : int(11)
 * @property integer $status status type : tinyint(1)
 * @property string $CODE CODE type : varchar(20) max_length : 20
 * @property integer $can_delete can_delete type : tinyint(1)
 * @property integer $ordering ordering type : int(11)
 * @property string $logistic_code logistic_code type : char(5) max_length : 5

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Locations[] findById(integer $id) find objects in database by id
 * @method static \Locations findOneById(integer $id) find object in database by id
 * @method static \Locations retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setLabel(string $label) set label value
 * @method string getLabel() get label value
 * @method static \Locations[] findByLabel(string $label) find objects in database by label
 * @method static \Locations findOneByLabel(string $label) find object in database by label
 * @method static \Locations retrieveByLabel(string $label) retrieve object from poll by label, get it from db if not exist in poll

 * @method void setKeyCode(string $key_code) set key_code value
 * @method string getKeyCode() get key_code value
 * @method static \Locations[] findByKeyCode(string $key_code) find objects in database by key_code
 * @method static \Locations findOneByKeyCode(string $key_code) find object in database by key_code
 * @method static \Locations retrieveByKeyCode(string $key_code) retrieve object from poll by key_code, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \Locations[] findByType(string $type) find objects in database by type
 * @method static \Locations findOneByType(string $type) find object in database by type
 * @method static \Locations retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setParentId(integer $parent_id) set parent_id value
 * @method integer getParentId() get parent_id value
 * @method static \Locations[] findByParentId(integer $parent_id) find objects in database by parent_id
 * @method static \Locations findOneByParentId(integer $parent_id) find object in database by parent_id
 * @method static \Locations retrieveByParentId(integer $parent_id) retrieve object from poll by parent_id, get it from db if not exist in poll

 * @method void setStatus(integer $status) set status value
 * @method integer getStatus() get status value
 * @method static \Locations[] findByStatus(integer $status) find objects in database by status
 * @method static \Locations findOneByStatus(integer $status) find object in database by status
 * @method static \Locations retrieveByStatus(integer $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setCODE(string $CODE) set CODE value
 * @method string getCODE() get CODE value
 * @method static \Locations[] findByCODE(string $CODE) find objects in database by CODE
 * @method static \Locations findOneByCODE(string $CODE) find object in database by CODE
 * @method static \Locations retrieveByCODE(string $CODE) retrieve object from poll by CODE, get it from db if not exist in poll

 * @method void setCanDelete(integer $can_delete) set can_delete value
 * @method integer getCanDelete() get can_delete value
 * @method static \Locations[] findByCanDelete(integer $can_delete) find objects in database by can_delete
 * @method static \Locations findOneByCanDelete(integer $can_delete) find object in database by can_delete
 * @method static \Locations retrieveByCanDelete(integer $can_delete) retrieve object from poll by can_delete, get it from db if not exist in poll

 * @method void setOrdering(integer $ordering) set ordering value
 * @method integer getOrdering() get ordering value
 * @method static \Locations[] findByOrdering(integer $ordering) find objects in database by ordering
 * @method static \Locations findOneByOrdering(integer $ordering) find object in database by ordering
 * @method static \Locations retrieveByOrdering(integer $ordering) retrieve object from poll by ordering, get it from db if not exist in poll

 * @method void setLogisticCode(string $logistic_code) set logistic_code value
 * @method string getLogisticCode() get logistic_code value
 * @method static \Locations[] findByLogisticCode(string $logistic_code) find objects in database by logistic_code
 * @method static \Locations findOneByLogisticCode(string $logistic_code) find object in database by logistic_code
 * @method static \Locations retrieveByLogisticCode(string $logistic_code) retrieve object from poll by logistic_code, get it from db if not exist in poll


 */
abstract class LocationsBase extends ActiveRecord {
    protected static $_tableName = 'locations';
    protected static $_phpName = 'Locations';
    protected static $_pk = 'id';
    protected static $_alias = 'l';
    protected static $_dbConnectName = 'locations';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(10) unsigned',
                'length' => 4),
        'label' => array('name' => 'label',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(150)',
                'length' => 150),
        'key_code' => array('name' => 'key_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(5)',
                'length' => 5),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'COUNTRY\',\'STATE\',\'DISTRICT\',\'VILLAGE\')',
                'length' => 8),
        'parent_id' => array('name' => 'parent_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'CODE' => array('name' => 'CODE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'can_delete' => array('name' => 'can_delete',
                'default' => 1,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'ordering' => array('name' => 'ordering',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'logistic_code' => array('name' => 'logistic_code',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'char(5)',
                'length' => 5),
     );
    protected static $_validate = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'COUNTRY|STATE|DISTRICT|VILLAGE',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'COUNTRY|STATE|DISTRICT|VILLAGE',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','label','key_code','type','parent_id','status','CODE','can_delete','ordering','logistic_code');

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