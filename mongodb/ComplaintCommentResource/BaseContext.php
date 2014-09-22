<?php
namespace mongodb\ComplaintCommentResource;


use mongodb\ComplaintComment;

abstract class BaseContext {

    const
        TYPE_CHAT = 'CHAT',
        TYPE_ACTIVITY = 'ACTIVITY',
        TYPE_LOG = 'LOG';
    protected $_attributes = array();

    /** @var ComplaintComment  */
    protected $_owner;

    protected $_type;

    /**
     * @param $data
     * @param $type
     * @return Activity|Chat|Log
     */
    public static function createFromDataAndType($data, $type) {
        switch ($type) {
            case 'CHAT' :
                return new Chat($data);
            case 'ACTIVITY':
                return new Activity($data);
            case 'LOG':
                return new Log($data);
        }
    }

    public function setOwner(ComplaintComment $owner) {
        $this->_owner = $owner;
    }

    public function getType() {
        return strtoupper($this->_type);
    }

    public function toJSon() {
        return json_encode($this->_attributes);
    }
    public function toArray() {
        return $this->_attributes;
    }

    public function __set($name, $value) {
        $this->_attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->_attributes[$name];
    }
}