<?php
namespace Background\Task;
use Flywheel\Controller\ConsoleTask;
abstract class BackgroundBase extends ConsoleTask{

    const QUEUE_EMAIL = 'email_queue';


    protected $limitExecuteTime = 30; //second
    protected $timeBegin;

    public function beforeExecute() {
        $this->timeBegin = time();
    }

    /**
     * @return bool
     */
    public function checkValidExecutionTime() {
        return abs((time() - $this->timeBegin)) < $this->limitExecuteTime;
    }
}
