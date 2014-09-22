<?php
namespace User\Controller;
use Flywheel\Controller\Web;
use SeuDo\Main;

abstract class UserBase extends Web {

    public function beforeExecute() {
        /* check authen when on user app*/
        $instance = \UserAuth::getInstance();
        if ( $instance->isAuthenticated() === false) {
            $this->redirect(Main::getHomeRouter()->createUrl('login/login'));
        }
    }

    public function assign($var, $value = null) {
        $this->view()->assign($var, $value);
    }
}
