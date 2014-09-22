<?php
use SeuDo\Logger;
use mongodb\OrderCommentResource\BaseContext;
use mongodb\OrderCommentResource\Chat;
use \mongodb\OrderCommentResource;

class OrderEvent extends \Flywheel\Event\Event {

    private $logger = null;
    public function __construct(){
        $this->logger = Logger::factory("transfer_order_push_queue");
    }

    /**
     * Push order to queue  to logistic when change status bought
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function transferOrderLogisticWhenBought( $data ){
        try{
            $params = $data->params;
            $order = isset($params["order"]) ? $params["order"] : array();

            if($order instanceof \Order){
                if($order->getStatus() == \Order::STATUS_BOUGHT){
                    $message = "Đơn hàng chuyển trạng thái sang đã mua hàng";
                    $this->logger->info("Push order to queue  to logistic with order code: {$order->getCode()} ".$message);
                    return OrderUtil::pushTransferOrderLogistic($order,$message);
                }
            }
            return false;
        }catch (\Exception $e){
            $this->logger->warning("Push order to queue  to logistic not success ".$e->getMessage(),$data);
            throw $e;
        }
    }

    /**
     * Push order to queue  to logistic when update freight bill
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function transferOrderLogisticWhenUpdateFreightBill( $data ){
        try{
            $params = $data->params;
            $order = isset($params["order"]) ? $params["order"] : array();

            if($order instanceof \Order){
                $message = "Thay đổi mã vận đơn";
                $this->logger->info("Push order to queue  to logistic with order code: {$order->getCode()} ".$message);
                return OrderUtil::pushTransferOrderLogistic($order,$message);
            }
            return false;
        }catch (\Exception $e){
            $this->logger->warning("Push order to queue  to logistic not success ".$e->getMessage(),$data);
            throw $e;
        }
    }

    /**
     * Push order to queue  to logistic when choose services CPN
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function transferOrderChooseServicesCPN( $data ){
        try{
            $params = $data->params;
            $order = isset($params["order"]) ? $params["order"] : array();
            $service_type = isset($params["service_type"]) ? $params["service_type"] : "";

            if($service_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM && $order instanceof \Order){
                if($order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
                    $message = "Chọn dịch vụ CPN";
                }else{
                    $message = "Bỏ chọn dịch vụ CPN";
                }
                $this->logger->info("Push order to queue  to logistic with order code: {$order->getCode()} ".$message);
                return OrderUtil::pushTransferOrderLogistic($order,$message);
            }

            return false;

        }catch (\Exception $e){
            $this->logger->warning("Push order to queue  to logistic not success ".$e->getMessage(),$data);
            throw $e;
        }
    }


    /**
     * Push order to queue  to logistic when choose services CPN
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function transferOrderChooseServices( $data ){
        try{
            $params = $data->params;
            $order = isset($params["order"]) ? $params["order"] : array();
            $service_type = isset($params["service_type"]) ? $params["service_type"] : "";

            if($service_type == \Services::TYPE_WOOD_CRATING && $order instanceof \Order){
                if($order->mappingToService(\Services::TYPE_WOOD_CRATING)){
                    $message = "Chọn dịch vụ đóng gỗ ";
                }else{
                    $message = "Bỏ chọn dịch vụ đóng gỗ ";
                }
                $this->logger->info("Push order to queue  to logistic with order code: {$order->getCode()} ".$message);
                return OrderUtil::pushTransferOrderLogistic($order,$message);
            }else if($service_type == \Services::TYPE_FRAGILE && $order instanceof \Order){
                if($order->mappingToService(\Services::TYPE_FRAGILE)){
                    $message = "Chọn dịch vụ giá trị cao ";
                }else{
                    $message = "Bỏ chọn dịch vụ giá trị cao ";
                }
                $this->logger->info("Push order to queue  to logistic with order code: {$order->getCode()} ".$message);
                return OrderUtil::pushTransferOrderLogistic($order,$message);
            }

            return false;

        }catch (\Exception $e){
            $this->logger->warning("Push order to queue  to logistic not success ".$e->getMessage(),$data);
            throw $e;
        }
    }

    /**
     * @param $data
     */
    public function logOrderCommentFrontend($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $service_type = isset($params["service_type"]) ? $params["service_type"] : "";
        if ($order instanceof \Order && $service_type == \Services::TYPE_EXPRESS_CHINA_VIETNAM) {
            $user = $order->getBuyer();
            $order_id = $order->getId();
            $user_id = $user->getId();

            if($order->mappingToService(\Services::TYPE_EXPRESS_CHINA_VIETNAM)){
                $message = "Chọn dịch vụ Chuyển Phát Nhanh ";
            }else{
                $message = "Bỏ chọn dịch vụ Chuyển Phát Nhanh ";
            }

            $context_chat = new Chat( $message );



            \OrderComment::addComment( $user_id, $order_id, \mongodb\OrderComment::TYPE_EXTERNAL, $context_chat, true,
                BaseContext::TYPE_CHAT );
        }
    }


    /**
     * @param $data
     */
    public function logOrderCommentWhenChangeStatus($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $staff = isset($params["staff"]) ? $params["staff"] : array();
        $log = BaseContext::TYPE_LOG;
        $chat = BaseContext::TYPE_CHAT;
        $activity = BaseContext::TYPE_ACTIVITY;
        $external = \mongodb\OrderComment::TYPE_EXTERNAL;
        $internal = \mongodb\OrderComment::TYPE_INTERNAL;
        if ($order instanceof \Order && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE) {

//            $staff_id = 0;
            if($order->getStatus() == \Order::STATUS_NEGOTIATING){
                $message = "Đơn hàng đã được đặt hàng trên {$order->getSellerHomeland()}";
                $context_log = new OrderCommentResource\Log( $message );
                $content_activity = new OrderCommentResource\Activity( $message );
                \OrderComment::addComment( 0, $order->getId(), $external,
                    $context_log, false,
                    $log );
                \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                    $content_activity, true,$activity );
            }
            if($order->getStatus() == \Order::STATUS_NEGOTIATED){
                $message = "Đã đàm phán";
                $content_activity = new OrderCommentResource\Activity( $message );
                \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                    $content_activity, true,$activity );
            }

            if($order->getStatus() == \Order::STATUS_BOUGHT){
                $message = "Đơn hàng đã được thanh toán thành công trên {$order->getSellerHomeland()}";
                $context_log = new OrderCommentResource\Log( $message );
                $content_activity = new OrderCommentResource\Activity( "Đã thanh toán" );
                \OrderComment::addComment( 0, $order->getId(), $external,
                    $context_log, false,$log );
                \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                    $content_activity, true,$activity );
            }
        }elseif($order instanceof \Order && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE){
            $message = "Đơn hàng chuyển trạng thái sang ".$order->getStatusTitle();
            $context_activity = new OrderCommentResource\Activity( $message );
            \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                $context_activity, true,$activity );
        }
    }


    /**
     * Log order comment when choose services in backend
     * @param $data
     */
    public function logOrderCommentWhenChooseServices($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $staff = isset($params["staff"]) ? $params["staff"] : array();
        $type = isset($params["service_type"]) ? $params["service_type"] : array();

        if ($order instanceof \Order && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE) {
            $chat = BaseContext::TYPE_CHAT;
            $log = BaseContext::TYPE_LOG;
            $activity = BaseContext::TYPE_ACTIVITY;
            $external = \mongodb\OrderComment::TYPE_EXTERNAL;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;

            $services = \Services::retrieveByCode($type);
            if($services instanceof \Services){
                $message = "";
                if($order->mappingToService($type)){
                    $message = "Chọn dịch vụ {$services->getTitle()}";
                }elseif(!$order->mappingToService($type)){
                    $message = "Bỏ dịch vụ {$services->getTitle()}";
                }
                if($message != ""){
                    $content_activity = new OrderCommentResource\Activity( $message );
                    \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                        $content_activity, true,$activity );
                    $content_log = new OrderCommentResource\Log( $message );
                    \OrderComment::addComment( 0, $order->getId(), $external,
                        $content_log, false,$log );
                }
            }
        }
    }

    /**
     * Log order comment when change domestic shipping in backend
     * @param $data
     */
    public function logCommentWhenChangeDomesticShipping($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $order_old = isset($params["order_old"]) ? $params["order_old"] : array();
        $staff = isset($params["staff"]) ? $params["staff"] : array();

        if ($order instanceof \Order && $staff instanceof \Users
            && $staff->getSection() == \Users::SECTION_CRANE && $order_old instanceof \Order) {
            $log = BaseContext::TYPE_LOG;
            $activity = BaseContext::TYPE_ACTIVITY;
            $external = \mongodb\OrderComment::TYPE_EXTERNAL;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;



            $message = "Đơn hàng đã được thêm ".Common::numberFormat($order->getDomesticShippingFeeVnd())."
                    VNĐ phí Vận chuyển nội địa Trung Quốc";
            $message_activity = "Sửa phí VC nội địa TQ thành ".Common::numberFormat($order->getDomesticShippingFeeVnd())."
                    VNĐ";
            $content_activity = new OrderCommentResource\Activity( $message_activity );
            $content_log = new OrderCommentResource\Log( $message );
            \OrderComment::addComment( 0, $order->getId(), $external,
                $content_log, false,$log );
            \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                $content_activity, true,$activity );
        }
    }

    /**
     * Log order comment when CustomerConfirmed in frontend - log activity
     * @param $data
     */
    public function logOrderCommentWhenCustomerConfirmed($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();

        if ($order instanceof \Order) {
            $buyer = $order->getBuyer();
            $log = BaseContext::TYPE_LOG;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;

            $message = "{$buyer->getUsername()} đã xác nhận mua đơn hàng.";
            $content_log = new OrderCommentResource\Log( $message );
            \OrderComment::addComment( 0, $order->getId(), $internal,
                $content_log, false,$log );
        }
    }

    /**
     * Log order comment when change invoice in backend
     * @param $data
     */
    public function logOrderWhenChangeInvoice($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $staff = isset($params["staff"]) ? $params["staff"] : array();

        if ($order instanceof \Order && $staff instanceof \Users) {
            $activity = BaseContext::TYPE_ACTIVITY;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;

            $message = "Thay đổi mã hóa đơn trên site gốc.";
            $content = new OrderCommentResource\Activity( $message );
            \OrderComment::addComment( $staff->getId(), $order->getId(), $internal,
                $content, true,$activity );
        }
    }

    /**
     * Log order comment when stock in
     * @param $data
     * @throws Exception
     */
    public function logOrderWhenStockIn($data){
        try{
            $params = $data->params;
            $order = isset($params["order"]) ? $params["order"] : array();
            $warehouse = isset($params["warehouse"]) ? $params["warehouse"] : array();

            if ($order instanceof \Order) {
                $log = BaseContext::TYPE_LOG;
                $internal = \mongodb\OrderComment::TYPE_INTERNAL;

                $message = "Nhập kho {$warehouse}.";
                $content = new OrderCommentResource\Log( $message );
                \OrderComment::addComment( 0, $order->getId(), $internal,
                    $content, false,$log );
            }else{
                \SeuDo\Logger::factory("stock_in_out")->warning("\$order not instanceof \Order when stock in",$order);
            }
        }catch (\Exception $e){
            \SeuDo\Logger::factory("stock_in_out")->warning("Can't save log order when stock in ".$e->getMessage());
            throw $e;
        }

    }

    /**
     * Log order comment when stock out
     * @param $data
     */
    public function logOrderWhenStockOut($data){
        $params = $data->params;
        $order = isset($params["order"]) ? $params["order"] : array();
        $warehouse = isset($params["warehouse"]) ? $params["warehouse"] : "";

        if ($order instanceof \Order) {
            $log = BaseContext::TYPE_LOG;
            $internal = \mongodb\OrderComment::TYPE_INTERNAL;

            $message = "Xuất kho {$warehouse}.";
            $content = new OrderCommentResource\Log( $message );
            \OrderComment::addComment( 0, $order->getId(), $internal,
                $content, false,$log );
        }
    }

    /**
     * Khi update item như thay dổi số lượng - sửa giá sẽ activity trên item
     * @param $data
     * @return bool
     */
    public function activityItemWhenUpdate($data){
        try{
            $params = $data->params;
            $order_item = isset($params["order_item"]) ? $params["order_item"] : array();
            $order = isset($params["order"]) ? $params["order"] : array();
            $message = isset($params["message"]) ? $params["message"] : array();
            $staff = isset($params["staff"]) ? $params["staff"] : array();

            if($order_item instanceof \OrderItem && $staff instanceof \Users
                && $staff->getSection() == \Users::SECTION_CRANE && $order instanceof \Order){
                $user_id = $staff->getId();
                $created_time = new \MongoDate();
                \OrderItemComment::addComment($user_id, $order->getId(), $order_item->getId()
                    , $message, $created_time, BaseContext::TYPE_ACTIVITY);
                return true;
            }

            return false;

        }catch (\Exception $e){
            return false;
        }

    }
}