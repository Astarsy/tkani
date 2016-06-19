<?php
class Cabinet{
	// Отвечает за профиль п-ля, обрабатывает POST от форм:
	//-для guest- форма регистрации register_form.html
	//-для залогиненного- форма профиля п-ля profile_form.html
	protected function getSlug(){
		$slug='u'.time();
		return $slug;
	}
	protected function clearPassword(){
		if(!(isset($_POST['pass1'])&isset($_POST['pass2'])))die('Нет полей паролей');
		$p1=Globals\clearPassword($_POST['pass1']);
		$p2=Globals\clearPassword($_POST['pass2']);
		if(empty($p1)||empty($p2))die('Не заполнены поля паролей');
		if($p1!==$p1)die('Пароли не совпадают');
		return $p1;
	}
	protected function clearValue($val,$prop){
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
				unset($user->id);
				unset($user->slug);
				$rc=new ReflectionObject($user);
				$props=$rc->getProperties();
				foreach($props as $prop){
					if(!isset($_POST[$prop->name]))die('Не все данные получены в Кабинете: '.$prop);
					else{
						$prop->setAccessible(true);
						$prop->setValue($user,$this->clearValue($_POST[$prop->name],$prop));
					}
				}
				$user->slug=$this->getSlug();
				$user->password=$this->clearPassword();
				if(	empty($user->slug)||
					empty($user->mail)||
					empty($user->password))die('Инвалидные поля'.var_dump($user));
				// всё проверено, сохранять в файл и в БД
				if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))die('Не удалось создать пользователя');
				if(!DB::getInstance()->createUser($user))die('Не удалось сохранить профиль');
			}
		}
	}
}