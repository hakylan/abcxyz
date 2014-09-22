<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class ItemViewedBase extends Widget
{
    public $cart_item = null;
    public $item_viewed = null;
    public function begin() {

        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "ItemViewed";
    }
    public function end()
    {
//        return $this->render(null);
        return $this->render(array(
            "item_viewed"=>$this->item_viewed
        ));
    }
}