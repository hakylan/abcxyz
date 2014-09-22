<?php
/**
 * Created by PhpStorm.
 * User: hosivan
 * Date: 29/08/2014
 * Time: 10h25 AM
 */

namespace User\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use SeuDo\Main;

class AccumulationScore extends UserBase
{
    private $user;
    private $number_show = 10;

    public function beforeExecute() {
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault() {
        $page = $this->request()->request('page',"INT", 1);

        $document = $this->document();
        $document->title = "Điểm tích lũy";
        $document->addJsVar("linkGetListAccumulationScore", Main::getHomeRouter()->createUrl('user/AccumulationScore/GetList'));
        $document->addJsVar("linkGetInfoLevel", Main::getHomeRouter()->createUrl('user/AccumulationScore/GetInfoLevel'));
        $document->addJsVar("ListAccumulationScoreUrl", Main::getHomeRouter()->createUrl('user/diem-tich-luy'));

        $userInfo = array();
        $userInfo['first_name'] = $this->user->getFirstName();
        $userInfo['shorten_fullname'] = $this->user->getShortenFullName();
        $userInfo['user_id'] = $this->user->getId();
        $userInfo['username'] = $this->user->getUsername();
        $userInfo['avatar'] = \Users::getAvatar32x($this->user);

        $this->view()->assign('userInfo', $userInfo);
        $this->view()->assign('page', $page);

        $this->setView("AccumulationScore/list");

        return $this->renderComponent();
    }

    /**
     * Hàm lấy ra lịch sử điểm tích lũy theo từng khách hàng
     * @return string
     * @throws \Flywheel\Exception
     */
    public function executeGetList() {
        $this->validAjaxRequest();

        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        $this->user = \BaseAuth::getInstance()->getUser();

        try {
            $page = $this->request()->request('page',"INT", 1);

            $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS);
            $ajax->message = 'OK';
            $ajax->data = \MemberScoreHistory::getMemberScoreHistory($this->user->getId(), $page);
            return $this->renderText($ajax->toString());

        } catch (\Exception $e) {
            \SeuDo\Logger::factory('user_accumulation_score')->addError('has error when try get list member score history',array($e->getMessage()));
            $conn->rollBack();
            throw new \Flywheel\Exception('has error when try get list member score history');
        }
    }

    /**
     * Hàm lấy ra thông tin về level của khách hàng
     * @return string
     * @throws \Flywheel\Exception
     */
    public function executeGetInfoLevel() {
        $this->validAjaxRequest();

        $conn = \Flywheel\Db\Manager::getConnection();
        $conn->beginTransaction();
        $this->user = \BaseAuth::getInstance()->getUser();
        $user_id = $this->user->getId();

        try {
            $data = array();
            $ajax = new \AjaxResponse(\AjaxResponse::SUCCESS);
            $ajax->message = 'OK';

            if( $user_id == 0 ) {
                $ajax = new \AjaxResponse(\AjaxResponse::ERROR);
                $ajax->message = 'User không tồn tại!';
                return $this->renderText($ajax->toString());
            }

            $query = \LevelSetting::select();
            $lv_last = (int)$query->count()->execute();

            $check_last_level = true;
            $point = 0;

            //Xem user hiện đang có bao nhiêu điểm trong hệ thống
            $user = \Users::retrieveById($user_id);
            if( !$user instanceof \Users ) {
                $user = new \Users();
            }

            $point = round( $user->getPointMember() );

            //Lấy ra thông tin cấp độ hiện tại của user
            $levelInfo = \LevelSetting::retrieveById( $user->getLevelId() );
            if( !$levelInfo instanceof \LevelSetting ) {
                $levelInfo = new \LevelSetting();
            }

            $level = $levelInfo->getLevel();
            $level_id = $levelInfo->getId();

            //Kiểm tra xem đây có phải cấp độ cuối cùng hay không?
            if( ( $level + 1 ) == $lv_last ) {
                $check_last_level = false;
            }
            $data['check_last_level'] = $check_last_level;

            //Service discount
            $discount_bought = $discount_checking = $discount_transport = $fixed_fee_service = 0;
            $_discount_bought = $_discount_checking = $_discount_transport = $_fixed_fee_service = 0;

            $tmp = $level_id + 1;

            $q = \ServiceDiscount::select();
            $q->andWhere("( level_id = {$level_id} OR level_id = {$tmp} )");
            $lv_current = $q->execute();

            foreach( $lv_current as $lv ) {
                if( $lv instanceof \ServiceDiscount ) {
                    /* Cấp độ hiện tại */

                    //Mua hàng
                    if( $lv->getLevelId() == $level_id && $lv->getService() == \ServiceDiscount::SERVICE_BUYING ) {
                        $discount_bought = $lv->getValue();
                    }

                    //Kiểm hàng
                    if( $lv->getLevelId() == $level_id && $lv->getService() == \ServiceDiscount::SERVICE_BUYING ) {
                        $discount_checking = $lv->getValue();
                    }

                    //Vận chuyển quốc tế
                    if( $lv->getLevelId() == $level_id && $lv->getService() == \ServiceDiscount::SERVICE_SHIPPING_CHINA_VIETNAM ) {
                        $discount_transport = $lv->getValue();
                    }

                    //Phí cố định
                    if( $lv->getLevelId() == $level_id && $lv->getService() == \ServiceDiscount::SERVICE_ORDER_FIXED && $lv->getValue() == 0 ) {
                        $fixed_fee_service = 5000;
                    }

                    /* Cấp độ tiếp theo */

                    //Mua hàng
                    if( $lv->getLevelId() == $tmp && $lv->getService() == \ServiceDiscount::SERVICE_BUYING ) {
                        $_discount_bought = $lv->getValue();
                    }

                    //Kiểm hàng
                    if( $lv->getLevelId() == $tmp && $lv->getService() == \ServiceDiscount::SERVICE_BUYING ) {
                        $_discount_checking = $lv->getValue();
                    }

                    //Vận chuyển quốc tế
                    if( $lv->getLevelId() == $tmp && $lv->getService() == \ServiceDiscount::SERVICE_SHIPPING_CHINA_VIETNAM ) {
                        $_discount_transport = $lv->getValue();
                    }

                    //Phí cố định
                    if( $lv->getLevelId() == $tmp && $lv->getService() == \ServiceDiscount::SERVICE_ORDER_FIXED && $lv->getValue() == 0 ) {
                        $_fixed_fee_service = 5000;
                    }
                }
            }

            //Cấp độ hiện tại của khách hàng
            $data['level'] = $level;
            $data['level_title'] = $levelInfo->getLevelName();

            //Điểm tích lũy của khách hàng
            $data['score'] = $point;

            /* Cấp độ hiện tại */

            //C.khấu phí mua hàng
            $data['discount_bought'] = $discount_bought;

            //C.khấu phí kiểm hàng
            $data['discount_checking'] = $discount_checking;

            //C.khấu phí VC Q.tế:
            $data['discount_transport'] = $discount_transport;

            //Phí DV cố định trên đơn
            $data['fixed_fee_service'] = $fixed_fee_service;

            /* Cấp độ tiếp theo */

            //C.khấu phí mua hàng
            $data['_discount_bought'] = $_discount_bought;

            //C.khấu phí kiểm hàng
            $data['_discount_checking'] = $_discount_checking;

            //C.khấu phí VC Q.tế:
            $data['_discount_transport'] = $_discount_transport;

            //Phí DV cố định trên đơn
            $data['_fixed_fee_service'] = $_fixed_fee_service;


            //level tiếp theo
            $data['_level'] = $level + 1;

            $ajax->data = $data;
            return $this->renderText($ajax->toString());

        } catch (\Exception $e) {
            \SeuDo\Logger::factory('get_info_level_by_user')->addError('has error when try get get info level by user',array($e->getMessage()));
            $conn->rollBack();
            throw new \Flywheel\Exception('has error when try get get info level by user');
        }
    }

}

?>