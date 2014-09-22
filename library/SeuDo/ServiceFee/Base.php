<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 4/9/14
 * Time: 11:51 AM
 */

namespace SeuDo\ServiceFee;


abstract class Base {
    abstract protected function _getChargingFormula();
    abstract public function calculateFee();
} 