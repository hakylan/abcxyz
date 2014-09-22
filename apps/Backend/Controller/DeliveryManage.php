<?php

namespace Backend\Controller;

use Backend\Controller\BackendBase;
use Flywheel\Db\Query;
use Flywheel\Db\Type\DateTime;
use Flywheel\Exception;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use SeuDo\Main;
use SeuDo\Logger;
use SeuDo\BarcodeFile;
use Flywheel\Filesystem\Uploader;
use Flywheel\Util\Folder;
use Flywheel\Event\Event;

class DeliveryManage extends BackendBase
{
    /**
     * @var \Users
     */
    private $user = null;

    /**
     * @var \SeuDo\Logger::factory("");
     */
    private $logger = null;

    private $number_show = 60;
    public $is_public_profile = false;

    public function beforeExecute()
    {
        $this->setTemplate("Seudo");
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
        $this->logger = Logger::factory("delivery_management");
        if ($this->isAllowed(PERMISSION_PUBLIC_PERSONAL_INFO)) {
            $this->is_public_profile = true;
        }
    }

    public function executeDefault(){
        if(!$this->isAllowed(PERMISSION_DELIVERY_VIEW)){
            $this->raise403();
            exit;
        }
        $this->setView("Delivery/default");
//        $this->setLayout("print");
        $page = $this->request()->get("page","INT",1);
        $warehouse = $this->request()->get("warehouse","STRING","all");
        $address = $this->request()->get("address","STRING","");
        $customer = $this->request()->get("customer","STRING","");
        $page = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->number_show;

        $query = \Order::read()->select("user_address_id")->andWhere("status = '".\Order::STATUS_WAITING_FOR_DELIVERY."'");
        $query->orWhere("status = '".\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY."'");
        $query->groupBy("user_address_id");
//        $query_count = clone $query;
//        $query->setFirstResult($offset)->setMaxResults($this->numshow);
//        $address_id_list = $query->execute()->fetchAll();
        $total = $query->count("id")->execute();

        $this->view()->assign("total",$total);
//        $this->view()->assign("address_ids",$address_id_list);

        $document = $this->document();
        $document->title = "Quản lý giao hàng";

        $document->addJsVar("LoadUsersUrl",$this->createUrl("delivery_manage/load_user"));
        $document->addJsVar("LoadOrderUrl",$this->createUrl("delivery_manage/load_order"));
        $document->addJsVar("LoadAddressUrl",$this->createUrl("delivery_manage/load_address"));
        $document->addJsVar("RequestDeliveryUrl",$this->createUrl("order/detail/change_request_delivery"));
        $document->addJsVar("RefreshOrder",$this->createUrl("delivery_manage/refresh_order"));
        $document->addJsVar("CreateBill",$this->createUrl("delivery_manage/create_bill"));
        $document->addJsVar("SearchOrder",$this->createUrl("delivery_manage/search_order"));
        $document->addJsVar("ChangeShippingFee",$this->createUrl("delivery_manage/ChangeShippingFee"));
        $document->addJsVar("ChangeCod",$this->createUrl("delivery_manage/ChangeCod"));
        $document->addJsVar("FramePrint",$this->createUrl("delivery_manage/frame_print"));
        $document->addJsVar("DeliverManage",$this->createUrl("delivery_manage/default"));

        $this->view()->assign("warehouse",$warehouse);
        $this->view()->assign("address",$address);
        $this->view()->assign("customer",$customer);
        $this->view()->assign("page",$page);

        return $this->renderComponent();
    }

    public function executeSearchOrder(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $warehouse = $this->request()->get("warehouse");
        $address = $this->request()->get("address");
        $customer = $this->request()->get("customer");
        $page = $this->request()->get("page","INT",1);
        $page = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->number_show;
        $user_list = array();
        $query = \Order::read();
        $query->select("user_address_id");
        if($customer != ""){
            $user_list = \Users::read()->andWhere("code like '%{$customer}%' OR username like '%{$customer}%'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
            if(empty($user_list)){
                $query->andWhere("id=-1");
            }
        }

        $locations = array();
        if($address != ""){
            $locations = \Locations::read()->andWhere("label like '%{$address}%'")
            ->setFirstResult(0)->setMaxResults(5)
                ->execute()->fetchAll(\PDO::FETCH_CLASS,\Locations::getPhpName(),array(null,false));
            if(empty($locations)){
                $query->andWhere("id=-1");
            }
        }

        $user_address = array();
        if(!empty($locations) && is_array($locations)){
            $query_address = \UserAddress::read();
            foreach ($locations as $location) {
                if($location instanceof \Locations){
                    $query_address->orWhere("district_id={$location->getId()} OR province_id={$location->getId()}");
                }
            }
            $user_address = $query_address->execute()->fetchAll(\PDO::FETCH_CLASS,\UserAddress::getPhpName(),array(null,false));
        }

        $address_id = "";

        if(!empty($user_address)){
            foreach ($user_address as $address) {
                if($address instanceof \UserAddress){
                    $address_id .= ',"'.$address->getId().'"';
//                    $query->orWhere("user_address_id={$address->getId()}");
                }
            }
            $address_id = substr($address_id,1,strlen($address_id));
            $query->orWhere("user_address_id in ($address_id)");
        }
        if(!empty($user_list)){
            foreach ($user_list as $user) {
                if($user instanceof \Users){
                    $query->orWhere("buyer_id={$user->getId()}");
                }
            }
        }
        if($warehouse != '' && $warehouse != 'all'){
            $query->andWhere("current_warehouse like '%{$warehouse}%'");
        }

        $query->andWhere("status = '".\Order::STATUS_WAITING_FOR_DELIVERY."' OR status = '".\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY."'");

        $query->orderBy("confirm_delivery_time","ASC");
//        $query->groupBy("user_address_id");


        $query->setFirstResult($offset)->setMaxResults($this->number_show);

        $address_id_list = $query->execute()->fetchAll();

        $address_list = array();

        if(!empty($address_id_list)){
            foreach ($address_id_list as $address_id) {
                if(isset($address_id[0])){
                    $address_list[$address_id[0]] = $address_id[0];
                }
            }
        }

        $this->setView("Delivery/search");
        $this->view()->assign("address_ids",$address_list);
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->html_search = $this->renderPartial();
        return $this->renderText($ajax->toString());
    }

    public function executeCreateBill(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $address_id = $this->request()->post("address_id");
        $order_id_list = $this->request()->post("order_id_list","ARRAY",array());

        $logger = Logger::factory("domestic_shipping_bill");

        try{
            $result = \DomesticShipping::createNewDomesticShipping($address_id,$order_id_list,$this->user);
            if($result){
                if($result instanceof \DomesticShipping){
                    $logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} create delivery bill with address id: {$address_id} , bill id :{$result->getId()}  SUCCESS",$order_id_list);
                    $this->dispatch( ON_AFTER_CREATE_BILL, new Event( $this, array(
                        'domestic_shipping' => $result,
                        'staff' => $this->user,
                        "is_public" => $this->is_public_profile
                    ) ) );
                    $ajax->bill_id = $result->getId();
                    $ajax->type = \AjaxResponse::SUCCESS;
                    $ajax->message = "Thành công";
                    return $this->renderText($ajax->toString());
                }else{
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = "Không thành công, xin thử lại";
                    return $this->renderText($ajax->toString());
                }
            }else{
                $ajax->type = \AjaxResponse::ERROR;
                $ajax->message = "Không thành công, xin thử lại";
                return $this->renderText($ajax->toString());
            }
        }catch (\Exception $e){
            $logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} create delivery bill with address id: {$address_id} NOT SUCCESS ".$e->getMessage(),$order_id_list);
            return $this->renderText($ajax->responseError($e->getMessage()));
        }
    }


    public function executeLoadUser(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $address_id = $this->request()->get("address_id");
        $address = \UserAddress::retrieveById($address_id);

        if($address instanceof \UserAddress){
            $user = \Users::retrieveById($address->getUserId());
            if($user instanceof \Users){
                $array = $user->toArray();
                $array["avatar_128x"] = \Users::getAvatar128x($user);
                $array["fullname"] = $user->getFullName();
                $array["link_detail_backend"] = $this->createUrl("user/detail",array("id"=>$user->getId()));
                $ajax->user = $array;
                $ajax->type = \AjaxResponse::SUCCESS;
                return $this->renderText($ajax->toString());
            }
        }
        $ajax->type = \AjaxResponse::ERROR;
        $ajax->message = "Không tồn tại User";
        return $this->renderText($ajax->toString());
    }

    public function executeLoadOrder(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $address_id = $this->request()->get("address_id");
        $query = \Order::read()->andWhere("status = '".\Order::STATUS_WAITING_FOR_DELIVERY."'");
        $query->orWhere("status = '".\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY."'");
        $query->andWhere("user_address_id={$address_id}");
        $query->orderBy("warehouse_status","DESC");
        $query->addOrderBy("confirm_delivery_time","DESC");
        $order_list = \OrderPeer::getOrder($query);
        $orders = $this->_buildOrderDelivery($order_list,$address_id);
        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->order_list = $orders;
        return $this->renderText($ajax->toString());
    }

    public function executeLoadAddress(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $address_id = $this->request()->get("address_id");
        $address = \UserAddress::retrieveById($address_id);
        $address_info = array();
        if($address instanceof \UserAddress){
            $user = \Users::retrieveById($address->getUserId());
            if($user instanceof \Users){
                $address_info["address"] = $address->toArray();
                $address_info["address"]["province_label"] = $address->getProvinceLabel();
                $address_info["address"]["district_label"] = $address->getDistrictLabel();
                $address_info["user"] = $user->toArray();
                $address_info["user"]["fullname"] = $user->getFullName();
                $address_info["cod"] = \OrderCod::getCod($address);
                $address_info["shipping_fee"] = \DomesticShipping::getDomesticShippingFeeFromRedis($address);
                $financial = $user->getAccountBalance() + $address_info["cod"] - $address_info["shipping_fee"];
                $address_info["is_positive"] = $financial > 0 ? 1 : 0;
                $address_info["financial"] = $financial;
                $address_info["missing_amount"] = \OrderPeer::getTotalAmountWaitingDelivery($user);
            }
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->address = $address_info;
        return $this->renderText($ajax->toString());
    }


    /**
     * Change Cod using ajax
     * @return string
     */
    public function executeChangeCod(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $address_id = $this->request()->post("address_id");
        $cod = $this->request()->post("cod");
        $logger = Logger::factory("delivery_cod_backend");
        try{
            if(!$this->isAllowed(PERMISSION_DELIVERY_CHANGE_COD)){
                return $this->renderText($ajax->responseError("Bạn không có quyền sửa COD. Liên hệ Quản trị để được cấp quyền"));
            }


            if(floatval($cod) < 0){
                return $this->renderText($ajax->responseError("COD không hợp lệ, vui lòng thử lại"));
            }

            $address = \UserAddress::retrieveById($address_id);

            if(!$address instanceof \UserAddress){
                return $this->renderText($ajax->responseError("Không tồn tại địa chỉ cho COD này, vui lòng F5 trình duyệt để thử lại. Hoặc liên hệ bộ phân Kỹ thuật để được hỗ trợ"));
            }

            $user = \Users::retrieveById($address->getUserId());

            $result = \OrderCod::setCod($address,$cod);

            if($result){
                $logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} change COD = {$cod} with address id: {$address_id} of Customer:{$user->getUsername()} Success");
                return $this->renderText($ajax->responseSuccess("Thêm cod thành công"));
            }else{
                $logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} change COD = {$cod} with address id: {$address_id} of Customer:{$user->getUsername()} Not Success");
                return $this->renderText($ajax->responseError("Không thành công, xin thử lại"));
            }
        }catch (\Exception $e){
            $logger->error("User {$this->user->getUsername()} - {$this->user->getFullName()} change COD = {$cod} with address id: {$address_id} Not Success ".$e->getMessage());
            return $this->renderText($ajax->responseError("Lỗi kỹ thuật, liên hệ bộ phận Kỹ thuật để được hỗ trợ ".$e->getMessage()));
        }
    }


    /**
     * Change domestic shipping fee
     * @return string
     */
    public function executeChangeShippingFee(){
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        $fee = $this->request()->post("fee");
        $purpose = $this->request()->post("purpose");
        $address_id = $this->request()->post("address_id");
        $logger = Logger::factory("delivery_shipping_backend");
        try{
            if(!$this->isAllowed(PERMISSION_DELIVERY_CHANGE_SHIPPING_FEE)){
                return $this->renderText($ajax->responseError("Bạn không có quyền sửa Phí. Liên hệ Quản trị để được cấp quyền"));
            }

            if(floatval($fee) < 0){
                return $this->renderText($ajax->responseError("Phí không hợp lệ, vui lòng thử lại"));
            }

            $address = \UserAddress::retrieveById($address_id);

            if(!$address instanceof \UserAddress){
                return $this->renderText($ajax->responseError("Không tồn tại địa chỉ này, vui lòng F5 trình duyệt để thử lại. Hoặc liên hệ bộ phân Kỹ thuật để được hỗ trợ"));
            }

            $user = \Users::retrieveById($address->getUserId());

            $result = \DomesticShipping::setDomesticShippingFeeToRedis($address,$fee,$purpose);

            if($result){
                $logger->info("User {$this->user->getUsername()} - {$this->user->getFullName()} change Domestic Shipping Fee = {$fee} with address id: {$address_id} of Customer:{$user->getUsername()} Success");
                return $this->renderText($ajax->responseSuccess("Thêm phí thành công"));
            }else{
                $logger->warning("User {$this->user->getUsername()} - {$this->user->getFullName()} change Domestic Shipping Fee = {$fee} with address id: {$address_id} of Customer:{$user->getUsername()} Not Success");
                return $this->renderText($ajax->responseError("Không thành công, xin thử lại"));
            }
        }catch (\Exception $e){
            $logger->error("User {$this->user->getUsername()} - {$this->user->getFullName()} change Domestic Shipping Fee = {$fee} with address id: {$address_id} Not Success ".$e->getMessage());
            return $this->renderText($ajax->responseError("Lỗi kỹ thuật, liên hệ bộ phận Kỹ thuật để được hỗ trợ ".$e->getMessage()));
        }
    }

    public function executeFramePrint(){
        $this->setView("Delivery/print");
        $this->setLayout("print");

        $domestic_id = $this->request()->get("domestic_id","INT",0);

        $domestic_shipping = \DomesticShipping::retrieveById($domestic_id);

        $domestic_order = \DomesticShippingOrder::findByDomesticShippingId($domestic_id);

        if($domestic_shipping instanceof \DomesticShipping){
            $user_id = $domestic_shipping->getUserId();
            $address_id = $domestic_shipping->getUserAddressId();

            $created_by_id = $domestic_shipping->getCreatedBy();
        }else{
            $user_id = 0;
            $address_id = 0;
            $created_by_id= 0;
        }

        $user = \Users::retrieveById($user_id);

        $address = \UserAddress::retrieveById($address_id);

        $created_by = \Users::retrieveById($created_by_id);

        $this->view()->assign("domestic_shipping",$domestic_shipping);
        $this->view()->assign("domestic_order",$domestic_order);
        $this->view()->assign("user",$user);
        $this->view()->assign("address",$address);
        $this->view()->assign("create_by",$created_by);

        return $this->renderComponent();
    }

    /**
     * Refresh order
     * @return string
     */
    public function executeRefreshOrder(){
        $this->validAjaxRequest();
        $order_id = $this->request()->post("order_id");
        $order = \Order::retrieveById($order_id);

        $ajax = new \AjaxResponse();
        if($order instanceof \Order){
            if($order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_OUT){
                $ajax->status = \AjaxResponse::SUCCESS;
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->time = date("H:i d/m/Y");
                $ajax->message = "Đã xuất kho";
                return $this->renderText($ajax->toString());
            }else{
                $ajax->status = \AjaxResponse::ERROR;
                $ajax->type = \AjaxResponse::SUCCESS;
                $ajax->message = "Chưa xuất kho";
                return $this->renderText($ajax->toString());
            }
        }else{
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Không tồn tại đơn hàng";
            return $this->renderText($ajax->toString());
        }
    }

    public function _buildOrderDelivery($order_list,$address_id){
        $address = \UserAddress::retrieveById($address_id);
        if(!$address instanceof \UserAddress){
            return array();
        }
        $address_full = array();
        $order_list_array = array();
        $order_general = array();
        if(!empty($order_list)){
            foreach ($order_list as $order) {
                if($order instanceof \Order){
                    $array = \OrderPeer::buildArrayOrderData($order);
                    $time = new \DateTime($order->getConfirmDeliveryTime());
                    if(strtotime($order->getConfirmDeliveryTime()) > 0){
                        $array['confirm_delivery_time_format'] = $time->format("H:i d/m/Y");
                    }else{
                        $array['confirm_delivery_time_format'] = "...";
                    }
                    $array["is_out"] = $order->getWarehouseStatus() == \Order::WAREHOUSE_STATUS_OUT ? 1 : 0;
                    $array["is_confirm_delivery"] = $order->getStatus() == \Order::STATUS_CUSTOMER_CONFIRM_DELIVERY ? 1 : 0;

                    if(!$array["is_out"] || !$array["is_confirm_delivery"]){
                        $array["is_disabled"] = 1;
                    }else{
                        $array["is_disabled"] = 0;
                    }

                    $order_list_array["orders"][] = $array;
                }
            }
            $total_amount_not_about = \OrderPeer::getTotalAmountNotAbout($address->getUserId());
            $order_list_array["total_amount"] = $total_amount_not_about;
            $order_list_array["count"] = \OrderPeer::countOrderNotAbout($address->getUserId());
        }

        return $order_list_array;
    }
}
