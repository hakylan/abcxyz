<?php
namespace SeuDo;

use Flywheel\Db\Type\DateTime;
use SeuDo\Logger;
use Flywheel\Object;

class GlobalEventDispatcher extends Object {
    protected static $_instance;

    /**
     * @return GlobalEventDispatcher
     */
    public static function getInstance() {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * add handling event
     * @param $event
     */
    public static function addEvent($event) {
        $handling = self::getInstance();
        self::getEventDispatcher()->addListener($event, array($handling, $event));
    }

    /**
     * add handling many events
     * @param $events
     */
    public static function addEvents($events) {
        for($i = 0; $i < sizeof($events); ++$i) {
            self::addEvent($events[$i]);
        }
    }

    /**
     * @param \Flywheel\Event\Event $event
     * @return bool|\Events
     */
    /*protected static function _storeEvent($event) {
        $logger = Logger::factory('global_event');

        try {
            $om = new \Events();
            $om->setName($event->getName());
            $om->setData(json_encode($event->params));
            $om->setGroup('account');
            if($om->save()) {
                $logger->info('Saved new events "' .$om->getName() .'"');
                return $om;
            } else {
                $context = (!$om->isValid())? $om->getValidationFailuresMessage("\n") : "";
                $logger->error('Failed to save new events "' .$om->getName() .'" ' .$context);
            }

            return false;
        } catch (\Exception $e) {
            $logger->addError('Failed to save new events. Message:' .$e->getMessage() .$e->getTraceAsString());
        }
    }

    public function __call($method, $params) {
        $event = $params[0];

        if ($event instanceof \Flywheel\Event\Event) {
            self::_storeEvent($event);

        }
    }*/
} 