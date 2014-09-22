<?php
namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;

class Management extends BackendBase {

    private $user;

    public function beforeExecute() {
        $this->setTemplate('Seudo');
        parent::beforeExecute();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeSearchCustomer(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $customer = $this->request()->get('customer', 'STRING', '');
            $ajax->customer = $customer;

            $arrCustomer = array();
            if($customer != ""){
                if(is_int($customer)){
                    $arrCustomer[] = (int)$customer;
                }else{
                    $users = \UsersPeer::searchByCodeOrUsername($customer, $customer);
                    if(sizeof($users) > 0){
                        for($u = 0; $u < sizeof($users); $u++){
                            $arrCustomer[] = '"' . $users[$u]->getId() . '"';
                        }
                    }
                }
            }

            $data = "";
            if(sizeof($arrCustomer) > 0){
                $data = implode(",", $arrCustomer);
            }
            $ajax->data = $data;

            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }catch (\Flywheel\Exception $e) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Lỗi kỹ thuật. Vui lòng liên hệ với kỹ thuật để được hỗ trợ!';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeSearchShippingMobile(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $shipping_mobile = $this->request()->get('shipping_mobile', 'STRING', '');

            $arrAddress = array();
            if($shipping_mobile != ""){
                $address = \UserAddress::searchByPhone($shipping_mobile);
                if(sizeof($address) > 0){
                    foreach($address as $adr){
                        $arrAddress[] = '"' . $adr->getId() . '"';
                    }
                }
            }

            $data = "";
            if(sizeof($arrAddress) > 0){
                $data = implode(",", $arrAddress);
            }
            $ajax->data = $data;

            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }catch (\Flywheel\Exception $e) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Lỗi kỹ thuật. Vui lòng liên hệ với kỹ thuật để được hỗ trợ!';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeSearchFreightBill(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            $freight_bill = $this->request()->get('freight_bill', 'STRING', '');

            $arrPackage = array();
            if($freight_bill != ""){
                $packages = \Packages::searchByFreightBill($freight_bill);
                if(sizeof($packages) > 0){
                    for($p = 0; $p < sizeof($packages); $p++){
                        $arrPackage[] = $packages[$p]->getOrderId();
                    }
                }
            }

            $data = "";
            if(sizeof($arrPackage) > 0){
                $data = implode(",", $arrPackage);
            }
            $ajax->data = $data;

            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }catch (\Flywheel\Exception $e) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Lỗi kỹ thuật. Vui lòng liên hệ với kỹ thuật để được hỗ trợ!';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeGetOrders(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $ajax->format = 'JSON';

        try{
            if (!$this->isAllowed(PERMISSION_ORDER_VIEW_ORDER_MANAGE)) {
                $ajax->message = self::t('Bạn không có quyền vào khu vực này');
                return $this->renderText($ajax->toString());
            }

            $condition = array();

            $condition['page'] = $this->request()->get('page', 'INT', 1);

            $condition['status'] = trim($this->request()->get('status', 'STRING', ''));//Trạng thái đơn hàng
            $condition['customer'] = trim($this->request()->get('customer', 'STRING', ''));//Mã khách hoặc tên đăng nhập
            $condition['shipping_mobile'] = trim($this->request()->get('shipping_mobile', 'STRING', ''));//Số điện thoại nhận hàng
            $condition['keyword'] = trim($this->request()->get('keyword', 'STRING', ''));//Từ khóa tìm kiếm chung
            $condition['homeland'] = trim($this->request()->get('homeland', 'STRING', ''));//Nguồn hàng
            $condition['freight_bill'] = trim($this->request()->get('freight_bill', 'STRING', ''));//Mã vận đơn

            $condition['ordering'] = trim($this->request()->get('ordering', 'STRING', ''));//Thời gian sắp xếp [Tạo đơn, Đặt cọc, Mua hàng, ...]
            $condition['search_date'] = trim($this->request()->get('search_date', 'STRING', ''));//Lọc trong vòng
            $condition['search_bill'] = trim($this->request()->get('search_bill', 'STRING', ''));//Lọc theo Tình trạng [Tất cả, Chưa có vận đơn, Có vận đơn]
            $condition['sort_order'] = $this->request()->get('sort_order', 'STRING', 'DESC');//Cách sắp xếp [Cũ trước, mới trước]
            $condition['longer_day'] = trim($this->request()->get('longer_day', 'STRING', ''));//lâu hơn
            $condition['date_from'] = trim($this->request()->get('date_from', 'STRING', ''));//Từ ngày
            $condition['date_to'] = trim($this->request()->get('date_to', 'STRING', ''));//Đến ngày
            $condition['is_complaint_seller'] = $this->request()->get('is_complaint_seller', 'STRING', '');//Đang khiếu nại người bán
            $condition['all'] = $this->request()->get('all', 'INT', 0);//Lấy toàn bộ bản ghi hay không (0 : phân trang; 1: Lấy toàn bộ)
            $condition['sync'] = $this->request()->get('sync', 'INT', 0);//(0 : Không đồng bộ; 1: Đồng bộ dữ liệu)
            $condition['last_modified_time'] = $this->request()->get('last_modified_time', 'STRING', '');
            $condition['seller_aliwang'] = $this->request()->get('seller_aliwang', 'STRING', '');
            $condition['user_origin_site'] = $this->request()->get('user_origin_site', 'STRING', '');

            $condition['current_warehouse'] = $this->request()->get('current_warehouse', 'STRING', '');//Kho hiện tại
            $condition['destination_warehouse'] = $this->request()->get('destination_warehouse', 'STRING', '');//Kho đích

            $condition['warehouse_status_in'] = $this->request()->get('warehouse_status_in', 'STRING', '');//Trạng thái kho
            $condition['warehouse_status_out'] = $this->request()->get('warehouse_status_out', 'STRING', '');//Trạng thái kho

            $condition['is_cpn'] = $this->request()->get('is_cpn', 'STRING', '');
            $condition['is_need_checking'] = $this->request()->get('is_need_checking', 'STRING', '');
            $condition['is_check_wood_crating'] = $this->request()->get('is_check_wood_crating', 'STRING', '');
            $condition['is_complaint'] = $this->request()->get('is_complaint', 'STRING', '');
            $condition['is_have_bill_lading'] = $this->request()->get('is_have_bill_lading', 'STRING', '');
            $condition['is_no_bill_lading'] = $this->request()->get('is_no_bill_lading', 'STRING', '');
            $condition['is_search_by_date'] = $this->request()->get('$is_search_by_date', 'STRING', '');
            $condition['wait_customer_confirm'] = $this->request()->get('wait_customer_confirm', 'STRING', '');

            $condition['no_weight'] = $this->request()->get('no_weight', 'STRING', '');

            $condition['limit'] = $this->request()->get('limit', 'INT', 0);
//            print_r($condition);

            //Kiểm tra thời gian
            if($condition['date_from'] != ""){
                $tmpDateFrom = DateTime::createFromFormat('d/m/Y', $condition['date_from']);
                if(!$tmpDateFrom){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Ngày bắt đầu không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }
            }
            if($condition['date_to'] != ""){
                $tmpDateTo = DateTime::createFromFormat('d/m/Y', $condition['date_to']);
                if(!$tmpDateTo){
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Ngày kết thúc không hợp lệ, vui lòng nhập đúng định dạng';
                    return $this->renderText($ajax->toString());
                }
            }

            $data = \OrderPeer::getListOrders($this, $condition);

            $ajax->message = self::t('OK');

            $ajax->orders = $data['orders'];
            $ajax->total_page = $data['total_page'];
            $ajax->total = $data['total'];
            $ajax->arrOrderId = sizeof($data['arrOrderId']) > 0 ? implode(',', $data['arrOrderId']) : '';

            $ajax->page_size = \Order::PER_PAGE;
            $ajax->page = $condition['page'];
            $ajax->status = $condition['status'];
            $ajax->condition = $condition;
            $ajax->SQL = $data['SQL'];

            $ajax->type = \AjaxResponse::SUCCESS;
            return $this->renderText($ajax->toString());
        }catch (\Flywheel\Exception $e) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Lỗi kỹ thuật. Vui lòng liên hệ với kỹ thuật để được hỗ trợ!';
            return $this->renderText($ajax->toString());
        }
    }

    public function executeDefault() {
        $this->setView('Order/management');

        $page = $this->request()->get('page', 'INT', 1);

        $status = trim($this->request()->get('status', 'STRING', ''));//Trạng thái đơn hàng
        $customer = trim($this->request()->get('customer', 'STRING', ''));//Mã khách hoặc tên đăng nhập
        $shipping_mobile = trim($this->request()->get('shipping_mobile', 'STRING', ''));//Số điện thoại nhận hàng
        $keyword = trim($this->request()->get('keyword', 'STRING', ''));//Từ khóa tìm kiếm chung
        $homeland = trim($this->request()->get('homeland', 'STRING', ''));//Nguồn hàng
        $freight_bill = trim($this->request()->get('freight_bill', 'STRING', ''));//Mã vận đơn

        $ordering = trim($this->request()->get('ordering', 'STRING', ''));//Thời gian sắp xếp [Tạo đơn, Đặt cọc, Mua hàng, ...]
        $search_date = trim($this->request()->get('search_date', 'STRING', ''));//Lọc trong vòng
        $search_bill = trim($this->request()->get('search_bill', 'STRING', ''));//Lọc theo Tình trạng [Tất cả, Chưa có vận đơn, Có vận đơn]
        $sort_order = trim($this->request()->get('sort_order', 'STRING', 'ASC'));//Cách sắp xếp [Cũ trước, mới trước]
        $longer_day = trim($this->request()->get('longer_day', 'STRING', ''));//lâu hơn
        $date_from = trim($this->request()->get('date_from', 'STRING', ''));//Từ ngày
        $date_to = trim($this->request()->get('date_to', 'STRING', ''));//Đến ngày
        $is_complaint_seller = $this->request()->get('is_complaint_seller', 'STRING', '');//Đang khiếu nại người bán
        $all = $this->request()->get('all', 'INT', 0);//Lấy toàn bộ bản ghi hay không (0 : phân trang; 1: Lấy toàn bộ)
        $sync = $this->request()->get('sync', 'INT', 0);//(0 : Không đồng bộ; 1: Đồng bộ dữ liệu)
        $last_modified_time = $this->request()->get('last_modified_time', 'STRING', '');
        $seller_aliwang = $this->request()->get('seller_aliwang', 'STRING', '');
        $user_origin_site = $this->request()->get('user_origin_site', 'STRING', '');

        $current_warehouse = $this->request()->get('current_warehouse', 'STRING', '');//Kho hiện tại
        $destination_warehouse = $this->request()->get('destination_warehouse', 'STRING', '');//Kho đích

        $warehouse_status_in = $this->request()->get('warehouse_status_in', 'STRING', '');//Trạng thái kho
        $warehouse_status_out = $this->request()->get('warehouse_status_out', 'STRING', '');//Trạng thái kho

        $is_cpn = $this->request()->get('is_cpn', 'STRING', '');
        $is_need_checking = $this->request()->get('is_need_checking', 'STRING', '');
        $is_check_wood_crating = $this->request()->get('is_check_wood_crating', 'STRING', '');
        $is_complaint = $this->request()->get('is_complaint', 'STRING', '');
        $is_have_bill_lading = $this->request()->get('is_have_bill_lading', 'STRING', '');
        $is_no_bill_lading = $this->request()->get('is_no_bill_lading', 'STRING', '');
        $is_search_by_date = $this->request()->get('is_search_by_date', 'STRING', '');
        $wait_customer_confirm = $this->request()->get('wait_customer_confirm', 'STRING', '');
        $no_weight = $this->request()->get('no_weight', 'STRING', '');

        $limit = $this->request()->get('limit', 'INT', 0);

        $this->view()->assign(array(
            'status' => $status,
            'is_complaint_seller' => $is_complaint_seller,
            'customer' => $customer,
            'shipping_mobile' => $shipping_mobile,
            'keyword' => $keyword,
            'homeland' => $homeland,
            'ordering' => $ordering,
            'freight_bill' => $freight_bill,
            'page' => $page,
            'search_date' => $search_date,
            'longer_day' => $longer_day,
            'search_bill' => $search_bill,
            'sort_order' => $sort_order,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'all' => $all,
            'sync' => $sync,
            'last_modified_time' => $last_modified_time,
            'limit' => $limit,
            'seller_aliwang' => $seller_aliwang,
            'user_origin_site' => $user_origin_site,
            'current_warehouse' => $current_warehouse,
            'destination_warehouse' => $destination_warehouse,
            'warehouse_status_in' => $warehouse_status_in,
            'warehouse_status_out' => $warehouse_status_out,
            'is_cpn' => $is_cpn,
            'is_search_by_date' => $is_search_by_date,
            'is_need_checking' => $is_need_checking,
            'is_check_wood_crating' => $is_check_wood_crating,
            'is_complaint' => $is_complaint,
            'is_have_bill_lading' => $is_have_bill_lading,
            'is_no_bill_lading' => $is_no_bill_lading,
            'wait_customer_confirm' => $wait_customer_confirm,
            'no_weight' => $no_weight
        ));

        $cookie = Factory::getCookie();
        $version = 'v60';
        $get_init_data = $cookie->read("loaded_" . $this->user->getId() . "_" . $version) == 'OK' ? false : true;
        if( !$cookie->read("loaded_" . $this->user->getId() . "_" . $version) ){
            $cookie->write("loaded_" . $this->user->getId() . "_" . $version, 'OK', 60*60*24*90);
        }
        $this->document()->addJsVar('get_init_data', $get_init_data);
        $this->document()->addJsVar('last_modified_time', \Order::getLastModifiedTime());

        return $this->renderComponent();
    }

    public function executeOutOfStock() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse(\AjaxResponse::ERROR);

        //check permission first
        if (!$this->isAllowed(PERMISSION_ORDER_TRANSITION_OUT_OF_STOCK)) {
            $ajax->message = self::t('Bạn không có quyền thực hiện thao tác này');
            return $this->renderText($ajax->toString());
        }

        $order_id = $this->post('id', 'INT');

        if (!$order_id || !($order = \Order::retrieveById($order_id))) {
            $ajax->message = self::t('Đơn hàng không tồn tại');
            return $this->renderText($ajax->toString());
        }

        if (!in_array($order->getStatus(), array(
            \Order::STATUS_BUYING,
            \Order::STATUS_NEGOTIATING,
            \Order::STATUS_NEGOTIATED,
            \Order::STATUS_BOUGHT,
            \Order::STATUS_BUYER_CONFIRMED,
            \Order::STATUS_SELLER_DELIVERY))) {
            $ajax->message = self::t('Đơn hàng không ở trạng thái cho phép hết hàng');
            return $this->renderText($ajax->toString());
        }

        try {
            $transaction = \OrderPeer::transitOutOfStock($order, "Trả lại đơn hàng {$order->getCode()} hết hàng khi mua");

            if ($transaction) {
                $ajax->message = self::t('Thành công');
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->order = $order->toArray();
                $ajax->transacion = $transaction->getId();
            } else {
                $ajax->message = self::t('Lỗi kỹ thuật! Không thể thực hiện hết hàng, vui lòng thông báo kiểm tra!');
            }

            return $this->renderText($ajax->toString());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}