<?php
class DefaultController extends BaseController{
    //Контроллер поумолчанию, выводит основной контент
    public function errorMethod(){
        // запрос несуществующего метода Любого контроллера
        $fc=AppController::getInstance();
        //echo $_SERVER['REQUEST_URI'];
        $fc->setContent($fc->render('error.twig.html'));
    }

    public function Method(){
        // gladkov.loc
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('index.twig.html',array('logger'=>$this->_logger,)));
    }
    public function goodMethod(){
        // gladkov.loc/good/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_good.twig.html'));
    }
    public function cathMethod(){
        // gladkov.loc/cath/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_cath.twig.html'));
    }
    public function registercompleteMethod(){
        $fc=AppController::getInstance();
        if(isset($_GET['msg']))$msg=convert_uudecode(base64_decode($_GET['msg']));
        else $msg=false;
        $fc->setContent($fc->render('registercomplete.twig.html',array(
            'msg'=>$msg,
            )));   
    }
    public function cabinetMethod(){
        // gladkov.loc/cath/3
        $fc=AppController::getInstance();
        if(isset($_GET['msg']))$msg=convert_uudecode(base64_decode($_GET['msg']));
        else $msg=false;
        $user=$this->_logger->getUser();
        $cabinet=new Cabinet($user);
        $countries=DB::getInstance()->getCountries();
        $fn=get_class($cabinet->getForm()).'.html';
        $fc->setContent($fc->render('cabinet.twig.html',array(
            'msg'=>$msg,
            'form_name'=>$fn,
            'user'=>$user,
            'classes'=>$cabinet->getForm()->getClasses(),
            'fields'=>$cabinet->getForm()->getFields(),
            'msgs'=>$cabinet->getForm()->getMsgs(),
            'countries'=>$countries,
            )));
    }
    public function forgetMethod(){
        $fc=AppController::getInstance();
        if(isset($_GET['msg']))$msg=Msg::decode($_GET['msg']);
        else $msg=false;
        if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['fgtml'])){
            //нажата кнопка Отправить Хэш
            $mail=Globals\clearMail($_POST['fgtml']);
            if($mail!=''){
                $user=DB::getPDO()->getUserByMail($mail);
                if(!empty($user)){
                    //TODO: отправлять mail Здесь
                }
            }
            header('Location:/registercomplete?msg='.Msg::encode('На указанный Вами e-mail отправлено письмо, содержащее ссылку для восстановление пароля.'));
        }
        $fc->setContent($fc->render('forget_password.twig.html',array(
            'msg'=>$msg,
            )));
    }
    public function confirmMethod(){
        //подтверждение регистрации
        $fc=AppController::getInstance();
        $recived_hesh=array_keys($fc->getArgs())[0];
        $recived_slug_hesh=$fc->getArgs()[$recived_hesh];
        $res=DB::getInstance()->activateUser($recived_hesh,$recived_slug_hesh);
        die($res);
        $fc->setContent($fc->render('confirm.twig.html',array(
            'res'=>$res,
            )));
    }
}