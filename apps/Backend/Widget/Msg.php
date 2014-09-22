<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class Msg extends Widget
{
    public function begin() {
        $this->viewPath = Base::getApp()->getController()->getTemplatePath() .DIRECTORY_SEPARATOR .'Widget' .DIRECTORY_SEPARATOR;
        $this->viewFile = "Msg";
    }
    public function end()
    {
        return $this->render();
    }
}