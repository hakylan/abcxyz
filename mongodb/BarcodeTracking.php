<?php
namespace mongodb;

/**
 * Class BarcodeTracking
 * @package mongodb
 *
 * @property string $barcode
 * @property string $activity
 * @property string $division
 * @property string $warehouse
 * @property string $action_time
 */
class BarcodeTracking {
    protected $_attributes = array();
    protected $_collection = 'barcode';
    private $_isNew = true;
    private $_id;

    /**
     * @return \MongoQB\Builder
     */
    public static function getConnection() {
        return $conn = \SeuDo\MongoDB::getConnection('barcode_tracking');
    }

    /**
     * @return bool
     */
    public function save() {
        $data = $this->_attributes;
        unset($data['_id']);

        if (!$this->_isNew) {
            if (!isset($data['created_time'])) {
                $this->created_time = $data['created_time'] = new \MongoDate();
            }
        }
        $conn = self::getConnection();

        if ($this->_isNew) {
            $this->_id = $conn->insert($this->_collection, $data);
            $this->_isNew = false;
            return (bool) $this->getId();
        } else {
            return $conn->where(array('_id' => new \MongoId($this->getId())))
                ->set($data)
                ->update($this->_collection);
        }
    }

    /**
     * Get object data's attribute as array
     * @return array
     */
    public function toArray() {
        return $this->_attributes;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->_id;
    }

    public function hydrate($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach($data as $property => $value) {
            $this->$property = $value;
        }
    }

    public function __set($name, $value) {
        $this->_attributes[$name] = $value;
    }

    public function __get($name) {
        return isset($this->_attributes[$name])? $this->_attributes[$name] : null;
    }
} 