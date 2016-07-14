<?php
class GoodsController extends BaseController{
    //Контроллер Товарной части приложения
    public function addMethod(){
        //adds new good
        $fc=AppController::getInstance();
        $this->manufs=$this->_db->getManufNames();
        //TODO: передавать массив экземпляров полей!
//        $this->form=new ValidableForm(array('name'=>true,'price'=>true,'descr'=>false,'manuf'=>true,'consist'=>true,'width'=>true));            
        $this->form=$this->_db->formFactory('add_good');
        $this->form->validate();
        if($_SERVER['REQUEST_METHOD']=='POST'){
            // echo'<pre>';
            // var_dump($this->form);
        }
        $fc->setContent($fc->render('goods/add.twig.html',array('this'=>$this)));
    }
}