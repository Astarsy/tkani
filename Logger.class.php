<?php
class Logger{
    // Отвечает за вход и выход пользователя
    //выводит либо guest, либо имя залогиненного п-ля
    //для рендера соотв-й формы- 1 для входа, 2- для выхода
    //файлы форм:
    // templates/logger_in_form.html
    // templates/logger_out_form.html
    //которую выводить определяет шаблон *.twig.html
    //инф-ю залогиненного п-ля помещает в сессию
    //в виде строке json
    protected $_user;
    public function __construct(){        
        if(isset($_SESSION[Globals\USER_SESNAME])){
            $mail=$_SESSION[Globals\USER_SESNAME];
            $this->_user=DB::getInstance()->getUserByMail($mail);
            if($this->_user==false)$this->logout();
        }else{
            $this->setGuest();
        }
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(isset($_POST['logout'])){
                // нажата кнопка Выйти
                $this->logout();
                header('Location: '.$_SERVER['REQUEST_URI']);
            }elseif(isset($_POST['login'])&&isset($_POST['mail'])&&isset($_POST['pass'])){
                // попытка войти
                $this->login($this->clearMail($_POST['mail']),$this->clearPW($_POST['pass']));
                header('Location: '.$_SERVER['REQUEST_URI']);
            }
        }
    }
    protected function setGuest(){
        $this->_user=DB::getInstance()->getUserByMail('');
        if(empty($this->_user))die('Нет такого п-ля guest');

    }
    protected function logout(){
        session_destroy();
        unset($_SESSION[Globals\USER_SESNAME]);
        $this->setGuest();
    }
    protected function login($mail,$passwd){
        // tries loging user in
        $udstrs=RegistrationDataStorage::getUserRegistrationData($mail,$passwd);
        if(false===$udstrs)return;
        list(,$s_pass_hesh,$s,$i)=$udstrs;
        if($s_pass_hesh!==RegistrationDataStorage::getHesh($passwd,$s,$i))return;
        $user=DB::getInstance()->getUserByMail($mail);
        if($user==false)die('Не найден профиль для mail '.$mail);
        $_SESSION[Globals\USER_SESNAME]=$user->mail;
    }
    public function getUser(){
        //Возвращает объект с данными профиля п-ля
        return $this->_user;
    }
    public function __toString(){
        return $this->_user->name;
    }
    protected function clearMail($mail){
        return Globals\clearMail($mail);
    }
    protected function clearPW($pw){
        //Очищает и возвращает Password
        return Globals\clearPassword($pw);
    }
}