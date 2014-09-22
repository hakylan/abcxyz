<?php
namespace Api\Controller\Logistic;

use Api\Controller\ApiBase;
use Flywheel\Controller\Api;
use Flywheel\Validator\Util;
use mongodb\BarcodeTracking;
use SeuDo\Logger;
use SeuDo\Queue;

class Barcode extends ApiBase
{
    public function beforeExecute()
    {
        //overwrite to remove verify request. Need remove it

        $this->getEventDispatcher()
            ->addListener('orderStockOut',
                array(new \ApiEvent(), 'logStockOrderStatus'));

        $this->getEventDispatcher()
            ->addListener('orderStockIn',
                array(new \ApiEvent(), 'logStockOrderStatus'));

        header("Access-Control-Allow-Origin: *");
    }

    public function getRequestOrder()
    {
        $barcode = strtoupper(trim($this->get('barcode')));
//        $freight = trim($this->post("freight"));
        try {
            $order_list = array();
            if (preg_match("/S\//", $barcode)) {
                $order = substr($barcode, 2, strlen($barcode));
                if (is_numeric($order)) {
                    $order = \Order::retrieveById($order);
                } else {
                    $order = \Order::retrieveByCode($order);
                }
                if ($order instanceof \Order) {
                    $order_list[$order->getId()] = (array)\SeuDo\Logistic\BarcodeTracking::getOrderInfo($order);
                }
            } else {
                $order = $barcode;
                if (is_numeric($order)) {
                    $order = \Order::retrieveById($order);
                } else {
                    $order = \Order::retrieveByCode($order);
                }
                if ($order instanceof \Order) {
                    $order_list[$order->getId()] = (array)\SeuDo\Logistic\BarcodeTracking::getOrderInfo($order);
                }
            }
            if ($barcode != "") {
                $packages = \Packages::findByFreightBill($barcode);
                if (!empty($packages)) {
                    foreach ($packages as $package) {
                        if ($package instanceof \Packages) {
                            $order = \Order::retrieveById($package->getOrderId());
                            if ($order instanceof \Order) {
                                $order_list[$order->getId()] = (array)\SeuDo\Logistic\BarcodeTracking::getOrderInfo($order);
                            }
                        }
                    }
                }
            }

            if (empty($order_list)) {
                $array = array("code" => 404,
                    "message" => "Not found with condition");
                return $this->sendResponse(404, $array);
            }

            $array = array("code" => 200,
                "data" => $order_list);

            return $this->sendResponse(200, $array);
        } catch (\Exception $e) {
            return $this->sendResponse(500, array(
                "message" => "Exception : " . $e->getMessage()
            ));
        }
    }


    /**
 * Receive post barcode from logistics and return anything related to it
 * @return array
 * @throws \RuntimeException
 */
    public function postTracking()
    {
        $barcode = strtoupper(trim($this->post('barcode')));
        $activity = strtoupper(trim($this->post('activity')));
        $division = strtoupper(trim($this->post('division')));
        $warehouse = strtoupper(trim($this->post('warehouse')));
        $action_time = $this->post('action_time');
        $barcode_type = strtoupper(trim($this->post('barcode_type')));
        $other_barcode_origin = $this->post('other_barcodes');
        if(is_string($other_barcode_origin)){
            $other_barcodes= json_decode($other_barcode_origin);
        }


        $order_id = intval($this->post('order_id'));

        //log input
        $logger = Logger::factory('logistic');

        $logger->info("BARCODE CALLBACK: {$barcode}/{$activity}/{$division}/{$warehouse}/{$action_time}/{$barcode_type}/{$other_barcode_origin}/{$order_id}");

        $error = array();

        if (!$action_time) {
            $action_time = new \DateTime();
        } else {
            if (!Util::validateDate($action_time, 'Y-m-d H:i:s')) {
                $error['action_time'] = 'Invalid action time format';
            } else {
                $action_time = \DateTime::createFromFormat('Y-m-d H:i:s', $action_time);
            }
        }

        if(!is_array($other_barcodes)){
            $error['other_barcode']="Invalid parameters: $other_barcodes is not array after json decode,$other_barcode_origin=".$other_barcode_origin;

        }


        if (count($error)> 0) {
            $result = new \stdClass();
            $result->code = 500;
            $result->message = "Invalid parameters";
            $result->hash = $error;
            return json_encode($result);

        }

        $odm = new BarcodeTracking();
        $odm->barcode = $barcode;
        $odm->activity = $activity;
        $odm->division = $division;
        $odm->warehouse = $warehouse;
        $odm->action_time = new \MongoDate($action_time->format('U'));
        $odm->barcode_type = $barcode_type;
        $odm->other_barcodes = $other_barcodes;
        $odm->order_id = $order_id;
        if ($odm->save()) {
            $logger->info('Save new barcode tracking. ID:' . $odm->getId());
            /*$queue = Queue::factory('barcode_analysis');
            $queue->push(json_encode(array(
                'barcode' => $barcode,
                'activity' => $activity,
                'division' => $division,
                'action_time' => $action_time->format('Y-m-d H:i:s')
            )));*/
            $problem = array();
            $result = (array)\SeuDo\Logistic\BarcodeTracking::analysisBarcode($problem,$barcode_type,$other_barcodes,$order_id, $barcode, $activity, $division, $warehouse, $action_time);

            foreach ($result as $k => $v) {
                $odm->$k = $v;
            }

            foreach ($problem as $k => $v) {
                $odm->$k = $v;
            }

            $odm->save();
            //$result = array_merge($odm->toArray(), array('action_time' => $action_time->format('Y-m-d H:i:s')));
            $logger->info("BARCODE RESPONSE", $result);
            return json_encode($result);
        } else {
            $result = new \stdClass();
            $result->code = 500;
            $result->message = "Sorry, we could not save barcode tracking.";
            return json_encode($result);
            throw new \RuntimeException("Could not save barcode tracking.");
        }
    }

    /**
     * Receive post package from logistics and return anything related to it
     * @return array
     * @throws \RuntimeException
     */
    public function postUpdatePackage()
    {

        $package_code = strtoupper(trim($this->post('package_code')));
        $weight = $this->post('weight','FLOAT',0);
        $packing_level = strtoupper(trim($this->post('packing_level')));
        $freight_barcode = strtoupper(trim($this->post('freight_barcode')));
        $order_id = $this->post('order_id','INT',0);
        $order_code = strtoupper(trim($this->post('order_code')));
        $current_warehouse = $this->post('current_warehouse');
        $division = $this->post('division');
        $modifiedTime = $this->post('modifiedTime');
        $warehouse_status = $this->post('warehouse_status');
        $status_time = $this->post('status_time');

        //log input
        $logger = Logger::factory('logistic');

        $logger->info("Package info update: {$package_code}/{$weight}/{$packing_level}/{$freight_barcode}/{$order_id}/{$order_code}/{$current_warehouse}/{$division}/{$modifiedTime}/{$warehouse_status}/{$status_time}");

        $error = array();

        if (!Util::validateDate($status_time, 'Y-m-d H:i:s')) {
            $error['status_time'] = 'Invalid $status_time format';
        }

        if (!Util::validateDate($modifiedTime, 'Y-m-d H:i:s')) {
            $error['modifiedTime'] = 'Invalid $modifiedTime format';
        }

        if (!empty($error)) {
            $result = new \stdClass();
            $result->code = 400;
            $result->message = "Invalid parameters.";
            $result->error = $error;
            return json_encode($result);

        }

        $result = (array)\SeuDo\Logistic\Package::updatePackageInfo($package_code,$weight,$packing_level,
            $freight_barcode,$order_id,$order_code,$current_warehouse,$division,$modifiedTime,$warehouse_status,$status_time);

        $logger->info("Package RESPONSE", array(json_encode($result)));
        return json_encode($result);
    }
} 