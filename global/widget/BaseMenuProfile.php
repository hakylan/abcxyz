<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class BaseMenuProfile extends Widget
{
    public $logger = null;
    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "MenuProfile";
        $this->logger = \BaseAuth::getInstance()->getUser();

    }
    public function end()
    {
        return $this->render(array(
            'user' => $this->logger
        ));
    }
}