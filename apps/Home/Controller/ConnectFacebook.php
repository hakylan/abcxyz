<?php
namespace Home\Controller;
use Flywheel\Config\ConfigHandler;
use Flywheel\Factory;
use Flywheel\Filesystem\AjaxUploader;
use \Flywheel\Session;
use Home\Controller\HomeBase;
use Monolog\Handler\LogglyHandler;
use Facebook\Facebook;
use SeuDo\Event\User;
use SeuDo\Main;
use \SeuDo\Accountant\Util;
use Flywheel\Redis\Client;

require_once(LIBRARY_PATH . '/Facebook/facebook.php');
class ConnectFacebook extends HomeBase{

    const ErrorPassEmpty = 0,
          ErrorDb = -1,
          ErrorNotUsername = -3,
          ErrorNotEmail = -5,
          RegisterSuccess = 1,
          NotLogin = -4,
          NotSessionFacebook = -2,
           RegisterFacebook = 1,
            ConfirmPass = 2;
    //public $facebook;

    public $session;

    public function beforeExecute(){

        $this->session = new Session\Session();
    }

    public function executeDefault(){
//        if($_SERVER['REMOTE_ADDR']=='42.115.210.28'){
        $redis = Client::getConnection("order_link_error");
        $error_link = $redis->zRange(REDIS_ORDER_LINK_ERROR,0,-1);
        if(!empty($error_link)){
            foreach ($error_link as $error) {
                $error = json_decode($error,true);
                print_r('<pre>');
                print_r($error);
                print_r('</pre>');
            }

        }
//            print_r('<pre>');
//            print_r($error_link);
//            print_r('</pre>');
        exit();
//        }
        $this->setView('User/test_login');

        return $this->renderComponent();
    }

    public function executeConnectFacebook(){
        $this->validAjaxRequest();
        $user = \HomeAuth::getInstance()->getUser();

        $profile_fb = $this->request()->post('profile_fb','STRING','');

        $profile_fb = json_decode($profile_fb,true);

        if(!isset($profile_fb['id'])){
            $response = array(
                'result' => \AjaxResponse::ERROR,
                'html'   => '',
                'message' => "Có lỗi xảy ra, xin thử lại. Nếu không được, hãy thử lại bằng một tài khoản khác."
            );
            return $this->renderText(json_encode($response));
        }

        if($user){

            $profile_fb['avatar'] = "https://graph.facebook.com/{$profile_fb['id']}/picture?width=600&height=600";

            \Users::insertProfileFacebook($profile_fb,$user);

            $user->setFacebookId($profile_fb['id']);

            if($user->getAvatar()!=''){
                $user->setAvatar(\Users::setAvatarFacebook($user,$profile_fb['avatar']));
            }

            $user->save();

            if($user->account_no==''){
                Util::createUserAccount($user);
            }

            $this->view()->assign('profile_fb',$profile_fb);
            $this->view()->assign('user',$user);
            $this->setView("User/profile_connect_fb");

            $response = array(
                'result' => 5,
                'html'   => $this->renderPartial(),
                'url'    => $this->createUrl('Register/register_success'),
            );

            return $this->renderText(json_encode($response));

        }else if($profile_fb){
            $fb_user_id = $profile_fb['id'];

            $profile_fb['avatar'] = "https://graph.facebook.com/{$profile_fb['id']}/picture?width=600&height=600";

            $check_user = \Users::checkRegisterFacebook($fb_user_id);
            if($check_user){

                \BaseAuth::getInstance()->setSession($check_user);

                $response = array(
                    'result' => 3,
                    'html'   => ''
                );
            }else{
                if(!isset($profile_fb["emmail"])){
                    $profile_fb["emmail"] = "";
                }
                $check_email = \Users::retrieveByEmail($profile_fb['email']);

                if($check_email){
                    $this->view()->assign('profile_fb',$profile_fb);

                    $this->view()->assign('user_check',$check_email);

                    $profile_fb['avatar_seudo'] = $check_email->getAvatar();

                    $this->session->set('profile_fb',$profile_fb);

                    $this->setView('User/confirm_password');

                    $html = $this->renderPartial();

                    $response = array(
                        'result' => ConnectFacebook::ConfirmPass,
                        'html'   => $html
                    );
                }else{
                    $this->view()->assign('profile_fb',$profile_fb);

                    $this->session->set('profile_fb',$profile_fb);

                    $this->setView('User/register_facebook');

                    $html = $this->renderPartial();

                    $response = array(
                        'result' => ConnectFacebook::RegisterFacebook,
                        'html'   => $html
                    );
                }
            }
            //$profile_fb = $facebook->api('/me');

        }

        return $this->renderText(json_encode($response));
    }

    public function executeProcessRegister(){
        $this->validAjaxRequest();
        $profile_fb = $this->session->get('profile_fb');

        if(!$profile_fb){
            $response['message'] = "Xin đăng nhập lại Facebook";
            $response['error'] = ConnectFacebook::NotSessionFacebook;
            return $this->renderText(json_encode($response));
        }

        $check_isset_user = \Users::retrieveByFacebookId($profile_fb['id']);

        if($check_isset_user){
            $link = $this->createUrl('ConnectFacebook/connect_facebook');
            $this->redirect($link);
            $response['message'] = "Xin đăng nhập lại Facebook";
            $response['error'] = ConnectFacebook::NotSessionFacebook;
            return $this->renderText(json_encode($response));
        }

        $password = $this->request()->post('password','STRING','');
        $profile_fb['username'] = $this->request()->post('username','STRING','');
        $profile_fb['first_name'] = $this->request()->post('first_name','STRING','');
        $profile_fb['last_name'] = $this->request()->post('last_name','STRING','');
        $email_sd = $this->request()->post('email','STRING','');
        if($email_sd != $profile_fb['email']){
            $profile_fb['email_sd'] = $email_sd;
        }
        $response = array();
        if($password == ''){
            $response['message'] = "Mật khẩu không được bỏ trống";
            $response['error'] = ConnectFacebook::ErrorPassEmpty;

        }else if($profile_fb['username'] == ''){
            $response = array("message"=>"Tài khoản không được bỏ trống","error"=>ConnectFacebook::ErrorNotUsername);
        }else{

            $profile_fb['email_fb'] = $profile_fb['email'];

            $check_user = \Users::retrieveByUsername($profile_fb['username']);

            $check_email = \Users::retrieveByEmail($email_sd);

            if($check_user){
                $response['message'] = "Tài khoản đã tồn tại.";
                $response['error'] = ConnectFacebook::ErrorNotUsername;
            }

            elseif($check_email){
                $response['message'] = "Email này đã có người sử dụng, xin thử lại một Email khác";
                $response['error'] = ConnectFacebook::ErrorNotEmail;
            }

            else{
                $profile_fb['password'] = \Users::hashPassword($password);

                $user = \Users::registerAccount($profile_fb);

                if(!$user){

                    $response['message'] = "Không thể tạo mới tài khoản. Xin thử lại";
                    $response['error'] = ConnectFacebook::ErrorDb;
                }else{
                    Util::createUserAccount($user);
                    // Dispatch event
                    $this->dispatch('afterRegisterByFacebook', new User($user));

                    $result = \BaseAuth::getInstance()->authenticate($profile_fb['username'],$password);
                    if($result){
                        $this->session->remove('profile_fb');
                        $response['error'] = ConnectFacebook::RegisterSuccess;
                        $response['url'] = Main::getHomeRouter()->createUrl("Register/register_success");
                    }else{
                        $response['message'] = "Không thể đăng nhập";
                        $response['error'] = ConnectFacebook::NotLogin;
                    }
                }
            }
        }

        return $this->renderText(json_encode($response));
    }

    public function executeConfirmPassLogin(){
        $this->validAjaxRequest();
        $profile_fb = $this->session->get('profile_fb');
        $password = $this->request()->post('password','STRING','');

        $user = \Users::retrieveByEmail($profile_fb['email']);

        if(!$user){
            $response['message'] = "Không tồn tại tài khoản.";
            $response['error'] = 0;
        }else{


            $result = \BaseAuth::getInstance()->authenticate($user->username,$password);

            if ($result == true && intval($result) >= 0) {
                $this->session->remove('profile_fb');

                $avatar = \Users::setAvatarFacebook($user,"https://graph.facebook.com/{$profile_fb['id']}/picture?width=600&height=600");

                $user->setFacebookId($profile_fb['id']);
                $user->setAvatar($avatar);
                $result = $user->save();

                if($result){

                    if($user->account_no==''){
                        Util::createUserAccount($user);
                    }


                    \UserProfiles::setUserProfile($user->id,\UserProfiles::USER_PROFILE_AVATAR_FB,$avatar);
                }
                $response['message'] = "Đăng nhập thành công";
                $response['error'] = 1;

            }else{
                $response['message'] = "Mật khẩu không chính xác. Xin thử lại";
                $response['error'] = 0;
            }
        }

        return $this->renderText(json_encode($response));

    }

    public function executeSelectAvatar(){

    }

    public function executeRegisterSuccess(){
        $this->setView("User/register_success");
        return $this->renderComponent();
    }

    public function checkExistAccount(){
        $username = $this->request()->post('username');
    }
}
