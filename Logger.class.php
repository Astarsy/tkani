<?php
class Logger{
	public function __construct(){
}
    public function getUserName(){
    	if(isset($_SESSION[Globals\USER_SESNAME]))return $_SESSION[Globals\USER_SESNAME];
    	else return 'guest';
    }
    public function __toString(){
    	return $this->getUserName();
    }
}