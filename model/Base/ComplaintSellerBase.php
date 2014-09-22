<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ComplaintSeller
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $reason reason type : varchar(255) max_length : 255
 * @property string $level level type : varchar(255) max_length : 255
 * @property string $status status type : varchar(255) max_length : 255
 * @property integer $order_id order_id type : int(11)
 * @property string $order_code order_code type : varchar(255) max_length : 255
 * @property string $order_invoice order_invoice type : varchar(255) max_length : 255
 * @property integer $order_buyer_id order_buyer_id type : int(11)
 * @property string $order_buyer_code order_buyer_code type : varchar(255) max_length : 255
 * @property string $order_buyer_username order_buyer_username type : varchar(255) max_length : 255
 * @property string $seller_name seller_name type : varchar(200) max_length : 200
 * @property string $seller_aliwang seller_aliwang type : varchar(100) max_length : 100
 * @property string $seller_homeland seller_homeland type : varchar(100) max_length : 100
 * @property string $seller_info seller_info type : text max_length : 
 * @property string $description description type : text max_length : 
 * @property number $amount_seller_refund amount_seller_refund type : double
 * @property integer $processed_by processed_by type : int(11)
 * @property integer $created_by created_by type : int(11)
 * @property integer $accepted_by accepted_by type : int(11)
 * @property integer $rejected_by rejected_by type : int(11)
 * @property datetime $created_time created_time type : datetime
 * @property datetime $accepted_time accepted_time type : datetime
 * @property datetime $rejected_time rejected_time type : datetime
 * @property datetime $processed_time processed_time type : datetime
 * @property datetime $refocus_time refocus_time type : datetime
 * @property integer $flag flag type : tinyint(4)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ComplaintSeller[] findById(integer $id) find objects in database by id
 * @method static \ComplaintSeller findOneById(integer $id) find object in database by id
 * @method static \ComplaintSeller retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setReason(string $reason) set reason value
 * @method string getReason() get reason value
 * @method static \ComplaintSeller[] findByReason(string $reason) find objects in database by reason
 * @method static \ComplaintSeller findOneByReason(string $reason) find object in database by reason
 * @method static \ComplaintSeller retrieveByReason(string $reason) retrieve object from poll by reason, get it from db if not exist in poll

 * @method void setLevel(string $level) set level value
 * @method string getLevel() get level value
 * @method static \ComplaintSeller[] findByLevel(string $level) find objects in database by level
 * @method static \ComplaintSeller findOneByLevel(string $level) find object in database by level
 * @method static \ComplaintSeller retrieveByLevel(string $level) retrieve object from poll by level, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \ComplaintSeller[] findByStatus(string $status) find objects in database by status
 * @method static \ComplaintSeller findOneByStatus(string $status) find object in database by status
 * @method static \ComplaintSeller retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \ComplaintSeller[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \ComplaintSeller findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \ComplaintSeller retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \ComplaintSeller[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \ComplaintSeller findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \ComplaintSeller retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setOrderInvoice(string $order_invoice) set order_invoice value
 * @method string getOrderInvoice() get order_invoice value
 * @method static \ComplaintSeller[] findByOrderInvoice(string $order_invoice) find objects in database by order_invoice
 * @method static \ComplaintSeller findOneByOrderInvoice(string $order_invoice) find object in database by order_invoice
 * @method static \ComplaintSeller retrieveByOrderInvoice(string $order_invoice) retrieve object from poll by order_invoice, get it from db if not exist in poll

 * @method void setOrderBuyerId(integer $order_buyer_id) set order_buyer_id value
 * @method integer getOrderBuyerId() get order_buyer_id value
 * @method static \ComplaintSeller[] findByOrderBuyerId(integer $order_buyer_id) find objects in database by order_buyer_id
 * @method static \ComplaintSeller findOneByOrderBuyerId(integer $order_buyer_id) find object in database by order_buyer_id
 * @method static \ComplaintSeller retrieveByOrderBuyerId(integer $order_buyer_id) retrieve object from poll by order_buyer_id, get it from db if not exist in poll

 * @method void setOrderBuyerCode(string $order_buyer_code) set order_buyer_code value
 * @method string getOrderBuyerCode() get order_buyer_code value
 * @method static \ComplaintSeller[] findByOrderBuyerCode(string $order_buyer_code) find objects in database by order_buyer_code
 * @method static \ComplaintSeller findOneByOrderBuyerCode(string $order_buyer_code) find object in database by order_buyer_code
 * @method static \ComplaintSeller retrieveByOrderBuyerCode(string $order_buyer_code) retrieve object from poll by order_buyer_code, get it from db if not exist in poll

 * @method void setOrderBuyerUsername(string $order_buyer_username) set order_buyer_username value
 * @method string getOrderBuyerUsername() get order_buyer_username value
 * @method static \ComplaintSeller[] findByOrderBuyerUsername(string $order_buyer_username) find objects in database by order_buyer_username
 * @method static \ComplaintSeller findOneByOrderBuyerUsername(string $order_buyer_username) find object in database by order_buyer_username
 * @method static \ComplaintSeller retrieveByOrderBuyerUsername(string $order_buyer_username) retrieve object from poll by order_buyer_username, get it from db if not exist in poll

 * @method void setSellerName(string $seller_name) set seller_name value
 * @method string getSellerName() get seller_name value
 * @method static \ComplaintSeller[] findBySellerName(string $seller_name) find objects in database by seller_name
 * @method static \ComplaintSeller findOneBySellerName(string $seller_name) find object in database by seller_name
 * @method static \ComplaintSeller retrieveBySellerName(string $seller_name) retrieve object from poll by seller_name, get it from db if not exist in poll

 * @method void setSellerAliwang(string $seller_aliwang) set seller_aliwang value
 * @method string getSellerAliwang() get seller_aliwang value
 * @method static \ComplaintSeller[] findBySellerAliwang(string $seller_aliwang) find objects in database by seller_aliwang
 * @method static \ComplaintSeller findOneBySellerAliwang(string $seller_aliwang) find object in database by seller_aliwang
 * @method static \ComplaintSeller retrieveBySellerAliwang(string $seller_aliwang) retrieve object from poll by seller_aliwang, get it from db if not exist in poll

 * @method void setSellerHomeland(string $seller_homeland) set seller_homeland value
 * @method string getSellerHomeland() get seller_homeland value
 * @method static \ComplaintSeller[] findBySellerHomeland(string $seller_homeland) find objects in database by seller_homeland
 * @method static \ComplaintSeller findOneBySellerHomeland(string $seller_homeland) find object in database by seller_homeland
 * @method static \ComplaintSeller retrieveBySellerHomeland(string $seller_homeland) retrieve object from poll by seller_homeland, get it from db if not exist in poll

 * @method void setSellerInfo(string $seller_info) set seller_info value
 * @method string getSellerInfo() get seller_info value
 * @method static \ComplaintSeller[] findBySellerInfo(string $seller_info) find objects in database by seller_info
 * @method static \ComplaintSeller findOneBySellerInfo(string $seller_info) find object in database by seller_info
 * @method static \ComplaintSeller retrieveBySellerInfo(string $seller_info) retrieve object from poll by seller_info, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \ComplaintSeller[] findByDescription(string $description) find objects in database by description
 * @method static \ComplaintSeller findOneByDescription(string $description) find object in database by description
 * @method static \ComplaintSeller retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setAmountSellerRefund(number $amount_seller_refund) set amount_seller_refund value
 * @method number getAmountSellerRefund() get amount_seller_refund value
 * @method static \ComplaintSeller[] findByAmountSellerRefund(number $amount_seller_refund) find objects in database by amount_seller_refund
 * @method static \ComplaintSeller findOneByAmountSellerRefund(number $amount_seller_refund) find object in database by amount_seller_refund
 * @method static \ComplaintSeller retrieveByAmountSellerRefund(number $amount_seller_refund) retrieve object from poll by amount_seller_refund, get it from db if not exist in poll

 * @method void setProcessedBy(integer $processed_by) set processed_by value
 * @method integer getProcessedBy() get processed_by value
 * @method static \ComplaintSeller[] findByProcessedBy(integer $processed_by) find objects in database by processed_by
 * @method static \ComplaintSeller findOneByProcessedBy(integer $processed_by) find object in database by processed_by
 * @method static \ComplaintSeller retrieveByProcessedBy(integer $processed_by) retrieve object from poll by processed_by, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \ComplaintSeller[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \ComplaintSeller findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \ComplaintSeller retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setAcceptedBy(integer $accepted_by) set accepted_by value
 * @method integer getAcceptedBy() get accepted_by value
 * @method static \ComplaintSeller[] findByAcceptedBy(integer $accepted_by) find objects in database by accepted_by
 * @method static \ComplaintSeller findOneByAcceptedBy(integer $accepted_by) find object in database by accepted_by
 * @method static \ComplaintSeller retrieveByAcceptedBy(integer $accepted_by) retrieve object from poll by accepted_by, get it from db if not exist in poll

 * @method void setRejectedBy(integer $rejected_by) set rejected_by value
 * @method integer getRejectedBy() get rejected_by value
 * @method static \ComplaintSeller[] findByRejectedBy(integer $rejected_by) find objects in database by rejected_by
 * @method static \ComplaintSeller findOneByRejectedBy(integer $rejected_by) find object in database by rejected_by
 * @method static \ComplaintSeller retrieveByRejectedBy(integer $rejected_by) retrieve object from poll by rejected_by, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \ComplaintSeller[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \ComplaintSeller findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \ComplaintSeller retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setAcceptedTime(\Flywheel\Db\Type\DateTime $accepted_time) setAcceptedTime(string $accepted_time) set accepted_time value
 * @method \Flywheel\Db\Type\DateTime getAcceptedTime() get accepted_time value
 * @method static \ComplaintSeller[] findByAcceptedTime(\Flywheel\Db\Type\DateTime $accepted_time) findByAcceptedTime(string $accepted_time) find objects in database by accepted_time
 * @method static \ComplaintSeller findOneByAcceptedTime(\Flywheel\Db\Type\DateTime $accepted_time) findOneByAcceptedTime(string $accepted_time) find object in database by accepted_time
 * @method static \ComplaintSeller retrieveByAcceptedTime(\Flywheel\Db\Type\DateTime $accepted_time) retrieveByAcceptedTime(string $accepted_time) retrieve object from poll by accepted_time, get it from db if not exist in poll

 * @method void setRejectedTime(\Flywheel\Db\Type\DateTime $rejected_time) setRejectedTime(string $rejected_time) set rejected_time value
 * @method \Flywheel\Db\Type\DateTime getRejectedTime() get rejected_time value
 * @method static \ComplaintSeller[] findByRejectedTime(\Flywheel\Db\Type\DateTime $rejected_time) findByRejectedTime(string $rejected_time) find objects in database by rejected_time
 * @method static \ComplaintSeller findOneByRejectedTime(\Flywheel\Db\Type\DateTime $rejected_time) findOneByRejectedTime(string $rejected_time) find object in database by rejected_time
 * @method static \ComplaintSeller retrieveByRejectedTime(\Flywheel\Db\Type\DateTime $rejected_time) retrieveByRejectedTime(string $rejected_time) retrieve object from poll by rejected_time, get it from db if not exist in poll

 * @method void setProcessedTime(\Flywheel\Db\Type\DateTime $processed_time) setProcessedTime(string $processed_time) set processed_time value
 * @method \Flywheel\Db\Type\DateTime getProcessedTime() get processed_time value
 * @method static \ComplaintSeller[] findByProcessedTime(\Flywheel\Db\Type\DateTime $processed_time) findByProcessedTime(string $processed_time) find objects in database by processed_time
 * @method static \ComplaintSeller findOneByProcessedTime(\Flywheel\Db\Type\DateTime $processed_time) findOneByProcessedTime(string $processed_time) find object in database by processed_time
 * @method static \ComplaintSeller retrieveByProcessedTime(\Flywheel\Db\Type\DateTime $processed_time) retrieveByProcessedTime(string $processed_time) retrieve object from poll by processed_time, get it from db if not exist in poll

 * @method void setRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) setRefocusTime(string $refocus_time) set refocus_time value
 * @method \Flywheel\Db\Type\DateTime getRefocusTime() get refocus_time value
 * @method static \ComplaintSeller[] findByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) findByRefocusTime(string $refocus_time) find objects in database by refocus_time
 * @method static \ComplaintSeller findOneByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) findOneByRefocusTime(string $refocus_time) find object in database by refocus_time
 * @method static \ComplaintSeller retrieveByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) retrieveByRefocusTime(string $refocus_time) retrieve object from poll by refocus_time, get it from db if not exist in poll

 * @method void setFlag(integer $flag) set flag value
 * @method integer getFlag() get flag value
 * @method static \ComplaintSeller[] findByFlag(integer $flag) find objects in database by flag
 * @method static \ComplaintSeller findOneByFlag(integer $flag) find object in database by flag
 * @method static \ComplaintSeller retrieveByFlag(integer $flag) retrieve object from poll by flag, get it from db if not exist in poll


 */
abstract class ComplaintSellerBase extends ActiveRecord {
    protected static $_tableName = 'complaint_seller';
    protected static $_phpName = 'ComplaintSeller';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaint_seller';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'reason' => array('name' => 'reason',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'level' => array('name' => 'level',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'order_invoice' => array('name' => 'order_invoice',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'order_buyer_id' => array('name' => 'order_buyer_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_buyer_code' => array('name' => 'order_buyer_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'order_buyer_username' => array('name' => 'order_buyer_username',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'seller_name' => array('name' => 'seller_name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(200)',
                'length' => 200),
        'seller_aliwang' => array('name' => 'seller_aliwang',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'seller_homeland' => array('name' => 'seller_homeland',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'seller_info' => array('name' => 'seller_info',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'description' => array('name' => 'description',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'amount_seller_refund' => array('name' => 'amount_seller_refund',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'processed_by' => array('name' => 'processed_by',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'created_by' => array('name' => 'created_by',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'accepted_by' => array('name' => 'accepted_by',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'rejected_by' => array('name' => 'rejected_by',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'created_time' => array('name' => 'created_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'accepted_time' => array('name' => 'accepted_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'rejected_time' => array('name' => 'rejected_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'processed_time' => array('name' => 'processed_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'refocus_time' => array('name' => 'refocus_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'flag' => array('name' => 'flag',
                'default' => 0,
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
    protected static $_cols = array('id','reason','level','status','order_id','order_code','order_invoice','order_buyer_id','order_buyer_code','order_buyer_username','seller_name','seller_aliwang','seller_homeland','seller_info','description','amount_seller_refund','processed_by','created_by','accepted_by','rejected_by','created_time','accepted_time','rejected_time','processed_time','refocus_time','flag');

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