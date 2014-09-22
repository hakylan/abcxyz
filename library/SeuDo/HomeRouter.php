<?php

namespace SeuDo;

use Flywheel\Router\WebRouter;
use SeuDo\Main;

class HomeRouter extends WebRouter {
    public function init($config = null) {
        parent::init($config);
        $this->_baseUrl = Main::getHomeUrl();
    }
}