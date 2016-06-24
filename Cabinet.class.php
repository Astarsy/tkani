<?php
class Cabinet{
	// Отвечает за профиль п-ля, обрабатывает POST от форм:
	//-для guest- форма регистрации register_form.html
	//-для залогиненного- форма профиля п-ля profile_form.html
	protected $_form;
	public $err_msg;
	public function __construct($user)
	{
		if($user->mail==''){
			$this->_form=new RegisterForm($user,array(
					'slug'=>true,
					'password'=>true,
					'name'=>false,
					'mail'=>true,
					'alt_mail'=>false,
					'gender'=>true,
					'mobile'=>true,
					'tel'=>false,
					'fax'=>false,
					'zip'=>false,
					'street'=>false,
					'city'=>false,
					'country'=>false,
					'job_title'=>false,
					));
		}else{
			$this->_form=new ProfileForm($user,array(
					'name'=>false,
					'alt_mail'=>false,
					'gender'=>true,
					'mobile'=>true,
					'tel'=>false,
					'fax'=>false,
					'zip'=>false,
					'street'=>false,
					'city'=>false,
					'country'=>false,
					'job_title'=>false,
					));
		}
		if($_SERVER['REQUEST_METHOD']=='POST'&&(isset($_POST['register'])||isset($_POST['profile']))){
			//сохранение Изменений в профиле текущего п-ля
			//или регистрация Нового п-ля
			$this->_form->processForm($user);
		}
	}
	public function getForm(){
		return $this->_form;
	}
}