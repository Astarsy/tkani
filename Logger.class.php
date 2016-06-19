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
        if(isset($_SESSION[Globals\USER_SESNAME]))$this->_user=DB::getInstance()->getUserByName(json_decode($_SESSION[Globals\USER_SESNAME])->name);
        else{
            $this->_user=DB::getInstance()->getUserByName("guest");
            if(empty($this->_user))die('Нет такого п-ля guest');
        }
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if(isset($_POST['logout'])){
                // нажата кнопка Выйти
                session_destroy();
                unset($_SESSION[Globals\USER_SESNAME]);
                header('Location: '.$_SERVER['REQUEST_URI']);
            }elseif(isset($_POST['login'])&&isset($_POST['mail'])&&isset($_POST['pass'])){
                // попытка войти
                $this->login($this->clearMail($_POST['mail']),$this->clearPW($_POST['pass']));
                header('Location: '.$_SERVER['REQUEST_URI']);
            }
        }
    }
    protected function login($mail,$passwd){
        // tries loging user in
        $udstrs=self::getUserRegistrationData($mail,$passwd);
        if(false===$udstrs)return;
        list(,$s_pass_hesh,$s,$i)=$udstrs;
        if($s_pass_hesh!==self::getHesh($passwd,$s,$i))return;
        $user=DB::getInstance()->getUserByMail($mail);
        $_SESSION[Globals\USER_SESNAME]=json_encode($user);
    }
    public static function getUserRegistrationData($mail){
        //Получает из хранилища данные п-ля
        //возвращает массив строк mail,pass_hash,salt,iters
        //либо false        
        //В данной версии учетные записи хранятся в файле
        if($mail==='')return false;
        if(!is_file(USERS_FILENAME)){
            echo'Файл не найден '.USERS_FILENAME;// ОТЛАДКА!!!
            exit;
        }
        $users=file(USERS_FILENAME);
        foreach($users as $user){
            $strs=explode(':',$user);
            if($strs[0]==$mail)return $strs;
        }
        return false;
    }
    public function getUser(){
        //Возвращает объект с данными профиля п-ля
        return $this->_user;
    }
    public function getUserName(){
        return $this->_user->name;
    }
    public function __toString(){
        return $this->getUserName();
    }
    protected function clearMail($mail){
        return Globals\clearMail($mail);
    }
    protected function clearPW($pw){
        //Очищает и возвращает Password
        return Globals\clearPassword($pw);
    }
    public static function getHesh($p,$s,$its){
        $str=$p;
        for($i=0;$i<$its;$i++)$str=sha1($str.$s);
        return $str;
    }
}