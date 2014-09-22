<?php
namespace mongodb\ComplaintCommentResource;

use mongodb\ComplaintComment;

class Image {
    protected $_url;


    /** @var ComplaintComment */
    protected $_owner;

    public function __construct($url = null) {
        if ($url) {
            $this->setUrl($url);
        }
    }

    public function setOwner(ComplaintComment $owner) {
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