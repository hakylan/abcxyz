<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * DebugRefundDiscount
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $user_id user_id type : int(11)
 * @property integer $order_id order_id type : int(11)
 * @property number $fee_buying fee_buying type : float
 * @property number $fee_checking fee_checking type : float
 * @property number $fee_transport fee_transport type : float
 * @property number $fee_fixed fee_fixed type : float
 * @property string $note note type : text max_length : 
 * @property number $money_refund money_refund type : double
 * @property number $point point type : double
 * @property number $total_point total_point type : double
 * @property string $old_level old_level type : varchar(100) max_length : 100
 * @property string $new_level new_level type : varchar(100) max_length : 100
 * @property integer $status_calculated status_calculated type : tinyint(1)
 * @property integer $service_buying_id service_buying_id type : int(11)
 * @property integer $service_checking_id service_checking_id type : int(11)
 * @property integer $service_transport_id service_transport_id type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \DebugRefundDiscount[] findById(integer $id) find objects in database by id
 * @method static \DebugRefundDiscount findOneById(integer $id) find object in database by id
 * @method static \DebugRefundDiscount retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setUserId(integer $user_id) set user_id value
 * @method integer getUserId() get user_id value
 * @method static \DebugRefundDiscount[] findByUserId(integer $user_id) find objects in database by user_id
 * @method static \DebugRefundDiscount findOneByUserId(integer $user_id) find object in database by user_id
 * @method static \DebugRefundDiscount retrieveByUserId(integer $user_id) retrieve object from poll by user_id, get it from db if not exist in poll

 * @method void setOrderId(integer $order_id) set order_id value
 * @method integer getOrderId() get order_id value
 * @method static \DebugRefundDiscount[] findByOrderId(integer $order_id) find objects in database by order_id
 * @method static \DebugRefundDiscount findOneByOrderId(integer $order_id) find object in database by order_id
 * @method static \DebugRefundDiscount retrieveByOrderId(integer $order_id) retrieve object from poll by order_id, get it from db if not exist in poll

 * @method void setFeeBuying(number $fee_buying) set fee_buying value
 * @method number getFeeBuying() get fee_buying value
 * @method static \DebugRefundDiscount[] findByFeeBuying(number $fee_buying) find objects in database by fee_buying
 * @method static \DebugRefundDiscount findOneByFeeBuying(number $fee_buying) find object in database by fee_buying
 * @method static \DebugRefundDiscount retrieveByFeeBuying(number $fee_buying) retrieve object from poll by fee_buying, get it from db if not exist in poll

 * @method void setFeeChecking(number $fee_checking) set fee_checking value
 * @method number getFeeChecking() get fee_checking value
 * @method static \DebugRefundDiscount[] findByFeeChecking(number $fee_checking) find objects in database by fee_checking
 * @method static \DebugRefundDiscount findOneByFeeChecking(number $fee_checking) find object in database by fee_checking
 * @method static \DebugRefundDiscount retrieveByFeeChecking(number $fee_checking) retrieve object from poll by fee_checking, get it from db if not exist in poll

 * @method void setFeeTransport(number $fee_transport) set fee_transport value
 * @method number getFeeTransport() get fee_transport value
 * @method static \DebugRefundDiscount[] findByFeeTransport(number $fee_transport) find objects in database by fee_transport
 * @method static \DebugRefundDiscount findOneByFeeTransport(number $fee_transport) find object in database by fee_transport
 * @method static \DebugRefundDiscount retrieveByFeeTransport(number $fee_transport) retrieve object from poll by fee_transport, get it from db if not exist in poll

 * @method void setFeeFixed(number $fee_fixed) set fee_fixed value
 * @method number getFeeFixed() get fee_fixed value
 * @method static \DebugRefundDiscount[] findByFeeFixed(number $fee_fixed) find objects in database by fee_fixed
 * @method static \DebugRefundDiscount findOneByFeeFixed(number $fee_fixed) find object in database by fee_fixed
 * @method static \DebugRefundDiscount retrieveByFeeFixed(number $fee_fixed) retrieve object from poll by fee_fixed, get it from db if not exist in poll

 * @method void setNote(string $note) set note value
 * @method string getNote() get note value
 * @method static \DebugRefundDiscount[] findByNote(string $note) find objects in database by note
 * @method static \DebugRefundDiscount findOneByNote(string $note) find object in database by note
 * @method static \DebugRefundDiscount retrieveByNote(string $note) retrieve object from poll by note, get it from db if not exist in poll

 * @method void setMoneyRefund(number $money_refund) set money_refund value
 * @method number getMoneyRefund() get money_refund value
 * @method static \DebugRefundDiscount[] findByMoneyRefund(number $money_refund) find objects in database by money_refund
 * @method static \DebugRefundDiscount findOneByMoneyRefund(number $money_refund) find object in database by money_refund
 * @method static \DebugRefundDiscount retrieveByMoneyRefund(number $money_refund) retrieve object from poll by money_refund, get it from db if not exist in poll

 * @method void setPoint(number $point) set point value
 * @method number getPoint() get point value
 * @method static \DebugRefundDiscount[] findByPoint(number $point) find objects in database by point
 * @method static \DebugRefundDiscount findOneByPoint(number $point) find object in database by point
 * @method static \DebugRefundDiscount retrieveByPoint(number $point) retrieve object from poll by point, get it from db if not exist in poll

 * @method void setTotalPoint(number $total_point) set total_point value
 * @method number getTotalPoint() get total_point value
 * @method static \DebugRefundDiscount[] findByTotalPoint(number $total_point) find objects in database by total_point
 * @method static \DebugRefundDiscount findOneByTotalPoint(number $total_point) find object in database by total_point
 * @method static \DebugRefundDiscount retrieveByTotalPoint(number $total_point) retrieve object from poll by total_point, get it from db if not exist in poll

 * @method void setOldLevel(string $old_level) set old_level value
 * @method string getOldLevel() get old_level value
 * @method static \DebugRefundDiscount[] findByOldLevel(string $old_level) find objects in database by old_level
 * @method static \DebugRefundDiscount findOneByOldLevel(string $old_level) find object in database by old_level
 * @method static \DebugRefundDiscount retrieveByOldLevel(string $old_level) retrieve object from poll by old_level, get it from db if not exist in poll

 * @method void setNewLevel(string $new_level) set new_level value
 * @method string getNewLevel() get new_level value
 * @method static \DebugRefundDiscount[] findByNewLevel(string $new_level) find objects in database by new_level
 * @method static \DebugRefundDiscount findOneByNewLevel(string $new_level) find object in database by new_level
 * @method static \DebugRefundDiscount retrieveByNewLevel(string $new_level) retrieve object from poll by new_level, get it from db if not exist in poll

 * @method void setStatusCalculated(integer $status_calculated) set status_calculated value
 * @method integer getStatusCalculated() get status_calculated value
 * @method static \DebugRefundDiscount[] findByStatusCalculated(integer $status_calculated) find objects in database by status_calculated
 * @method static \DebugRefundDiscount findOneByStatusCalculated(integer $status_calculated) find object in database by status_calculated
 * @method static \DebugRefundDiscount retrieveByStatusCalculated(integer $status_calculated) retrieve object from poll by status_calculated, get it from db if not exist in poll

 * @method void setServiceBuyingId(integer $service_buying_id) set service_buying_id value
 * @method integer getServiceBuyingId() get service_buying_id value
 * @method static \DebugRefundDiscount[] findByServiceBuyingId(integer $service_buying_id) find objects in database by service_buying_id
 * @method static \DebugRefundDiscount findOneByServiceBuyingId(integer $service_buying_id) find object in database by service_buying_id
 * @method static \DebugRefundDiscount retrieveByServiceBuyingId(integer $service_buying_id) retrieve object from poll by service_buying_id, get it from db if not exist in poll

 * @method void setServiceCheckingId(integer $service_checking_id) set service_checking_id value
 * @method integer getServiceCheckingId() get service_checking_id value
 * @method static \DebugRefundDiscount[] findByServiceCheckingId(integer $service_checking_id) find objects in database by service_checking_id
 * @method static \DebugRefundDiscount findOneByServiceCheckingId(integer $service_checking_id) find object in database by service_checking_id
 * @method static \DebugRefundDiscount retrieveByServiceCheckingId(integer $service_checking_id) retrieve object from poll by service_checking_id, get it from db if not exist in poll

 * @method void setServiceTransportId(integer $service_transport_id) set service_transport_id value
 * @method integer getServiceTransportId() get service_transport_id value
 * @method static \DebugRefundDiscount[] findByServiceTransportId(integer $service_transport_id) find objects in database by service_transport_id
 * @method static \DebugRefundDiscount findOneByServiceTransportId(integer $service_transport_id) find object in database by service_transport_id
 * @method static \DebugRefundDiscount retrieveByServiceTransportId(integer $service_transport_id) retrieve object from poll by service_transport_id, get it from db if not exist in poll


 */
abstract class DebugRefundDiscountBase extends ActiveRecord {
    protected static $_tableName = 'debug_refund_discount';
    protected static $_phpName = 'DebugRefundDiscount';
    protected static $_pk = 'id';
    protected static $_alias = 'd';
    protected static $_dbConnectName = 'debug_refund_discount';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'user_id' => array('name' => 'user_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_id' => array('name' => 'order_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'fee_buying' => array('name' => 'fee_buying',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'fee_checking' => array('name' => 'fee_checking',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'fee_transport' => array('name' => 'fee_transport',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'fee_fixed' => array('name' => 'fee_fixed',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'float'),
        'note' => array('name' => 'note',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'text'),
        'money_refund' => array('name' => 'money_refund',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'point' => array('name' => 'point',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'total_point' => array('name' => 'total_point',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double'),
        'old_level' => array('name' => 'old_level',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'new_level' => array('name' => 'new_level',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'status_calculated' => array('name' => 'status_calculated',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'service_buying_id' => array('name' => 'service_buying_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'service_checking_id' => array('name' => 'service_checking_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'service_transport_id' => array('name' => 'service_transport_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
     );
    protected static $_validate = array(
    );
    protected static $_validatorRules = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','user_id','order_id','fee_buying','fee_checking','fee_transport','fee_fixed','note','money_refund','point','total_point','old_level','new_level','status_calculated','service_buying_id','service_checking_id','service_transport_id');

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