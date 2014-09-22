<?php

use Flywheel\Controller\Widget;
use Flywheel\Html\Form;

class SelectWarehouse extends Widget {
    /** @var Form */
    public $form;
    public $activities = array(
        \BarcodeFiles::ACT_IN => '+ Nhập kho',
        \BarcodeFiles::ACT_OUT => '- Xuất kho',
        \BarcodeFiles::ACT_INVENTORY => '= Kiểm kê'
    );
    public function begin() {
    }

    public function end() {
        $warehouses = \Warehouses::getAllWarehouses();
        $select = $this->form->selectOption($this->elementName, $this->selected, (array) $this->htmlOptions);
        $select->addOption($this->label, "");
        foreach($warehouses as $warehouse) {
            foreach($this->activities as $act => $label) {
                $option = mb_strtoupper("{$label} - {$warehouse->getName()}", 'UTF-8');
                $select->addOption($option, $act .'/' .$warehouse->getCode());
            }
        }

        ob_start();
        $select->display();
        $s = ob_get_clean();
        return $s;
    }
} 