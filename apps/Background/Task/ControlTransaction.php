<?php
namespace Background\Task;
use Background\Library\EmailHelper;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use SeuDo\Logger;


class ControlTransaction extends BackgroundBase {
    protected $limitExecuteTime = 20;
    protected $limit = 100;

    /**
     * @var \SeuDo\Logger::factory("transaction")
     */
    protected $logger = null;


    public function beforeExecute(){
        $this->logger = Logger::factory("transaction_checking");
    }

    /**
     * Kiểm tra lịch sử giao dịch nếu  $before_transaction->getEndingBalance() + $after_transaction->getAmount())
    != $after_transaction->getEndingBalance() sẽ coi như là lỗi
     * và bắn mail cảnh báo.
     */

    public function executeControl() {

        $username = $this->getParam("username");
        $day = $this->getParam("day");

        $day = intval($day) > 0 ? $day : 1;

        $query = \UserTransaction::read();
        $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $to_time = date('Y-m-d 00:00:00');
        $query->andWhere("'$from_time' <= closed_time AND closed_time <= '{$to_time}' ");
        if($username != ""){
            $user = \Users::retrieveByUsername($username);
            if(!$user instanceof \Users){
                print("Not exist User with username : {$username}");
            }
            $query->andWhere("user_id={$user->getId()}");
        }
        $query->orderBy("user_id","ASC");
        $query->orderBy("closed_time,id","ASC");
        $transaction_list = $query->execute()->fetchAll(\PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(false,null));
        $transaction_user = array();
        if(!empty($transaction_list)){
            foreach ($transaction_list as $transaction) {
                if($transaction instanceof \UserTransaction){
                    $transaction_user[$transaction->getUserId()][] = $transaction;
                }
            }
        }else{
            print("Control Transaction Success\n");
        }

        $number_error = 0;
        $array_error["user"] = array();
        foreach ($transaction_user as $transaction) {

            if(count($transaction) > 1){

                foreach ($transaction as $key=>$tran) {
                    if($tran instanceof \UserTransaction){
                        if(!is_numeric($key)){
                            continue;
                        }
                        if(isset($transaction[$key + 1])){

                            $transaction_next = $transaction[$key + 1];
                            try{
                                if($transaction_next instanceof \UserTransaction){

                                    $check_control = \UserTransaction::controlTransaction($tran,$transaction_next);
                                    if(!$check_control){
                                        $this->_warning(
                                            "Transaction no match with user_id : {$tran->getUserId()} Before transaction code is : {$tran->getTransactionCode()} and the".
                                            " after transaction code is : {$transaction_next->getTransactionCode()}",
                                            $tran,
                                            $transaction_next
                                        );
                                        $user = \Users::retrieveById($tran->getUserId());
                                        $array = array(
                                            "user_id"=>$tran->getUserId(),
                                            "user_name"=>$user->getUsername(),
                                            "after_transaction_code"=> $tran->getTransactionCode(),
                                            "before_transaction_code"=> $transaction_next->getTransactionCode()
                                        );
                                        $array_error["user"][] = $array;
                                        $number_error++;
                                        print("Transaction not match  : Before - {$tran->getTransactionCode()} / After {$transaction_next->getTransactionCode()} \n");
                                        continue;
                                    }else{
                                        $this->logger->info("Information Match with transaction : Before - {$tran->getTransactionCode()} / After {$transaction_next->getTransactionCode()}");
                                        print("Information match : {$tran->getTransactionCode()} - {$transaction_next->getTransactionCode()}\n");
                                        continue;
                                    }
                                }
                            }catch (\Exception $e){
                                $number_error++;
                                $this->_warning($e->getMessage(),$tran,$transaction_next);
                                print("Exception : {$e->getMessage()}\n");
                            }
                        }
                    }
                    sleep(1);
                    print "DONE ".$key;
                }
            }else{
                $transaction = isset($transaction[0]) ? $transaction[0] : array();
                if($transaction instanceof \UserTransaction){
                    $this->logger->info("Information Match with transaction code : {$transaction->getTransactionCode()}");
                    print("Information Match {$transaction->getTransactionCode()}\n");
                    continue;
                }
            }
        }

        $count_transaction = count($transaction_list);

        if($number_error > 0){
            $this->logger->warning("Control Transaction Success : control {$count_transaction} transaction with {$number_error} Error : Not Match ",$array_error);
            print("Control Transaction Success\n");
        }
    }

    /**
     * Kiểm tra xem đơn hàng có nhiều hơn 1 giao dịch hoặc đặt cọc, hoặc thanh toán ko.
     */
    public function executeControlOrder(){
        $username = $this->getParam("username");
        $day = $this->getParam("day");

        $day = intval($day) > 0 ? $day : 1;

        $query = \UserTransaction::read();
        $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $to_time = date('Y-m-d 00:00:00');
        $query->andWhere("'$from_time' <= closed_time AND closed_time <= '{$to_time}' ");
        if($username != ""){
            $user = \Users::retrieveByUsername($username);
            if(!$user instanceof \Users){
                print("Not exist User with username : {$username}");
            }
            $query->andWhere("user_id={$user->getId()}");
        }
        $query->orderBy("user_id","ASC");
        $query->orderBy("closed_time,id","ASC");
        $transaction_list = $query->execute()->fetchAll(\PDO::FETCH_CLASS,\UserTransaction::getPhpName(),array(false,null));
        $array_transaction_error = array();
        if(!empty($transaction_list)){
            foreach ($transaction_list as $transaction) {
                if($transaction instanceof \UserTransaction){
                    $order = \Order::retrieveByCode($transaction->getObjectId());
                    if($order instanceof \Order){
                        $array_error = \UserTransaction::checkTransactionOrder($order);
                        if(!empty($array_error)){
                            $user = \Users::retrieveById($order->getBuyerId());
                            $array_transaction_error[$order->getId().$order->getBuyerId()] = $array_error;
                            $array_transaction_error[$order->getId().$order->getBuyerId()]["info"] = array(
                                "user"=>$user->getUsername().' - '.$user->getFullName(),
                                "order_code" => $order->getCode()
                            );
                            print("Order {$order->getCode()} is not ok\n");
                        }else{
                            print("Order {$order->getCode()} is ok\n");
                        }
                    }
                }
                sleep(1);
            }
        }else{
            print("Control Transaction Success\n");
        }

        $this->_sendMail($array_transaction_error);
    }

    /**
     * Đối soát lịch sử giao dịch ở accountant, những giao dịch nào chưa tồn tại ở user transaction được coi là lỗi
     */
    public function executeControlHistory(){
        print("START\n");
        $username = $this->getParam("username");
        $day = $this->getParam("day");

        $day = intval($day) > 0 ? $day : 1;

//?        $from_time =  date('2014-01-01 00:00:00');
        $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $to_time = date('Y-m-d 00:00:00');
        $array_transaction_error = array();
        if($username != ""){
            $user = \Users::retrieveByUsername($username);
            if($user instanceof \Users){

                $array_error = \UserTransaction::controlTransactionHistory($user,$from_time,$to_time);
                if(sizeof($array_error) > 0){
                    $array_transaction_error[$user->getId()] = $array_error;
                }
            }
            print("Control user ".$user->getUsername()." success\n");
        }else{
            $users = \Users::read()->andWhere("status='ACTIVE' AND account_no != '' AND section = 'CUSTOMER'")
                ->execute()->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
            foreach ($users as $user) {
                try{
                    if($user instanceof \Users && $user->getSection() == \Users::SECTION_CUSTOMER ){
                        $array_error = \UserTransaction::controlTransactionHistory($user,$from_time,$to_time);
                        if(sizeof($array_error) > 0){
                            $array_transaction_error[$user->getId()] = $array_error;
                        }
                    }
                    print("Control user ".$user->getUsername()." success\n");
                }catch(\Exception $e){
                    echo $e->getMessage()."\n";
                    continue;
                }

            }
        }
        $result = $this->_sendMailTransactionHistory($array_transaction_error);
        var_dump($result);
    }

    public function _sendMailTransactionHistory($array_error){
        $template = GLOBAL_TEMPLATES_PATH.'/email/TransactionHistory';
        $array_mail = array(
            "chuminhquyen@alimama.vn",
            "nguyenvangiang@alimama.vn"
        );
        $subject = "Thông tin giao dịch lỗi - đối soát từ Transaction history - ".date('H:i:s d/m');
        $params = array(
            'array_transaction_error' => $array_error
        );
        foreach ($array_mail as $email) {
            $sendMail= \MailHelper::mailHelperWithBody($template,$params);
            $sendMail->setReciver($email);
            $sendMail->setSubject($subject);
            $checkSend = $sendMail->sendMail();
            if($checkSend){
                print "send $email success\n";
            }else{
                print "send $email not success\n";
            }
        }


        return true;


    }

    public function _sendMail($array_transaction_error){
        print "Send mail\n";
        $template = GLOBAL_TEMPLATES_PATH.'/email/OrderTransaction';
        $array = array(
            "chuminhquyen@alimama.vn",
            "nguyenvangiang@alimama.vn"
        );
        foreach ($array as $email) {
            $subject = "Thông tin giao dịch trên những đơn hàng có nhiều hơn 1 lần đặt cọc hoặc thanh toán - ".date('H:i:s d/m');
            $params = array(
                'array_transaction_error' => $array_transaction_error
            );
            $sendMail= \MailHelper::mailHelperWithBody($template,$params);
            $sendMail->setReciver($email);
            $sendMail->setSubject($subject);
            $checkSend = $sendMail->sendMail();
            var_dump($checkSend);
            if($checkSend){
                print "send {$email} ok\n";
            }else{
                print "send {$email} not ok\n";
            }
        }

        return false;
    }

    public function _warning($message,\UserTransaction $before_transaction,\UserTransaction $after_transaction){

        $array = array();
        $array["before"] = array(
            "id" => $before_transaction->getId(),
            "amount" => $before_transaction->getAmount(),
            "ending_balance" => $before_transaction->getEndingBalance(),
            "transaction_type" => $before_transaction->getTransactionType(),
            "transaction_code" => $before_transaction->getTransactionCode(),
            "user_id" => $before_transaction->getUserId(),
            "closed_time" => $before_transaction->getClosedTime()
        );
        $array["after"] = array(
            "id" => $after_transaction->getId(),
            "amount" => $after_transaction->getAmount(),
            "ending_balance" => $after_transaction->getEndingBalance(),
            "transaction_type" => $after_transaction->getTransactionType(),
            "transaction_code" => $after_transaction->getTransactionCode(),
            "user_id" => $after_transaction->getUserId(),
            "closed_time" => $after_transaction->getClosedTime()
        );

        $this->logger->info(
            $message,$array
        );
    }
}
