<?php
namespace Background\Task;
use Background\Library\EmailHelper;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use SeuDo\Logger;


class Email extends BackgroundBase {
    protected $limitExecuteTime = 20;
    protected $limit = 100;

    /*
     * @todo push email verify from queue
     * */

    public function executePushVerify() {

        $queue = \SeuDo\Queue::emailVerify();

        $logger = Logger::factory("email_log");

        try {
            do {
                $current = $queue->pop();
                if (!$current) {
                    break;
                }

                $data = json_decode($current, true);

                if(!$data || $data == '') continue;

                $valid = EmailHelper::valid($data);

                if($valid == false) {
                    echo 'ERROR : '.$data['email'].' is invalid \n!'; continue;
                }

                $result = EmailHelper::sendVerifyEmail($data);

                if($result == false){
                    $logger->info('ERROR : '.$data['email'].' is invalid');
                    echo 'ERROR : '.$data['email'].' is invalid \n!';
                }else{
                    $logger->info('Send mail to : '.$data['email'].' Success');
                    echo('SUCCESS: '.$result ." is sent \n");
                }

                usleep(200);

            } while($current); //not need check time

        }catch (Exception $e) {
            $logger->info('Send Email Error : '.$e->getMessage());
        }
    }

    public function executePushError() {
        $queue = \SeuDo\Queue::emailError();
        static $sent = [];

        try {
            do {
                $current = $queue->pop();
                if(!$current) {
                    break;
                }
                if (isset($sent[md5($current)])) {
                    continue;
                }

                $data = json_decode($current, true);
                /* LT. Hieu: we all know that this not need
                 * $valid = EmailHelper::valid($data);

                if($valid == false) {
                    echo 'ERROR : '.$data['email'].' is invalid \n!'; continue;
                }*/

                $result = EmailHelper::sendEmailError($data);
                $sent[md5($current)] = true;

                if($result == false){
                    print_r("ERROR : " .$data['email'] ." is invalid!\n");
                }else{
                    print_r("SUCCESS: {$result} is sent \n");
                }

                sleep(1);

            } while ($current);

        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function executePushNotification(){

        $queue = \SeuDo\Queue::emailNotification();
        try {
            do {
                $current = $queue->pop();
                if(!$current) {
                    break;
                }

                $data = json_decode($current, true);


                $valid = EmailHelper::valid($data);

                if($valid == false) {
                    Logger::factory("test_email")->info("Email error",$data['email']);
                    echo 'ERROR : is invalid \n!'; continue;
                }
                $result = EmailHelper::sendEmail($data);

                if($result == false){
                    Logger::factory("test_email")->info("Email error",$data['email']);
                    echo 'ERROR : is invalid \n!';
                }else{
                    echo('SUCCESS: '.$result ." is sent \n");
                }

                sleep(1);

            } while ($current);

        } catch (Exception $e) {}
    }
}
