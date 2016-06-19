<?php
abstract class BaseController{
    // Базовый класс для всех контроллеров, реализует проверку прав доступа на уровне контроллера

    public function __construct(){
        //проверить права для Всего Контроллера
        $this->_logger=new Logger();
        $this->_db=DB::getInstance();
        $un=$this->_logger->getUserName();
        if(empty($permit=$this->_db->getPermition($un,$this))){
            die('access denied');
        }
        echo'Permitions for user '.$un.' on '.get_class($this).' -> '.$permit;    
    }
}