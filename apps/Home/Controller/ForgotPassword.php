<?php
namespace Home\Controller;
use Flywheel\Db\Type\DateTime;
use Home\Controller\HomeBase;
class ForgotPassword extends HomeBase {
    public function executeDefault() {
        //not need login, cus they used remember password
        $mss = array();
        $email = '';
        $user = '';
        $ajax = new \AjaxResponse();
        if ($this->request()->isPostRequest()) {
            $this->validAjaxRequest();
            $email = $this->request()->post('email_request');
            if (!$email) {
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->element = '_error-email';
                $ajax->message = 'Vui lòng nhập địa chỉ mail hoặc tên đăng nhập';
                return $this->renderText($ajax->toString());
            } else {
                //checking Email existed
                $users = \Users::findByEmail($email);
                if (!$users) {
                    $users = \Users::findByUsername($email);
                }
                if(!$users){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-email';
                    $ajax->message = 'Không tìm thấy tài khoản của bạn trên hệ thống SếuĐỏ!';
                    return $this->renderText($ajax->toString());
                } else {
                    foreach ($users as $u) {
                        if ($u->verify_email) {$user = $u;}
                    }
                    if (!$user) {$user = $users[0];}

                    if (\HomeAuth::getInstance()->isAuthenticated()
                        && \HomeAuth::getInstance()->getUser()->getId() != $user->getId()) {
                        $ajax->type = \AjaxResponse::ERROR;
                        $ajax->element = '_error-email';
                        $ajax->message = 'Bạn đang đăng nhập, tài khoản bạn nhập vào không khớp với tài khoản hiện tại của bạn.
                        Nếu bạn chưa kích hoạt tài khoản này, có thể một người khác đã sử dụng.';
                        return $this->renderText($ajax->toString());
                    }
                }
            }

            if ($user) {
                $email = $user->getEmail();
                $now = new \DateTime();
                $expire = new \DateTime("+ 1 hour");
                $emailActivity = new \EmailActivities();
                $emailActivity->setCode(md5(uniqid() .mt_rand()));
                $emailActivity->setEmail($email);
                $emailActivity->setActivity('RESET_PASSWORD');
                $emailActivity->setParams(json_encode(array('user_id' => $user->getId())));
                $emailActivity->setCreatedTime($now);
                $emailActivity->setExpiredTime($expire);
                if ($emailActivity->save()) {
                    $link = $this->createUrl('forgot_password/confirm', array('t' => $emailActivity->getCode()));
                    //send mail
                    $mail = new \UserMailUtil();
                    if ($mail->sendRequestresetpassword($user->getUsername(), $email, $link, $user->getFullName())) {
                        //it ok
                        $ajax->type = \AjaxResponse::SUCCESS;
                        $ajax->element = '_success-email';
                        $ajax->message = 'Email xác nhận tới địa chỉ <strong>'.$email.'</strong>. Bạn sẽ nhận được một email với hướng dẫn về cách thiết lập một mật khẩu mới. Rất có thể mail đã bị gửi vào mục Spam/Junk Mail, hãy kiểm tra 2 mục này nếu bạn không thấy mail của chúng tôi. Xin cảm ơn!';
                        return $this->renderText($ajax->toString());
                    }
                } else {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->element = '_error-email';
                    $ajax->message = 'Quá trình lỗi không thể thực hiện, vui lòng liên hệ CSKH để được giúp đỡ';
                    return $this->renderText($ajax->toString());
                    /**
                     * @TODO need log here
                     */
                }
            }
        }
    }

    public function executeConfirm() {
        if (\HomeAuth::getInstance()->isAuthenticated()) {
            $this->redirect($this->createUrl('login/logout', array('r' => $this->request()->getUri())));
        }

        $token = trim($this->request()->get('t'));
        $error = array();
        $submit_error = array();
        $dateTime = new \DateTime();

        if (!$token) {
            $error[] = t('Yêu cầu không hợp lệ.');
            /**
             * @TODO logging
             */
        } else {
            /** @var \EmailActivities $emailActivity */
            $emailActivity = \EmailActivities::findOneByCodeAndActivity($token, 'RESET_PASSWORD');
            if (!$emailActivity) {
                $error[] = t('Không tim thấy yêu cầu của bạn!');
            }else if ($emailActivity->getFinish() || (($emailActivity->getExpiredTime()) < $dateTime)) {
                $error[] = t('Yêu cầu đã quá hạn hoặc đã được thực hiện.');
            } else {
                $params = json_decode($emailActivity->getParams());
                $user = \Users::retrieveById($params->user_id);
                if (!$user) {
                    $error[] = t('Tài khoản phát sinh yêu cầu này không tồn tại hoặc đã bị xoá.');
                }
            }
        }

        if (empty($error)) {
            if ($this->request()->isPostRequest()) {
                $password = $this->request()->post('new_pass');
                $confirm = $this->request()->post('confirm_pass');

                if (!$password) {
                    $submit_error['new_pass'] = t('Nhập mật khẩu mới');
                } else {
                    if (mb_strlen($password) < 5) {
                        $submit_error['new_pass'] = t('Mật khẩu ngắn dưới 5 ký tự!');
                    }

                    if ($password != $confirm) {
                        $submit_error['confirm_pass'] = t('Xác nhận mật khẩu không chính xác!');
                    }
                }

                if (empty($submit_error)) {
                    $user->setPassword(\Users::hashPassword($password));
                    $user->beginTransaction();
                    try {
                        if ($user->save(false)) {
                            $emailActivity->setFinish(1);
                            $emailActivity->setFinishedTime($dateTime);
                            $emailActivity->save(false);
                            $user->commit();
                            //logout and redirect
                            $this->redirect($this->createUrl('forgot_password/success'));
                        } else {
                            $submit_error['common'] = t('Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    } catch (\Exception $e) {
                        $user->rollBack();
                    }

                }
            }

            $this->setView('User/change_password');
            $this->view()->assign('submit_error', $submit_error);
        } else {
            $this->view()->assign('error', $error);
            $this->setView('User/confirm_pass_error');
        }
        return $this->renderComponent();
    }

    public function executeSuccess() {
        $this->setView('User/reset_pass_success');
        return $this->renderComponent();
    }
}