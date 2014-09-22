<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nobita
 * Date: 8/22/13
 * Time: 5:25 PM
 * To change this template use File | Settings | File Templates.
 */

namespace SeuDo;


use Flywheel\Router\WebRouter;

class UserRouter extends WebRouter {
    public function init($config = null) {
        parent::init($config);

        $this->_baseUrl = Main::getAppUrl('User');
    }
}