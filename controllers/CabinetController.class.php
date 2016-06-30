<?php
class CabinetController extends BaseController{
    //Контроллер Кабинета Пользоваталя
    public function Method(){
        // gladkov.loc/cabinet
        $fc=AppController::getInstance();

        if(isset(array_keys($fc->getArgs())[0]))$title=array_keys($fc->getArgs())[0];
        else $title='Сообщение';
        if(isset($fc->getArgs()[$title]))$msg=Msg::decode($fc->getArgs()[$title]);
        else $msg='Отсутствует текст сообщения...';
        $title=Msg::decode($title);

        if(isset(array_keys($fc->getArgs())[0]))$title=array_keys($fc->getArgs())[0];
        else $title='Сообщение';
        if(isset($fc->getArgs()[$title]))$msg=Msg::decode($fc->getArgs()[$title]);
        else $msg='';
        $title=Msg::decode($title);
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
    public function restoreMethod(){
        //ссылка с кодом подтверждения восстановления пароля
        $fc=AppController::getInstance();
        if(!isset(array_keys($fc->getArgs())[0]))header('Location:/error');
        $u_m=array_keys($fc->getArgs())[0];
        if(!isset($fc->getArgs()[$u_m]))header('Location:/error');
        $res_u_h=$fc->getArgs()[$u_m];
        $user_mail=Msg::decodeSecret($u_m);
        if($user_mail=='')header('Location:/error');
        $user=DB::getInstance()->getUserByMail($user_mail);
        if(!$user)header('Location:/error');
        $user_slug_hesh=RegistrationDataStorage::getHesh($user->slug,1,1);
        // echo('true hesh:'.$user_slug_hesh.'<br>');
        // echo('resived h:'.$res_u_h);exit;
        if($user_slug_hesh!=$res_u_h)header('Location:/error');
        //всё сошлось, вывести форму для ввода нового пароля,
        //или принять данные формы ввода нового пароля
        $msg='Пожалуйста, дважды введите новый пароль.';
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(!isset($_POST['np1'])||!isset($_POST['np2']))header('Location:/error');
            $np1=Globals\clearPassword($_POST['np1']);
            $np2=Globals\clearPassword($_POST['np2']);
            if($np1==''||$np2=='')header('Location:'.$_SERVER['REQUEST_URI']);
            if($np1!==$np2)$msg='Введённые пароли не совпадают. Пожалуйста, введите одинаковые значения.';
            else{
                //принятые данные корректны,сменить пароль и уйти
                if(!RegistrationDataStorage::changeUserRegistrationData($user->mail,$np1))header('Location:/msg/'.Msg::encode('Восстановление пароля').'/'.Msg::encode('Не удалось сменить пароль. Пожалуйста, обратитесь в службу технической поддержки. Приносим извенения за неудобства.'));
                //пароль сменён, установить Активность, на случай восстановления НЕ Активным п-лем, т.к. он подтвердил эл адрес
                DB::getInstance()->setActiveByMail($user->mail,1);
                header('Location:/msg/'.Msg::encode('Восстановление пароля').'/'.Msg::encode('Пароль успешно изменён. Вы можете войти на сайт используя свои e-mail и пароль.'));
            }
        }
        $fc->setContent($fc->render('enter_new_passwd.twig.html',array(
            'msg'=>$msg,
            )));
    }
    public function forgetMethod(){
        //ссылка Забыл Пароль
        //выводит форму/принимает e-mail, проверяет,
        //отправляет письмо с кодом-ссылкой для ввода
        //нового пароля
        //если это не Гость- Разлогинить и выгнать
        $user=$this->_logger->getUser();
        if($user->name!='guest'){
            $this->_logger->logout();
            header('Location: /error');
        }
        $fc=AppController::getInstance();
        if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['fgtml'])){
            //нажата кнопка Отправить Хэш
            $mail=Globals\clearMail($_POST['fgtml']);
            if($mail!=''){
                $user=DB::getInstance()->getUserByMail($mail);
                if(!empty($user)){
                    //отправлять mail c ссылкой вида:
                    //  /restore/user_mail/user_slug_hesh

                    //TODO:конкатенировать хеш с паролем, иначе сслыка продолжит работать после смены пароля
                    $u_m=Msg::encodeSecret($user->mail);
                    $s_h=RegistrationDataStorage::getHesh($user->slug,1,1);
                    $ref='http://'.$_SERVER['HTTP_HOST'].'/cabinet/restore/'.$u_m.'/'.$s_h;
                    $msg="Для восстановления пароля на сайте ".$_SERVER['HTTP_HOST']." нажмите на кнопке <a href='$ref'>КНОПКА</a>. Если кнопка не работает, скoпируйте ссылку ниже и перейдите по ней вставив текст ссылки в адресную строку браузера. $ref";
                    $res=Msg::sendMail($user->mail,$msg);
                    if($res)header('Locatioin:/msg/'.Msg::encode('Отправка письма').'/'.Msg::encode('Возникли затруднения при отправке письма. '.$res));
                }
            }
            //это сообщения выводится в любом случае после нажатия на кнопке Отправить, для конспирации
            header( 'Location:/msg/'.
                    Msg::encode('Отправлено письмо').'/'.
                    Msg::encode('На указанный Вами e-mail отправлено письмо, содержащее ссылку для восстановление пароля.'));
        }
        $fc->setContent($fc->render('forget_password.twig.html'));
    }
    public function confirmMethod(){
        //подтверждение регистрации
        $fc=AppController::getInstance();        
        if(!isset(array_keys($fc->getArgs())[0]))header('Location:/error');
        $recived_hesh=array_keys($fc->getArgs())[0];
        if(!isset($fc->getArgs()[$recived_hesh]))header('Location:/error');
        $recived_slug_hesh=$fc->getArgs()[$recived_hesh];
        $res=DB::getInstance()->activateUser($recived_hesh,$recived_slug_hesh);
        if($res!==false)die($res);
        $fc->setContent($fc->render('msg.twig.html',array(
            'title'=>'Активация учетной записи',
            'msg'=>'Подтверждение Вашего электронного адреса успешно выполнено. Ваша учетная запись активирована. Вы можете войти на сайт используя указзанный при регистрации электронный адрес и пароль.'
            )));
    }
}