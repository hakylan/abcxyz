<?php
namespace mongodb\OrderCommentResource;

use mongodb\OrderComment;

class Image {
    protected $_url;

    /** @var OrderComment */
    protected $_owner;

    public function __construct($url = null) {
        if ($url) {
            $this->setUrl($url);
        }
    }

    public function setOwner(OrderComment $owner) {
        $this->_owner = $owner;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->_url;
    }

    public static function createFromFilePath($url) {
        return new self($url);
    }
} 