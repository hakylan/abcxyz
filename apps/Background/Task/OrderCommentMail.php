<?php
namespace Background\Task;
use Background\Library\EmailHelper;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use SeuDo\Logger;
use SeuDo\Queue;
use mongodb\OrderCommentResource\BaseContext;

class OrderCommentMail extends BackgroundBase {
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
     * Control Transaction
     */
    public function executeSendMail(){

        $type = $this->getParam("type");
        $day = $this->getParam("day");

        $type = $type != "" ? $type : "hour";
        $day = $day != "" ? $day : 1;

        $created_time = new \MongoDate(strtotime("-1 hour"));

        print "Start \n";

        $condition  =  array('created_time' => array('$gt'=>$created_time),"scope"=>\mongodb\OrderComment::TYPE_EXTERNAL,"type_context"=>BaseContext::TYPE_CHAT);// "created_time > '{$created_time}'";// array("created_time",$created_time);//"created_time=='{$created_time}'";
        $order_comment_list = \mongodb\OrderComment::findByCondition($condition);
        $order_comment_list = \OrderComment::getDataCommentsWithOrderComments($order_comment_list);

        $order_comment = array();
        foreach ($order_comment_list as $comment) {
            $order_comment[$comment["order_id"]][] = $comment;
        }
        $template = GLOBAL_TEMPLATES_PATH.'/email/MailOrderComment';
        $subject = 'Danh sách comment đơn hàng thời gian :'.date("H:i:s d-m-Y",strtotime("-1 hour")).' đến '.date("H:i:s d-m-Y");
        $email =  array('xinchao@alimama.vn');
//        $subject = 'Danh sách comment đơn hàng thời gian :'.date("d-m-Y H:i:s",strtotime("-2 hour")).' đến '.date("d-m-Y H:i:s");
        if(!empty($order_comment)){
            print "Start send";
            $params = array(
                'order_comment' => $order_comment,
            );
            foreach ($email as $to) {
                $sendMail= \MailHelper::mailHelperWithBody($template,$params);
                $sendMail->setReciver($to);
                $sendMail->setSubject($subject);
                $checkSend = $sendMail->sendMail();
                if($checkSend){
                    print "Da gui {$to}\n";
                }else{
                    print "{$to} Miss roi\n"; exit;
                }
            }
        }
        print "END\n";
    }

    public function _getListComment($condition = array()){
        $order_comment_list = \mongodb\OrderComment::findByCondition($condition);
        return $order_comment_list;
    }
}
