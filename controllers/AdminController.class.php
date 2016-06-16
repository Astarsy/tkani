<?php
class AdminController{
    //Контроллер Админки
    public function __construct(){
        //проверить права
        echo'Permitions for '.get_class($this).' -> Ok';
    }
    public function mainMethod(){
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('admin_main.twig.html'));
    }
}