<?php
class RegisterForm extends Form{
	public function __construct($user,$arr){
		parent::__construct($user,$arr);
		$user->password='';
	}
	protected function save($user){
		//сохраняет профиль и рег.данные
		//возвращает false в вл.успеха или текст ошибки
		if($res=$this->saveRegData($user))return $res;
		return $this->saveProfile($user);
	}
	protected function saveRegData($user){
		//сохраняет рег.данные возвращает
		//false в вл.успеха или текст ошибки
		if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))return'Регистрационные данные не сохранены';
		return false;
	}
	protected function saveProfile($user){
		//сохраняет профиль возвращает
		//false в вл.успеха или текст ошибки
		if(!DB::getInstance()->saveUser($user))return'Не удалось сохранить профиль';
		return false;
	}
	public function passwordValidator($n){
		if(!(isset($_POST['password'])&&isset($_POST['password2'])))die('Нет полей паролей');
		$p1=Globals\clearPassword($_POST['password']);
		$p2=Globals\clearPassword($_POST['password2']);
		if(empty($p1)||empty($p2)||($p1!==$p2))$this->_classes[$n]='err';
		return $p1;
	}
	public function mobileValidator($n){
		$c_ph=Globals\clearPhone($_POST[$n]);
		if($c_ph==false&&$this->_used_fields[$n])$this->_classes[$n]='err';
		return $c_ph;
	}
	public function mailValidator($n){
		$c_mail=Globals\clearMail($_POST[$n]);
		if($c_mail==false){
			if($this->_used_fields[$n])$this->_classes[$n]='err';
			return Globals\clearStr($_POST[$n]);
		}
		if(false!==RegistrationDataStorage::getUserRegistrationData($c_mail)){
			$this->_classes[$n]='err';
			$this->_msgs[$n]='e-mail уже используется';
		}
		return $c_mail;
	}
	public function alt_mailValidator($n){
		return $this->mailValidator($n);
	}
}