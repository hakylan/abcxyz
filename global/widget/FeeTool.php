<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class FeeTool extends Widget
{
    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "FeeTool";

    }
    public function end()
    {
        return $this->render(null);
    }
}