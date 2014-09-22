<?php 
/**
 * OrderComment
 * @version		$Id$
 * @package		Model

 */
use SeuDo\MongoDB;

require_once dirname(__FILE__) .'/Base/OrderCommentBase.php';
class OrderComment extends \OrderCommentBase {
    const USER_SYSTEM = 'Sếu đỏ';
    const TYPE_EXTERNAL = 'EXTERNAL';
    const TYPE_INTERNAL = 'INTERNAL';

    /*
     * dau's
     * return array infor display
     */
    public static function getDataCommentsWithOrderComments($comments=array()) {
        $result = array();

        foreach ($comments as $comment) {
            if ($comment instanceof \mongodb\OrderComment) {

                $username = self::USER_SYSTEM;
                $first_name = self::USER_SYSTEM;
                $user = Users::retrieveById($comment->getCreatedBy());
                $shorten_fullname = $message = $time = $_time = $sub_time = "";
                $date_time = $comment->getCreatedTime();
                if ($date_time instanceof \MongoDate) {
                    $time = date('h:i:s d/m/Y', $date_time->sec);
                    $sub_time = date('Y-m-d H:i:s', $date_time->sec);
                    $_time = date('Y-m-d H:i:s', $date_time->sec);
                }

                if ($user instanceof \Users) {
                    $img_path = Users::getAvatar32x($user);
                    $user_id = $comment->getCreatedBy();
                    if ($comment->getIsPublicProfile() || $comment->getScope() == 'INTERNAL') {
                        $user = \Users::findOneById($comment->getCreatedBy());
                        if ($user instanceof \Users) {
                            $username = $user->getFullName();
                            $first_name = $user->getFirstName();
                            $shorten_fullname = $user->getShortenFullName();
                        }
                    }

                    $context = $comment->getContext();
                    $message = $context->getMessage();
                    $type_context = $comment->getTypeContext();

                    $result[] = array('user_id' => $user_id,
                        'account' => $comment->getTypeContext() == 'LOG' ? '' : $user->getUsername() ,
                        'username' => $username,
                        'first_name' => $first_name,
                        'time' => $time,
                        'sub_time' => $sub_time,
                        '_time' => $_time,
                        'shorten_fullname' => $shorten_fullname,
                        'message' => $message,
                        'img_path' => $img_path,
                        'order_id' =>$comment->getOrderId(),
                        'is_public_profile' => $comment->getIsPublicProfile(),
                        'type_context' => $comment->getTypeContext(),
                        'user_system' => self::USER_SYSTEM,
                        'scope' => $comment->getScope(),
                        'is_external' => $comment->getScope() == 'EXTERNAL',
                        'is_internal' => $comment->getScope() == 'INTERNAL',
                        'is_customer' => $comment->getTypeContext() == 'LOG' ? false : $user->getSection() == \Users::SECTION_CUSTOMER,
                        'is_chat' => $comment->getTypeContext() == 'CHAT',
                        'is_log' => false,
                        'is_activity' => $comment->getTypeContext() == 'ACTIVITY');
                }else{
                    //This is log
//                    print_r($comment->getContext()->toArray());
                    if( $comment->getContext() ) {
                        $data  = $comment->getContext()->toArray();
                        $message = isset($data['message']) ? $data['message'] : "";
                    }

                    $result[] = array('user_id' => 0,
                        'account' => '',
                        'username' => $username,
                        'first_name' => $first_name,
                        'time' => $time,
                        'sub_time' => $sub_time,
                        '_time' => $_time,
                        'shorten_fullname' => $shorten_fullname,
                        'message' => $message,
                        'img_path' => '',
                        'order_id' =>$comment->getOrderId(),
                        'is_public_profile' => $comment->getIsPublicProfile(),
                        'type_context' => $comment->getTypeContext(),
                        'user_system' => self::USER_SYSTEM,
                        'scope' => $comment->getScope(),
                        'is_external' => $comment->getScope() == 'EXTERNAL',
                        'is_internal' => $comment->getScope() == 'INTERNAL',
                        'is_customer' => false,
                        'is_chat' => false,
                        'is_activity' => false,
                        'is_log' => true
                    );
                }

            }
        }
        return $result;
    }

    /*
     * dau's
     */
    public static function loadOrderComments($order_id, $scope, $type_context = '') {
        $comments = \mongodb\OrderComment::findByScope($order_id, $scope, $type_context);
        $results = self::getDataCommentsWithOrderComments($comments);
        return $results;
    }

    /*
     * dau's
     */
    public static function addComment($user, $order_id, $type, $context, $is_public_profile,
                                      $type_context) {
        try {
            // Save data into mongodb
            $user_id = 0;
            if ($user instanceof Users) {
                $user_id = $user->getId();
            } else {
                $user_id = intval($user);
            }
            $created_time = new \MongoDate();
            $order_comment = new \mongodb\OrderComment();
            $order_comment->setCreatedBy($user_id);
            $order_comment->setOrderId($order_id);
            $order_comment->setScope($type);
            $order_comment->setContext($context);
            $order_comment->setIsPublicProfile($is_public_profile);
            $order_comment->setTypeContext($type_context);
            $order_comment->setCreatedTime($created_time);

            return $order_comment->save();
        } catch (\Flywheel\Exception $e) {
            return false;
        }
    }

    /*
     * dau's
     * return string
     */
    public static function convertToText($str) {
        try {
            // Remove content script, style in string
            $str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
            $str = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $str);

            // Remove all html without tag a
            $str = strip_tags($str, '<a>');
            // Remove all attributes tag a without href
            $pattern = '/<a\s+href="([^"]+)"[^>]+>/i';
            $replacement = '/<a href="([^"]+)">/i';
            $str = preg_replace($pattern, $replacement, $str);
            // Parse word start with http => tag a
            if (preg_match('/http/i', $str)) {
                $str = preg_replace('!(http)(s)?:\/\/[a-zA-Z0-9.?&_/\-\+]+!', "<a href=\"\\0\">\\0</a>", $str);
            }
            return $str;
        } catch (Exception $e) {
            return '';
        }
    }
}