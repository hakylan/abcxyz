<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class CartItemWidget extends Widget
{
    public $cart_item = null;
    public function begin() {

        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "CartItem";
        $this->logger = \BaseAuth::getInstance()->getUser();
    }
    public function end()
    {
//        return $this->render(null);
        return $this->render(array(
            'cart_item' => $this->cart_item
        ));
    }
}