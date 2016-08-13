<?php
class GoodsController extends BaseController{
    //Контроллер Товарной части приложения
    public function Method(){
        // defualt method- shows all goods and a menu
        $fc=AppController::getInstance();
        $this->goods=$this->_db->getGoodsOfShopOfUserById($this->_user->id);
        $this->goods_count=count($this->goods);
        $fc->setContent($fc->render('goods/default.twig.html',array('this'=>$this)));
    }
    public function addMethod(){
        //adds new good
        $fc=AppController::getInstance();
        $this->form=$this->_db->formFactory('AddGoodForm');
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->form->validate();
            if(!$this->form->getErrMsg()){
                $this->form->save();
                if(NULL===$err=$this->form->getErrMsg()){
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
        if(!isset($args[0]))exit(header('Location:/error'));
        if(!$good=$this->_db->getGoodBySlug($args[0]))exit(header('Location:/error'));
        //die('<pre>'.var_dump($good));

        //создать э-р EditGoodField с иниц-ей полей
        $this->form=$this->_db->formFactory('EditGoodForm');
        $this->form->init($good);
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->form->validate();
            if(NULL===$err=$this->form->getErrMsg()){
                $this->form->save();
                if(NULL===$err=$this->form->getErrMsg()){
                    if(NULL===$err=$this->_db->saveGood($this->form,$this->_user->id,$good->id)){
                        exit(header('Location:/goods/edit/'.$good->slug));
                    }else{
                        $this->errMsg='Ошибка при создании записи БД. '.$err;
                    }
                }else{
                    $this->errMsg='Ошибка при сохранении формы '.$err;
                }
            }else{
                $this->errMsg='Ошибка валидации формы. '.$err;
            }
        }
        $fc->setContent($fc->render('goods/edit.twig.html',array('this'=>$this)));
    }
    public function deleteMethod(){
        //deletes the good by slug
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))header('Location:/error');
        if(!$good=$this->_db->getGoodBySlug($args[0]))exit(header('Location:/error'));
        $fn=$good->foto;
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(isset($_POST['yes'])){
                // получено подтверждение на удаление товара
                if(NULL===$err=$this->_db->deleteGoodOfUserShop($good->id,$this->_user->id))if(NULL===$err=ImgProc::deleteFotos($fn))exit(header('Location:/goods'));
                die($err);
            }
            exit(header('Location:/goods'));
        }
        $fc->setContent($fc->render('goods/delete.twig.html',array('this'=>$this,'good'=>$good)));
    }
}