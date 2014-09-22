<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ComplaintsReasons
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property integer $complaint_id complaint_id type : int(11)
 * @property string $long_type long_type type : varchar(255) max_length : 255
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ComplaintsReasons[] findById(integer $id) find objects in database by id
 * @method static \ComplaintsReasons findOneById(integer $id) find object in database by id
 * @method static \ComplaintsReasons retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setComplaintId(integer $complaint_id) set complaint_id value
 * @method integer getComplaintId() get complaint_id value
 * @method static \ComplaintsReasons[] findByComplaintId(integer $complaint_id) find objects in database by complaint_id
 * @method static \ComplaintsReasons findOneByComplaintId(integer $complaint_id) find object in database by complaint_id
 * @method static \ComplaintsReasons retrieveByComplaintId(integer $complaint_id) retrieve object from poll by complaint_id, get it from db if not exist in poll

 * @method void setLongType(string $long_type) set long_type value
 * @method string getLongType() get long_type value
 * @method static \ComplaintsReasons[] findByLongType(string $long_type) find objects in database by long_type
 * @method static \ComplaintsReasons findOneByLongType(string $long_type) find object in database by long_type
 * @method static \ComplaintsReasons retrieveByLongType(string $long_type) retrieve object from poll by long_type, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \ComplaintsReasons[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \ComplaintsReasons findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \ComplaintsReasons retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class ComplaintsReasonsBase extends ActiveRecord {
    protected static $_tableName = 'complaints_reasons';
    protected static $_phpName = 'ComplaintsReasons';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaints_reasons';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'complaint_id' => array('name' => 'complaint_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'long_type' => array('name' => 'long_type',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'create_time' => array('name' => 'create_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','complaint_id','long_type','create_time');

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