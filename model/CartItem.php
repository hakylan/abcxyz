<?php
/**
 * CartItem
 * @version        $Id$
 * @package        Model
 */

require_once dirname(__FILE__) . '/Base/CartItemOrigin.php';

use Flywheel\Redis\Client;

class CartItem extends CartItemOrigin
{
    const CONFIG_REDIS = "cart";
    const TAOBAO_SITE = "TAOBAO";
    const TMALL_SITE = "TMALL";
    const ALIBABA_SITE = "1688";
    const EELLY_SITE = "EELLY";
    const NAHUO_SITE = "NAHUO";

    public function __construct($data = array()){

        parent::__construct($data);
    }

    public function save(){
        return self::hSetCartItem($this);
    }

    public function delete(){
        return self::delCartItem($this);
    }

    /**
     * Get Image show
     * @return string
     */
    public function getImageShow(){
        if($this->getImgModel() != ''){
            return $this->getImgModel();
        }else if($this->getItemImg()){
            return $this->getItemImg();
        }
        return '';
    }

    public function getPriceCny(){
        $price = $this->getPromotionPrice() > 0 ? $this->getPromotionPrice() : $this->getPrice();
        return $price;
    }

    /*
     * function changeCartQuantity - thay đ?i quantity item trong Cart và update vào redis
     * return Cart Item Object
     * param $userId int, $cartId int, $quantity int
     * */

    public function changeCartQuantity($quantity)
    {
        try{
            $this->setAmount($quantity);
            $exchange       = ExchangeRate::getExchange();

            $price_table    = $this->getPriceTable();
            if(!empty($price_table)) {
                $price = \CartItem::getPriceByPriceTable($price_table, $quantity,$this->getItemId(),$this->getUid()); //$quantity
            } else {
                $price = $this->getPrice();
            }

            if($price == 0){
                $price = $this->getPrice();
            }
            if($price != $this->getPrice()){
                $price_cny = $this->getPriceCny();
                $price_vnd = $price_cny * $exchange;
                $price_vnd = Common::roundingMoney($price_vnd);
                $this->setPriceVnd($price_vnd);
                if($this->getSite() != self::TAOBAO_SITE && $this->getSite() != self::TMALL_SITE){
                    self::updatePriceToCart($this->getUid(),$this->getItemId(),$price,$this->getPromotionPrice());
                }
            }

            $this->setPrice($price);
            $result = $this->save();
            return $result;
        }catch (\Flywheel\Exception $e){
            throw new \Flywheel\Exception("Error Save Quantity" . $e->getMessage());
        }
    }

    // Move Cart To Redis V2

    /**
     * Save cart item type Hashes -> hSetCartItem theo Key dạng CART_{UID}_{SHOP_ID}_{ITEM_SITE_ID}_{md5(PROPERTIES)}
     * @param CartItem $cartItem
     * @return bool
     * @throws Flywheel\Exception
     */
    private static function hSetCartItem(\CartItem $cartItem){

        if(!empty($thisItem) && !($cartItem instanceof \CartItem)){
            throw new \Flywheel\Exception("$cartItem not instanceof \CartItem ");
        }
        try{
            $redis_client = Client::getConnection(self::CONFIG_REDIS);

            $properties_md5 = md5($cartItem->getProperties());

            $key = REDIS_CART.$cartItem->getUid(). "_{$cartItem->getShopId()}_{$cartItem->getItemId()}_{$properties_md5}";

            $cart_id = $cartItem->getUid(). "_{$cartItem->getShopId()}_{$cartItem->getItemId()}_{$properties_md5}";

            $cartItem->setId($cart_id);

            if(!empty($cartItem)){
                $redis_client->hSet($key,1,json_encode($cartItem));
                $redis_client->expire($key,60*60*24*30);
                return true;
            }

            return false;
        }
        catch (\Exception $cartItem){
            return false;
        }
    }

    /**
     * Get Cart Item By Key
     * @param $key
     * @return array | CartItem
     */
    private static function hGetCartItem($key){
        if($key == ""){
            return array();
        }
        try{
            $redis_client = Client::getConnection(self::CONFIG_REDIS);

            $cart = $redis_client->hGetAll($key);

            if(!empty($cart)){
                if(isset($cart[1])){
                    $cart = json_decode($cart[1],true);

                    $cart_item = new CartItem($cart);

                    return $cart_item;
                }else{
                    $cart = json_decode($cart,true);
                    $cart_item = new CartItem($cart);
                    return $cart_item;
                }
            }else{
                return array();
            }


        }catch (\Exception $e){
            return array();
        }
    }

    /**
     * Delete cart item
     * @param CartItem $cartItem
     * @return bool
     */
    private static function delCartItem(\CartItem $cartItem){

        if(!empty($cartItem) && !($cartItem instanceof \CartItem)){
            return false;
        }
        try{
            $redis_client = Client::getConnection(self::CONFIG_REDIS);

            $properties_md5 = md5($cartItem->getProperties());

            $key = REDIS_CART.$cartItem->getUid(). "_{$cartItem->getShopId()}_{$cartItem->getItemId()}_{$properties_md5}";

            $redis_client->del($key);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * Del cart By Keys Dạng Cart_uid_*_...
     * @param $keys
     * @return bool
     */
    public static function delCartByKeys($keys){
        $redis_client = Client::getConnection(self::CONFIG_REDIS);
        $list_key = $redis_client->keys($keys);

        if(!empty($list_key) && is_array($list_key)){
            foreach ($list_key as $key) {
                $redis_client->del($key);
            }
            return true;
        }
        return false;
    }

    /**
     * Find By Keys -> using Keys {$key}
     * @param $keys
     * @return array CartItem
     */
    public static function findByKeys($keys){
        $redis_client = Client::getConnection(self::CONFIG_REDIS);
        $list_key = $redis_client->keys($keys);

        $cart_list = array();

        if(is_array($list_key)){
            foreach ($list_key as $k) {
                $cart = self::hGetCartItem($k);
                if(empty($cart)){
                    continue;
                }

                $shop_id = $cart->getShopId();

                if($shop_id == ""){
                    continue;
                }

                $cart_list[$shop_id][] = $cart;
            }
        }

        return $cart_list;
    }

    /**
     * Find By Key
     * @param $key
     * @return CartItem
     */
    public static function findByKey($key){
        $cart = self::hGetCartItem($key);

        return $cart;
    }

    /**
     * Find By Uid And Item Id
     * @param $uid
     * @param $item_id
     * @return array CartItem
     */
    public static function findByUidAndItemId($uid,$item_id){
        try{
            $keys = REDIS_CART."{$uid}_*_{$item_id}_*";

            $cart_list = self::findByKeys($keys);

            return $cart_list;
        }catch (\Flywheel\Exception $e){
            return array();
        }
    }


    /**
     * find Cart Item By User Id - quyen
     * @param $user_id
     * @return array CartItem
     */

    public static function findByUid($user_id){
        if($user_id == ""){
            return array();
        }
        try{
            $keys = REDIS_CART."{$user_id}_*";

            $cart_list = self::findByKeys($keys);

            return $cart_list;
        }catch (\Exception $e){
            return array();
        }
    }

    /**
     * Find Cart Item By User Id And shop id - quyen
     * @param $user_id
     * @param $shop_id
     * @return CartItem[]
     */
    public static function findByUidAndShopId($user_id,$shop_id){
        if($user_id == "" || $shop_id == ""){
            return array();
        }
        try{
            $keys = REDIS_CART."{$user_id}_{$shop_id}_*";

            $cart_list = self::findByKeys($keys);

            return $cart_list;
        }catch (\Exception $e){
            return array();
        }
    }

    /**
     * retrieve By Id
     * @param $cart_id
     * @return CartItem
     */
    public static function retrieveById($cart_id)
    {
        return self::findByKey(REDIS_CART."".$cart_id);
    }

    /**
     * get Cart Quantity By User  Id
     * @param $user_id
     * @return int
     */
    public static function getCartQuantity($user_id)
    {
        $cart_list = self::findByUid($user_id);

        $quantity = 0;
        if(!empty($cart_list)){
            foreach ($cart_list as $carts) {
                if(is_array($carts)){
                    foreach ($carts as $cart) {
                        if(!empty($cart) && ($cart instanceof CartItem)){
                            $quantity += $cart->getAmount();
                        }
                    }
                }
            }

        }
        return intval($quantity);
    }

    /**
     * Get Quantity By Uid and item id
     * @param $user_id
     * @param $item_id
     * @return int
     */
    public static function getQuantityByUidAndItemId($user_id,$item_id){
        $keys = REDIS_CART."{$user_id}_*_{$item_id}_*";
        $quantity = 0;
        $cart_list = self::findByKeys($keys);

        if(!empty($cart_list)){
            foreach ($cart_list as $carts) {
                if(is_array($carts)){
                    foreach ($carts as $cart) {
                        if(!empty($cart) && ($cart instanceof CartItem)){
                            $quantity += $cart->getAmount();
                        }
                    }
                }
            }
        }

        return intval($quantity);
    }

    /**
     * Del Cart Shop -- quyen
     * @param $user_id
     * @param $shop_id
     * @return bool
     */
    public static function delCartShop($user_id, $shop_id)
    {
        $keys = REDIS_CART."{$user_id}_{$shop_id}*";
        return self::delCartByKeys($keys);
    }

    /**
     * Lay danh sach cart Item theo shop id -- quyen
     * @param $user_id
     * @param $shop_id
     * @return CartItem[]
     */
    public static function getCartItemByShop($user_id, $shop_id)
    {
        return self::findByUidAndShopId($user_id,$shop_id);
    }

    /**
     * Get price by price table and amount
     * @param $price_table
     * @param int $amount
     * @param int $item_site_id
     * @param int $user_id
     * @return array|int
     */
    public static function getPriceByPriceTable($price_table, $amount = 1,$item_site_id = 0,$user_id = 0)
    {
        try{
            $quantity = $amount;
            if($item_site_id != 0 && $user_id !== 0){

                $quantity = self::getQuantityByUidAndItemId($user_id,$item_site_id);

                $quantity = $quantity > 0 ? $quantity : $amount;
            }

            if($price_table != ''){
                if(!is_array($price_table)){
                    $price_table = json_decode($price_table, true);
                }

                $price = 0;
                if(is_array($price_table)){
                    foreach ($price_table as $key => $value) {
                        if (is_string($key)) {
                            $price = $price_table['price'];
                            break;
                        } else {
                            $begin = isset($value['begin']) ? intval($value['begin']) : 0;
                            $end = isset($value['end']) ? intval($value['end']) : 0;


                            if ($begin <= $quantity && $quantity <= $end
                                || ($begin <= $quantity && ($end) == 0)) {

                                $price = isset($value['price']) ? $value['price'] : 0;
                                break;
                            }

                            if(is_array($value['price']) && isset($value['price'][1])){
                                $price = 0;
                            } else {
                                // Get first price
                                if($price == 0) $price = $value['price'];
                            }
                        }
                    }
                }

                if(is_array($price)) {
                    $price = 0;
                }

                return $price;
            }

            return 0;
        }
        catch (\Exception $e){
            return 0;
        }

    }

    public static function getCartDataForPreOrder($userId, $shopId, $services = array())
    {
        $cartItems = \CartItem::getCartItemByShop($userId, $shopId);
        $cartItems = isset($cartItems[$shopId]) ? $cartItems[$shopId] : array();
        if (!$cartItems) {
            return null;
        }
        $totalWeight = 0;
        $totalAmount = 0;
        $totalQuantity = 0;
        $totalServiceFee = 0;
        $totalCheckingFee = 0;
        $totalShippingFee = 0;
        $totalBuyingFee = 0;
        $discountFee = 0;
        $totalPackingFee = 0;

        $accessItemCount = 0; // phu kien duoi 10 ndt
        $normalItemCount = 0;

        //$exchangeRate = \ExchangeRate::getExchange();
        $sysConfig = new SystemConfig();
        $accessMoneyLimit = $sysConfig->retrieveByKey(SystemConfig::ACCESS_MONEY_LIMIT)->config_value;

        foreach ($cartItems AS $cItem) {
            if(!$cItem instanceof \CartItem) {
                continue;
            }
            $totalAmount = $totalAmount + $cItem->getPriceVnd() * $cItem->getAmount();
            $totalQuantity = $totalQuantity + $cItem->getAmount();
            $totalWeight = $totalWeight + $cItem->getWeight();

            if ($cItem->getPrice() < $accessMoneyLimit) { //
                $accessItemCount = $accessItemCount + $cItem->getAmount();
            } else {
                $normalItemCount = $normalItemCount + $cItem->getAmount();
            }
        }

        $cart_item = isset($cartItems[0]) ? $cartItems[0] : array();
        if(!$cart_item instanceof \CartItem) {
            $cart_item =  new \CartItem();
        }
        $site = strtolower($cart_item->site);
        switch ($site) {
            case "taobao" :
                $business_model = "Nhà bán lẻ";
                break;
            case "tmall" :
                $business_model = "Nhà bán lẻ";
                break;
            default :
                $business_model = "Đại lý bán sỉ";
                break;
        }
        $shop_comment = json_decode($cart_item->getCommentShop());


        $orderData = array(
            'shopInfo' => array(
                'shopId' => $cart_item->getShopId(),
                'shopUsername' => $cart_item->getShopUsername(),
                'siteUrl' => 'http://' . $site . '.com',
                'shopLink' => 'http://' . $shopId . '.' . $site . '.com',
                'aliwangwang' => $cart_item->getAliwangwang(),
                'data' => json_decode($cart_item->data,true),
                'businessModel' => $business_model,
                'homeland' => $cart_item->site,
                'comment'=>$shop_comment
            ),
            'orderInfo' => array(
                'totalAmount' => \GlobalHelper::rounding($totalAmount),
                'totalWeight' => $totalWeight,
                'totalQuantity' => $totalQuantity,
                'totalServiceFee' => $totalServiceFee,
                'totalDepositFee' => OrderPeer::calculateDepositAmount($totalAmount),
                'totalCheckingFee' => \GlobalHelper::rounding($totalCheckingFee),
                'totalShippingFee' => \GlobalHelper::rounding($totalShippingFee),
                'totalBuyingFee' => \GlobalHelper::rounding($totalBuyingFee),
                'totalPackingFee' => \GlobalHelper::rounding($totalPackingFee),
                'totalFee' => \GlobalHelper::rounding($totalAmount + $totalServiceFee),
                'totalBuyingDiscountFee' => \GlobalHelper::rounding($discountFee),
            ),
            'cartItems' => $cartItems,
            'services' => $services
        );
        return $orderData;
    }

    /**
     * lay tong so tien trong gio hang theo user id - Quyen
     * @param $user_id
     * @return int
     */
    public static function getPriceCartByUser($user_id){
        $cart_list = self::findByUid($user_id);
        $total_price = 0;
        if(!empty($cart_list)){
            foreach ($cart_list as $carts) {
                if(is_array($carts)){
                    foreach ($carts as $cart) {
                        if(!empty($cart) && ($cart instanceof CartItem)){
                            $total_price += $cart->getPriceVnd()*$cart->getAmount();
                        }
                    }
                }
            }
        }
        return intval($total_price);
    }

    /**
     * Update cart item khi dang nhap. Create By Quyeenf
     * @param $session_id
     * @param $user_id
     */
    public static function mergeCartItem($session_id,$user_id){
        $cart_item_list = CartItem::findByUid($session_id);
        if($cart_item_list){
            foreach ($cart_item_list as $cart_item) {
                if(!empty($cart_item) && ($cart_item instanceof CartItem)){
                    $cart_item->setUid($user_id);

                    $cart_item->save();
                }
            }

        }
    }

    /**
     * @param $data
     * @param $user
     * @param $ajax
     * @return mixed
     */
    public static function addCartItemV2($data,$user,$ajax){

        if (intval($data['item_id']) == 0) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Hệ thống không nhận được thông tin của sản phẩm, vấn đề này chúng tôi sẽ khắc phục sớm nhất.';
            \OrderingTool::sendMailError($data["link_origin"],"bookmarklet","Không lấy được id của sản phẩm",$user["username"]);
            return $ajax;
        }

        if($data["shop_id"] === ""){
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = 'Hệ thống không nhận được thông tin của Người bán, vấn đề này chúng tôi sẽ khắc phục sớm nhất.';
            \OrderingTool::sendMailError($data["link_origin"],"bookmarklet","không nhận được thông tin của người bán",$user["username"]);
            return $ajax;
        }

        $tool = isset($data["tool"]) ? $data["tool"] : "Order Link";
        try{
            $cart_check = CartItem::checkIssetCart($data['item_id'], $user['user_id'], $data['properties'],$data["shop_id"]);

            $price_origin = floatval($data["price_origin"]);

            $price_promotion = floatval($data['price_promotion']);

            $exchange = ExchangeRate::getExchange();

            if ($price_origin <= 0 && $price_promotion <= 0) {
                $ajax->type = AjaxResponse::ERROR;
                $ajax->message = 'Chúng tôi không nhận được giá của sản phẩm . Liên hệ Admin để được hỗ trợ.';
                \OrderingTool::sendMailError($data["link_origin"],"bookmarklet",
                    "không nhận được giá của sản phẩm",$user["username"]);
                return $ajax;
            }

            if ($cart_check && ($cart_check instanceof \CartItem)) {
                $cart_item = $cart_check;
                $quantity_current = $cart_item->getAmount();

                $cart_item->setAmount($quantity_current + $data['quantity']);


                if($data["comment"] != ""){
                    $commentData = array();
                    if ($cart_item->getCommentShop()) {
                        $commentData = json_decode($cart_item->getCommentShop());
                    }
                    $time = time();
                    $created_time = date('d/m/Y H:i:s', $time);
                    $postData = array(
                        'uid'       => $user['user_id'],
                        'content'   => $data["comment"],
                        'created_time' => $created_time
                    );
                    $commentData[] = $postData;

                    $cart_item->setCommentShop(json_encode($commentData));
                    $cart_item->setComment($data["comment"]);
                }
                $cart_item->setTool($tool);

                $result = CartItem::hSetCartItem($cart_item);

                if ($result) {
                    if($data['site'] != "TAOBAO" && $data['site'] != "TMALL"){
                        $price_cn = self::getPriceByPriceTable($data['price_table'],$quantity_current + $data['quantity'],$data['item_id'],$user['user_id']);
                        if($price_cn == 0){
                            $price_cn = $price_origin;
                        }

                        $cart_item->setPrice($price_cn);

                        if($price_promotion > 0){
                            $price_vnd = Common::roundingMoney($price_promotion*$exchange); //numberFormat($price_promotion*$exchange,true);
                        }else{
                            $price_vnd = Common::roundingMoney($price_cn*$exchange);
                        }

                        $cart_item->setPriceVnd($price_vnd);

                        $cart_item->save();

                        self::updatePriceToCart($user['user_id'],$data['item_id'],$price_cn,$price_promotion);
                    }

                    $ajax->type = AjaxResponse::SUCCESS;
                    $ajax->message = 'Thêm sản phẩm vào giỏ thành công';
                    return $ajax;
                } else {
                    $ajax->type = AjaxResponse::ERROR;
                    $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ';
                    return $ajax;
                }
            }else{

                if(!isset($data['shop_id']) || $data['shop_id'] == ''){
                    $ajax->type = AjaxResponse::ERROR;
                    \OrderingTool::sendMailError($data["link_origin"],"bookmarklet","không nhận được thông tin người bán",$user["username"]);
                    $ajax->message = 'Không có thông tin của người bán. Liên hệ Admin để được hỗ trợ.';
                    return $ajax;
                }

                $price_vnd = $price_promotion > 0 ? $price_promotion * $exchange : $price_origin * $exchange;

                $title_origin = $data['title_origin'] != "" ? $data['title_origin'] : "Sản phẩm trên {$data['site']}";

                $title_translated = $data['title_translated'] != "" ? $data['title_translated'] : $title_origin;

                $step = $data['step'];

                if(preg_match("/bookmark/",strtolower($tool))){
                    $properties_translate = GlobalHelper::translate($data['properties_translated']);
                    $title_translated = GlobalHelper::translateTitle($title_translated);
                }else{
                    $properties_translate = $data['properties_translated'];
                }

                $stock = $data['stock'];

                $stock = intval($stock);
                $cart_item = new CartItem();
                $cart_item->setUid($user['user_id']);
//                $cart_item->setUsername($user['username']);
                $cart_item->setShopId(trim($data['shop_id'])); //Shop ID
                $cart_item->setShopUsername(trim($data['shop_name']));
                $cart_item->setTitle(trim($title_translated));
                $cart_item->setItemId($data['item_id']);
                $cart_item->setRequireMin($data['require_min']);
                $cart_item->setLinkOrigin($data['link_origin']);
                $cart_item->setProperties($data['properties']);
                $cart_item->setAmount($data['quantity']);
                $cart_item->setItemImg($data['image_origin']);
                $cart_item->setAliwangwang(trim($data['wangwang']));
                $cart_item->setImgModel(trim($data['image_model']));
                $cart_item->setWeight($data['weight']);
                $cart_item->setStep($step);
                $cart_item->setPropertiesTranslate($properties_translate);
                $cart_item->setPromotionPrice($price_promotion);

                if($data["comment"] != ""){
                    $commentData = array();
                    if ($cart_item->getCommentShop()) {
                        $commentData = json_decode($cart_item->getCommentShop());
                    }
                    $time = time();
                    $created_time = date('d/m/Y H:i:s', $time);
                    $postData = array(
                        'uid'       => $user['user_id'],
                        'content'   => $data["comment"],
                        'created_time' => $created_time
                    );
                    $commentData[] = $postData;

                    $cart_item->setCommentShop(json_encode($commentData));
                    $cart_item->setComment($data["comment"]);
                }
                $cart_item->setPriceTable(json_encode($data['price_table']));
                $cart_item->setStock($stock);
                $cart_item->setPriceVnd(Common::roundingMoney($price_vnd)); //numberFormat($data['price_origin'] * $exchange,true));
                $cart_item->setPrice($data['price_origin']);
                $cart_item->setOuterId($data['outer_id']);
                $cart_item->setSite($data['site']);
                $cart_item->setTimeCreated(new DateTime());
                $cart_item->setTool($tool);
                $result = $cart_item->save();

                if ($result) {
                    if($data['site'] != "TAOBAO" && $data['site'] != "TMALL"){
                        $price_cn = self::getPriceByPriceTable($data['price_table'],$data['quantity'],$data['item_id'],$user['user_id']);
                        if($price_cn == 0){
                            $price_cn = $data['price_origin'];
                        }

                        $cart_item->setPrice($price_cn);

                        if($price_promotion != 0){
                            $price_vnd = Common::roundingMoney($price_promotion* $exchange);//numberFormat($price_promotion* $exchange,true);
                        }else{
                            $price_vnd = Common::roundingMoney($price_cn*$exchange);
                        }

                        $cart_item->setPriceVnd($price_vnd);

                        $cart_item->save();

                        self::updatePriceToCart($user['user_id'],$data['item_id'],$price_cn,$price_promotion );
                    }
                    $ajax->type = AjaxResponse::SUCCESS;
                    $ajax->message = 'Thêm sản phẩm vào giỏ thành công';
                    $ajax->element = \SeuDo\Main::getHomeRouter()->createUrl("cart/success");
                    return $ajax;
                } else {
                    $ajax->type = AjaxResponse::ERROR;
                    $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ';
                    return $ajax;
                }
            }
        }catch (\Flywheel\Exception $e){
            $ajax->type = AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ '.$e;
            return $ajax;
        }
    }

    /**
     * Add new cart Item to db
     * @param $data
     * @param $user
     * @param $properties
     * @param $ajax
     * @return mixed
     */
    public static function addCartItem($data, $user, $properties, $ajax)
    {

        if ($data['item_id'] === "") {
            $ajax->type = AjaxResponse::ERROR;
            $ajax->message = 'Hệ thống không nhận được thông tin của sản phẩm, vấn đề này chúng tôi sẽ khắc phục sớm nhất.';
            return $ajax;
        }

        if(!isset($data['shop_id']) || $data['shop_id'] == ''){
            $ajax->type = AjaxResponse::ERROR;
            $ajax->message = 'Không có thông tin của người bán. Liên hệ Admin để được hỗ trợ.';
            return $ajax;
        }

        $tool = isset($data["tool"]) ? $data["tool"] : "Order Link";
        try {

            $cart_item = CartItem::checkIssetCart($data['item_id'], $user['user_id'], $properties,$data["shop_id"]);

            $require_min = $data['require_min']; //$item->getRequiredMin() ? $item->getRequiredMin() : $data['require_min'];

            $link_origin = $data['link_origin']; // $item->getLinkOrigin() ? $item->getLinkOrigin() : $data['link_origin'];

            $quantity = isset($properties['amount']) ? intval($properties['amount']) : 0;

            $wangwang = isset($data['wangwang']) && $data['wangwang'] != 'undefined' ? $data['wangwang'] : '';

            $comment = isset($data['comment']) ? $data['comment'] : '';

            $data_promotion = isset($data['promotion_price']) ? $data['promotion_price'] : 0;

            $data_price = isset($data["price"]) ? $data["price"] : 0;

            $properties_price = isset($properties['price']) ? floatval($properties['price']) : 0;

            $price_cny = $properties_price > 0 ? $properties_price : $data_price;

            $price_promotion = isset($properties['promotion_price']) && floatval($properties['promotion_price']) > 0 ?
                $properties['promotion_price'] : $data_promotion;

            $price_cny = $price_cny > 0 ? $price_cny : $price_promotion;

            if ($price_cny <= 0 && $price_promotion <= 0) {
                $ajax->type = AjaxResponse::ERROR;
                $ajax->message = 'Chúng tôi không nhận được giá của sản phẩm . Liên hệ Admin để được hỗ trợ.';
                return $ajax;
            }

            if ($quantity <= 0) {
                $ajax->type = AjaxResponse::ERROR;
                $ajax->message = 'Không có sô lượng đặt trên sản phẩm.';
                return $ajax;
            }


            $outer_id = isset($properties['outer_id']) ? $properties['outer_id'] : '';

            $site = "";
            if (isset($data['site'])) {
                $site = $data['site'];
            } elseif ($data['homeland']) {
                $site = $data['homeland'];
            }

            if ($cart_item && ($cart_item instanceof \CartItem)) {

                $quantity_current = $cart_item->getAmount();

                $cart_item->setAmount($quantity_current + $quantity);
                if($comment != ''){
                    $cart_item->setComment($comment);
                }

                $cart_item->setTool($tool);
                $result = $cart_item->save();

                if ($result) {
                    if($site != 'TAOBAO' && $site != "TMALL"){
                        $price_cn = self::getPriceByPriceTable($data['price_table'],$quantity_current + $quantity,$data['item_id'],$user['user_id']);
                        if($price_cn == 0){
                            $price_cn = $price_cny;
                        }

                        $cart_item->setPrice($price_cn);

                        if($price_promotion != 0){
                            $price_vnd =  Common::roundingMoney($price_promotion*ExchangeRate::getExchange());
                        }else{
                            $price_vnd = Common::roundingMoney($price_cn*ExchangeRate::getExchange());
                        }

                        $cart_item->setPriceVnd($price_vnd);

                        $cart_item->save();

                        self::updatePriceToCart($user['user_id'],$data['item_id'],$price_cn,$price_promotion);
                    }

                    $ajax->type = AjaxResponse::SUCCESS;
                    $ajax->message = 'Thêm sản phẩm vào giỏ thành công';
                    return $ajax;
                } else {
                    $ajax->type = AjaxResponse::ERROR;
                    $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ';
                    return $ajax;
                }
            } else {
                $weight = isset($data['weight']) ? $data['weight'] : 0;
                $item_img = isset($data['item_img']) ? $data['item_img'] : 0;
                $properties_type = isset($properties['properties']) ? $properties['properties'] : '';
                $shop_id = $data['shop_id'];

                $step = isset($data['data']['step']) ? $data['data']['step'] : 0;
                if($step == 0){
                    $step = isset($data['step']) ? $data['step'] : 0;
                }
                $properties_translate = isset($data['data']['property_translate'][$properties['properties']]) ?
                    $data['data']['property_translate'][$properties['properties']] : $properties['properties'];

                $stock = isset($data['data']['stock'][$properties['properties']]) ? $data['data']['stock'][$properties['properties']]
                    : 0;

                if($price_promotion != 0){
                    $price_vnd = Common::roundingMoney($price_promotion*ExchangeRate::getExchange());
                }else{
                    $price_vnd = Common::roundingMoney($price_cny*ExchangeRate::getExchange());
                }

                $stock = intval($stock);

                $image_model = isset($properties['imgUrl']) && $properties['imgUrl'] != ''
                && $properties['imgUrl'] != 'undefined'  ?
                    $properties['imgUrl'] : $item_img;
                $cart_item = new CartItem();
                $cart_item->setUid($user['user_id']);
//                $cart_item->setUsername($user['username']);
                $cart_item->setShopId(trim($shop_id)); //Shop ID
                $cart_item->setShopUsername(trim($data['shop']));
                $cart_item->setTitle(trim($data['title']));
                $cart_item->setItemId($data['item_id']);
                $cart_item->setRequireMin($require_min);
                $cart_item->setLinkOrigin($link_origin);
                $cart_item->setProperties($properties_type);
                $cart_item->setAmount($quantity);
                $cart_item->setItemImg($item_img);
                $cart_item->setAliwangwang(trim($wangwang));
                $cart_item->setImgModel(trim($image_model));
                $cart_item->setWeight($weight);
                $cart_item->setStep($step);
                $cart_item->setPropertiesTranslate($properties_translate);
                $cart_item->setPromotionPrice($price_promotion);
                $cart_item->setPrice($price_cny);
                $cart_item->setPriceVnd($price_vnd);
                $cart_item->setComment($comment);
                $cart_item->setPriceTable(json_encode($data['price_table']));
                $cart_item->setData(json_encode($data['data']));
                $cart_item->setStock($stock);
                $cart_item->setOuterId($outer_id);
                $comment = isset($data['comment']) ? $data['comment'] : '';
                if($comment != ''){
                    $cart_item->setComment($comment);
                }

                $cart_item->setSite($site);
                $cart_item->setTool($tool);
                $cart_item->setTimeCreated(date('Y-m-d H:i:s'), time());

                $result = $cart_item->save();

                if ($result) {
                    if($site != "TAOBAO" && $site != "TMALL"){
                        $price_cn = self::getPriceByPriceTable($data['price_table'],$quantity,$data['item_id'],$user['user_id']);
                        if($price_cn == 0){
                            $price_cn = $price_cny;
                        }

                        $cart_item->setPrice($price_cn);

                        if($price_promotion != 0){
                            $price_vnd = Common::roundingMoney($price_promotion*ExchangeRate::getExchange());
                        }else{
                            $price_vnd = Common::roundingMoney($price_cn*ExchangeRate::getExchange());
                        }

                        $cart_item->setPriceVnd($price_vnd);

                        $cart_item->save();

                        self::updatePriceToCart($user['user_id'],$data['item_id'],$price_cn,$price_promotion);
                    }

                    $ajax->type = AjaxResponse::SUCCESS;
                    $ajax->message = 'Thêm giỏ hàng thành công';
                    $ajax->element = \SeuDo\Main::getHomeRouter()->createUrl("cart/success");
                    return $ajax;
                } else {
                    $ajax->type = AjaxResponse::ERROR;
                    $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ';
                    return $ajax;
                }
            }
        } catch (\Exception $e) {
            $ajax->type = AjaxResponse::ERROR;
            $ajax->message = 'Có lỗi khi thêm sản phẩm vào giỏ';
            return $ajax;
        }
    }

    /**
     * Kiểm tra tồn tại của cart
     * @param $item_id
     * @param $user_id
     * @param $properties
     * @param $shop_id
     * @return CartItem
     */
    public static function checkIssetCart($item_id, $user_id, $properties,$shop_id)
    {
        if(is_array($properties) && isset($properties['properties'])){
            $properties = trim($properties['properties']);
        }else{
            $properties = trim($properties);
        }

        $properties = md5($properties);

        $key = REDIS_CART."{$user_id}_{$shop_id}_{$item_id}_{$properties}";

        $cart = self::findByKey($key);

        return $cart;
    }


    /**
     * @param $user_id
     * @param $item_site_id
     * @param $price_cny
     * @param $promotion
     */
    public static function updatePriceToCart($user_id,$item_site_id,$price_cny,$promotion = 0.0){
        $cart_list = CartItem::findByUidAndItemId($user_id,$item_site_id);
        if($promotion != 0){
            $price_vnd = Common::roundingMoney($promotion*ExchangeRate::getExchange());
        }else{
            $price_vnd = Common::roundingMoney($price_cny*ExchangeRate::getExchange());
        }
        if(!empty($cart_list)){
            foreach ($cart_list as $carts) {
                if(is_array($carts)){
                    foreach ($carts as $cart) {
                        if(!empty($cart) && $cart instanceof CartItem){
                            $cart->setPrice($price_cny);
                            $cart->setPriceVnd($price_vnd);
                            if($promotion != 0){
                                $cart->setPromotionPrice($promotion);
                            }
                            $cart->save();
                        }
                    }
                }

            }
        }
    }
}