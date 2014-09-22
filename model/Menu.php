<?php 
use Flywheel\Db\Manager;
use Flywheel\Mongodb\Document;

 class Menu extends Document {
    public $name;
    public $parent_id;


    protected static $_tableName = 'menu';
   
    public  $_schema = array(
       
        'parent_id' => '',
        'name' => ''
       
     );
  
    public function embeddedDocuments(){
        return array();
    }
    
}