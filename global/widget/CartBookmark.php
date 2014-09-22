<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;
use Flywheel\Session\Session;

class CartBookmark extends Widget
{
    public $cart_list = null;
    public $user = null;
    public $user_id = null;

    public function begin() {

        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "CartBookmark";
        $auth = BaseAuth::getInstance();
        $this->user = $auth->getUser();
        if($this->user){
            $this->user_id = $this->user->id;
        }else{
            $ses = Session::getInstance();
            $this->user_id = $ses->id();//Factory::getSession()->id();
        }

    }
    public function end()
    {
//        return $this->render(null);
        return $this->render(array(
            'cart_list' => $this->cart_list,
            'user_id' => $this->user_id
        ));
    }
}