<?php
namespace Backend\Controller\User;

use Backend\Controller\BackendBase;
use Flywheel\Db\Type\DateTime;
use SeuDo\Main;
use Flywheel\Filesystem\Uploader;
use \SeuDo\SFS\Client;
use \SeuDo\SFS\Upload;
use Flywheel\Factory;
use Flywheel\Config\ConfigHandler;
use Flywheel\Session\Session;
use Flywheel\Validator\Util;
use SeuDo\Permission;


class UserProfile extends BackendBase {

    private $logger = null;

    public function beforeExecute() {
        parent::beforeExecute();
        $this->logger = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault(){

    }

    public function executeEdit(){

        if ($this->request()->isPostRequest()) {
            $this->validAjaxRequest();
            $ajax = new \AjaxResponse();

            $data = $this->request()->post('input', 'ARRAY', array());

            $data['modified_time'] = new \DateTime();

            $user = $this->logger;
            if (!($user instanceof \Users)) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = '_error';
                $ajax->message = 'Tài khoản không tồn tại trên hệ thống!';
                return $this->renderText($ajax->toString());
            }

            if($data['last_name'] == ''){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error-fullname';
                $ajax->message = 'Họ không được để trống.';
                return $this->renderText($ajax->toString());
            }

            if($data['first_name'] == ''){
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = 'error-fullname';
                $ajax->message = 'Tên không được để trống.';
                return $this->renderText($ajax->toString());
            }

            if (isset($data['birthday'])) {

                $data['birthday']=trim($data['birthday']);

                $date = DateTime::createFromFormat('d/m/Y', $data['birthday']);

                if(!$date){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-birthday';
                    $ajax->message = 'Ngày sinh không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }else{
                    $data['birthday'] = $date->format('Y-m-d');
                }
            }

            if($data['mobile']){
                if (!Util::isValidPhoneNumber($data['mobile'])) {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = 'error-mobile';
                    $ajax->message = 'Số điện thoại sai cú pháp';
                    return $this->renderText($ajax->toString());
                }
            }

            $checkFlag = $this->_save($user, $data, $error);

            if ($checkFlag == false) {
                $ajax->type = \AjaxResponse::WARNING;
                $ajax->element = 'error';
                $ajax->message = 'Hiện tại không thể lưu thông tin mới, vui lòng liên hệ với CSKH để được giúp đỡ!';
                return $this->renderText($ajax->toString());
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->element = 'success';
            $ajax->message = 'Cập nhật thành công';
            return $this->renderText($ajax->toString());
        }

        $this->setView('UserProfile/form');

        $user = \BackendAuth::getInstance()->getUser();

        $this->view()->assign(
            array(
                'user' => $user
            )
        );

        return $this->renderComponent();
    }

    public function executeChangePass() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        $user = \BackendAuth::getInstance()->getUser();

        $new_pass = $this->post('new_pass');
        $re_new_pass = $this->post('re_new_pass');
        $old_pass = $this->post('old_pass');

        $error = array(
            'old_pass' => '',
            'new_pass' => '',
            're_new_pass' => ''
        );

        if($old_pass == ''){
            $error['old_pass'] = self::t('Mật khẩu cũ không được để trống');
        }

        if($new_pass == ''){
            $error['new_pass'] = self::t('Mật khẩu mới không được để trống');
        }

        if($re_new_pass == ''){
            $error['re_new_pass'] = self::t('Nhập lại mật khẩu mới không được để trống');
        }

        if($old_pass != ''){
            $result = \BackendAuth::getInstance()->authenticate($user->getUsername(), $old_pass, true);

            if ($result !== true) {
                $error['old_pass'] = self::t('Mật khẩu cũ không đúng.');
            }
        }

        if($new_pass != ''){
            if (strlen($new_pass) < 6) {
                $error['new_pass'] = self::t('Mật khẩu mới tối thiểu %require_length% ký tự.', array(
                    '%require_length%' => 6
                ));
            }
        }

        if(strlen($new_pass) >=6){
            if ($new_pass != $re_new_pass) {
                $error['re_new_pass'] = self::t('Mật khẩu mới và nhập lại mật khẩu mới không khớp.');
            }
        }

        if (!($user)) {
            $error['_err-old_payment_pass'] = self::t('Không tìm thấy tên này để sửa đổi');
        }

        $error_filter = array_filter($error);

        if (empty($error_filter)) {
            $user->setPassword(\Users::hashPassword($new_pass));
            $user->save(false); //Quick save
            $this->dispatch('onChangedUserPassword', new \BackendEvent($this, array(
                'user' => $user
            )));

            $ajax->message = self::t('Thành công');
            $ajax->type = \AjaxResponse::SUCCESS;
        } else {
            $ajax->message = self::t('Có lỗi xảy ra');
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->error = $error;
        }

        return $this->renderText($ajax->toString());
    }

    public function executeChangePassPayment() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        $user = \BackendAuth::getInstance()->getUser();

        $new_payment_pass = $this->post('new_payment_pass');
        $re_payment_pass = $this->post('re_payment_pass');
        $old_payment_pass = $this->post('old_payment_pass');

        $error = array(
            'old_payment_pass' => '',
            'new_payment_pass' => '',
            're_payment_pass' => ''
        );

        if($user->getPaymentPass() != ''){
            if($old_payment_pass == ''){
                $error['old_payment_pass'] = self::t('Mật khẩu cũ không được để trống');
            }
        }

        if($new_payment_pass == ''){
            $error['new_payment_pass'] = self::t('Mật khẩu mới không được để trống');
        }

        if($re_payment_pass == ''){
            $error['re_payment_pass'] = self::t('Nhập lại mật khẩu mới không được để trống');
        }

        if($old_payment_pass != ''){
            if ($user->getPaymentPass() != \Users::hashPassword($old_payment_pass, $user->getPaymentPass())) {
                $error['old_payment_pass'] = self::t('Mật khẩu cũ không đúng.');
            }
        }

        if($new_payment_pass != ''){
            if (strlen($new_payment_pass) < 6) {
                $error['new_payment_pass'] = self::t('Mật khẩu mới tối thiểu %require_length% ký tự.', array(
                    '%require_length%' => 6
                ));
            }
        }

        if(strlen($new_payment_pass) >=6){
            if ($new_payment_pass != $re_payment_pass) {
                $error['re_payment_pass'] = self::t('Mật khẩu mới và nhập lại mật khẩu mới không khớp.');
            }
        }

        if (!($user)) {
            $error['_err-old_pass'] = self::t('Không tìm thấy tên này để sửa đổi');
        }

        $error_filter = array_filter($error);

        if (empty($error_filter)) {
            $user->setPaymentPass(\Users::hashPassword($new_payment_pass));
            $user->save(false); //Quick save
            $this->dispatch('onChangedUserPassword', new \BackendEvent($this, array(
                'user' => $user
            )));

            $ajax->message = self::t('Thành công');
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->error = $error;
        } else {
            $ajax->message = self::t('Có lỗi xảy ra');
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->error = $error;
        }

        return $this->renderText($ajax->toString());
    }

    public function _save(\Users $user, $data = array(), &$error = array())
    {

        $user->hydrate($data);

        if (empty($error)) {
            if (empty($error) && $user->save()) {
                // Add new address default for this user if not set

                $data['first_name'] = trim($data['first_name']);
                $data['last_name'] = trim($data['last_name']);
                $data['birthday'] = (isset($data['birthday']))? trim($data['birthday']) : '';

                if (isset($data['yahoo']) && $data['yahoo'] != '') {
                    $yahoo = new \UserProfiles();
                    $yahoo->setUserProfile($user->getId(), 'yahoo', $data['yahoo']);
                }

                if (isset($data['skype']) && $data['skype'] != '') {
                    $skype = new \UserProfiles();
                    $skype->setUserProfile($user->getId(), 'skype', $data['skype']);
                }

                if (isset($data['mobile']) && $data['mobile'] != '') {
                    $mobile = new \UserProfiles();
                    $mobile->setUserProfile($user->getId(), 'mobile', $data['mobile']);
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

    public function executeUploadAvatar(){
        $error = '';
        $host = Main::getBackendUrl();
        $path = PUBLIC_DIR."backend".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR;
        if ($this->request()->isPostRequest()) {

            $fileUpload = new Uploader($path,'avatar');

            $fileUpload->setMaximumFileSize(8);//8mb
            $fileUpload->setFilterType('.jpg, .jpeg, .png, .bmp, .gif');
            $fileUpload->setIsEncryptFileName(true);

            if ($fileUpload->upload('avatar')) {
                $data = $fileUpload->getData();

                $urlAvatar = $host . "public/temp/" . $data['file_name'];

                $ext = strrchr($urlAvatar, '.');
                $ext = strtolower($ext);
                $filename = uniqid() . '_' . time() . $ext;

                $sfs = Client::getInstance();

                $user = \BackendAuth::getInstance()->getUser();
                $uploader = new Upload('useravatar/' . $user->getModifiedTime()->format('mY'));
                $uploader->setUrl($urlAvatar);
                $uploader->setFileName($filename);

                if ($sfs->upload($uploader)) {
                    $sfs->getHttpCode();
                }

                $avatar = json_decode($sfs->getResponse());

                if ($avatar) {
                    $userAvatar = \Users::findOneById($user->getId());
                    $userAvatar->setAvatar($avatar->file);
                    $userAvatar->save();
                }

                // Sửa
                echo "<img id='draggable' src='" . $host . "public/temp/" . $data['file_name']. "' class='preview'>";
                exit;
            } else {
                $error = $fileUpload->getError();
                // Sửa
                echo $error[0];
                exit;
            }
        }
    }
}
