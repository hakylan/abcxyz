<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * SystemConfig
 * @version		$Id$
 * @package		Model

 * @property string $config_key config_key primary type : varchar(255) max_length : 255
 * @property string $config_value config_value type : text max_length : 

 * @method void setConfigKey(string $config_key) set config_key value
 * @method string getConfigKey() get config_key value
 * @method static \SystemConfig[] findByConfigKey(string $config_key) find objects in database by config_key
 * @method static \SystemConfig findOneByConfigKey(string $config_key) find object in database by config_key
 * @method static \SystemConfig retrieveByConfigKey(string $config_key) retrieve object from poll by config_key, get it from db if not exist in poll

 * @method void setConfigValue(string $config_value) set config_value value
 * @method string getConfigValue() get config_value value
 * @method static \SystemConfig[] findByConfigValue(string $config_value) find objects in database by config_value
 * @method static \SystemConfig findOneByConfigValue(string $config_value) find object in database by config_value
 * @method static \SystemConfig retrieveByConfigValue(string $config_value) retrieve object from poll by config_value, get it from db if not exist in poll


 */
abstract class SystemConfigBase extends ActiveRecord {
    protected static $_tableName = 'system_config';
    protected static $_phpName = 'SystemConfig';
    protected static $_pk = 'config_key';
    protected static $_alias = 's';
    protected static $_dbConnectName = 'system_config';
    protected static $_instances = array();
    protected static $_schema = array(
        'config_key' => array('name' => 'config_key',
                'not_null' => true,
                'type' => 'string',
                'primary' => true,
                'db_type' => 'varchar(255)',
                'length' => 255),
        'config_value' => array('name' => 'config_value',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('config_key','config_value');

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