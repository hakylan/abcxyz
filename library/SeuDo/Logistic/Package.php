<?php
namespace SeuDo\Logistic;

use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use Home\Controller\Service;
use SeuDo\Logger;
use SeuDo\Main;

class Package
{
    public static function updatePackageInfo($package_code, $weight, $packing_level,
                                             $freight_barcode, $order_id, $order_code, $current_warehouse, $division, $modifiedTime, $warehouse_status, $status_time)
    {
        //check code first
        $result = new \stdClass();

        $result->code = 200;
        $result->data = array();
        $order = false;
        $multil_order = false;
        if ($freight_barcode != '') {
            if ($order_id > 0) {
                //tim don hang theo id
                $order = \Order::retrieveById($order_id);
            } elseif ($freight_barcode != '') {
                $orders = \Packages::getOrdersByFreightBill($freight_barcode);
                if (sizeof($orders) == 1) {
                    $order = $orders[0];
                } elseif (sizeof($orders) > 1) {
                    $multil_order = $orders;
                }
            }

            $package = '';
            //tim thay 1 don hang thi update don hang day
            if ($order instanceof \Order) {
                //khop kien hang theo order id an freight_bill
                $package = \Packages::retrieveByOrderIdAndFreightBill($order->getId(), $freight_barcode);

                if ($package instanceof \Packages) {

                    //update package
                    $logger = Logger::factory('logistic');
                    try {
                        $time_update = strtotime($package->getPackageModifiedTime());
                        $time_new_update = strtotime($modifiedTime);
                        if ($time_new_update > $time_update) {
                            if ($package->getPackageCode() == '') {
                                $code = \Packages::createPackageCode($order);
                                if ($code) {
                                    $package->setPackageCode($code);
                                    //cap nhat so luong kien hang thuc te nhan duoc cua don hang
                                    $old_packages_quantity = $order->getPackagesQuantity();
                                    $order->setPackagesQuantity($old_packages_quantity + 1);
                                    $order->save();
                                }
                            }

                            if ($package_code != '') {
                                $package->setLogisticPackageBarcode($package_code);
                            }

                            $package->setDistributionWarehouse($order->getDestinationWarehouse());
                            $package->setLevel($packing_level);
                            $package->setCurrentWarehouse($current_warehouse);
                            $package->setPackageModifiedTime(date('Y-m-d H:i:s', $time_new_update));
                            $package->setWarehouseStatus($warehouse_status);
                            if ($warehouse_status == 'IN') {
                                $package->setWarehouseStatusInTime(date('Y-m-d H:i:s', strtotime($status_time)));
                            } elseif ($warehouse_status == 'OUT') {
                                $package->setWarehouseStatusOutTime(date('Y-m-d H:i:s', strtotime($status_time)));
                            }
                            $package->save();

                        } else {
                            $result->code = 200;
                            $result->message = 'Thông tin kiện là cũ hơn so với hiện tại nên không update';
                            return $result;
                        }


                    } catch (\Exception $e) {
                        $logger->addDebug('update Package has exception:' . $e->getMessage());
                        return false;
                    }

                } else {
                    //tao moi kien hang
                    $package = new \Packages();
                    $code = \Packages::createPackageCode($order);
                    if ($code) {
                        $package->setPackageCode($code);
                        //cap nhat so luong kien hang thuc te nhan duoc cua don hang
                        $old_packages_quantity = $order->getPackagesQuantity();
                        $order->setPackagesQuantity($old_packages_quantity + 1);
                        $order->save();
                    }

                    if ($package_code != '') {
                        $package->setLogisticPackageBarcode($package_code);
                    }
                    $package->setOrderId($order->getId());
                    $package->setFreightBill($freight_barcode);
                    $package->setCreatedTime(date('Y-m-d H:i:s', time()));
                    $package->setDistributionWarehouse($order->getDestinationWarehouse());
                    $package->setLevel($packing_level);
                    $package->setCurrentWarehouse($current_warehouse);
                    $package->setPackageModifiedTime(date('Y-m-d H:i:s', strtotime($modifiedTime)));
                    $package->setWarehouseStatus($warehouse_status);
                    if ($warehouse_status == 'IN') {
                        $package->setWarehouseStatusInTime(date('Y-m-d H:i:s', strtotime($status_time)));
                    } elseif ($warehouse_status == 'OUT') {
                        $package->setWarehouseStatusOutTime(date('Y-m-d H:i:s', strtotime($status_time)));
                    }

                    $package->save();


                }

                if ($package instanceof \Packages) {

                    //update weight package and order
                    try {
//                        $package->updateWeight($weight, $order->getId());
                    } catch (\Flywheel\Exception $e) {
                        \SeuDo\Logger::factory('update_package_weight')->addError('has error when try to update package weight', array($e->getMessage()));
                        $result->code = 500;
                        $result->message = 'Has error when try to update package weight';
                        return $result;
                    }
                    //update status package
                    $package_status_mapping = \SeuDo\Logistic\BarcodeTracking::getAnalysisStatusOfPackage($warehouse_status, $division, $current_warehouse, $package, $order);
                    if ($package_status_mapping != null) {
                        try {
                            $package->changeStatus($package_status_mapping);
                        } catch (\Flywheel\Exception $e) {
                            \SeuDo\Logger::factory('update_package_weight')->addError('has error when try to change status package', array($e->getMessage()));
                            $result->code = 500;
                            $result->message = 'Has error when try to change status package';
                            return $result;
                        }

                    }
                    //cap nhat trang thai don hang: voi truong hop kien hang khong quet vao kho CNGZ, chi quet nhap vao bao
                    if ($warehouse_status == 'IN' && $current_warehouse=\Order::WAREHOUSE_CNGZ) {
                        //check update don hang theo kien hang co trang thai moi nhat cua no
                        $has_update_order = $package->checkUpdateOrderByPackage($order->getId());
                        if ($has_update_order) {
                            $problem='';
                            \SeuDo\Logistic\BarcodeTracking::updateOrder($problem,$order,$warehouse_status,$division,$current_warehouse);

                        }
                    }

                }

                $result->code = 200;
                $result->message = 'Success';
                return $result;


            } elseif ($multil_order) {
                //tim thay nhieu don hang, do nothing - tra ve danh sach don hang cho logistic
                $result->code = 200;
                $result->message = "Tìm thấy nhiều đơn hàng ứng với mã vận đơn.";
                return $result;

            } else {
                $result->code = 404;
                $result->message = 'Not found order';
                return $result;
            }
        } else {
            $result->code = 500;
            $result->message = 'Freight bracode is null';
            return $result;
        }

    }


}
