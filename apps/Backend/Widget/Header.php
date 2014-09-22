<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;
use Backend\Controller\BackendBase;

class Header extends Widget{

    public function begin(){
        $this->viewPath = Base::getApp()->getController()->getTemplatePath() .DIRECTORY_SEPARATOR .'Widget' .DIRECTORY_SEPARATOR;
        $this->viewFile = "Header";

        $controller = Base::getApp()->getController();

        $items = array();

        if($controller->isAllowed(PERMISSION_USER_VIEW))
        {
            $items['member'][] = array(
                'label' => 'Quản lý thành viên',
                'url' => 'user/default'
            );
        };
        if($controller->isAllowed(PERMISSION_USER_INFO_EDIT))
        {
            $items['member'][] = array(
                'label' => 'Thêm mới thành viên',
                'url' => 'user/add'
            );
        };
        if($controller->isAllowed(PERMISSION_ROLE_VIEW))
        {
            $items['member'][] = array(
                'label' => 'Quản lý nhóm nhân viên',
                'url' => 'role'
            );
        };
        if($controller->isAllowed(PERMISSION_SYSTEM_EXCHANGE_MANAGE))
        {
            $items['system'][] = array(
                'label' => ' Quản lý tỷ giá',
                'url' => 'exchange_rate/default'
            );
        };
        if($controller->isAllowed(PERMISSION_SYSTEM_LOCATION_MANAGE))
        {
            $items['system'][] = array(
                'label' => 'Location',
                'url' => 'location'
            );
        };
        if($controller->isAllowed(PERMISSION_ORDER_VIEW_ORDER_MANAGE))
        {
            $items['orders'][] = array(
                'label' => 'Quản lý đơn hàng',
                'url' => 'order/management'
            );
        };
        if($controller->isAllowed(PERMISSION_ORDER_VIEW_PURCHASE_MANAGE))
        {
            $items['orders'][] = array(
                'label' => 'Quản lý mua hàng',
                'url' => 'purchase_manage/default'
            );
        };
        if($controller->isAllowed(PERMISSION_PURCHASE_ORDER) || $controller->isAllowed(PERMISSION_ORDER_PAYMENT))
        {
            $items['orders'][] = array(
                'label' => 'Mua hàng & thanh toán',
                'url' => 'order/purchase'
            );
        };
        if($controller->isAllowed(PERMISSION_ORDER_ADD_FREIGHT_BILL) || $controller->isAllowed(PERMISSION_ORDER_EDIT_FREIGHT_BILL))
        {
            $items['orders'][] = array(
                'label' => 'Quản lý mã vận đơn',
                'url' => 'order/freight_bill'
            );
        };

        if($controller->isAllowed(PERMISSION_ORDER_CHANGE_DELIVERY))
        {
            $items['delivery'][] = array(
                'label' => 'Quản lý giao hàng',
                'url' => 'delivery_manage/default'
            );
        };

        if($controller->isAllowed(PERMISSION_DELIVERY_VIEW_BILL_MANAGE))
        {
            $items['delivery'][] = array(
                'label' => 'Quản lý phiếu giao hàng',
                'url' => 'delivery/bill_manage/default'
            );
        };

        if($controller->isAllowed(PERMISSION_UPLOAD_BARCODE)){
            $items['operation'][] = array(
                'label' => 'Upload file mã vạch',
                'url' => 'barcode/file/upload_page'
            );
        }

        if($controller->isAllowed(PERMISSION_VIEW_BARCODE_TRACKING)){
            $items['operation'][] = array(
                'label' => 'Lịch sử mã vạch',
                'url' => 'barcode/history'
            );
        }

        if($controller->isAllowed(PERMISSION_VIEW_UPLOADED_BARCODE_SCAN_FILES)){
            $items['operation'][] = array(
                'label' => 'File bắn mã vạch',
                'url' => 'barcode/file/history'
            );
        }

        $total_complaint = \Complaints::getTotalComplaintByStatus();
        $show_total_complaint = $total_complaint > 0 ? '<span class="font-red-new pull-right">(' . $total_complaint . ')</span>' : '';
        if($controller->isAllowed(PERMISSION_COMPLAINT_VIEW_LIST)){
            $items['complaint'][] = array(
                'label' => 'Khiếu nại dịch vụ ' . $show_total_complaint,
                'url' => 'complaint/managerment'
            );
        }


        $total_complaint_seller = \ComplaintSeller::getTotalStatus();
        $show_total_complaint_seller = $total_complaint_seller > 0 ? '<span class="font-red-new pull-right">(' . $total_complaint_seller . ')</span>' : '';
        if($controller->isAllowed(PERMISSION_COMPLAINT_SELLER_VIEW_LIST)){
            $items['complaint'][] = array(
                'label' => 'Khiếu nại người bán ' . $show_total_complaint_seller,
                'url' => 'ComplaintSeller/managerment'
            );
        }

        $this->items = $items;

    }
    public function html() {
        $this->begin();
        $this->fetchViewPath();
        $this->fetchViewFile();

        $widget = Factory::getWidget('\Flywheel\Html\Widget\Menu', $this->getRender());

        if (isset($this->items)) {
            $widget->items = $this->items;
        }
        $widget->begin();

        $widget->viewFile = $this->viewFile;
        $widget->viewPath = $this->viewPath;

        return $widget->render(array(
            'items'=>$widget->items
        ));
    }

}