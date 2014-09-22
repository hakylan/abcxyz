<?php
namespace Home\Controller;

use Flywheel\Config\ConfigHandler;
use Flywheel\Event\Event;
use Flywheel\Session\Storage\Redis;
use Home\Controller\HomeBase;
use Flywheel\Session;
use SeuDo\Main;

class OrderLink extends HomeBase
{
    protected $db_name = 'seudo_vn';
    protected $exchange = 3514;

    public $authen;
    public $user;

    public function beforeExecute(){
        parent::beforeExecute();
        $this->authen = \HomeAuth::getInstance();
        $this->user = $this->authen->getUser();
    }

    public function executeDefault()
    {
        $this->setLayout('default');
        $this->setView('OrderLink/default');
        $url = $this->request()->get('url');
        $document = $this->document();
        $document->title = "Đặt hàng qua Link";
        $translate_url = Main::getHomeRouter()->createUrl('PaiTool/Translate');
        $this->view()->assign('translate_url',$translate_url);
        $this->view()->assign('url',$url);
        $this->view()->assign('url_load_order',$this->createUrl('OrderLink/load_order_content'));
        return $this->renderComponent();
    }



    public function executeLoadOrderLink(){
        $this->validAjaxRequest();
        $url = $this->request()->get('url');
        $translate_url = Main::getHomeRouter()->createUrl('PaiTool/Translate');
        $this->view()->assign('translate_url',$translate_url);
        $this->setView('OrderLink/default');
        $document = $this->document();
        $document->addJsVar('url',$url);
        $this->view()->assign('url',$url);
        $this->view()->assign('url_load_order',$this->createUrl('OrderLink/load_order_content'));
        return $this->renderPartial();
    }

    public function executeLoadOrderContent(){
        $this->validAjaxRequest();
        $this->setView('OrderLink/order_link');

        $urlDetail = $this->request()->get('url');

        $result = array();

        if($urlDetail != ''){
            $result = \GlobalHelper::getCrawlData($urlDetail, $website, $file, $content_file);
        }else{
            $website = '';
            $file = '';
            $content_file = '';
        }

        if(!$result && $urlDetail != ''){
//            $this->dispatch('errorOrderLink', new Event("OrderLink",array(
//                'link_error' => urldecode($urlDetail),
//                "message"=> "Không trả về kết quả khi crawl"
//            )));
        }

        $this->view()->assign(
            array(
                'urlDetail' => $urlDetail,
                'result' => $result,
                'exchange' => \ExchangeRate::getExchange(),
                'resource' => \GlobalHelper::resourceKeyToTranslate($this->db_name),
                'website' => $website,
                'cache_file' => $file,
                'content_file' => $content_file,
                'UrlLoadItemView' => $this->createUrl("OrderLink/get_item_viewed")
            )
        );

        return $this->renderPartial();
    }

    public function executeGetItemViewed(){

        $item_viewed = $this->request()->post('item_viewed');
        return $this->renderText('');
//        $item_viewed = explode(';',$item_viewed);
        $this->setView('OrderLink/item_viewed');
        $this->view()->assign('item_viewed',$item_viewed);
        return $this->renderPartial();
    }
}