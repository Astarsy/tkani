<?php
class ShopController{
    //Контроллер главной- товарной части    
    public function Method(){
    	// gladkov.loc/shop
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('shop_main.twig.html'));
    }
    public function goodMethod(){
    	// gladkov.loc/shop/good
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('shop_good.twig.html'));
    }
}