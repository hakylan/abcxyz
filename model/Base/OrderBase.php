<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Order
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11)
 * @property string $code code type : char(100) max_length : 100
 * @property string $avatar avatar type : varchar(255) max_length : 255
 * @property string $status status type : varchar(50) max_length : 50
 * @property string $seller_name seller_name type : varchar(200) max_length : 200
 * @property string $seller_aliwang seller_aliwang type : varchar(100) max_length : 100
 * @property string $seller_homeland seller_homeland type : varchar(100) max_length : 100
 * @property string $seller_info seller_info type : text max_length : 
 * @property integer $buyer_id buyer_id type : int(11)
 * @property integer $order_quantity order_quantity type : int(11)
 * @property integer $pending_quantity pending_quantity type : int(11)
 * @property integer $recive_quantity recive_quantity type : int(11)
 * @property integer $packages_quantity packages_quantity type : int(11)
 * @property string $customer_confirm customer_confirm type : enum('CONFIRMED','WAIT','NONE') max_length : 9
 * @property string $note_customer_confirm note_customer_confirm type : varchar(255) max_length : 255
 * @property number $total_amount total_amount type : double(20,2)
 * @property number $order_amount order_amount type : double(20,2)
 * @property number $real_amount real_amount type : double(20,2)
 * @property number $bought_amount bought_amount type : double(20,2)
 * @property number $deposit_amount deposit_amount type : double(20,2)
 * @property number $deposit_ratio deposit_ratio type : decimal(2,1)
 * @property number $refund_amount refund_amount type : double(20,2)
 * @property number $real_payment_amount real_payment_amount type : double(20,2)
 * @property number $real_refund_amount real_refund_amount type : double(20,2)
 * @property number $real_surcharge real_surcharge type : double(20,2)
 * @property number $real_service_amount real_service_amount type : double(20,2)
 * @property number $service_fee service_fee type : double(20,2)
 * @property number $domestic_shipping_fee domestic_shipping_fee type : double(20,2)
 * @property number $domestic_shipping_fee_vnd domestic_shipping_fee_vnd type : double(20,2)
 * @property number $direct_fill_amount_cny direct_fill_amount_cny type : double(20,2)
 * @property number $direct_fill_amount_vnd direct_fill_amount_vnd type : double(20,2)
 * @property string $payment_link payment_link type : varchar(255) max_length : 255
 * @property integer $exchange exchange type : int(11)
 * @property number $weight weight type : decimal(6,2)
 * @property string $invoice invoice type : varchar(100) max_length : 100
 * @property string $alipay alipay type : varchar(200) max_length : 200
 * @property string $freight_bill freight_bill type : varchar(100) max_length : 100
 * @property integer $has_freight_bill has_freight_bill type : tinyint(1)
 * @property string $current_warehouse current_warehouse type : char(10) max_length : 10
 * @property string $next_warehouse next_warehouse type : char(10) max_length : 10
 * @property string $destination_warehouse destination_warehouse type : char(10) max_length : 10
 * @property string $warehouse_status warehouse_status type : varchar(50) max_length : 50
 * @property string $transport_status transport_status type : varchar(255) max_length : 255
 * @property string $checking_status checking_status type : char(20) max_length : 20
 * @property string $transport_vn_type transport_vn_type type : varchar(255) max_length : 255
 * @property string $warning_score warning_score type : varchar(100) max_length : 100
 * @property integer $user_address_id user_address_id type : int(11)
 * @property integer $tellers_id tellers_id type : int(11)
 * @property integer $paid_staff_id paid_staff_id type : int(11)
 * @property integer $delivery_staff_id delivery_staff_id type : int(11)
 * @property integer $checker_id checker_id type : int(11)
 * @property string $account_purchase_origin account_purchase_origin type : varchar(100) max_length : 100
 * @property string $name_recipient_origin name_recipient_origin type : varchar(30) max_length : 30
 * @property integer $complain_seller complain_seller type : tinyint(1)
 * @property integer $is_deleted is_deleted type : tinyint(2)
 * @property datetime $confirm_created_time confirm_created_time type : datetime
 * @property datetime $confirm_approval_time confirm_approval_time type : datetime
 * @property datetime $created_time created_time type : datetime
 * @property datetime $modified_time modified_time type : datetime
 * @property datetime $expire_time expire_time type : datetime
 * @property datetime $deposit_time deposit_time type : datetime
 * @property datetime $real_payment_last_time real_payment_last_time type : datetime
 * @property datetime $tellers_assigned_time tellers_assigned_time type : datetime
 * @property datetime $paid_staff_assigned_time paid_staff_assigned_time type : datetime
 * @property datetime $buying_time buying_time type : datetime
 * @property datetime $negotiating_time negotiating_time type : datetime
 * @property datetime $negotiated_time negotiated_time type : datetime
 * @property datetime $bought_time bought_time type : datetime
 * @property datetime $seller_delivered_time seller_delivered_time type : datetime
 * @property datetime $received_from_seller_time received_from_seller_time type : datetime
 * @property datetime $checking_time checking_time type : datetime
 * @property datetime $checked_time checked_time type : datetime
 * @property datetime $transporting_time transporting_time type : datetime
 * @property datetime $waiting_delivery_time waiting_delivery_time type : datetime
 * @property datetime $confirm_delivery_time confirm_delivery_time type : datetime
 * @property datetime $delivered_time delivered_time type : datetime
 * @property datetime $received_time received_time type : datetime
 * @property datetime $complaint_time complaint_time type : datetime
 * @property datetime $out_of_stock_time out_of_stock_time type : datetime
 * @property datetime $warehouse_in_time warehouse_in_time type : datetime
 * @property datetime $warehouse_out_time warehouse_out_time type : datetime
 * @property datetime $current_warehouse_time current_warehouse_time type : datetime
 * @property datetime $cancelled_time cancelled_time type : datetime

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Order[] findById(integer $id) find objects in database by id
 * @method static \Order findOneById(integer $id) find object in database by id
 * @method static \Order retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setCode(string $code) set code value
 * @method string getCode() get code value
 * @method static \Order[] findByCode(string $code) find objects in database by code
 * @method static \Order findOneByCode(string $code) find object in database by code
 * @method static \Order retrieveByCode(string $code) retrieve object from poll by code, get it from db if not exist in poll

 * @method void setAvatar(string $avatar) set avatar value
 * @method string getAvatar() get avatar value
 * @method static \Order[] findByAvatar(string $avatar) find objects in database by avatar
 * @method static \Order findOneByAvatar(string $avatar) find object in database by avatar
 * @method static \Order retrieveByAvatar(string $avatar) retrieve object from poll by avatar, get it from db if not exist in poll

 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Order[] findByStatus(string $status) find objects in database by status
 * @method static \Order findOneByStatus(string $status) find object in database by status
 * @method static \Order retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll

 * @method void setSellerName(string $seller_name) set seller_name value
 * @method string getSellerName() get seller_name value
 * @method static \Order[] findBySellerName(string $seller_name) find objects in database by seller_name
 * @method static \Order findOneBySellerName(string $seller_name) find object in database by seller_name
 * @method static \Order retrieveBySellerName(string $seller_name) retrieve object from poll by seller_name, get it from db if not exist in poll

 * @method void setSellerAliwang(string $seller_aliwang) set seller_aliwang value
 * @method string getSellerAliwang() get seller_aliwang value
 * @method static \Order[] findBySellerAliwang(string $seller_aliwang) find objects in database by seller_aliwang
 * @method static \Order findOneBySellerAliwang(string $seller_aliwang) find object in database by seller_aliwang
 * @method static \Order retrieveBySellerAliwang(string $seller_aliwang) retrieve object from poll by seller_aliwang, get it from db if not exist in poll

 * @method void setSellerHomeland(string $seller_homeland) set seller_homeland value
 * @method string getSellerHomeland() get seller_homeland value
 * @method static \Order[] findBySellerHomeland(string $seller_homeland) find objects in database by seller_homeland
 * @method static \Order findOneBySellerHomeland(string $seller_homeland) find object in database by seller_homeland
 * @method static \Order retrieveBySellerHomeland(string $seller_homeland) retrieve object from poll by seller_homeland, get it from db if not exist in poll

 * @method void setSellerInfo(string $seller_info) set seller_info value
 * @method string getSellerInfo() get seller_info value
 * @method static \Order[] findBySellerInfo(string $seller_info) find objects in database by seller_info
 * @method static \Order findOneBySellerInfo(string $seller_info) find object in database by seller_info
 * @method static \Order retrieveBySellerInfo(string $seller_info) retrieve object from poll by seller_info, get it from db if not exist in poll

 * @method void setBuyerId(integer $buyer_id) set buyer_id value
 * @method integer getBuyerId() get buyer_id value
 * @method static \Order[] findByBuyerId(integer $buyer_id) find objects in database by buyer_id
 * @method static \Order findOneByBuyerId(integer $buyer_id) find object in database by buyer_id
 * @method static \Order retrieveByBuyerId(integer $buyer_id) retrieve object from poll by buyer_id, get it from db if not exist in poll

 * @method void setOrderQuantity(integer $order_quantity) set order_quantity value
 * @method integer getOrderQuantity() get order_quantity value
 * @method static \Order[] findByOrderQuantity(integer $order_quantity) find objects in database by order_quantity
 * @method static \Order findOneByOrderQuantity(integer $order_quantity) find object in database by order_quantity
 * @method static \Order retrieveByOrderQuantity(integer $order_quantity) retrieve object from poll by order_quantity, get it from db if not exist in poll

 * @method void setPendingQuantity(integer $pending_quantity) set pending_quantity value
 * @method integer getPendingQuantity() get pending_quantity value
 * @method static \Order[] findByPendingQuantity(integer $pending_quantity) find objects in database by pending_quantity
 * @method static \Order findOneByPendingQuantity(integer $pending_quantity) find object in database by pending_quantity
 * @method static \Order retrieveByPendingQuantity(integer $pending_quantity) retrieve object from poll by pending_quantity, get it from db if not exist in poll

 * @method void setReciveQuantity(integer $recive_quantity) set recive_quantity value
 * @method integer getReciveQuantity() get recive_quantity value
 * @method static \Order[] findByReciveQuantity(integer $recive_quantity) find objects in database by recive_quantity
 * @method static \Order findOneByReciveQuantity(integer $recive_quantity) find object in database by recive_quantity
 * @method static \Order retrieveByReciveQuantity(integer $recive_quantity) retrieve object from poll by recive_quantity, get it from db if not exist in poll

 * @method void setPackagesQuantity(integer $packages_quantity) set packages_quantity value
 * @method integer getPackagesQuantity() get packages_quantity value
 * @method static \Order[] findByPackagesQuantity(integer $packages_quantity) find objects in database by packages_quantity
 * @method static \Order findOneByPackagesQuantity(integer $packages_quantity) find object in database by packages_quantity
 * @method static \Order retrieveByPackagesQuantity(integer $packages_quantity) retrieve object from poll by packages_quantity, get it from db if not exist in poll

 * @method void setCustomerConfirm(string $customer_confirm) set customer_confirm value
 * @method string getCustomerConfirm() get customer_confirm value
 * @method static \Order[] findByCustomerConfirm(string $customer_confirm) find objects in database by customer_confirm
 * @method static \Order findOneByCustomerConfirm(string $customer_confirm) find object in database by customer_confirm
 * @method static \Order retrieveByCustomerConfirm(string $customer_confirm) retrieve object from poll by customer_confirm, get it from db if not exist in poll

 * @method void setNoteCustomerConfirm(string $note_customer_confirm) set note_customer_confirm value
 * @method string getNoteCustomerConfirm() get note_customer_confirm value
 * @method static \Order[] findByNoteCustomerConfirm(string $note_customer_confirm) find objects in database by note_customer_confirm
 * @method static \Order findOneByNoteCustomerConfirm(string $note_customer_confirm) find object in database by note_customer_confirm
 * @method static \Order retrieveByNoteCustomerConfirm(string $note_customer_confirm) retrieve object from poll by note_customer_confirm, get it from db if not exist in poll

 * @method void setTotalAmount(number $total_amount) set total_amount value
 * @method number getTotalAmount() get total_amount value
 * @method static \Order[] findByTotalAmount(number $total_amount) find objects in database by total_amount
 * @method static \Order findOneByTotalAmount(number $total_amount) find object in database by total_amount
 * @method static \Order retrieveByTotalAmount(number $total_amount) retrieve object from poll by total_amount, get it from db if not exist in poll

 * @method void setOrderAmount(number $order_amount) set order_amount value
 * @method number getOrderAmount() get order_amount value
 * @method static \Order[] findByOrderAmount(number $order_amount) find objects in database by order_amount
 * @method static \Order findOneByOrderAmount(number $order_amount) find object in database by order_amount
 * @method static \Order retrieveByOrderAmount(number $order_amount) retrieve object from poll by order_amount, get it from db if not exist in poll

 * @method void setRealAmount(number $real_amount) set real_amount value
 * @method number getRealAmount() get real_amount value
 * @method static \Order[] findByRealAmount(number $real_amount) find objects in database by real_amount
 * @method static \Order findOneByRealAmount(number $real_amount) find object in database by real_amount
 * @method static \Order retrieveByRealAmount(number $real_amount) retrieve object from poll by real_amount, get it from db if not exist in poll

 * @method void setBoughtAmount(number $bought_amount) set bought_amount value
 * @method number getBoughtAmount() get bought_amount value
 * @method static \Order[] findByBoughtAmount(number $bought_amount) find objects in database by bought_amount
 * @method static \Order findOneByBoughtAmount(number $bought_amount) find object in database by bought_amount
 * @method static \Order retrieveByBoughtAmount(number $bought_amount) retrieve object from poll by bought_amount, get it from db if not exist in poll

 * @method void setDepositAmount(number $deposit_amount) set deposit_amount value
 * @method number getDepositAmount() get deposit_amount value
 * @method static \Order[] findByDepositAmount(number $deposit_amount) find objects in database by deposit_amount
 * @method static \Order findOneByDepositAmount(number $deposit_amount) find object in database by deposit_amount
 * @method static \Order retrieveByDepositAmount(number $deposit_amount) retrieve object from poll by deposit_amount, get it from db if not exist in poll

 * @method void setDepositRatio(number $deposit_ratio) set deposit_ratio value
 * @method number getDepositRatio() get deposit_ratio value
 * @method static \Order[] findByDepositRatio(number $deposit_ratio) find objects in database by deposit_ratio
 * @method static \Order findOneByDepositRatio(number $deposit_ratio) find object in database by deposit_ratio
 * @method static \Order retrieveByDepositRatio(number $deposit_ratio) retrieve object from poll by deposit_ratio, get it from db if not exist in poll

 * @method void setRefundAmount(number $refund_amount) set refund_amount value
 * @method number getRefundAmount() get refund_amount value
 * @method static \Order[] findByRefundAmount(number $refund_amount) find objects in database by refund_amount
 * @method static \Order findOneByRefundAmount(number $refund_amount) find object in database by refund_amount
 * @method static \Order retrieveByRefundAmount(number $refund_amount) retrieve object from poll by refund_amount, get it from db if not exist in poll

 * @method void setRealPaymentAmount(number $real_payment_amount) set real_payment_amount value
 * @method number getRealPaymentAmount() get real_payment_amount value
 * @method static \Order[] findByRealPaymentAmount(number $real_payment_amount) find objects in database by real_payment_amount
 * @method static \Order findOneByRealPaymentAmount(number $real_payment_amount) find object in database by real_payment_amount
 * @method static \Order retrieveByRealPaymentAmount(number $real_payment_amount) retrieve object from poll by real_payment_amount, get it from db if not exist in poll

 * @method void setRealRefundAmount(number $real_refund_amount) set real_refund_amount value
 * @method number getRealRefundAmount() get real_refund_amount value
 * @method static \Order[] findByRealRefundAmount(number $real_refund_amount) find objects in database by real_refund_amount
 * @method static \Order findOneByRealRefundAmount(number $real_refund_amount) find object in database by real_refund_amount
 * @method static \Order retrieveByRealRefundAmount(number $real_refund_amount) retrieve object from poll by real_refund_amount, get it from db if not exist in poll

 * @method void setRealSurcharge(number $real_surcharge) set real_surcharge value
 * @method number getRealSurcharge() get real_surcharge value
 * @method static \Order[] findByRealSurcharge(number $real_surcharge) find objects in database by real_surcharge
 * @method static \Order findOneByRealSurcharge(number $real_surcharge) find object in database by real_surcharge
 * @method static \Order retrieveByRealSurcharge(number $real_surcharge) retrieve object from poll by real_surcharge, get it from db if not exist in poll

 * @method void setRealServiceAmount(number $real_service_amount) set real_service_amount value
 * @method number getRealServiceAmount() get real_service_amount value
 * @method static \Order[] findByRealServiceAmount(number $real_service_amount) find objects in database by real_service_amount
 * @method static \Order findOneByRealServiceAmount(number $real_service_amount) find object in database by real_service_amount
 * @method static \Order retrieveByRealServiceAmount(number $real_service_amount) retrieve object from poll by real_service_amount, get it from db if not exist in poll

 * @method void setServiceFee(number $service_fee) set service_fee value
 * @method number getServiceFee() get service_fee value
 * @method static \Order[] findByServiceFee(number $service_fee) find objects in database by service_fee
 * @method static \Order findOneByServiceFee(number $service_fee) find object in database by service_fee
 * @method static \Order retrieveByServiceFee(number $service_fee) retrieve object from poll by service_fee, get it from db if not exist in poll

 * @method void setDomesticShippingFee(number $domestic_shipping_fee) set domestic_shipping_fee value
 * @method number getDomesticShippingFee() get domestic_shipping_fee value
 * @method static \Order[] findByDomesticShippingFee(number $domestic_shipping_fee) find objects in database by domestic_shipping_fee
 * @method static \Order findOneByDomesticShippingFee(number $domestic_shipping_fee) find object in database by domestic_shipping_fee
 * @method static \Order retrieveByDomesticShippingFee(number $domestic_shipping_fee) retrieve object from poll by domestic_shipping_fee, get it from db if not exist in poll

 * @method void setDomesticShippingFeeVnd(number $domestic_shipping_fee_vnd) set domestic_shipping_fee_vnd value
 * @method number getDomesticShippingFeeVnd() get domestic_shipping_fee_vnd value
 * @method static \Order[] findByDomesticShippingFeeVnd(number $domestic_shipping_fee_vnd) find objects in database by domestic_shipping_fee_vnd
 * @method static \Order findOneByDomesticShippingFeeVnd(number $domestic_shipping_fee_vnd) find object in database by domestic_shipping_fee_vnd
 * @method static \Order retrieveByDomesticShippingFeeVnd(number $domestic_shipping_fee_vnd) retrieve object from poll by domestic_shipping_fee_vnd, get it from db if not exist in poll

 * @method void setDirectFillAmountCny(number $direct_fill_amount_cny) set direct_fill_amount_cny value
 * @method number getDirectFillAmountCny() get direct_fill_amount_cny value
 * @method static \Order[] findByDirectFillAmountCny(number $direct_fill_amount_cny) find objects in database by direct_fill_amount_cny
 * @method static \Order findOneByDirectFillAmountCny(number $direct_fill_amount_cny) find object in database by direct_fill_amount_cny
 * @method static \Order retrieveByDirectFillAmountCny(number $direct_fill_amount_cny) retrieve object from poll by direct_fill_amount_cny, get it from db if not exist in poll

 * @method void setDirectFillAmountVnd(number $direct_fill_amount_vnd) set direct_fill_amount_vnd value
 * @method number getDirectFillAmountVnd() get direct_fill_amount_vnd value
 * @method static \Order[] findByDirectFillAmountVnd(number $direct_fill_amount_vnd) find objects in database by direct_fill_amount_vnd
 * @method static \Order findOneByDirectFillAmountVnd(number $direct_fill_amount_vnd) find object in database by direct_fill_amount_vnd
 * @method static \Order retrieveByDirectFillAmountVnd(number $direct_fill_amount_vnd) retrieve object from poll by direct_fill_amount_vnd, get it from db if not exist in poll

 * @method void setPaymentLink(string $payment_link) set payment_link value
 * @method string getPaymentLink() get payment_link value
 * @method static \Order[] findByPaymentLink(string $payment_link) find objects in database by payment_link
 * @method static \Order findOneByPaymentLink(string $payment_link) find object in database by payment_link
 * @method static \Order retrieveByPaymentLink(string $payment_link) retrieve object from poll by payment_link, get it from db if not exist in poll

 * @method void setExchange(integer $exchange) set exchange value
 * @method integer getExchange() get exchange value
 * @method static \Order[] findByExchange(integer $exchange) find objects in database by exchange
 * @method static \Order findOneByExchange(integer $exchange) find object in database by exchange
 * @method static \Order retrieveByExchange(integer $exchange) retrieve object from poll by exchange, get it from db if not exist in poll

 * @method void setWeight(number $weight) set weight value
 * @method number getWeight() get weight value
 * @method static \Order[] findByWeight(number $weight) find objects in database by weight
 * @method static \Order findOneByWeight(number $weight) find object in database by weight
 * @method static \Order retrieveByWeight(number $weight) retrieve object from poll by weight, get it from db if not exist in poll

 * @method void setInvoice(string $invoice) set invoice value
 * @method string getInvoice() get invoice value
 * @method static \Order[] findByInvoice(string $invoice) find objects in database by invoice
 * @method static \Order findOneByInvoice(string $invoice) find object in database by invoice
 * @method static \Order retrieveByInvoice(string $invoice) retrieve object from poll by invoice, get it from db if not exist in poll

 * @method void setAlipay(string $alipay) set alipay value
 * @method string getAlipay() get alipay value
 * @method static \Order[] findByAlipay(string $alipay) find objects in database by alipay
 * @method static \Order findOneByAlipay(string $alipay) find object in database by alipay
 * @method static \Order retrieveByAlipay(string $alipay) retrieve object from poll by alipay, get it from db if not exist in poll

 * @method void setFreightBill(string $freight_bill) set freight_bill value
 * @method string getFreightBill() get freight_bill value
 * @method static \Order[] findByFreightBill(string $freight_bill) find objects in database by freight_bill
 * @method static \Order findOneByFreightBill(string $freight_bill) find object in database by freight_bill
 * @method static \Order retrieveByFreightBill(string $freight_bill) retrieve object from poll by freight_bill, get it from db if not exist in poll

 * @method void setHasFreightBill(integer $has_freight_bill) set has_freight_bill value
 * @method integer getHasFreightBill() get has_freight_bill value
 * @method static \Order[] findByHasFreightBill(integer $has_freight_bill) find objects in database by has_freight_bill
 * @method static \Order findOneByHasFreightBill(integer $has_freight_bill) find object in database by has_freight_bill
 * @method static \Order retrieveByHasFreightBill(integer $has_freight_bill) retrieve object from poll by has_freight_bill, get it from db if not exist in poll

 * @method void setCurrentWarehouse(string $current_warehouse) set current_warehouse value
 * @method string getCurrentWarehouse() get current_warehouse value
 * @method static \Order[] findByCurrentWarehouse(string $current_warehouse) find objects in database by current_warehouse
 * @method static \Order findOneByCurrentWarehouse(string $current_warehouse) find object in database by current_warehouse
 * @method static \Order retrieveByCurrentWarehouse(string $current_warehouse) retrieve object from poll by current_warehouse, get it from db if not exist in poll

 * @method void setNextWarehouse(string $next_warehouse) set next_warehouse value
 * @method string getNextWarehouse() get next_warehouse value
 * @method static \Order[] findByNextWarehouse(string $next_warehouse) find objects in database by next_warehouse
 * @method static \Order findOneByNextWarehouse(string $next_warehouse) find object in database by next_warehouse
 * @method static \Order retrieveByNextWarehouse(string $next_warehouse) retrieve object from poll by next_warehouse, get it from db if not exist in poll

 * @method void setDestinationWarehouse(string $destination_warehouse) set destination_warehouse value
 * @method string getDestinationWarehouse() get destination_warehouse value
 * @method static \Order[] findByDestinationWarehouse(string $destination_warehouse) find objects in database by destination_warehouse
 * @method static \Order findOneByDestinationWarehouse(string $destination_warehouse) find object in database by destination_warehouse
 * @method static \Order retrieveByDestinationWarehouse(string $destination_warehouse) retrieve object from poll by destination_warehouse, get it from db if not exist in poll

 * @method void setWarehouseStatus(string $warehouse_status) set warehouse_status value
 * @method string getWarehouseStatus() get warehouse_status value
 * @method static \Order[] findByWarehouseStatus(string $warehouse_status) find objects in database by warehouse_status
 * @method static \Order findOneByWarehouseStatus(string $warehouse_status) find object in database by warehouse_status
 * @method static \Order retrieveByWarehouseStatus(string $warehouse_status) retrieve object from poll by warehouse_status, get it from db if not exist in poll

 * @method void setTransportStatus(string $transport_status) set transport_status value
 * @method string getTransportStatus() get transport_status value
 * @method static \Order[] findByTransportStatus(string $transport_status) find objects in database by transport_status
 * @method static \Order findOneByTransportStatus(string $transport_status) find object in database by transport_status
 * @method static \Order retrieveByTransportStatus(string $transport_status) retrieve object from poll by transport_status, get it from db if not exist in poll

 * @method void setCheckingStatus(string $checking_status) set checking_status value
 * @method string getCheckingStatus() get checking_status value
 * @method static \Order[] findByCheckingStatus(string $checking_status) find objects in database by checking_status
 * @method static \Order findOneByCheckingStatus(string $checking_status) find object in database by checking_status
 * @method static \Order retrieveByCheckingStatus(string $checking_status) retrieve object from poll by checking_status, get it from db if not exist in poll

 * @method void setTransportVnType(string $transport_vn_type) set transport_vn_type value
 * @method string getTransportVnType() get transport_vn_type value
 * @method static \Order[] findByTransportVnType(string $transport_vn_type) find objects in database by transport_vn_type
 * @method static \Order findOneByTransportVnType(string $transport_vn_type) find object in database by transport_vn_type
 * @method static \Order retrieveByTransportVnType(string $transport_vn_type) retrieve object from poll by transport_vn_type, get it from db if not exist in poll

 * @method void setWarningScore(string $warning_score) set warning_score value
 * @method string getWarningScore() get warning_score value
 * @method static \Order[] findByWarningScore(string $warning_score) find objects in database by warning_score
 * @method static \Order findOneByWarningScore(string $warning_score) find object in database by warning_score
 * @method static \Order retrieveByWarningScore(string $warning_score) retrieve object from poll by warning_score, get it from db if not exist in poll

 * @method void setUserAddressId(integer $user_address_id) set user_address_id value
 * @method integer getUserAddressId() get user_address_id value
 * @method static \Order[] findByUserAddressId(integer $user_address_id) find objects in database by user_address_id
 * @method static \Order findOneByUserAddressId(integer $user_address_id) find object in database by user_address_id
 * @method static \Order retrieveByUserAddressId(integer $user_address_id) retrieve object from poll by user_address_id, get it from db if not exist in poll

 * @method void setTellersId(integer $tellers_id) set tellers_id value
 * @method integer getTellersId() get tellers_id value
 * @method static \Order[] findByTellersId(integer $tellers_id) find objects in database by tellers_id
 * @method static \Order findOneByTellersId(integer $tellers_id) find object in database by tellers_id
 * @method static \Order retrieveByTellersId(integer $tellers_id) retrieve object from poll by tellers_id, get it from db if not exist in poll

 * @method void setPaidStaffId(integer $paid_staff_id) set paid_staff_id value
 * @method integer getPaidStaffId() get paid_staff_id value
 * @method static \Order[] findByPaidStaffId(integer $paid_staff_id) find objects in database by paid_staff_id
 * @method static \Order findOneByPaidStaffId(integer $paid_staff_id) find object in database by paid_staff_id
 * @method static \Order retrieveByPaidStaffId(integer $paid_staff_id) retrieve object from poll by paid_staff_id, get it from db if not exist in poll

 * @method void setDeliveryStaffId(integer $delivery_staff_id) set delivery_staff_id value
 * @method integer getDeliveryStaffId() get delivery_staff_id value
 * @method static \Order[] findByDeliveryStaffId(integer $delivery_staff_id) find objects in database by delivery_staff_id
 * @method static \Order findOneByDeliveryStaffId(integer $delivery_staff_id) find object in database by delivery_staff_id
 * @method static \Order retrieveByDeliveryStaffId(integer $delivery_staff_id) retrieve object from poll by delivery_staff_id, get it from db if not exist in poll

 * @method void setCheckerId(integer $checker_id) set checker_id value
 * @method integer getCheckerId() get checker_id value
 * @method static \Order[] findByCheckerId(integer $checker_id) find objects in database by checker_id
 * @method static \Order findOneByCheckerId(integer $checker_id) find object in database by checker_id
 * @method static \Order retrieveByCheckerId(integer $checker_id) retrieve object from poll by checker_id, get it from db if not exist in poll

 * @method void setAccountPurchaseOrigin(string $account_purchase_origin) set account_purchase_origin value
 * @method string getAccountPurchaseOrigin() get account_purchase_origin value
 * @method static \Order[] findByAccountPurchaseOrigin(string $account_purchase_origin) find objects in database by account_purchase_origin
 * @method static \Order findOneByAccountPurchaseOrigin(string $account_purchase_origin) find object in database by account_purchase_origin
 * @method static \Order retrieveByAccountPurchaseOrigin(string $account_purchase_origin) retrieve object from poll by account_purchase_origin, get it from db if not exist in poll

 * @method void setNameRecipientOrigin(string $name_recipient_origin) set name_recipient_origin value
 * @method string getNameRecipientOrigin() get name_recipient_origin value
 * @method static \Order[] findByNameRecipientOrigin(string $name_recipient_origin) find objects in database by name_recipient_origin
 * @method static \Order findOneByNameRecipientOrigin(string $name_recipient_origin) find object in database by name_recipient_origin
 * @method static \Order retrieveByNameRecipientOrigin(string $name_recipient_origin) retrieve object from poll by name_recipient_origin, get it from db if not exist in poll

 * @method void setComplainSeller(integer $complain_seller) set complain_seller value
 * @method integer getComplainSeller() get complain_seller value
 * @method static \Order[] findByComplainSeller(integer $complain_seller) find objects in database by complain_seller
 * @method static \Order findOneByComplainSeller(integer $complain_seller) find object in database by complain_seller
 * @method static \Order retrieveByComplainSeller(integer $complain_seller) retrieve object from poll by complain_seller, get it from db if not exist in poll

 * @method void setIsDeleted(integer $is_deleted) set is_deleted value
 * @method integer getIsDeleted() get is_deleted value
 * @method static \Order[] findByIsDeleted(integer $is_deleted) find objects in database by is_deleted
 * @method static \Order findOneByIsDeleted(integer $is_deleted) find object in database by is_deleted
 * @method static \Order retrieveByIsDeleted(integer $is_deleted) retrieve object from poll by is_deleted, get it from db if not exist in poll

 * @method void setConfirmCreatedTime(\Flywheel\Db\Type\DateTime $confirm_created_time) setConfirmCreatedTime(string $confirm_created_time) set confirm_created_time value
 * @method \Flywheel\Db\Type\DateTime getConfirmCreatedTime() get confirm_created_time value
 * @method static \Order[] findByConfirmCreatedTime(\Flywheel\Db\Type\DateTime $confirm_created_time) findByConfirmCreatedTime(string $confirm_created_time) find objects in database by confirm_created_time
 * @method static \Order findOneByConfirmCreatedTime(\Flywheel\Db\Type\DateTime $confirm_created_time) findOneByConfirmCreatedTime(string $confirm_created_time) find object in database by confirm_created_time
 * @method static \Order retrieveByConfirmCreatedTime(\Flywheel\Db\Type\DateTime $confirm_created_time) retrieveByConfirmCreatedTime(string $confirm_created_time) retrieve object from poll by confirm_created_time, get it from db if not exist in poll

 * @method void setConfirmApprovalTime(\Flywheel\Db\Type\DateTime $confirm_approval_time) setConfirmApprovalTime(string $confirm_approval_time) set confirm_approval_time value
 * @method \Flywheel\Db\Type\DateTime getConfirmApprovalTime() get confirm_approval_time value
 * @method static \Order[] findByConfirmApprovalTime(\Flywheel\Db\Type\DateTime $confirm_approval_time) findByConfirmApprovalTime(string $confirm_approval_time) find objects in database by confirm_approval_time
 * @method static \Order findOneByConfirmApprovalTime(\Flywheel\Db\Type\DateTime $confirm_approval_time) findOneByConfirmApprovalTime(string $confirm_approval_time) find object in database by confirm_approval_time
 * @method static \Order retrieveByConfirmApprovalTime(\Flywheel\Db\Type\DateTime $confirm_approval_time) retrieveByConfirmApprovalTime(string $confirm_approval_time) retrieve object from poll by confirm_approval_time, get it from db if not exist in poll

 * @method void setCreatedTime(\Flywheel\Db\Type\DateTime $created_time) setCreatedTime(string $created_time) set created_time value
 * @method \Flywheel\Db\Type\DateTime getCreatedTime() get created_time value
 * @method static \Order[] findByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findByCreatedTime(string $created_time) find objects in database by created_time
 * @method static \Order findOneByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) findOneByCreatedTime(string $created_time) find object in database by created_time
 * @method static \Order retrieveByCreatedTime(\Flywheel\Db\Type\DateTime $created_time) retrieveByCreatedTime(string $created_time) retrieve object from poll by created_time, get it from db if not exist in poll

 * @method void setModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) setModifiedTime(string $modified_time) set modified_time value
 * @method \Flywheel\Db\Type\DateTime getModifiedTime() get modified_time value
 * @method static \Order[] findByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findByModifiedTime(string $modified_time) find objects in database by modified_time
 * @method static \Order findOneByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) findOneByModifiedTime(string $modified_time) find object in database by modified_time
 * @method static \Order retrieveByModifiedTime(\Flywheel\Db\Type\DateTime $modified_time) retrieveByModifiedTime(string $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll

 * @method void setExpireTime(\Flywheel\Db\Type\DateTime $expire_time) setExpireTime(string $expire_time) set expire_time value
 * @method \Flywheel\Db\Type\DateTime getExpireTime() get expire_time value
 * @method static \Order[] findByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) findByExpireTime(string $expire_time) find objects in database by expire_time
 * @method static \Order findOneByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) findOneByExpireTime(string $expire_time) find object in database by expire_time
 * @method static \Order retrieveByExpireTime(\Flywheel\Db\Type\DateTime $expire_time) retrieveByExpireTime(string $expire_time) retrieve object from poll by expire_time, get it from db if not exist in poll

 * @method void setDepositTime(\Flywheel\Db\Type\DateTime $deposit_time) setDepositTime(string $deposit_time) set deposit_time value
 * @method \Flywheel\Db\Type\DateTime getDepositTime() get deposit_time value
 * @method static \Order[] findByDepositTime(\Flywheel\Db\Type\DateTime $deposit_time) findByDepositTime(string $deposit_time) find objects in database by deposit_time
 * @method static \Order findOneByDepositTime(\Flywheel\Db\Type\DateTime $deposit_time) findOneByDepositTime(string $deposit_time) find object in database by deposit_time
 * @method static \Order retrieveByDepositTime(\Flywheel\Db\Type\DateTime $deposit_time) retrieveByDepositTime(string $deposit_time) retrieve object from poll by deposit_time, get it from db if not exist in poll

 * @method void setRealPaymentLastTime(\Flywheel\Db\Type\DateTime $real_payment_last_time) setRealPaymentLastTime(string $real_payment_last_time) set real_payment_last_time value
 * @method \Flywheel\Db\Type\DateTime getRealPaymentLastTime() get real_payment_last_time value
 * @method static \Order[] findByRealPaymentLastTime(\Flywheel\Db\Type\DateTime $real_payment_last_time) findByRealPaymentLastTime(string $real_payment_last_time) find objects in database by real_payment_last_time
 * @method static \Order findOneByRealPaymentLastTime(\Flywheel\Db\Type\DateTime $real_payment_last_time) findOneByRealPaymentLastTime(string $real_payment_last_time) find object in database by real_payment_last_time
 * @method static \Order retrieveByRealPaymentLastTime(\Flywheel\Db\Type\DateTime $real_payment_last_time) retrieveByRealPaymentLastTime(string $real_payment_last_time) retrieve object from poll by real_payment_last_time, get it from db if not exist in poll

 * @method void setTellersAssignedTime(\Flywheel\Db\Type\DateTime $tellers_assigned_time) setTellersAssignedTime(string $tellers_assigned_time) set tellers_assigned_time value
 * @method \Flywheel\Db\Type\DateTime getTellersAssignedTime() get tellers_assigned_time value
 * @method static \Order[] findByTellersAssignedTime(\Flywheel\Db\Type\DateTime $tellers_assigned_time) findByTellersAssignedTime(string $tellers_assigned_time) find objects in database by tellers_assigned_time
 * @method static \Order findOneByTellersAssignedTime(\Flywheel\Db\Type\DateTime $tellers_assigned_time) findOneByTellersAssignedTime(string $tellers_assigned_time) find object in database by tellers_assigned_time
 * @method static \Order retrieveByTellersAssignedTime(\Flywheel\Db\Type\DateTime $tellers_assigned_time) retrieveByTellersAssignedTime(string $tellers_assigned_time) retrieve object from poll by tellers_assigned_time, get it from db if not exist in poll

 * @method void setPaidStaffAssignedTime(\Flywheel\Db\Type\DateTime $paid_staff_assigned_time) setPaidStaffAssignedTime(string $paid_staff_assigned_time) set paid_staff_assigned_time value
 * @method \Flywheel\Db\Type\DateTime getPaidStaffAssignedTime() get paid_staff_assigned_time value
 * @method static \Order[] findByPaidStaffAssignedTime(\Flywheel\Db\Type\DateTime $paid_staff_assigned_time) findByPaidStaffAssignedTime(string $paid_staff_assigned_time) find objects in database by paid_staff_assigned_time
 * @method static \Order findOneByPaidStaffAssignedTime(\Flywheel\Db\Type\DateTime $paid_staff_assigned_time) findOneByPaidStaffAssignedTime(string $paid_staff_assigned_time) find object in database by paid_staff_assigned_time
 * @method static \Order retrieveByPaidStaffAssignedTime(\Flywheel\Db\Type\DateTime $paid_staff_assigned_time) retrieveByPaidStaffAssignedTime(string $paid_staff_assigned_time) retrieve object from poll by paid_staff_assigned_time, get it from db if not exist in poll

 * @method void setBuyingTime(\Flywheel\Db\Type\DateTime $buying_time) setBuyingTime(string $buying_time) set buying_time value
 * @method \Flywheel\Db\Type\DateTime getBuyingTime() get buying_time value
 * @method static \Order[] findByBuyingTime(\Flywheel\Db\Type\DateTime $buying_time) findByBuyingTime(string $buying_time) find objects in database by buying_time
 * @method static \Order findOneByBuyingTime(\Flywheel\Db\Type\DateTime $buying_time) findOneByBuyingTime(string $buying_time) find object in database by buying_time
 * @method static \Order retrieveByBuyingTime(\Flywheel\Db\Type\DateTime $buying_time) retrieveByBuyingTime(string $buying_time) retrieve object from poll by buying_time, get it from db if not exist in poll

 * @method void setNegotiatingTime(\Flywheel\Db\Type\DateTime $negotiating_time) setNegotiatingTime(string $negotiating_time) set negotiating_time value
 * @method \Flywheel\Db\Type\DateTime getNegotiatingTime() get negotiating_time value
 * @method static \Order[] findByNegotiatingTime(\Flywheel\Db\Type\DateTime $negotiating_time) findByNegotiatingTime(string $negotiating_time) find objects in database by negotiating_time
 * @method static \Order findOneByNegotiatingTime(\Flywheel\Db\Type\DateTime $negotiating_time) findOneByNegotiatingTime(string $negotiating_time) find object in database by negotiating_time
 * @method static \Order retrieveByNegotiatingTime(\Flywheel\Db\Type\DateTime $negotiating_time) retrieveByNegotiatingTime(string $negotiating_time) retrieve object from poll by negotiating_time, get it from db if not exist in poll

 * @method void setNegotiatedTime(\Flywheel\Db\Type\DateTime $negotiated_time) setNegotiatedTime(string $negotiated_time) set negotiated_time value
 * @method \Flywheel\Db\Type\DateTime getNegotiatedTime() get negotiated_time value
 * @method static \Order[] findByNegotiatedTime(\Flywheel\Db\Type\DateTime $negotiated_time) findByNegotiatedTime(string $negotiated_time) find objects in database by negotiated_time
 * @method static \Order findOneByNegotiatedTime(\Flywheel\Db\Type\DateTime $negotiated_time) findOneByNegotiatedTime(string $negotiated_time) find object in database by negotiated_time
 * @method static \Order retrieveByNegotiatedTime(\Flywheel\Db\Type\DateTime $negotiated_time) retrieveByNegotiatedTime(string $negotiated_time) retrieve object from poll by negotiated_time, get it from db if not exist in poll

 * @method void setBoughtTime(\Flywheel\Db\Type\DateTime $bought_time) setBoughtTime(string $bought_time) set bought_time value
 * @method \Flywheel\Db\Type\DateTime getBoughtTime() get bought_time value
 * @method static \Order[] findByBoughtTime(\Flywheel\Db\Type\DateTime $bought_time) findByBoughtTime(string $bought_time) find objects in database by bought_time
 * @method static \Order findOneByBoughtTime(\Flywheel\Db\Type\DateTime $bought_time) findOneByBoughtTime(string $bought_time) find object in database by bought_time
 * @method static \Order retrieveByBoughtTime(\Flywheel\Db\Type\DateTime $bought_time) retrieveByBoughtTime(string $bought_time) retrieve object from poll by bought_time, get it from db if not exist in poll

 * @method void setSellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) setSellerDeliveredTime(string $seller_delivered_time) set seller_delivered_time value
 * @method \Flywheel\Db\Type\DateTime getSellerDeliveredTime() get seller_delivered_time value
 * @method static \Order[] findBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) findBySellerDeliveredTime(string $seller_delivered_time) find objects in database by seller_delivered_time
 * @method static \Order findOneBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) findOneBySellerDeliveredTime(string $seller_delivered_time) find object in database by seller_delivered_time
 * @method static \Order retrieveBySellerDeliveredTime(\Flywheel\Db\Type\DateTime $seller_delivered_time) retrieveBySellerDeliveredTime(string $seller_delivered_time) retrieve object from poll by seller_delivered_time, get it from db if not exist in poll

 * @method void setReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) setReceivedFromSellerTime(string $received_from_seller_time) set received_from_seller_time value
 * @method \Flywheel\Db\Type\DateTime getReceivedFromSellerTime() get received_from_seller_time value
 * @method static \Order[] findByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) findByReceivedFromSellerTime(string $received_from_seller_time) find objects in database by received_from_seller_time
 * @method static \Order findOneByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) findOneByReceivedFromSellerTime(string $received_from_seller_time) find object in database by received_from_seller_time
 * @method static \Order retrieveByReceivedFromSellerTime(\Flywheel\Db\Type\DateTime $received_from_seller_time) retrieveByReceivedFromSellerTime(string $received_from_seller_time) retrieve object from poll by received_from_seller_time, get it from db if not exist in poll

 * @method void setCheckingTime(\Flywheel\Db\Type\DateTime $checking_time) setCheckingTime(string $checking_time) set checking_time value
 * @method \Flywheel\Db\Type\DateTime getCheckingTime() get checking_time value
 * @method static \Order[] findByCheckingTime(\Flywheel\Db\Type\DateTime $checking_time) findByCheckingTime(string $checking_time) find objects in database by checking_time
 * @method static \Order findOneByCheckingTime(\Flywheel\Db\Type\DateTime $checking_time) findOneByCheckingTime(string $checking_time) find object in database by checking_time
 * @method static \Order retrieveByCheckingTime(\Flywheel\Db\Type\DateTime $checking_time) retrieveByCheckingTime(string $checking_time) retrieve object from poll by checking_time, get it from db if not exist in poll

 * @method void setCheckedTime(\Flywheel\Db\Type\DateTime $checked_time) setCheckedTime(string $checked_time) set checked_time value
 * @method \Flywheel\Db\Type\DateTime getCheckedTime() get checked_time value
 * @method static \Order[] findByCheckedTime(\Flywheel\Db\Type\DateTime $checked_time) findByCheckedTime(string $checked_time) find objects in database by checked_time
 * @method static \Order findOneByCheckedTime(\Flywheel\Db\Type\DateTime $checked_time) findOneByCheckedTime(string $checked_time) find object in database by checked_time
 * @method static \Order retrieveByCheckedTime(\Flywheel\Db\Type\DateTime $checked_time) retrieveByCheckedTime(string $checked_time) retrieve object from poll by checked_time, get it from db if not exist in poll

 * @method void setTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) setTransportingTime(string $transporting_time) set transporting_time value
 * @method \Flywheel\Db\Type\DateTime getTransportingTime() get transporting_time value
 * @method static \Order[] findByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) findByTransportingTime(string $transporting_time) find objects in database by transporting_time
 * @method static \Order findOneByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) findOneByTransportingTime(string $transporting_time) find object in database by transporting_time
 * @method static \Order retrieveByTransportingTime(\Flywheel\Db\Type\DateTime $transporting_time) retrieveByTransportingTime(string $transporting_time) retrieve object from poll by transporting_time, get it from db if not exist in poll

 * @method void setWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) setWaitingDeliveryTime(string $waiting_delivery_time) set waiting_delivery_time value
 * @method \Flywheel\Db\Type\DateTime getWaitingDeliveryTime() get waiting_delivery_time value
 * @method static \Order[] findByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) findByWaitingDeliveryTime(string $waiting_delivery_time) find objects in database by waiting_delivery_time
 * @method static \Order findOneByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) findOneByWaitingDeliveryTime(string $waiting_delivery_time) find object in database by waiting_delivery_time
 * @method static \Order retrieveByWaitingDeliveryTime(\Flywheel\Db\Type\DateTime $waiting_delivery_time) retrieveByWaitingDeliveryTime(string $waiting_delivery_time) retrieve object from poll by waiting_delivery_time, get it from db if not exist in poll

 * @method void setConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) setConfirmDeliveryTime(string $confirm_delivery_time) set confirm_delivery_time value
 * @method \Flywheel\Db\Type\DateTime getConfirmDeliveryTime() get confirm_delivery_time value
 * @method static \Order[] findByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) findByConfirmDeliveryTime(string $confirm_delivery_time) find objects in database by confirm_delivery_time
 * @method static \Order findOneByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) findOneByConfirmDeliveryTime(string $confirm_delivery_time) find object in database by confirm_delivery_time
 * @method static \Order retrieveByConfirmDeliveryTime(\Flywheel\Db\Type\DateTime $confirm_delivery_time) retrieveByConfirmDeliveryTime(string $confirm_delivery_time) retrieve object from poll by confirm_delivery_time, get it from db if not exist in poll

 * @method void setDeliveredTime(\Flywheel\Db\Type\DateTime $delivered_time) setDeliveredTime(string $delivered_time) set delivered_time value
 * @method \Flywheel\Db\Type\DateTime getDeliveredTime() get delivered_time value
 * @method static \Order[] findByDeliveredTime(\Flywheel\Db\Type\DateTime $delivered_time) findByDeliveredTime(string $delivered_time) find objects in database by delivered_time
 * @method static \Order findOneByDeliveredTime(\Flywheel\Db\Type\DateTime $delivered_time) findOneByDeliveredTime(string $delivered_time) find object in database by delivered_time
 * @method static \Order retrieveByDeliveredTime(\Flywheel\Db\Type\DateTime $delivered_time) retrieveByDeliveredTime(string $delivered_time) retrieve object from poll by delivered_time, get it from db if not exist in poll

 * @method void setReceivedTime(\Flywheel\Db\Type\DateTime $received_time) setReceivedTime(string $received_time) set received_time value
 * @method \Flywheel\Db\Type\DateTime getReceivedTime() get received_time value
 * @method static \Order[] findByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) findByReceivedTime(string $received_time) find objects in database by received_time
 * @method static \Order findOneByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) findOneByReceivedTime(string $received_time) find object in database by received_time
 * @method static \Order retrieveByReceivedTime(\Flywheel\Db\Type\DateTime $received_time) retrieveByReceivedTime(string $received_time) retrieve object from poll by received_time, get it from db if not exist in poll

 * @method void setComplaintTime(\Flywheel\Db\Type\DateTime $complaint_time) setComplaintTime(string $complaint_time) set complaint_time value
 * @method \Flywheel\Db\Type\DateTime getComplaintTime() get complaint_time value
 * @method static \Order[] findByComplaintTime(\Flywheel\Db\Type\DateTime $complaint_time) findByComplaintTime(string $complaint_time) find objects in database by complaint_time
 * @method static \Order findOneByComplaintTime(\Flywheel\Db\Type\DateTime $complaint_time) findOneByComplaintTime(string $complaint_time) find object in database by complaint_time
 * @method static \Order retrieveByComplaintTime(\Flywheel\Db\Type\DateTime $complaint_time) retrieveByComplaintTime(string $complaint_time) retrieve object from poll by complaint_time, get it from db if not exist in poll

 * @method void setOutOfStockTime(\Flywheel\Db\Type\DateTime $out_of_stock_time) setOutOfStockTime(string $out_of_stock_time) set out_of_stock_time value
 * @method \Flywheel\Db\Type\DateTime getOutOfStockTime() get out_of_stock_time value
 * @method static \Order[] findByOutOfStockTime(\Flywheel\Db\Type\DateTime $out_of_stock_time) findByOutOfStockTime(string $out_of_stock_time) find objects in database by out_of_stock_time
 * @method static \Order findOneByOutOfStockTime(\Flywheel\Db\Type\DateTime $out_of_stock_time) findOneByOutOfStockTime(string $out_of_stock_time) find object in database by out_of_stock_time
 * @method static \Order retrieveByOutOfStockTime(\Flywheel\Db\Type\DateTime $out_of_stock_time) retrieveByOutOfStockTime(string $out_of_stock_time) retrieve object from poll by out_of_stock_time, get it from db if not exist in poll

 * @method void setWarehouseInTime(\Flywheel\Db\Type\DateTime $warehouse_in_time) setWarehouseInTime(string $warehouse_in_time) set warehouse_in_time value
 * @method \Flywheel\Db\Type\DateTime getWarehouseInTime() get warehouse_in_time value
 * @method static \Order[] findByWarehouseInTime(\Flywheel\Db\Type\DateTime $warehouse_in_time) findByWarehouseInTime(string $warehouse_in_time) find objects in database by warehouse_in_time
 * @method static \Order findOneByWarehouseInTime(\Flywheel\Db\Type\DateTime $warehouse_in_time) findOneByWarehouseInTime(string $warehouse_in_time) find object in database by warehouse_in_time
 * @method static \Order retrieveByWarehouseInTime(\Flywheel\Db\Type\DateTime $warehouse_in_time) retrieveByWarehouseInTime(string $warehouse_in_time) retrieve object from poll by warehouse_in_time, get it from db if not exist in poll

 * @method void setWarehouseOutTime(\Flywheel\Db\Type\DateTime $warehouse_out_time) setWarehouseOutTime(string $warehouse_out_time) set warehouse_out_time value
 * @method \Flywheel\Db\Type\DateTime getWarehouseOutTime() get warehouse_out_time value
 * @method static \Order[] findByWarehouseOutTime(\Flywheel\Db\Type\DateTime $warehouse_out_time) findByWarehouseOutTime(string $warehouse_out_time) find objects in database by warehouse_out_time
 * @method static \Order findOneByWarehouseOutTime(\Flywheel\Db\Type\DateTime $warehouse_out_time) findOneByWarehouseOutTime(string $warehouse_out_time) find object in database by warehouse_out_time
 * @method static \Order retrieveByWarehouseOutTime(\Flywheel\Db\Type\DateTime $warehouse_out_time) retrieveByWarehouseOutTime(string $warehouse_out_time) retrieve object from poll by warehouse_out_time, get it from db if not exist in poll

 * @method void setCurrentWarehouseTime(\Flywheel\Db\Type\DateTime $current_warehouse_time) setCurrentWarehouseTime(string $current_warehouse_time) set current_warehouse_time value
 * @method \Flywheel\Db\Type\DateTime getCurrentWarehouseTime() get current_warehouse_time value
 * @method static \Order[] findByCurrentWarehouseTime(\Flywheel\Db\Type\DateTime $current_warehouse_time) findByCurrentWarehouseTime(string $current_warehouse_time) find objects in database by current_warehouse_time
 * @method static \Order findOneByCurrentWarehouseTime(\Flywheel\Db\Type\DateTime $current_warehouse_time) findOneByCurrentWarehouseTime(string $current_warehouse_time) find object in database by current_warehouse_time
 * @method static \Order retrieveByCurrentWarehouseTime(\Flywheel\Db\Type\DateTime $current_warehouse_time) retrieveByCurrentWarehouseTime(string $current_warehouse_time) retrieve object from poll by current_warehouse_time, get it from db if not exist in poll

 * @method void setCancelledTime(\Flywheel\Db\Type\DateTime $cancelled_time) setCancelledTime(string $cancelled_time) set cancelled_time value
 * @method \Flywheel\Db\Type\DateTime getCancelledTime() get cancelled_time value
 * @method static \Order[] findByCancelledTime(\Flywheel\Db\Type\DateTime $cancelled_time) findByCancelledTime(string $cancelled_time) find objects in database by cancelled_time
 * @method static \Order findOneByCancelledTime(\Flywheel\Db\Type\DateTime $cancelled_time) findOneByCancelledTime(string $cancelled_time) find object in database by cancelled_time
 * @method static \Order retrieveByCancelledTime(\Flywheel\Db\Type\DateTime $cancelled_time) retrieveByCancelledTime(string $cancelled_time) retrieve object from poll by cancelled_time, get it from db if not exist in poll


 */
abstract class OrderBase extends ActiveRecord {
    protected static $_tableName = 'order';
    protected static $_phpName = 'Order';
    protected static $_pk = 'id';
    protected static $_alias = 'o';
    protected static $_dbConnectName = 'order';
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
                'db_type' => 'char(100)',
                'length' => 100),
        'avatar' => array('name' => 'avatar',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'status' => array('name' => 'status',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
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
        'buyer_id' => array('name' => 'buyer_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'order_quantity' => array('name' => 'order_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'pending_quantity' => array('name' => 'pending_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'recive_quantity' => array('name' => 'recive_quantity',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'packages_quantity' => array('name' => 'packages_quantity',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'customer_confirm' => array('name' => 'customer_confirm',
                'default' => 'NONE',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'enum(\'CONFIRMED\',\'WAIT\',\'NONE\')',
                'length' => 9),
        'note_customer_confirm' => array('name' => 'note_customer_confirm',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'total_amount' => array('name' => 'total_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'order_amount' => array('name' => 'order_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'real_amount' => array('name' => 'real_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'bought_amount' => array('name' => 'bought_amount',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'deposit_amount' => array('name' => 'deposit_amount',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'deposit_ratio' => array('name' => 'deposit_ratio',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(2,1)',
                'length' => 2),
        'refund_amount' => array('name' => 'refund_amount',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'real_payment_amount' => array('name' => 'real_payment_amount',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'real_refund_amount' => array('name' => 'real_refund_amount',
                'default' => 0.00,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'real_surcharge' => array('name' => 'real_surcharge',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'real_service_amount' => array('name' => 'real_service_amount',
                'default' => 0.00,
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'service_fee' => array('name' => 'service_fee',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'domestic_shipping_fee' => array('name' => 'domestic_shipping_fee',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'domestic_shipping_fee_vnd' => array('name' => 'domestic_shipping_fee_vnd',
                'default' => 0.00,
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'direct_fill_amount_cny' => array('name' => 'direct_fill_amount_cny',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'direct_fill_amount_vnd' => array('name' => 'direct_fill_amount_vnd',
                'not_null' => false,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'double(20,2)',
                'length' => 20),
        'payment_link' => array('name' => 'payment_link',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'exchange' => array('name' => 'exchange',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'weight' => array('name' => 'weight',
                'not_null' => true,
                'type' => 'number',
                'auto_increment' => false,
                'db_type' => 'decimal(6,2)',
                'length' => 6),
        'invoice' => array('name' => 'invoice',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'alipay' => array('name' => 'alipay',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(200)',
                'length' => 200),
        'freight_bill' => array('name' => 'freight_bill',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'has_freight_bill' => array('name' => 'has_freight_bill',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'current_warehouse' => array('name' => 'current_warehouse',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'char(10)',
                'length' => 10),
        'next_warehouse' => array('name' => 'next_warehouse',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'char(10)',
                'length' => 10),
        'destination_warehouse' => array('name' => 'destination_warehouse',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'char(10)',
                'length' => 10),
        'warehouse_status' => array('name' => 'warehouse_status',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(50)',
                'length' => 50),
        'transport_status' => array('name' => 'transport_status',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'checking_status' => array('name' => 'checking_status',
                'default' => 'NOT_YET_CHECKED',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'char(20)',
                'length' => 20),
        'transport_vn_type' => array('name' => 'transport_vn_type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'warning_score' => array('name' => 'warning_score',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'user_address_id' => array('name' => 'user_address_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'tellers_id' => array('name' => 'tellers_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'paid_staff_id' => array('name' => 'paid_staff_id',
                'default' => 0,
                'not_null' => false,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'delivery_staff_id' => array('name' => 'delivery_staff_id',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'checker_id' => array('name' => 'checker_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'account_purchase_origin' => array('name' => 'account_purchase_origin',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'name_recipient_origin' => array('name' => 'name_recipient_origin',
                'not_null' => false,
                'type' => 'string',
                'db_type' => 'varchar(30)',
                'length' => 30),
        'complain_seller' => array('name' => 'complain_seller',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(1)',
                'length' => 1),
        'is_deleted' => array('name' => 'is_deleted',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'tinyint(2)',
                'length' => 1),
        'confirm_created_time' => array('name' => 'confirm_created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'confirm_approval_time' => array('name' => 'confirm_approval_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'created_time' => array('name' => 'created_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'modified_time' => array('name' => 'modified_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'expire_time' => array('name' => 'expire_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'deposit_time' => array('name' => 'deposit_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'real_payment_last_time' => array('name' => 'real_payment_last_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'tellers_assigned_time' => array('name' => 'tellers_assigned_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'paid_staff_assigned_time' => array('name' => 'paid_staff_assigned_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'buying_time' => array('name' => 'buying_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'negotiating_time' => array('name' => 'negotiating_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'negotiated_time' => array('name' => 'negotiated_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'bought_time' => array('name' => 'bought_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
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
        'checking_time' => array('name' => 'checking_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'checked_time' => array('name' => 'checked_time',
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
        'delivered_time' => array('name' => 'delivered_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'received_time' => array('name' => 'received_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'complaint_time' => array('name' => 'complaint_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'out_of_stock_time' => array('name' => 'out_of_stock_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'warehouse_in_time' => array('name' => 'warehouse_in_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'warehouse_out_time' => array('name' => 'warehouse_out_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => false,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'current_warehouse_time' => array('name' => 'current_warehouse_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'cancelled_time' => array('name' => 'cancelled_time',
                'default' => '0000-00-00 00:00:00',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
     );
    protected static $_validate = array(
        'code' => array(
            array('name' => 'Unique',
                'message'=> 'code\'s was used'
            ),
        ),
        'customer_confirm' => array(
            array('name' => 'ValidValues',
                'value' => 'CONFIRMED|WAIT|NONE',
                'message'=> 'customer confirm\'s values is not allowed'
            ),
        ),
    );
    protected static $_validatorRules = array(
        'code' => array(
            array('name' => 'Unique',
                'message'=> 'code\'s was used'
            ),
        ),
        'customer_confirm' => array(
            array('name' => 'ValidValues',
                'value' => 'CONFIRMED|WAIT|NONE',
                'message'=> 'customer confirm\'s values is not allowed'
            ),
        ),
    );
    protected static $_init = false;
    protected static $_cols = array('id','code','avatar','status','seller_name','seller_aliwang','seller_homeland','seller_info','buyer_id','order_quantity','pending_quantity','recive_quantity','packages_quantity','customer_confirm','note_customer_confirm','total_amount','order_amount','real_amount','bought_amount','deposit_amount','deposit_ratio','refund_amount','real_payment_amount','real_refund_amount','real_surcharge','real_service_amount','service_fee','domestic_shipping_fee','domestic_shipping_fee_vnd','direct_fill_amount_cny','direct_fill_amount_vnd','payment_link','exchange','weight','invoice','alipay','freight_bill','has_freight_bill','current_warehouse','next_warehouse','destination_warehouse','warehouse_status','transport_status','checking_status','transport_vn_type','warning_score','user_address_id','tellers_id','paid_staff_id','delivery_staff_id','checker_id','account_purchase_origin','name_recipient_origin','complain_seller','is_deleted','confirm_created_time','confirm_approval_time','created_time','modified_time','expire_time','deposit_time','real_payment_last_time','tellers_assigned_time','paid_staff_assigned_time','buying_time','negotiating_time','negotiated_time','bought_time','seller_delivered_time','received_from_seller_time','checking_time','checked_time','transporting_time','waiting_delivery_time','confirm_delivery_time','delivered_time','received_time','complaint_time','out_of_stock_time','warehouse_in_time','warehouse_out_time','current_warehouse_time','cancelled_time');

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