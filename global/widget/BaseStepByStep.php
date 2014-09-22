<?php
use SeuDo\Main;
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class BaseStepByStep extends Widget
{


    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "StepByStep";
    }

    public function end()
    {
        return $this->render(array());
    }
}