<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * BarcodeFiles
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $state state type : enum('NOT_ANALYZED','ANALYZING','DONE') max_length : 12
 * @property string $type type type : enum('FREIGHT_BILL','ORDER') max_length : 12
 * @property string $activity activity type : enum('IN','OUT','INVENTORY','OTHER') max_length : 9
 * @property string $warehouse warehouse type : varchar(10) max_length : 10
 * @property string $file_location file_location type : varchar(255) max_length : 255
 * @property string $file_name file_name type : varchar(255) max_length : 255
 * @property string $content content type : text max_length : 
 * @property integer $total_barcode total_barcode type : int(11)
 * @property date $working_date working_date type : date
 * @property integer $uploaded_by uploaded_by type : int(10) unsigned
 * @property datetime $uploaded_time uploaded_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \BarcodeFiles[] findById(integer $id) find objects in database by id
 * @method static \BarcodeFiles findOneById(integer $id) find object in database by id
 * @method static \BarcodeFiles retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setState(string $state) set state value
 * @method string getState() get state value
 * @method static \BarcodeFiles[] findByState(string $state) find objects in database by state
 * @method static \BarcodeFiles findOneByState(string $state) find object in database by state
 * @method static \BarcodeFiles retrieveByState(string $state) retrieve object from poll by state, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \BarcodeFiles[] findByType(string $type) find objects in database by type
 * @method static \BarcodeFiles findOneByType(string $type) find object in database by type
 * @method static \BarcodeFiles retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setActivity(string $activity) set activity value
 * @method string getActivity() get activity value
 * @method static \BarcodeFiles[] findByActivity(string $activity) find objects in database by activity
 * @method static \BarcodeFiles findOneByActivity(string $activity) find object in database by activity
 * @method static \BarcodeFiles retrieveByActivity(string $activity) retrieve object from poll by activity, get it from db if not exist in poll

 * @method void setWarehouse(string $warehouse) set warehouse value
 * @method string getWarehouse() get warehouse value
 * @method static \BarcodeFiles[] findByWarehouse(string $warehouse) find objects in database by warehouse
 * @method static \BarcodeFiles findOneByWarehouse(string $warehouse) find object in database by warehouse
 * @method static \BarcodeFiles retrieveByWarehouse(string $warehouse) retrieve object from poll by warehouse, get it from db if not exist in poll

 * @method void setFileLocation(string $file_location) set file_location value
 * @method string getFileLocation() get file_location value
 * @method static \BarcodeFiles[] findByFileLocation(string $file_location) find objects in database by file_location
 * @method static \BarcodeFiles findOneByFileLocation(string $file_location) find object in database by file_location
 * @method static \BarcodeFiles retrieveByFileLocation(string $file_location) retrieve object from poll by file_location, get it from db if not exist in poll

 * @method void setFileName(string $file_name) set file_name value
 * @method string getFileName() get file_name value
 * @method static \BarcodeFiles[] findByFileName(string $file_name) find objects in database by file_name
 * @method static \BarcodeFiles findOneByFileName(string $file_name) find object in database by file_name
 * @method static \BarcodeFiles retrieveByFileName(string $file_name) retrieve object from poll by file_name, get it from db if not exist in poll

 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \BarcodeFiles[] findByContent(string $content) find objects in database by content
 * @method static \BarcodeFiles findOneByContent(string $content) find object in database by content
 * @method static \BarcodeFiles retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll

 * @method void setTotalBarcode(integer $total_barcode) set total_barcode value
 * @method integer getTotalBarcode() get total_barcode value
 * @method static \BarcodeFiles[] findByTotalBarcode(integer $total_barcode) find objects in database by total_barcode
 * @method static \BarcodeFiles findOneByTotalBarcode(integer $total_barcode) find object in database by total_barcode
 * @method static \BarcodeFiles retrieveByTotalBarcode(integer $total_barcode) retrieve object from poll by total_barcode, get it from db if not exist in poll

 * @method void setWorkingDate(\Flywheel\Db\Type\DateTime $working_date) setWorkingDate(string $working_date) set working_date value
 * @method \Flywheel\Db\Type\DateTime getWorkingDate() get working_date value
 * @method static \BarcodeFiles[] findByWorkingDate(\Flywheel\Db\Type\DateTime $working_date) findByWorkingDate(string $working_date) find objects in database by working_date
 * @method static \BarcodeFiles findOneByWorkingDate(\Flywheel\Db\Type\DateTime $working_date) findOneByWorkingDate(string $working_date) find object in database by working_date
 * @method static \BarcodeFiles retrieveByWorkingDate(\Flywheel\Db\Type\DateTime $working_date) retrieveByWorkingDate(string $working_date) retrieve object from poll by working_date, get it from db if not exist in poll

 * @method void setUploadedBy(integer $uploaded_by) set uploaded_by value
 * @method integer getUploadedBy() get uploaded_by value
 * @method static \BarcodeFiles[] findByUploadedBy(integer $uploaded_by) find objects in database by uploaded_by
 * @method static \BarcodeFiles findOneByUploadedBy(integer $uploaded_by) find object in database by uploaded_by
 * @method static \BarcodeFiles retrieveByUploadedBy(integer $uploaded_by) retrieve object from poll by uploaded_by, get it from db if not exist in poll

 * @method void setUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) setUploadedTime(string $uploaded_time) set uploaded_time value
 * @method \Flywheel\Db\Type\DateTime getUploadedTime() get uploaded_time value
 * @method static \BarcodeFiles[] findByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) findByUploadedTime(string $uploaded_time) find objects in database by uploaded_time
 * @method static \BarcodeFiles findOneByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) findOneByUploadedTime(string $uploaded_time) find object in database by uploaded_time
 * @method static \BarcodeFiles retrieveByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) retrieveByUploadedTime(string $uploaded_time) retrieve object from poll by uploaded_time, get it from db if not exist in poll


 */
abstract class BarcodeFilesBase extends ActiveRecord {
    protected static $_tableName = 'barcode_files';
    protected static $_phpName = 'BarcodeFiles';
    protected static $_pk = 'id';
    protected static $_alias = 'b';
    protected static $_dbConnectName = 'barcode_files';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'state' => array('name' => 'state',
                'default' => 'NOT_ANALYZED',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'NOT_ANALYZED\',\'ANALYZING\',\'DONE\')',
                'length' => 12),
        'type' => array('name' => 'type',
                'default' => 'ORDER',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'FREIGHT_BILL\',\'ORDER\')',
                'length' => 12),
        'activity' => array('name' => 'activity',
                'default' => 'OTHER',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'enum(\'IN\',\'OUT\',\'INVENTORY\',\'OTHER\')',
                'length' => 9),
        'warehouse' => array('name' => 'warehouse',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(10)',
                'length' => 10),
        'file_location' => array('name' => 'file_location',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'file_name' => array('name' => 'file_name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'content' => array('name' => 'content',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'total_barcode' => array('name' => 'total_barcode',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'working_date' => array('name' => 'working_date',
                'default' => '0000-00-00',
                'not_null' => true,
                'type' => 'date',
                'db_type' => 'date'),
        'uploaded_by' => array('name' => 'uploaded_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(10) unsigned',
                'length' => 4),
        'uploaded_time' => array('name' => 'uploaded_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'NOT_ANALYZED|ANALYZING|DONE',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'FREIGHT_BILL|ORDER',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
        'activity' => array(
            array('name' => 'ValidValues',
                'value' => 'IN|OUT|INVENTORY|OTHER',
                'message'=> 'activity\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'state' => array(
            array('name' => 'ValidValues',
                'value' => 'NOT_ANALYZED|ANALYZING|DONE',
                'message'=> 'state\'s values is not allowed'
            ),
        ),
        'type' => array(
            array('name' => 'ValidValues',
                'value' => 'FREIGHT_BILL|ORDER',
                'message'=> 'type\'s values is not allowed'
            ),
        ),
        'activity' => array(
            array('name' => 'ValidValues',
                'value' => 'IN|OUT|INVENTORY|OTHER',
                'message'=> 'activity\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','state','type','activity','warehouse','file_location','file_name','content','total_barcode','working_date','uploaded_by','uploaded_time');

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