<?php
/**
 * Permissions
 * @version		$Id$
 * @package		Model
 */

require_once dirname(__FILE__) .'/Base/PermissionsBase.php';
class Permissions extends \PermissionsBase {

    public static $permissions = array (
        'user_role_permission' => array (
            'label' => 'Người dùng, nhóm, phân quyền',
            'permissions' => array(
                PERMISSION_USER_VIEW => array(
                    'label' => 'Xem thông tin người dùng',
                    'description' => 'Quyền cho phép quản trị viên xem thông tin người dùng',
                ),

                PERMISSION_USER_MOBILE_EDIT => array(
                    'label' => 'Thay đổi số điện thoại người dùng',
                    'description' => 'Quyền cho phép quản trị viên thêm/sửa/xóa số điện thoại của kháck hàng',
                ),

                PERMISSION_USER_ADDRESS_EDIT => array(
                    'label' => 'Thêm, sửa, xóa số điện thoại',
                    'description' => 'Quyền cho phép quản trị viên thêm/sửa/xóa số điện thoại của kháck hàng',
                ),

                PERMISSION_USER_INFO_EDIT => array(
                    'label' => 'Thêm, sửa, xóa thông tin người dùng',
                    'description' => 'Quyền cho phép quản trị viên thêm, sửa, xóa thông tin người dùng',
                ),

                PERMISSION_USER_VIEW_FINANCE => array(
                    'label' => 'Xem tài chính khách hàng',
                    'description' => 'Quyền cho phép xem tài chính khách hàng, lịch sử giao dịch và thao tác đồng bộ thông tin tài chính khách',
                ),

                PERMISSION_USER_PROCURACY => array(
                    'label' => 'Quyền khóa hoặc cấm người dùng',
                    'description' => 'Cho phép quản trị viên khóa có thời hạn hoặc cấm vĩnh viễn một người dùng, cũng như hủy việc này',
                ),

                PERMISSION_ROLE_VIEW => array (
                    'label' => 'Xem thông tin nhóm',
                    'description' => 'Quyền cho phép quản trị viên xem thông tin nhóm',
                ),

                PERMISSION_ROLE_EDIT => array (
                    'label' => 'Thêm, sửa, xóa thông tin nhóm',
                    'description' => 'Quyền cho phép quản trị viên sửa thông tin nhóm',
                ),

                PERMISSION_ROLE_PERMISSION_MANAGE => array(
                    'label' => 'Quản lý quyền của nhóm',
                    'description' => 'Quyền cho phép quản trị viên quản lý quyền trong nhóm',
                ),
            ),
        ),

        'customer_care' => array(
            'label' => 'Làm việc với khách hàng',
            'permissions' => array(
                PERMISSION_COMMUNICATE_CUSTOMER => array(
                    'label' => 'Chat với khách hàng',
                    'description' => 'Cho phép chat với khách hàng trên kênh công cộng',
                ),
                PERMISSION_PUBLIC_PERSONAL_INFO => array(
                    'label' => 'Hiện thông tin cá nhân khi trao đổi với khách',
                    'description' => 'Nếu nhân viên có quyền này và quyền chat với khách hàng, khách sẽ nhìn thấy thông tin người chat. Ngược lại khách chỉ nhìn đoạn chat được gửi bởi hệ thống',
                )
            )
        ),

        'order' => array (
            'label' => 'Quản lý đơn hàng',
            'permissions' => array (
                PERMISSION_ORDER_VIEW_ORDER_MANAGE => array(
                    'label' => 'Xem trang quản lý đơn hàng',
                    'description' => 'Quyền cho phép quản trị viên xem trang quản lý đơn hàng',
                ),
                PERMISSION_ORDER_VIEW_DELIVERY_MANAGEMENT => array(
                    'label' => 'Xem trang yêu cầu giao hàng',
                    'description' => 'Quyền cho phép quản trị viên xem trang Yêu cầu giao hàng',
                ),
                PERMISSION_CANCEL_ORDER => array(
                    'label' => 'Hủy đơn hàng',
                    'description' => 'Quyền cho phép quản trị viên hủy đơn hàng',
                ),
                PERMISSION_APPOINTMENT_ORDER => array(
                    'label' => 'Bổ nhiệm nhân viên phụ trách',
                    'description' => 'Quyền cho phép quản trị viên bổ nhiệm nhân viên phụ trách',
                ),
                PERMISSION_ORDER_EDIT_FEES => array(
                    'label' => 'Sửa phí đơn hàng',
                    'description' => 'Quyền cho phép quản trị viên sửa phí đơn hàng',
                ),
                PERMISSION_ORDER_PACKAGING_SURCHARGES => array(
                    'label' => 'Thêm phụ phí đóng gói',
                    'description' => 'Quyền cho phép quản trị viên thêm phụ phí đóng gói',
                ),
                PERMISSION_ORDER_VIEW_ORDER_DETAIL => array(
                    'label' => 'Xem chi tiết đơn hàng',
                    'description' => 'Quyền cho phép quản trị viên xem chi tiết đơn hàng',
                ),
                PERMISSION_ORDER_DELETE_ORDER => array(
                    'label' => 'Xóa đơn hàng',
                    'description' => 'Quyền cho phép quản trị viên xóa đơn hàng',
                ),
                PERMISSION_ORDER_VIEW_HISTORY => array(
                    'label' => 'Xem lịch sử đơn',
                    'description' => 'Quyền cho phép quản trị viên xem lịch sử đơn',
                ),
                PERMISSION_ORDER_VIEW_TRANSACTION => array(
                    'label' => 'Xem lịch sử giao dịch',
                    'description' => 'Quyền cho phép quản trị viên xem lịch sử giao dịch trên đơn hàng',
                ),
                PERMISSION_ORDER_VIEW_ITEMS => array(
                    'label' => 'Xem sản phẩm trong đơn',
                    'description' => 'Xem danh sách sản phẩm trong đơn',
                ),
                PERMISSION_ORDER_EDIT_SERVICES_REQUEST => array(
                    'label' => 'Sửa yêu cầu dịch vụ của đơn hàng',
                    'description' => 'Quyền sửa yêu cầu dịch vụ của đơn hàng.',
                ),
                PERMISSION_ORDER_VIEW_SHIPPING_ADDRESS => array(
                    'label' => 'Xem địa chỉ nhận hàng',
                    'description' => 'Quyền xem địa chỉ nhận hàng của khách',
                ),
                PERMISSION_ORDER_CHANGE_TRANSPORTING => array(
                    'label' => 'Chuyển vận chuyển',
                    'description' => 'Chuyển đơn hàng sang trạng thái vận chuyển'
                ),
                PERMISSION_ORDER_CHANGE_WAIT_DELIVERY => array(
                    'label' => 'Chuyển chờ giao',
                    'description' => 'Chuyển đơn hàng sang trạng thái chờ giao'
                ),
                PERMISSION_ORDER_CHANGE_REQUEST_DELIVERY => array(
                    'label' => 'Chuyển YC giao',
                    'description' => 'Chuyển đơn hàng sang trạng thái yêu cầu giao'
                ),
                PERMISSION_ORDER_CUSTOMER_CONFIRM => array(
                    'label' => 'Xác nhận đơn hàng được gửi từ mua hàng viên',
                    'description' => 'Cho phép nhân viên kích xác nhận đơn hàng được gửi từ mua hàng viên'
                ),
            ),
        ),

        'complaint' => array(
            'label' => 'Quản lý khiếu nại dịch vụ',
            'permissions' => array(
                PERMISSION_COMPLAINT_CHAT_EXTERNAL => array(
                    'label' => 'Chat với khách hàng trong trang chi tiết khiếu nại',
                    'description' => 'Cho phép quản trị viên chat với khách trong trang chi tiết khiếu nại dịch vụ'
                ),
//                PERMISSION_COMPLAINT_CAN_PROCESSING => array(
//                    'label' => 'Quyền xử lý khiếu nại',
//                    'description' => 'Quyền cho phép quản trị viên xử lý khiếu nại của khách hàng'
//                ),
                PERMISSION_COMPLAINT_CAN_RECEPTION => array(
                    'label' => 'Tiếp nhận khiếu nại dịch vụ của khách hàng',
                    'description' => 'Cho phép quản trị viên tiếp nhận khiếu nại dịch vụ của khách hàng'
                ),
                PERMISSION_COMPLAINT_CAN_PROPOSED_AMOUNT => array(
                    'label' => 'Đề xuất số tiền bồi thường cho khách hàng',
                    'description' => 'Cho phép quản trị viên đề xuất số tiền bồi thường cho khách hàng'
                ),
                PERMISSION_COMPLAINT_CAN_ACCEPT => array(
                    'label' => 'Đồng ý trả tiền bồi hoàn cho khách hàng',
                    'description' => 'Cho phép quản trị viên đồng ý trả tiền bồi hoàn cho khách hàng'
                ),
                PERMISSION_COMPLAINT_CAN_REJECT => array(
                    'label' => 'Từ chối trả tiền bồi hoàn cho khách hàng',
                    'description' => 'Cho phép quản trị viên từ chối trả tiền bồi hoàn cho khách hàng'
                ),
                PERMISSION_COMPLAINT_CAN_CENSORSHIP_FINANCICAL => array(
                    'label' => 'Duyệt tài chính khiếu nại dịch vụ',
                    'description' => 'Cho phép quản trị viên duyệt tài chính khiếu nại dịch vụ'
                ),
                PERMISSION_COMPLAINT_VIEW_LIST => array(
                    'label' => 'Xem trang danh sách khiếu nại dịch vụ',
                    'description' => 'Cho phép quản trị viên xem trang danh sách khiếu nại dịch vụ'
                )
            ),
        ),

        'complaint_seller' => array(
            'label' => 'Quản lý khiếu nại người bán',
            'permissions' => array(
                PERMISSION_COMPLAINT_SELLER_CREATE_NEW => array(
                    'label' => 'Tạo khiếu nại người bán',
                    'description' => 'Cho phép quản trị viên tạo khiếu nại người bán'
                ),
                PERMISSION_COMPLAINT_SELLER_VIEW_LIST => array(
                    'label' => 'Xem trang danh sách khiếu nại người bán',
                    'description' => 'Cho phép quản trị viên xem trang danh sách khiếu nại người bán'
                ),
                PERMISSION_COMPLAINT_SELLER_CAN_PROCESSING => array(
                    'label' => 'Xử lý (tiếp nhận) khiếu nại người bán',
                    'description' => 'Cho phép quản trị viên xử lý (tiếp nhận) khiếu nại người bán'
                ),
                PERMISSION_COMPLAINT_SELLER_CAN_ACCEPT => array(
                    'label' => 'Xác nhận khiếu nại người bán thành công',
                    'description' => 'Cho phép quản trị viên xác nhận khiếu nại người bán thành công'
                ),
                PERMISSION_COMPLAINT_SELLER_CAN_REJECT => array(
                    'label' => 'Xác nhận khiếu nại người bán thất bại',
                    'description' => 'Cho phép quản trị viên xác nhận khiếu nại người bán là thất bại'
                )
            ),
        ),

        'negotiating_pay' => array (
            'label' => 'Đàm phán, mua hàng',
            'permissions' => array(
                PERMISSION_ORDER_VIEW_PURCHASE_MANAGE => array(
                    'label' => 'Xem trang quản lý mua hàng',
                    'description' => 'Quyền cho phép quản trị viên xem trang quản lý mua hàng',
                ),
                PERMISSION_PURCHASE_SKIPS_TELLERS => array(
                    'label' => 'Thu hồi người mua hàng (quản lý mua hàng)',
                    'description' => 'Quyền cho phép quản trị viên thu hồi người mua hàng trong quản lý mua hàng',
                ),
                PERMISSION_PURCHASE_SELECT_TELLERS => array(
                    'label' => 'Chọn người mua hàng',
                    'description' => 'Quyền cho phép quản trị viên chọn người mua hàng',
                ),
                PERMISSION_PURCHASE_SKIPS_PAID_STAFF => array(
                    'label' => 'Thu hồi thanh toán viên (Quản lý mua hàng)',
                    'description' => 'Quyền cho thu hồi thanh toán viên trong quản lý mua hàng dành cho quản lý',
                ),
                PERMISSION_PURCHASE_CHANGE_CONFIG => array(
                    'label' => 'Thay đổi Config quản lý mua hàng',
                    'description' => 'Quyền cho phép quản trị viên Thay đổi những Config trong quản lý mua hàng cho quản trị viên',
                ),
                PERMISSION_PURCHASE_ORDER => array(
                    'label' => 'Mua hàng',
                    'description' => 'Dành cho giao dịch viên nhận đơn và đàm phán với người bán'
                ),
                PERMISSION_ORDER_TRANSITION_OUT_OF_STOCK => array(
                    'label' => 'Click hết hàng',
                    'description' => 'Cho phép click hết hàng đơn hàng trước khi thanh toán',
                ),
                PERMISSION_ORDER_PAYMENT => array(
                    'label' => 'Thanh toán đặt hàng',
                    'description' => 'Quyền cho phép quản trị viên thanh toán đặt hàng',
                ),
                PERMISSION_ORDER_ADD_FREIGHT_BILL => array(
                    'label' => 'Thêm mã vận đơn',
                    'description' => 'Quyền cho phép quản trị viên thêm mã vận đơn',
                ),
                PERMISSION_ORDER_EDIT_FREIGHT_BILL => array(
                    'label' => 'Sửa mã vận đơn',
                    'description' => 'Quyền cho phép quản trị viên sửa mã vận đơn',
                ),
                PERMISSION_ORDER_EDIT_INVOICE => array(
                    'label' => 'Sửa mã hóa đơn',
                    'description' => 'Quyền cho phép quản trị viên sửa mã hóa đơn',
                ),
                PERMISSION_ORDER_ADD_INVOICE => array(
                    'label' => 'Thêm mã hóa đơn',
                    'description' => 'Quyền cho phép quản trị viên thêm mã hóa đơn',
                ),
                PERMISSION_ORDER_EDIT_PRICE => array(
                    "label" => "Sửa đơn giá trên sản phẩm",
                    'description' => 'Quyền cho phép nhân viên sửa đơn giá trên sản phẩm của đơn hàng',
                ),
            ),
        ),

        'logistic' => array(
            'label' => 'Vận chuyển hàng',
            'permissions' => array(
                PERMISSION_UPLOAD_BARCODE => array(
                    'label' => 'Upload file quét mã vạch',
                    'description' => 'Upload các file kết quả bắn mã vạch',
                ),
                PERMISSION_VIEW_UPLOADED_BARCODE_SCAN_FILES => array(
                    'label' => 'Xem file quét mã vạch',
                    'description' => 'Xem các file mã vạch đã bắn',
                ),
                PERMISSION_VIEW_BARCODE_TRACKING => array(
                    'label' => 'Xem lịch sử vận chuyển mã vạch',
                    'description' => 'Chi tiết đánh dấu trạng thái các mã vạch qua các kho và bộ phận',
                ),
                PERMISSION_DOWNLOAD_BARCODE_SCAN_FILE => array(
                    'label' => 'Download file mã vạch',
                    'description' => 'Download file mã vạch đã upload trước đó',
                ),
            ),
        ),

        'order_checking' => array(
            'label' => 'Kiểm hàng',
            'permissions' => array(
                PERMISSION_ORDER_EDIT_ORDER_QUANTITY => array(
                    'label' => 'Sửa số lượng đặt hàng',
                    'description' => 'Quản trị viên có thể sửa số lượng đặt hàng khi đơn hàng ở trạng thái đã đặt cọc',
                ),
                PERMISSION_ORDER_EDIT_PENDING_QUANTITY => array(
                    'label' => 'Sửa số lượng mua hàng',
                    'description' => 'Quản trị viên có thể sửa số lượng mua hàng trước khi đơn hàng chuyển sang trạng thái kiểm hàng',
                ),
                PERMISSION_ORDER_EDIT_RECEIVED_QUANTITY => array(
                    'label' => 'Sửa số lượng kiểm hàng',
                    'description' => 'Quản trị viên có thể sửa số lượng sản phẩm khi kiểm hàng',
                ),
                PERMISSION_ORDER_EDIT_WEIGHT => array(
                    'label' => 'Sửa trọng lượng của đơn hàng',
                    'description' => 'Quyền sửa trọng lượng của đơn hàng',
                ),
            ),
        ),

        'system' => array (
            'label' => 'Hệ thống',
            'permissions' => array(
                PERMISSION_SYSTEM_MANAGE => array(
                    'label' => 'Quản lý kỹ thuật',
                    'description' => 'Quản lý các thành phần kỹ thuật nói chung, cache, reset secret key v.v..',
                ),
                PERMISSION_SYSTEM_EXCHANGE_MANAGE => array(
                    'label' => 'Quản lý tỉ giá',
                    'description' => 'Thay đổi tỉ giá hệ thống',
                ),
                PERMISSION_SYSTEM_CONFIG_MANAGE => array(
                    'label' => 'Quản lý cấu hình hệ thống',
                    'description' => 'Thay đổi cấu hình hệ thông',
                ),
                PERMISSION_SYSTEM_LOCATION_MANAGE => array(
                    'label' => 'Quản lý vị trí địa lý',
                    'description' => 'Thay đổi thông tin tỉnh thành, quận huyện',
                ),
            )
        ),
        'delivery_manage' => array (
            'label' => 'Quản lý giao hàng',
            'permissions' => array(
                PERMISSION_DELIVERY_CHANGE_COD => array(
                    'label' => 'Thay đổi COD',
                    'description' => 'Cho phép nhân viên thay đổi COD trong Quản lý giao hàng',
                ),
                PERMISSION_DELIVERY_CHANGE_REAL_COD => array(
                    'label' => 'Thay đổi số thực thu COD',
                    'description' => 'Cho phép nhân viên thay đổi số thực thu COD trong Chi tiết phiếu giao hàng',
                ),
                PERMISSION_DELIVERY_CHANGE_SHIPPING_FEE => array(
                    'label' => 'Thay đổi Phí vận chuyển',
                    'description' => 'Cho phép nhân viên thay đổi Domestic Shipping Fee trong quản lý giao hàng',
                ),
                PERMISSION_DELIVERY_VIEW => array(
                    'label' => 'Cho phép xem trang quản lý giao hàng',
                    'description' => 'Cho phép nhân viên xem trang quản lý giao hàng',
                ),
                PERMISSION_ORDER_CHANGE_DELIVERY => array(
                    'label' => 'Chuyển đang giao',
                    'description' => 'Chuyển đơn hàng sang trạng thái đang giao hàng'
                ),
                PERMISSION_DELIVERY_VIEW_BILL_MANAGE => array(
                    'label' => 'Quyền xem danh sách và chi tiết phiếu giao hàng',
                    'description' => 'Quyền cho phép nhân viên xem trang danh sách và chi tiết phiếu giao hàng'
                ),
            )
        ),
    );

    /**
     * Find permission by role id
     * return associate array with permission code as key if $assoc is true
     * @param int $role_id
     * @param bool $assoc
     * @return Permissions[]
     */
    public static function findByRoleId($role_id, $assoc = false) {
        /** @var \Permissions $rolePermissions */
        $rolePermissions = parent::findByRoleId($role_id);
        if (!$assoc) {
            return $rolePermissions;
        }

        $t = array();
        for ($i = 0, $size = sizeof($rolePermissions); $i < $size; ++$i) {
            $t[$rolePermissions[$i]->getCode()] = $rolePermissions[$i];
        }

        return $t;
    }

    /**
     * @param Users $user
     * @return array|bool
     */
    public static function buildPermission(\Users $user) {
        /** @var \UserRoles[] $user_roles */
        $user_roles = \UserRoles::findByUserId($user->getId());
        $role_ids = array();
        $role_data = array();
        if (is_array($user_roles) && !empty($user_roles)) {
            foreach($user_roles as $user_role) {
                $role_ids[] = $user_role->getRoleId();
            }
        }

        //get all roles
        /** @var \Roles[] $roles */
        $roles = array();
        if( sizeof($role_ids) > 0 ) {
            $roles = \Roles::select()->where('`state` = :state')
                ->setParameter(':state', 'ACTIVE', \PDO::PARAM_STR)
                ->andWhere('`id` IN (' .implode(',', $role_ids) .')')
                ->execute();
        }

        if (is_array($roles) && !empty($roles)) {
            foreach($roles as $role) {
                /** @var \Permissions $permissions */
                $permissions = \Permissions::findByRoleId($role->getId());
                $permission_codes = array();
                if(!empty($permissions)) {
                    foreach ($permissions as $p) {
                        array_push($permission_codes, $p->getCode());
                    }
                }
                $role_data[$role->getId()] = $permission_codes;
            }
        }
        return $role_data;
    }
}