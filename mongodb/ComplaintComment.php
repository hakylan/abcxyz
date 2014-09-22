<?php
namespace mongodb;

use Flywheel\Util\Inflection;
use mongodb\ComplaintCommentResource\Activity;
use mongodb\ComplaintCommentResource\BaseContext;
use mongodb\ComplaintCommentResource\Chat;
use mongodb\ComplaintCommentResource\Image;
use mongodb\ComplaintCommentResource\Log;
use MongoQB\Exception;
use SeuDo\MongoDB;

/**
 * Class OrderComment
 * @package mongodb
 *
 * @method static \mongodb\ComplaintComment findOneById(integer $id) find object in database by id
 *
 */
class ComplaintComment {

    const
        TYPE_INTERNAL = 'INTERNAL',
        TYPE_EXTERNAL = 'EXTERNAL';

    protected static $_collectionName = 'complaint_comment';
    /**
     * @var Image[]
     */
    protected $_images = array();
    /**
     * @var BaseContext
     */
    protected $_context;
    protected $_attributes = array();
    /**
     * @var \MongoId
     */
    protected $_id;
    private $_isNew = true;

    function __construct()
    {
        $this->created_time = new \MongoDate();
    }

    public function setComplaintId($complaint_id) {
        if (is_string($complaint_id) && strlen($complaint_id) > 0) {
            $complaint_id = intval($complaint_id);
        }
        if (is_int($complaint_id) && ($complaint_id > 0)) {
            $this->_attributes['complaint_id'] = $complaint_id;
        }
    }

    public function setItemId($item_id) {
        if (is_string($item_id) && strlen($item_id) > 0) {
            $item_id = intval($item_id);
        }
        if (is_int($item_id) && ($item_id > 0)) {
            $this->_attributes['item_id'] = $item_id;
        }
    }

    public function setOrderId($order_id) {
        if (is_string($order_id) && strlen($order_id) > 0) {
            $order_id = intval($order_id);
        }
        if (is_int($order_id) && ($order_id > 0)) {
            $this->_attributes['order_id'] = $order_id;
        }
    }

    /*
     * dau's
     * return int
     */
    public function  getOrderId() {
        if (array_key_exists('order_id', $this->_attributes)) {
            return $this->_attributes['order_id'];
        }
        return 0;
    }

    /*
   * dau's
   * get object OrderComment with data item (array) from mongodb
   * Note: all data get mongodb is type: string
   */
    public static function getComplaintCommentWithData($item) {
        if (is_array($item)) {
            $complaint_comment = new self();
            if (array_key_exists('_id', $item)) {
                $obj_id = $item['_id']; // Object id
                $str_id = strval($obj_id);
                $complaint_comment->setId($str_id);
            }
            $create_by = isset($item['created_by']) ? $item['created_by'] : 0;
            $complaint_comment->setComplaintId(@$item['complaint_id']);
            $complaint_comment->setItemId(@$item['item_id']);
            $complaint_comment->setOrderId(@$item['order_id']);
            $complaint_comment->setContent(@$item['content']);
            $complaint_comment->setComplaintCode(@$item['complaint_code']);
            $complaint_comment->setScope(@$item['scope']);
            $complaint_comment->setCreatedBy($create_by);
            $complaint_comment->setIsPublicProfile($item['is_public_profile']);
            $complaint_comment->setCreatedTime($item['created_time']);

            $type_context = $item['type_context'];
            $complaint_comment->setTypeContext($type_context);
            $data = $item['context'];
            $context = null;
            if (is_array($data)) {
                if ($type_context == BaseContext::TYPE_CHAT) {
                    $context = new Chat($data);
                } elseif ($type_context == BaseContext::TYPE_ACTIVITY) {
                    $context = new Activity($data);
                } elseif ($type_context == BaseContext::TYPE_LOG) {
                    $context = new Log($data);
                }
            }
            if ($context != null) {
                $complaint_comment->setContext($context);
            }
            return $complaint_comment;
        }
        return null;
    }

    /**
     * find By condition
     * @param array $condition
     * @return array
     */
    public static function findByCondition($condition = array()){
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $fields = array('created_time' => -1);

            $items = $db->where($condition)->orderBy($fields)->get(self::$_collectionName);
            foreach ($items as $item) {
                $results[] = self::getOrderCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment[]
     */
    public static function findByOrderId($order_id, $scope='', $type_context='', $limit=-1) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('order_id' => $order_id);
            if ($type_context==BaseContext::TYPE_CHAT || $type_context==BaseContext::TYPE_ACTIVITY
                || $type_context==BaseContext::TYPE_LOG) {
                $conditions['type_context'] = $type_context;
            }
            if (strlen($scope) > 0) {
                $conditions['scope'] = $scope;
            }
            $fields = array('created_time' => -1);
            if ($limit > 0) {
                $items = $db->where($conditions)->orderBy($fields)->limit($limit)->get(self::$_collectionName);
            } else {
                $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            }
            foreach ($items as $item) {
                $results[] = self::getComplaintCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment
     */
    public static function findOneByOrderId($order_id) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions = array('order_id' => $order_id);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getOrderCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $result;
        }
    }

    /*
     * dau's
     */
    public function setComplaintCode($complaint_code) {
        if (is_string($complaint_code) && strlen($complaint_code) > 0) {
            $complaint_code = intval($complaint_code);
        }
        if (is_int($complaint_code) && ($complaint_code > 0)) {
            $this->_attributes['complaint_code'] = $complaint_code;
            return true;
        }
        return false;
    }

    /*
     * dau's
     * return int
     */
    public function getOrderCode() {
        if(array_key_exists('order_code', $this->_attributes)) {
            return $this->_attributes['order_code'];
        }
        return 0;
    }

    /*
     * dau's
     * return OrderComment[]
     */
    public static function findByOrderCode($order_code) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('order_code' => $order_code);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            foreach ($items as $item) {
                $results[] = self::getOrderCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment
     */
    public static function findOneByOrderCode($order_code) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions = array('order_code' => $order_code);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getOrderCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $result;
        }
    }

    /*
    * dau's
    */
    public function setContent($value) {
        $content = '';
        if (is_string($value) && (strlen($value) > 0)) {
            $content = $value;
        }
        $this->_attributes['content'] = $content;
    }

    /*
     * dau's
     * return int
     */
    public function getContent() {
        $content = '';
        if(array_key_exists('content', $this->_attributes)) {
            $content = $this->_attributes['content'];
        }
        return $content;
    }

    /*
     * dau's
     * return OrderComment[]
     */
    public static function findByContent($content) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('content' => $content);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            foreach ($items as $item) {
                $results[] = self::getOrderCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment
     */
    public static function findOneByContent($content) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions = array('content' => $content);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getOrderCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $result;
        }
    }

    /*
       * dau's
       */
    public function setScope($value) {
        $scope = '';
        if (is_string($value) && (strlen($value) > 0)) {
            $scope = $value;
        }
        $this->_attributes['scope'] = $scope;
    }

    /*
     * dau's
     * return int
     */
    public function getScope() {
        $scope = '';
        if(array_key_exists('scope', $this->_attributes)) {
            $scope = $this->_attributes['scope'];
        }
        return $scope;
    }

    /*
     * dau's
     * return ComplaintComment[]
     */
    public static function findByScope($complaint_id, $scope, $type_context='', $limit=-1) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('complaint_id' => $complaint_id);
            if ($type_context==BaseContext::TYPE_CHAT || $type_context==BaseContext::TYPE_ACTIVITY
                || $type_context==BaseContext::TYPE_LOG) {
                $conditions['type_context'] = $type_context;
            }
            $conditions['scope'] = $scope;
            $fields = array('created_time' => -1);
            if ($limit > 0) {
                $items = $db->where($conditions)->orderBy($fields)->limit($limit)->get(self::$_collectionName);
            } else {
                $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            }
            foreach ((array)$items as $item) {
                $results[] = self::getComplaintCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('complaint_comment')->addError($e->getMessage());
            return $results;
        }
    }

    public static function findPageByComplaintId($complaint_id, $page, $page_size, $scope) {
        $results = array('page' => 1, 'pages' => 1, 'data' => array());
        try {
            $db = self::getConnection();
            $conditions = array('complaint_id' => $complaint_id, 'scope' => $scope);
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
                $comments[] = self::getComplaintCommentWithData($item);
            }
            $results['page'] = $page;
            $results['pages'] = $pages;
            $results['data'] = $comments;
            return $results;
        } catch (Exception $e) {
            \SeuDo\Logger::factory('complaint_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment
     */
    public static function findOneByScope($scope) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions = array('scope' => $scope);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getOrderCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $result;
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

    /*
     * dau's
     * return OrderComment[]
     */
    public static function findByCreatedBy($created_by) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('created_by' => $created_by);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->get(self::$_collectionName);
            foreach ($items as $item) {
                $results[] = self::getOrderCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $results;
        }
    }

    /*
     * dau's
     * return OrderComment
     */
    public static function findOneByCreatedBy($created_by) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions['created_by'] = $created_by;
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getOrderCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('order_comment')->addError($e->getMessage());
            return $result;
        }
    }

    public function hydrate($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach($data as $property => $value) {
            if ($property == 'context') {
                $this->setContext($this->createContext($data, $data['type']));
            } else if ($property == 'images') {
                foreach($value as $img) {
                    $this->addImage(Image::createFromFilePath($img));
                }
            } else {
                $this->$property = $value;
            }
        }
    }

    /*
     * dau's
     * if set objectid => str_id
     */
    public function setId($id) {
        if (is_object($id)) {
            $id = strval($id);
        }
        $this->_id = $id;
    }

    /**
     * @return \MongoId
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @return \MongoQB\Builder
     */
    public static function getConnection() {
        return $conn = \SeuDo\MongoDB::getConnection('order_comment');
    }

    public function save() {
        $data = $this->_attributes;
        $data['images'] = $this->getImagesBaseUrl();
        $context = $this->_context->toArray();
        $type_context = '';
        if (array_key_exists('type', $context)) {
            $type_context = $context['type'];
            unset($context['type']);
        }
        $data['type_context'] = $type_context;
        $data['context'] = $context;
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

    public function setContext(BaseContext $context) {
        $this->_context = $context;
        $this->_context->setOwner($this);
    }

    public function addImage(Image $image) {
        $image->setOwner($this);
        $this->_images[] = $image;
    }

    public function getImagesBaseUrl() {
        $result = array();
        foreach($this->_images as $image) {
            $result[] = $image->getUrl();
        }

        return $result;
    }

    /**
     * @param $data
     * @param $type
     * @return OrderCommentResource\Activity|OrderCommentResource\Chat|OrderCommentResource\Log
     */
    public function createContext($data, $type) {
        return BaseContext::createFromDataAndType($data, $type);
    }

    public function __call($method, $params) {
        if (strrpos($method, 'set') === 0
            && isset($params[0]) && null !== $params[0]) {
            $name = Inflection::camelCaseToHungary(substr($method, 3, strlen($method)));

            $this->_attributes[$name] = $params[0];

            return true;
        }

        if (strpos($method, 'get') === 0) {
            $name = Inflection::camelCaseToHungary(substr($method, 3, strlen($method)));
            return isset($this->_attributes[$name])? $this->_attributes[$name]: null;
        }

        $lcMethod = strtolower($method);
        if (substr($lcMethod, 0, 6) == 'findby') {
            $by = substr($method, 6, strlen($method));
            $method = 'findBy';
            $one = false;
        } else if(substr($lcMethod, 0, 9) == 'findoneby') {
            $by = substr($method, 9, strlen($method));
            $method = 'findOneBy';
            $one = true;
        }

        if ($method == 'findBy' || $method == 'findOneBy') {
            if (isset($by)) {
                if (!isset($params[0])) {
                    throw new Exception('You must specify the value to ' . $method);
                }

                /*if ($one) {
                    $fieldName = static::_resolveFindByFieldsName($by);
                    if(false == $fieldName) {
                        throw new Exception('Column ' .$fieldName .' not found!');
                    }
                }*/

                return static::findBy($by, $params, $one);
            }
        }
    }

    /**
     * @param boolean $is_public_profile
     */
    public function setIsPublicProfile($is_public_profile)
    {
        $this->_attributes['is_public_profile'] = $is_public_profile;
    }

    /**
     * @return boolean
     */
    public function getIsPublicProfile()
    {
        $is_public_profile = false;
        if (array_key_exists('is_public_profile', $this->_attributes)) {
            $is_public_profile = $this->_attributes['is_public_profile'];
        }
        return $is_public_profile;
    }

    /**
     * @param mixed $type_context
     */
    public function setTypeContext($type_context)
    {
        $this->_attributes['type_context'] = $type_context;
    }

    /**
     * @return mixed
     */
    public function getTypeContext()
    {
        if (array_key_exists('type_context', $this->_attributes)) {
            return $this->_attributes['type_context'];
        }
    }

    /**
     * @param \MongoDate $created_time
     */
    public function setCreatedTime($created_time)
    {
        $this->_attributes['created_time'] = $created_time;
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

    public function __set($name, $value) {
        $this->_attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->_attributes[$name];
    }

    /*
     * dau's
     * get Context: Activity, Log, Chat
     */
    public function getContext() {
        return $this->_context;
    }
} 