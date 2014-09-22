<?php
namespace Home\Controller;
use Flywheel\Controller\Web;
use SeuDo\Main;

abstract class HomeBase extends Web{
    public function beforeExecute(){
        $eventDispatcher = self::getEventDispatcher();
        $eventDispatcher->addListener('errorOrderLink', array(new \HomeEvent(), 'errorOrderLink'));
    }
}
