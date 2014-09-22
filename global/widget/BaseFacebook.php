<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class BaseFacebook extends Widget
{

    public $appId = null;
    public $homeUrl = null;

    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "FacebookJs";
        $this->appId = \Flywheel\Config\ConfigHandler::get('facebook.appId');
        $this->homeUrl = \SeuDo\Main::getHomeUrl();
    }

    public function end()
    {
        return $this->render(array(
            'appId' => $this->appId,
            'homeUrl' =>  $this->homeUrl
        ));
    }
}