<?php 

abstract class CartItemOrigin {


    public $id;
    public $uid;
    public $shop_id;
    public $shop_username;
    public $aliwangwang;
    public $step;
    public $title;
    public $item_id;
    public $site;
    public $require_min;
    public $stock;
    public $link_origin;
    public $properties;
    public $properties_translate;
    public $outer_id;
    public $amount;
    public $item_img;
    public $img_model;
    public $price;
    public $promotion_price;
    public $price_vnd;
    public $price_table;
    public $weight;
    public $comment;
    public $comment_shop;
    public $data;
    public $tool;
    public $time_created;
    public $time_updated;

    public function __construct($data = array()){
        if(!empty($data)){
            $this->hydrate($data);
        }
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $aliwangwang
     */
    public function setAliwangwang($aliwangwang)
    {
        $this->aliwangwang = $aliwangwang;
    }

    /**
     * @return mixed
     */
    public function getAliwangwang()
    {
        return $this->aliwangwang;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment_shop
     */
    public function setCommentShop($comment_shop)
    {
        $this->comment_shop = $comment_shop;
    }

    /**
     * @return mixed
     */
    public function getCommentShop()
    {
        return $this->comment_shop;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $img_model
     */
    public function setImgModel($img_model)
    {
        $this->img_model = $img_model;
    }

    /**
     * @return mixed
     */
    public function getImgModel()
    {
        return $this->img_model;
    }

    /**
     * @param mixed $item_id
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * @param mixed $item_img
     */
    public function setItemImg($item_img)
    {
        $this->item_img = $item_img;
    }

    /**
     * @return mixed
     */
    public function getItemImg()
    {
        return $this->item_img;
    }

    /**
     * @param mixed $link_origin
     */
    public function setLinkOrigin($link_origin)
    {
        $this->link_origin = $link_origin;
    }

    /**
     * @return mixed
     */
    public function getLinkOrigin()
    {
        return $this->link_origin;
    }

    /**
     * @param mixed $outer_id
     */
    public function setOuterId($outer_id)
    {
        $this->outer_id = $outer_id;
    }

    /**
     * @return mixed
     */
    public function getOuterId()
    {
        return $this->outer_id;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price_table
     */
    public function setPriceTable($price_table)
    {
        $this->price_table = $price_table;
    }

    /**
     * @return mixed
     */
    public function getPriceTable()
    {
        return $this->price_table;
    }

    /**
     * @param mixed $price_vnd
     */
    public function setPriceVnd($price_vnd)
    {
        $this->price_vnd = $price_vnd;
    }

    /**
     * @return mixed
     */
    public function getPriceVnd()
    {
        return $this->price_vnd;
    }

    /**
     * @param mixed $promotion_price
     */
    public function setPromotionPrice($promotion_price)
    {
        $this->promotion_price = $promotion_price;
    }

    /**
     * @return mixed
     */
    public function getPromotionPrice()
    {
        return $this->promotion_price;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param mixed $properties_translate
     */
    public function setPropertiesTranslate($properties_translate)
    {
        $this->properties_translate = $properties_translate;
    }

    /**
     * @return mixed
     */
    public function getPropertiesTranslate()
    {
        return $this->properties_translate;
    }

    /**
     * @param mixed $require_min
     */
    public function setRequireMin($require_min)
    {
        $this->require_min = $require_min;
    }

    /**
     * @return mixed
     */
    public function getRequireMin()
    {
        return $this->require_min;
    }

    /**
     * @param mixed $shop_id
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    /**
     * @param mixed $shop_username
     */
    public function setShopUsername($shop_username)
    {
        $this->shop_username = $shop_username;
    }

    /**
     * @return mixed
     */
    public function getShopUsername()
    {
        return $this->shop_username;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * @return mixed
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $time_created
     */
    public function setTimeCreated($time_created)
    {
        $this->time_created = $time_created;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * @param mixed $time_updated
     */
    public function setTimeUpdated($time_updated)
    {
        $this->time_updated = $time_updated;
    }

    /**
     * @return mixed
     */
    public function getTimeUpdated()
    {
        return $this->time_updated;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $tool
     */
    public function setTool($tool)
    {
        $this->tool = $tool;
    }

    /**
     * @return mixed
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * hydrate data to object
     *
     * @param object | array $data
     */
    public function hydrate($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach ($data as $p=>$value) {
            if(is_numeric($p)){
                continue;
            }
            $this->$p = $value;
        }
    }
}