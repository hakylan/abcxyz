<?php
namespace Backend\Controller;

class Error extends BackendBase {

    public function executeDefault(){
        $this->executeNotPermission();exit;
    }

    public function executeNotFound() {
        echo 'khong tim thay';exit;
    }

    public function executeNotPermission(){
        $this->setView('Error/403');
        return $this->renderComponent();
    }

}


