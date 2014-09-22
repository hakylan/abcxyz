<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class Menu extends Widget
{
    public function begin() {
        $this->viewPath = Base::getApp()->getController()->getTemplatePath() .DIRECTORY_SEPARATOR .'Widget' .DIRECTORY_SEPARATOR;
        $this->viewFile = "Menu";
    }
    public function end()
    {
        return $this->render();
    }
}