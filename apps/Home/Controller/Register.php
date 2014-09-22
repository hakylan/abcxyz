<?php
namespace Home\Controller;

use Flywheel\Filesystem\Uploader;
use SeuDo\Logger;
use SeuDo\Main;
use \SeuDo\SFS\Client;
use \SeuDo\SFS\Upload;
use \Flywheel\Validator\Util;
use Flywheel\Factory;
use \Flywheel\Session\Session;
use Home\Controller\HomeBase;
use \Flywheel\Config\ConfigHandler;

class Register extends HomeBase
{

    public function beforeExecute()
    {
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('afterRegister', array(new \HomeEvent(), 'afterRegister'));
        $eventDispatcher->addListener('beginRegister', array(new \HomeEvent(), 'afterRegister'));
    }

    public function executeDefault()
    {
        $homeAuth = new \HomeAuth();
        if ($homeAuth->isAuthenticated()) {
            $back = $this->request()->get('r');
            $back = (null != $back) ? urldecode($back) : '/';
            $back_url = $this->request()->get('url');
            if (!empty($back_url)) {
                $this->redirect(base64_decode($back_url));
            }
            $this->redirect($back);
        }
        if ($this->request()->isPostRequest()) {
            return $this->executeRegister();
        }
        $states = \Locations::findByType('STATE');
        $this->view()->assign('states', $states);
        $this->setView('Register/default');
        return $this->renderComponent();
    }

    public function executeChooseDistrict()
    {
        $id = $this->request()->get('id');
        $streets = '';
        $location = new \Locations();
        $datas = $location->get(array(
            'condition' => array(
                'parent_id=' . $id
            ),
            'order_by' => 'id asc'
        ));
        if ($datas) {
            $streets .= "<option value='-1'>Quận/Huyện (chưa chọn)</option>";
            foreach ($datas as $data) {
                $streets .= "<option value=" . $data->getId() . ">" . $data->getLabel() . "</option>";
            }
        } else {
            $streets .= "<option value='-1'>Quận/Huyện (chưa có)</option>";
        }
        return $this->renderText(json_encode($streets));
    }

    public function executeCheckUsername()
    {

        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();

        $nameForUser = $this->request()->post('name');

        $nameForUser = strtolower($nameForUser);

        if ($this->_inValidUsername($nameForUser) != true) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập này không được sử dụng';
            return $this->renderText($ajax->toString());
        }

        if (Util::isValidUsername($nameForUser) != 1) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập từ 3-15 ký tự và không chứa ký tự đặc biệt!';
            return $this->renderText($ajax->toString());
        }

        $user = \Users::retrieveByUsername(trim($nameForUser));

        if (!empty($user) || $user != false) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập đã tồn tại.';
            return $this->renderText($ajax->toString());
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = 'Tên đăng nhập hợp lệ';
        return $this->renderText($ajax->toString());
    }

    public function executeRegister()
    {
        $this->dispatch('beginRegistry', new \HomeEvent($this, array()));

        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $data = $this->request()->post('input', 'ARRAY', array());
        $data['joined_time'] = new \DateTime();
        $data['last_login_time'] = $data['joined_time'];
        $data['modified_time'] = $data['joined_time'];
        $data['username'] = strtolower($data['username']);

        if (isset($data['tt_id']) && isset($data['qh_id']) && isset($data['detail_address'])
            && $data['tt_id'] > 0 && $data['qh_id'] > 0 && $data['detail_address'] != ''
        ) {
            $province = \Locations::findOneById($data['tt_id']);
            $district = \Locations::findOneById($data['qh_id']);

            $data['tt_address'] = $province->getLabel();
            $data['qh_address'] = $district->getLabel();
        }

        $user = \Users::retrieveByUsername(trim($data['username']));

        if (!empty($user) || $user != false) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập đã tồn tại.';
            return $this->renderText($ajax->toString());
        }

        if (Util::isValidUsername($data['username']) != 1) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập từ 3-15 ký tự và không chứa ký tự đặc biệt';
            return $this->renderText($ajax->toString());
        }

        if ($this->_inValidUsername($data['username']) != 1) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-username';
            $ajax->message = 'Tên đăng nhập này không được sử dụng';
            return $this->renderText($ajax->toString());
        }

        if (strlen($data['password']) < 6) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-password';
            $ajax->message = 'Mật khẩu từ 6 ký tự trở lên.';
            return $this->renderText($ajax->toString());
        }
        if ($data['password'] != $data['repassword']) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-password';
            $ajax->message = 'Mật khẩu không khớp.';
            return $this->renderText($ajax->toString());
        }
        if (Util::isValidEmail($data['email']) != 1) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-email';
            $ajax->message = 'Email không hợp lệ';
            return $this->renderText($ajax->toString());
        }
        $user_email = \Users::retrieveByEmail($data['email']);
        if ($user_email && ($user_email->getVerifyEmail()==1)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->element = 'error-email';
            $ajax->message = 'Email này đã được đăng ký, vui lòng sử dụng email khác.';
            return $this->renderText($ajax->toString());
        }

        $data['password'] = \Users::hashPassword($data['password']);

        if ($this->_save(new \Users(), $data)) {
            $this->dispatch('beginRegistry', new \HomeEvent(array('user' => $user)));

            $homeAuth = \HomeAuth::getInstance();
            $homeAuth->authenticate($data['username'], $data['repassword'], true);

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->element = 'success';
            $ajax->message = 'Trong 3-5 phút tới bạn sẽ nhận được một email tới ' . $data['email'] . ' Vui lòng kiểm tra hòm mail để kích hoạt tài khoản. 
                Rất có thể mail đã bị gửi vào mục Spam/Junk Mail, hãy kiểm tra 2 mục này nếu bạn không thấy mail của chúng tôi. Xin cảm ơn!';
            return $this->renderText($ajax->toString());
        }

        $ajax->type = \AjaxResponse::WARNING;
        $ajax->element = 'error';
        $ajax->message = 'Hiện tại không thể đăng ký, vui lòng liên hệ với CSKH để được giúp đỡ!';
        return $this->renderText($ajax->toString());
    }

    private function _save(\Users $user, $data = array())
    {
        $user->hydrate($data);
        if ($user->save()) {
            // Dispatch event
            $this->dispatch('afterRegister', new \SeuDo\Event\User($user));

            \SeuDo\Accountant\Util::createUserAccount($user);

            $emailActivity = new \EmailActivities();
            $activity = $emailActivity->setNewActivitis($user->getEmail(), $user->getId());

            if ($activity) {
                $t = base64_encode($user->getId() . '-' . ($activity->getCode()));
                $link = Main::getHomeUrl() . 'register/success_verify?t=' . $t;

                $userMailUtil = new \UserMailUtil();
                $userMailUtil->pushVerifyEmail($user->getUsername(), $user->getEmail(), $link, $user->getFullName());
            }

            if (isset($data['yahoo']) && $data['yahoo'] != '') \UserProfiles::setUserProfile($user->getId(), 'yahoo', $data['yahoo']);

            if (isset($data['skype']) && $data['skype'] != '') \UserProfiles::setUserProfile($user->getId(), 'skype', $data['skype']);

            //Set UserAddress
            if (isset($data['tt_id']) && isset($data['qh_id']) && isset($data['detail_address'])
                && $data['tt_id'] > 0 && $data['qh_id'] > 0 && $data['detail_address'] != ''
            ) {
                $addressUser = new \UserAddress();
                $addressUser->setUserId($user->getId());
                $addressUser->setDetail($data['detail_address']);
                $addressUser->setDistrictId($data['qh_id']);
                $addressUser->setProvinceId($data['tt_id']);
                $addressUser->setReciverName($data['first_name'] . ' ' . $data['last_name']);
                $addressUser->setIsDefault(1);

                $addressUser->setIsDefault(1);
                $addressUser->setCreatedTime(date('Y-m-d'), time());
                $addressUser->setUpdatedTime(date('Y-m-d'), time());
                $addressUser->save();
            }
            $this->dispatch('afterRegister', new \HomeEvent(array('user' => $user)));
            return true;
        }
        foreach ($user->getValidationFailures() as $validationFailure) {
            $error[$validationFailure->getColumn()] = $validationFailure->getMessage();
        }
        return false;
    }

    public function executeUploadAvatar()
    {
        $homeAuth = \HomeAuth::getInstance();
        if ($homeAuth->isAuthenticated() == false) {
            $this->redirect($this->document()->getPublicPath() . '../login?r=' . $this->request()->getUri());
        }
        $user = $homeAuth->getUser();
        $this->view()->assign(
            array(
                'user' => $user,
            )
        );
        $this->setView('Register/up_avatar');
        return $this->renderComponent();
    }

    public function executeUploadAvatarStep2()
    {
        $error = '';
        $host = Main::getHomeUrl();
        $path = PUBLIC_DIR."public".DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR;
        if ($this->request()->isPostRequest()) {
            $fileUpload = new Uploader($path,'photoimg');

            $fileUpload->setMaximumFileSize(8);//8mb
            $fileUpload->setFilterType('.jpg, .jpeg, .png, .bmp, .gif');
            $fileUpload->setIsEncryptFileName(true);

            if ($fileUpload->upload('photoimg')) {
                $data = $fileUpload->getData();
                echo "<img id='draggable' src='" . $host . "public/temp/" . $data['file_name']. "' class='preview'>";
                echo "<input type='hidden' name='filename' value='" . $host . "public/temp/" . $data['file_name'] ."'>";
                exit;
            } else {
                $error = $fileUpload->getError();
                echo $error[0];
                exit;
            }
        }
    }

    public function executeUploadAvatarStep3() {
        $user = \HomeAuth::getInstance()->getUser();

        if ($this->request()->isPostRequest()) {
            $this->validAjaxRequest();
            if($this->request()->post('filename')){
                $urlAvatar = $this->request()->post('filename');
            }else{
                $urlAvatar = $this->request()->post('filenameUrl');
            }

            $image_info = getimagesize($urlAvatar);

            if(empty($image_info)){
                $resporn = \AjaxResponse::responseError('Ảnh tải lên có lỗi, vui lòng chọn hình ảnh khác!');
                return $this->renderText($resporn);
            }

            $ext = strrchr($urlAvatar, '.');
            $ext = strtolower($ext);
            $filename = uniqid() . '_' . time() . $ext;

            $locationX = $this->request()->post('locationX');
            $locationY = $this->request()->post('locationY');
            $width = $this->request()->post('sizeWidth');
            $height = $this->request()->post('sizeHeight');
            if(($width=='' && $height=='') || empty($width) || empty($height)){
                return $this->renderText(\AjaxResponse::responseError('Không tìm thấy hình ảnh, vui lòng chọn hình ảnh khác!'));
            }
            $sizeCrop = $width-$height;
            if($sizeCrop<0){$sizeCrop *=-1; }
            $sizeResign = ($width+$height-$sizeCrop)/2;
            $locationX += 0;$locationY += 0;
            $locationX = floor($locationX*(-1)*$sizeResign/160);
            $locationY = floor($locationY*(-1)*$sizeResign/160);

            try {
                $sfs = Client::getInstance();
                $uploader = new Upload('useravatar/' . $user->getModifiedTime()->format('mY'));
                $uploader->setUrl($urlAvatar);
                $uploader->setFileName($filename);
                if ($sizeCrop>0) {
                    $uploader->addTransformation('crop', array(
                        'x' => $locationX,
                        'y' => $locationY,
                        'w' => $sizeResign,
                        'h' => $sizeResign
                    ));
                } else {
                    $uploader->addTransformation('square', array(
                        'w' => $sizeResign
                    ));
                }
                if ($sfs->upload($uploader)) {
                    $avatar = json_decode($sfs->getResponse());
                    if ($avatar) {
                        $setAvatar = $avatar->file;
                        $userAvatar = \Users::findOneById($user->getId());
                        $userAvatar->setAvatar($setAvatar);
                        $checkSave = $userAvatar->save();
                        if ($checkSave) {
                            $sfsConfig = ConfigHandler::get('sfs');
                            if(!$sfsConfig){
                                /*TO DO log here*/
                            }
                            $sfsUrl = $sfsConfig['service_url'];
                            $ajax = new \AjaxResponse();
                            $ajax->type = \AjaxResponse::SUCCESS;
                            $ajax->element = $sfsUrl.'/'.$setAvatar;
                            $ajax->message = 'Chọn ảnh đại diện thành công!';
                            return $this->renderText($ajax->toString());
                        }
                    }
                } else {
                    Logger::factory('system')->error('Something went wrong: '. $sfs->getResponse());
                    return $this->renderText(\AjaxResponse::responseError('Máy chủ bận, không thể tải ảnh lên lúc này!'));
                }

            } catch (\Exception $e) {
                Logger::factory('system')->error($e->getMessage() .' at:' .$e->getFile() .' in:' .$e->getLine());
                throw $e;
            }
        }
        return $this->renderText(\AjaxResponse::responseError('Invalid request!'));
    }

    public function executeRegisterSuccess()
    {
        $this->setView('Register/register_success');
        return $this->renderComponent();
    }


    public function executeSuccessVerify()
    {
        $now = new \DateTime();
        $t = $this->request()->get('t');
        $deActive = base64_decode($t);
        $aboutActive = explode("-", $deActive);
        $id_user = trim($aboutActive[0]);
        $code = trim($aboutActive[1]);

        // Check validate code for table Email_Activities
        $ts = time();
        $activities = \EmailActivities::findOneByCode($code);
        if (!$activities || $activities->getCode() != $code || (strtotime($activities->getExpiredTime())) < $ts) {
            $this->setView('Register/error_active');
            return $this->renderComponent();
        }
        $user = \Users::findOneById($id_user);
        if (isset($user) && isset($activities)) {
            //Set active and verify Email
            $user->setVerifyEmail(1);
            $user->setStatus('ACTIVE');
            $user->save();

            $activities->setFinish(1);
            $activities->setFinishedTime($now);
            $activities->save();


            $link = Main::getHomeUrl();
            $checkSend = \UserMailUtil::sendWelcomeEmail($user->getUsername(), $user->getEmail(), $link, $user->getFullName());
            if ($checkSend == true) {
                $this->setView('Register/success_verify');
                $this->view()->assign('username', $user->getUsername());
                return $this->renderComponent();
            }
        }
    }

    private function _inValidUsername($username){

        if(!$username || $username==''){
            return false;
        }
        $ban_username = array();

        $ban_username = require_once ROOT_PATH .'/ban_username.php';

        if(empty($ban_username)){
            return false;
        }

        if(in_array($username,$ban_username)){
            return false;
        }

        return true;
    }


}