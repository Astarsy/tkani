<?php
class Cabinet{
	// Отвечает за профиль п-ля, обрабатывает POST от форм:
	//-для guest- форма регистрации register_form.html
	//-для залогиненного- форма профиля п-ля profile_form.html
	protected $_form;
	public function __construct($user)
	{
		if($user->mail==''){
			$this->_form=new RegisterForm(array(
					'name'=>false,
					'alt_mail'=>false,
					'tel'=>false,
					'fax'=>false,
					'zip'=>false,
					'street'=>false,
					'city'=>false,
					'country'=>false,
					'job_title'=>false,
					));
			$user->password='';
		}else{
			$this->_form=new ProfileForm(array(
					'name'=>false,
					'tel'=>false,
					'fax'=>false,
					'zip'=>false,
					'street'=>false,
					'city'=>false,
					'country'=>false,
					'job_title'=>false,
					));
		}
		if($_SERVER['REQUEST_METHOD']=='POST'&&
			(isset($_POST['register'])||isset($_POST['profile']))){
			//сохранение Изменений в профиле текущего п-ля
			//или регистрация Нового п-ля
			$this->processForm($user);
		}
	}
	protected function processForm($user){
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
			if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))echo('Регистрационные данные не сохранены');
			if(!DB::getInstance()->saveUser($user))die('Не удалось сохранить профиль');
			header('Location:'.$_SERVER['REQUEST_URI']);
		}
		//есть инвалидные поля, не сохранять
		unset($user->slug);//понять в twig, что не сохранен
		//var_dump($this->_form->getFields());
	}
	public function getForm(){
		return $this->_form;
	}
}