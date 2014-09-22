<?php
/**
 * Created by PhpStorm.
 * User: Hindua
 * Date: 4/4/14
 * Time: 1:43 PM
 */

namespace mongodb;

use mongodb\Exception;
use mongodb\OrderCommentResource\BaseContext;

class OrderItemComment {
    public static $_collectionName = 'order_item_comment';
    protected $_attributes = array();
    /**
     * @var \MongoId
     */
    protected $_id;
    private $_isNew = true;

    public function __construct() {
    }

    /**
     * dau's
     */
    public function setId($id)
    {
        if (is_object($id)) {
            $id = strval($id);
        }
        $this->_id = $id;
    }

    /**
     * @return \MongoId
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param mixed $item_id
     */
    public function setItemId($item_id)
    {
        if (is_string($item_id) && strlen($item_id) > 0) {
            $item_id = intval($item_id);
        }
        if (is_int($item_id) && $item_id > 0) {
            $this->_attributes['item_id'] = $item_id;
        }
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        if (array_key_exists('item_id', $this->_attributes)) {
            return $this->_attributes['item_id'];
        }
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->_attributes['message'] = $message;
    }

    public function setType($type)
    {
        $this->_attributes['type'] = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if(array_key_exists('type', $this->_attributes)) {
            return $this->_attributes['type'];
        }
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if(array_key_exists('message', $this->_attributes)) {
            return $this->_attributes['message'];
        }
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id)
    {
        if (is_string($order_id) && strlen($order_id) > 0) {
            $order_id = intval($order_id);
        }
        if (is_int($order_id) && $order_id > 0) {
            $this->_attributes['order_id'] = $order_id;
        }
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        if (array_key_exists('order_id', $this->_attributes)) {
            return $this->_attributes['order_id'];
        }
    }

    /*
    * dau's
    *
    */
    public function setCreatedBy($created_by) {
        if (is_string($created_by) && strlen($created_by) > 0) {
            $created_by = intval($created_by);
        }
        if (is_int($created_by) && ($created_by > 0)) {
            $this->_attributes['created_by'] = $created_by;
            return true;
        }
        return false;
    }

    /*
     * dau's
     * return int
     */
    public function getCreatedBy() {
        if(array_key_exists('created_by', $this->_attributes)) {
            return $this->_attributes['created_by'];
        }
        return 0;
    }

    /**
     * @param \MongoDate $created_time
     */
    public function setCreatedTime($created_time)
    {
        if ($created_time instanceof \MongoDate) {
            $this->_attributes['created_time'] = $created_time;
        }
    }

    /**
     * @return \MongoDate
     */
    public function getCreatedTime()
    {
        if (array_key_exists('created_time', $this->_attributes)) {
            return $this->_attributes['created_time'];
        }
    }

    public static function getConnection() {
        return $conn = \SeuDo\MongoDB::getConnection(self::$_collectionName);
    }

    public function save() {
        $data = $this->_attributes;
        $conn = self::getConnection();
        if ($this->_isNew) {
            if (!isset($data['created_time'])) {
                $this->created_time = $data['created_time'] = new \MongoDate();
            }

            if(($this->_id = $conn->insert(self::$_collectionName, $data))) {
                $this->_isNew = false;
                return true;
            }
        } else {
            $conn->where(array('_id' => new \MongoId($this->getId())))
                ->set($data)
                ->update(self::$_collectionName);
        }
        return false;
    }

    public static function getOrderItemCommentWithData($item) {
        $result = null;
        try {
            if (is_array($item)) {
                $order_item_comment = new OrderItemComment();
                if (array_key_exists('_id', $item)) {
                    $obj_id = $item['_id'];
                    $str_id = strval($obj_id);
                    $order_item_comment->setId($str_id);
                }
                $type = isset($item["type"]) ? $item["type"] : BaseContext::TYPE_CHAT;
                $order_item_comment->setOrderId($item['order_id']);
                $order_item_comment->setItemId($item['item_id']);
                $order_item_comment->setMessage($item['message']);
                $order_item_comment->setCreatedBy($item['created_by']);
                $order_item_comment->setCreatedTime($item['created_time']);
                $order_item_comment->setType($type);
                $result = $order_item_comment;
            }
            return $result;
        } catch (Exception $e) {
            \SeuDo\Logger::factory('order_item_comment')->addError($e->getMessage());
            return $result;
        }
    }

    /*
     * dau's
     * return OrderItemComment[]
     */
    public static function findByItemId($order_id, $item_id, $limit=-1) {
        $results = array();
        try {
            $db = self::getConnection();
            $conditions = array('order_id' => $order_id, 'item_id' => $item_id);
            $fields = array('created_time' => -1);
            // Get all
            if ($limit > 0) {
                $items = $db->where($conditions)->orderBy($fields)->limit($limit)->get(self::$_collectionName);
            } else {
                $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            }
            foreach ($items as $item) {
                $results[] = self::getOrderItemCommentWithData($item);
            }
            return $results;
        } catch (\MongoQB\Exception $e) {
            \SeuDo\Logger::factory('order_item_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return ('page': page, 'pages': total page, 'data' => OrderItemComment[])
     */
    public static function findPageByItemId($order_id, $item_id, $page, $page_size, $type) {
        $results = array('page' => 1, 'pages' => 1, 'data' => array());
        try {
            $db = self::getConnection();
            if($type != ""){
                $conditions = array('order_id' => $order_id, 'item_id' => $item_id, 'type' => $type);
            }else{
                $conditions = array('order_id' => $order_id, 'item_id' => $item_id);
            }

            $fields = array('created_time' => -1);
            $comments = array();
            $count = $db->where($conditions)->orderBy($fields)->count(self::$_collectionName);
            $limit = 3;
            if ($page_size > 0) {
                $limit = $page_size;
            }
            $pages = intval(ceil($count / $limit));
            // Over range total page
            if ($page < 1) {
                $page = 1;
            }
            if ($page > $pages) {
                return $results;
            }
            $offset = ($page-1) * $limit;
            $items = $db->where($conditions)->orderBy($fields)->offset($offset)->limit($limit)->get(self::$_collectionName);
            foreach ($items as $item) {
                $comments[] = self::getOrderItemCommentWithData($item);
            }
            $results['page'] = $page;
            $results['pages'] = $pages;
            $results['data'] = $comments;
            $results['total_record'] = isset($count) ? $count : 0;
            return $results;
        } catch (Exception $e) {
            \SeuDo\Logger::factory('order_item_comment')->addError($e->getMessage());
            return $results;
        }
    }

    public function __set($name, $value) {
        $this->_attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->_attributes[$name];
    }
} 