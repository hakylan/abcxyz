<?php 
/**
 * ComplaintSellerComment
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/ComplaintSellerCommentBase.php';
class ComplaintSellerComment extends \ComplaintSellerCommentBase {
    const USER_SYSTEM = 'Sếu đỏ';
    const TYPE_EXTERNAL = 'EXTERNAL';
    const TYPE_INTERNAL = 'INTERNAL';

    public static function getDataCommentsWithComplaintSellerComments($comments=array()) {
        $result = array();
        foreach ($comments as $comment) {
            if ($comment instanceof \mongodb\ComplaintSellerComment) {
                $username = self::USER_SYSTEM;
                $first_name = self::USER_SYSTEM;
                $user = Users::retrieveById($comment->getCreatedBy());
                if ($user instanceof \Users) {
                    $img_path = Users::getAvatar32x($user);
                    $user_id = $comment->getCreatedBy();
                    if ($comment->getIsPublicProfile() || $comment->getScope() == 'INTERNAL') {
                        $user = \Users::findOneById($comment->getCreatedBy());
                        if ($user instanceof \Users) {
                            $username = $user->getFullName();
                            $first_name = $user->getFirstName();
                        }
                    }

                    $time = $sub_time = "";
                    $date_time = $comment->getCreatedTime();
                    if ($date_time instanceof \MongoDate) {
                        $time = date('h:i:s d/m/Y', $date_time->sec);
                        $sub_time = date('H:i d/m', $date_time->sec);
                    }
                    $message = '';
                    $context = $comment->getContext();
                    $message = $context->getMessage();
                    $type_context = $comment->getTypeContext();
                    $result[] = array('user_id' => $user_id,
                        'account' =>$user->getUsername() ,
                        'username' => $username,
                        'first_name' => $first_name,
                        'time' => $time,
                        'sub_time' => $sub_time,
                        'message' => $message,
                        'img_path' => $img_path,
                        'order_id' =>$comment->getOrderId(),
                        'is_public_profile' => $comment->getIsPublicProfile(),
                        'type_context' => $type_context,
                        'user_system' => self::USER_SYSTEM,
                        'scope' => $comment->getScope(),
                        'is_external' => $comment->getScope() == 'EXTERNAL' ? true : false,
                        'is_internal' => $comment->getScope() == 'INTERNAL' ? true : false,
                        'is_chat' => ($type_context == 'CHAT') ? true : false,
                        'is_log' => ($type_context == 'LOG') ? true : false,
                        'is_activity' => ($type_context == 'ACTIVITY') ? true : false);
                }
            }
        }
        return $result;
    }

    public static function loadComplaintSellerComments($complaint_sellder_id, $scope, $type_context = '') {
        $comments = \mongodb\ComplaintSellerComment::findByScope($complaint_sellder_id, $scope, $type_context);
//        print_r($comments);
        $results = self::getDataCommentsWithComplaintSellerComments($comments);
        return $results;
    }

    public static function loadComplaintSellerItemComments($complaint_sellder_id, $page=1, $page_size=3, $scope = 'EXTERNAL') {
        $data = \mongodb\ComplaintSellerComment::findPageByComplaintSellerId($complaint_sellder_id, $page, $page_size, $scope);
        $results = self::getDataCommentsWithComplaintSellerComments($data['data']);
        return array('page' => $data['page'], 'pages' => $data['pages'], 'data' => $results);
    }

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
//            if (preg_match('/http/i', $str)) {
//                $str = preg_replace('!(http)(s)?:\/\/[a-zA-Z0-9.?&_/\-\+]+!', "<a href=\"\\0\">\\0</a>", $str);
//            }
            return $str;
        } catch (Exception $e) {
            return '';
        }
    }

    public static function addComment($user, $order_id, $item_id, $complaint_seller_id, $type, $context, $is_public_profile,
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
            $complaint_comment = new \mongodb\ComplaintSellerComment();
            $complaint_comment->setCreatedBy($user_id);
            $complaint_comment->setOrderId($order_id);
            $complaint_comment->setItemId($item_id);
            $complaint_comment->setComplaintSellerId($complaint_seller_id);
            $complaint_comment->setScope($type);
            $complaint_comment->setContext($context);
            $complaint_comment->setIsPublicProfile($is_public_profile);
            $complaint_comment->setTypeContext($type_context);
            $complaint_comment->setCreatedTime($created_time);

            return $complaint_comment->save();
        } catch (\Flywheel\Exception $e) {
            return false;
        }
    }
}