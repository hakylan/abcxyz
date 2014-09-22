<?php
namespace SeuDo;

use Flywheel\Session\Session;
use SeuDo;
class Captcha {
    private $key = '',$length = 6;

    public $error = array();

    public function setKey($key) {
        $this->key = $key;
    }
    public function getKey() {
        return $this->key;
    }
    public function setLength($length) {
        $this->length = $length;
    }
    public function getLength(){
        return $this->length;
    }


    public function generate() {
        header("Content-type: image/png");
        $string = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = '';
        for($i=0;$i<6;$i++){
            $pos = rand(0,36);
            $str .= $string{$pos};
        }
        echo $str;exit;
        $img_handle = ImageCreate (60, 20);
        $back_color = ImageColorAllocate($img_handle, 255, 255, 255);
        $txt_color = ImageColorAllocate($img_handle, 0, 0, 0);
        ImageString($img_handle, 31, 5, 0, $str, $txt_color);
        Imagepng($img_handle);

        Session::set($this->getKey(),$str);

    }
} 