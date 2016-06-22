<?php
class ProfileForm extends RegisterForm{
	public function __construct($user,$arr){
		parent::__construct($user,$arr);
		$this->setFieldsFromUser($user);
	}
	protected function save($user){
		if(!DB::getInstance()->saveUser($user))die('Не удалось сохранить профиль');
	}
	protected function setFieldsFromUser($user){		
		$rc=new ReflectionObject($user);
		$props=$rc->getProperties();
		foreach($props as $prop){
			$prop->setAccessible(true);
			if(!isset($this->_used_fields[$prop->name]))continue;
			$this->_fields[$prop->name]=$prop->getValue($user);
		}		
	}
	protected function redirect($m,$u){
		Form::redirect('успешно сохранен','/cabinet');
	}
}