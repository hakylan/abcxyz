<?php
namespace Background\Task;

use Flywheel\Event\Event;
use Flywheel\Util\Folder;
use SeuDo\Logger;

class RefineOrder extends BackgroundBase {

    public function executeTest(){
        //TODO
    }

    public function executeCancelOldOrder() {
        $limit = (int) $this->getParam('limit');

        $sys_day_expire  = \SystemConfig::retrieveByConfigKey(\SystemConfig::ORDER_EXPIRE_DATE);

        if($sys_day_expire instanceof \SystemConfig){
            $day_expire = $sys_day_expire->getConfigValue();
        }else{
            $day_expire = 30;
        }

        //select order in status CANCELLED and OUT_OF_STOCK
        $q = \Order::select()->where('`status` = "INIT"')
            ->andWhere("`created_time` < DATE_SUB(NOW(), INTERVAL {$day_expire} DAY)")
            ->orderBy('created_time');

        if ($limit) {
            $q->setMaxResults($limit);
        }

        /** @var \Order[] $orders */
        $orders = $q->execute();
        foreach ($orders as $order) {
            $this->dispatch(ON_CHAT_ORDER_BACKEND, new Event($this, array(
                'order' => $order,
                'sender_id'=>'',
                'message_content'=>" bị hủy tự động sau 30 ngày không đặt cọc",
                'type_chat'=>'log'
            )));
            $order->changeStatus(\Order::STATUS_CANCELLED);
            //dispatch event for create notification here.
            print_r(":( {$order->getId()} gone!\n");
        }
    }

    public function executeCleanOrder() {
        $limit = (int) $this->getParam('limit');
        $json_file_path = RUNTIME_PATH .'/order_archive/';
        Folder::create($json_file_path);

        //select order in status CANCELLED and OUT_OF_STOCK
        $q = \Order::select()->where('`status` IN ("CANCELLED", "OUT_OF_STOCK")')
            ->andWhere('`cancelled_time` < DATE_SUB(NOW(), INTERVAL 60 DAY)
                        OR `out_of_stock_time` < DATE_SUB(NOW(), INTERVAL 60 DAY)')
            ->orderBy('out_of_stock_time')
            ->addOrderBy('cancelled_time');

        if ($limit) {
            $q->setMaxResults($limit);
        }

        $arrOrderIds = array();
        $tmp1 = $tmp2 = array();

        //Lấy toàn bộ KNDV đang xử lý
        $query1 = \Complaints::select();
        $query1->andWhere(" `status` = '" . \Complaints::STATUS_OUSTANDING . "' ");
        $data1 = $query1->execute();

        if( sizeof($data1) > 0 ) {
            foreach( $data1 as $item ) {
                if( $item instanceof \Complaints ) {
                    $id = (int) $item->getOrderId();

                    $arrOrderIds[ $id ] = $id;
                }
            }
        }

        //Lấy toàn bộ KNNB đang xử lý
        $query2 = \ComplaintSeller::select();
        $query2->andWhere(" `status` = '" . \ComplaintSeller::STATUS_PROCESSING . "' ");
        $data2 = $query2->execute();

        if( sizeof($data2) > 0 ) {
            foreach( $data2 as $item ) {
                if( $item instanceof \ComplaintSeller ) {
                    $id = (int) $item->getOrderId();

                    $arrOrderIds[ $id ] = $id;
                }
            }
        }

        /** @var \Order[] $orders */
        $orders = $q->execute();
        foreach ($orders as $order) {

            $order_id = (int) $order->getId();

            $is_deleted = true;

            if( in_array($order_id, $arrOrderIds) ) {
                $is_deleted = false;
            }

            if( $is_deleted ) {

                //write json file
                file_put_contents($json_file_path .$order->getCode() .'_' .$order->getId() .'.json', $order->toJSon());

                try {
                    $order->delete();
                    print_r("Deleted {$order->getId()}\n");
                } catch (\Exception $e) {
                    Logger::factory('system')->error($e->getMessage(). "\nTraces:\n" .$e->getTraceAsString());
                    print_r("Fail to delete {$order->getId()}\n");
                }
            }


        }
    }
} 