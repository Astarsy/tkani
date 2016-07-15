<?php
class GoodsController extends BaseController{
    //Контроллер Товарной части приложения
    public function addMethod(){
        //adds new good
        $fc=AppController::getInstance();
        $this->form=$this->_db->formFactory('add_good');
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->form->validate();
            if(!$this->form->getErrMsg())$this->form->save();
            // echo'<pre>';
            // var_dump($this->form);
        }
        $fc->setContent($fc->render('goods/add.twig.html',array('this'=>$this)));
    }
}