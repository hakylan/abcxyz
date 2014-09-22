<?php
namespace mongodb\ComplaintSellerCommentResource;

class Log extends BaseContext {
    protected $type = BaseContext::TYPE_LOG;


    public function __construct($data) {
        $this->_attributes['type'] = $this->type;

        $message = '';
        // Chưa biết dạng log: Ex: Khiếu nại vừa hủy hoặc vừa được đặt
        if (is_array($data)) {
            // Lấy nội dung
            if (array_key_exists('message', $data)) {
                $message = $data['message'];
            }
            $excludes = array('user_id', $message);
            foreach ($data as $key => $value) {
                if (in_array($key, $excludes)) {
                    continue;
                }
                $this->_attributes[$key] = $value;
            }
        } elseif (is_string($data)) {
            $message = $data;
        }

        // Nội dung
        $this->_attributes['message'] = $message;
    }
} 