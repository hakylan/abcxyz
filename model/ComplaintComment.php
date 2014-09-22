<?php 
/**
 * ComplaintComment
 * @version		$Id$
 * @package		Model

 */
use SeuDo\MongoDB;

require_once dirname(__FILE__) .'/Base/ComplaintCommentBase.php';
class ComplaintComment extends \ComplaintCommentBase {

    const USER_SYSTEM = 'Sếu đỏ';
    const TYPE_EXTERNAL = 'EXTERNAL';
    const TYPE_INTERNAL = 'INTERNAL';

    public static function getDataCommentsWithComplaintComments($comments=array()) {
        $result = array();

        foreach ($comments as $comment) {
            if ($comment instanceof \mongodb\ComplaintComment) {
                $username = self::USER_SYSTEM;
                $first_name = self::USER_SYSTEM;

                $time = $sub_time = "";
                $date_time = $comment->getCreatedTime();
                if ($date_time instanceof \MongoDate) {
                    $time = date('h:i:s d/m/Y', $date_time->sec);
                    $sub_time = date('H:i d/m', $date_time->sec);
                }
                $message = '';

                if( $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_CHAT
                    || $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_ACTIVITY) {
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

                        $context = $comment->getContext();
                        $message = $context->getMessage();

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
                            'type_context' => $comment->getTypeContext(),
                            'user_system' => self::USER_SYSTEM,
                            'scope' => $comment->getScope(),
                            'is_external' => $comment->getScope() == \mongodb\ComplaintComment::TYPE_EXTERNAL,
                            'is_internal' => $comment->getScope() == \mongodb\ComplaintComment::TYPE_INTERNAL,
                            'is_chat' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_CHAT,
                            'is_log' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_LOG,
                            'is_activity' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_ACTIVITY);
                    }
                }

                if( $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_LOG ) {

                    if( $comment->getContext() ) {
                        $data  = $comment->getContext()->toArray();
                        $message = isset($data['message']) ? $data['message'] : "";
                    }

                    $result[] = array('user_id' => 0,
                        'account' => '',
                        'username' => '',
                        'first_name' => '',
                        'time' => $time,
                        'sub_time' => $sub_time,
                        'message' => $message,
                        'img_path' => '',
                        'order_id' =>$comment->getOrderId(),
                        'is_public_profile' => false,
                        'type_context' => $comment->getTypeContext(),
                        'user_system' => self::USER_SYSTEM,
                        'scope' => $comment->getScope(),
                        'is_external' => $comment->getScope() == \mongodb\ComplaintComment::TYPE_EXTERNAL,
                        'is_internal' => $comment->getScope() == \mongodb\ComplaintComment::TYPE_INTERNAL,
                        'is_chat' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_CHAT,
                        'is_log' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_LOG,
                        'is_activity' => $comment->getTypeContext() == \mongodb\ComplaintCommentResource\BaseContext::TYPE_ACTIVITY);
                }


            }
        }
        return $result;
    }

    public static function loadComplaintComments($complaint_id, $scope, $type_context = '') {
        $comments = \mongodb\ComplaintComment::findByScope($complaint_id, $scope, $type_context);
        $results = self::getDataCommentsWithComplaintComments($comments);
        return $results;
    }

    public static function loadComplaintItemComments($complaint_id, $page=1, $page_size=3, $scope = 'EXTERNAL') {
        $data = \mongodb\ComplaintComment::findPageByComplaintId($complaint_id, $page, $page_size, $scope);
        $results = self::getDataCommentsWithComplaintComments($data['data']);
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

    public static function addComment($user, $order_id, $item_id, $complaint_id, $type, $context, $is_public_profile = true,
                                        $type_context = \mongodb\ComplaintCommentResource\BaseContext::TYPE_CHAT) {
        try {
            // Save data into mongodb
            $user_id = 0;
            if ($user instanceof Users) {
                $user_id = $user->getId();
            } else {
                $user_id = intval($user);
            }

            $created_time = new \MongoDate();
            $complaint_comment = new \mongodb\ComplaintComment();
            $complaint_comment->setCreatedBy($user_id);
            $complaint_comment->setOrderId($order_id);
            $complaint_comment->setItemId($item_id);
            $complaint_comment->setComplaintId($complaint_id);
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