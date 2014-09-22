<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * PointMemberExchangeSetting
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $service_code service_code type : varchar(50) max_length : 50
 * @property number $value value type : double
 * @property string $type type type : enum('RATE') max_length : 4
 * @property string $note note type : text max_length : 

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \PointMemberExchangeSetting[] findById(integer $id) find objects in database by id
 * @method static \PointMemberExchangeSetting findOneById(integer $id) find object in database by id
 * @method static \PointMemberExchangeSetting retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setServiceCode(string $service_code) set service_code value
 * @method string getServiceCode() get service_code value
 * @method static \PointMemberExchangeSetting[] findByServiceCode(string $service_code) find objects in database by service_code
 * @method static \PointMemberExchangeSetting findOneByServiceCode(string $service_code) find object in database by service_code
 * @method static \PointMemberExchangeSetting retrieveByServiceCode(string $service_code) retrieve object from poll by service_code, get it from db if not exist in poll

 * @method void setValue(number $value) set value value
 * @method number getValue() get value value
 * @method static \PointMemberExchangeSetting[] findByValue(number $value) find objects in database by value
 * @method static \PointMemberExchangeSetting findOneByValue(number $value) find object in database by value
 * @method static \PointMemberExchangeSetting retrieveByValue(number $value) retrieve object from poll by value, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \PointMemberExchangeSetting[] findByType(string $type) find objects in database by type
 * @method static \PointMemberExchangeSetting findOneByType(string $type) find object in database by type
 * @method static \PointMemberExchangeSetting retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \PointMemberExchangeSetting[] findByNote(string $note) find objects in database by note
 * @method static \PointMemberExchangeSetting findOneByNote(string $note) find object in database by note
 * @method static \PointMemberExchangeSetting retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll


 */
abstract class PointMemberExchangeSettingBase extends ActiveRecord {
    protected static $_tableName = 'point_member_exchange_setting';
    protected static $_phpName = 'PointMemberExchangeSetting';
    protected static $_pk = 'id';
    protected static $_alias = 'p';
    protected static $_dbConnectName = 'point_member_exchange_setting';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'service_code' => array('name' => 'service_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'value' => array('name' => 'value',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'type' => array('name' => 'type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'RATE\')',
                'length' => 4),
        'note' => array('name' => 'note',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
     );
    protected static $_validate = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'RATE',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'RATE',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','service_code','value','type','note');

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