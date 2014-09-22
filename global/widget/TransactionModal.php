<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class TransactionModal extends Widget
{
    public $order_code = '';
    public $text_link = "Xem lịch sử giao dịch";
    public $user = null;
    public $type_button = 'a';
    public $urlLoadTransaction = '';
    public $app = 'user';
    public $class = '';

    public function begin() {
        $this->viewPath = GLOBAL_PATH . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'template';
        $this->viewFile = "ModalBoxTransaction";
        $this->user = \BaseAuth::getInstance()->getUser();

        $this->urlLoadTransaction = \SeuDo\Main::getUserRouter()->createUrl("UserTransaction/ModalBox");

        if(!$this->user){
            return;
        }

    }
    public function end()
    {
        return $this->render(array(
            'order_code' => $this->order_code,
            'text_link' =>  $this->text_link,
            'urlLoadTransaction' => $this->urlLoadTransaction,
            'class' => $this->class,
            'app' => $this->app
        ));
    }
}