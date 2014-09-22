<?php
use SeuDo\Main;
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class BaseHeader extends Widget
{

    public $items = null;
    public $logged = null;
    public $urlOrderLink = null;
    public $is_header_slide = 1;

    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "Header";
        $this->logged = \BaseAuth::getInstance()->getUser();

        $controller = Base::getApp()->getController();

        $this->urlOrderLink = \SeuDo\Main::getHomeRouter()->createUrl("OrderLink");
        $this->items['url'] = array(
            'cart' => \SeuDo\Main::getHomeRouter()->createUrl('Cart/default'),
            'controller' => $controller
        );
    }

    public function end()
    {
        $params['items'] = $this->items;
        return $this->render(array(
            'items' => $this->items,
            'logged' => $this->logged,
            'urlOrderLink' => $this->urlOrderLink,
            'is_header_slide' => $this->is_header_slide
        ));
    }
}