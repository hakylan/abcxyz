<?php

namespace Backend\Controller;


use Flywheel\Exception;
use SeuDo\Barcode;

// Including all required classes


class BillCode extends BackendBase
{
    public $user = null;
    public function beforeExecute()
    {
        $this->setTemplate("Seudo");
        parent::beforeExecute();
        $this->user = \BaseAuth::getInstance()->getUser();
    }

    public function executeDefault(){
        $fontSize = 10;   // GD1 in px ; GD2 in point
        $marge    = 10;   // between barcode and hri in pixel
        $x        = 110;  // barcode center
        $y        = 30;  // barcode center
        $height   = 50;   // barcode height in 1D ; module size in 2D
        $width    = 2;    // barcode height in 1D ; not use in 2D
        $angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation

        $code = $this->request()->get("code");

//        $code     = '12345678'; // barcode, of course ;)
        $type     = 'ean8';
        $im     = imagecreatetruecolor(230, 60);
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);

        $data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);

        if ( isset($font) ){
            $box = imagettfbbox($fontSize, 0, $font, $data['hri']);
            $len = $box[2] - $box[0];
            Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
//    imagettftext($im, $fontSize, $angle, $x + $xt, $y + $yt, $blue, $font, $data['hri']);
        }
        header('Content-type: image/gif');
        imagegif($im);
        imagedestroy($im);
        exit;
    }
}