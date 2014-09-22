<?php
namespace Home\Controller;
use SeuDo\Main;

class Home extends HomeBase{

    public function executeDefault(){
        $this->setView('Home/default');
        $this->setLayout('home');
        $locations = \Locations::findByType('STATE');
        $this->view()->assign('locations', $locations);

        return $this->renderComponent();
    }

    public function executePageBookmark(){
        $this->setView('Home/page_bookmark');
        return $this->renderComponent();
    }

    public function executeAskConfirm(){

        $user = \HomeAuth::getInstance()->getUser();
        if ($user === false) {
            $this->redirect(\SeuDo\Main::getHomeRouter()->createUrl('login', array('r' => $this->request()->getUri())));
        }
        $this->document()->title = "Bổ sung thông tin";

        $this->setView('Home/ask_confirm');
        $this->view()->assign('user', $user);

        return $this->renderComponent();
    }
}
