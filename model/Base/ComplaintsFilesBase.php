<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ComplaintsFiles
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $name name type : varchar(255) max_length : 255
 * @property string $path path type : varchar(255) max_length : 255
 * @property integer $complaint_id complaint_id type : int(11)
 * @property string $file_type file_type type : varchar(255) max_length : 255
 * @property string $invalid invalid type : varchar(255) max_length : 255
 * @property datetime $create_time create_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ComplaintsFiles[] findById(integer $id) find objects in database by id
 * @method static \ComplaintsFiles findOneById(integer $id) find object in database by id
 * @method static \ComplaintsFiles retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setName(string $name) set name value
 * @method string getName() get name value
 * @method static \ComplaintsFiles[] findByName(string $name) find objects in database by name
 * @method static \ComplaintsFiles findOneByName(string $name) find object in database by name
 * @method static \ComplaintsFiles retrieveByName(string $name) retrieve object from poll by name, get it from db if not exist in poll

 * @method void setPath(string $path) set path value
 * @method string getPath() get path value
 * @method static \ComplaintsFiles[] findByPath(string $path) find objects in database by path
 * @method static \ComplaintsFiles findOneByPath(string $path) find object in database by path
 * @method static \ComplaintsFiles retrieveByPath(string $path) retrieve object from poll by path, get it from db if not exist in poll

 * @method void setComplaintId(integer $complaint_id) set complaint_id value
 * @method integer getComplaintId() get complaint_id value
 * @method static \ComplaintsFiles[] findByComplaintId(integer $complaint_id) find objects in database by complaint_id
 * @method static \ComplaintsFiles findOneByComplaintId(integer $complaint_id) find object in database by complaint_id
 * @method static \ComplaintsFiles retrieveByComplaintId(integer $complaint_id) retrieve object from poll by complaint_id, get it from db if not exist in poll

 * @method void setFileType(string $file_type) set file_type value
 * @method string getFileType() get file_type value
 * @method static \ComplaintsFiles[] findByFileType(string $file_type) find objects in database by file_type
 * @method static \ComplaintsFiles findOneByFileType(string $file_type) find object in database by file_type
 * @method static \ComplaintsFiles retrieveByFileType(string $file_type) retrieve object from poll by file_type, get it from db if not exist in poll

 * @method void setInvalid(string $invalid) set invalid value
 * @method string getInvalid() get invalid value
 * @method static \ComplaintsFiles[] findByInvalid(string $invalid) find objects in database by invalid
 * @method static \ComplaintsFiles findOneByInvalid(string $invalid) find object in database by invalid
 * @method static \ComplaintsFiles retrieveByInvalid(string $invalid) retrieve object from poll by invalid, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \ComplaintsFiles[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \ComplaintsFiles findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \ComplaintsFiles retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll


 */
abstract class ComplaintsFilesBase extends ActiveRecord {
    protected static $_tableName = 'complaints_files';
    protected static $_phpName = 'ComplaintsFiles';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaints_files';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'name' => array('name' => 'name',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'path' => array('name' => 'path',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'complaint_id' => array('name' => 'complaint_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'file_type' => array('name' => 'file_type',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'invalid' => array('name' => 'invalid',
                'default' => 'NONE',
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
    protected static $_cols = array('id','name','path','complaint_id','file_type','invalid','create_time');

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