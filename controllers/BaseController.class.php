<?php
abstract class BaseController{
    // Базовый класс для всех контроллеров, реализует проверку прав доступа на уровне контроллера

    public function __construct(){
        //проверить права для Всего Контроллера
        $this->_logger=new Logger();
        $this->_db=DB::getInstance();
        $user=$this->_logger->getUser();
        if(empty($permit=$this->_db->getPermition($user->mail,$this))){
            die('access denied');
        }
        //echo'Permitions for user '.$user->name.' on '.get_class($this).' -> '.$permit;    
    }
}