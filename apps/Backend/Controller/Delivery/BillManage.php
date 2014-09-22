<?php
namespace Backend\Controller\Delivery;

use Backend\Controller\BackendBase;

class BillManage extends BackendBase{

    private $user = null;

    private $num_show = 50;

    public function beforeExecute()
    {
        $this->setTemplate("Seudo");
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $this->setView("Delivery/bill_manage");
        $this->setLayout("default");

        $document = $this->document();
        $document->title = "Danh sách phiếu giao hàng";

        $page = $this->request()->get("page");
        $code = $this->request()->get("code");
        $province = $this->request()->get("province");
        $start_date = $this->request()->get("start_date");
        $end_date = $this->request()->get("end_date");
        $order = $this->request()->get("order");
        $user = $this->request()->get("user");
        $cod = $this->request()->get("cod");
        $warehouse = $this->request()->get("warehouse");
        $domestic_shipping = $this->request()->get("domestic_shipping");

        $query = \DomesticShipping::read();
        $query->andWhere("status= '".\DomesticShipping::STATUS_ACTIVE."'");
        if($code != ""){
            $query->andWhere("code like '%{$code}%'");
        }

        if(!is_numeric($warehouse)){
            $query->andWhere("warehouse = '{$warehouse}'");
        }


        if($order != ""){
            $domestic_order = \DomesticShippingOrder::read()->andWhere("order_code like '%{$order}%'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\DomesticShippingOrder::getPhpName(),array(null,false));
            $domestic = "";
            if(!empty($domestic_order)){
                foreach ($domestic_order as $d_o) {
                    if($d_o instanceof \DomesticShippingOrder){
                        $domestic .= ',"'.$d_o->getDomesticShippingId().'"';
                    }
                }
                $domestic = substr($domestic,1,strlen($domestic));
                $query->andWhere("id in ($domestic)");
            }
        }

        if(intval($province) > 0 ){

            $user_address = \UserAddress::findByProvinceId($province);

            $address_id = "";

            if(!empty($user_address)){
                foreach ($user_address as $address) {
                    if($address instanceof \UserAddress){
                        $address_id .= ',"'.$address->getId().'"';
//                    $query->orWhere("user_address_id={$address->getId()}");
                    }
                }
                $address_id = substr($address_id,1,strlen($address_id));
                $query->andWhere("user_address_id in ($address_id)");
            }else{
                $query->andWhere("user_address_id = -1");
            }
        }

        if($user != ""){
            $users_id = "";
            $users = \Users::read()->andWhere("username like '%{$user}%'")
                ->orWhere("code like '%{$user}%'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
            if(!empty($users)){
                foreach ($users as $us) {
                    if($us instanceof \Users){
                        $users_id .= ',"'.$us->getId().'"';
                    }
                }
                $users_id = substr($users_id,1,strlen($users_id));
                $query->andWhere("user_id in ($users_id)");
            }else{
                $query->andWhere("user_id=-1");
            }
        }

        if($start_date != ""){
            $start_date = \Common::validDateTime($start_date);
            $query->andWhere("created_time >= '{$start_date}'");
        }
        if($end_date != ""){
            $end_date = \Common::validDateTime($end_date);
            $query->andWhere("created_time <= '{$end_date}'");
        }

        if($cod){
            $query->andWhere("cod > 0");
        }

        if($domestic_shipping){
            $query->andWhere("domestic_shipping_fee > 0");
        }

        $total = $query->count("id")->execute();

        $total_page = ceil($total/$this->num_show);

        $document->addJsVar("SearchBill",$this->createUrl("Delivery/BillManage/search_bill"));
        $document->addJsVar("BillManage",$this->createUrl("delivery/bill_manage/default"));

        $this->view()->assign("code",$code);
        $this->view()->assign("total",$total);
        $this->view()->assign("page",$page);
        $this->view()->assign("total_page",$total_page);
        $this->view()->assign("province",$province);
        $this->view()->assign("start_date",$start_date);
        $this->view()->assign("end_date",$end_date);
        $this->view()->assign("user",$user);
        $this->view()->assign("order",$order);
        $this->view()->assign("cod",$cod);
        $this->view()->assign("warehouse",$warehouse);
        $this->view()->assign("domestic_shipping",$domestic_shipping);

        return $this->renderComponent();
    }

    public function executeSearchBill(){
        $this->validAjaxRequest();
        $code = $this->request()->get("code");
        $province = $this->request()->get("province");
        $start_date = $this->request()->get("start_date");
        $end_date = $this->request()->get("end_date");
        $order = $this->request()->get("order");
        $user = $this->request()->get("user");
        $cod = $this->request()->get("cod");
        $page = $this->request()->request('page',"INT",1);
        $page = $page > 0 ? $page : 1;
        $warehouse = $this->request()->get("warehouse");
        $domestic_shipping = $this->request()->get("domestic_shipping");
        $query = \DomesticShipping::read();
        $query->andWhere("status= '".\DomesticShipping::STATUS_ACTIVE."'");
        $offset = ($page - 1) * $this->num_show;
        if($code != ""){
            $query->andWhere("code like '%{$code}%'");
        }

        if(!is_numeric($warehouse)){
            $query->andWhere("warehouse = '{$warehouse}'");
        }


        if($order != ""){
            $domestic_order = \DomesticShippingOrder::read()->andWhere("order_code like '%{$order}%'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\DomesticShippingOrder::getPhpName(),array(null,false));
            $domestic = "";
            if(!empty($domestic_order)){
                foreach ($domestic_order as $d_o) {
                    if($d_o instanceof \DomesticShippingOrder){
                        $domestic .= ',"'.$d_o->getDomesticShippingId().'"';
                    }
                }
                $domestic = substr($domestic,1,strlen($domestic));
                $query->andWhere("id in ($domestic)");
            }else{
                $query->andWhere("id = -1");
            }
        }

        if(intval($province) > 0 ){

            $user_address = \UserAddress::findByProvinceId($province);

            $address_id = "";

            if(!empty($user_address)){
                foreach ($user_address as $address) {
                    if($address instanceof \UserAddress){
                        $address_id .= ',"'.$address->getId().'"';
//                    $query->orWhere("user_address_id={$address->getId()}");
                    }
                }
                $address_id = substr($address_id,1,strlen($address_id));
                $query->andWhere("user_address_id in ($address_id)");
            }else{
                $query->andWhere("user_address_id = -1");
            }
        }

        if($user != ""){
            $users_id = "";
            $users = \Users::read()->andWhere("username like '%{$user}%'")
                ->orWhere("code like '%{$user}%'")->execute()
                ->fetchAll(\PDO::FETCH_CLASS,\Users::getPhpName(),array(null,false));
            if(!empty($users)){
                foreach ($users as $us) {
                    if($us instanceof \Users){
                        $users_id .= ',"'.$us->getId().'"';
                    }
                }
                $users_id = substr($users_id,1,strlen($users_id));
                $query->andWhere("user_id in ($users_id)");
            }else{
                $query->andWhere("user_id=-1");
            }
        }

        if($start_date != ""){
            $start_date = \Common::validDateTime($start_date);
            $query->andWhere("created_time >= '{$start_date}'");
        }
        if($end_date != ""){
            $end_date = \Common::validDateTime($end_date);
            $query->andWhere("created_time <= '{$end_date}'");
        }

        if($cod){
            $query->andWhere("cod > 0");
        }

        if($domestic_shipping){
            $query->andWhere("domestic_shipping_fee > 0");
        }

        $query->setFirstResult($offset,$this->num_show);

        $query_count = clone $query;
        $query->orderBy("id","desc");
        $domestic_shipping = $query->execute()
            ->fetchAll(\PDO::FETCH_CLASS,\DomesticShipping::getPhpName(),array(null,false));
        $domestic_shipping = $this->_buildBill($domestic_shipping);
        $ajax = new \AjaxResponse();
        $ajax->domestic_shipping = $domestic_shipping;
        $ajax->total = $query_count->count("id")->execute();
        $ajax->type = \AjaxResponse::SUCCESS;
        return $this->renderText($ajax->toString());

    }

    public function _buildBill($domestic_shipping_list){
        $bill_list = array();
        if(!empty($domestic_shipping_list)){
            foreach ($domestic_shipping_list as $key=>$domestic_shipping) {
                if($domestic_shipping instanceof \DomesticShipping){
                    $address = \UserAddress::retrieveById($domestic_shipping->getUserAddressId());
                    $user = \Users::retrieveById($domestic_shipping->getUserId());
                    $domestic_shipping_order = \DomesticShippingOrder::findByDomesticShippingId($domestic_shipping->getId());
                    if(!$address instanceof \UserAddress || !$user instanceof \Users){
                        continue;
                    }

                    $order_code = "";
                    if(!empty($domestic_shipping_order)){
                        foreach ($domestic_shipping_order as $domestic_order) {
                            if($domestic_order instanceof \DomesticShippingOrder){
                                $order_code .= $domestic_order->getOrderCode().",";
                            }
                        }
                    }

                    $order_code = substr($order_code,0,-1);
                    $bill_list[$key]["domestic_shipping"] = $domestic_shipping->toArray();
                    $bill_list[$key]["domestic_shipping"]["link_detail"] = $this->createUrl("delivery/bill_detail",array("id"=>$domestic_shipping->getId()));
                    $bill_list[$key]["domestic_shipping"]["order_code"] = $order_code;
                    $bill_list[$key]["domestic_shipping"]["code"] = $domestic_shipping->getDomesticBarcode();
                    $bill_list[$key]["domestic_shipping_order"] = $domestic_shipping->getDomesticBarcode();
                    $bill_list[$key]["domestic_shipping"]["bill_link"] =
                        $this->createUrl("delivery_manage/frame_print",array("domestic_id"=>$domestic_shipping->getId()));
                    $bill_list[$key]["address"] = $address->toFullArray();
                    $bill_list[$key]["user"] = $user->toArray();
                    $bill_list[$key]["user"]["user_link_backend"] = $this->createUrl("user/detail",array("id"=>$user->getId()));
                }
            }
        }
        return $bill_list;
    }
}
