<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Packages
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $package_code package_code type : varchar(50) max_length : 50
 * @property string $logistic_package_barcode logistic_package_barcode type : varchar(20) max_length : 20
 * @property integer $order_id order_id type : int(10) unsigned
 * @property string $freight_bill freight_bill type : varchar(50) max_length : 50
 * @property string $note note type : varchar(255) max_length : 255
 * @property integer $created_by created_by type : int(10) unsigned
 * @property string $current_warehouse current_warehouse type : varchar(100) max_length : 100
 * @property string $warehouse_status warehouse_status type : varchar(20) max_length : 20
 * @property datetime $warehouse_status_in_time warehouse_status_in_time type : datetime
 * @property datetime $warehouse_status_out_time warehouse_status_out_time type : datetime
 * @property number $weight weight type : float
 * @property integer $total_quantity total_quantity type : int(11)
 * @property integer $total_checking total_checking type : int(2)
 * @property datetime $created_time created_time type : datetime
 * @property string $level level type : varchar(10) max_length : 10
 * @property integer $buyer_id buyer_id type : int(11)
 * @property integer $user_address_id user_address_id type : int(11)
 * @property string $distribution_warehouse distribution_warehouse type : varchar(100) max_length : 100
 * @property datetime $modified_time modified_time type : datetime
 * @property string $status status type : varchar(50) max_length : 50
 * @property datetime $seller_delivered_time seller_delivered_time type : datetime
 * @property datetime $received_from_seller_time received_from_seller_time type : datetime
 * @property datetime $transporting_time transporting_time type : datetime
 * @property datetime $waiting_delivery_time waiting_delivery_time type : datetime
 * @property datetime $confirm_delivery_time confirm_delivery_time type : datetime
 * @property datetime $received_time received_time type : datetime
 * @property datetime $package_modified_time package_modified_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Packages[] findById(integer $id) find objects in database by id
 * @method static \Packages findOneById(integer $id) find object in database by id
 * @method static \Packages retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setPackageCode(string $package_code) set package_code value
 * @method string getPackageCode() get package_code value
 * @method static \Packages[] findByPackageCode(string $package_code) find objects in database by package_code
 * @method static \Packages findOneByPackageCode(string $package_code) find object in database by package_code
 * @method static \Packages retrieveByPackageCode(string $package_code) retrieve object from poll by package_code, get it from db if not exist in poll

 * @method void setLogisticPackageBarcode(string $logistic_package_barcode) set logistic_package_barcode value
 * @method string getLogisticPackageBarcode() get logistic_package_barcode value
 * @method static \Packages[] findByLogisticPackageBarcode(string $logistic_package_barcode) find objects in database by logistic_package_barcode
 * @method static \Packages findOneByLogisticPackageBarcode(string $logistic_package_barcode) find object in database by logistic_package_barcode
 * @method static \Packages retrieveByLogisticPackageBarcode(string $logistic_package_barcode) retrieve object from poll by logistic_package_barcode, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \Packages[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \Packages findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \Packages retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setFreightBill(string $freight_bill) set freight_bill value
 * @method string getFreightBill() get freight_bill value
 * @method static \Packages[] findByFreightBill(string $freight_bill) find objects in database by freight_bill
 * @method static \Packages findOneByFreightBill(string $freight_bill) find object in database by freight_bill
 * @method static \Packages retrieveByFreightBill(string $freight_bill) retrieve object from poll by freight_bill, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \Packages[] findByNote(string $note) find objects in database by note
 * @method static \Packages findOneByNote(string $note) find object in database by note
 * @method static \Packages retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \Packages[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \Packages findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \Packages retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setCurrentWarehouse(string $current_warehouse) set current_warehouse value
 * @method string getCurrentWarehouse() get current_warehouse value
 * @method static \Packages[] findByCurrentWarehouse(string $current_warehouse) find objects in database by current_warehouse
 * @method static \Packages findOneByCurrentWarehouse(string $current_warehouse) find object in database by current_warehouse
 * @method static \Packages retrieveByCurrentWarehouse(string $current_warehouse) retrieve object from poll by current_warehouse, get it from db if not exist in poll

 * @method void setWarehouseStatus(string $warehouse_status) set warehouse_status value
 * @method string getWarehouseStatus() get warehouse_status value
 * @method static \Packages[] findByWarehouseStatus(string $warehouse_status) find objects in database by warehouse_status
 * @method static \Packages findOneByWarehouseStatus(string $warehouse_status) find object in database by warehouse_status
 * @method static \Packages retrieveByWarehouseStatus(string $warehouse_status) retrieve object from poll by warehouse_status, get it from db if not exist in poll

 * @method void setWarehouseStatusInTime(\Flywheel\Db\Type\DateTime $warehouse_status_in_time) setWarehouseStatusInTime(string $warehouse_status_in_time) set warehouse_status_in_time value
 * @method \Flywheel\Db\Type\DateTime getWarehouseStatusInTime() get warehouse_status_in_time value
 * @method static \Packages[] findByWarehouseStatusInTime(\Flywheel\Db\Type\DateTime $warehouse_status_in_time) findByWarehouseStatusInTime(string $warehouse_status_in_time) find objects in database by warehouse_status_in_time
 * @method static \Packages findOneByWarehouseStatusInTime(\Flywheel\Db\Type\DateTime $warehouse_status_in_time) findOneByWarehouseStatusInTime(string $warehouse_status_in_time) find object in database by warehouse_status_in_time
 * @method static \Packages retrieveByWarehouseStatusInTime(\Flywheel\Db\Type\DateTime $warehouse_status_in_time) retrieveByWarehouseStatusInTime(string $warehouse_status_in_time) retrieve object from poll by warehouse_status_in_time, get it from db if not exist in poll

 * @method void setWarehouseStatusOutTime(\Flywheel\Db\Type\DateTime $warehouse_status_out_time) setWarehouseStatusOutTime(string $warehouse_status_out_time) set warehouse_status_out_time value
 * @method \Flywheel\Db\Type\DateTime getWarehouseStatusOutTime() get warehouse_status_out_time value
 * @method static \Packages[] findByWarehouseStatusOutTime(\Flywheel\Db\Type\DateTime $warehouse_status_out_time) findByWarehouseStatusOutTime(string $warehouse_status_out_time) find objects in database by warehouse_status_out_time
 * @method static \Packages findOneByWarehouseStatusOutTime(\Flywheel\Db\Type\DateTime $warehouse_status_out_time) findOneByWarehouseStatusOutTime(string $warehouse_status_out_time) find object in database by warehouse_status_out_time
 * @method static \Packages retrieveByWarehouseStatusOutTime(\Flywheel\Db\Type\DateTime $warehouse_status_out_time) retrieveByWarehouseStatusOutTime(string $warehouse_status_out_time) retrieve object from poll by warehouse_status_out_time, get it from db if not exist in poll

 * @method void setWeight(number $weight) set weight value
 * @method number getWeight() get weight value
 * @method static \Packages[] findByWeight(number $weight) find objects in database by weight
 * @method static \Packages findOneByWeight(number $weight) find object in database by weight
 * @method static \Packages retrieveByWeight(number $weight) retrieve object from poll by weight, get it from db if not exist in poll

 * @method void setTotalQuantity(integer $total_quantity) set total_quantity value
 * @method integer getTotalQuantity() get total_quantity value
 * @method static \Packages[] findByTotalQuantity(integer $total_quantity) find objects in database by total_quantity
 * @method static \Packages findOneByTotalQuantity(integer $total_quantity) find object in database by total_quantity
 * @method static \Packages retrieveByTotalQuantity(integer $total_quantity) retrieve object from poll by total_quantity, get it from db if not exist in poll

 * @method void setTotalChecking(integer $total_checking) set total_checking value
 * @method integer getTotalChecking() get total_checking value
 * @method static \Packages[] findByTotalChecking(integer $total_checking) find objects in database by total_checking
 * @method static \Packages findOneByTotalChecking(integer $total_checking) find object in database by total_checking
 * @method static \Packages retrieveByTotalChecking(integer $total_checking) retrieve object from poll by total_checking, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \Packages[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \Packages findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \Packages retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setLevel(string $level) set level value
 * @method string getLevel() get level value
 * @method static \Packages[] findByLevel(string $level) find objects in database by level
 * @method static \Packages findOneByLevel(string $level) find object in database by level
 * @method static \Packages retrieveByLevel(string $level) retrieve object from poll by level, get it from db if not exist in poll

 * @method void setBuyerId(integer $buyer_id) set buyer_id value
 * @method integer getBuyerId() get buyer_id value
 * @method static \Packages[] findByBuyerId(integer $buyer_id) find objects in database by buyer_id
 * @method static \Packages findOneByBuyerId(integer $buyer_id) find object in database by buyer_id
 * @method static \Packages retrieveByBuyerId(integer $buyer_id) retrieve object from poll by buyer_id, get it from db if not exist in poll

 * @method void setUserAddressId(integer $user_address_id) set user_address_id value
 * @method integer getUserAddressId() get user_address_id value
 * @method static \Packages[] findByUserAddressId(integer $user_address_id) find objects in database by user_address_id
 * @method static \Packages findOneByUserAddressId(integer $user_address_id) find object in database by user_address_id
 * @method static \Packages retrieveByUserAddressId(integer $user_address_id) retrieve object from poll by user_address_id, get it from db if not exist in poll

 * @method void setDistributionWarehouse(string $distribution_warehouse) set distribution_warehouse value
 * @method string getDistributionWarehouse() get distribution_warehouse value
 * @method static \Packages[] findByDistributionWarehouse(string $distribution_warehouse) find objects in database by distribution_warehouse
 * @method static \Packages findOneByDistributionWarehouse(string $distribution_warehouse) find object in database by distribution_warehouse
 * @method static \Packages retrieveByDistributionWarehouse(string $distribution_warehouse) retrieve object from poll by distribution_warehouse, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \Packages[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \Packages findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \Packages retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Packages[] findByStatus(string $status) find objects in database by status
 * @method static \Packages findOneByStatus(string $status) find object in database by status
 * @method static \Packages retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setSellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) setSellerDeliveredTime(string $seller_delivered_time) set seller_delivered_time value
 * @method \Flywheel\Db\Type\DateTime getSellerDeliveredTime() get seller_delivered_time value
 * @method static \Packages[] findBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) findBySellerDeliveredTime(string $seller_delivered_time) find objects in database by seller_delivered_time
 * @method static \Packages findOneBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) findOneBySellerDeliveredTime(string $seller_delivered_time) find object in database by seller_delivered_time
 * @method static \Packages retrieveBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) retrieveBySellerDeliveredTime(string $seller_delivered_time) retrieve object from poll by seller_delivered_time, get it from db if not exist in poll

 * @method void setReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) setReceivedFromSellerTime(string $received_from_seller_time) set received_from_seller_time value
 * @method \Flywheel\Db\Type\DateTime getReceivedFromSellerTime() get received_from_seller_time value
 * @method static \Packages[] findByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) findByReceivedFromSellerTime(string $received_from_seller_time) find objects in database by received_from_seller_time
 * @method static \Packages findOneByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) findOneByReceivedFromSellerTime(string $received_from_seller_time) find object in database by received_from_seller_time
 * @method static \Packages retrieveByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) retrieveByReceivedFromSellerTime(string $received_from_seller_time) retrieve object from poll by received_from_seller_time, get it from db if not exist in poll

 * @method void setTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) setTransportingTime(string $transporting_time) set transporting_time value
 * @method \Flywheel\Db\Type\DateTime getTransportingTime() get transporting_time value
 * @method static \Packages[] findByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) findByTransportingTime(string $transporting_time) find objects in database by transporting_time
 * @method static \Packages findOneByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) findOneByTransportingTime(string $transporting_time) find object in database by transporting_time
 * @method static \Packages retrieveByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) retrieveByTransportingTime(string $transporting_time) retrieve object from poll by transporting_time, get it from db if not exist in poll

 * @method void setWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) setWaitingDeliveryTime(string $waiting_delivery_time) set waiting_delivery_time value
 * @method \Flywheel\Db\Type\DateTime getWaitingDeliveryTime() get waiting_delivery_time value
 * @method static \Packages[] findByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) findByWaitingDeliveryTime(string $waiting_delivery_time) find objects in database by waiting_delivery_time
 * @method static \Packages findOneByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) findOneByWaitingDeliveryTime(string $waiting_delivery_time) find object in database by waiting_delivery_time
 * @method static \Packages retrieveByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) retrieveByWaitingDeliveryTime(string $waiting_delivery_time) retrieve object from poll by waiting_delivery_time, get it from db if not exist in poll

 * @method void setConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) setConfirmDeliveryTime(string $confirm_delivery_time) set confirm_delivery_time value
 * @method \Flywheel\Db\Type\DateTime getConfirmDeliveryTime() get confirm_delivery_time value
 * @method static \Packages[] findByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) findByConfirmDeliveryTime(string $confirm_delivery_time) find objects in database by confirm_delivery_time
 * @method static \Packages findOneByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) findOneByConfirmDeliveryTime(string $confirm_delivery_time) find object in database by confirm_delivery_time
 * @method static \Packages retrieveByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) retrieveByConfirmDeliveryTime(string $confirm_delivery_time) retrieve object from poll by confirm_delivery_time, get it from db if not exist in poll

 * @method void setReceivedTime(\Flywheel\Db\Type\DateTime $received_time) setReceivedTime(string $received_time) set received_time value
 * @method \Flywheel\Db\Type\DateTime getReceivedTime() get received_time value
 * @method static \Packages[] findByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) findByReceivedTime(string $received_time) find objects in database by received_time
 * @method static \Packages findOneByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) findOneByReceivedTime(string $received_time) find object in database by received_time
 * @method static \Packages retrieveByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) retrieveByReceivedTime(string $received_time) retrieve object from poll by received_time, get it from db if not exist in poll

 * @method void setPackageModifiedTime(\Flywheel\Db\Type\DateTime $package_modified_time) setPackageModifiedTime(string $package_modified_time) set package_modified_time value
 * @method \Flywheel\Db\Type\DateTime getPackageModifiedTime() get package_modified_time value
 * @method static \Packages[] findByPackageModifiedTime(\Flywheel\Db\Type\DateTime $package_modified_time) findByPackageModifiedTime(string $package_modified_time) find objects in database by package_modified_time
 * @method static \Packages findOneByPackageModifiedTime(\Flywheel\Db\Type\DateTime $package_modified_time) findOneByPackageModifiedTime(string $package_modified_time) find object in database by package_modified_time
 * @method static \Packages retrieveByPackageModifiedTime(\Flywheel\Db\Type\DateTime $package_modified_time) retrieveByPackageModifiedTime(string $package_modified_time) retrieve object from poll by package_modified_time, get it from db if not exist in poll


 */
abstract class PackagesBase extends ActiveRecord {
    protected static $_tableName = 'packages';
    protected static $_phpName = 'Packages';
    protected static $_pk = 'id';
    protected static $_alias = 'p';
    protected static $_dbConnectName = 'packages';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'package_code' => array('name' => 'package_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'logistic_package_barcode' => array('name' => 'logistic_package_barcode',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(10) unsigned',
                'length' => 4),
        'freight_bill' => array('name' => 'freight_bill',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'note' => array('name' => 'note',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'created_by' => array('name' => 'created_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(10) unsigned',
                'length' => 4),
        'current_warehouse' => array('name' => 'current_warehouse',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'warehouse_status' => array('name' => 'warehouse_status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'warehouse_status_in_time' => array('name' => 'warehouse_status_in_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'warehouse_status_out_time' => array('name' => 'warehouse_status_out_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'weight' => array('name' => 'weight',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'total_quantity' => array('name' => 'total_quantity',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'total_checking' => array('name' => 'total_checking',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(2)',
                'length' => 4),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'level' => array('name' => 'level',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(10)',
                'length' => 10),
        'buyer_id' => array('name' => 'buyer_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_address_id' => array('name' => 'user_address_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'distribution_warehouse' => array('name' => 'distribution_warehouse',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'modified_time' => array('name' => 'modified_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'seller_delivered_time' => array('name' => 'seller_delivered_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'received_from_seller_time' => array('name' => 'received_from_seller_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'transporting_time' => array('name' => 'transporting_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'waiting_delivery_time' => array('name' => 'waiting_delivery_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'confirm_delivery_time' => array('name' => 'confirm_delivery_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'received_time' => array('name' => 'received_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'package_modified_time' => array('name' => 'package_modified_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','package_code','logistic_package_barcode','order_id','freight_bill','note','created_by','current_warehouse','warehouse_status','warehouse_status_in_time','warehouse_status_out_time','weight','total_quantity','total_checking','created_time','level','buyer_id','user_address_id','distribution_warehouse','modified_time','status','seller_delivered_time','received_from_seller_time','transporting_time','waiting_delivery_time','confirm_delivery_time','received_time','package_modified_time');

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