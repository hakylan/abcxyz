<?php
namespace Backend\Controller;
use Flywheel\Loader;
use Flywheel\Router\WebRouter;
use Flywheel\Session\Session;
use SeuDo\Logger;
use SeuDo\Permisson;

class Dashboard extends BackendBase {

    public function beforeExecute()
    {
        $this->setTemplate("Seudo");
        parent::beforeExecute();
    }

    public function executeDefault(){

        $this->setView('Dashboard/default');



        return $this->renderComponent();

    }
}
