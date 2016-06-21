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
					header('Content-Type:text/plain;');
					echo"\r\nСохраняю:";
					var_dump($user);
					exit;
					//if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))die('Не удалось создать пользователя');
					//if(!DB::getInstance()->createUser($user))die('Не удалось сохранить профиль');
				}
				//есть инвалидные поля, не сохранять
				//var_dump($this->_form->getClasses());
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
}