<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Complaints
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $title title type : varchar(255) max_length : 255
 * @property string $status status type : varchar(255) max_length : 255
 * @property string $type type type : varchar(255) max_length : 255
 * @property number $quantity quantity type : float(255,0)
 * @property string $item_code item_code type : varchar(255) max_length : 255
 * @property integer $item_id item_id type : int(11)
 * @property string $order_code order_code type : varchar(255) max_length : 255
 * @property integer $order_id order_id type : int(11)
 * @property integer $buyer_id buyer_id type : int(11)
 * @property integer $recipient_by recipient_by type : int(11)
 * @property integer $accept_by accept_by type : int(11)
 * @property integer $approval_by approval_by type : int(11)
 * @property integer $reject_by reject_by type : int(11)
 * @property datetime $create_time create_time type : datetime
 * @property datetime $recipient_time recipient_time type : datetime
 * @property datetime $accept_time accept_time type : datetime
 * @property datetime $approval_time approval_time type : datetime
 * @property datetime $reject_time reject_time type : datetime
 * @property datetime $refocus_time refocus_time type : datetime
 * @property datetime $customer_amount_reimbursement_time customer_amount_reimbursement_time type : datetime
 * @property number $customer_amount_reimbursement customer_amount_reimbursement type : double(255,0)
 * @property datetime $recipient_amount_reimbursement_time recipient_amount_reimbursement_time type : datetime
 * @property number $recipient_amount_reimbursement recipient_amount_reimbursement type : double(255,0)
 * @property number $damage_amount damage_amount type : double
 * @property string $damage damage type : varchar(255) max_length : 255
 * @property string $description description type : tinytext max_length : 
 * @property string $transaction transaction type : varchar(255) max_length : 255
 * @property string $customer_delivery customer_delivery type : varchar(255) max_length : 255
 * @property string $error_division_company error_division_company type : varchar(255) max_length : 255
 * @property string $error_partner error_partner type : varchar(255) max_length : 255
 * @property string $error_seller error_seller type : varchar(255) max_length : 255

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Complaints[] findById(integer $id) find objects in database by id
 * @method static \Complaints findOneById(integer $id) find object in database by id
 * @method static \Complaints retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \Complaints[] findByTitle(string $title) find objects in database by title
 * @method static \Complaints findOneByTitle(string $title) find object in database by title
 * @method static \Complaints retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Complaints[] findByStatus(string $status) find objects in database by status
 * @method static \Complaints findOneByStatus(string $status) find object in database by status
 * @method static \Complaints retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setType(string $type) set type value
 * @method string getType() get type value
 * @method static \Complaints[] findByType(string $type) find objects in database by type
 * @method static \Complaints findOneByType(string $type) find object in database by type
 * @method static \Complaints retrieveByType(string $type) retrieve object from poll by type, get it from db if not exist in poll

 * @method void setQuantity(number $quantity) set quantity value
 * @method number getQuantity() get quantity value
 * @method static \Complaints[] findByQuantity(number $quantity) find objects in database by quantity
 * @method static \Complaints findOneByQuantity(number $quantity) find object in database by quantity
 * @method static \Complaints retrieveByQuantity(number $quantity) retrieve object from poll by quantity, get it from db if not exist in poll

 * @method void setItemCode(string $item_code) set item_code value
 * @method string getItemCode() get item_code value
 * @method static \Complaints[] findByItemCode(string $item_code) find objects in database by item_code
 * @method static \Complaints findOneByItemCode(string $item_code) find object in database by item_code
 * @method static \Complaints retrieveByItemCode(string $item_code) retrieve object from poll by item_code, get it from db if not exist in poll

 * @method void setItemId(integer $item_id) set item_id value
 * @method integer getItemId() get item_id value
 * @method static \Complaints[] findByItemId(integer $item_id) find objects in database by item_id
 * @method static \Complaints findOneByItemId(integer $item_id) find object in database by item_id
 * @method static \Complaints retrieveByItemId(integer $item_id) retrieve object from poll by item_id, get it from db if not exist in poll

 * @method void setOrderCode(string $order_code) set order_code value
 * @method string getOrderCode() get order_code value
 * @method static \Complaints[] findByOrderCode(string $order_code) find objects in database by order_code
 * @method static \Complaints findOneByOrderCode(string $order_code) find object in database by order_code
 * @method static \Complaints retrieveByOrderCode(string $order_code) retrieve object from poll by order_code, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \Complaints[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \Complaints findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \Complaints retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setBuyerId(integer $buyer_id) set buyer_id value
 * @method integer getBuyerId() get buyer_id value
 * @method static \Complaints[] findByBuyerId(integer $buyer_id) find objects in database by buyer_id
 * @method static \Complaints findOneByBuyerId(integer $buyer_id) find object in database by buyer_id
 * @method static \Complaints retrieveByBuyerId(integer $buyer_id) retrieve object from poll by buyer_id, get it from db if not exist in poll

 * @method void setRecipientBy(integer $recipient_by) set recipient_by value
 * @method integer getRecipientBy() get recipient_by value
 * @method static \Complaints[] findByRecipientBy(integer $recipient_by) find objects in database by recipient_by
 * @method static \Complaints findOneByRecipientBy(integer $recipient_by) find object in database by recipient_by
 * @method static \Complaints retrieveByRecipientBy(integer $recipient_by) retrieve object from poll by recipient_by, get it from db if not exist in poll

 * @method void setAcceptBy(integer $accept_by) set accept_by value
 * @method integer getAcceptBy() get accept_by value
 * @method static \Complaints[] findByAcceptBy(integer $accept_by) find objects in database by accept_by
 * @method static \Complaints findOneByAcceptBy(integer $accept_by) find object in database by accept_by
 * @method static \Complaints retrieveByAcceptBy(integer $accept_by) retrieve object from poll by accept_by, get it from db if not exist in poll

 * @method void setApprovalBy(integer $approval_by) set approval_by value
 * @method integer getApprovalBy() get approval_by value
 * @method static \Complaints[] findByApprovalBy(integer $approval_by) find objects in database by approval_by
 * @method static \Complaints findOneByApprovalBy(integer $approval_by) find object in database by approval_by
 * @method static \Complaints retrieveByApprovalBy(integer $approval_by) retrieve object from poll by approval_by, get it from db if not exist in poll

 * @method void setRejectBy(integer $reject_by) set reject_by value
 * @method integer getRejectBy() get reject_by value
 * @method static \Complaints[] findByRejectBy(integer $reject_by) find objects in database by reject_by
 * @method static \Complaints findOneByRejectBy(integer $reject_by) find object in database by reject_by
 * @method static \Complaints retrieveByRejectBy(integer $reject_by) retrieve object from poll by reject_by, get it from db if not exist in poll

 * @method void setCreateTime(\Flywheel\Db\Type\DateTime $create_time) setCreateTime(string $create_time) set create_time value
 * @method \Flywheel\Db\Type\DateTime getCreateTime() get create_time value
 * @method static \Complaints[] findByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findByCreateTime(string $create_time) find objects in database by create_time
 * @method static \Complaints findOneByCreateTime(\Flywheel\Db\Type\DateTime $create_time) findOneByCreateTime(string $create_time) find object in database by create_time
 * @method static \Complaints retrieveByCreateTime(\Flywheel\Db\Type\DateTime $create_time) retrieveByCreateTime(string $create_time) retrieve object from poll by create_time, get it from db if not exist in poll

 * @method void setRecipientTime(\Flywheel\Db\Type\DateTime $recipient_time) setRecipientTime(string $recipient_time) set recipient_time value
 * @method \Flywheel\Db\Type\DateTime getRecipientTime() get recipient_time value
 * @method static \Complaints[] findByRecipientTime(\Flywheel\Db\Type\DateTime $recipient_time) findByRecipientTime(string $recipient_time) find objects in database by recipient_time
 * @method static \Complaints findOneByRecipientTime(\Flywheel\Db\Type\DateTime $recipient_time) findOneByRecipientTime(string $recipient_time) find object in database by recipient_time
 * @method static \Complaints retrieveByRecipientTime(\Flywheel\Db\Type\DateTime $recipient_time) retrieveByRecipientTime(string $recipient_time) retrieve object from poll by recipient_time, get it from db if not exist in poll

 * @method void setAcceptTime(\Flywheel\Db\Type\DateTime $accept_time) setAcceptTime(string $accept_time) set accept_time value
 * @method \Flywheel\Db\Type\DateTime getAcceptTime() get accept_time value
 * @method static \Complaints[] findByAcceptTime(\Flywheel\Db\Type\DateTime $accept_time) findByAcceptTime(string $accept_time) find objects in database by accept_time
 * @method static \Complaints findOneByAcceptTime(\Flywheel\Db\Type\DateTime $accept_time) findOneByAcceptTime(string $accept_time) find object in database by accept_time
 * @method static \Complaints retrieveByAcceptTime(\Flywheel\Db\Type\DateTime $accept_time) retrieveByAcceptTime(string $accept_time) retrieve object from poll by accept_time, get it from db if not exist in poll

 * @method void setApprovalTime(\Flywheel\Db\Type\DateTime $approval_time) setApprovalTime(string $approval_time) set approval_time value
 * @method \Flywheel\Db\Type\DateTime getApprovalTime() get approval_time value
 * @method static \Complaints[] findByApprovalTime(\Flywheel\Db\Type\DateTime $approval_time) findByApprovalTime(string $approval_time) find objects in database by approval_time
 * @method static \Complaints findOneByApprovalTime(\Flywheel\Db\Type\DateTime $approval_time) findOneByApprovalTime(string $approval_time) find object in database by approval_time
 * @method static \Complaints retrieveByApprovalTime(\Flywheel\Db\Type\DateTime $approval_time) retrieveByApprovalTime(string $approval_time) retrieve object from poll by approval_time, get it from db if not exist in poll

 * @method void setRejectTime(\Flywheel\Db\Type\DateTime $reject_time) setRejectTime(string $reject_time) set reject_time value
 * @method \Flywheel\Db\Type\DateTime getRejectTime() get reject_time value
 * @method static \Complaints[] findByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) findByRejectTime(string $reject_time) find objects in database by reject_time
 * @method static \Complaints findOneByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) findOneByRejectTime(string $reject_time) find object in database by reject_time
 * @method static \Complaints retrieveByRejectTime(\Flywheel\Db\Type\DateTime $reject_time) retrieveByRejectTime(string $reject_time) retrieve object from poll by reject_time, get it from db if not exist in poll

 * @method void setRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) setRefocusTime(string $refocus_time) set refocus_time value
 * @method \Flywheel\Db\Type\DateTime getRefocusTime() get refocus_time value
 * @method static \Complaints[] findByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) findByRefocusTime(string $refocus_time) find objects in database by refocus_time
 * @method static \Complaints findOneByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) findOneByRefocusTime(string $refocus_time) find object in database by refocus_time
 * @method static \Complaints retrieveByRefocusTime(\Flywheel\Db\Type\DateTime $refocus_time) retrieveByRefocusTime(string $refocus_time) retrieve object from poll by refocus_time, get it from db if not exist in poll

 * @method void setCustomerAmountReimbursementTime(\Flywheel\Db\Type\DateTime $customer_amount_reimbursement_time) setCustomerAmountReimbursementTime(string $customer_amount_reimbursement_time) set customer_amount_reimbursement_time value
 * @method \Flywheel\Db\Type\DateTime getCustomerAmountReimbursementTime() get customer_amount_reimbursement_time value
 * @method static \Complaints[] findByCustomerAmountReimbursementTime(\Flywheel\Db\Type\DateTime $customer_amount_reimbursement_time) findByCustomerAmountReimbursementTime(string $customer_amount_reimbursement_time) find objects in database by customer_amount_reimbursement_time
 * @method static \Complaints findOneByCustomerAmountReimbursementTime(\Flywheel\Db\Type\DateTime $customer_amount_reimbursement_time) findOneByCustomerAmountReimbursementTime(string $customer_amount_reimbursement_time) find object in database by customer_amount_reimbursement_time
 * @method static \Complaints retrieveByCustomerAmountReimbursementTime(\Flywheel\Db\Type\DateTime $customer_amount_reimbursement_time) retrieveByCustomerAmountReimbursementTime(string $customer_amount_reimbursement_time) retrieve object from poll by customer_amount_reimbursement_time, get it from db if not exist in poll

 * @method void setCustomerAmountReimbursement(number $customer_amount_reimbursement) set customer_amount_reimbursement value
 * @method number getCustomerAmountReimbursement() get customer_amount_reimbursement value
 * @method static \Complaints[] findByCustomerAmountReimbursement(number $customer_amount_reimbursement) find objects in database by customer_amount_reimbursement
 * @method static \Complaints findOneByCustomerAmountReimbursement(number $customer_amount_reimbursement) find object in database by customer_amount_reimbursement
 * @method static \Complaints retrieveByCustomerAmountReimbursement(number $customer_amount_reimbursement) retrieve object from poll by customer_amount_reimbursement, get it from db if not exist in poll

 * @method void setRecipientAmountReimbursementTime(\Flywheel\Db\Type\DateTime $recipient_amount_reimbursement_time) setRecipientAmountReimbursementTime(string $recipient_amount_reimbursement_time) set recipient_amount_reimbursement_time value
 * @method \Flywheel\Db\Type\DateTime getRecipientAmountReimbursementTime() get recipient_amount_reimbursement_time value
 * @method static \Complaints[] findByRecipientAmountReimbursementTime(\Flywheel\Db\Type\DateTime $recipient_amount_reimbursement_time) findByRecipientAmountReimbursementTime(string $recipient_amount_reimbursement_time) find objects in database by recipient_amount_reimbursement_time
 * @method static \Complaints findOneByRecipientAmountReimbursementTime(\Flywheel\Db\Type\DateTime $recipient_amount_reimbursement_time) findOneByRecipientAmountReimbursementTime(string $recipient_amount_reimbursement_time) find object in database by recipient_amount_reimbursement_time
 * @method static \Complaints retrieveByRecipientAmountReimbursementTime(\Flywheel\Db\Type\DateTime $recipient_amount_reimbursement_time) retrieveByRecipientAmountReimbursementTime(string $recipient_amount_reimbursement_time) retrieve object from poll by recipient_amount_reimbursement_time, get it from db if not exist in poll

 * @method void setRecipientAmountReimbursement(number $recipient_amount_reimbursement) set recipient_amount_reimbursement value
 * @method number getRecipientAmountReimbursement() get recipient_amount_reimbursement value
 * @method static \Complaints[] findByRecipientAmountReimbursement(number $recipient_amount_reimbursement) find objects in database by recipient_amount_reimbursement
 * @method static \Complaints findOneByRecipientAmountReimbursement(number $recipient_amount_reimbursement) find object in database by recipient_amount_reimbursement
 * @method static \Complaints retrieveByRecipientAmountReimbursement(number $recipient_amount_reimbursement) retrieve object from poll by recipient_amount_reimbursement, get it from db if not exist in poll

 * @method void setDamageAmount(number $damage_amount) set damage_amount value
 * @method number getDamageAmount() get damage_amount value
 * @method static \Complaints[] findByDamageAmount(number $damage_amount) find objects in database by damage_amount
 * @method static \Complaints findOneByDamageAmount(number $damage_amount) find object in database by damage_amount
 * @method static \Complaints retrieveByDamageAmount(number $damage_amount) retrieve object from poll by damage_amount, get it from db if not exist in poll

 * @method void setDamage(string $damage) set damage value
 * @method string getDamage() get damage value
 * @method static \Complaints[] findByDamage(string $damage) find objects in database by damage
 * @method static \Complaints findOneByDamage(string $damage) find object in database by damage
 * @method static \Complaints retrieveByDamage(string $damage) retrieve object from poll by damage, get it from db if not exist in poll

 * @method void setDescription(string $description) set description value
 * @method string getDescription() get description value
 * @method static \Complaints[] findByDescription(string $description) find objects in database by description
 * @method static \Complaints findOneByDescription(string $description) find object in database by description
 * @method static \Complaints retrieveByDescription(string $description) retrieve object from poll by description, get it from db if not exist in poll

 * @method void setTransaction(string $transaction) set transaction value
 * @method string getTransaction() get transaction value
 * @method static \Complaints[] findByTransaction(string $transaction) find objects in database by transaction
 * @method static \Complaints findOneByTransaction(string $transaction) find object in database by transaction
 * @method static \Complaints retrieveByTransaction(string $transaction) retrieve object from poll by transaction, get it from db if not exist in poll

 * @method void setCustomerDelivery(string $customer_delivery) set customer_delivery value
 * @method string getCustomerDelivery() get customer_delivery value
 * @method static \Complaints[] findByCustomerDelivery(string $customer_delivery) find objects in database by customer_delivery
 * @method static \Complaints findOneByCustomerDelivery(string $customer_delivery) find object in database by customer_delivery
 * @method static \Complaints retrieveByCustomerDelivery(string $customer_delivery) retrieve object from poll by customer_delivery, get it from db if not exist in poll

 * @method void setErrorDivisionCompany(string $error_division_company) set error_division_company value
 * @method string getErrorDivisionCompany() get error_division_company value
 * @method static \Complaints[] findByErrorDivisionCompany(string $error_division_company) find objects in database by error_division_company
 * @method static \Complaints findOneByErrorDivisionCompany(string $error_division_company) find object in database by error_division_company
 * @method static \Complaints retrieveByErrorDivisionCompany(string $error_division_company) retrieve object from poll by error_division_company, get it from db if not exist in poll

 * @method void setErrorPartner(string $error_partner) set error_partner value
 * @method string getErrorPartner() get error_partner value
 * @method static \Complaints[] findByErrorPartner(string $error_partner) find objects in database by error_partner
 * @method static \Complaints findOneByErrorPartner(string $error_partner) find object in database by error_partner
 * @method static \Complaints retrieveByErrorPartner(string $error_partner) retrieve object from poll by error_partner, get it from db if not exist in poll

 * @method void setErrorSeller(string $error_seller) set error_seller value
 * @method string getErrorSeller() get error_seller value
 * @method static \Complaints[] findByErrorSeller(string $error_seller) find objects in database by error_seller
 * @method static \Complaints findOneByErrorSeller(string $error_seller) find object in database by error_seller
 * @method static \Complaints retrieveByErrorSeller(string $error_seller) retrieve object from poll by error_seller, get it from db if not exist in poll


 */
abstract class ComplaintsBase extends ActiveRecord {
    protected static $_tableName = 'complaints';
    protected static $_phpName = 'Complaints';
    protected static $_pk = 'id';
    protected static $_alias = 'c';
    protected static $_dbConnectName = 'complaints';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'title' => array('name' => 'title',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'status' => array('name' => 'status',
                'default' => 'WAITING_RECEIVE',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'type' => array('name' => 'type',
                'default' => 'PRODUCT_ERROR',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'quantity' => array('name' => 'quantity',
                'default' => 0,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float(255,0)',
                'length' => 255),
        'item_code' => array('name' => 'item_code',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'item_id' => array('name' => 'item_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_code' => array('name' => 'order_code',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'order_id' => array('name' => 'order_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'buyer_id' => array('name' => 'buyer_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'recipient_by' => array('name' => 'recipient_by',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'accept_by' => array('name' => 'accept_by',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'approval_by' => array('name' => 'approval_by',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'reject_by' => array('name' => 'reject_by',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'create_time' => array('name' => 'create_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'recipient_time' => array('name' => 'recipient_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'accept_time' => array('name' => 'accept_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'approval_time' => array('name' => 'approval_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'reject_time' => array('name' => 'reject_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'refocus_time' => array('name' => 'refocus_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'customer_amount_reimbursement_time' => array('name' => 'customer_amount_reimbursement_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'customer_amount_reimbursement' => array('name' => 'customer_amount_reimbursement',
                'default' => 0,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(255,0)',
                'length' => 255),
        'recipient_amount_reimbursement_time' => array('name' => 'recipient_amount_reimbursement_time',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'recipient_amount_reimbursement' => array('name' => 'recipient_amount_reimbursement',
                'default' => 0,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(255,0)',
                'length' => 255),
        'damage_amount' => array('name' => 'damage_amount',
                'default' => 0,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'damage' => array('name' => 'damage',
                'default' => 'NO',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'description' => array('name' => 'description',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'tinytext'),
        'transaction' => array('name' => 'transaction',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'customer_delivery' => array('name' => 'customer_delivery',
                'default' => 'NONE',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'error_division_company' => array('name' => 'error_division_company',
                'default' => 'NO',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'error_partner' => array('name' => 'error_partner',
                'default' => 'NO',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'error_seller' => array('name' => 'error_seller',
                'default' => 'NO',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','title','status','type','quantity','item_code','item_id','order_code','order_id','buyer_id','recipient_by','accept_by','approval_by','reject_by','create_time','recipient_time','accept_time','approval_time','reject_time','refocus_time','customer_amount_reimbursement_time','customer_amount_reimbursement','recipient_amount_reimbursement_time','recipient_amount_reimbursement','damage_amount','damage','description','transaction','customer_delivery','error_division_company','error_partner','error_seller');

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