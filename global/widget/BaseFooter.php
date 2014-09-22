<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class BaseFooter extends Widget
{
    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "Footer";

    }
    public function end()
    {
        return $this->render(null);
    }
}