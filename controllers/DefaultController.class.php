<?php
class DefaultController{
    //Контроллер поумолчанию, выводит основной контент
    public function Method(){
    	// gladkov.loc
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('index.twig.html'));
    }
    public function errorMethod(){
    	// запрос несуществующего метода Любого контроллера
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('error.twig.html'));
    }
}