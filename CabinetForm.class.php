<?php
class CabinetForm{
	//Отвечает за извлечение из $_POST, валидацию
	//и хранение полей и их класса ошибки.
	//Конструктор принимает массив полей для обхода,
	//где true-обязательные, false- мгут быть не заполнены,
	//поля отсутств. в массиве и присутв. в мoдели
	//пропускаются.
	//Валидатор поумолчанию- defaultValidator- строковой.
	//Имя кастомного- fieldnameValidator
	protected $_fields=array();
	protected $_used_fields=array();
	protected $_classes=array();
	protected $_msgs=array();
	public function __construct($user,$arr){
		$this->_used_fields=$arr;
	}
	public function processForm($user){
		//обрабатывает форму, возвращает false или
		//текст ошибки
		$rc=new ReflectionObject($user);
		$props=$rc->getProperties();
		foreach($props as $prop){
			$prop->setAccessible(true);
			if(!isset($this->_used_fields[$prop->name]))continue;
			$clean_val=$this->createFormField($prop->name);
			$prop->setValue($user,$clean_val);
		}
		if(empty($this->_classes)){
			//инвалидных полей нет, Сохранять
			// header('Content-Type:text/plain;');
			// echo"\r\nСохраняю:";
			// die(var_dump($user));
			if($res=$this->save($user))Msg::error($res);
			//состояние $user сохранено
			$this->_msgs[]=$this->processOver($user);
			$this->redirect('Регистрация пользователя',implode(' ',$this->_msgs),$_SERVER['REQUEST_URI']);
		}
		//если есть инвалидные поля- не сохранять
		//var_dump($this->_fields);exit;
		else return 'Инвалидные поля';
	}
	protected function processOver($user){
		//вызывается только при удачном сохрании перед
		//перенаправлением
	}
	protected function redirect($title,$msg,$uri){
		//перенаправление после успешной обработки данных
		$title=Msg::encode($title);
		$msg=Msg::encode($msg);
		$h='Location:'.$uri.'/'.$title.'/'.$msg;
		print_r($h);
		header($h);
	}
	protected function save($user){
		//требутся перегрузить этот метод
		return'НЕ ПЕРЕГРУЖЕН МЕТОД Form::save';
	}
	public function getClasses(){
		return $this->_classes;
	}
	public function getFields(){
		return $this->_fields;
	}
	public function getMsgs(){
		return implode(' ',$this->_msgs);
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
		if($c_str==''&&$this->_used_fields[$n]){
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