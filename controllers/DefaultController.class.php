<?php
class DefaultController{
    //Контроллер поумолчанию, выводит лендинг
    public function defaultMethod(){
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('index.twig.html'));
    }
    public function errorMethod(){
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('error.twig.html'));
    }
}