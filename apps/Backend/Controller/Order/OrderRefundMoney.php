<?php
namespace Backend\Controller\Order;

use Backend\Controller\BackendBase;
use SeuDo\Logger;

class OrderRefundMoney extends BackendBase
{

//    public function executeDefault() {
//
//        $query_user = \Users::select();
//        $query_user->addSelect('id');
//        $query_user->where("level_id > 1");
//        $list_user_id = $query_user->execute();
//        $array_user_id= array();
//        foreach($list_user_id as $user_id){
//            $array_user_id[] = $user_id->id;
//        }
//        $string_user_id = "(".implode(",",$array_user_id).")";
//        $query = \DebugRefundDiscount::select();
//        $query->addSelect("SUM(`money_refund`) as money");
//        $total_money_refund_data = $query->execute();
//        $total_money_refund =0;
//        foreach($total_money_refund_data as $money){
//            $total_money_refund = $money->money;
//        }
//        $list_user_order_info = array();
//
//        foreach($list_user_id as $user_id){
//            $user = \Users::retrieveById($user_id->id);
//            $tmp= array();
//            $query = \DebugRefundDiscount::select();
//            $query->addSelect("SUM(`money_refund`) as money");
//            $query->addSelect("user_id");
//            $query->where("`debug_refund_discount`.`user_id`=$user_id->id");
//            $query->groupBy("user_id");
//
//            $list_data_refund = $query->execute();
//            $tmp["money"]=0;
//            foreach($list_data_refund as $data){
//                $tmp["money"]=number_format(round($data->money));
//            }
//
//            if($tmp["money"]==0){
//                continue;
//            }
//            $tmp['username']=$user->getUsername();
//
//            $query = \DebugRefundDiscount::select();
//            $query->where("`debug_refund_discount`.`user_id`=$user_id->id");
//            $query->setMaxResults(1);
//            $query->orderBy("id","DESC");
//            $data_user = $query->execute();
//            foreach($data_user as $data){
//                $tmp['point']=$data->total_point;
//                $level = \LevelSetting::retrieveById($data->new_level);
//                $tmp['level']= $level->getLevelName();
//            }
//
//            //get list order of user
//            $query = \DebugRefundDiscount::select();
//            $query->where("`debug_refund_discount`.`user_id`=$user_id->id");
//            $query->orderBy("order_id");
//            $list_order = $query->execute();
//            foreach($list_order as $order){
//                $old_level = \LevelSetting::retrieveById($order->old_level)->getLevelName();
//                $new_level = \LevelSetting::retrieveById($order->new_level)->getLevelName();
//                $order_object = \Order::retrieveById($order->order_id);
//
//                $info_order = array(
//                    'order_id'=>$order->order_id,
//                    'order_code'=>$order_object->getCode(),
//                    'order_avatar'=>$order_object->getAvatar(),
//                    'fee_buying'=> number_format(round($order->fee_buying)),
//                    'fee_checking'=>number_format(round($order->fee_checking)),
//                    'fee_transport'=> number_format(round($order->fee_transport)),
//                    'fee_fix'=> number_format(round($order->fee_fixed)) ,
//                    'money_refund'=> number_format(round($order->money_refund)) ,
//                    'point'=>$order->point,
//                    'total_point'=>$order->total_point,
//                    'old_level'=> $old_level,
//                    'new_level'=> $new_level
//
//
//                );
//                $tmp['order'][]=$info_order;
//            }
//
//            $list_user_order_info[]= $tmp;
//        }
//
//
//
//        $this->setView('Order/refund');
//        $this->view()->assign('list_user_order_info', $list_user_order_info);
//        $this->view()->assign('total_money_refund', number_format(round($total_money_refund)));
//
//        return $this->renderComponent();
//    }

    public function executeDefault()
    {


        $query = \DebugRefundDiscount::select();
        $query->addSelect("SUM(`money_refund`) as money");
        $total_money_refund_data = $query->execute();
        $total_money_refund = 0;
        foreach ($total_money_refund_data as $money) {
            $total_money_refund = $money->money;
        }
        $list_user_order_info = array();

        $query = \DebugRefundDiscount::select();
        $query->addSelect("SUM(`money_refund`) as money");
        $query->addSelect("user_id");
        $query->groupBy("user_id");
        $query->orderBy("money", 'DESC');
        $list_data_refund = $query->execute();
        $list_customer_level = array();
        foreach ($list_data_refund as $refund) {
            if ($refund->money > 0) {
                $user = \Users::retrieveById($refund->user_id);
                $tmp = array();

                $tmp["money"] = number_format(round($refund->money));

                $tmp['username'] = $user->getUsername();

                $query = \DebugRefundDiscount::select();
                $query->where("`debug_refund_discount`.`user_id`=$refund->user_id");
                $query->setMaxResults(1);
                $query->orderBy("id", "DESC");
                $data_user = $query->execute();
                foreach ($data_user as $data) {
                    $tmp['point'] = $data->total_point;
                    $level = \LevelSetting::retrieveById($data->new_level);
                    $tmp['level'] = $level->getLevelName();

                    if (isset($list_customer_level[$level->getLevelName()])) {
                        $list_customer_level[$level->getLevelName()] = $list_customer_level[$level->getLevelName()] + 1;
                    } else {
                        $list_customer_level[$level->getLevelName()] = 1;
                    }


                }

                //get list order of user
                $query = \DebugRefundDiscount::select();
                $query->where("`debug_refund_discount`.`user_id`=$refund->user_id");
                $query->orderBy("order_id");
                $list_order = $query->execute();
                foreach ($list_order as $order) {
                    $old_level = \LevelSetting::retrieveById($order->old_level)->getLevelName();
                    $new_level = \LevelSetting::retrieveById($order->new_level)->getLevelName();
                    $order_object = \Order::retrieveById($order->order_id);

                    $info_order = array(
                        'order_id' => $order->order_id,
                        'order_code' => $order_object->getCode(),
                        'order_avatar' => $order_object->getAvatar(),
                        'fee_buying' => number_format(round($order->fee_buying)),
                        'fee_checking' => number_format(round($order->fee_checking)),
                        'fee_transport' => number_format(round($order->fee_transport)),
                        'fee_fix' => number_format(round($order->fee_fixed)),
                        'money_refund' => number_format(round($order->money_refund)),
                        'point' => $order->point,
                        'total_point' => $order->total_point,
                        'old_level' => $old_level,
                        'new_level' => $new_level


                    );
                    $tmp['order'][] = $info_order;
                }

                $list_user_order_info[] = $tmp;
            }
        }


        $this->setView('Order/refund');
        $this->view()->assign('list_user_order_info', $list_user_order_info);
        $this->view()->assign('total_money_refund', number_format(round($total_money_refund)));
        $this->view()->assign('list_customer_level', $list_customer_level);
        return $this->renderComponent();
    }



    public function executeTransactionReport(){
        $list_user_report = array();
        //lay danh sach user da refund sai nhieu lan
        $query = \UserTransaction::select();

        $query->where("transaction_detail='Trả lại tiền chiết khấu các đơn hàng cũ chưa chiết khấu theo chính sách thành viên của SeuDo. Chi tiết điểm tích lũy vui lòng xem tại trang cá nhân hoặc liên hệ với CSKH.'");
        $query->orderBy("user_id, created_time");
        $user_transaction = $query->execute();

        foreach ($user_transaction as $transaction) {
            if($transaction instanceof \UserTransaction){

                if(isset($list_user_report[$transaction->getUserId()])){
                    $list_user_report[$transaction->getUserId()]['get_money'] += $transaction->getAmount();
                    $list_user_report[$transaction->getUserId()]['detail'][]=  array(
                        'error'=>1,
                        'money'=>$transaction->getAmount(),
                        'code'=> $transaction->getTransactionCode(),
                        'note'=>'Trả lại tiền chiết khấu các đơn hàng cũ chưa chiết khấu theo chính sách thành viên của SeuDo. Chi tiết điểm tích lũy vui lòng xem tại trang cá nhân hoặc liên hệ với CSKH.',
                        'time'=>date("d-m-Y h:i:s",$transaction->getCreatedTime()->getTimestamp())
                    );

                }else{
                    $list_user_report[$transaction->getUserId()] = array(
                        'get_money'=>$transaction->getAmount(),
                        'refund_money'=>0,
                        'detail'=>array(
                            array(
                                'error'=>1,
                                'money'=>$transaction->getAmount(),
                                'code'=> $transaction->getTransactionCode(),
                                'note'=>'Trả lại tiền chiết khấu các đơn hàng cũ chưa chiết khấu theo chính sách thành viên của SeuDo. Chi tiết điểm tích lũy vui lòng xem tại trang cá nhân hoặc liên hệ với CSKH.',
                                'time'=>date("d-m-Y h:i:s",$transaction->getCreatedTime()->getTimestamp())
                            )
                        )
                    );
                }

            }
        }



        //lay danh sách user da nhan tien giam gia cac loai phi 10/9 - nay
        $query = \DebugRefundOrder::select();
        $query->where("discount_money >0");
        $user_get_discount_fee = $query->execute();
        foreach($user_get_discount_fee as $data){
            if($data instanceof \DebugRefundOrder){
                $order_temp = \Order::retrieveById($data->getOrderId());

                if(isset($list_user_report[$data->getUserId()])){

                    $list_user_report[$data->getUserId()]['get_money'] += $data->getDiscountMoney();

                    $list_user_report[$data->getUserId()]['detail'][]=  array(
                        'error'=>1,
                        'money'=>$data->getDiscountMoney(),
                        'code'=>'',
                        'note'=>"Tiền đã giảm giá trực tiếp vào tài chính với đơn hàng order_id=".$data->getOrderId()." va order code =".$order_temp->getCode(),
                        'time'=>date("d-m-Y h:i:s",$order_temp->getConfirmDeliveryTime()->getTimestamp())
                    );

                }else{
                    $list_user_report[$data->getUserId()] = array(
                        'get_money'=>$data->getDiscountMoney(),
                        'refund_money'=>0,
                        'detail'=>array(
                            array(
                                'error'=>1,
                                'money'=>$data->getDiscountMoney(),
                                'code'=>'',
                                'note'=>"Tiền đã giảm giá trực tiếp vào tài chính với đơn hàng order_id=".$data->getOrderId()." va order code =".$order_temp->getCode(),
                                'time'=>date("d-m-Y h:i:s",$order_temp->getConfirmDeliveryTime()->getTimestamp())
                            )
                        )
                    );
                }
            }
        }



        //lay danh sach user nhan lai tien chiet khau sau khi da kiem tra va chay lai
        $query = \DebugRefundDiscount::select();
        $query->addSelect("SUM(`money_refund`) as money");
        $query->addSelect("user_id");
        $query->groupBy("user_id");
        $query->orderBy("money", 'DESC');
        $list_data_refund = $query->execute();
        $i = 0;

        foreach ($list_data_refund as $refund) {
            if ($refund->money > 0) {
                $user = \Users::retrieveById($refund->user_id);
                if ($user instanceof \Users) {
                    if ($user->getUsername() != "duybao") {

                        if(isset($list_user_report[$refund->user_id])){

                            $list_user_report[$refund->user_id]['refund_money'] += $refund->money;

//                            $list_user_report[$refund->user_id]['detail'][]=  array(
//                                'money'=>$refund->money,
//                                'note'=>"Trả lại tiền thừa cho khách sau khi tính lại chiết khấu",
//                                'time'=>date("d-m-Y h:i:s",time())
//                            );

                        }else{
                            $list_user_report[$refund->user_id] = array(
                                'get_money'=>0,
                                'refund_money'=>$refund->money,
                                'detail'=>array(
//                                    array(
//                                        'money'=>$refund->money,
//                                        'note'=>"Trả lại tiền thừa cho khách sau khi tính lại chiết khấu",
//                                        'time'=>date("d-m-Y h:i:s",time())
//                                    )
                                )
                            );
                        }

                    }else{
                        //
                    }

                }else{
                    var_dump("user not instane of User");
                }
            }else{
                //money <=0
            }

        }

        //thong ke khach hang bi truy thu
        $query = \UserTransaction::select();

        $query->where("transaction_detail='Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật. Mọi thắc mắc Quý khách liên hệ DVKH để được giải đáp. Vô cùng xin lỗi quý khách.'");
        $query->orderBy("user_id, created_time");
        $user_transaction = $query->execute();

        foreach ($user_transaction as $transaction) {
            if($transaction instanceof \UserTransaction){
                $money = $transaction->getAmount() *(-1);
                if(isset($list_user_report[$transaction->getUserId()])){
//                    $list_user_report[$transaction->getUserId()]['refund_money'] += $transaction->getAmount();
                    $list_user_report[$transaction->getUserId()]['detail'][]=  array(
                        'error'=>0,
                        'money'=>$money,
                        'code'=> $transaction->getTransactionCode(),
                        'note'=>'Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật. Mọi thắc mắc Quý khách liên hệ DVKH để được giải đáp. Vô cùng xin lỗi quý khách.',
                        'time'=>date("d-m-Y h:i:s",$transaction->getCreatedTime()->getTimestamp())
                    );

                }else{
                    $list_user_report[$transaction->getUserId()] = array(
                        'get_money'=>0,
                        'refund_money'=>0,
                        'detail'=>array(
                            array(
                                'error'=>0,
                                'money'=>$money,
                                'code'=> $transaction->getTransactionCode(),
                                'note'=>'Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật. Mọi thắc mắc Quý khách liên hệ DVKH để được giải đáp. Vô cùng xin lỗi quý khách.',
                                'time'=>date("d-m-Y h:i:s",$transaction->getCreatedTime()->getTimestamp())
                            )
                        )
                    );
                }

            }
        }

        $this->setView('Order/refund_report');
        $this->view()->assign('list_user_report', $list_user_report);

        return $this->renderComponent();


    }




}
