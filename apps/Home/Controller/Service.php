<?php
namespace Home\Controller;

use Flywheel\Redis\Client;
use Flywheel\Session;
use SeuDo\Main;

class Service extends HomeBase
{

    public $auth;
    /** @var \Users login user */
    public $user;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->auth = \HomeAuth::getInstance();
        $this->user = $this->auth->getUser();
    }

    public function executeDefault() {}
    public function executeCalc() {
        $this->validAjaxRequest();

        $data = $this->request()->post('data', 'ARRAY');
        $is_choose_service = $this->request()->post('is_choose_service');

        $totalAmount = $data['totalAmount'];
        $totalAmount = strtolower($totalAmount);
        // init variable
        $buying = $checking = $packing = $shipping = $totalNormalItem = $totalAccessItem = 0;

        if(isset($data['normalItemCount'])) {
            $totalNormalItem = $data['normalItemCount'];
        }
        if(isset($data['accessItemCount'])) {
            $totalAccessItem = $data['accessItemCount'];
        }
        $totalItem = $totalNormalItem+$totalAccessItem;

        $totalWeight = 0;
        if(isset($data['totalWeight'])){
            $totalWeight = $data['totalWeight'];
            if(strpos($totalWeight,',') !==false && strpos($totalWeight,'.') !==false){
                $totalWeight = str_replace('.','',$totalWeight);
            }
            if(strpos($totalWeight, ',') !==false) {
                $totalWeight = str_replace(',','.',$totalWeight);
            }
            $totalWeight = floatval($totalWeight);
        }
        $target = '01';
        if(isset($data['targetCode'])){
            $target = $data['targetCode'];
        }
        
        if($this->user instanceof \Users && $is_choose_service){
            $level_obj = \LevelSetting::retrieveById($this->user->getLevelId());

            if(!$level_obj instanceof \LevelSetting){
                $level_obj = \LevelSetting::retrieveById(1);
            }
        }else{
            $level = 0;
            if(isset($data['userRank'])) {
                $userRank = $data['userRank'];
                $level = \UserRank::$userLevel[$userRank];
            }

            $level_obj = \LevelSetting::getLevelObjByLevel($level);
        }

        //Tính phí mua hàng
        if($totalAmount != ''){

            $money = str_replace(array(',','.'),'',$totalAmount);

            preg_match('/vnd|ndt/i', $totalAmount, $match);
            if(!isset($match[0])) {
                $sym = 'ndt';
            }else{
                $sym = strtolower($match[0]);
            }

            if($sym == 'ndt') {
                $cny = str_replace('ndt','',$money);
                $cny = trim($cny);
                $money = $cny*\ExchangeRate::getExchange();
            } else if ( $sym == 'vnd' ){
                $vnd = str_replace('vnd','',$money);
                $vnd = trim($vnd);
                $money = $vnd;
            } else {
                preg_match('/\d+((.|,)\d+)?/',$money,$match);
                $money = $match[0]*\ExchangeRate::getExchange();
            }

            if(intval($money) > 0 || floatval($money)) {
                $buying =  \ServiceBuying::getTotalFee($money, $level_obj->getId());
            }
        }


        if(isset($data['services']) && !empty($data['services']) && $totalItem>0){
            $services = $data['services'];
            //Tính phí kiểm hàng
            if(in_array(\Services::TYPE_CHECKING,$services)){
                $checking =\ServiceChecking::getTotalFee($totalItem, $totalNormalItem, $totalAccessItem,"",$level_obj->getId());
            }

            //Tính phí đóng gỗ
            if(in_array(\Services::TYPE_PACKING, $services)){
                $packing = \ServicePacking::getTotalFee($totalWeight,$level_obj->getId());
            }
        }
        //phí vận chuyển nội địa Trung Quốc
        $shipInChina = \ServiceShipping::getInChinaFee($totalWeight);

        //Phí vận chuyển Việt Nam, Trung Quốc
        $shipChinaVietnam = \ServiceShipping::getChinaVietnamFee($totalWeight, $target,"",$level_obj->getId());

        $shipInChinaFrom = $shipInChinaTo = 0;
        if(isset($shipInChina['from'])) {
            $shipInChinaFrom = $shipInChina['from'];
        }
        if(isset($shipInChina['to'])) {
            $shipInChinaTo = $shipInChina['to'];
        }

        //Chuẩn bị dữ liệu cho việc tính phí
        $buyingFeeOrigin = $buyingFeeDiscount = 0;
        if(isset($buying)) {
            if(isset($buying['fee_origin'])) $buyingFeeOrigin = $buying['fee_origin'];
            if(isset($buying['fee_discount'])) $buyingFeeDiscount = $buying['fee_discount'];
        }
        $shipChinaVietnamFeeOrigin = $shipChinaVietnamDiscount = 0;
        if(isset($shipChinaVietnam)) {
            if(isset($shipChinaVietnam['fee_origin']))
            $shipChinaVietnamFeeOrigin = $shipChinaVietnam['fee_origin'];

            if(isset($shipChinaVietnam['fee_discount']))
            $shipChinaVietnamDiscount = $shipChinaVietnam['fee_discount'];
        }

        $checkingFeeOrigin = $checkingFeeDiscount = 0;
        if(isset($checking)) {
            if(isset($checking['fee_origin'])) $checkingFeeOrigin = $checking['fee_origin'];
            if(isset($checking['fee_discount'])) $checkingFeeDiscount = $checking['fee_discount'];
        }

        //Tính phí để mua chuẩn
        $shippingFrom = $shipInChinaFrom+$shipChinaVietnamDiscount;
        $shippingTo = $shipInChinaTo+$shipChinaVietnamDiscount;
        $totalFeeFrom = $buyingFeeDiscount+$shippingFrom+$packing+$checkingFeeDiscount;
        $totalFeeTo = $buyingFeeDiscount+$shippingTo+$packing+$checkingFeeDiscount;

        //Tính phí khi chưa áp dụng triết khấu
        $shippingFromOrigin = $shipInChinaFrom+$shipChinaVietnamFeeOrigin;
        $shippingToOrigin = $shipInChinaTo+$shipChinaVietnamFeeOrigin;
        $totalFeeOriginFrom = ($buyingFeeOrigin+$shippingFromOrigin+$packing+$checkingFeeOrigin);
        $totalFeeOriginTo = ($buyingFeeOrigin+$shippingToOrigin+$packing+$checkingFeeOrigin);

        $ajax = new \AjaxResponse();
        $ajax->type = \AjaxResponse::SUCCESS;

        $ajax->data = array (
            'totalServiceFeeFrom'=>$totalFeeFrom,
            'totalServiceFeeTo'=>$totalFeeTo,
            'totalServiceFee'=>($totalFeeFrom+$totalFeeTo)/2,
            'totalServiceFeeOrigin'=>($totalFeeOriginFrom+$totalFeeOriginTo)/2,

            'buyingFee'=>$buyingFeeOrigin,
            'buyingDiscountFee'=>$buyingFeeDiscount,
            'buyingFeeDiscountPercent'=>isset($rank['discount_buying'])?$rank['discount_buying']:0,
            'checkingFee'=>$checkingFeeOrigin,
            'checkingDiscountFee'=>$checkingFeeDiscount,
            'checkingFeeDiscountPercent'=>isset($rank['discount_checking'])?$rank['discount_checking']:0,
            'packingFee'=>$packing,
            'shippingFee'=>$shippingFrom.'-'.$shippingTo,
            'shippingFeeDetail'=>array (

                'inlandChina'=>array(
                    'from'=>$shipInChinaFrom,
                    'to'=>$shipInChinaTo,
                    'avg'=> ($shipInChinaFrom+$shipInChinaTo)/2
                ),
                'inlandVietnam'=>0,
                'chinaVietnam'=>$shipChinaVietnamFeeOrigin,
                'chinaVietnamDiscount'=>$shipChinaVietnamDiscount,
                'chinaVietnamDiscountPercent'=>isset($rank['discount_shipping_nation'])?$rank['discount_shipping_nation']:0
            )

        );

        return $this->renderText($ajax->toString());
    }
}