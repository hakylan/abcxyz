<?php
use Backend\Controller\BackendBase;
class Backend extends BackendBase{


    public function executeDefault(){
      return $this->renderComponent();
    }
}
