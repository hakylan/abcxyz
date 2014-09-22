<?php
namespace mongodb;

use Flywheel\Util\Inflection;
use mongodb\DeliveryBillCommentResource\Activity;
use mongodb\DeliveryBillCommentResource\BaseContext;
use mongodb\DeliveryBillCommentResource\Chat;
use mongodb\DeliveryBillCommentResource\Image;
use mongodb\DeliveryBillCommentResource\Log;
use MongoQB\Exception;
use SeuDo\MongoDB;

/**
 * Class OrderComment
 * @package mongodb
 *
 * @method static \mongodb\OrderComment findOneById(integer $id) find object in database by id
 *
 */
class DeliveryBillComment {

    const
        TYPE_INTERNAL = 'INTERNAL',
        TYPE_EXTERNAL = 'EXTERNAL';

    protected static $_collectionName = 'delivery_bill_comment';
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

    /*
     * dau's
     */
    public function setDomesticShippingId($domestic_shipping_id) {
        if (is_string($domestic_shipping_id) && strlen($domestic_shipping_id) > 0) {
            $domestic_shipping_id = intval($domestic_shipping_id);
        }
        if (is_int($domestic_shipping_id) && ($domestic_shipping_id > 0)) {
            $this->_attributes['domestic_shipping_id'] = $domestic_shipping_id;
        }
    }

    /*
     * dau's
     * return int
     */
    public function  getDomesticShippingId() {
        if (array_key_exists('domestic_shipping_id', $this->_attributes)) {
            return $this->_attributes['domestic_shipping_id'];
        }
        return 0;
    }

    /*
   * dau's
   * get object DeliveryBillComment with data item (array) from mongodb
   * Note: all data get mongodb is type: string
   */
    public static function getDeliveryBillCommentWithData($item) {
        if (is_array($item)) {
            $delivery_bill_comment = new self();
            if (array_key_exists('_id', $item)) {
                $obj_id = $item['_id']; // Object id
                $str_id = strval($obj_id);
                $delivery_bill_comment->setId($str_id);
            }
            $create_by = isset($item['created_by']) ? $item['created_by'] : 0;
            $delivery_bill_comment->setDomesticShippingId(@$item['domestic_shipping_id']);
            $delivery_bill_comment->setScope(@$item['scope']);
            $delivery_bill_comment->setCreatedBy($create_by);
            $delivery_bill_comment->setIsPublicProfile($item['is_public_profile']);
            $delivery_bill_comment->setCreatedTime($item['created_time']);

            $type_context = $item['type_context'];
            $delivery_bill_comment->setTypeContext($type_context);
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
                $delivery_bill_comment->setContext($context);
            }
            return $delivery_bill_comment;
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
                $results[] = self::getDeliveryBillCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
            return $results;
        }
    }


    public static function findByDomesticShippingId($domestic_shipping_id, $scope='', $type_context='', $limit=-1) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('domestic_shipping_id' => $domestic_shipping_id);
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
                $results[] = self::getDeliveryBillCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
            return $results;
        }
    }


    public static function findOneByDomesticShippingId($domestic_shipping_id) {
        $result = null;
        try {
            $db = MongoDB::getConnection();
            $conditions = array('domestic_shipping_id' => $domestic_shipping_id);
            $fields = array('created_time' => -1);
            $items = $db->where($conditions)->orderBy($fields)->limit(1)->get(self::$_collectionName);
            foreach ($items as $item) {
                $result = self::getDeliveryBillCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
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
     * return OrderComment[]
     */
    public static function findByScope($domestic_shipping_id, $scope, $type_context='', $limit=-1) {
        $results = array();
        try {
            $db = MongoDB::getConnection();
            $conditions = array('domestic_shipping_id' => $domestic_shipping_id);
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
                $results[] = self::getDeliveryBillCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
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
                $result = self::getDeliveryBillCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
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
                $results[] = self::getDeliveryBillCommentWithData($item);
            }
            return $results;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
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
                $result = self::getDeliveryBillCommentWithData($item);
                break;
            }
            return $result;
        } catch (\Flywheel\MongoDB\Exception $e) {
            \SeuDo\Logger::factory('delivery_bill_comment')->addError($e->getMessage());
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
        return $conn = \SeuDo\MongoDB::getConnection('delivery_bill_comment');
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
     * @return DeliveryBillCommentResource\Activity|DeliveryBillCommentResource\Chat|DeliveryBillCommentResource\Log
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