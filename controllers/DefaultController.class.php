<?php
class DefaultController extends BaseController{
    //Контроллер поумолчанию, выводит основной контент
    public function errorMethod(){
        // запрос несуществующего метода Любого контроллера
        $fc=AppController::getInstance();
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
    // !!! ОТЛАДКА
    public function createdbMethod(){
        // !!! ОТЛАДКА создаёт НОВУЮ БД по запосам из файла create.sql
        header('Content-Type:text/plain;');
        echo'Создание НОВОЙ БД '.Globals\DB_NAME;
        if(DB::getInstance()->createTestDB())echo'->Ok';
        exit;
    }
}