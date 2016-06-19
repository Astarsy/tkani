<?php
class Cabinet{
	// Отвечает за профиль п-ля, обрабатывает POST от форм:
	//-для guest- форма регистрации register_form.html
	//-для залогиненного- форма профиля п-ля profile_form.html
	protected function setValue($val,$prop){
		return Globals\clearStr($val);//!!! ОТЛАДКА
	}
	public function __construct()
	{
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if(isset($_POST['profile'])){
				die('Сохраняю профиль');
			}elseif (isset($_POST['register'])) {
				//регистрация поль-ля
				$user=DB::getInstance()->getUserByName('guest');
				$rc=new ReflectionObject($user);
				$props=$rc->getProperties();
				foreach($props as $prop){
					if(isset($_POST[$prop->__toString()]))$prop->setValue($this->clearValue($_POST[$prop->__toString()],$prop));
				}
				// if(!(
				// 	isset($_POST['name'])&
				// 	isset($_POST['mail'])&
				// 	isset($_POST['pass1'])&
				// 	isset($_POST['pass2'])&
				// 	isset($_POST['alt_mail'])&
				// 	isset($_POST['gender'])&
				// 	isset($_POST['mobile'])&
				// 	isset($_POST['tel'])&
				// 	isset($_POST['fax'])&
				// 	isset($_POST['zip'])&
				// 	isset($_POST['street'])&
				// 	isset($_POST['city'])&
				// 	isset($_POST['country'])&
				// 	isset($_POST['job_title'])
				// 		))die('ERROR');
				
				// $name=Globals\clearStr($_POST['name']);
				// $mail=Globals\clearMail($_POST['mail']);
				// $pass1=Globals\clearPassword($_POST['pass1']);
				// $pass2=Globals\clearPassword($_POST['pass2']);
				// $alt_mail=Globals\clearMail($_POST['alt_mail']);
				// $gendergender=(bool)($_POST['gender']);
				// $mobile=Globals\clearPhone($_POST['mobile']);
				// $tel=Globals\clearPhone($_POST['tel']);
				// $fax=Globals\clearPhone($_POST['fax']);
				// $zip=Globals\clearStr($_POST['zip']);
				// $street=Globals\clearStr($_POST['street']);
				// $city=Globals\clearStr($_POST['city']);
				// $country=Globals\clearStr($_POST['country']);
				// $job_title=Globals\clearStr($_POST['job_title']);

				
				//echo'<pre>';var_dump($_POST);exit;
			}
		}
	}
}