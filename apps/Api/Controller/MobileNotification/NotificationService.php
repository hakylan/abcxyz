<?php
/*
 * Nguyen Xuan Kien
 * Notification API Services
 */
namespace Api\Controller\MobileNotification;

use FlyApi\Exception;
use mongodb\NotificationResource\Notification;
use SeuDo\Logger;
use Seudo\Main;
use SeuDo\Notification\NotificationUser;
use CartShop;

class NotificationService extends Base{

    /* This section is for test only, do not uncomment this */

    public function getPlus()
    {
        $apiKey = "AIzaSyBZZdzBevd2zBc19uaVbkP2oZY8eB2NaW4";
        $url = 'https://android.googleapis.com/gcm/send';
        $ids = array();
        $body = 'testing messages';
        $message = array(
            'message' => $body,
            'title' => 'Message from seudo.vn'
        );

        $to = \Users::retrieveById(1);
        if ($to instanceof \Users)
        {
            $devices = \ClientApi::findByUserId($to->getId());
            foreach ($devices as $device)
            {
                if ($device instanceof \ClientApi)
                {
                    $ids[] = $device->getGcmRegId();
                }
            }
        }
        else
        {
            return false;
        }
        //------------------------------
        // Set GCM post variables
        // (Device IDs and push payload)
        //------------------------------

        $post = array(
            'registration_ids'  => $ids,
            'data'              => $message,
        );

        //------------------------------
        // Set CURL request headers
        // (Authentication and type)
        //------------------------------

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        //------------------------------
        // Initialize curl handle
        //------------------------------

        $ch = curl_init();

        //------------------------------
        // Set URL to GCM endpoint
        //------------------------------

        curl_setopt( $ch, CURLOPT_URL, $url );

        //------------------------------
        // Set request method to POST
        //------------------------------

        curl_setopt( $ch, CURLOPT_POST, true );

        //------------------------------
        // Set our custom headers
        //------------------------------

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        //------------------------------
        // Get the response back as
        // string instead of printing it
        //------------------------------

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        //------------------------------
        // Set post data as JSON
        //------------------------------

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

        //------------------------------
        // Actually send the push!
        //------------------------------

        $result = curl_exec( $ch );

        //------------------------------
        // Error? Display it!
        //------------------------------

        if ( curl_errno( $ch ) )
        {
            Logger::factory('gcm_notification')->warn('Could not send notification to Google Cloud Message!', curl_error( $ch ) , $to, $body);
            //echo 'GCM error: ' . curl_error( $ch );
        }

        //------------------------------
        // Close curl handle
        //------------------------------

        curl_close( $ch );

        //------------------------------
        // Debug GCM response
        //------------------------------

        Logger::factory('gcm')->addNotice('gcm send with result: '. $result);
        //echo $result;
        return true;
    }



    const WEB_PATH = "http://static.seudo.vn/thumb/resize/w_256/";
    const UNKNOWN_ERROR = 0;
    const USERNAME_OR_PASSWORD_MISMATCH = 1;
    const GRANT_TYPE_MISMATCH = 2;

    /**
     * Authorize client, require grant_type, scope, client_id, client_secret, (username, password if grant type is
     * resource owner password
     * 1. Get client id and client secret from request, then make sure that client id and client secret is valid
     * 2. Check grant types (only support 'password')
     * 3. Check scope (not implemented yet)
     * 4. Authenticate user by username & password client provided
     * TODO: Save refresh_token and access_token
     * 5. Generate refresh token & access token, return as Json
     * @return string
     */
    public function postAuthorize()
    {
        define('UNKNOWN_ERROR', 0);
        define('USERNAME_OR_PASSWORD_MISMATCH', 1);
        define('GRANT_TYPE_MISMATCH', 2);

        //$result['error'] = 'password invalid';
        //$result['error_code'] = USERNAME_OR_PASSWORD_MISMATCH;
        //return $result;

        //$this->_verifyRequest(); // client_id & client_secret handle by consumer_id, consumer_secret instead
        $consumer_id = $this->post('client_id');
        $consumer_secret = $this->post('client_secret');

        $result['error'] ='';

        //verify consumer id and secret
        $consumer = \Consumer::retrieveById($consumer_id);
        if (!($consumer instanceof \Consumer) || $consumer_secret != $consumer->getConsumerSecret())
        {
            $result['error'] = 'client is not valid';
            return json_encode($result);
        }

        // default only accept password grant type
        switch($this->post('grant_type'))
        {
            case 'password':
                break;
            default:
                $result['error_code'] = self::GRANT_TYPE_MISMATCH;
                $result['error'] = 'api call must specify grant type';
                return json_encode($result);
        }

        //TODO: implement scope, now full trust
        $scope = $this->post('scope');

        try
        {
            $credential = $this->post('username');
            $password = $this->post('password');

            if (!isset($credential))
            {
                $result['error'] = 'username not found';
                $result['error_code'] = self::USERNAME_OR_PASSWORD_MISMATCH;
                return json_encode($result);
            }

            if (strpos($credential, '@') !== false) {
                $user = \Users::retrieveByEmail($credential);
            } else {
                $user = \Users::retrieveByUsername($credential);
            }

            if(!$user){
                $result['error'] = 'username not found';
                $result['error_code'] = self::USERNAME_OR_PASSWORD_MISMATCH;
                return json_encode($result);
            }

            if ($user->getPassword() != \Users::hashPassword($password, $user->getPassword())) {
                $result['error'] = 'password invalid';
                $result['error_code'] = self::USERNAME_OR_PASSWORD_MISMATCH;
                return json_encode($result);
            }
        }
        catch (Exception $ex)
        {
            $result['error_code'] = self::UNKNOWN_ERROR;
            $result['error'] = $ex->getMessage();
            return json_encode($result);
        }

        if(!(!$user))
        {
            $user_id = $user->getId();
            $consumer_secret = $consumer->getConsumerSecret(); //'consumer_secret';
            $consumer_id = $consumer->getId(); //'consumer id';
            $result = array();

            //'user_id' + 'consumer_id' + 'scope'
            $result['refresh_token'] = $this->_generateRefreshToken($user_id, $consumer_id, $scope, $consumer_secret);
            $result['access_token'] = $this->_generateAccessToken($user_id, $consumer_id, $scope, $consumer_secret);
            return json_encode($result);
        }
        else
        {
            $result['error_code'] = self::UNKNOWN_ERROR;
            $result['error'] = 'login failed';
            return json_encode($result);
        }
    }

    /**
     * Log out, delete refresh_token and access_token
     * @return string
     */
    public function postLogout(){
        $data = $this->_verifyAccessToken();
        if (isset($data['error_code']) && $data['error_code'] != 0)
        {
            return json_encode('success');
        }
        if (!isset($data['client_id'],$data['access_token'], $data['user_id']))
        {
            return 'success';
        }
        $client_id = $data['client_id'];
        $access_token = $data['access_token'];
        $refresh_token = $this->post('refresh_token');
        $user_id = $data['user_id'];
        $this->_removeAccessToken($access_token);
        $this->_removeRefreshToken($client_id, $user_id, $refresh_token);
        return json_encode('success');
        // Delete Refresh token by retrieve by user_id, client_id
        // Delete access_token by access_token
    }

    /**
     * Accept refresh_token and return new access token
     * 1. Confirm API call contains client id and refresh token
     * 2. Verify Consumer and get Consumer Secret
     * 3. Use consumer secret as key to decrypt refresh token
     * 4. Generate new access token, return as Json
     * @return mixed
     */
    public function postRefreshToken()
    {
        $consumer_id = $this->post('client_id');
        $refresh_token = $this->post('refresh_token');

        // if any of these value is null
        if (!isset($consumer_id, $refresh_token))
        {
            $result["error"] = "must post both client id and refresh token";
            return json_encode($result);
        }

        $result = $this->_verifyRefreshToken($consumer_id, $refresh_token);
        return json_encode($result);
    }

    /**
     * return user information such as name, id
     * @return mixed
     */
    public function postMe()
    {
        $data = $this->_verifyAccessToken();
        if (isset($data['error_code']) && $data["error_code"] != "0")
        {
            $result["error_code"] = 3;
            //$result["error"] = "Access token is not valid";
            $result["error"] = $data["error"];
            return json_encode($result);
        }

        $user_id = $data["user_id"];

        $user = \Users::retrieveById($user_id);

        if ($user instanceof \Users)
        {
            $result["id"] = $user->getId();
            $result["name"] = $user->getFullName();

            $avatar = $user->getAvatar();

            if (strpos($avatar, 'facebook') !== false) // if avatar facebook used
            {
                $result["avatar"] = $user->getAvatar();
            }
            else
            {
                $result["avatar"] = NotificationService::WEB_PATH . $user->getAvatar();
            }
            return json_encode($result);
        }


        $result["error_code"] = 0;
        $result["error"] = 'Unknown error';
        return json_encode($result);

    }

    /**
     * return list of notification based on update time
     * @return array
     */
    public function postGetNotification()
    {
        $data = $this->_verifyAccessToken();
        if (isset($data['error_code']) && $data['error_code'] != 0)
        {
            $result["error_code"] = 3;
            $result["error"] = "Access token is not valid";
            return json_encode($result);
        }

        //$start_time = new \MongoDate(intval(strtotime('2014-05-28 00:00:00')));
        //$end_time = new \MongoDate(intval(strtotime('2014-05-31 00:00:00')));
        //$condition = array('user_id'=> intval($this->_user->id),'created_time'=>array('$gt' =>$start_time ),'created_time'=>array('$lt' =>$end_time ));
        $condition = array('user_id' => intval($data["user_id"]));
        //TODO: use actual user id here
        //$condition = array('user_id' => intval(143));
        //$lastest = $this->post('time');
        //if ($lastest)
        //{
        //    array_push($condition, 'created_time'=>array('$gt'=> $lastest));
        //}
        //$notifications = Notification::searchNotificationByCondition($condition, 1, 100);
        $notifications = Notification::readNotificationByUser(intval($data["user_id"]),1, 100);
        //for ($ii = 0; $ii < count($notifications); $ii++)
        //{
        //    $result[$ii][""] = $notifications[$ii]->get;
        //}


        if (!isset($notifications))
        {
            return json_encode(array());
        }

        return $this->_getNotification($notifications);
    }

    /**
     * keep track of mobile app usages (without Google Analytics)
     * because we could not use device plug-in with windows phone yet, just return true
     */
    public function postTrack()
    {
        return true;
    }

    /**
     * Just return true to confirm internet connection
     */
    public function getConnection()
    {
        return true;
    }

    /**
     * Register GCM reg-id with api, so we could push notification to google cloud services
     */
    public function postRegisterGCM()
    {
        Logger::factory('gcm')->addAlert('GCM begin');
        $ajax =  new \AjaxResponse(\AjaxResponse::SUCCESS,'posted');
        $data = $this->_verifyAccessToken();
        if (isset($data['error_code']) && $data['error_code'] != 0)
        {
            $result["error_code"] = 3;
            $result["error"] = "Access token is not valid";
            return $result;
        }

        $deviceid = $this->post('uuid');
        $gcm_reg_id = $this->post('regid');
        $client_id = $data['client_id'];
        $user_id = $data["user_id"];
        Logger::factory('gcm')->addAlert('GCM begin'.$deviceid.$gcm_reg_id.$client_id.$user_id);

        $records = \ClientApi::findByClientIdAndUserId($client_id, $user_id);
        foreach ($records as $record)
        {
            if ($record instanceof \ClientApi)
            {
                $record_deviceid = $record->getDeviceid();
                if (!empty($record_deviceid) && $record_deviceid !== $deviceid)
                {
                    continue;
                }

                $record->setDeviceid($deviceid);
                $record->setGcmRegId($gcm_reg_id);
                $record->save();
                break;
            }
        }
        return true;
    }

    /**
     * Convert from mongo data to array of notification object
     * @param $data_notify
     * @return array
     */
    protected function _getNotification( $data_notify )
    {

        $array_notify = array();
        foreach ( $data_notify as $data ) {
            $tmp = array();
            $order = \Order::retrieveById(intval($data[ 'order_id' ]));
            $notify_id = $data[ '_id' ]->{'$id'};
            if (!$order)
            {
                //$tmp['poster'] = $order->getOrderAvatar();
            }
            else
            {
                $tmp['poster'] = $order->getOrderAvatar();
            }

            if (!isset($data['type'],$data['is_new'], $data[ 'created_time' ]))
            {
                continue;
            }
            $tmp['type'] = $data['type'];
            $tmp['id'] = $notify_id;
            $tmp['time'] = $this->_formatTimeNotification( $data[ 'created_time' ]->sec );
            $tmp['is_new'] = $data['is_new'];
            $tmp['message'] = '';
            switch ( $tmp[ 'type' ] )
            {
                case NotificationUser::TYPE_NOTIFY_ORDER_STATUS:
                    if (!isset($data['order_status'], $data[ 'order_name' ]))
                    {
                        continue;
                    }
                    $tmp['status'] = $data['order_status'];
                    $tmp[ 'order_name' ] = $data[ 'order_name' ];
                    //TODO: $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                    switch ( $data[ 'order_status' ] ) {
                        case 'SELLER_DELIVERY':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' của bạn người bán đã giao hàng.';
                            break;
                        case 'RECEIVED_FROM_SELLER':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đã nhận được hàng từ người bán.';
                            break;
                        case 'CHECKED':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đã được kiểm hàng.';
                            break;
                        case 'BOUGHT':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đã được mua';
                            break;
                        case 'WAITING_DELIVERY':
                            if ( isset($data[ 'is_express_cn_vn' ])) {
                                if($data[ 'is_express_cn_vn' ] == 1 ){
                                    $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' CPN đã về kho phân phối, chúng tôi đang tiến hàng giao hàng cho quý khách';
                                }
                            } else {
                                $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đã có thể giao hàng cho bạn';
                            }
                            break;
                        case 'CONFIRM_DELIVERY':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' chuẩn bị được giao hàng cho bạn';
                            break;
                        case 'DELIVERING':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đang trên đường giao cho bạn';
                            break;
                    }
                    break;
                case NotificationUser::TYPE_NOTIFY_CONFIRM_ORDER:
                    if (!isset($data[ 'order_name' ], $data[ 'type_confirm' ]))
                    {
                        continue;
                    }
                    $tmp[ 'order_name' ] = $data[ 'order_name' ];
                    //TODO: $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                    switch ( $data[ 'type_confirm' ] ) {
                        case 'price':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đang chờ được xác nhận về giá';
                            break;
                        case 'quantity':
                            $tmp[ 'message' ] = 'Đơn hàng '.$tmp[ 'order_name' ].' đang chờ được xác nhận về số lượng';
                            break;
                    }
                    break;
                case NotificationUser::TYPE_NOTIFY_CHAT_ORDER:
                    if (!isset($data[ 'order_name' ], $data[ 'type_chat' ]))
                    {
                        continue;
                    }
                    $tmp[ 'order_name' ] = $data[ 'order_name' ];
                    //TODO: $tmp[ 'order_url' ] = Main::getUserRouter()->createUrl( 'order_detail/default', array( 'id' => $order->id, 'notify_id' => $notify_id ) );
                    switch ( $data[ 'type_chat' ] ) {
                        case 'human':
                            if(isset($data['total_message']) && intval($data['total_message'])>1){
                                $tmp[ 'message' ] = 'Sếu Đỏ gửi cho bạn '.intval($data['total_message']).' tin nhắn ở đơn hàng '.$tmp[ 'order_name' ];
                            }else{
                                $tmp[ 'message' ] = 'Sếu Đỏ gửi cho bạn tin nhắn ở đơn hàng '.$tmp[ 'order_name' ];
                            }

                            break;
                        case 'activity':
                            $tmp[ 'message' ] = $data[ 'message_content' ].' ở đơn hàng '.$tmp[ 'order_name' ];
                            break;
                    }
                    break;
            }

            $array_notify[ ] = $tmp;
        }

        return $array_notify;
    }

    /**
     * Convert Mongo Date to string
     * @param $time
     * @return bool|string
     */
    private function _formatTimeNotification( $time )
    {
        $time = intval( $time );
        $string_time = date( 'h:i d/m/Y', $time );

        return $string_time;
    }
}