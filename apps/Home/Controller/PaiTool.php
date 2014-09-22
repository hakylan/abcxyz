<?php
namespace Home\Controller;

use Flywheel\Caching\Storage\Cache_redis;
use Flywheel\Config\ConfigHandler;
use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event;
use Flywheel\Factory;
use Flywheel\Log\Logger;
use Flywheel\Session\Storage\Redis;
use Home\Controller\HomeBase;
use Flywheel\Session;
use SeuDo\Main;

class PaiTool extends HomeBase
{
    protected $db_name = 'seudo_vn';
    protected $exchange = 3514;

    public $authen;
    public $user;

    public function beforeExecute(){
        parent::beforeExecute();
        $this->authen = \HomeAuth::getInstance();
        $this->user = $this->authen->getUser();
        if(!$this->user) {
            if(strtolower(Factory::getRouter()->getAction()) == 'login') {
                return;
            }
            $this->request()->redirect($this->createUrl('paiTool/login', array(
               'url' => base64_encode($this->request()->getUri())
            )));
        }
    }

    public function executeDefault()
    {
        $this->setLayout('bookmarklet');
        $this->setView('PaiTool/default');

        $url = $this->request()->get('url');
        $decodeUrl = parse_url(urldecode($url));
        $promotionPrice = 0;
        /*if(array_key_exists('query', $decodeUrl)) {
            $params = explode('&', $decodeUrl['query']);
            if(!empty($params)) {
                foreach($params as $p) {
                    if(strpos('_price', $p) !== 0) {
                        $price = explode('=', $p);
                        $promotionPrice = floatval($price[1]);
                    }
                }
            }
        }*/

        // Get data crawl
        $result = \GlobalHelper::getCrawlData($url, $website, $file, $content_file);
        if(isset($result['promotion'])) {
            $promotionPrice = $result['promotion'];
        }

        if($this->user){
            $user_id = $this->user->id;
        }else{
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();//Factory::getSession()->id();
        }

        if(!$result && $decodeUrl != ''){

            $this->dispatch('errorOrderLink', new Event("OrderLink",array('link_error' => $decodeUrl,"message"=> "Khong crawl duoc du lieu")));
        }

        $cart_list = \CartItem::findByUid($user_id);

        $document = $this->document();
        $document->addJsVar('paitool_url',Main::getHomeRouter()->createUrl('PaiTool'));
        $document->addJsVar('TranslateUrl',Main::getHomeRouter()->createUrl('PaiTool/Translate'));

        $this->view()->assign('cart_list',$cart_list);
        $this->view()->assign('user_id',$user_id);

        $this->view()->assign(
            array(
                'urlDetail' => $decodeUrl,
                'result' => $result,
                'exchange' => \ExchangeRate::getExchange(),
                'resource' => \GlobalHelper::resourceKeyToTranslate($this->db_name),//$this->loadKeyword($this->db_name),
                'website' => $website,
                'cache_file' => $file,
                'content_file' => $content_file,
                'promotionPrice' => $promotionPrice
            )
        );
        return $this->renderComponent();
    }

    public function executeLoadCart(){
        if($this->user){
            $user_id = $this->user->id;
        }else{
            $ses = Session\Session::getInstance();
            $user_id = $ses->id();//Factory::getSession()->id();
        }

        $cart_list = \CartItem::findByUid($user_id);

        $this->setView('PaiTool/cart_load');

        $this->view()->assign('cart_list',$cart_list);

        return $this->renderPartial();

    }

    public function executeLogin() {

        $this->setLayout('bookmarkLogin');
        $this->setView('PaiTool/login');

        if($this->request()->getMethod() == $this->request()->isPostRequest()) {
            $credential = $this->request()->post('credential');
            $password = $this->request()->post('password');
            $remember = $this->request()->post('remember');

            $auth = \HomeAuth::getInstance();
            $result =  $auth->authenticate($credential, $password, $remember);
            if($result) {
                if($this->request()->get('url') != '') {
                    $this->redirect(base64_decode($this->request()->get('url')));
                }
            }
        }

        return $this->renderComponent();
    }

    public function executeTranslate(){
//        $this->validAjaxRequest();
        $text = trim($this->request()->post("text"));
        $type = $this->request()->post("type","STRING","title");
        $data_translate = $text;
        if($type == "title"){
            $data_translate = \GlobalHelper::translateTitle($text);
        }else if($type == "properties"){
            $data_translate = \GlobalHelper::translate($text);
        }

//        $this->setView("PaiTool/test_translate");
//        $this->view()->assign("data_translate",$data_translate);

        $request = array(
            "type" => \AjaxResponse::SUCCESS,
            "data_translate" => $data_translate
        );

//        return $this->renderComponent();
        return $this->renderText(json_encode($request));
    }

    public function executeBookmark(){
        $this->setView("PaiTool/test_bookmarklet");
        return $this->renderPartial();
    }

    public function executeTrackError() {
        $link = $this->request()->post("link");
        $error = $this->request()->post("error");
        \SeuDo\Logger::factory('add_on')->addError('Error at '.(new DateTime()),array('link'=>$link,'error'=>json_encode($error)));
        return $this->renderText('success');
    }
}
