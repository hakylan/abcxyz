<?php
namespace mongodb\ComplaintSellerCommentResource;

class Chat extends BaseContext {
    protected $type = BaseContext::TYPE_CHAT;


    public function __construct($message) {
        $this->_attributes['type'] = $this->type;
        if (is_array($message) && isset($message['message'])) {
            $this->_attributes['message'] = $message['message'];
        } else {
            $this->_attributes['message'] = $message;
        }
    }

    public function getMessage() {
        return $this->_attributes['message'];
    }
} 