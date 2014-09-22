<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 4/9/14
 * Time: 11:50 AM
 */

namespace SeuDo\ServiceFee;


class Checking extends Base {
    protected $_nonAccessoriesQuantity = 0;
    protected $_accessoriesQuantity = 0;

    public function __construct($_accessoriesQuantity, $_nonAccessoriesQuantity) {
        $this->_accessoriesQuantity = $_accessoriesQuantity;
        $this->_nonAccessoriesQuantity = $_nonAccessoriesQuantity;
    }

    protected function _getChargingFormula() {
    }

    public function calculateFee() {
    }
} 