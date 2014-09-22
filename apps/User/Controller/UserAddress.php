<?php
namespace User\Controller;

use SeuDo\Main;

class UserAddress extends UserBase
{
    public $logger = null;
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->logger = \UserAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $this->setLayout("address");
        $this->setView("UserAddress/default");
        $document = $this->document();
        $document->title = "Quản lý địa chỉ nhận hàng";
        $document->addJsVar("checkout_address",Main::getHomeRouter()->createUrl('CheckoutAddress'));
        $province_list = \Locations::findByType(\Locations::LOCATION_STATE);
        $this->view()->assign('province_list', $province_list);
        return $this->renderComponent();
    }
}