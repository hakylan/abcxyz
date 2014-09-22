<?php
use Flywheel\Factory;
use Flywheel\Db\Type\DateTime;
use \Flywheel\Session\Session;
class BaseEvent extends SeuDo\Event {
    public function onBeginAuthen(){}
    public function onAfterAuthen(){}
}