<?php 
/**
 * BarcodeFiles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/BarcodeFilesBase.php';
class BarcodeFiles extends \BarcodeFilesBase {
    const TYPE_FREIGHT_BILL = 'FREIGHT_BILL',
            TYPE_ORDER = 'ORDER';

    const ACT_IN = 'IN',
        ACT_OUT = 'OUT',
        ACT_INVENTORY = 'INVENTORY';

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'uploaded_time',
            )
        );
    }

    /**
     * @return array
     */
    public function getContentInArray() {
        return explode(',', $this->getContent());
    }
}