<?php
class AdminController extends BaseController{
    //Контроллер Админки
    public function __construct(){
        parent::__construct();
        $this->site=$_SERVER['HTTP_HOST'];
    }
    public function Method(){
        // gladkov.loc/admin
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('admin/base.twig.html',array(
            'this'=>$this
            ,)));
    }
    public function user_editMethod(){
        $fc=AppController::getInstance();
        $args=$fc->getArgs();
        if(empty($args))die('Нет необходимого аргумента');
        $mail=Msg::decode(array_keys($args)[0]);
        $db=DB::getInstance();
        $this->user=$db->getUserByMailFull($mail,PDO::FETCH_ASSOC);
        if(!($this->user))die('Пользователь не найден по mail '.$mail);
        $this->subjects=$db->getAllSubjects();
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(isset($_POST['active'])){
                //Активировати/Деактивировать
                if($this->user['active']=='0')$this->user['active']='1';
                else $this->user['active']='0';
                $db->setActiveByMail($mail,$this->user['active']);
                header('Location:'.$_SERVER['REQUESR_URI']);
                exit;
            }
        }
        $fc->setContent($fc->render('admin/user_edit.twig.html',array(
            'this'=>$this
            ,)));
    }
    public function usersMethod(){
        // gladkov.loc/admin/users
        $fc=AppController::getInstance();
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(!empty($_POST['mail'])){
                $this->mail=Globals\clearStr($_POST['mail']);
                if($this->mail!=''){
                    header('Location:/admin/user_edit/'.Msg::encode($this->mail));
                    exit;
                }
            }
            header('Location:'.$_SERVER['REQUESR_URI']);
        }
        $fc->setContent($fc->render('admin/user_search.twig.html',array(
            'this'=>$this
            ,)));
    }
    public function requestsMethod(){
        // Отображает непогашенные заявки на отк-е Магазина
        $fc=AppController::getInstance();
        $this->requests=$this->_db->getAllSalerRequests();
        $this->requsts_count=(int)count($this->requests);
        if($_SERVER['REQUEST_METHOD']=='POST'){
            //Одобрение заявки
            if(!isset($_POST['request_id']))die('Нет ожидаемого поля');
            $id=Globals\clearUInt($_POST['request_id']);
            if($err=$request=$this->_db->confirmSalerRequest($id))die($err);
            header('Location:'.$_SERVER['REQUESR_URI']);
        }
        $fc->setContent($fc->render('admin/requests.twig.html',array(
            'this'=>$this
            ,)));
    }
}