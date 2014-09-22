<?php
namespace Home\Controller;

use Flywheel\Exception;
use \Flywheel\Factory;
use Flywheel\Http\Response;
use \Flywheel\Redis\Client;
use Flywheel\Session\Session;
use SeuDo\Logger;
use SeuDo\Main;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;

class ChooseService extends HomeBase
{
    public $auth;
    /** @var \Users login user */
    public $user;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->auth = \HomeAuth::getInstance();
        $this->user = $this->auth->getUser();
        if (!$this->user) {
            $this->redirect('login?url=' . base64_encode($this->createUrl('cart')));
        }
    }

    public function executeDefault()
    {
        $this->setLayout('checkout');
        $document = $this->document();
        $document->title = "Chọn dịch vụ";

        $document->addJsVar('checkout_address', Main::getHomeRouter()->createUrl('CheckoutAddress'));
        $document->addJsVar('cart_url', Main::getHomeRouter()->createUrl('cart'));
        $document->addJsVar('delCartByItem', Main::getHomeRouter()->createUrl('cart/delete'));
        $document->addJsVar('delCartByShop', Main::getHomeRouter()->createUrl('cart/deleteCartShop'));
        $document->addJsVar('choose_service_url', Main::getHomeRouter()->createUrl('chooseService'));
        $document->addJsVar('calService', Main::getHomeRouter()->createUrl('Service/Calc'));

        $shopIdString   = $this->request()->get('sid');
        $shopArray      = (explode(',', $shopIdString));
        $receiveAddressId = $this->request()->get('aid'); // dia chi nhan hang$receiveAddressId

        $receiveAddressId = $receiveAddressId ? $receiveAddressId : 1;
        if (!$shopArray) {
            $this->redirect(Main::getHomeRouter()->createUrl('cart/default'));
        }
        $receiveAddress = \UserAddress::retrieveById($receiveAddressId);
        $location = \Locations::retrieveById($receiveAddress->getProvinceId());

        $shopOrders = array();
        foreach ($shopArray AS $shopId) {
            $cartItem = \CartItem::getCartDataForPreOrder($this->user->getId(), $shopId,
                array(\Services::TYPE_BUYING, \Services::TYPE_CHECKING, \Services::TYPE_PACKING), $location->getKeyCode());
            if ($cartItem) {
                $shopOrders[] = $cartItem;
            }
        }
        if (!$shopOrders) {
            $this->redirect(Main::getHomeRouter()->createUrl('cart/default'));
        }

        $this->view()->assign(array(
            'exchange' => \ExchangeRate::getExchange(),
            'receiveAddressId' => $receiveAddressId,
            'receiveAddress' => $receiveAddress,
            'shopOrders' => $shopOrders,
            'userAvatar' => \Users::getAvatar32x($this->user),
            'currentUser' => $this->user,
            'user_id' => $this->user->getId()
         ));

        $this->setView('ChooseService/default');

        return $this->renderComponent();
    }

    public function updateChooseService()
    {
        $this->validAjaxRequest();

        if ($this->user) {
            $userId = $this->user->id;
        } else {
            $ses = Session::getInstance();
            $userId = $ses->id(); //Factory::getSession()->id();
        }

        if ($userId == null) {
            $userId = 0;
        }

        $shopId = $this->request()->get('sid');
        $services = $this->request()->get('sv');
        $services = explode(',', $services);
        if (empty($services)) {
            $services = array(\Services::TYPE_BUYING);
        }

        $cartData = \CartItem::getCartDataForPreOrder($userId, $shopId, $services);


        return $this->renderText(json_encode($cartData));
    }

    public function executeUpdateItemNote()
    {
        $this->validAjaxRequest();

        $cartId = $this->request()->post('cid');
        $content = $this->request()->post('c');

        $item = \CartItem::retrieveById($cartId);
        if ($item->uid != $this->user->id) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Bạn không có quyền.')));
        }
        if (!$item) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Sản phẩm không tồn tại trong giỏ hàng')));
        }

        $item->setComment(trim($content));
        $item->save();
        return $this->renderText(json_encode(array('error' => 0)));
    }

    public function executePostCommentOrder()
    {

        $this->validAjaxRequest();

        $shopId     = $this->request()->post('sid');
        $userId     = $this->user->getId();
        $content    = $this->request()->post('c');
        if (!$content) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Bạn chưa gửi comment.')));
        }
        $items = \CartItem::getCartItemByShop($userId, $shopId);
        if (!$items) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Không tìm thấy sản phẩm trong shop')));
        }
        $commentData = array();
        if ($items[$shopId][0]->getCommentShop()) {
            $commentData = json_decode($items[$shopId][0]->getCommentShop());
        }
        $time = time();
        $created_time = date('d/m/Y H:i:s', $time);
        $postData = array(
            'uid'       => $userId,
            'content'   => $content,
            'created_time' => $created_time
        );
        $commentData[] = $postData;

        foreach ($items[$shopId] AS $item) {
            if($item instanceof \CartItem){
                $item->setCommentShop(json_encode($commentData));
                $item->save();
            }
        }
        $currentUser = \Users::retrieveById($userId);
        $returnData = array(
            'uid' => $userId,
            'username' => $currentUser->getUsername(),
            'fullname' => $currentUser->getFullName(),
            'avatar' => \Users::getAvatar32x($currentUser),
            'content' => $content,
            'created_time' => date('d/m/Y H:i:s', $time)
        );
        return $this->renderText(json_encode(array('error' => 0, 'result' => $returnData)));
    }

    public function executeChangeQuantity()
    {
        $this->validAjaxRequest();

        $userId = $this->user->id;
        $quantity = $this->request()->post('q');
        $cartId = $this->request()->post('cid');

        $services = $this->request()->post('sv');
        $services = explode(',', $services);
        if (empty($services)) {
            $services = array(\Services::TYPE_BUYING);
        }

        $item = \CartItem::retrieveById($cartId);

        $item->changeCartQuantity($quantity);

        if(!($item instanceof \CartItem)) {
            return $this->renderText(json_encode(array(
                'error' => 1,
                'msg' => 'Không cập nhật được dữ liệu. Vui lòng thử lại.'
            )));
        }
        // Check price lấy theo bảng giá hay giá riêng từng item
        $price_table    = $item->getPriceTable();
        $itemByPriceTable = true;
        if(!empty($price_table)) {
            $prices = json_decode($price_table, true);
            if(is_array($prices)) {
                foreach($prices as $k => $v) {
                    if(isset($v['price']) && is_array($v['price'])) {
                        $itemByPriceTable = false;
                        break;
                    }
                }
            }
        } else { $itemByPriceTable = false; }

        if ($item->getId()) {
            $cartInfo = \CartItem::getCartDataForPreOrder($userId, $item->getShopId(), $services);
            return $this->renderText(json_encode(array(
                'error' => 0,
                'data' => array(
                    'itemInfo' => array(
                        'id' => $item->id,
                        'shopId' => $item->shop_id,
                        'shopUsername' => $item->shop_username,
                        'price' => $item->price_vnd,
                        'amount' => $item->amount,
                        'totalItemAmount' => \GlobalHelper::rounding($item->price_vnd * $item->amount, 1000),
                        'step' => $item->step,
                        'itemByPriceTable' => $itemByPriceTable
                    ),
                    'orderInfo' => $cartInfo['orderInfo']
                )
            )));
        } else {
            return $this->renderText(json_encode(array(
                'error' => 1,
                'msg' => 'Không cập nhật được dữ liệu. Vui lòng thử lại.'
            )));
        }

    }

    public function executeSubmitOrder() {
        $this->validAjaxRequest();
        $userId = $this->user->id;
        $data = $this->request()->post('data', 'ARRAY');
        $service_default = array(\Services::TYPE_BUYING => 1, \Services::TYPE_SHIPPING_CHINA_VIETNAM => 1);

        $addressId = $this->request()->post('aid');

        $orders = array();

        $sid = array();
        foreach ($data as $order) {

            $sid[]      = $order[0];
            $shopId     = $order[0];

            $services = json_decode($order[1], true);
            if(isset($services[\Services::TYPE_EXPRESS_CHINA_VIETNAM])){
                $service_default = array(\Services::TYPE_BUYING => 1);
            }
            $services = array_merge($services, $service_default);

            $orderData  = \CartItem::getCartDataForPreOrder($userId, $order[0], $services);
            $orders[$shopId] = $orderData;
        }
        $orderObj = new \Order();
        $result = array();

        try {
            $address = \UserAddress::retrieveById($addressId);
            if((!$address instanceof \UserAddress) || ($address instanceof \UserAddress &&
                    $address->getUserId() != $userId)){
                $result = array(
                    'error' => 1,
                    'message' => "Không tồn tại địa chỉ nhận hàng cho đơn hàng, xin làm mới trình duyệt và thử lại.
                        Nếu không thành công, vui lòng liên hệ bộ phận CSKH để được hỗ trợ."
                );
                return $this->renderText(json_encode($result));
            }
            $newOrders = $orderObj->createOrder($orders, $this->user, $addressId);

            foreach ($newOrders as $order) {
                if($order instanceof \Order){
                    $order->updateInfo();
                }
            }
            //delete cart item after create new order
            foreach($sid as $s) {
                \CartItem::delCartShop($this->user->getId(),$s);
            }

            $orderId = Session::getInstance()->get("order_deposit_id");

            if($orderId == ''){
                $orderId = array();
            }
            foreach($newOrders as $newOrder) {
                $orderId[] = $newOrder->getId();
            }

            Session::getInstance()->set("order_deposit_id",$orderId);

            $hashKey = md5(json_encode($orderId));

            $session = Session::getInstance()->set($hashKey, $orderId);
            $result = array(
                'error' => 0,
                'msg' => 'Success',
                'redirectUrl' => Main::getHomeRouter()->createUrl('OrderDeposit', array('hash' => $hashKey))
            );
        }catch (InvalidArgumentException $iae){
            $result = array(
                'error' => 1,
                'message' => "Tồn tại sản phẩm không hợp lệ trong giỏ, hãy xóa sản phẩm để tiếp tục"
            );
        }
        catch (Exception $e) {
            Logger::factory('system')->error($e->getMessage() .'Context:' .$e->getTraceAsString());

            $result = array(
                'error' => 1,
                'message' => "Exception ". $e->getMessage()
            );
        }

        return $this->renderText(json_encode($result));
    }
}


