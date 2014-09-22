<?php 
/**
 * Roles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/RolesBase.php';
class Roles extends \RolesBase {
    const RolesPurchasersId = 9;  // Người mua hàng - Giao dịch viên
    const RolesOrderPaymentsStaffId = 10;

    const ROLES_LABEL_PURCHASERS = "Purchasers";
    const ROLES_LABEL_ORDER_PAYMENTS_STAFF = "Order Payments Staff";

    const STATE_ACTIVE = 'ACTIVE',
        STATE_INACTIVE = 'INACTIVE'; // Thanh toán viên

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'created_time',
                'modify_attr' => 'modified_time',
            )
        );
    }

    /**
     * Check is active
     * @return bool
     */
    public function isActive() {
        return $this->getState() == self::STATE_ACTIVE;
    }
}