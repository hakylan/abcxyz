<?php
namespace SeuDo\Logistic;

use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use Home\Controller\Service;
use SeuDo\Logger;
use SeuDo\Main;

class BarcodeTracking
{
    const ACT_IN = 'IN',
        ACT_OUT = 'OUT',
        ACT_INVENTORY = 'INVENTORY';

    /**
     * @param $barcode
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param null $working_time
     * @return \stdClass
     */
    public static function analysisBarcode(&$problem, $barcode_type, $other_barcodes, $order_id, $barcode, $activity, $division, $warehouse, $working_time = null)
    {

        //check code first
        $result = new \stdClass();

        $result->code = 200;
        $result->data = array();
        $order = false;
        $multil_order = false;

        if ($order_id > 0) {
            //tim don hang theo id
            $order = \Order::retrieveById($order_id);
        } else {
            if ($barcode_type == "PACKAGE_BARCODE") {
                //tim don hang theo ma dich vu, ma van don
                if (is_array($other_barcodes) && count($other_barcodes) > 0) {
                    foreach ($other_barcodes as $item) {
                        $item = strtoupper($item);
                        $code = substr($barcode, 2);
                        if (substr($barcode, 2) == 'S/') {
                            $order = \Order::retrieveById($code);
                            if ($order) {
                                break;
                            }
                        } else {
                            $orders = \Packages::getOrdersByFreightBill($item);
                            if (sizeof($orders) == 1) {
                                $order = $orders[0];
                                break;
                            } elseif (sizeof($orders) > 1) {
                                $multil_order = $orders;

                            }
                        }
                    }
                }
            } elseif ($barcode_type == "SERVICE_BARCODE") {
                $code = substr($barcode, 2);
                $order = \Order::retrieveById($code);
            } elseif ($barcode_type == 'FREIGHT_BARCODE') {
                //return self::updateFreightBill($barcode, $activity, $division, $warehouse, $working_time);
                $orders = \Packages::getOrdersByFreightBill($barcode);
                if (sizeof($orders) == 1) {
                    $order = $orders[0];
                } elseif (sizeof($orders) > 1) {
                    $multil_order = $orders;
                }
            }
        }

        $package = '';
        //tim thay 1 don hang thi update don hang day
        if ($order instanceof \Order) {
            //khop kien hang theo order id an freight_bill
            if ($barcode_type == 'FREIGHT_BARCODE') {
                $package = \Packages::retrieveByOrderIdAndFreightBill($order->getId(), $barcode);

            } else {
                if (is_array($other_barcodes) && count($other_barcodes) > 0) {
                    foreach ($other_barcodes as $item) {
                        $package = \Packages::retrieveByOrderIdAndFreightBill($order->getId(), $item);
                        if ($package instanceof \Packages) {
                            break;
                        }
                    }
                }
            }

            if ($package instanceof \Packages) {
                //update package
                $result_update_package = self::updatePackage($package, $activity, $division, $warehouse,$order);
                $has_update_order = $package->checkUpdateOrderByPackage($order->getId());

                if ($result_update_package && $has_update_order) {
                    //update thong tin don hang
                    try{
                        $order_data = self::updateOrder($problem, $order, $activity, $division, $warehouse, $working_time);
                    }catch (\Exception $e) {
                        $result->code = 500;
                        $result->message = $e->getMessage();
                        return $result;

                    }
                }


            } else {
                //update thong tin don hang voi truong hop barcode la ma dich vu, khong tim thay kien
                try{
                    $order_data = self::updateOrder($problem, $order, $activity, $division, $warehouse, $working_time);
                }catch (\Exception $e) {
                    $result->code = 500;
                    $result->message = $e->getMessage();
                    return $result;

                }
            }

            if (!empty($order_data) && empty($order_data->message)) // have data but no error
            {
                $result->code = 200;
                $result->data[] = $order_data;
                return $result;
            } else if (!empty($order_data->message)) {
                $result->code = $order_data->code;
                $result->message = $order_data->message;
                return $result;
            } else {
                $result->code = 500;
                $result->message = 'Unknown error';
                return $result;
            }
        } elseif ($multil_order) {
            //tim thay nhieu don hang, do nothing - tra ve danh sach don hang cho logistic
            $result->code = 200;
            foreach ($multil_order as $order) {
                $result->data[] = self::getOrderInfo($order);
            }
            return $result;

        } else {
            $result->code = 404;
            $result->message = 'Not found order';
            return $result;
        }


    }

    /**
     * @param $package
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param $working_time
     */
    public static function updatePackage($package, $activity, $division, $warehouse, $order)
    {

        $logger = Logger::factory('logistic');


        if (!($package instanceof \Packages)) {
            $logger->addDebug('"$package" parameter must be a instance of \Packages class');
            return false;
        }
        try {
            if ($warehouse != $package->getCurrentWarehouse() && $activity == self::ACT_IN) {
                $package->setCurrentWarehouse($warehouse);
                $package->setWarehouseStatus(\Packages::WAREHOUSE_STATUS_IN);
                $package->setWarehouseStatusInTime(new DateTime());
                $package->save(false); // quick save
            }

            if ($activity == self::ACT_OUT) {
                $package->setCurrentWarehouse($warehouse);
                $package->setWarehouseStatus(\Packages::WAREHOUSE_STATUS_OUT);
                $package->setWarehouseStatusOutTime(new DateTime());
                $package->save(false); // quick save
            }

            $package_status_mapping = self::getAnalysisStatusOfPackage($activity, $division, $warehouse, $package,$order);
            if ($package_status_mapping != null) {
                $result_update_status = $package->changeStatus($package_status_mapping);

            }


            return true;
        } catch (\Exception $e) {
            $logger->addDebug('update Package has exception:' . $e->getMessage());
            return false;
        }

    }

    /**
     * Update order status, and return data as Logistics System API required
     * @param $order
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param null $working_time
     * @return \stdClass
     * @throws \RuntimeException
     */
    public static function updateOrder(&$problem, $order, $activity, $division, $warehouse, $working_time = null)
    {
        $working_time = self::_manipulateWorkingTime($working_time);
        $logger = Logger::factory('logistic');
        if (is_int($order)) {
            $order = \Order::retrieveById($order);
        } else if (is_string($order)) {
            $order = \Order::retrieveByCode($order);
        }

        if (!($order instanceof \Order)) {
            throw new \RuntimeException('"$order" parameter must be a instance of \Order class');
        }

        if ($warehouse != $order->getCurrentWarehouse() && $activity == self::ACT_IN) {
            //$result->part_warehouse = $order->getCurrentWarehouse(); // do not need this anymore
            //$result->warehouse = $warehouse; // do not need this anymore
            try{
                $order->setCurrentWarehouse($warehouse);
                $order->setWarehouseStatus(\Order::WAREHOUSE_STATUS_IN);
                $order->setWarehouseInTime(new DateTime());
                $order->setCurrentWarehouseTime(new DateTime());
                $check_save = $order->save(false); // quick save
                if($check_save){
                    $order->dispatch('orderStockIn', new Event(null, array(
                        'order' => $order,
                        'warehouse' => $warehouse
                    )));
                }
            }catch (\Exception $e) {
                $problem['exception']= $e->getMessage();
                throw $e;
            }
        }

        if ($activity == self::ACT_OUT) {
            try{
                $order->setCurrentWarehouse($warehouse);
                $order->setWarehouseStatus(\Order::WAREHOUSE_STATUS_OUT);
                $order->setWarehouseOutTime(new DateTime());
                $order->save(false); //quick save
                $check_save = $order->save(false); // quick save
                if($check_save){
                    $order->dispatch('orderStockOut', new Event(null, array(
                        'order' => $order,
                        'warehouse' => $warehouse
                    )));
                }
            }catch (\Exception $e) {
                $problem['exception']= $e->getMessage();
                throw $e;
            }
        }


        // check current status on seudo.vn to check if there is any problem with the warehouse status

        $order_status_mapping = self::getAnalysisStatus($activity, $division, $warehouse, $order, $problem);
        if (!$order_status_mapping || $order->getStatus() == $order_status_mapping) { //everything ok check other information
            if ($order->getCurrentWarehouse() != $warehouse) { //problem
                //commented by Kien Nguyen: we do not return problem to logistics anymore
                $problem['warehouse'] = 'Not match current warehouse';
            }
        } else if ($order_status_mapping) {
            if ($order->getIsDeleted() == 1 || $order->getStatus() == \Order::STATUS_OUT_OF_STOCK) {
                //commented by Kien Nguyen: we do not return problem to logistics anymore
                $problem['order'] = 'Order have deleted or out of stock';
            } else {
                try{
                    $result_change_status = $order->changeStatus($order_status_mapping);
                    if($result_change_status){
                        //dispatch Event - hanv
                        $order->dispatch(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND, new Event(null, array(
                            'order' => $order
                        )));
                    }
                }catch (\Exception $e) {
                    $problem['exception']= $e->getMessage();
                    throw $e;
                }

                /* @FUCK U
                 * nguyen thanh trung, I want to save both warehouse and status
                 * how could I do that
                 */
            }
            //commented by Kien Nguyen: we do not return new status to logistics, its system do not care about this
            //$result->new_status = $order->getStatus();
        }

        $result = self::getOrderInfo($order);
        return $result;
    }

    /**
     * Get order info, then return as array ready to be convert to Json to return as API response, return false if fail
     * @param $order \Order
     * @return \stdClass
     */
    public static function getOrderInfo($order)
    {
        $logger = Logger::factory('logistic');
        $result = new \stdClass();
        $result->buyer = '';
        $result->receiver = '';
        $order_info = new \stdClass();
        $order_info->order_id = $order->getId();
        $order_info->order_code = $order->getCode();

        // get buyer info
        $buyer = new \stdClass();
        $buyer_data = $order->getBuyer();
        if ($buyer_data instanceof \Users) {
            $buyer->username = $buyer_data->getUsername();
            $buyer->fullname = $buyer_data->getFullName();
            $phones = array();
            $mobiles = $buyer_data->getMobiles();
            foreach ($mobiles as $mobile) {
                $phones[] = $mobile->getMobile();
            }
            $buyer->phone = implode(',', $phones);
            $buyer->email = $buyer_data->getEmail();
            $buyer->customer_code = $buyer_data->getCode();

            $result->buyer = $buyer;
        } else {
            $result->code = 404;
            $result->message = 'Buyer not found';
            $logger->err('COULD NOT RETRIEVE BUYER OF ORDER ID ' . $order->getId());
            return $result;
        }

        // get receiver info
        $address = $order->getAddress();
        if ($address instanceof \UserAddress) {
            $receiver = new \stdClass();
            $receiver->receiver_name = $address->getReciverName();
            $receiver->phone = $address->getReciverPhone();
            $receiver_id = $address->getUserId();
            $receiver->note = $address->getNote();
            if ($receiver_id == $buyer_data->getId()) {
                $receiver->email = $buyer_data->getEmail();
            } else {
                $receiver_data = \Users::retrieveById($receiver_id);
                if ($receiver_data instanceof \Users) {
                    $receiver->email = $receiver_data->getEmail();
                } else {
                    $receiver->email = $buyer_data->getEmail();
                }
            }

            $district = $address->getDistrict();
            $province = $address->getProvince();
            $address_data = new \stdClass();
            $address_data->detail = $address->getDetail();
            $address_data->district_code = $district->getLogisticCode();
            $address_data->state_code = $province->getLogisticCode();

            //$receiver->address = $address_data;
            //$receiver->address = $address->getDetail();

            $receiver->address = $address_data;
            $receiver->distribution_warehouse = $order->getDestinationWarehouse();
            if (!$receiver->distribution_warehouse) {
                $logger->err('COULD NOT RETRIEVE DISTRIBUTION WAREHOUSE OF ORDER ID ' . $order->getId());
                $result->code = 404;
                $result->message = 'Distribution warehouse not found';
                return $result;
            }
        } else {
            // this case rarely happens, when user do not have address associated with him
            // then we just use old type of data (thus could not provide province and district
            $receiver = new \stdClass();
            $receiver->receiver_name = $buyer_data->getFullName();

            $phones = array();
            $mobiles = $buyer_data->getMobiles();
            foreach ($mobiles as $mobile) {
                $phones[] = $mobile->getMobile();
            }
            $buyer->phone = implode(',', $phones);

            $receiver->email = $buyer_data->getEmail();

            $address = new \stdClass();
            // we do not have detail address here!
            //$address->detail = $buyer_data->getDetailAddress();
            //$receiver->address = $address;

            $receiver->address = $buyer_data->getDetailAddress();


            $receiver->distribution_warehouse = $order->getDestinationWarehouse();
            if (!$receiver->distribution_warehouse) {
                $logger->err('COULD NOT RETRIEVE DISTRIBUTION WAREHOUSE OF ORDER ID ' . $order->getId());
                $result->code = 404;
                $result->message = 'Distribution warehouse not found';
                return $result;
            }
        }

        $result->receiver = $receiver;

        $order_info->seller_name = $order->getSellerName();
        $order_info->homeland = strtolower($order->getSellerHomeland());
        $order_info->homeland_url = \Common::getHomelandUrl($order_info->homeland);
        $order_info->receiver_name_on_bill = $order->getNameRecipientOrigin();
        $order_info->service = 'seudo';
        $order_info->service_url = Main::getHomeUrl();
        $order_info->service_barcode = 'S/' . $order->getId();
        $packages = \Packages::retrieveByOrderId($order->getId());

        $order_info->freight_barcode = array();
        foreach ($packages as $package) {
            if ($package instanceof \Packages) {
                $order_info->freight_barcode[] = $package->getFreightBill();
            }
        }

        $service_request = new \stdClass();

        $services = \OrderService::findByOrderId($order->getId());

        $express = false;
        $wood_crating = false;
        $fragile = false;
        $avoid_water = false;
        $electron = false;
        $mechanic = false;
        $high_value = false;
        $checking = false;
        $vip = false;

        //$fragile = count($services);
        foreach ($services as $service) {

            if ($service instanceof \OrderService) {
                $code = $service->getServiceCode();
                //$wood_crating = $code;
                switch ($code) {
                    case \Services::TYPE_WOOD_CRATING:
                        $wood_crating = true;
                        break;
                    case \Services::TYPE_FRAGILE:
                        $fragile = true;
                        break;
                    case \Services::TYPE_HIGH_VALUE:
                        $high_value = true;
                        break;
                    case \Services::TYPE_EXPRESS_CHINA_VIETNAM:
                        $express = true;
                        break;
                    case \Services::TYPE_CHECKING:
                        $checking = true;
                        break;
                    default:
                        break;
                }
            }
        }

        $service_request->transport_method = new \stdClass();
        $service_request->transport_method->EXPRESS = $express;
        $service_request->transport_method->STANDARD_DELIVERY = !$express;

        $service_request->extra_service = new \stdClass();
        $service_request->extra_service->WOODEN_CRATING = $wood_crating;
        $service_request->extra_service->FRAGILE = $fragile;
        $service_request->extra_service->AVOID_WATER = $avoid_water;
        $service_request->extra_service->ELECTRON = $electron;
        $service_request->extra_service->MECHANIC = $mechanic;
        $service_request->extra_service->EXPENSIVENESS = $high_value;
        $service_request->extra_service->CHECKING = $checking;

        $service_request->private_service = new \stdClass();
        $service_request->private_service->VIP = $vip;

        $order_info->service_request = $service_request;

        $result->order_information = $order_info;
        return $result;
    }

    /**
     * From freight bill, return array of "order" as stdClass to be converted to Json
     * @param $bill
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param null $working_time
     * @return \stdClass
     */
    public static function updateFreightBill($bill, $activity, $division, $warehouse, $working_time = null)
    {
        $working_time = self::_manipulateWorkingTime($working_time);

        $orders = \Packages::getOrdersByFreightBill($bill);

        $result = new \stdClass();

        $result->code = 200;
        $result->data = array();
        $result->message = "OK";

        if (!$orders) {
            $result->code = 404;
            $result->message = 'Do not match any order';
            return $result;
        }

        if (sizeof($orders) == 1) {
            self::analyseOrder($orders[0], $activity, $division, $warehouse, $working_time, $result);
            return $result;
        }

        foreach ($orders as $order) {
            $order_data = self::updateOrder($order, $activity, $division, $warehouse, $working_time);
            if (!empty($order_data) && empty($order_data->message)) // have data but no error
            {
                $result->code = 200;
                $result->data[] = $order_data;
            } else if (!empty($order_data->message)) {
                $result->code = $order_data->code;
                $result->message = $order_data->message;
            } else {
                $result->code = 500;
                $result->message = 'Unknown error';
            }
        }

        return $result;
    }

    /**
     * @param $working_time
     * @return \DateTime
     */
    protected static function _manipulateWorkingTime($working_time)
    {
        if (!$working_time) {
            return new \DateTime();
        } else if ($working_time instanceof \DateTime) {
            return $working_time;
        } else {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $working_time);
        }
    }

    /**
     * Analyse 1 order, return as API ready response
     * @param $order
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param $working_time
     * @param $result /stdClass
     * @return /stdClass
     */
    protected static function analyseOrder($order, $activity, $division, $warehouse, $working_time, &$result)
    {
        $order_data = self::updateOrder($order, $activity, $division, $warehouse, $working_time);
        if (!empty($order_data) && empty($order_data->message)) // have data but no error
        {
            $result->code = 200;
            $result->data[] = $order_data;
            return $result;
        } else if (!empty($order_data->message)) {
            $result->code = $order_data->code;
            $result->message = $order_data->message;
            return $result;
        } else {
            $result->code = 500;
            $result->message = 'Unknown error';
            return $result;
        }
    }

    /**
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param \Order $order
     * @param \stdClass $result
     * @return null|string
     */
    public static function getAnalysisStatus($activity, $division, $warehouse, \Order $order, &$problem)
    {
        if ($order->isBeforeStatus(\Order::STATUS_BOUGHT)) {
            $problem['status'] = 'Current order status is ' . $order->getStatus() . '. Something went wrong!';
        }

        $checked_timestamp = \Common::getTimeStamp($order->getCheckedTime());

        if ($activity == self::ACT_IN) {
            if ($order->getDestinationWarehouse() == $warehouse) { //the last distribution warehouse
                if ($order->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)) {
                    if (($order->needToChecking() && $order->getCheckingStatus() == \Order::CHECKING_STATUS_CHECKED)
                        || !$order->needToChecking()
                    ) {
                        return \Order::STATUS_WAITING_FOR_DELIVERY;
                    } else if ($order->needToChecking() && $order->getCheckingStatus() == \Order::CHECKING_STATUS_NOT_YET_CHECKED
                        && $checked_timestamp <= 0
                    ) {
                        $problem['status'] = 'Stock in distribution warehouse, using Checking service but wrong status. ' . $order->getStatus();
                        return null;
                    }
                    return \Order::STATUS_WAITING_FOR_DELIVERY;
                } else {
                    $problem['status'] = 'Stock in distribution warehouse but wrong status. ' . $order->getStatus();
                    return null;
                }
            }

            if ($order->isBeforeStatus(\Order::STATUS_RECEIVED_FROM_SELLER)) {
                return \Order::STATUS_RECEIVED_FROM_SELLER;
            }
        }

        if ($activity == self::ACT_OUT) {
            if ($order->getDestinationWarehouse() == $warehouse) {
                if ($order->isBeforeStatus(\Order::STATUS_CUSTOMER_CONFIRM_DELIVERY)) {
                    $problem['status'] = 'Stock out distribution warehouse but wrong status. ' . $order->getStatus();
                    return null;
                }
                /**
                 * @Todo Khong chuyen trang thai don hang nua - Quyền
                 */
                return null;

//                return \Order::STATUS_DELIVERING;
                // Xuất kho CNGZ sẽ chuyển trạng thái đơn sang vận chuyển
            } elseif ($warehouse == \Order::WAREHOUSE_CNGZ && $order->isBeforeStatus(\Order::STATUS_WAITING_FOR_DELIVERY)) {
                return \Order::STATUS_TRANSPORTING;
            }

            return null;
        }

        if ($activity == self::ACT_INVENTORY) {
            if ($order->getCurrentWarehouse() != $warehouse) {
                $problem['warehouse_inventory'] = 'Wrong warehouse. Current order warehouse ' . $order->getCurrentWarehouse() . ' not match with ' . $warehouse;
            }
        }

        return null;
    }

    /**get status of package
     * @param $activity
     * @param $division
     * @param $warehouse
     * @param \Order $order
     * @param \stdClass $result
     * @return null|string
     */
    public static function getAnalysisStatusOfPackage($activity, $division, $warehouse, \Packages $packages, \Order $order)
    {
        $checked_timestamp = \Common::getTimeStamp($order->getCheckedTime());
        if ($activity == self::ACT_IN) {
            if ($packages->getDistributionWarehouse() == $warehouse && $packages->isBeforeStatus(\Packages::STATUS_WAITING_FOR_DELIVERY)) {
                //the last distribution warehouse
//                 if (($order->needToChecking() && $order->getCheckingStatus() == \Order::CHECKING_STATUS_CHECKED)
//                    || !$order->needToChecking()
//                ) {
//                    return \Packages::STATUS_WAITING_FOR_DELIVERY;
//                } else if ($order->needToChecking() && $order->getCheckingStatus() == \Order::CHECKING_STATUS_NOT_YET_CHECKED
//                    && $checked_timestamp <= 0
//                ) {
//
//                    return null;
//                }
                return \Packages::STATUS_WAITING_FOR_DELIVERY;

            } elseif ($warehouse == \Packages::WAREHOUSE_CNGZ && $packages->isBeforeStatus(\Packages::STATUS_RECEIVED_FROM_SELLER)) {
                return \Packages::STATUS_RECEIVED_FROM_SELLER;
            }
        } elseif ($activity == self::ACT_OUT) {
            if ($packages->getDistributionWarehouse() == $warehouse && $packages->isBeforeStatus(\Packages::STATUS_DELIVERING)) {
                //return \Packages::STATUS_DELIVERING;
                //khong thay doi trang thai
                return null;
            } elseif ($warehouse == \Packages::WAREHOUSE_CNGZ && $packages->isBeforeStatus(\Packages::STATUS_TRANSPORTING)) {
                // Xuất kho CNGZ sẽ chuyển trạng thái kien sang vận chuyển
                return \Packages::STATUS_TRANSPORTING;
            }
        }

        return null;
    }
}