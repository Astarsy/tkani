<?php
class AdminController extends BaseController{
    //Контроллер Админки
    public function Method(){
        // gladkov.loc/admin
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('admin_main.twig.html'));
    }
}