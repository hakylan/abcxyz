<?php
///**
// * Created by PhpStorm.
// * User: Dell Precision M6400
// * Date: 8/26/14
// * Time: 3:33 PM
// */
//namespace Background\Task;
//
//use Background\Library\EmailHelper;
//use Background\Library\OrderHelper;
//use Flywheel\Db\Type\DateTime;
//use Flywheel\Exception;
//use Flywheel\Queue\Queue;
//use Flywheel\Util\Folder;
//use SeuDo\Logger;
//use SeuDo\Logistic;
//use SeuDo\Logistic\Client;
//
//class OrderRefundMoney extends BackgroundBase
//{
//    public function executeRefundMoney()
//    {
////        $table = '<p><table width="100%" cellspacing="0" cellpadding="5" border="1" style="border-collapse:collapse">
////                        <tr>
////                          <th >STT</th>
////                          <th >USERNAME</th>
////                          <th >EMAIL</th>
////                          <th >MÃ KHÁCH</th>
////                          <th >TIỀN TRẢ LẠI</th>
////                        <th >MÃ GIAO DỊCH TRẢ LẠI</th>
////
////                        </tr>';
////        $query = \DebugRefundDiscount::select();
////        $query->addSelect("SUM(`money_refund`) as money");
////        $query->addSelect("user_id");
////        $query->groupBy("user_id");
////        $query->orderBy("money", 'DESC');
////        $list_data_refund = $query->execute();
////        $i = 0;
////
////        foreach ($list_data_refund as $refund) {
////            if ($refund->money > 0) {
////                $user = \Users::retrieveById($refund->user_id);
////                if ($user instanceof \Users) {
////                    if ($user->getUsername() != "duybao") {
////                        $i++;
////
////                        Logger::factory('refund_money_after_discount')->debug("before balance:" . $user->getAccountBalance());
////
////                        $refund_amount = round($refund->money);
////                        Logger::factory('refund_money_after_discount')->debug("refund money:" . $refund_amount);
////
////                        $detail = array(
////                            'type' => 'REFUND',
////                            'message' => "Trả lại tiền chiết khấu các đơn hàng cũ chưa chiết khấu theo chính sách thành viên của SeuDo. Chi tiết điểm tích lũy vui lòng xem tại trang cá nhân hoặc liên hệ với CSKH."
////                        );
////                        $user->beginTransaction();
////                        try {
////                            $transfer = \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail), $detail['message']);
////
////                            try {
////
////                                $balance = $transfer['to_account']['balance'];
////                                Logger::factory('refund_money_after_discount')->debug("after balance:" . $balance);
////
////                                \UsersPeer::changeAccountBalance($user, $balance);
////                                $username = $user->getUsername();
////                                $user_email = $user->getEmail();
////                                $user_code = $user->getCode();
////
////                                //save user transaction
////                                try {
////                                    $accountantTransaction = $transfer['receiving_transaction'];
////                                    $transaction_code = $accountantTransaction['uid'];
////
////                                    $userTransaction = new \UserTransaction();
////                                    $userTransaction->setUserId($user->getId());
////                                    $userTransaction->setState(\UserTransaction::STATE_COMPLETED);
////                                    $userTransaction->setTransactionCode($transaction_code);
////                                    $userTransaction->setTransactionType(\UserTransaction::TRANSACTION_TYPE_REFUND);
////                                    $userTransaction->setAmount($accountantTransaction["amount"]);
////                                    $userTransaction->setEndingBalance($balance);
////                                    $userTransaction->setTransactionDetail($detail['message']);
////                                    $userTransaction->setTransactionNote($detail['message']);
////                                    $userTransaction->setCreatedTime(new \DateTime());
////                                    if (is_array($accountantTransaction['modified_time'])) {
////                                        $closedTime = new \DateTime($accountantTransaction['modified_time']['date']);
////                                    } elseif (is_scalar($accountantTransaction['modified_time'])) {
////                                        $closedTime = new \DateTime($accountantTransaction['modified_time']);
////                                    } else {
////                                        $closedTime = new \DateTime();
////                                    }
////                                    $userTransaction->setClosedTime($closedTime);
////
////                                    if (!$userTransaction->save()) {
////                                        throw new \RuntimeException('Could not save user transaction:' . $userTransaction->getValidationFailuresMessage("\n"));
////                                    }
////                                } catch (\Exception $e) {
////                                    throw $e;
////                                }
////
////                                $user->commit();
////
////                                $table .= '<tr>
////                                            <td>' . $i . '</td>
////                                            <td>' . $username . '</td>
////                                            <td>' . $user_email . '</td>
////                                             <td>' . $user_code . '</td>
////                                             <td>' . number_format($refund_amount) . ' VNĐ</td>
////                                             <td>' . $transaction_code . '</td>
////                                       </tr>';
////
////                                Logger::factory('refund_money_after_discount')->info('Refund old order after discount completed', array(
////
////                                    'user' => $username,
////                                    'email'=>$user_email,
////                                    'user_code'=>$user_code,
////                                    'refund money'=> number_format($refund_amount),
////                                    'transaction code'=> $transaction_code,
////                                    'accountant_transaction' => $userTransaction->getTransactionCode(),
////                                    'user_transaction' => $userTransaction->getId()
////                                ));
////
////
////
////
////                            } catch (\Exception $e) {
////                                //charging back
////                                \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
////
////                                    'type' => 'ROLLBACK',
////                                    'detail' => 'Giao dịch hoàn tiền chiết khấu các đơn hàng cũ không thành công. Trả lại tiền cho dịch vụ.'
////                                )));
////
////                                throw $e;
////                            }
////                        } catch (\Exception $e) { //accountant's transaction success need rollback and recharge
////                            $user->rollBack();
////                            Logger::factory('refund_money_after_discount')->error($e->getMessage() . ".\nTrances:\n" . $e->getTraceAsString());
////
////                        }
////                        Logger::factory('refund_money_after_discount')->debug("refund money success:" . $user->getUsername());
////
////                    }
////                }
////            }
////        }//end for
////
////        $table .= '</table>';
////        $list_receiver_email = array('ha nguyen' => 'nguyenvietha@alimama.vn');
////        $data = array(
////            'email' => $list_receiver_email,
////            'subject' => 'Kết quả thống kê tiền trả lại cho khách hàng  ' . date('H:i d/m/Y', time()),
////            'body' => $table,
////
////        );
////
////        $result = EmailHelper::sendEmail($data);
////        var_dump("DONE TASK REFUND");
//
//    }
//
//    public function executeSaveListOrderAndCustomer()
//    {
//        die;
//        ini_set('memory_limit', '-1');
//        $from = $this->getParam("from");
//        $limit = $this->getParam("limit");
//        $order_model = new \Order();
//        $query = $order_model::select();
//
//        $query->where("`status` IN ('CONFIRM_DELIVERY','DELIVERING','RECEIVED')");
//        $query->andWhere("`confirm_delivery_time` <= '2014-09-13 22:10:00'");
//        $query->orderBy("created_time");
//        $query->setFirstResult($from);
//        $query->setMaxResults($limit);
//
//        $list_order = $query->execute();
//        var_dump(count($list_order));
//        if (count($list_order) > 0) {
//            foreach ($list_order as $order) {
//                $fee_buying = 0;
//                $fee_checking = 0;
//                $fee_transport = 0;
//                if ($order instanceof \Order) {
//                    try {
//                        $debug_refund_discount = new \DebugRefundDiscount();
//                        $debug_refund_discount->user_id = $order->getBuyerId();
//                        $debug_refund_discount->order_id = $order->getId();
//                        $order_services = \OrderService::findByOrderId($order->getId());
//                        if ($order_services) {
//                            foreach ($order_services as $order_service) {
//                                if ($order_service instanceof \OrderService) {
//                                    switch ($order_service->getServiceCode()) {
//                                        case "BUYING":
//                                            $fee_buying = $order_service->getMoney();
//                                            $service_buying_id = $order_service->getId();
//                                            break;
//                                        case "CHECKING":
//                                            $fee_checking = $order_service->getMoney();
//                                            var_dump("co phi kiem hang:" . $fee_checking);
//                                            $service_checking_id = $order_service->getId();
//                                            break;
//                                        case "SHIPPING_CHINA_VIETNAM":
//                                        case "EXPRESS_CHINA_VIETNAM":
//                                            $fee_transport = $order_service->getMoney();
//                                            $service_transport_id = $order_service->getId();
//                                            break;
//
//                                    }
//                                }
//
//                            }
//                        }
//
//                        if ($fee_buying > 0) {
//
//                            $debug_refund_discount->setFeeBuying($fee_buying);
//                            $debug_refund_discount->setServiceBuyingId($service_buying_id);
//                        } else {
//
//                            $debug_refund_discount->setFeeBuying(0);
//                            $debug_refund_discount->setServiceBuyingId(0);
//                        }
//
//                        if ($fee_checking > 0) {
//                            var_dump("ton tai phi kiem hang:" . $fee_checking);
//                            $debug_refund_discount->setFeeChecking($fee_checking);
//                            $debug_refund_discount->setServiceCheckingId($service_checking_id);
//                        } else {
//                            var_dump("khong ton tai phi kiem hang");
//                            $debug_refund_discount->setFeeChecking(0);
//                            $debug_refund_discount->setServiceCheckingId(0);
//                        }
//
//                        if ($fee_transport > 0) {
//                            $debug_refund_discount->setFeeTransport($fee_transport);
//                            $debug_refund_discount->setServiceTransportId($service_transport_id);
//                        } else {
//                            $debug_refund_discount->setFeeTransport(0);
//                            $debug_refund_discount->setServiceTransportId(0);
//                        }
//
//                        $debug_refund_discount->setFeeFixed(5000);
//                        $debug_refund_discount->save();
//                        var_dump("order id=" . $order->getId());
//                    } catch (\Exception $e) {
//                        var_dump($e->getMessage() . " and order id=" . $order->getId());
//                    }
//                }
//
//
//            }
//        }
//    }
//
//    public function executeCalculateRefundMoney()
//    {
//        die;
//        ini_set('memory_limit', '-1');
//        set_time_limit(7000);
//        $begin_time = time();
//        $count_run = 0;
//        while ($count_run < 1000) {
//            $count_run++;
//            $query = \DebugRefundDiscount::select();
//            $query->andWhere("status_calculated = 0");
//            $query->setMaxResults(1);
//            $list_order_refund = $query->execute();
//
//            if (count($list_order_refund) > 0) {
//                foreach ($list_order_refund as $order_refund) {
//                    if ($order_refund instanceof \DebugRefundDiscount) {
//                        //check status is calculating refund
//                        $order_refund->setStatusCalculated(1);
//                        $order_refund->save();
//                        //info customer
//                        $customer_data = \Users::findById($order_refund->getUserId());
//                        $customer = '';
//                        if (is_array($customer_data) && isset($customer_data[0])) {
//                            $customer = $customer_data[0];
//                        }
//                        if ($customer instanceof \Users) {
//                            // $level
//                            $customer_level_id = $customer->getLevelId();
//                            $service_discount = \ServiceDiscount::findByLevelId($customer_level_id);
//                            if ($service_discount) {
//                                $note = '';
//                                $money_refund = 0;
//                                $point_member = 0;
//                                $note_history = array();
//                                $is_calculate_transport = false;
//                                foreach ($service_discount as $fee_discount) {
//                                    if ($fee_discount instanceof \ServiceDiscount) {
//
//                                        switch ($fee_discount->getService()) {
//                                            case \ServiceDiscount::SERVICE_BUYING:
//                                                $discount_money_buying = 0;
//                                                if ($fee_discount->getType() == \ServiceDiscount::TYPE_FIX) {
//
//                                                    $discount_money_buying = $order_refund->getFeeBuying() - $fee_discount->getValue();
//                                                    $money_refund = $money_refund + $fee_discount->getValue();
//                                                    $point_member = $point_member + round(($order_refund->getFeeBuying() - $fee_discount->getValue()) / 1000, 2);
//
//                                                    $note .= "// refund fee buying: {$fee_discount->getValue()}";
//                                                    $note .= "// point member:  round(({$order_refund->getFeeBuying()} - {$fee_discount->getValue()}) / 1000,2)";
//                                                    die("buying");
//                                                } elseif ($fee_discount->getType() == \ServiceDiscount::TYPE_PERCENT) {
//
//                                                    $discount_money_buying = $order_refund->getFeeBuying() - $order_refund->getFeeBuying() * $fee_discount->getValue() / 100;
//                                                    $money_refund = $money_refund + $order_refund->getFeeBuying() * $fee_discount->getValue() / 100;
//                                                    $note .= "// refund fee buying:  {$order_refund->getFeeBuying()} * {$fee_discount->getValue()} / 100";
//
//
//                                                    $point = $order_refund->getFeeBuying() - $order_refund->getFeeBuying() * $fee_discount->getValue() / 100;
//                                                    $point = round($point / 1000, 2);
//                                                    //$point = floor($order_refund->getFeeBuying()/1000);
//                                                    $point_member = $point_member + $point;
//                                                    $note .= "// point member: {$order_refund->getFeeBuying()} - {$order_refund->getFeeBuying()} * {$fee_discount->getValue()} / 100 => {$point}";
//
//                                                    if ($point > 0) {
//                                                        $note_history[] = "Tích lũy từ phí mua hàng {$point} điểm";
//                                                    }
//
//                                                    var_dump("buying:" . $point_member);
//                                                }
//
//                                                if ($order_refund->getServiceBuyingId() > 0) {
//                                                    $order_service_data = \OrderService::retrieveById($order_refund->getServiceBuyingId());
//                                                    $order_service_data->setDiscountedMoney($discount_money_buying);
//                                                    $order_service_data->save();
//                                                }
//                                                break;
//                                            case \ServiceDiscount::SERVICE_CHECKING:
//                                                $discount_money_checking = 0;
//                                                if ($fee_discount->getType() == \ServiceDiscount::TYPE_FIX) {
//
//                                                    $discount_money_checking = $order_refund->getFeeChecking() - $fee_discount->getValue();
//                                                    $money_refund = $money_refund + $fee_discount->getValue();
//                                                    $point_member = $point_member + round(($order_refund->getFeeChecking() - $fee_discount->getValue()) / 1000, 2);
//
//                                                    $note .= "// refund fee checking: {$fee_discount->getValue()}";
//                                                    $note .= "// point member:  round(({$order_refund->getFeeChecking()} - {$fee_discount->getValue()}) / 1000,2)";
//                                                    die("checking");
//                                                } elseif ($fee_discount->getType() == \ServiceDiscount::TYPE_PERCENT) {
//
//                                                    $discount_money_checking = $order_refund->getFeeChecking() - $order_refund->getFeeChecking() * $fee_discount->getValue() / 100;
//                                                    $money_refund = $money_refund + $order_refund->getFeeChecking() * $fee_discount->getValue() / 100;
//                                                    $note .= "// refund fee checking:  {$order_refund->getFeeChecking()} * {$fee_discount->getValue()} / 100";
//
//
//                                                    $point = $order_refund->getFeeChecking() - $order_refund->getFeeChecking() * $fee_discount->getValue() / 100;
//                                                    $point = round($point / 1000, 2);
//                                                    //$point = floor($order_refund->getFeeChecking()/1000);
//                                                    $point_member = $point_member + $point;
//                                                    $note .= "// point member: {$order_refund->getFeeChecking()} - {$order_refund->getFeeChecking()} * {$fee_discount->getValue()} / 100 => {$point}";
//
//                                                    if ($point > 0) {
//                                                        $note_history[] = "Tích lũy từ phí kiểm hàng {$point} điểm";
//                                                    }
//                                                    var_dump("checking:" . $point_member);
//                                                }
//
//                                                if ($order_refund->getServiceCheckingId() > 0) {
//                                                    $order_service_data = \OrderService::retrieveById($order_refund->getServiceCheckingId());
//                                                    $order_service_data->setDiscountedMoney($discount_money_checking);
//                                                    $order_service_data->save();
//                                                }
//                                                break;
//                                            case \ServiceDiscount::SERVICE_SHIPPING_CHINA_VIETNAM:
//                                            case \ServiceDiscount::SERVICE_EXPRESS_CHINA_VIETNAM:
//                                                if ($is_calculate_transport == false) {
//                                                    $is_calculate_transport = true;
//                                                    $discount_money_transport = 0;
//                                                    if ($fee_discount->getType() == \ServiceDiscount::TYPE_FIX) {
//
//                                                        $discount_money_transport = $order_refund->getFeeTransport() - $fee_discount->getValue();
//                                                        $money_refund = $money_refund + $fee_discount->getValue();
//                                                        $point_member = $point_member + round(($order_refund->getFeeTransport() - $fee_discount->getValue()) / 5000, 2);
//
//                                                        $note .= "// refund fee transport: {$fee_discount->getValue()}";
//                                                        $note .= "// point member:  round(({$order_refund->getFeeTransport()} - {$fee_discount->getValue()}) / 5000,2)";
//                                                        die('transport');
//                                                    } elseif ($fee_discount->getType() == \ServiceDiscount::TYPE_PERCENT) {
//
//                                                        $discount_money_transport = $order_refund->getFeeTransport() - $order_refund->getFeeTransport() * $fee_discount->getValue() / 100;
//                                                        $money_refund = $money_refund + $order_refund->getFeeTransport() * $fee_discount->getValue() / 100;
//                                                        $note .= "// refund fee transport:  {$order_refund->getFeeTransport()} * {$fee_discount->getValue()} / 100";
//
//
//                                                        $point = $order_refund->getFeeTransport() - $order_refund->getFeeTransport() * $fee_discount->getValue() / 100;
//                                                        $point = round($point / 5000, 2);
//                                                        //$point = floor($order_refund->getFeeTransport()/5000);
//                                                        $point_member = $point_member + $point;
//
//
//                                                        $note .= "// point member: {$order_refund->getFeeTransport()} - {$order_refund->getFeeTransport()} * {$fee_discount->getValue()} / 100 => {$point}";
//
//                                                        if ($point > 0) {
//                                                            $note_history[] = "Tích lũy từ phí vận chuyển quốc tế {$point} điểm";
//                                                        }
//                                                        var_dump("transport:" . $point_member);
//
//                                                    }
//
//                                                    if ($order_refund->getServiceTransportId() > 0) {
//                                                        $order_service_data = \OrderService::retrieveById($order_refund->getServiceTransportId());
//                                                        $order_service_data->setDiscountedMoney($discount_money_transport);
//                                                        $order_service_data->save();
//                                                    }
//                                                }
//                                                break;
//                                            case \ServiceDiscount::SERVICE_ORDER_FIXED:
//                                                if ($fee_discount->getType() == \ServiceDiscount::TYPE_FIX) {
//
//                                                    $money_refund = $money_refund + $fee_discount->getValue();
//
//
//                                                    $note .= "// refund fee order fix: {$fee_discount->getValue()}";
//
//
//                                                } elseif ($fee_discount->getType() == \ServiceDiscount::TYPE_PERCENT) {
//
//                                                    $money_refund = $money_refund + $order_refund->getFeeFixed() * $fee_discount->getValue() / 100;
//
//
//                                                    $note .= "// refund fee order fix:  {$order_refund->getFeeFixed()} * {$fee_discount->getValue()} / 100";
//
//
//                                                }
//                                                break;
//                                        }
//                                        //end switch
//
//
//                                    }
//                                    //if ($fee_discount instanceof \ServiceDiscount) {
//                                }
//                                //foreach ($service_discount as $fee_discount) {
//                                $money_refund = round($money_refund, 2);
//                                $order_refund->setMoneyRefund($money_refund);
//                                $order_refund->setPoint($point_member);
//                                $total_point = $customer->getPointMember() + $point_member;
//
//                                $order_refund->setTotalPoint($total_point);
//                                //get level by point
//                                $query_level = \LevelSetting::select();
//                                $query_level->where("{$total_point} >= from_score");
//                                $query_level->andWhere("{$total_point} < to_score");
//                                $query_level->setMaxResults(1);
//                                $level_setting = $query_level->execute();
//                                $new_level = $customer->getLevelId();
//                                if (count($level_setting) > 0) {
//                                    foreach ($level_setting as $level) {
//                                        if ($level instanceof \LevelSetting) {
//                                            $new_level = $level->getId();
//                                        }
//                                    }
//                                }
//
//                                $order_refund->setOldLevel($customer->getLevelId());
//                                $order_refund->setNewLevel($new_level);
//                                $order_refund->setNote($note);
//                                $order_refund->setStatusCalculated(2); //done
//                                $order_refund->save();
//
//                                $customer->setPointMember($total_point);
//                                $customer->setLevelId($new_level);
//                                $customer->save();
//                                //save history discount
//                                $order_data = \Order::retrieveById($order_refund->getOrderId());
//                                $member_score_history = new \MemberScoreHistory();
//                                $member_score_history->setUserId($order_refund->getUserId());
//                                $member_score_history->setObjectId($order_refund->getOrderId());
//                                $member_score_history->setObjectType("ORDER");
//                                $member_score_history->setLevelId($new_level);
//                                $member_score_history->setPoint($point_member);
//                                $member_score_history->setTotalPoint($total_point);
//                                if (count($note_history) > 0) {
//                                    $note_history_string = implode(", ", $note_history);
//                                } else {
//                                    $note_history_string = '';
//                                }
//
//                                $member_score_history->setNote($note_history_string);
//                                $member_score_history->setCreatedTime(new \DateTime($order_data->getCreatedTime()));
//                                $member_score_history->save();
//                                var_dump("thoi gian 1 vong lap=" . (time() - $begin_time));
//                            } else {
//                                var_dump("khong ton tai service discount");
//                                die;
//                            }
//                        } else {
//                            var_dump("khong tim thay customer");
//                            die;
//                        }
//
//                    } //if ($order_refund instanceof \DebugRefundDiscount) {
//                    else {
//                        var_dump('$order_refund not instanceof \DebugRefundDiscount');
//                        die;
//                    }
//                }
//                //foreach ($list_order_refund as $order_refund) {
//            } else {
//                var_dump("thoi gian run=" . (time() - $begin_time));
//                var_dump("khong tim thay don hang can refund");
//                die;
//            }
//        }
//        //end while
//        echo "DONe";
//    }
//
//    public function executeSendEmailRefund()
//    {
//
//        die;
//        $query_user = \Users::select();
//        $query_user->addSelect('id');
//        $query_user->where("level_id > 1");
//        $list_user_id = $query_user->execute();
//        $array_user_id = array();
//        foreach ($list_user_id as $user_id) {
//            $array_user_id[] = $user_id->id;
//        }
//        $string_user_id = "(" . implode(",", $array_user_id) . ")";
//        $query = \DebugRefundDiscount::select();
//        $query->addSelect("SUM(`money_refund`) as money");
//        $query->addSelect("user_id");
//        $query->where("`debug_refund_discount`.`user_id` IN {$string_user_id}");
//        $query->groupBy("user_id");
//
//        $list_data_refund = $query->execute();
//
//        $table_2 = 'Thống kê theo ngày:<p><table width="100%" cellspacing="0" cellpadding="5" border="1" style="border-collapse:collapse">
//                        <tr>
//                          <th >STT</th>
//                          <th >USERNAME</th>
//                          <th >Tiền Trả Lại</th>
//                          <th >Điểm Tích Lũy</th>
//                          <th >Level</th>
//
//
//                        </tr>';
//
//
//        $i = 1;
//        foreach ($list_data_refund as $data) {
//
//            $user = \Users::retrieveById($data->user_id);
//            $level = \LevelSetting::retrieveById($user->getLevelId());
//            $table_2 .= '<tr>
//                <td>' . $i . '</td>
//                <td>' . $user->getUsername() . '</td>
//                <td>' . number_format(round($data->money)) . ' VNĐ</td>
//                 <td>' . $user->getPointMember() . '</td>
//                 <td>' . $level->getLevelName() . '</td>
//                ';
//
//            $table_2 .= ' </tr>';
//
//
//            $i++;
//        }
//        $table_2 .= '</table>';
//
//
//        $body = $table_2;
//
//
//        $list_receiver_email = array('ha nguyen' => 'nguyenvietha@alimama.vn');
//
//
//        $data = array(
//            'email' => $list_receiver_email,
//            'subject' => 'Kết quả thống kê tiền trả lại cho khách hàng  ' . date('H:i d/m/Y', time()),
//            'body' => $body,
//
//        );
//
//        $result = EmailHelper::sendEmail($data);
//        var_dump($result);
//    }
//
//
//
//    public function executeSaveHistoryDiscountOrder()
//    {
//        die;
//        $order_model = new \Order();
//        $query = $order_model::select();
//
//        $query->where("`status` IN ('CONFIRM_DELIVERY','DELIVERING','RECEIVED')");
//        $query->andWhere("`confirm_delivery_time` >= '2014-09-09 10:10:00'");
//        $query->andWhere("`confirm_delivery_time` <= '2014-09-13 22:10:00'");
//        $query->orderBy("created_time");
//
//        $list_order = $query->execute();
//        var_dump(count($list_order));
//        $i = 0;
//        if (count($list_order) > 0) {
//            foreach ($list_order as $order) {
//                $i++;
//                var_dump("lan " . $i . ": " . $order->getId());
//                $discount_money = 0;
//                $note = array();
//                if ($order instanceof \Order) {
//                    $order_services = \OrderService::findByOrderId($order->getId());
//                    if ($order_services) {
//                        foreach ($order_services as $order_service) {
//                            if ($order_service instanceof \OrderService) {
//                                switch ($order_service->getServiceCode()) {
//                                    case "BUYING":
//                                        $discount_money = $discount_money + ($order_service->getMoney() - $order_service->getDiscountedMoney());
//                                        $note[] = "phi mua hang: ({$order_service->getMoney()}- {$order_service->getDiscountedMoney()})";
//                                        break;
//                                    case "CHECKING":
//                                        $discount_money = $discount_money + ($order_service->getMoney() - $order_service->getDiscountedMoney());
//                                        $note[] = "phi mua hang: ({$order_service->getMoney()}- {$order_service->getDiscountedMoney()})";
//                                        break;
//                                    case "SHIPPING_CHINA_VIETNAM":
//                                    case "EXPRESS_CHINA_VIETNAM":
//                                        $discount_money = $discount_money + ($order_service->getMoney() - $order_service->getDiscountedMoney());
//                                        $note[] = "phi mua hang: ({$order_service->getMoney()}- {$order_service->getDiscountedMoney()})";
//                                        break;
//
//                                }
//                            }
//
//                        }
//
//                        if (count($note) > 0) {
//                            $str_note = "Phí giảm giá: " . implode(",", $note);
//                        } else {
//                            $str_note = '';
//                        }
//
//                        try {
//                            $debug_order = new \DebugRefundOrder();
//                            $debug_order->setUserId($order->getBuyerId());
//                            $debug_order->setOrderId($order->getOrderId());
//                            $debug_order->setDiscountMoney($discount_money);
//                            $debug_order->setNote($str_note);
//                            $debug_order->save();
//                        } catch (\Exception $e) {
//                            var_dump($e->getMessage());
//                        }
//                    }
//                } else {
//                    var_dump("not order");
//                }
//            }
//        }
//    }
//
//    public function executeFinallRefund()
//    {
//        die;
//        $list_user_report = array();
//        //lay danh sach user da refund sai nhieu lan
//        $query = \UserTransaction::select();
//
//        $query->where("transaction_detail='Trả lại tiền chiết khấu các đơn hàng cũ chưa chiết khấu theo chính sách thành viên của SeuDo. Chi tiết điểm tích lũy vui lòng xem tại trang cá nhân hoặc liên hệ với CSKH.'");
//        $query->orderBy("user_id, created_time");
//        $user_transaction = $query->execute();
//
//        foreach ($user_transaction as $transaction) {
//            if ($transaction instanceof \UserTransaction) {
//
//                if (isset($list_user_report[$transaction->getUserId()])) {
//                    $list_user_report[$transaction->getUserId()]['get_money'] += $transaction->getAmount();
//                    $list_user_report[$transaction->getUserId()]['detail'][] = array(
//                        'money' => $transaction->getAmount(),
//                        'note' => "Thu lại tiền đã refund sau khi tính chiết khấu cho khách bị sai",
//                        'time' => date("d-m-Y h:i:s", $transaction->getCreatedTime()->getTimestamp())
//                    );
//
//                } else {
//                    $list_user_report[$transaction->getUserId()] = array(
//                        'get_money' => $transaction->getAmount(),
//                        'refund_money' => 0,
//                        'detail' => array(
//                            array('money' => $transaction->getAmount(),
//                                'note' => "Thu lại tiền đã refund sau khi tính chiết khấu cho khách bị sai",
//                                'time' => date("d-m-Y h:i:s", $transaction->getCreatedTime()->getTimestamp())
//                            )
//                        )
//                    );
//                }
//
//            }
//        }
//
//
//        //lay danh sách user da nhan tien giam gia cac loai phi 10/9 - nay
//        $query = \DebugRefundOrder::select();
//        $query->where("discount_money >0");
//        $user_get_discount_fee = $query->execute();
//        foreach ($user_get_discount_fee as $data) {
//            if ($data instanceof \DebugRefundOrder) {
//                $order_temp = \Order::retrieveById($data->getOrderId());
//
//                if (isset($list_user_report[$data->getUserId()])) {
//
//                    $list_user_report[$data->getUserId()]['get_money'] += $data->getDiscountMoney();
//
//                    $list_user_report[$data->getUserId()]['detail'][] = array(
//                        'money' => $data->getDiscountMoney(),
//                        'note' => "Thu lại tiền đã giảm giá với đơn hàng order_id=" . $data->getOrderId() . " va order code =" . $order_temp->getCode(),
//                        'time' => date("d-m-Y h:i:s", $order_temp->getConfirmDeliveryTime()->getTimestamp())
//                    );
//
//                } else {
//                    $list_user_report[$data->getUserId()] = array(
//                        'get_money' => $data->getDiscountMoney(),
//                        'refund_money' => 0,
//                        'detail' => array(
//                            array(
//                                'money' => $data->getDiscountMoney(),
//                                'note' => "Thu lại tiền đã giảm giá với đơn hàng order_id=" . $data->getOrderId() . " va order code =" . $order_temp->getCode(),
//                                'time' => date("d-m-Y h:i:s", $order_temp->getConfirmDeliveryTime()->getTimestamp())
//                            )
//                        )
//                    );
//                }
//            }
//        }
//
//
//        //lay danh sach user nhan lai tien chiet khau sau khi da kiem tra va chay lai
//        $query = \DebugRefundDiscount::select();
//        $query->addSelect("SUM(`money_refund`) as money");
//        $query->addSelect("user_id");
//        $query->groupBy("user_id");
//        $query->orderBy("money", 'DESC');
//        $list_data_refund = $query->execute();
//        $i = 0;
//
//        foreach ($list_data_refund as $refund) {
//            if ($refund->money > 0) {
//                $user = \Users::retrieveById($refund->user_id);
//                if ($user instanceof \Users) {
//                    if ($user->getUsername() != "duybao") {
//
//                        if (isset($list_user_report[$refund->user_id])) {
//
//                            $list_user_report[$refund->user_id]['refund_money'] += $refund->money;
//
//                            $list_user_report[$refund->user_id]['detail'][] = array(
//                                'money' => $refund->money,
//                                'note' => "Trả lại tiền thừa cho khách sau khi tính lại chiết khấu",
//                                'time' => date("d-m-Y h:i:s", time())
//                            );
//
//                        } else {
//                            $list_user_report[$refund->user_id] = array(
//                                'get_money' => 0,
//                                'refund_money' => $refund->money,
//                                'detail' => array(
//                                    array(
//                                        'money' => $refund->money,
//                                        'note' => "Trả lại tiền thừa cho khách sau khi tính lại chiết khấu",
//                                        'time' => date("d-m-Y h:i:s", time())
//                                    )
//                                )
//                            );
//                        }
//
//                    } else {
//                        //
//                    }
//
//                } else {
//                    var_dump("user not instane of User");
//                }
//            } else {
//                //money <=0
//            }
//
//        }
//
//
//        //refund money for customer
//
//        $table = '<p><table width="100%" cellspacing="0" cellpadding="5" border="1" style="border-collapse:collapse">
//                        <tr>
//                          <th >STT</th>
//                          <th >USERNAME</th>
//                          <th >EMAIL</th>
//                          <th >MÃ KHÁCH</th>
//                          <th >TIỀN TRUY THU</th>
//                        <th >MÃ GIAO DỊCH TRUY THU</th>
//
//                        </tr>';
//        $i=0;
//        foreach ($list_user_report as $user_id => $user_report) {
//            $i++;
//            $user = \Users::retrieveById($user_id);
//            //tong so tien truy thu - tong so tien refund
//            $total_money =   $user_report['get_money'] - $user_report['refund_money'];
//
//            Logger::factory('refund_money_after_discount')->debug("before balance:" . $user->getAccountBalance());
//
//            $refund_amount = round($total_money);
//            Logger::factory('refund_money_after_discount')->debug("get again money:" . $refund_amount);
//
//
//            $user->beginTransaction();
//            try {
//
//                $transfer = \SeuDo\Accountant\Util::charge($user, $refund_amount, json_encode(array(
//
//                    'type' => \UserTransaction::TRANSACTION_TYPE_CHARGE_FEE,
//                    'detail' => "Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật. Mọi thắc mắc Quý khách liên hệ DVKH để được giải đáp. Vô cùng xin lỗi quý khách."
//                )));
//
//                $message_charge = "Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật. Mọi thắc mắc Quý khách liên hệ DVKH để được giải đáp. Vô cùng xin lỗi quý khách.";
//
//                try {
//
//                    $balance = $transfer['from_account']['balance'];
//                    Logger::factory('refund_money_after_discount')->debug("after balance:" . $balance);
//
//                    \UsersPeer::changeAccountBalance($user, $balance);
//                    $username = $user->getUsername();
//                    $user_email = $user->getEmail();
//                    $user_code = $user->getCode();
//
//                    //save user transaction
//                    try {
//                        $accountantTransaction = $transfer['transfer_transaction'];
//                        $transaction_code = $accountantTransaction['uid'];
//
//                        $userTransaction = new \UserTransaction();
//                        $userTransaction->setUserId($user->getId());
//                        $userTransaction->setState(\UserTransaction::STATE_COMPLETED);
//                        $userTransaction->setTransactionCode($transaction_code);
//                        $userTransaction->setTransactionType(\UserTransaction::TRANSACTION_TYPE_CHARGE_FEE);
//                        $userTransaction->setAmount($accountantTransaction["amount"]);
//                        $userTransaction->setEndingBalance($balance);
//                        $userTransaction->setTransactionDetail($message_charge);
//                        $userTransaction->setTransactionNote($message_charge);
//                        $userTransaction->setCreatedTime(new \DateTime());
//                        if (is_array($accountantTransaction['modified_time'])) {
//                            $closedTime = new \DateTime($accountantTransaction['modified_time']['date']);
//                        } elseif (is_scalar($accountantTransaction['modified_time'])) {
//                            $closedTime = new \DateTime($accountantTransaction['modified_time']);
//                        } else {
//                            $closedTime = new \DateTime();
//                        }
//                        $userTransaction->setClosedTime($closedTime);
//
//                        if (!$userTransaction->save()) {
//                            throw new \RuntimeException('Could not save user transaction:' . $userTransaction->getValidationFailuresMessage("\n"));
//                        }
//                    } catch (\Exception $e) {
//                        throw $e;
//                    }
//
//                    $user->commit();
//
//                    $table .= '<tr>
//                                            <td>' . $i . '</td>
//                                            <td>' . $username . '</td>
//                                            <td>' . $user_email . '</td>
//                                             <td>' . $user_code . '</td>
//                                             <td>' . number_format($refund_amount) . ' VNĐ</td>
//                                             <td>' . $transaction_code . '</td>
//                                       </tr>';
//
//                    Logger::factory('refund_money_after_discount')->info('Refund old order after discount completed', array(
//
//                        'user' => $username,
//                        'email' => $user_email,
//                        'user_code' => $user_code,
//                        'refund money' => number_format($refund_amount),
//                        'transaction code' => $transaction_code,
//                        'accountant_transaction' => $userTransaction->getTransactionCode(),
//                        'user_transaction' => $userTransaction->getId()
//                    ));
//
//
//                } catch (\Exception $e) {
//                    //refund back
//                    $detail_refund = array(
//                        'type' => 'ROLLBACK',
//                        'message' => "Điều chỉnh số tiền giao dịch trả lại thừa tiền chiết khấu các đơn hàng cũ đủ điều kiện nâng hạng do lỗi kỹ thuật không thành công. Hoàn lại tiền cho khách hàng."
//                    );
//                     \SeuDo\Accountant\Util::refund($user, $refund_amount, json_encode($detail_refund), $detail_refund['message']);
//                    throw $e;
//                }
//            } catch (\Exception $e) { //accountant's transaction success need rollback and recharge
//                $user->rollBack();
//                Logger::factory('refund_money_after_discount')->error($e->getMessage() . ".\nTrances:\n" . $e->getTraceAsString());
//                continue;
//            }
//            Logger::factory('refund_money_after_discount')->debug("refund money success:" . $user->getUsername());
//        }//end for
//
//        $table .= '</table>';
//        $list_receiver_email = array('ha nguyen' => 'nguyenvietha@alimama.vn');
//        $data = array(
//            'email' => $list_receiver_email,
//            'subject' => 'Kết quả thống kê tiền truy thu tiền của khách hàng  ' . date('H:i d/m/Y', time()),
//            'body' => $table,
//
//        );
//
//        $result = EmailHelper::sendEmail($data);
//        var_dump("DONE TASK REFUND");
//
//
//    }
//
//
//}