<?php
class ShopController extends BaseController{
    //Контроллер Магазина Пользоваталя
    public function Method(){
        //Если это Saler, показать магазины
        //иначе- форму заявки на регистрацию
        $fc=AppController::getInstance();
        if($this->_user->shops>0){
            //This is a Saler
            $template='shop/shops.twig.html';
            $this->shops=DB::getInstance()->getShopsOfUserById($this->_user->id);
        }else{
            //This is't Saler
            if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['regiser_saler'])){
                //Register button was pressed
                //Add a request to DB
                DB::getInstance()->addSalerRequest($this->_user);
                //TODO: Send meail to Admin
                Msg::message('Ваша заявка успешно зарегистрирована. Менеджер свяжется с Вами в ближайшее время.');
            }
            $template='shop/register.twig.html';
        }
        $fc->setContent($fc->render($template,array('this'=>$this)));
    }
    public function editMethod(){
        //Edit shop method. 
        $fc=AppController::getInstance();
        
        $fc->setContent($fc->render('shop/edit.twig.html',array('this'=>$this)));
    }
}