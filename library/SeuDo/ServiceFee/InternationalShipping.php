<?php
namespace SeuDo\ServiceFee;

class InternationalShipping extends Base {
    protected $_weight = 0;

    public function __construct($_weight) {
        $this->_weight = $_weight;
    }

    protected function _getChargingFormula() {
        // TODO: Implement _getChargingFormula() method.
    }

    public function calculateFee() {
        // TODO: Implement calculateFee() method.
    }
} 