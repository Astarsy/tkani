<?php
class ShopController extends BaseController{
    //Контроллер Магазина Пользоваталя, доступен только для Продавцов
    public function Method(){
        //Если это Saler, показать магазины
        //иначе- форму заявки на регистрацию
        $fc=AppController::getInstance();
        if($this->_user->shops>0){
            //This is a Saler
            $template='shop/shops.twig.html';
            $this->shops=$this->_db->getShopsOfUserById($this->_user->id);
        $fc->setContent($fc->render($template,array('this'=>$this)));
    }
    public function editMethod(){
        //Edit the shop of the saler
        $fc=AppController::getInstance();
        $args=$fc->getArgs();
        if(empty($args['id']))header('Location:/error');
        $s_id=(int)($fc->getArgs()['id']);        
        if(!($this->shop=new ShopForm($this->_user->id,$s_id)))header('Location:/error');
        $fc->setContent($fc->render('shop/edit.twig.html',array('this'=>$this)));
    }
}