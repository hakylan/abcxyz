<?php
namespace Background\Task;
use Flywheel\Exception;
use SeuDo\Logistic;
use SeuDo\Queue;
use SeuDo\Logger;

class TransferOrder extends BackgroundBase {

    private $logger = null;
    public function __construct(){
        $this->logger = Logger::factory("transfer_order");
    }

    public function executeTransferLogistic(){

        $key = $this->getParam('key');
        if (null == $key) {
            $key = Queue::TRANSFER_ORDER_LOGISTIC;
        }

        $nextQueue = null;
        if ($key == Queue::TRANSFER_ORDER_LOGISTIC) {
            $nextQueue = Queue::TRANSFER_ORDER_LOGISTIC_3M;
        } else if ($key == Queue::TRANSFER_ORDER_LOGISTIC_3M) {
            $nextQueue = Queue::TRANSFER_ORDER_LOGISTIC_15M;
        } else {
            $nextQueue = false;
        }

        $queue = Queue::getQueue($key);
        try{
            do {
                $order_data = $queue->pop();
                if(!$order_data) {
                    print "Not exist order data";
                    break;
                }
                print "\n";

                var_dump($order_data) ;

                $result = Logistic\OrderTransfer::postOrderInfo($order_data);

                var_dump($result);

                if(!is_array($result)){
                    $result = json_decode($result,true);
                }

                if(is_array($result) && $result["code"] == "200"){
                    $this->logger->info("Send to api Logistic success with key : {$key} - ".date("H:i d-m-Y")." ",array($order_data));
                    print "OK {$result["code"]}";
                }else{
                    if($nextQueue){
                        $nextQueue = Queue::getQueue($nextQueue);
                        if(is_array($order_data)){
                            $order_data = json_encode($order_data);
                        }
                        $nextQueue->push($order_data);
                    }
                    $this->logger->info("Send to api Logistic Not success with key : {$key} - ".date("H:i d-m-Y")." ",array($order_data));
                    print "Not OK \n";
                }

                sleep(3);

            } while ($order_data);
            echo "END\n";
        }catch (\Exception $e){
            $this->logger->warning("Send to api Logistic Not success with key : {$key} - ".date("H:i d-m-Y")." - {$e->getMessage()}");
            throw $e;
        }
    }
//
//    public function executeBackupTransfer(){
//        echo "Start\n";
//        $queue = Queue::backupTransferOrderLogistic();
//        try{
//            do {
//                echo "Start Do\n";
//                $order_data = $queue->pop();
//                if(!$order_data) {
//                    print "Not exist order data";
//                    break;
//                }
//
//                $result = Logistic\OrderTransfer::postOrderInfo($order_data);
//
//                if(!is_array($result)){
//                    $result = json_decode($result,true);
//                }
//
//                if(is_array($result) && $result["code"] == "200"){
//                    print "OK {$result["code"]}";
//                }else{
//                    \OrderUtil::pushBackupTransferOrderLogistic($order_data);
//                    print "Not OK \n";
//                }
//
//                sleep(3);
//
//            } while ($order_data);
//            echo "END\n";
//        }catch (\Exception $e){
//            throw $e;
//        }
//    }
}
