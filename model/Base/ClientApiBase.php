<?php
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ClientApi
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property integer $client_id client_id type : int(11)
 * @property string $scope scope type : varchar(300) max_length : 300
 * @property string $refresh_token refresh_token type : varchar(300) max_length : 300
 * @property string $deviceid deviceid type : varchar(50) max_length : 50
 * @property string $gcm_reg_id gcm_reg_id type : varchar(300) max_length : 300

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ClientApi[] findById(integer $id) find objects in database by id
 * @method static \ClientApi findOneById(integer $id) find object in database by id
 * @method static \ClientApi retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \ClientApi[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \ClientApi findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \ClientApi retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setClientId(integer $client_id) set client_id value
 * @method integer getClientId() get client_id value
 * @method static \ClientApi[] findByClientId(integer $client_id) find objects in database by client_id
 * @method static \ClientApi findOneByClientId(integer $client_id) find object in database by client_id
 * @method static \ClientApi retrieveByClientId(integer $client_id) retrieve object from poll by client_id, get it from db if not exist in poll

 * @method void setScope(string $scope) set scope value
 * @method string getScope() get scope value
 * @method static \ClientApi[] findByScope(string $scope) find objects in database by scope
 * @method static \ClientApi findOneByScope(string $scope) find object in database by scope
 * @method static \ClientApi retrieveByScope(string $scope) retrieve object from poll by scope, get it from db if not exist in poll

 * @method void setRefreshToken(string $refresh_token) set refresh_token value
 * @method string getRefreshToken() get refresh_token value
 * @method static \ClientApi[] findByRefreshToken(string $refresh_token) find objects in database by refresh_token
 * @method static \ClientApi findOneByRefreshToken(string $refresh_token) find object in database by refresh_token
 * @method static \ClientApi retrieveByRefreshToken(string $refresh_token) retrieve object from poll by refresh_token, get it from db if not exist in poll

 * @method void setDeviceid(string $deviceid) set deviceid value
 * @method string getDeviceid() get deviceid value
 * @method static \ClientApi[] findByDeviceid(string $deviceid) find objects in database by deviceid
 * @method static \ClientApi findOneByDeviceid(string $deviceid) find object in database by deviceid
 * @method static \ClientApi retrieveByDeviceid(string $deviceid) retrieve object from poll by deviceid, get it from db if not exist in poll

 * @method void setGcmRegId(string $gcm_reg_id) set gcm_reg_id value
 * @method string getGcmRegId() get gcm_reg_id value
 * @method static \ClientApi[] findByGcmRegId(string $gcm_reg_id) find objects in database by gcm_reg_id
 * @method static \ClientApi findOneByGcmRegId(string $gcm_reg_id) find object in database by gcm_reg_id
 * @method static \ClientApi retrieveByGcmRegId(string $gcm_reg_id) retrieve object from poll by gcm_reg_id, get it from db if not exist in poll


 */
abstract class ClientApiBase extends ActiveRecord {
    protected static $_tableName = 'client_api';
    protected static $_phpName = 'ClientApi';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'client_api';
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
        'client_id' => array('name' => 'client_id',
            'not_null' => false,
            'type' => 'integer',
            'auto_increment' => false,
            'db_type' => 'int(11)',
            'length' => 4),
        'scope' => array('name' => 'scope',
            'not_null' => false,
            'type' => 'string',
            'db_type' => 'varchar(300)',
            'length' => 300),
        'refresh_token' => array('name' => 'refresh_token',
            'not_null' => false,
            'type' => 'string',
            'db_type' => 'varchar(300)',
            'length' => 300),
        'deviceid' => array('name' => 'deviceid',
            'not_null' => false,
            'type' => 'string',
            'db_type' => 'varchar(50)',
            'length' => 50),
        'gcm_reg_id' => array('name' => 'gcm_reg_id',
            'not_null' => false,
            'type' => 'string',
            'db_type' => 'varchar(300)',
            'length' => 300),
    );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','client_id','scope','refresh_token','deviceid','gcm_reg_id');

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