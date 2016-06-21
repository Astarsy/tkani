<?php
class Form{
	//Отвечает за извлечение из $_POST, валидацию
	//и хранение полей и их класса ошибки.
	//Конструктор принимает список необязательных полей.
	//Валидатор поумолчанию- defaultValidator- строковой.
	//Имя кастомного- fieldnameValidator
	protected $_fields;
	protected $_free_fields;
	protected $_classes;
	protected $_msgs;
	public function __construct($arr=array()){
		$this->_fields=array();
		$this->_classes=array();
		$this->_msgs=array();
		$this->_free_fields=$arr;
	}
	public function getClasses(){
		return $this->_classes;
	}
	public function getFields(){
		return $this->_fields;
	}
	public function getMsgs(){
		return $this->_msgs;
	}
	public function getFieldValue($n){
		if(!isset($this->_fields[$n]))die('Нет такого свойства '.$n);
		return $this->_fields[$n];
	}
	public function setFreeFields($arr){
		$this->_free_fields=$arr;
	}
	public function createFormField($f_name){	
		$this->_fields[$f_name]=$this->clearValue($f_name);
		return $this->getFieldValue($f_name);
	}
	protected function clearValue($f_n){
		$rc=new ReflectionClass($this);
		if($rc->hasMethod($f_n.'Validator'))$validator=$rc->getMethod($f_n.'Validator');
		else $validator=$rc->getMethod('defaultValidator');
		return $validator->invoke($this,$f_n);
	}
	public function defaultValidator($n){
		// String Validator
		if(!isset($_POST[$n])){
			$this->_classes[$n]='err';
			return false;
		}
		$c_str=Globals\clearStr($_POST[$n]);
		if($c_str==''&&(!isset($this->_free_fields[$n]))){
			$this->_classes[$n]='err';
			return false;
		}
		return $c_str;
	}
	public function slugValidator($n){
		$slug='u'.time();
		return $slug;		
	}
}