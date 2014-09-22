<?php
namespace Background\Task;
use SeuDo\MongoDB;
use SeuDo\Logger;

class Logging extends BackgroundBase {

    private $account_collection = 'accountant';

    public function executeRemoveAccountant(){
        print "Start\n";
        $db = MongoDB::getConnection(MongoDB::CONFIG_KEY_LOGGING);
        $month = date("m",strtotime("-2 month"));

        print_r($month . "\n");
//        $logger = Logger::factory("")

        $condition =  array('datetime' => array('$regex'=> "2014-{$month}-*"));

        $db->where($condition)->deleteAll($this->account_collection);
        print "end\n";
    }
}
