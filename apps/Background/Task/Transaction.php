<?php
namespace Background\Task;
use Background\Library\EmailHelper;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use Flywheel\Queue\Queue;
use SeuDo\Logger;


class Transaction extends BackgroundBase {
    protected $limitExecuteTime = 20;
    protected $limit = 100;

    /**
     * @var \SeuDo\Logger::factory("transaction")
     */
    protected $logger = null;


    public function beforeExecute(){
        $this->logger = Logger::factory("sys_transaction");
    }

    public function executeSys(){

        $username = $this->getParam("username");
        $day = $this->getParam("day");

        $day = intval($day) > 0 ? $day : 1;
        $from_time =  date('Y-m-d 00:00:00', strtotime("-{$day} days"));
        $to_time = date('Y-m-d 00:00:00');

        if($username != ""){
            print "SYS transaction with user: {$username}\n";
            $user_list = \Users::read()->andWhere("username = '{$username}'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
        }else{
            print "SYS transaction with day: {$day}\n";
            $query = \Users::read();

            $query->andWhere("'$from_time' <= joined_time AND joined_time <= '{$to_time}' ");
            $user_list = $query->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
        }

        foreach ($user_list as $user) {
            if($user instanceof \Users){
                try{
                    print "user : {$user->getUsername()} \n";
                    \UserTransaction::syncTransactionHistory($user,$from_time,date("Y-m-d H:i:s"));
                }catch(\Exception $e){
                    echo $e->getMessage();
                    continue;
                }
            }
        }
    }
}
