<?php
namespace Backend\Controller\User;
use Backend\Controller\BackendBase;
use Backend\Controller\User;
use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;
use \Flywheel\Validator\Util;
use SeuDo\Logger;
use SeuDo\Main;
use SeuDo\Permission;

class Detail extends BackendBase {
    /** @var \Users */
    public $auth;

    public function beforeExecute() {
        parent::beforeExecute();
        $this->auth = \BackendAuth::getInstance()->getUser();
    }

    private function _refuseEditHimself(\Users $user) {
        if($this->isAllowed(PERMISSION_USER_INFO_EDIT)){
            if ($this->auth && $this->auth->getId() == $user->getId()) {
                Permission::getInstance()->setDenied(PERMISSION_USER_INFO_EDIT);
            }
        }
    }

    //show user's information

    public function executeDefault() {
        if (!$this->isAllowed(PERMISSION_USER_VIEW)) {
            $this->raise403(self::t('Bạn không có quyền try cập vào khu vực này'));
        }

        $error = array();
        $user = null;

        $id = $this->get('id', 'INT', 0);
        if (!$id || !($user = \Users::retrieveById($id))) { //not found
            $error['not_found'] = true;
        } else {
            $this->_refuseEditHimself($user);
        }

        //delivery money
        $order_wait_delivery_amount = 0;
        if ($user->getSection() == \Users::SECTION_CUSTOMER) {
            $orders = \Order::findByBuyerIdAndStatus($user->getId(), \Order::STATUS_WAITING_FOR_DELIVERY);
            if (!empty($orders)) {
                foreach($orders as $order) {
                    $order_wait_delivery_amount += $order->requestDeliveryMoney();
                }
            }
        }

        $this->view()->assign(array(
            'user' => $user,
            'error' => $error,
            'order_wait_delivery_amount' => $order_wait_delivery_amount
        ));

        $this->setView('User/detail');
        return $this->renderComponent();
    }

    //add new user
    public function executeAddMobile() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_MOBILE_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $mobile = $this->post('mobile');
        $error = array();
        if (!$mobile) {
            $error[] = self::t('Số điện thoại không được để trống');
        }

        if (!Util::isValidPhoneNumber($mobile)) {
            $error[] = self::t('Số điện thoại sai cú pháp');
        }

        $user_id = $this->post('user_id');
        if (!$user_id || !($user = \Users::retrieveById($user_id))) {
            $error[] = self::t('Tài khoản không tồn tại');
        }

        //check mobile is existed
        if (empty($error)) {
            $old = \UserMobiles::retrieveByMobile($mobile);
            if ($old) {
                if ($old->getUserId() == $user_id) {
                    $error[] = self::t('Số điện thoại trùng với số đang sử dụng');
                } else {//other guy used it
                    $owner = \Users::retrieveById($old->getUserId());
                    if ($owner) {//other guy existed
                        $error[] = self::t("Số điện thoại {$mobile} hiện đang được sử dụng bởi @{$owner->getUsername()}");
                    }
                }
            }
        }

        if (!empty($error)) {
            $ajax->message = implode(' ', $error);
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $mobile = \UserMobiles::validPhoneNo($mobile);
        $om = \UserMobiles::addPhone($user, $mobile, \UserMobiles::COMING_BY_CUSTOMER_CARE);

        if ($om) {
            $this->dispatch('onAddUserMobile', new \BackendEvent($this, array(
                'mobile' => $om
            )));
            $ajax->message = self::t('Thêm thành công');
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->mobile = $om->toArray();
        } else {
            $ajax->message = self::t('Có lỗi');
            $ajax->type = \AjaxResponse::ERROR;

            Logger::factory('business')->error('Fail to save user mobile. Context:' .$om->getValidationFailuresMessage("\n"));
        }

        return $this->renderText($ajax->toString());
    }

    public function executeRemoveMobile() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS, 'OK');
        if (!$this->isAllowed(PERMISSION_USER_MOBILE_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id');
        $userMobile = \UserMobiles::retrieveById($id);
        if ($userMobile) {
            $userMobile->delete();
            $this->dispatch('onRemoveUserMobile', new \BackendEvent($this, array(
                'mobile' => $userMobile
            )));
        }

        return $this->renderText($ajax->toString());
    }


    public function executeVerifyEmail() {
        $this->validAjaxRequest();
        $ajax =new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->request()->post('id', 'INT', 0);
        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Tài khoản không tồn tại');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $email = $user->getEmail();

        if ($email && $user->isVerifyEmail()) {
            $ajax->message = self::t('Email đã được xác thực');
            $ajax->type = \AjaxResponse::SUCCESS;
        } elseif (!$email) {//maybe email has been removed before but not changed verify email status
            $user->setVerifyEmail(false); //update correct status
            $this->dispatch('onChangedUserInfo', new \BackendEvent($this, array(
                'modified' => $user->getModifiedCols(),
                'user' => $user
            )));
            $user->save(false); //quick save

            $ajax->message = self::t('Email không tồn tại');
            $ajax->sysMessage = 'REMOVE_EMAIL';
            $ajax->type = \AjaxResponse::SUCCESS;
        } else {
            //check email has been verify
            $owner = \Users::retrieveByEmail($email);
            if (!$owner || $owner->getId() == $user->getId()) {//everything ok
                $user->setVerifyEmail(true); //change state
                $user->setStatus(\Users::STATUS_ACTIVE);
                $user->save(false);//quick save
                $this->dispatch('onVerifyUserEmail', new \BackendEvent($this, array(
                    'email' => $email,
                    'user' => $user
                )));

                $ajax->message = self::t('Email đã được xác thực');
                $ajax->type = \AjaxResponse::SUCCESS;
            } else {//fuck it, this email have been used
                $ajax->message = self::t('Email đã được sử dụng bởi @%owner%', array(
                    '%owner%' => $owner->getUsername()
                ));
                $ajax->type = \AjaxResponse::ERROR;
            }
        }

        return $this->renderText($ajax->toString());
    }

    public function executeRemoveEmail() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id', 'INT');
        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $user->setEmail('');
        $user->setVerifyEmail(false);
        $user->save(false); //Quick save
        $this->dispatch('onRemovedUserEmail', new \BackendEvent($this, array(
            'user' => $user
        )));

        $ajax->message = self::t('Thành công');
        $ajax->type = \AjaxResponse::SUCCESS;
        return $this->renderText($ajax->toString());
    }

    public function executeChangeEmail() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id', 'INT');
        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $email = $this->post('new_email');
        $error = array();

        if (!$email) {
            $error['new_email'] = self::t('Email không được để trống');
        } else if (!Util::isValidEmail($email)) {
            $error['new_email'] = self::t('Email không đúng định dạng');
        } else if ($email == $user->getEmail()) {
            $error['new_email'] = self::t('Email trùng với email hiện tại');
        }

        if (empty($error)) {
            $owner = \Users::retrieveByEmail($email);
            if ($owner && $owner->isVerifyEmail() && $owner->getId() != $user->getId()) {
                //fuck it has been used
                $error['new_email'] = self::t('Email đã được xác thực bởi tài khoản @%owner%', array(
                    '%owner%' => $owner->getUsername()
                ));
            }
        }

        if (!empty($error)) {
            $ajax->message = self::t('Có lỗi xảy ra');
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->error = $error;
        } else {
            $user->setEmail($email);
            $user->save(false); //Quick save
            $this->dispatch('onChangeUserEmail', new \BackendEvent($this, array(
                'user' => $user
            )));

            $ajax->message = self::t('Thành công');
            $ajax->type = \AjaxResponse::SUCCESS;
        }

        return $this->renderText($ajax->toString());
    }

    public function executeChangePass() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id', 'INT');
        $new_pass = $this->post('new_pass');
        $confirm = $this->post('confirm');

        $error = array();

        if ($new_pass != $confirm) {
            $error['confirm'] = self::t('Mật khẩu xác nhận không khớp');
        }

        if (strlen($new_pass) < 6) {
            $error['new_pass'] = self::t('Mật khẩu yêu cầu tối thiểu %require_length% ký tự', array(
                '%require_length%' => 6
            ));
        }

        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        if (empty($error)) {
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

    public function executeResetSecret() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_SYSTEM_MANAGE)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id', 'INT');
        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }
        $secretKey = \Users::genSecretKey();
        $user->setSecretKey($secretKey);
        $user->save(false); //Quick save

        $this->dispatch('onChangedUserSecretKey', new \BackendEvent($this, array(
            'user' => $user
        )));

        $ajax->message = self::t('Thành công');
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->element = $secretKey;
        return $this->renderText($ajax->toString());
    }

    public function executeSyncAccountant() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_SYSTEM_MANAGE)) {
            $ajax->message = self::t('Bạn không có quyền thao tác này');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('id', 'INT');
        if (!$id || !($user = \Users::retrieveById($id))) {
            $ajax->message = self::t('Tài khoản không tồn tại');
            $ajax->type = \AjaxResponse::ERROR;
            return $this->renderText($ajax->toString());
        }

        try {
            if (!$user->getAccountNo()) {
                $user = \SeuDo\Accountant\Util::createUserAccount($user);
            }

            if (\UsersPeer::syncAccountBalance($user)) {
                $ajax->message = self::t('Đồng bộ thành công');
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->account_no = $user->getAccountNo();
                $ajax->account_blance = $user->getAccountBalance();
            } else {
                $ajax->message = self::t('Có lỗi xảy ra');
                $ajax->type = \AjaxResponse::ERROR;
            }
        } catch (\Exception $e) {
            $ajax->message = self::t('Có lỗi xảy ra');
            $ajax->type = \AjaxResponse::ERROR;
            Logger::factory('business')->error($e->getMessage() ."\nTraces:\n" .$e->getTraceAsString());
        }

        return $this->renderText($ajax->toString());
    }
}