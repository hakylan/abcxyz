<?php 
/**
 * UserTransaction
 * @version		$Id$
 * @package		Model

 */
use Flywheel\Db\Query;

require_once dirname(__FILE__) .'/Base/UserTransactionBase.php';
class UserTransaction extends \UserTransactionBase {

    const STATE_PENDING = 'PENDING';
    const STATE_COMPLETED = 'COMPLETED';
    const STATE_CANCELED = 'CANCELED';
    const STATE_REJECTED = 'REJECTED';

    const TRANSACTION_TYPE_INIT = 'INIT';
    const TRANSACTION_TYPE_DEPOSIT = 'DEPOSIT';
    const TRANSACTION_TYPE_WITHDRAWAL = 'WITHDRAWAL';
    const TRANSACTION_TYPE_REFUND = 'REFUND';
    const TRANSACTION_TYPE_ORDER_DEPOSIT = 'ORDER_DEPOSIT';
    const TRANSACTION_TYPE_ORDER_PAYMENT = 'ORDER_PAYMENT';
    const TRANSACTION_TYPE_ADJUSTMENT = 'ADJUSTMENT';
    const TRANSACTION_TYPE_CHARGE_FEE = 'CHARGE_FEE';
    const OBJECT_TYPE_ORDER = 'ORDER';
    //Khong dung
//    const TRANSACTION_TYPE_CHECKING = 'CHECKING';

    public static $transaction_type = array(
        self::TRANSACTION_TYPE_INIT => 'Khởi tạo',
        self::TRANSACTION_TYPE_DEPOSIT => 'Nạp tiền',
        self::TRANSACTION_TYPE_ORDER_DEPOSIT => 'Đặt cọc',
        self::TRANSACTION_TYPE_ORDER_PAYMENT => 'Thanh toán',
        self::TRANSACTION_TYPE_REFUND => 'Trả lại',
        self::TRANSACTION_TYPE_WITHDRAWAL => 'Rút tiền',
        self::TRANSACTION_TYPE_ADJUSTMENT => "Điều chỉnh",
        self::TRANSACTION_TYPE_CHARGE_FEE => "Thu PVC Nội địa VN"
    );

    public static $transaction_state = array(
        //'PENDING','COMPLETED','CANCELED','REJECTED'
        self::STATE_CANCELED => 'Hủy bỏ',
        self::STATE_COMPLETED => 'Hoàn thành',
        self::STATE_PENDING => 'Chờ duyệt',
        self::STATE_REJECTED => 'Từ chối',
    );

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'created_time',
                'modify_attr' => 'modified_time'
            )
        );
    }

    public function getDetail(){
        $transaction_detail = $this->getTransactionDetail();
        if($transaction_detail != ''){
            $transaction_detail = json_decode($transaction_detail,true);
        }
        if(!is_array($transaction_detail)){
            $transaction_detail = json_decode($transaction_detail,true);
        }

        $detail = "";

        if(isset($transaction_detail["message"])){
            $detail = $transaction_detail['message'];
        }elseif(isset($transaction_detail["detail"])){
            $detail = $transaction_detail["detail"];
        }

        if($transaction_detail == ''){
            $detail = $this->getTransactionDetail();
        }

        return $detail;
    }

    /**
     * Get transaction type title -- quyen
     * @return mixed
     */
    public function getTransactionTypeTitle()
    {
        $title = static::$transaction_type[$this->getTransactionType()];
        return $title;
    }


    /**
     * Get Transaction State Title - Quyen
     * @return mixed
     */
    public function getTransactionStateTitle(){
        return static::$transaction_state[$this->getState()];
    }

    /**
     * Get total ending balance by transaction type - Quyen
     * @param $user_id
     * @param null $transaction_type
     * @return int
     */
    public static function getTotalEndingBalance($user_id,$transaction_type = null){
        $query = UserTransaction::read();
        $query->select("sum(ending_balance) as total")->andWhere("user_id={$user_id}");
        if($transaction_type != null){
            $query->andWhere("transaction_type = '{$transaction_type}'");
        }
        $data = $query->execute()->fetch();

        $total = isset($data['total']) ? intval($data['total']) : 0;

        return $total;
    }

    /**
     * Get total ending balance by transaction type - QUyen
     * @param $user_id
     * @param null $transaction_type
     * @return int
     */
    public static function getTotalAmount($user_id,$transaction_type = null){
        $query = UserTransaction::read();
        $query->select("sum(amount) as total")->andWhere("user_id={$user_id}");
        if($transaction_type != null){
            $query->andWhere("transaction_type = '{$transaction_type}'");
        }
        $data = $query->execute()->fetch();

        $total = isset($data['total']) ? intval($data['total']) : 0;

        return $total;
    }

    /**
     * Get user transaction - Quyen
     * @param Query $query
     * @return mixed
     */
    public static function getUserTransaction(Query $query = null){
        if($query == null){
            $query = UserTransaction::read();
        }

        return $query->execute()
            ->fetchAll(\PDO::FETCH_CLASS, \UserTransaction::getPhpName(), array(null, false));
    }

    /**
     * create new user_transaction with object type is order
     * @param Order $order
     * @param $amount
     * @param $accountBalance
     * @param $transactionType
     * @param $accountantTransaction
     * @param $detail
     * @throws RuntimeException
     * @return \UserTransaction
     */
    public static function createOrderTransactionHistory(\Order $order,
                                                         $amount,
                                                 $accountBalance,
                                                 $transactionType,
                                                 $accountantTransaction,
                                                 $detail) {
        //save user transaction
        $userTransaction = new \UserTransaction();
        $userTransaction->setUserId($order->getBuyerId());
        $userTransaction->setAmount($accountantTransaction["amount"]);
        $userTransaction->setEndingBalance($accountBalance);
        $userTransaction->setObjectType(\UserTransaction::OBJECT_TYPE_ORDER);
        $userTransaction->setObjectId($order->getCode());
        $userTransaction->setTransactionType($transactionType);
        $userTransaction->setTransactionDetail($detail);
        $userTransaction->setTransactionCode($accountantTransaction['uid']);
        $userTransaction->setState(\UserTransaction::STATE_COMPLETED);
        if (is_array($accountantTransaction['modified_time'])) {
            $closedTime = new DateTime($accountantTransaction['modified_time']['date']);
        } elseif (is_scalar($accountantTransaction['modified_time'])) {
            $closedTime = new DateTime($accountantTransaction['modified_time']);
        } else {
            $closedTime = new DateTime();
        }
        $userTransaction->setClosedTime($closedTime);

        if ($userTransaction->save()) {
            return $userTransaction;
        } else {
            throw new \RuntimeException('Could not save user transaction:' . $userTransaction->getValidationFailuresMessage("\n"));
        }
    }

    /**
     * Tạo lịch sử giao dịch khi trừ phí vận chuyển nội địa trong quản lý giao hàng
     * @param Users $buyer
     * @param $account_balance
     * @param $transfer_transaction
     * @param $detail
     * @param $note
     * @return UserTransaction
     * @throws Exception
     */
    public static function createTransactionChargeFee(\Users $buyer,$account_balance,$transfer_transaction,$detail,$note){
        try{
            $userTransaction = new UserTransaction();
            $userTransaction->setUserId($buyer->getId());
            $userTransaction->setState(UserTransaction::STATE_COMPLETED);
            $userTransaction->setTransactionCode($transfer_transaction['uid']);
            $userTransaction->setTransactionType(UserTransaction::TRANSACTION_TYPE_CHARGE_FEE);
            $userTransaction->setAmount($transfer_transaction["amount"]);
            $userTransaction->setEndingBalance($account_balance);
            $userTransaction->setTransactionDetail($detail);
            $userTransaction->setTransactionNote($note);
            $userTransaction->setCreatedTime(new DateTime());
            if (is_array($transfer_transaction['modified_time'])) {
                $closedTime = new DateTime($transfer_transaction['modified_time']['date']);
            } elseif (is_scalar($transfer_transaction['modified_time'])) {
                $closedTime = new DateTime($transfer_transaction['modified_time']);
            } else {
                $closedTime = new DateTime();
            }
            $userTransaction->setClosedTime($closedTime);

            if ($userTransaction->save()) {
                return $userTransaction;
            } else {
                throw new \RuntimeException('Could not save user transaction:' . $userTransaction->getValidationFailuresMessage("\n"));
            }
        }catch (\Exception $e){
            throw $e;
        }

    }

    /**
     * Control transaction - create by quyen
     * @param UserTransaction $before_transaction
     * @param UserTransaction $after_transaction
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function controlTransaction(\UserTransaction $before_transaction,\UserTransaction $after_transaction){
        if($after_transaction instanceof \UserTransaction && $before_transaction instanceof \UserTransaction){
            if(($before_transaction->getEndingBalance() + $after_transaction->getAmount())
                != $after_transaction->getEndingBalance()){
                return false;
            }
            return true;
        } else {
            throw new InvalidArgumentException(
                "Invalid parameter in control transaction "
            );
        }
    }

    /**
     * Create user's transaction from payment's transaction history
     * history is array with data:
     * Array (
     *   [id] => 1598
     *   [transaction_uid] => 201404176136721817317384
     *   [account] => 2014036136721817
     *   [note] => Đặt cọc cho đơn hàng ME79_170408
     *   [detail] => {"order_code":"ME79_170408","type":"DEPOSIT","detail":"\u0110\u1eb7t c\u1ecdc cho \u0111\u01a1n h\u00e0ng ME79_170408"}
     *   [amount] => -1160000
     *   [acc_balance] => 3525840
     *   [completed_time] => Array(
     *      [date] => 2014-04-17 21:31:42
     *      [timezone_type] => 3
     *      [timezone] => Asia/Ho_Chi_Minh
     *   )
     * )

     * @param Users $user
     * @param $history
     * @return \UserTransaction
     * @throws Flywheel\Db\Exception
     */
    public static function createFromTransactionHistory(\Users $user, $history) {
        $transaction_check = \UserTransaction::retrieveByTransactionCode($history['transaction_uid']);
        if($transaction_check instanceof \UserTransaction){
            throw new \Flywheel\Db\Exception("Already exists user transaction with transaction code {$history['transaction_uid']}");
        }
        $obj = new self();
        //common
        $obj->setUserId($user->getId());
        $obj->setTransactionCode($history['transaction_uid']);
        $obj->setState(self::STATE_COMPLETED);
        $obj->setAmount($history['amount']);
        $obj->setEndingBalance($history['acc_balance']);
        $obj->setTransactionNote($history['note']);
        $obj->setTransactionDetail($history['detail']);

        //closed time
        if (is_array($history['completed_time'])) {
            $closed_time = new DateTime($history['completed_time']['date']);
        } elseif (is_scalar($history['completed_time'])) {
            $closed_time = new DateTime($history['completed_time']);
        } else {
            $closed_time = new DateTime();
        }
        $obj->setClosedTime($closed_time);

        //get payment transaction detail
        $trans =  \SeuDo\Accountant\Util::getTransactionDetail($history['transaction_uid']);

        if ('IN' == $trans['type']) {
            $obj->setTransactionType(self::TRANSACTION_TYPE_DEPOSIT);
        } else if ('OUT' == $trans['type']) {
            $obj->setTransactionType(self::TRANSACTION_TYPE_WITHDRAWAL);
        } else if ('ADJUSTMENT' == $trans['type']) {
            $obj->setTransactionType(self::TRANSACTION_TYPE_ADJUSTMENT);
        } else if ('TRANSFER' == $trans['type']) {
            //process type
            $detail = json_decode($trans['detail'], true);
            if (!$detail) {
                $detail = $trans['detail'];
            }

            if (is_array($detail) && isset($detail['order_code'])) {
                if (!isset($detail['type'])) {
                    $detail['type'] = 'UNKNOWN';
                }
                if ('DEPOSIT' == $detail['type']) {//deposit order
                    $obj->setTransactionType(self::TRANSACTION_TYPE_ORDER_DEPOSIT);
                } else if ('REFUND' == $detail['type']) {//refund when out of stock or cancelling
                    $obj->setTransactionType(self::TRANSACTION_TYPE_REFUND);
                } else {//payment (default) @todo should make it better
                    $obj->setTransactionType(self::TRANSACTION_TYPE_ORDER_PAYMENT);
                }

                $obj->setObjectType(self::OBJECT_TYPE_ORDER);
                $obj->setObjectId($detail['order_code']);
            } else {// is string not special transaction's business
                $obj->setTransactionType(self::TRANSACTION_TYPE_ADJUSTMENT);
            }

        } else {
            throw new \Flywheel\Db\Exception("Unknown transaction type: " .$trans['type'] .", trans uid: " .$trans['uid']);
        }

        $result = $obj->save();

        if(!$result) {
            throw new \Flywheel\Db\Exception("Could not save user transaction. Validation Failures:\n"
                .$obj->getValidationFailuresMessage("\n"));
        }

        return $obj;
    }

    /**
     * Synchronize account's transaction history
     *
     * @param Users $user
     * @param $from_time
     * @param $to_time
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function syncTransactionHistory(\Users $user, $from_time, $to_time) {

        $logger = \SeuDo\Logger::factory("transaction_sys");
        if (!$user->getAccountNo()) {
            throw new \Exception("{$user->getUsername()} have not had account number");
        }

        //process from time
        if (is_string($from_time)) {
            if (!\Flywheel\Validator\Util::validateDate($from_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'from_time' format!");
            }

            $from_time = \DateTime::createFromFormat("Y-m-d H:i:s", $from_time);
        } elseif (is_int($from_time)) {
            $from_time = \DateTime::createFromFormat('U', $from_time);
        }

        //process to time
        if (is_string($to_time)) {
            if (!\Flywheel\Validator\Util::validateDate($to_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'to_time' format!");
            }

            $to_time = \DateTime::createFromFormat("Y-m-d H:i:s", $to_time);
        } elseif (is_int($to_time)) {
            $to_time = \DateTime::createFromFormat('U', $to_time);
        }

        if (!($from_time instanceof \DateTime) || !($to_time instanceof \DateTime)) {
            throw new \InvalidArgumentException("Invalid 'from_time' or 'to_time' format!");
        }

        if ($from_time > $to_time) {
            throw new \InvalidArgumentException("From time could not be before to time");
        }

        /** @var \UserTransaction[] $transactions */
        $transactions = self::select()
            ->where('user_id = :id')
            ->setParameter(':id', $user->getId(), \PDO::PARAM_INT)
            ->andWhere('closed_time BETWEEN :from AND :to')
            ->setParameter(':from', $from_time->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->setParameter(':to', $to_time->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->execute();
        //make assoc array
        $t = array();
        for ($i = 0, $size = sizeof($transactions); $i < $size; ++$i) {
            $t[$transactions[$i]->getTransactionCode()] = $transactions[$i];
        }
        $transactions = $t;

        //payment's transaction history
        $histories =  \SeuDo\Accountant\Util::getUserTransactionHistory($user, $from_time, $to_time, 0, 99999);
//        $histories = \TransactionHistory::read()->andWhere("completed_time BETWEEN '{$from_time->format("Y-m-d H:i:s")}' and '{$to_time->format("Y-m-d H:i:s")}'")
//            ->andWhere("account='{$user->getAccountNo()}'")
//            ->execute()->fetchAll(PDO::FETCH_CLASS,TransactionHistory::getPhpName(),array(false,true));
//        if(empty($histories)){
//            return false;
//        }
        $conn = self::getWriteConnection();
        $conn->beginTransaction();
        $total_create = 0;
        $total_update = 0;
        for ($i = 0, $size = sizeof($histories); $i < $size; ++$i) {
//            $histories[$i] = $histories[$i]->toArray();
            $trans_code = $histories[$i]['transaction_uid'];
            if (!isset($transactions[$trans_code])) {//news
                $logger->info("Create user transaction with user : {$user->getUsername()} - {$user->getFullName()} when not exist transaction code :{$trans_code}" );
                $total_create ++;
                self::createFromTransactionHistory($user, $histories[$i]);
            } else {//existed
                if($transactions[$trans_code]->getAmount() == floatval(-$histories[$i]['amount'])){
                    $amount = $transactions[$trans_code]->getAmount();
                    $transactions[$trans_code]->setAmount($histories[$i]['amount']);
                    $result = $transactions[$trans_code]->save();
                    if($result){
                        $total_update++;
                        $logger->info("Update amount transaction match with amount history transaction. Transaction_amount = {$amount} , history_amount = {$histories[$i]['amount']}",array("Transaction code : {$transactions[$trans_code]->getTransactionCode()}") );
                    }else{
                        $logger->info("Can't Update amount transaction match with amount history transaction. Transaction_amount = {$amount} , history_amount = {$histories[$i]['amount']}",array("Transaction code : {$transactions[$trans_code]->getTransactionCode()}") );
                    }
                }else{
                    if ($transactions[$trans_code]->getAmount() != $histories[$i]['amount']
                        || $transactions[$trans_code]->getEndingBalance() != $histories[$i]['acc_balance']) {
                        $logger->addWarning("Something went wrong, transaction history not matching amount and acc_balance. Transaction {$transactions[$trans_code]}");
                        throw new \Exception("Something went wrong, transaction history not matching amount and acc_balance. Transaction {$transactions[$trans_code]}");
                    }
                    $logger->info("Transaction Match with transaction code: ".$trans_code );
                }
            }
        }

        $logger->addInfo("Create : {$total_create} Update: {$total_update}");
        $conn->commit();
    }

    /**
     * Check user transaction in exactly time
     * @param Users $user
     * @param $from_time
     * @param $to_time
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function checkUserTransaction(\Users $user, $from_time, $to_time) {
        if (!$user->getAccountNo()) {
            throw new \Exception("{$user->getUsername()} have not had account number");
        }

        //process from time
        if (is_string($from_time)) {
            if (!\Flywheel\Validator\Util::validateDate($from_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'from_time' format!");
            }

            $from_time = \DateTime::createFromFormat("Y-m-d H:i:s", $from_time);
        } elseif (is_int($from_time)) {
            $from_time = \DateTime::createFromFormat('U', $from_time);
        }

        //process to time
        if (is_string($to_time)) {
            if (!\Flywheel\Validator\Util::validateDate($to_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'to_time' format!");
            }

            $to_time = \DateTime::createFromFormat("Y-m-d H:i:s", $to_time);
        } elseif (is_int($to_time)) {
            $to_time = \DateTime::createFromFormat('U', $to_time);
        }

        if (!($from_time instanceof \DateTime) || !($to_time instanceof \DateTime)) {
            throw new \InvalidArgumentException("Invalid 'from_time' or 'to_time' format!");
        }

        if ($from_time > $to_time) {
            throw new \InvalidArgumentException("From time could not be before to time");
        }

        //get User's transaction
        /** @var \UserTransaction[] $transactions */
        $transactions = self::select()
            ->where('user_id = :id')
            ->setParameter(':id', $user->getId(), \PDO::PARAM_INT)
            ->andWhere('closed_time BETWEEN :from AND :to')
            ->setParameter(':from', $from_time->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->setParameter(':to', $to_time->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->orderBy('closed_time')
            ->execute();

        $discontinuous = array();
        for ($i = 0, $size = sizeof($transactions); $i < $size-1; ++$i) {
            $valid = self::controlTransaction($transactions[$i], $transactions[$i+1]);
            if (!$valid) {
                $discontinuous[] = array(
                    'from' => $transactions[$i],
                    'to' => $transactions[$i+1]
                );
            }
        }

        return $discontinuous;
    }

    /**
     * Build Transaction in order to array -- create by Quyen
     * @param Order $order
     * @return array
     */
    public static function buildTransactionInOrderToArray(\Order $order){
        $transaction_list = array();
        if($order instanceof \Order){
            /**
             * @var \UserTransaction[] $transaction
             */
            $transaction = \UserTransaction::read()->andWhere("object_id='{$order->getCode()}'")
                ->orderBy("id","ASC")->orderBy("closed_time","ASC")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(false,null));;// \UserTransaction::findByObjectId($order->getCode());
            if(!empty($transaction)){
                foreach ($transaction as $tran) {
                    $array = $tran->toArray();
                    $array["transaction_type_title"] = $tran->getTransactionTypeTitle();
                    $array["transaction_state_title"] = $tran->getTransactionStateTitle();
                    $time = $tran->getClosedTime();
                    $array["closed_time_format"] = $time->format("H:i d/m/Y");
                    $array["closed_time_format_type2"] = $time->format("H:i d/m");
                    $transaction_list[] = $array;
                }
            }
        }
        return $transaction_list;
    }

    /**
     * kiểm tra lịch sử giao dịch theo đơn hàng, nếu có lỗi sẽ bắn lỗi gửi mail
     * @param Order $order
     * @return array
     * @throws Flywheel\Exception
     */
    public static function checkTransactionOrder(\Order $order){
        $flag = true;
        $array_error = array();
        if($order instanceof \Order){
            $user = \Users::retrieveById($order->getBuyerId());
            if(!$user instanceof \Users){
                return false;
            }
            $logger = \SeuDo\Logger::factory("transaction_order_checking");
            $order_deposit_transaction = \UserTransaction::findByObjectIdAndTransactionType($order->getCode(),\UserTransaction::TRANSACTION_TYPE_ORDER_DEPOSIT);
            if(sizeof($order_deposit_transaction) > 1){
                $logger->addInfo("Order with code : {$order->getCode()} of user {$user->getUsername()} exist > 1 ORDER_DEPOSIT transaction");
                $flag = false;
                foreach ($order_deposit_transaction as $deposit) {
                    if($deposit instanceof \UserTransaction){
                        $array = $deposit->toArray();
                        $array_error["deposit"][] = $array;
                    }
                }
            }

            $order_payment_transaction = \UserTransaction::findByObjectIdAndTransactionType($order->getCode(),\UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT);
            if(sizeof($order_payment_transaction) > 1){
                $logger->addInfo("Order with code : {$order->getCode()} of user {$user->getUsername()} exist > 1 ORDER_PAYMENT transaction");
                $flag = false;
                foreach ($order_payment_transaction as $payment) {
                    if($payment instanceof \UserTransaction){
                        $array = $payment->toArray();
                        $array_error["payment"][] = $array;
                    }
                }
            }

            if($flag){
//                $logger->addInfo("Order with code: {$order->getCode()} of user {$user->getUsername()} transaction match");
            }
        }else{
            throw new \Flywheel\Exception("Not exist order. Order not instanceof \Order");
        }
        return $array_error;
    }

    /**
     * Kiểm tra trong lịch sử giao dịch có đơn hàng nào tồn tại nhiều hơn 1 lịch sử đặt cọc hoặc payment ko.
     */
    public static function checkUserTransactionOrder($from_time,$to_time = ''){
        try{
            if($to_time == ''){
                $to_time = date('Y-m-d H:i:s',time());
            }
            $from_time = Common::validDateTime($from_time);
            $to_time = Common::validDateTime($to_time);
            $transaction_list = \UserTransaction::read()->andWhere("closed_time > '{$from_time}' AND  closed_time < '{$to_time}' AND object_id != ''")
                ->execute()->fetchAll(PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(null,false));
            if(!empty($transaction_list)){
                foreach ($transaction_list as $transaction) {
                    if($transaction instanceof \UserTransaction){
                        $order = \Order::retrieveByCode($transaction->getObjectId());
                        if($order instanceof \Order){
                            self::checkTransactionOrder($order);
                        }
                    }
                }
            }
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * Đối soát lịch sử giao dịch ở accountant, những giao dịch nào chưa tồn tại ở user transaction được coi là lỗi
     * @param Users $user
     * @param $from_time
     * @param $to_time
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function controlTransactionHistory(\Users $user, $from_time, $to_time) {

        $logger = \SeuDo\Logger::factory("control_history");



        if (!$user->getAccountNo()) {
            throw new \Exception("{$user->getUsername()} have not had account number");
        }

        //process from time
        if (is_string($from_time)) {
            if (!\Flywheel\Validator\Util::validateDate($from_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'from_time' format!");
            }

            $from_time = \DateTime::createFromFormat("Y-m-d H:i:s", $from_time);
        } elseif (is_int($from_time)) {
            $from_time = \DateTime::createFromFormat('U', $from_time);
        }

        //process to time
        if (is_string($to_time)) {
            if (!\Flywheel\Validator\Util::validateDate($to_time, 'Y-m-d H:i:s')) {
                throw new \InvalidArgumentException("Invalid 'to_time' format!");
            }

            $to_time = \DateTime::createFromFormat("Y-m-d H:i:s", $to_time);
        } elseif (is_int($to_time)) {
            $to_time = \DateTime::createFromFormat('U', $to_time);
        }

        if (!($from_time instanceof \DateTime) || !($to_time instanceof \DateTime)) {
            throw new \InvalidArgumentException("Invalid 'from_time' or 'to_time' format!");
        }

        if ($from_time > $to_time) {
            throw new \InvalidArgumentException("From time could not be before to time");
        }

        /** @var \UserTransaction[] $transactions */
        $query = \UserTransaction::read()
            ->andWhere("user_id={$user->getId()}")
            ->andWhere("closed_time BETWEEN '{$from_time->format('Y-m-d H:i:s')}' AND '{$to_time->format('Y-m-d H:i:s')}'");
        $transactions = $query
            ->execute()->fetchAll(PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(null,false));

        $t = array();
        for ($i = 0, $size = sizeof($transactions); $i < $size; ++$i) {
            $t[$transactions[$i]->getTransactionCode()] = $transactions[$i];
        }
        $transactions = $t;

        //payment's transaction history
        $histories =  \SeuDo\Accountant\Util::getUserTransactionHistory($user, $from_time, $to_time, 0, 99999);

        $array_error = array();
        for ($i = 0, $size = sizeof($histories); $i < $size; ++$i) {
            $trans_code = $histories[$i]['transaction_uid'];
            $detail = json_decode($histories[$i]["detail"],true);

            $flag = false;
            //process type
            if (!$detail) {
                $detail = $histories[$i]['detail'];
            }

            $type = "";

            if (is_array($detail) && isset($detail['order_code'])) {
                if (!isset($detail['type'])) {
                    $detail['type'] = 'UNKNOWN';
                }
                if ('DEPOSIT' == $detail['type']) {//deposit order
                    $type = self::TRANSACTION_TYPE_ORDER_DEPOSIT;
                } else if ('REFUND' == $detail['type']) {//refund when out of stock or cancelling
                    $type = self::TRANSACTION_TYPE_REFUND;
                } else {//payment (default) @todo should make it better
                    $type = self::TRANSACTION_TYPE_ORDER_PAYMENT;
                }
            }

            if($type ==  self::TRANSACTION_TYPE_ORDER_DEPOSIT || $type == self::TRANSACTION_TYPE_ORDER_PAYMENT){
                if (!isset($transactions[$trans_code])) {//news
                    $user_transaction = \UserTransaction::findByObjectIdAndTransactionType($detail['order_code'],$type);
                    if($user_transaction){
                        $histories[$i]["message"] = "Tồn tại nhiều hơn 1 transaction {$type} với mã đơn hàng {$detail['order_code']} -- WTF";
                        $histories[$i]["username"] = $user->getUsername();
                        $array_error[] = $histories[$i];
                        $logger->info("Exist more than one transaction {$type} with order code :{$detail['order_code']}" );
                    }else{
//                        self::createFromTransactionHistory($user, $histories[$i]);
                    }
                }else{
                    $user_transaction = \UserTransaction::findByObjectIdAndTransactionType($detail['order_code'],$type);
                    if(sizeof($user_transaction) > 1){
                        $histories[$i]["message"] = "Tồn tại nhiều hơn 1 transaction {$type} với mã đơn hàng {$detail['order_code']}";
                        $histories[$i]["username"] = $user->getUsername();
                        $logger->info("Exist more than one transaction {$type} with order code :{$detail['order_code']}" );
                        $array_error[] = $histories[$i];
                    }
                }
            }else{
                if (!isset($transactions[$trans_code])) {//news
//                    self::createFromTransactionHistory($user, $histories[$i]);
                }

            }
        }

        return $array_error;
    }
}