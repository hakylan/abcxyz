<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * EmailBanned
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $email email type : varchar(255) max_length : 255
 * @property datetime $created_time created_time type : timestamp

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \EmailBanned[] findById(integer $id) find objects in database by id
 * @method static \EmailBanned findOneById(integer $id) find object in database by id
 * @method static \EmailBanned retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setEmail(string $email) set email value
 * @method string getEmail() get email value
 * @method static \EmailBanned[] findByEmail(string $email) find objects in database by email
 * @method static \EmailBanned findOneByEmail(string $email) find object in database by email
 * @method static \EmailBanned retrieveByEmail(string $email) retrieve object from poll by email, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \EmailBanned[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \EmailBanned findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \EmailBanned retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll


 */
abstract class EmailBannedBase extends ActiveRecord {
    protected static $_tableName = 'email_banned';
    protected static $_phpName = 'EmailBanned';
    protected static $_pk = 'id';
    protected static $_alias = 'e';
    protected static $_dbConnectName = 'email_banned';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'email' => array('name' => 'email',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'created_time' => array('name' => 'created_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'timestamp'),
     );
    protected static $_validate = array(
        'email' => array(
            array('name' => 'Unique',
                'message'=> 'email\'s was used'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'email' => array(
            array('name' => 'Unique',
                'message'=> 'email\'s was used'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','email','created_time');

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