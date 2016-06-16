<?php
class ShopController{
    //Контроллер главной- товарной части
    public function goodMethod(){
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('shop_good.twig.html'));
    }
}