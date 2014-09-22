<?php
require_once __DIR__ .'/../bootstrap.php';

try {
    \Flywheel\Config\ConfigHandler::import('root.config');
    $sfs = \SeuDo\SFS\Client::getInstance();

    //test upload with file no transform
    $uploader = new \SeuDo\SFS\Upload('test');
    $uploader->setFile(__DIR__ .'/1468710_716501181694775_1329039546_n.jpg');
    $uploader->setFileName(uniqid() .'_' . time() .'.jpg');
    if($sfs->upload($uploader)) {
        print_r($sfs->getHttpCode());
    }
    print_r(json_decode($sfs->getResponse()));

    //test upload with file with transform
    $uploader = new \SeuDo\SFS\Upload('test');
    $uploader->setFile(__DIR__ .'/1468710_716501181694775_1329039546_n.jpg');
    $uploader->setFileName(uniqid() .'_' . time() .'.jpg');
    $uploader->addTransformation('resize', array(
        'w' => '200',
        'h' => '200'
    ));
    if($sfs->upload($uploader)) {
        print_r($sfs->getHttpCode());
    }
    print_r(json_decode($sfs->getResponse()));

    //test upload with url no transform
    $uploader = new \SeuDo\SFS\Upload('test');
    $uploader->setUrl('https://fbcdn-sphotos-b-a.akamaihd.net/hphotos-ak-ash4/r270/1474492_716501088361451_1658527341_n.jpg');
    $uploader->setFileName(uniqid() .'_' . time() .'.jpg');
    if($sfs->upload($uploader)) {
        print_r($sfs->getHttpCode());
    }
    print_r(json_decode($sfs->getResponse()));

    //test upload with url no transform
    $uploader = new \SeuDo\SFS\Upload('test');
    $uploader->setUrl('https://fbcdn-sphotos-b-a.akamaihd.net/hphotos-ak-ash4/r270/1474492_716501088361451_1658527341_n.jpg');
    $uploader->setFileName(uniqid() .'_' . time() .'.jpg');
    $uploader->addTransformation('resize', array(
        'w' => '400',
    ));
    $uploader->addTransformation('square', array(
        'w' => '200',
    ));
    if($sfs->upload($uploader)) {
        print_r($sfs->getHttpCode());
    }
    print_r(json_decode($sfs->getResponse()));

} catch (\Exception $e) {
    print_r($e->getMessage() ."\n");
    print_r($e->getTraceAsString());
}