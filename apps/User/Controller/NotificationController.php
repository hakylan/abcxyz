<?php
namespace User\Controller;

use SeuDo\Notification\NotificationUser;
use mongodb\NotificationResource\Notification;
use SeuDo\Main;

class NotificationController extends UserBase
{
    protected $_user;

    public function beforeExecute()
    {
        $auth = \UserAuth::getInstance();
        if ( !$auth->isAuthenticated() || !( $this->_user = $auth->getUser() ) ) {
            $this->redirect( $this->createUrl( 'login', array(
                'url' => base64_encode( $this->request()->getUri() )
            ) ) );
        }

    }

    public function executeDefault()
    {
        $this->setView( 'User/notification' );
        $page = $this->request()->post( 'page', 'INT', 1 );
        $is_ajax = $this->request()->post( 'page', 'INT', 0 );
        $limit = 10;
        $total_notify = Notification::getTotalNotifyByUser( $this->_user->id );
        $total_page = ceil( $total_notify / $limit );
        $data_notify = Notification::readNotificationByUser( $this->_user->id, $page, $limit );
        $notifications = array();
        if ( $data_notify ) {
            $array_notify = $this->getNotification( $data_notify );
            $key_date = '';
            $tmp = array();
            foreach ( $array_notify as $notify ) {
                if ( $key_date != $notify[ 'date_time' ] ) {
                    if ( $key_date !== '' ) {
                        $notifications[ ] = $tmp;
                        $tmp = array();
                    }
                    $key_date = $notify[ 'date_time' ];
                    $tmp[ 'date_time' ] = $notify[ 'date_time' ];
                    $tmp[ 'data' ][ ] = $notify;
                } else {
                    $tmp[ 'data' ][ ] = $notify;
                }
            }

            if(count($tmp)>0){
                $notifications[ ] = $tmp;
            }
        }
        if ( $page > 5 ) {
            $begin_page = $page - $page % 5;
        } else {
            $begin_page = 1;
        }
        if ( $total_page < $begin_page + 5 ) {
            $end_page = $total_page + 1;
        } else {
            $end_page = $begin_page + 5;
        }
        $array_paging = array();
        for ( $i = $begin_page; $i < $end_page; $i++ ) {
            if ( $page == $i ) {
                $array_paging[ ] = array( 'page' => $i, 'is_current_page' => 1 );
            } else {
                $array_paging[ ] = array( 'page' => $i, 'is_current_page' => 0 );
            }
        }
        if ( $is_ajax ) {
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->notifications = $notifications;
            $ajax->array_paging = $array_paging;
            $ajax->page = $page;
            $ajax->pre_page = $page > 1 ? 1 : 0;
            $ajax->next_page = $page < $total_page ? 1 : 0;
            $ajax->total_page = $total_page;
            return $this->renderText( $ajax->toString() );
        } else {
            $this->view()->assign(
                array(
                    'notifications' => $notifications,
                    'array_paging' => $array_paging,
                    'current_page' => $page,
                    'total_page' => $total_page
                )
            );
            return $this->renderComponent();
        }

    }

    public function executeAjaxGetNotify()
    {
        //reset total notify
        Notification::resetNewTotalNotificationByUser( $this->_user->id );
        $ajax = new \AjaxResponse();
        $page = $this->request()->post( 'page', 'INT', 1 );
        $limit = 10;
        $total_notify = Notification::getTotalNotifyByUser( $this->_user->id );
        if ( ceil( $total_notify / $limit ) > $page ) {
            $load_more = true;
        } else {
            $load_more = false;
        }
        $data_notify = Notification::readNotificationByUser( $this->_user->id, $page, $limit );
        if ( $data_notify ) {
            $array_notify = $this->getNotification( $data_notify );
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->notifications = $array_notify;
            $ajax->load_more = $load_more;
            return $this->renderText( $ajax->toString() );
        } else {
            $ajax->type = \AjaxResponse::WARNING;
            if ( $page > 1 ) {
                $ajax->message = '';
            } else {
                $ajax->message = 'Hiện chưa có thông báo nào.';
            }
            return $this->renderText( $ajax->toString() );
        }

    }

    public function getNotification( $data_notify )
    {

        $array_notify = array();

        foreach ( $data_notify as $data ) {
            $tmp = array();

            $order = \Order::retrieveById( intval( $data[ 'order_id' ] ) );
            if ( $order ) {
                $notify_id = $data[ '_id' ]->{'$id'};
                $tmp[ 'order_avatar' ] = $order->getOrderAvatar();
                $tmp[ 'type' ] = $data[ 'type' ];
                switch ( $tmp[ 'type' ] ) {
                    case NotificationUser::TYPE_NOTIFY_ORDER_STATUS:
                        $tmp[ 'order_name' ] = $data[ 'order_name' ];
                        $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                        switch ( $data[ 'order_status' ] ) {
                            case 'SELLER_DELIVERY':
                                $tmp[ 'order_status' ] = 'người bán đã giao hàng';
                                break;
                            case 'RECEIVED_FROM_SELLER':
                                $tmp[ 'order_status' ] = 'đã nhân hàng từ người bán';
                                break;
                            case 'CHECKED':
                                $tmp[ 'order_status' ] = 'đã được kiểm hàng';
                                break;
                            case 'BOUGHT':
                                $tmp[ 'order_status' ] = 'đã được mua';
                                break;
                            case 'WAITING_DELIVERY':
                                $tmp[ 'order_status' ] = 'đã có thể giao hàng cho bạn';
                                if ( isset( $data[ 'is_express_cn_vn' ] ) ) {
                                    if ( $data[ 'is_express_cn_vn' ] == 1 ) {
                                        $tmp[ 'order_status' ] = 'CPN đã về kho phân phối, chúng tôi đang tiến hàng giao hàng cho quý khách';
                                    }
                                }
                                break;
                            case 'CONFIRM_DELIVERY':
                                $tmp[ 'order_status' ] = 'chuẩn bị được giao hàng cho bạn';
                                break;
                            case 'DELIVERING':
                                $tmp[ 'order_status' ] = 'đang trên đường giao cho bạn';
                                break;
                            default:
                                $tmp[ 'order_status' ] = '';
                                break;
                        }

                        $tmp[ 'created_time' ] = \Common::formatTimeNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'date_time' ] = \Common::formatDateNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'is_new' ] = $data[ 'is_new' ];
                        break;
                    case NotificationUser::TYPE_NOTIFY_CONFIRM_ORDER:
                        $tmp[ 'order_name' ] = $data[ 'order_name' ];
                        $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                        switch ( $data[ 'type_confirm' ] ) {
                            case 'price':
                                $tmp[ 'confirm_msg' ] = 'đang chờ được xác nhận về giá';
                                break;
                            case 'quantity':
                                $tmp[ 'confirm_msg' ] = 'đang chờ được xác nhận về số lượng';
                                break;
                        }

                        $tmp[ 'created_time' ] = \Common::formatTimeNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'date_time' ] = \Common::formatDateNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'is_new' ] = $data[ 'is_new' ];
                        break;
                    case NotificationUser::TYPE_NOTIFY_CHAT_ORDER:
                        $tmp[ 'order_name' ] = $data[ 'order_name' ];
                        $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                        $tmp[ 'is_log' ] = false;
                        switch ( $data[ 'type_chat' ] ) {
                            case 'human':
                                if ( isset( $data[ 'total_message' ] ) && intval( $data[ 'total_message' ] ) > 1 ) {
                                    $tmp[ 'confirm_msg' ] = 'Sếu Đỏ gửi cho bạn ' . intval( $data[ 'total_message' ] ) . ' tin nhắn ở đơn hàng ';
                                } else {
                                    $tmp[ 'confirm_msg' ] = 'Sếu Đỏ gửi cho bạn tin nhắn ở đơn hàng ';
                                }

                                break;
                            case 'activity':
                                $tmp[ 'confirm_msg' ] = $data[ 'message_content' ] . ' ở đơn hàng ';
                                break;
                            case 'log':
                                $tmp[ 'is_log' ] = true;
                                $tmp[ 'confirm_msg' ] = $data[ 'message_content' ];
                                break;
                        }

                        $tmp[ 'created_time' ] = \Common::formatTimeNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'date_time' ] = \Common::formatDateNotification( $data[ 'created_time' ]->sec );
                        $tmp[ 'is_new' ] = $data[ 'is_new' ];
                        break;
                }

                $array_notify[ ] = $tmp;
            }
        }

        return $array_notify;

    }

}