<?php
namespace User\Controller;
use SeuDo\Main;
use \Flywheel\Validator\Util;
use Flywheel\Db\Type\DateTime;
class User extends UserBase
{

    private $logger = null;
    const VN_LOCATION = 1;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->logger = \UserAuth::getInstance()->getUser();
        // Add js require

    }
    public function executeDefault()
    {
        return $this->executeDetail();
    }
    public function executeDetail()
    {
        $this->setView('User/default');
        $this->logger = \UserAuth::getInstance()->getUser();
        $mobiles = \UserMobiles::findByUserId($this->logger->getId());
        $states = \Locations::findByType('STATE');
        $districts = \Locations::findByType('DISTRICT');
        $this->view()->assign(
            array(
                'user' => $this->logger,
                'mobiles' => $mobiles,
                'states' => $states
            )
        );
        return $this->renderComponent();
    }

    public function executeUpdateProfile()
    {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();
        $data = $this->request()->post('input', 'ARRAY', array());

        if(empty($data)) {
            $error = \AjaxResponse::responseError('Data is missing', $data);
            return $this->renderText($error);
        }

        $data['modified_time'] = new \DateTime();
        /* init data */
        $identity = $address = array();

        if(isset($data['num_cmtnd'])) {
            $identity['num_cmtnd'] = $data['num_cmtnd'];
        }

        if(isset($data['city_cmtnd'])) {
            $identity['city_cmtnd'] = $data['city_cmtnd'];
        }

        if(isset($data['date_cmtnd'])) {
            $identity['date_cmtnd'] = $data['date_cmtnd'];
        }
        if(!empty($identity)){
            $data['cmtnd'] = json_encode($identity);
        }
        //tỉnh thành, quận huyện
        if(!empty($data['tt_id'])&& !empty($data['qh_id']) && !empty($data['detail_address']) ) {

            $province = \Locations::retrieveById($data['tt_id']);
            $state = \Locations::retrieveById($data['qh_id']);
            if($province) $data['tt_address'] = $province->getLabel();
            if($state) $data['qh_address'] = $state->getLabel();
            $data['detail_address']=trim($data['detail_address']);
        }

        $user = $this->logger;
        /* nếu user đang không đăng nhập, hoặc mất session */

        if(!$user || !($user instanceof \Users)) {
            $error = \AjaxResponse::responseError('Bạn phải đăng nhập để thực hiện tác vụ này !');
            return $this->renderText($error);
        } else {
            /* nếu email được post len */
            if(isset($data['email']) && $data['email']!=$user->getEmail()) {
                $email = $data['email'];
                /*check valid email*/
                if (Util::isValidEmail($email)!= 1) {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-email';
                    $ajax->message = 'Email không hợp lệ, vui lòng nhập đúng định dạng email!.';
                    return $this->renderText($ajax->toString());
                }
                /*check xem email da ton tai hay chưa*/
                $email_exist = \UsersPeer::checkIsTakenEmail($email);
                if($email_exist) {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-email';
                    $ajax->message = 'Email đã được sử dụng, vui lòng nhập địa chỉ email khác!';
                    return $this->renderText($ajax->toString());
                }
            }
            /* nếu ngày sinh được post lên*/
            if (isset($data['birthday'])) {
                $birthday = DateTime::createFromFormat('d/m/Y', trim($data['birthday']));

                if(!$birthday){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-birthday';
                    $ajax->message = 'Ngày sinh không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }

                $data['birthday'] = $birthday->format('Y-m-d');
            }

            /* nếu các dữ liệu valid hết */
            $result = $this->_save($user, $data, $error);
            if($result === true) {
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->element = 'success';
                $ajax->message = 'Cập nhật thành công';
                return $this->renderText($ajax->toString());
            }
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error';
            $ajax->message = 'Hiện tại không thể lưu thông tin mới, vui lòng liên hệ với CSKH để được giúp đỡ!';
            return $this->renderText($ajax->toString());
        }
    }

    public function _save(\Users $user, $data = array(), &$error = array())
    {
        $sendMailVerify = false;
        if (($data['email']!=($user->getEmail())) && $data['email'] != '') {
            $data['verify_email']=0;
            $sendMailVerify = true;
        }

        $user->hydrate($data);

        if (empty($error)) {
            if (empty($error) && $user->save()) {
                // Add new address default for this user if not set
                if ($sendMailVerify == true) {

                    $emailActivity = new \EmailActivities();

                    $activity = $emailActivity->setNewActivitis($user->getEmail(), $user->getId());

                    if($activity){
                        $t = base64_encode($user->getId().'-'.($activity->getCode()));
                        $link = Main::getHomeUrl().'register/success_verify?t='.$t;
                        \UserMailUtil::pushVerifyEmail($user->getUsername(), $user->getEmail(), $link, $user->getFullName());
                    }

                }
                if (isset($data['cmtnd']) && $data['cmtnd'] != '') {
                    $cmtnd = new \UserProfiles();
                    $cmtnd->setUserProfile($user->getId(), 'cmtnd', $data['cmtnd']);
                }
                if (isset($data['yahoo']) && $data['yahoo'] != '') {
                    $yahoo = new \UserProfiles();
                    $yahoo->setUserProfile($user->getId(), 'yahoo', $data['yahoo']);
                }
                if (isset($data['skype']) && $data['skype'] != '') {
                    $skype = new \UserProfiles();
                    $skype->setUserProfile($user->getId(), 'skype', $data['skype']);
                }
                $this->dispatch('afterUpdateProfile', new \SeuDo\Event\User($user));
                return true;
            }
            foreach ($user->getValidationFailures() as $validationFailure) {
                $error[$validationFailure->getColumn()] = $validationFailure->getMessage();
            }
        }
        return false;
    }

    public function executePasswordProfile()
    {
        $this->setView('User/password_profile');
        return $this->renderComponent();
    }

    public function executeUpdatePassword()
    {
        $data = $this->request()->post('input', 'ARRAY', array());
        $pwd = array_key_exists('pwd', $data) ? $data['pwd'] : "0";
        $newpwd = array_key_exists('newpwd', $data) ? $data['newpwd'] : "0";
        $renewpwd = array_key_exists('re_newpwd', $data) ? $data['re_newpwd'] : "1";

        $ajax = new \AjaxResponse();
        $result = \UserAuth::getInstance()->authenticate($this->logger->getUsername(), $pwd, true);
        if ($result !== true) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-pwd';
            $ajax->message = 'Mật khẩu của bạn không chính xác';
            return $this->renderText($ajax->toString());
        }
        $checkNewPass = \UserAuth::getInstance()->authenticate($this->logger->getUsername(), $newpwd, true);
        if ($checkNewPass === true) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-newpwd';
            $ajax->message = 'Mật khẩu mới không được trùng với mật khẩu cũ';
            return $this->renderText($ajax->toString());
        }
        if (preg_replace('/\s+/', '', $newpwd) == "") {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-newpwd';
            $ajax->message = 'Mật khẩu không được rỗng';
            return $this->renderText($ajax->toString());
        }
        if (strlen(preg_replace('/\s+/', '', $newpwd)) <6) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-newpwd';
            $ajax->message = 'Mật khẩu từ 6 ký tự trở lên';
            return $this->renderText($ajax->toString());
        }
        if (preg_replace('/\s+/', '', $renewpwd) == "") {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-renewpwd';
            $ajax->message = 'Nhập lại mật khẩu rỗng';
            return $this->renderText($ajax->toString());
        }

        if (preg_replace('/\s+/', '', $newpwd) != preg_replace('/\s+/', '', $renewpwd)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-renewpwd';
            $ajax->message = 'Mật khẩu không khớp';
            return $this->renderText($ajax->toString());
        }

        $user = \Users::findOneById($this->logger->getId());
        $this->logger = $user;
        $this->logger->setPassword(\Users::hashPassword($data['newpwd']));

        if ($this->logger->save()) {

            $this->dispatch('afterUpdatePassword', new \SeuDo\Event\User(array('user' => $user)));

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->element = 'success';
            $ajax->message = 'Bạn đã đổi mật khẩu cá nhân, bạn sẽ được chuyển đến trang đăng nhập để truy cập lại vào tài khoản.';
            return $this->renderText($ajax->toString());
        }
        // Save fail - log
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->element = 'error';
        $ajax->message = 'Đổi mật khẩu không thành công, vui lòng liên hệ chăm sóc khách hàng để được giúp đỡ!';
        return $this->renderText($ajax->toString());
    }

    public function executeDisconectFacebook()
    {
        $this->validAjaxRequest();
        if ($this->request()->isPostRequest()) {
            $user = \UserAuth::getInstance()->getUser();
            if ($user && ($user instanceof \Users)) {
                $user->setFacebookId(0);

                $result = $user->save();

                $ajax = new \AjaxResponse();
                if ($result) {
                    $setAuth = \UserAuth::getInstance()->setSession($user);
                    \Users::deleteProfileFacebook($user);
                    $this->view()->assign("user",$user);
                    $this->setView("User/disconnect_success");
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->element = $this->renderPartial();
                    $ajax->message = 'Đã ngắt kết nối thành công.';

                    return $this->renderText($ajax->toString());
                }

                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Không thể ngắt kết nối với tài khoản Facebook, vui lòng liên hệ với quản trị để được giúp đỡ';
                return $this->renderText($ajax->toString());
            }
        }
    }

    public function executeDeleteMobile(){

        $ajax = new \AjaxResponse();

        if ($this->request()->isPostRequest()) {
            $this->validAjaxRequest();
            $id = $this->request()->post('id');

            if (isset($id)) {
                $delMobile = \UserMobiles::findOneById($id);
                if($delMobile->delete()){
                    $user = \UserAuth::getInstance()->getUser();
                    $this->dispatch('afterDeleteMobile', new \SeuDo\Event\User(array('user' => $user)));
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->element = 'success';
                    $ajax->message = 'Đã xóa thành công số điện thoại.';
                    return $this->renderText($ajax->toString());
                }
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Không thể xóa số điện thoại.';
                return $this->renderText($ajax->toString());
            } else {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Không thể nhận diện được số điện thoại để xóa.';
                return $this->renderText($ajax->toString());
            }
        }
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->element = 'error';
        $ajax->message = 'Không thể xóa, vui lòng liên hệ CSKH hoặc sử dụng công cụ chát trên trang để được giúp đỡ!';
        return $this->renderText($ajax->toString());
    }

    public function executeVerifyEmail()
    {
        $this->validAjaxRequest();
        $user = \UserAuth::getInstance()->getUser();
        //Begin: setEmailActive+SendMail Verify for new user.
        $emailActivity = new \EmailActivities();
        $checkSet = $emailActivity->setNewActivitis($user->getEmail(), $user->getId());
        if ($checkSet == true) {
            $t = base64_encode($user->getId() . '-' . ($emailActivity->getCode()));
            $link = Main::getHomeUrl('register/success_verify') . '?t=' . $t;
            //send mail
            if (\UserMailUtil::pushVerifyEmail($user->getUsername(),$user->getEmail() ,$link, $user->getFullName() ) ) {
                return true;
            }
        }
        return false;
    }

    public function executeClickVerifyEmail(){

        $this->validAjaxRequest();
        $id = $this->request()->post('id');
        $user = \UserAuth::getInstance()->getUser();
        $ajax = new \AjaxResponse();

        if(!$user || !($user instanceof \Users)){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error';
            $ajax->message = 'Obj by User not found.';
            return $this->renderText($ajax->toString());
        }
        if($id){
            $emailActivity = new \EmailActivities();
            $activity = $emailActivity->setNewActivitis($user->getEmail(), $user->getId());

            $code = $activity->getCode();
            $t = base64_encode($id.'-'.$code);
            $link = Main::getHomeUrl(). 'register/success_verify?t=' . $t;

            $send = new \UserMailUtil();
            $checkSend = $send->pushVerifyEmail($user->getUsername(),$user->getEmail(),$link, $user->getFullName());
            if ($checkSend){
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->element = 'success';
                $ajax->message = 'Đã gửi một mail tới địa chỉ "<strong>'.$user->getEmail().'</strong>" của bạn. Vui lòng kiểm tra hòm thư để xem hướng dẫn kích hoạt tài khoản.';
                return $this->renderText($ajax->toString());
            }

        }
//        Logger::factory('test')->addError('test send mail from logger, with level >=notice',array('fucking message and data'));
        /**
         * @TODO log here
         */

        $ajax->type = \AjaxResponse::ERROR;
        $ajax->element = 'error';
        $ajax->message = 'Hiện tại không thể gửi mail, vui lòng liên hệ CSKH hoặc sử dụng công cụ chát trên trang để được giúp đỡ';
        return $this->renderText($ajax->toString());
        }

    public function executeEditAddress(){
        $this->setView('User/edit_address');
        $this->renderComponent();
    }

    public function executeSetEmailNew(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $user = \UserAuth::getInstance()->getUser();
        if($this->request()->post('email')){
        $email = trim($this->request()->post('email'));
        $user_email = \Users::retrieveByEmail($email);
            if($user_email) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error-email';
                $ajax->message = 'Email này đã được đăng ký.';
                return $this->renderText($ajax->toString());
            }
            if(Util::isValidEmail($email) != 1){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error';
                $ajax->message = 'Email không hợp lệ.';
                return $this->renderText($ajax->toString());
            }
                $user->setEmail($email);
                if($user->save()){
                $emailActivity = new \EmailActivities();
                $activity = $emailActivity->setNewActivitis($user->getEmail(), $user->getId());
                if ($activity) {
                    $code = $activity->getCode();
                    $t = base64_encode( $user->getId().'-'.$code);
                    $link = Main::getHomeUrl(). 'register/success_verify?t=' . $t;
                    $send = new \UserMailUtil();
                    if ($send->pushVerifyEmail($user->getUsername(),$user->getEmail(),$link, $user->getFullName() ) ) {
                        $ajax->type = \AjaxResponse::SUCCESS;
                        $ajax->element = 'success';
                        $ajax->message = 'Đã gửi một mail tới địa chỉ "<strong>'.$user->getEmail().'</strong>" của bạn. Vui lòng kiểm tra hòm thư để xem hướng dẫn kích hoạt tài khoản.';
                        return $this->renderText($ajax->toString());
                    }
                }
                }
            }
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error';
            $ajax->message = 'Vui lòng điền địa chỉ email mới!';
            return $this->renderText($ajax->toString());
    }

    public function executeEditAvatar()
    {
        $this->logger = \UserAuth::getInstance()->getUser();
        $this->view()->assign(
            array(
                'user' => $this->logger,
            )
        );
        $this->setView('User/edit_avatar');
        return $this->renderComponent();
    }

    /*
     * Hàm gửi email cho khách hàng
     * created: vanhs
     * time: 17/05/2014
     * input: subject (string), params (array)
     * output: none
     */

    public function sendEmailCustomers(){
        $customers = \Users::select()->where("section = '" . \Users::SECTION_CUSTOMER . "' status = '" . \Users::STATUS_ACTIVE . "'")->execute();
//        $customers = \Users::select()->execute();
        $send = new \UserMailUtil();

        $params = array('banner' => 'http://seudo.vn/assets/images/templates/email/chuyen-phat-nhanh-624x299.jpg');

        if(sizeof($customers) > 0){
            foreach($customers as $customer){
                $data = $customer->toArray();
//                $data['email'] = 'hosivan90@gmail.com';
                if($data['email']){
                    $subject = "Gửi bạn " . $data['last_name'] . ' ' . $data['first_name'];
                    if($send->sendUsersEmail($subject, $data['email'], $params)){
                        print "Gửi thành công tới địa chỉ email: " . $data['email'] . "\n";
                    }else{
                        print "Có lỗi xảy ra khi gửi email tới địa chỉ: " . $data['email'] . "\n";
                    }
                }//end if email

            }//end foreach $customers

        }//end if count $customers
    }
}
