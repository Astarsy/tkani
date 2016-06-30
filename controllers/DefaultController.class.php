<?php
class DefaultController extends BaseController{
    //Контроллер поумолчанию, выводит основной контент
    public function errorMethod(){
        // запрос несуществующего метода Любого контроллера
        $fc=AppController::getInstance();
        //echo $_SERVER['REQUEST_URI'];
        $fc->setContent($fc->render('error.twig.html'));
    }

    public function Method(){
        // gladkov.loc
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('index.twig.html',array('logger'=>$this->_logger,)));
    }
    public function goodMethod(){
        // gladkov.loc/good/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_good.twig.html'));
    }
    public function cathMethod(){
        // gladkov.loc/cath/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_cath.twig.html'));
    }
    public function msgMethod(){
        //выводит сообщение формата:
        //glagkov.loc/msg/title_text/message_text
        $fc=AppController::getInstance();
        if(isset(array_keys($fc->getArgs())[0]))$title=array_keys($fc->getArgs())[0];
        else $title='Сообщение';
        if(isset($fc->getArgs()[$title]))$msg=Msg::decode($fc->getArgs()[$title]);
        else $msg='Отсутствует текст сообщения...';
        $title=Msg::decode($title);
        $fc->setContent($fc->render('msg.twig.html',array(
            'title'=>$title,
            'msg'=>$msg,
            )));   
    }
}