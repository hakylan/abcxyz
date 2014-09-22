<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 4/9/14
 * Time: 11:50 AM
 */

namespace SeuDo\ServiceFee;


class Buying extends Base {
    protected $_totalAmount;

    public function __construct($total_amount) {
        $this->_totalAmount = $total_amount;
    }

    protected function _getChargingFormula() {
    }

    public function calculateFee() {}
} 