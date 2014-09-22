<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * DomesticShipping
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $code code type : char(20) max_length : 20
 * @property integer $user_address_id user_address_id type : int(11)
 * @property integer $user_id user_id type : int(11)
 * @property number $weight weight type : float(11,2)
 * @property string $warehouse warehouse type : char(10) max_length : 10
 * @property number $domestic_shipping_fee domestic_shipping_fee type : double
 * @property string $purpose_charge_fee purpose_charge_fee type : varchar(255) max_length : 255
 * @property number $cod cod type : double
 * @property number $payment_amount payment_amount type : double
 * @property string $transaction_code transaction_code type : varchar(50) max_length : 50
 * @property integer $created_by created_by type : int(11)
 * @property string $shipper_mobile shipper_mobile type : char(25) max_length : 25
 * @property datetime $created_time created_time type : datetime
 * @property datetime $updated_time updated_time type : datetime
 * @property string $status status type : char(15) max_length : 15
 * @property number $real_cod real_cod type : double

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \DomesticShipping[] findById(integer $id) find objects in database by id
 * @method static \DomesticShipping findOneById(integer $id) find object in database by id
 * @method static \DomesticShipping retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCode(string $code) set code value
 * @method string getCode() get code value
 * @method static \DomesticShipping[] findByCode(string $code) find objects in database by code
 * @method static \DomesticShipping findOneByCode(string $code) find object in database by code
 * @method static \DomesticShipping retrieveByCode(string $code) retrieve object from poll by code, get it from db if not exist in poll

 * @method void setUserAddressId(integer $user_address_id) set user_address_id value
 * @method integer getUserAddressId() get user_address_id value
 * @method static \DomesticShipping[] findByUserAddressId(integer $user_address_id) find objects in database by user_address_id
 * @method static \DomesticShipping findOneByUserAddressId(integer $user_address_id) find object in database by user_address_id
 * @method static \DomesticShipping retrieveByUserAddressId(integer $user_address_id) retrieve object from poll by user_address_id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \DomesticShipping[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \DomesticShipping findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \DomesticShipping retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setWeight(number $weight) set weight value
 * @method number getWeight() get weight value
 * @method static \DomesticShipping[] findByWeight(number $weight) find objects in database by weight
 * @method static \DomesticShipping findOneByWeight(number $weight) find object in database by weight
 * @method static \DomesticShipping retrieveByWeight(number $weight) retrieve object from poll by weight, get it from db if not exist in poll

 * @method void setWarehouse(string $warehouse) set warehouse value
 * @method string getWarehouse() get warehouse value
 * @method static \DomesticShipping[] findByWarehouse(string $warehouse) find objects in database by warehouse
 * @method static \DomesticShipping findOneByWarehouse(string $warehouse) find object in database by warehouse
 * @method static \DomesticShipping retrieveByWarehouse(string $warehouse) retrieve object from poll by warehouse, get it from db if not exist in poll

 * @method void setDomesticShippingFee(number $domestic_shipping_fee) set domestic_shipping_fee value
 * @method number getDomesticShippingFee() get domestic_shipping_fee value
 * @method static \DomesticShipping[] findByDomesticShippingFee(number $domestic_shipping_fee) find objects in database by domestic_shipping_fee
 * @method static \DomesticShipping findOneByDomesticShippingFee(number $domestic_shipping_fee) find object in database by domestic_shipping_fee
 * @method static \DomesticShipping retrieveByDomesticShippingFee(number $domestic_shipping_fee) retrieve object from poll by domestic_shipping_fee, get it from db if not exist in poll

 * @method void setPurposeChargeFee(string $purpose_charge_fee) set purpose_charge_fee value
 * @method string getPurposeChargeFee() get purpose_charge_fee value
 * @method static \DomesticShipping[] findByPurposeChargeFee(string $purpose_charge_fee) find objects in database by purpose_charge_fee
 * @method static \DomesticShipping findOneByPurposeChargeFee(string $purpose_charge_fee) find object in database by purpose_charge_fee
 * @method static \DomesticShipping retrieveByPurposeChargeFee(string $purpose_charge_fee) retrieve object from poll by purpose_charge_fee, get it from db if not exist in poll

 * @method void setCod(number $cod) set cod value
 * @method number getCod() get cod value
 * @method static \DomesticShipping[] findByCod(number $cod) find objects in database by cod
 * @method static \DomesticShipping findOneByCod(number $cod) find object in database by cod
 * @method static \DomesticShipping retrieveByCod(number $cod) retrieve object from poll by cod, get it from db if not exist in poll

 * @method void setPaymentAmount(number $payment_amount) set payment_amount value
 * @method number getPaymentAmount() get payment_amount value
 * @method static \DomesticShipping[] findByPaymentAmount(number $payment_amount) find objects in database by payment_amount
 * @method static \DomesticShipping findOneByPaymentAmount(number $payment_amount) find object in database by payment_amount
 * @method static \DomesticShipping retrieveByPaymentAmount(number $payment_amount) retrieve object from poll by payment_amount, get it from db if not exist in poll

 * @method void setTransactionCode(string $transaction_code) set transaction_code value
 * @method string getTransactionCode() get transaction_code value
 * @method static \DomesticShipping[] findByTransactionCode(string $transaction_code) find objects in database by transaction_code
 * @method static \DomesticShipping findOneByTransactionCode(string $transaction_code) find object in database by transaction_code
 * @method static \DomesticShipping retrieveByTransactionCode(string $transaction_code) retrieve object from poll by transaction_code, get it from db if not exist in poll

 * @method void setCreatedBy(integer $created_by) set created_by value
 * @method integer getCreatedBy() get created_by value
 * @method static \DomesticShipping[] findByCreatedBy(integer $created_by) find objects in database by created_by
 * @method static \DomesticShipping findOneByCreatedBy(integer $created_by) find object in database by created_by
 * @method static \DomesticShipping retrieveByCreatedBy(integer $created_by) retrieve object from poll by created_by, get it from db if not exist in poll

 * @method void setShipperMobile(string $shipper_mobile) set shipper_mobile value
 * @method string getShipperMobile() get shipper_mobile value
 * @method static \DomesticShipping[] findByShipperMobile(string $shipper_mobile) find objects in database by shipper_mobile
 * @method static \DomesticShipping findOneByShipperMobile(string $shipper_mobile) find object in database by shipper_mobile
 * @method static \DomesticShipping retrieveByShipperMobile(string $shipper_mobile) retrieve object from poll by shipper_mobile, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \DomesticShipping[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \DomesticShipping findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \DomesticShipping retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) setUpdatedTime(string $updated_time) set updated_time value
 * @method \Flywheel\Db\Type\DateTime getUpdatedTime() get updated_time value
 * @method static \DomesticShipping[] findByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) findByUpdatedTime(string $updated_time) find objects in database by updated_time
 * @method static \DomesticShipping findOneByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) findOneByUpdatedTime(string $updated_time) find object in database by updated_time
 * @method static \DomesticShipping retrieveByUpdatedTime(\Flywheel\Db\Type\DateTime $updated_time) retrieveByUpdatedTime(string $updated_time) retrieve object from poll by updated_time, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \DomesticShipping[] findByStatus(string $status) find objects in database by status
 * @method static \DomesticShipping findOneByStatus(string $status) find object in database by status
 * @method static \DomesticShipping retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setRealCod(number $real_cod) set real_cod value
 * @method number getRealCod() get real_cod value
 * @method static \DomesticShipping[] findByRealCod(number $real_cod) find objects in database by real_cod
 * @method static \DomesticShipping findOneByRealCod(number $real_cod) find object in database by real_cod
 * @method static \DomesticShipping retrieveByRealCod(number $real_cod) retrieve object from poll by real_cod, get it from db if not exist in poll


 */
abstract class DomesticShippingBase extends ActiveRecord {
    protected static $_tableName = 'domestic_shipping';
    protected static $_phpName = 'DomesticShipping';
    protected static $_pk = 'id';
    protected static $_alias = 'd';
    protected static $_dbConnectName = 'domestic_shipping';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11)',
                'length' => 4),
        'code' => array('name' => 'code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(20)',
                'length' => 20),
        'user_address_id' => array('name' => 'user_address_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'weight' => array('name' => 'weight',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float(11,2)',
                'length' => 11),
        'warehouse' => array('name' => 'warehouse',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(10)',
                'length' => 10),
        'domestic_shipping_fee' => array('name' => 'domestic_shipping_fee',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'purpose_charge_fee' => array('name' => 'purpose_charge_fee',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'cod' => array('name' => 'cod',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'payment_amount' => array('name' => 'payment_amount',
                'default' => 0,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'transaction_code' => array('name' => 'transaction_code',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'created_by' => array('name' => 'created_by',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'shipper_mobile' => array('name' => 'shipper_mobile',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(25)',
                'length' => 25),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'updated_time' => array('name' => 'updated_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'status' => array('name' => 'status',
                'default' => 'ACTIVE',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(15)',
                'length' => 15),
        'real_cod' => array('name' => 'real_cod',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','code','user_address_id','user_id','weight','warehouse','domestic_shipping_fee','purpose_charge_fee','cod','payment_amount','transaction_code','created_by','shipper_mobile','created_time','updated_time','status','real_cod');

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