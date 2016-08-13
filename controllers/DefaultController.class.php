<?php
class DefaultController{
    // Контроллер поумолчанию, выводит основной контент.
    // Не наследует BaseController, т.к. не нужна проверка прав.
    // Использует собственный провайдер БД- ShopDB
    public function __construct(){
        $this->_db=new ShopDB();
        $this->logger=new Logger();
        $this->basket=new Basket($this->_db);
        $this->search=new Search();
        $this->host=$_SERVER['SERVER_NAME'];
    }
    public function Method(){
        // Главная Витрина
        $fc=AppController::getInstance();
        $this->left_menu=new LeftMenu($this->_db);
        $this->new_goods=new NewGoods($this->_db);
        $this->recomended_goods=new RecomendedGoods($this->_db);

        $this->left_menu->setHere('jins');

        $fc->setContent($fc->render('default/index.twig.html',array('this'=>$this,)));
    }
    public function goodMethod(){
        // gladkov.loc/good/3
        // Большая карта товара
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        if(!$item=$this->_db->getGoodById(Globals\clearUInt($args[0])))exit(header('Location:/error'));
        $fc->setContent($fc->render('default/show_good.twig.html',array('this'=>$this,'item'=>$item,)));
    }
    public function cathMethod(){
        // gladkov.loc/cath/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('default/show_cath.twig.html',array('this'=>$this,)));
    }
    public function errorMethod(){
        // Выводит страницу ошибки. Сюда идет переадресация из многих обработчиков ошибок
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('error.twig.html'));
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