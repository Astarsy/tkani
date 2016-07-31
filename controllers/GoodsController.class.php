<?php
class GoodsController extends BaseController{
    //Контроллер Товарной части приложения
    public function addMethod(){
        //adds new good
        $fc=AppController::getInstance();
        $this->form=$this->_db->formFactory('add_good');
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->form->validate();
            if(!$this->form->getErrMsg()){
                $this->form->save();
                if(false===$err=$this->form->getErrMsg()){
                    if(false!==$good_slug=$this->_db->createGood($this->form,$this->_user->id)){
                        exit(header('Location:/goods/edit/'.$good_slug));
                    }else{
                        $this->errMsg='Ошибка при создании записи БД.';
                    }
                }else{
                    $this->errMsg='Ошибка при сохранении формы '.$err;
                }
            }
            // echo'<pre>';
            // var_dump($this->form);
        }
        $fc->setContent($fc->render('goods/add.twig.html',array('this'=>$this)));
    }
    public function editMethod(){
        //edits the good by slug
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))header('Location:/error');
        if(!$good=$this->_db->getGoodBySlug($args[0]))exit(header('Location:/error'));
        die('<pre>'.var_dump($good));
        $this->form=$this->_db->formFactory('add_good');
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->form->validate();
            if(!$this->form->getErrMsg()){
                if($good_slug=$this->_db->createGood($this->form)){
                    $this->form->save();
                    if(!$this->form->getErrMsg())exit(header('Location:/goods/edit/'.$good_slug));
                }else{
                    $this->errMsg='Не удалось добавить товар';
                }
            }
            // echo'<pre>';
            // var_dump($this->form);
        }
        $fc->setContent($fc->render('goods/add.twig.html',array('this'=>$this)));
    }
}