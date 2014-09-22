<?php
namespace Backend\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;
use Flywheel\Config\ConfigHandler;
use Flywheel\Session\Session;
use Flywheel\Validator\Util;
use SeuDo\Permission;

class User extends BackendBase {
    public $maxRecordPerPage = 30;

    /** @var \Users */
    public $auth;

    public function beforeExecute() {
        parent::beforeExecute();
        $this->auth = \BackendAuth::getInstance()->getUser();
    }

    private function _refuseEditHimself(\Users $user) {
        if ($this->auth && $this->auth->getId() == $user->getId()) {
            Permission::getInstance()->setDenied(PERMISSION_USER_INFO_EDIT);
        }
    }

    public function executeSearch() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_USER_VIEW)) { // check permission first
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền truy cập khu vực này");
            return $this->renderText($ajax->toString());
        }

        $query = \Users::select();

        $keyword = $this->get('keyword');
        $section = $this->get('section');
        $status = $this->get('status');
        $mobile_no = $this->get('mobile');
        $pre = $this->get('pre');
        $ordering = $this->get('ordering', 'username');
        $sort = $this->get('sort', 'ASC');
        $not_found = false;

        if ($keyword) {
            if (is_int($keyword)) {
                $query->andWhere('`id` = :keyword');
                $query->setParameter(':keyword', $keyword, \PDO::PARAM_INT);
            } else if (Util::isValidEmail($keyword)) {
                $query->andWhere('`email` = :keyword');
                $query->setParameter(':keyword', $keyword, \PDO::PARAM_STR);
            } else {
                $keyword = explode(' ', $keyword);
                foreach($keyword as $k) {
                    $k = trim($k);
                    if ($k) {
                        $query->orWhere('`username` LIKE :keyword')
                            ->orWhere('`code` = :keyword_code')
                            ->orWhere('`first_name` LIKE :keyword')
                            ->orWhere('`last_name` LIKE :keyword');

                        $query->setParameter(':keyword', "%$k%", \PDO::PARAM_STR);
                        $query->setParameter(':keyword_code', $k, \PDO::PARAM_STR);
                    }
                }
            }
        }

        if ($pre) {
            if ($pre == '0-9') {
                for ($i = 0; $i < 10; ++$i) {
                    if ($i == 0) {
                        $query->andWhere('`username` LIKE "0%"');
                    } else {
                        $query->orWhere('`username` LIKE "' .$i .'%"');
                    }
                }
            } else {
                $query->andWhere('`username` LIKE :pre')
                    ->setParameter(':pre', $pre.'%', \PDO::PARAM_STR);
            }
        }

        if ($section) {
            $query->andWhere("`section` = :section")
                ->setParameter(':section', $section, \PDO::PARAM_STR);
        }

        if ($status) {
            $query->andWhere('`status` = :status')
                ->setParameter(':status', $status, \PDO::PARAM_STR);
        }

        if ($mobile_no) {
            $userMobiles = \UserMobiles::findByMobile($mobile_no);
            if ($userMobiles) {
                $uid = array();
                foreach ($userMobiles as $userMobile) {
                    $uid[] = $userMobile->getUserId();
                }
                $query->andWhere('`id` IN(' .implode(',', $uid) .')');
            } else { //not found
                $not_found = true;
            }
        }

        //paging
        $page = $this->get('page', 'INT', 1);

        if ($not_found) {//not found
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->users = array();
            $ajax->total = 0;
            $ajax->page_size = $this->maxRecordPerPage;
            $ajax->page = $page;
            return $this->renderText($ajax->toString());
        }

        $countQuery = clone $query;
        $total = $countQuery->count('id')->execute();

        $query->setMaxResults($this->maxRecordPerPage)
            ->setFirstResult(($page-1) * $this->maxRecordPerPage);

        switch ($ordering) {
            case 'last_login_time':
                $query->orderBy('last_login_time', $sort);
                break;
            case 'joined_time':
                $query->orderBy('joined_time', $sort);
                break;
            case 'account_balance':
                $query->orderBy('account_balance', $sort);
                break;
            default:
                $query->orderBy('username');
        }

        /** @var \Users[] $users */
        $users = $query->execute();
        $result = array();

        if ($users) {
            foreach($users as $user) {
                $t = $user->toArray();
                $t['avatar'] = \Users::getAvatar32x($user);
                $t['detail_link'] = $this->createUrl('user/detail', array('id' => $user->getId()));
                $t['section'] = (($user->getSection() == \Users::SECTION_CUSTOMER)? self::t('Khách hàng') : self::t('Nhân viên'));
                $t['short_fullname'] = $user->getShortenFullName();
                $mobiles = $user->getMobiles();
                $t['mobiles'] = array();
                if (is_array($mobiles)) {
                    foreach($mobiles as $mobile) {
                        $t['mobiles'][] = $mobile->toArray();
                    }
                }

                switch ($user->getStatus()) {
                    case \Users::STATUS_ACTIVE:
                        $t['status'] = self::t('Kích hoạt');
                        break;
                    case \Users::STATUS_INACTIVE:
                        $t['status'] = self::t('Chưa kích hoạt');
                        break;
                    case \Users::STATUS_LOCK:
                        $t['status'] = self::t('Đang bị khóa');
                        break;
                    case \Users::STATUS_BAN:
                        $t['status'] = self::t('Cấm vĩnh viễn');
                        break;
                    case \Users::STATUS_DELETE:
                        $t['status'] = self::t('Đã xóa');
                        break;
                }

                //time
                $t['joined_time'] = $user->getJoinedTime()->format('H:i d/m/Y');
                $t['last_login_time'] = $user->getLastLoginTime()->format('H:i d/m/Y');
                unset($t['password']);
                unset($t['payment_pass']);
                unset($t['secret_key']);
                $result[] = $t;
            }
        }

        unset($users);

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->users = $result;
        $ajax->total = $total;
        $ajax->page_size = $this->maxRecordPerPage;
        $ajax->page = $page;
        return $this->renderText($ajax->toString());
    }

    //List users
    public function executeDefault() {
        if (!$this->isAllowed(PERMISSION_USER_VIEW)) {
            $this->raise403(self::t('Bạn không có quyền vào khu vực này'));
        }

        $this->setView('User/default');
        $this->view()->assign(array(
            'keyword' => $this->get('keyword'),
            'section' => $this->get('section'),
            'status' => $this->get('status'),
            'mobile' => $this->get('mobile'),
            'pre' => $this->get('pre'),
            'page' => $this->get('page', 'INT', 1),
            'ordering' => $this->get('ordering', 'STRING', 'username'),
            'sort' => $this->get('sort', 'STRING', 'ASC')
        ));

        return $this->renderComponent();
    }

    public function executeAdd() {
        $user = new \Users();
        $error = array();
        $this->setView('User/form');

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $this->raise403('Bạn không có quyền vào khu vực này');
        }

        if ($this->request()->isPostRequest()) {
            if ($this->_save($user, $this->request()->post('user', 'ARRAY', array()), $error)) {
                if (\Users::SECTION_CUSTOMER == $user->getSection()) {
                    \SeuDo\Accountant\Util::createUserAccount($user);
                }
                $this->dispatch('onAddUser', new \BackendEvent($this, array(
                    'user' => $user
                )));
                //success
                Session::getInstance()->setFlash('user_message', self::t('Tạo người dùng thành công'));
                $this->redirect($this->createUrl('user/detail', array(
                    'id' => $user->getId()
                )));
            }
        }

        $this->view()->assign(array(
            'user' => $user,
            'error' => $error
        ));
        return $this->renderComponent();
    }

    public function executeEdit() {
        $error = array();
        $this->setView('User/form');

        $id = $this->get('id', 'INT', 0);
        if (!$id || !($user = \Users::retrieveById($id))) {
            $error['not_found'] = true;
        } else {
            $this->_refuseEditHimself($user);
        }

        if (!$this->isAllowed(PERMISSION_USER_INFO_EDIT)) {
            $this->raise403('Bạn không có quyền vào khu vực này');
        }

        if ($this->request()->isPostRequest()) {
            if ($this->_save($user, $this->request()->post('user', 'ARRAY', array()), $error)) {
                $this->dispatch('onUpdateUserInfo', new \BackendEvent($this, array(
                    'user' => $user
                )));
                //success
                Session::getInstance()->setFlash('user_message', self::t('Tạo người dùng thành công'));
                $this->redirect($this->createUrl('user/detail', array(
                    'id' => $user->getId()
                )));
            }
        }

        $this->view()->assign(array(
            'user' => $user,
            'error' => $error
        ));
        return $this->renderComponent();
    }

    private function _save(\Users $user, $data = array(), &$error = array()) {
        $old = clone $user;
        /**
         * @TODO fuck it, cus handling data as post ARRAY so could not trim whitespace as handling post as STRING
         */
        $data['email'] = (isset($data['email']))? trim($data['email']) : '';
        $data['username'] = (isset($data['username']))? trim($data['username']) : $user->getUsername();
        $data['first_name'] = trim($data['first_name']);
        $data['last_name'] = trim($data['last_name']);
        $data['birthday'] = (isset($data['birthday']))? trim($data['birthday']) : '';

        if(isset($data['birthday'])) {
            $birthday = DateTime::createFromFormat('d/m/Y', trim($data['birthday']));

            if(!$birthday){
                $error['users.birthday'] = self::t('Ngày sinh nhập không đúng định dạng');
                $data['birthday'] = $user->getBirthday();
            }else{
                $data['birthday'] = $birthday->format('Y-m-d');
            }

        }

        if ($data['email'] == '') {
            unset($data['email']);
        }

        $user->hydrate($data);
        if ($user->isNew()) {
            if (!Util::isValidUsername($data['username'])) {
                $error['users.username'] = t('Tên đăng nhập không đúng định dạng');
            } else if (\Users::retrieveByUsername($data['username'])) {
                $error['users.username'] = t('Tên đăng nhập đã được sử dụng!');
            }

            if (!Util::isValidEmail($data['email'])) {
                $error['users.email'] = t('Email không đúng định dạng');
            } elseif (\UsersPeer::checkIsTakenEmail($data['email'])) {
                $error['users.email'] = t('Email đã được sử dụng');
            }

            //check password
            $password = $data['password'];
            $confirm = $data['confirm_pass'];

            if (strlen($password) < 6) {
                $error['users.password'] = self::t('Mật khẩu tối thiểu 6 ký tự');
            } else {
                if ($confirm != $password) {
                    $error['users-confirm-pass'] = self::t('Xác nhận mật khẩu không khớp');
                    $error['users-password'] = self::t('Xác nhận mật khẩu không khớp');
                }
            }
        } else {
            /**
             * @TODO cus hydrate function always set column was modified
             * so we could not used method @see \Flywheel\Model\ActiveRecord::isColumnModified()
             */
            if ($old->getEmail() != $user->getEmail()) { // email changed
                if ($user->getEmail()) {//email was set
                    if (!Util::isValidEmail($data['email'])) {
                        $error['users.email'] = t('Email không đúng định dạng');
                    } elseif (\UsersPeer::checkIsTakenEmail($data['email'])) {
                        $error['users.email'] = t('Email đã được sử dụng');
                    }

                    if (!isset($error['users.email'])) {
                        $user->setVerifyEmail(false);
                    }
                }
            }
        }

        if (!$data['last_name']) {
            $error['users.last_name'] = self::t('Họ không được để trống');
        }

        if (!$data['first_name']) {
            $error['users.first_name'] = self::t('Tên không được để trống');
        }

        if (empty($error)) {
            if ($user->isNew()) {
                $user->setPassword(\Users::hashPassword($data['password']));
            }

            if ($user->save()) {
                return true;
            } else {
                foreach ($user->getValidationFailures() as $validationFailure) {
                    $error[$validationFailure->getColumn()] = $validationFailure->getMessage();
                }
            }

        }
        return false;
    }
}