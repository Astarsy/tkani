<?php
abstract class BaseController{
    // Базовый класс для всех контроллеров, реализует проверку прав доступа на уровне контроллера
    public function __construct(){
        //проверить права для Всего Контроллера
        $this->_logger=new Logger();
        $this->_db=DB::getInstance();
        $this->_user=$this->_logger->getUser();
        if(empty($permitions=$this->_db->getPermitions($this->_user->mail,$this))){
            die('access denied');
        }
        echo('Permitions '.$permitions);
    }
}