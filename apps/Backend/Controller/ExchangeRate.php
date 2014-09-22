<?php
namespace Backend\Controller;
use Flywheel\Event\Event;
use Flywheel\Factory;

class ExchangeRate extends BackendBase {

    public function beforeExecute()
    {
        parent::beforeExecute();
        $eventDispatcher = $this->getEventDispatcher();
        $eventDispatcher->addListener('afterAddExchange', array(new \BackendEvent(), 'afterAddExchange'));

        $this->auth = \BaseAuth::getInstance();
        $this->user = $this->auth->getUser();

    }

    public function executeDefault(){

        $this->setView('ExchangeRate/default');

        $document = Factory::getDocument();

        $document->title = "Quản lý tỷ giá";

        $exchange_rate_list = \ExchangeRate::read()->select("*")->orderBy('id','desc')->execute()->fetchAll();

        $exchange_rate = array();

        foreach ($exchange_rate_list as $value) {
            $user = \Users::retrieveById($value['exchange_rate']);

            if($user){
                $array['user'] = $user;
            }

            $array = array();


            $array['exr'] = $value;

            $exchange_rate[] = $array;
        }

        $link_add = $this->createUrl("exchange_rate/add_exchange_rate");

        $document->addJsVar("link_add",$link_add,"TOP");

        $this->view()->assign('exchange_rate',$exchange_rate);

        return $this->renderComponent();
    }

    public function executeAddExchangeRate(){

        $this->validAjaxRequest();
        $exchange_rate = $this->request()->request('exchange_rate',"INT",0);

        $ajax = array();


        if(!is_numeric($exchange_rate)){
            $ajax['type'] = 0;
            $ajax['message'] = "Tỷ giá phải là kiểu số";
            return $this->renderText(json_encode($ajax));
        }

        $user = \BackendAuth::getInstance()->getUser();


        if(!$user){
            $ajax['type'] = 0;
            $ajax['message'] = "Bạn chưa đăng nhập";
            return $this->renderText(json_encode($ajax));
        }

        $query = \ExchangeRate::read();
        $query
            ->set('`status`',0)
            ->update(\ExchangeRate::getTableName())->execute();

        $exchange = new \ExchangeRate();

        $exchange->setExchangeRate($exchange_rate);
        $date = date('Y-m-d H:i:s',time());
        $exchange->setCreateTime($date);
        $exchange->setUserCreateId($user->id);
        $exchange->setStatus(1);
        $exchange->setNew(true);
        if($exchange->save()){
            \ExchangeRate::addNewExchangeRate($exchange_rate);
            $ajax['type'] = \AjaxResponse::SUCCESS;
            $ajax['message'] = "Thành công";
            $ajax['id'] = $exchange->getId();
            $ajax['username'] = $user->username.' - '.$user->first_name.' '.$user->last_name;
            $ajax['time'] = date('H:i:s d/m/Y');
            $this->dispatch('afterAddExchange', new Event(array('data' => $exchange)));
        }else{
            $ajax['type'] = \AjaxResponse::ERROR;
            $ajax['message'] = "Có lỗi xảy ra, xin thử lại";
        }
        return $this->renderText(json_encode($ajax));
    }
}