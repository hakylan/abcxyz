<?php
namespace Home\Controller;

use Flywheel\Db\Type\DateTime;
use Flywheel\Session;
use SeuDo\Logger;

class GoodiesUtil extends HomeBase
{

    /**
     * @var \Users
     */
    private $user = null;

    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->user = \HomeAuth::getInstance()->getUser();
    }
    
    public function executeDefault() {

    }


    public function executeTranslate(){
        $text = trim($this->request()->post("text"));
        $type = $this->request()->post("type","STRING","title");
        $data_translate = $text;
        if($type == "title"){
            $data_translate = \GlobalHelper::translateTitle($text);
        }else if($type == "properties"){
            $data_translate = \GlobalHelper::translate($text);
        }

        $request = array(
            "type" => \AjaxResponse::SUCCESS,
            "data_translate" => $data_translate
        );

        return $this->renderText(json_encode($request));
    }

    public function executeTrackError() {
        $link = $this->request()->post("link");
        $error = $this->request()->post("error");
        $tool = $this->request()->post("tool","STRING","add_on");

        \OrderingTool::sendMailError($link,$tool,$error,$this->user);
//        Logger::factory($tool)->addError('Error at '.(new DateTime()),array('link'=>$link,'error'=>json_encode($error)));
        return $this->renderText('success');
    }
}
