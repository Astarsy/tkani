<?php
class Cabinet{
	// Отвечает за профиль п-ля, обрабатывает POST от форм:
	//-для guest- форма регистрации register_form.html
	//-для залогиненного- форма профиля п-ля profile_form.html
	protected $_form;
	public function __construct()
	{
		$this->_form=new CabinetForm(array(
			'name'=>true,
			'alt_mail'=>true,
			'tel'=>true,
			'fax'=>true,
			'zip'=>true,
			'street'=>true,
			'city'=>true,
			'country'=>true,
			'job_title'=>true,
			));
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if(isset($_POST['profile'])){
				die('Сохраняю профиль');
			}elseif(isset($_POST['register'])){
				//регистрация поль-ля
				$user=DB::getInstance()->getUserByName('guest');
				$user->password='';
				$rc=new ReflectionObject($user);
				$props=$rc->getProperties();
				foreach($props as $prop){
					$prop->setAccessible(true);
					$clean_val=$this->_form->createFormField($prop->name);
					$prop->setValue($user,$clean_val);
				}
				if(empty($this->_form->getClasses())){
					//инвалидных полей нет, Сохранять
					// header('Content-Type:text/plain;');
					// echo"\r\nСохраняю:";
					// var_dump($user);
					// exit;
					if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))die('Не удалось создать пользователя');
					if(!DB::getInstance()->createUser($user))die('Не удалось сохранить профиль');
					header('Location:'.$_SERVER['REQUEST_URI']);
				}
				//есть инвалидные поля, не сохранять
				//var_dump($this->_form->getFields());
			}
		}
	}
	public function getForm(){
		return $this->_form;
	}
}
class CabinetForm extends Form{
	public function passwordValidator($n){
		if(!(isset($_POST['password'])&&isset($_POST['password2'])))die('Нет полей паролей');
		$p1=Globals\clearPassword($_POST['password']);
		$p2=Globals\clearPassword($_POST['password2']);
		if(empty($p1)||empty($p2)||($p1!==$p2))$this->_classes[$n]='err';
		return $p1;
	}
	public function mobileValidator($n){
		$c_ph=Globals\clearPhone($_POST[$n]);
		if($c_ph==false)$this->_classes[$n]='err';
		return $c_ph;
	}
	public function mailValidator($n){
		$c_mail=Globals\clearMail($_POST[$n]);
		if($c_mail==false){
			if(!isset($this->_free_fields[$n]))$this->_classes[$n]='err';
			return Globals\clearStr($_POST[$n]);
		}
		return $c_mail;
	}
	public function alt_mailValidator($n){
		return $this->mailValidator($n);
	}
}