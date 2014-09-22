<?php
namespace Home\Controller;

use ___PHPSTORM_HELPERS\object;
use Backend\Controller\ExchangeRate;
use Flywheel\Exception;
use Flywheel\Redis\Client;
use Flywheel\Session;
use SeuDo\Main;
use Flywheel\Db\Manager;

class Cart extends HomeBase
{

    public $authen;

    /**
     * @var \Users
     */
    public $user;
    public $redis_client;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->authen = \HomeAuth::getInstance();
        $this->user = $this->authen->getUser();

        $this->redis_client = Client::getConnection('default');

        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('onSuccessCartAdd', array(new \HomeEvent(), 'onSuccessCartAdd'));
    }

    public function executeDefault()
    {
        $this->setLayout('checkout');
        $this->setView('Cart/default');
        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id(); //Factory::getSession()->id();
        }

        if ($user_id == null) {
            $user_id = 0;
        }

        $cart_list = \CartItem::findByUid($user_id);
        $province_list = \Locations::findByType(\Locations::LOCATION_STATE);

        $document = $this->document();

        $document->title = "Giỏ hàng của bạn";

        $document->addJsVar('checkout_address', $this->createUrl('CheckoutAddress'));
        $document->addJsVar('cart_url', $this->createUrl('cart'));
        $document->addJsVar('payment_url', $this->createUrl('cart/payment'));
        $document->addJsVar('fav_url', $this->createUrl('user/favorite_item/like', array()));
        $document->addJsVar('user_id', $user_id);

        $this->view()->assign('cart_list', $cart_list);

        $this->view()->assign('province_list', $province_list);

        $this->view()->assign('user_id', $user_id);


        return $this->renderComponent();
    }

    public function executeAddCartV2(){

        if ($this->user) {
            $user_id = $this->user->getId();
            $username = $this->user->getUsername();
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();
            $username = '';
        }

        $user = array(
            'user_id' => $user_id,
            'username' => $username
        );
        $data_cart = array();
        $data_cart['data_value'] = $this->request()->post("data_value");
        $data_cart['image_model'] = urldecode($this->request()->post("image_model"));
        $data_cart['image_origin'] = urldecode($this->request()->post("image_origin"));
        $data_cart["image_model"] = str_replace("_.webp","",$data_cart['image_model']);
        $data_cart["image_origin"] = str_replace("_.webp","",$data_cart['image_origin']);
        $data_cart['item_id'] = $this->request()->post("item_id");
        $data_cart['link_origin'] = $this->request()->post("link_origin");
        $data_cart['price_origin'] = $this->request()->post("price_origin");

        if(is_array($data_cart['price_origin'])){
            $data_cart['price_origin'] = isset($data_cart['price_origin'][0]) ? $data_cart['price_origin'][0]
                : $data_cart['price_origin'];
        }

        $data_cart['price_origin'] = is_numeric($data_cart['price_origin']) ? $data_cart['price_origin']
            : 0;

        $data_cart['price_promotion'] = $this->request()->post("price_promotion","FLOAT",0);
        $data_cart['properties'] = $this->request()->post("property");
        $data_cart['properties_translated'] = $this->request()->post("property_translated");
        $data_cart['shop_id'] = $this->request()->post("shop_id");
        $data_cart['shop_name'] = $this->request()->post("shop_name");
        $data_cart['quantity'] = $this->request()->post("quantity","INT",0);
        $data_cart['require_min'] = $this->request()->post("require_min","INT",1);
        $data_cart['comment'] = $this->request()->post("comment");
        $data_cart['outer_id'] = $this->request()->post("outer_id");
        $data_cart['site'] = strtoupper($this->request()->post("site"));
        $data_cart['stock'] = $this->request()->post("stock","INT",99);
        $data_cart['step'] = $this->request()->post("step","INT",1);
        $data_cart['title_origin'] = strip_tags(trim($this->request()->post("title_origin")));
        $data_cart['title_translated'] = strip_tags(trim($this->request()->post("title_translated")));
        $data_cart['wangwang'] = $this->request()->post("wangwang");
        $data_cart['weight'] = $this->request()->post("weight");
        $data_cart['price_table'] = $this->request()->post("price_table");
        $data_cart['tool'] = $this->request()->post("tool");

        if($data_cart['item_id'] == "" && $data_cart['link_origin'] != ""){
            $parts = parse_url($data_cart['link_origin']);
            $query = isset($parts['query']) ? $parts['query'] : "";
            parse_str($query, $query_url);
            $id = isset($query_url['id']) ? $query_url['id'] : "";
            $data_cart['item_id'] = $id;
        }

        if($data_cart['price_table'] != '' && !is_array($data_cart['price_table'])){
            $data_cart['price_table'] = json_decode($data_cart['price_table'],true);
        }

        $ajax = new \AjaxResponse();

        $ajax = \CartItem::addCartItemV2($data_cart,$user,$ajax);
        $this->setView("Cart/confirm");
        $this->view()->assign("ajax",$ajax);
        $this->view()->assign("price",$data_cart['price_promotion'] > 0 ? $data_cart['price_promotion'] : $data_cart['price_origin']);
        $ajax->html = $this->renderPartial();
        $ajax->user = $user_id;

        return $this->renderText($ajax->toString());
    }

    public function executeFrameCart(){
        echo "Chuc nang dang duoc hoan thien!";
        exit;
    }

    public function executeAdd()
    {
        $this->validAjaxRequest();

        $item = $this->request()->request('item', "STRING", '');

        $ajax = new \AjaxResponse();
//
        if ($item == '') {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Không có thông tin về sản phẩm. Xin thử lại';

            return $this->renderText($ajax->toString());
        }

        $item = json_decode($item, true);

        if ($this->user) {
            $user_id = $this->user->id;
            $username = $this->user->username;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();
            $username = '';
        }

        $user = array(
            'user_id' => $user_id,
            'username' => $username
        );

        if($item['properties'] ){
            foreach ($item['properties'] as $key => $prop) {

                $quantity = $prop['amount'];

                $prop['properties'] = $key;

                if ($quantity == 0) {
                    $ajax->type = \AjaxResponse::ERROR;
                    $ajax->message = 'Bạn chưa nhập số lượng';
                    return $this->renderText($ajax->toString());
                } else {
                    $ajax = \CartItem::addCartItem($item, $user, $prop, $ajax);
                }
            }
        }else{
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Bạn chưa chọn sản phẩm nào';
        }


        return $this->renderText($ajax->toString());
    }

    /**
     * Delete Cart
     * @return string
     */
    public function executeDelete()
    {
        $this->validAjaxRequest();

        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();
        }

        $ajax = new \AjaxResponse();
        $cart_id = $this->request()->request('id');

        $cart = \CartItem::retrieveById($cart_id);

        if ($cart instanceof \CartItem && $user_id == $cart->getUid()) {

            $price_old = $cart->getPriceCny();
            $price_vnd = $cart->getPriceVnd();

            $item_site_id = $cart->getItemId();

            $price_table = $cart->getPriceTable();

            $site = $cart->getSite();
            $result = $cart->delete();

            if($result){
                if($site != \CartItem::TMALL_SITE && $site != \CartItem::TAOBAO_SITE){
                    $price_new = \CartItem::getPriceByPriceTable($price_table,0,$item_site_id,$user_id);
                    if($price_new == 0){
                        $price_new = $price_old;
                    }
                    if($price_new != $price_old){
                        $price_vnd = \Common::roundingMoney($price_new * \ExchangeRate::getExchange());
                    }
                    \CartItem::updatePriceToCart($user_id,$item_site_id,$price_new);
                }else{
                    $price_new = $price_old;
                }

                $ajax->type = \AjaxResponse::SUCCESS;

                $ajax->price = $price_new;

                $ajax->price_vnd = $price_vnd;

                $ajax->item_site_id = $item_site_id;
            }else{
                $ajax->type = \AjaxResponse::ERROR;
            }

            return $this->renderText($ajax->toString());
        }

        $ajax->type = \AjaxResponse::ERROR;

        return $this->renderText($ajax->toString());
    }

    /**
     * Save Cart Quantity
     * @return string
     */
    public function executeSaveQuantity()
    {
        $this->validAjaxRequest();
        $cart_id = $this->request()->post('cart_id');
        $quantity = $this->request()->post('quantity');

        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id(); //Factory::getSession()->id();
        }

        $cart = \CartItem::retrieveById($cart_id);

        if(!empty($cart) && $cart instanceof \CartItem){
            try{
                $result = $cart->changeCartQuantity($quantity);
                if(!$result){
                    $response = array();
                    $response['type'] = 0;
                    $response['message'] = "Không thành công";
                }
            }catch (\Flywheel\Exception $e){
                $response = array();
                $response['type'] = 0;
                $response['message'] = "Không thành công";
            }

            $response['type'] = 1;
            $response['message'] = "Thành công";
            $response['price'] = $cart->getPriceCny();
            $price_vnd = $cart->getPriceVnd();
            $response['price_vnd'] = $price_vnd;
            $response['item_site_id'] = $cart->getItemId();
            $response['site'] = $cart->getSite();
            return $this->renderText(json_encode($response));
        }
        $response = array();
        $response['type'] = 0;
        $response['message'] = "Không thành công";
        return $this->renderText(json_encode($response));

    }

    /**
     * Xoa danh sach cart item theo shop
     * @return string
     */
    public function executeDeleteCartShop()
    {
        $this->validAjaxRequest();
        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();
        }
        $shop_id = $this->request()->post('shop_id');

        $result = \CartItem::delCartShop($user_id,$shop_id);

        if($result){
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = "Thành công";
        }else{
            $ajax = new \AjaxResponse();
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = "Thất bại";
        }


        return $this->renderText($ajax->toString());
    }

    public function executePayment()
    {

        $this->validAjaxRequest();
        $shop_ids = $this->request()->request("shop_ids", "STRING", '');
        $address_id = $this->request()->request("address_id", "STRING", '');

        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $url_redirect = $this->createUrl('Login/login');
            $response = array(
                "type" => \AjaxResponse::SUCCESS,
                "message" => "Bạn chưa đăng nhập",
                "url" => $url_redirect
            );
            return $this->renderText(json_encode($response));
        }

        if (!$address_id) {
            $address = \UserAddress::findOneByUserIdAndIsDefaultAndIsDelete($user_id, 1,0);

            if($address instanceof \UserAddress){
                $address_id = $address->id;
                if($address->getReciverPhone() == ""){
                    $user_mobile = \UserMobiles::retrieveByUserId($user_id);
                    if($user_mobile instanceof \UserMobiles){
                        $address->setReciverPhone($user_mobile->getMobile());
                        $address->save();
                    }else{
                        $response = array(
                            "type" => \AjaxResponse::ERROR,
                            "message" => "Yêu cầu bổ sung số điện thoại cho địa chỉ nhận hàng.
                        Chúng tôi sẽ liên hệ với bạn qua số điện thoại này khi giao hàng.",
                            "element" => "Phone"
                        );
                        return $this->renderText(json_encode($response));
                    }
                }
            } else {

                $response = array(
                    "type" => \AjaxResponse::ERROR,
                    "message" => "Bạn chưa chọn địa chỉ, yêu cầu chọn địa chỉ để chúng tôi giao hàng cho bạn!",

                );
                return $this->renderText(json_encode($response));
            }
        }else{
            $address = \UserAddress::retrieveById($address_id);
            if($address instanceof \UserAddress){
                if($address->getReciverPhone() == ""){
                    $response = array(
                        "type" => \AjaxResponse::ERROR,
                        "message" => "Yêu cầu bổ sung số điện thoại cho địa chỉ nhận hàng.
                        Chúng tôi sẽ liên hệ với bạn qua số điện thoại này khi giao hàng.",
                        "element" => "Phone"
                    );
                    return $this->renderText(json_encode($response));
                }
            } else {
                $response = array(
                    "type" => \AjaxResponse::ERROR,
                    "message" => "Gặp lỗi khi chọn địa chỉ, xin chọn lại địa chỉ khác hoặc F5 và thử lại",
                );
                return $this->renderText(json_encode($response));
            }
        }

        if ($shop_ids == '') {
            $response = array(
                "type" => \AjaxResponse::ERROR,
                "message" => "Xin bấm F5 để thử lại"
            );
            return $this->renderText(json_encode($response));
        }

        $shop_ids = json_decode($shop_ids, true);

        $shopIds = '';
        foreach ($shop_ids as $shop_id) {
            $cart_list = \CartItem::getCartItemByShop($user_id, $shop_id);
            if (empty($cart_list)) {
                continue;
            }
            foreach ($cart_list as $cart) {
                if($cart instanceof \CartItem){
                    $data = array();
//                if ($cart->data != '') {
//                    $data = json_decode($cart->data, true);
//                }

                    if ($cart->amount > $cart->getStock()) {
                        $response = array(
                            "type" => \AjaxResponse::ERROR,
                            "message" => "Số lượng còn lại của sản phẩm trong kho là {$cart->getStock()}",
                            "cart_id" => $cart->id
                        );
                        return $this->renderText(json_encode($response));
                    }
                }

            }

            $shopIds .= $shop_id . ',';
        }

        $shopIds = rtrim($shopIds, ',');

        Session\Session::set('payment_shop_id', $shop_ids);

        $url_redirect = $this->createUrl('chooseService/default', array("sid" => $shopIds, "aid" => $address_id));

        $response = array(
            "type" => \AjaxResponse::SUCCESS,
            "message" => "Thành công",
            "url" => $url_redirect
        );
        return $this->renderText(json_encode($response));
    }

    public function executeSuccess()
    {
        $this->setView('Cart/success');
        return $this->renderComponent();
    }

    public function executeCountCartAndPrice(){
        $this->validAjaxRequest();

        if ($this->user) {
            $user_id = $this->user->id;
        } else {
            $ses = Session\Session::getInstance();
            $user_id = $ses->id(); //Factory::getSession()->id();
        }

        $total_cart = \CartItem::getCartQuantity($user_id);
        $total_price = \CartItem::getPriceCartByUser($user_id);
        if($total_price == 0){
            $total_cart = 0;
        }

        $total_price = \Common::numberFormat($total_price,true);

        $response = array("total_cart"=>$total_cart,"total_price"=>$total_price);
        return $this->renderText(json_encode($response));
    }

    /**
     * @return string
     */
    public function executeGetExchangeRate(){
        $exchange_rate = \ExchangeRate::getExchange();
        return $this->renderText($exchange_rate);
    }
}