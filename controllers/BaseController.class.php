<?php
abstract class BaseController{
    // Базовый класс для всех контроллеров, реализует проверку прав доступа на уровне контроллера
    public function __construct(){
        //проверить права для Всего Контроллера
        $this->_logger=new Logger();
        $this->_db=DB::getInstance();
        $this->_user=$this->_logger->getUser();
        $permitions=$this->_db->getPermitions($this->_user->id,$this);
        if(!$permitions){
            die('access denied');
        }
        echo('Name: '.$this->_user->name.' ,mail: '.$this->_user->mail.' ,access allow');
    }
}