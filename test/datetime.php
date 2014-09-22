<?php
//require_once __DIR__ .'/../bootstrap.php';
//\Flywheel\Config\ConfigHandler::import('root.config');
//
//\Flywheel\Loader::import('root.model.*');
//$date = new \Flywheel\Db\Type\DateTime('20/02/1988');
//echo $date->format('Y-m-d H:i(worry)');

class A {
    const USER = 'ABC';
    public function test() {
        echo "alo", self::USER;
    }
}
$a = new A();
$a->test();