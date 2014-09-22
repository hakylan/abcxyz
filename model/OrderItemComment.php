<?php 
/**
 * OrderItemComment
 * @version		$Id$
 * @package		Model

 */
use Flywheel\Db\Query;
use \mongodb\OrderCommentResource\BaseContext;

require_once dirname(__FILE__) .'/Base/OrderItemCommentBase.php';
class OrderItemComment extends \OrderItemCommentBase {
    const TYPE_INTERNAL = 'INTERNAL';
    const TYPE_EXTERNAL = 'EXTERNAL';

//    public static function addComment($data = array(), \Users $user) {
//        try {
//            $itemComment = new OrderItemComment();
//
//            $itemComment->setNew(true);
//            $itemComment->setContent($data['content']);
//            $itemComment->setCreatedBy($user->getId());
//            $itemComment->setCreatedTime(new \Flywheel\Db\Type\DateTime());
//            $itemComment->setOrderId($data['order_id']);
//            $itemComment->setOrderItemId($data['order_item_id']);
//            $itemComment->setType(OrderItemComment::TYPE_INTERNAL);
//
//            $result = $itemComment->save();
//
//            if($result && $result == true) {
//                return $itemComment;
//            }
//
//            return $itemComment->getValidationFailuresMessage();
//
//        }catch (\Flywheel\Model\Exception $e){}
//        return false;
//    }
//
//    public static function listComment($itemId, Query $query = null) {
//        if(!$query) {
//            $query = OrderItemComment::read();
//
//        }
//        $query->andWhere('item_id='.$itemId);
//        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, \OrderItemComment::getPhpName(), array(null, false));
//    }

    public static function addComment($user_id, $order_id, $item_id, $message, $created_time, $type = '') {
        try {
            // Save data into mongodb
            $order_comment = new \mongodb\OrderItemComment();
            $order_comment->setCreatedBy($user_id);
            $order_comment->setOrderId($order_id);
            $order_comment->setItemId($item_id);
            $order_comment->setMessage($message);
            $order_comment->setType($type);

            $order_comment->setCreatedTime($created_time);

            return $order_comment->save();
        } catch (\Flywheel\Exception $e) {
            return false;
        }
    }

    /*
     * return follow page in item commment
     */
    public static function loadOrderItemComments($order_id, $item_id, $page=1, $page_size=3, $type = '') {
        $data = \mongodb\OrderItemComment::findPageByItemId($order_id, $item_id, $page, $page_size, $type);
        $results = self::getDataCommentsWithOrderItemComments($data['data']);
        $total_record = isset($data['total_record']) ? $data['total_record'] : 0;
        return array('page' => $data['page'], 'pages' => $data['pages'], 'data' => $results, 'total_record' => $total_record);
    }

    /*
     * get infor from object display HTML
     */
    public static function getDataCommentsWithOrderItemComments($comments=array()) {
        $results = array();
        foreach ($comments as $comment) {
            if ($comment instanceof \mongodb\OrderItemComment) {

                $username = $user_avatar = '';
                $user_id = $comment->getCreatedBy();
                $user = \Users::findOneById($user_id);
                $shorten_fullname = "";
                if ($user instanceof \Users) {
                    $username = $user->getFullName();
                    $user_avatar = $user->getAvatar32x($user);
                    $shorten_fullname = $user->getShortenFullName();
                }
                $time = "";
                $date_time = $comment->getCreatedTime();
                if ($date_time instanceof \MongoDate) {
                    $time = date('h:i:s d/m/Y', $date_time->sec);
                    $short_time = date('h:i d/m', $date_time->sec);
                }
                $message = $comment->getMessage();
                $is_activity = $comment->getType() == BaseContext::TYPE_ACTIVITY ? true : false;
                $is_chat = $comment->getType() == BaseContext::TYPE_CHAT ? true : false;
                $is_log = $comment->getType() == BaseContext::TYPE_LOG ? true : false;
                $results[] = array('user_id' => $user_id,
                                    'username' => $username,
                                    'user_avatar' => $user_avatar,
                                    'time' => $time,
                                    'shorten_fullname' => $shorten_fullname,
                                    'short_time' => $short_time,
                                    'message' => $message,
                                    'is_activity' => $is_activity,
                                    'is_log' => $is_log,
                                    'is_chat' => $is_chat,
                );
            }
        }
        return $results;
    }
}