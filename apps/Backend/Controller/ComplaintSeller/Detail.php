<?php

namespace Backend\Controller\ComplaintSeller;

use Backend\Controller\BackendBase;
use SeuDo\Main;

class Detail extends BackendBase
{
    public function beforeExecute(){
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $complaint_seller_id = (int)$this->get('id');
        $page = $this->request()->get('page', 'INT', 1);

        $document = $this->document();
        $document->title = "Chi tiết khiếu nại người bán";
        $this->setView('ComplaintSeller/detail');

        $complaint_seller = false;
        $complaint_seller = \ComplaintSeller::retrieveById($complaint_seller_id);

        if (!$complaint_seller){
            $this->raise404(self::t('Khiếu nại người bán bạn yêu cầu không tồn tại'));
        }

        $disable_form = false;
        if($complaint_seller->getStatus() == \ComplaintSeller::STATUS_SUCCESS
            || $complaint_seller->getStatus() == \ComplaintSeller::STATUS_FAILURE){
            $disable_form = true;
        }

        $order = \Order::retrieveById($complaint_seller->getOrderId());
        $existOrder = true;
        if (!$order){
//            $this->raise404(self::t('Không tìm thấy đơn hàng'));
            $order = new \Order();
            $existOrder = false;
        }

        //BUYER
        $buyer = \Users::retrieveById($order->getBuyerId());
        $buyer_avatar = $buyer_detail_link = "";
        if ($buyer) {
//            $buyer = $buyer->getAttributes('id,username,code,last_name,first_name');
            $buyer_avatar = \Users::getAvatar32x($buyer);
            $buyer_detail_link = $this->createUrl('user/detail', array('id' => $buyer->getId()));
        }
        //Nhân viên quản lý khiếu nại (tiếp nhận khiếu nại)
        $processed = \Users::retrieveById($complaint_seller->getProcessedBy());
        $processed_avatar = $processed_detail_link = "";
        if($processed){
//            $processed = $processed->getAttributes('id,username,code,last_name,first_name');
            $processed_avatar = \Users::getAvatar32x($processed);
            $processed_detail_link = $this->createUrl('user/detail', array('id' => $processed->getId()));
        }

        $total_pendding_quantity = $total_receive_quantity = $total_order_quantity = 0;
        $items = \OrderPeer::getOrderItem($order->getId());
        foreach((array)$items as $item) {
            $total_pendding_quantity = $item->getPendingQuantity();
            $total_receive_quantity = $item->getReciveQuantity();
            $total_order_quantity = $item->getOrderQuantity();
        }

        $this->view()->assign('total_pendding_quantity', $total_pendding_quantity);
        $this->view()->assign('total_receive_quantity', $total_receive_quantity);
        $this->view()->assign('total_order_quantity', $total_order_quantity);

        $this->view()->assign('complaint_seller', $complaint_seller);
        $this->view()->assign('page', $page);
        $this->view()->assign('order', $order);
        $this->view()->assign('buyer', $buyer);

        if( $existOrder ) {
            $this->view()->assign('order_services', \OrderService::buildOrderServicesArray($order));
        } else {
            $this->view()->assign('order_services', array());
        }


        $this->view()->assign('need_checking', $order->needToChecking());
        $this->view()->assign('check_wood_crating', $order->needToWoodCrating());
        $this->view()->assign('check_fragile', $order->needToFragile());
        $this->view()->assign('is_cpn', $order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM));
        $this->view()->assign('buyer_avatar', $buyer_avatar);
        $this->view()->assign('buyer_detail_link', $buyer_detail_link);
        $this->view()->assign('processed_avatar', $processed_avatar);
        $this->view()->assign('processed_detail_link', $processed_detail_link);
        $this->view()->assign('seller_favicon_site', \Common::getFaviconSite($order->getSellerHomeland()));
        $this->view()->assign('processed', $processed);

        $document->addJsVar("complaint_seller_id", $complaint_seller_id);
        //info current user
        $document->addJsVar('_first_name', $this->user->getFirstName());
        $document->addJsVar('_user_id', $this->user->getId());
        $document->addJsVar('_username', $this->user->getFullName());
        $document->addJsVar('_account', $this->user->getUsername());
        $document->addJsVar('_img_path', \Users::getAvatar32x($this->user));
        $document->addJsVar('disable_form', $disable_form);
        $document->addJsVar('complaint_seller', $complaint_seller->toArray());
        $document->addJsVar('LinkComplaintSellerDetailUrl',  $this->createUrl( 'ComplaintSeller/detail', array('id' => $complaint_seller_id) ) );

        //info order
        $document->addJsVar("order_id", $order->getId());
        $document->addJsVar('LinkListBackendComplaintUrl', $this->createUrl('ComplaintSeller/detail', array('id' => $complaint_seller_id)));
        $document->addJsVar('linkGetListComplaint', $this->createUrl('Complaint/Managerment/GetGirdComplaints'));
        $document->addJsVar('LinkUpdateStatusSuccess', $this->createUrl('ComplaintSeller/Init/UpdateStatusSuccess'));
        $document->addJsVar('LinkUpdateStatusFailure', $this->createUrl('ComplaintSeller/Init/UpdateStatusFailure'));
        $document->addJsVar('LinkUpdateStatusProcess', $this->createUrl('ComplaintSeller/Init/UpdateStatusProcess'));
        $document->addJsVar('linkUpdateInfoComplaintSeller', $this->createUrl('ComplaintSeller/Init/UpdateInfo'));

        return $this->renderComponent();
    }

}
?>