<?php
require_once __DIR__ .'/../bootstrap.php';

try {
    $r = \SeuDo\BarcodeFile::parsingFile(__DIR__ .'/nhap 10-4.xls');
    print_r($r);
} catch (\Exception $e) {
    print($e->getMessage());
}