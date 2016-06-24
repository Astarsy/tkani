<?php
class RegisterForm extends Form{
	public function __construct($user,$arr){
		parent::__construct($user,$arr);
		$user->password='';
	}
	protected function processOver($user){
		//вызывается только при удачном сохрании перед
		//перенаправлением
		$hesh=RegistrationDataStorage::getHesh(openssl_random_pseudo_bytes(5),1,1);
		//отправить e-mail с хэшем для подтверждения
		if($res=$this->mailToUser($user,$hesh))return $res;
		//раз всё Ок, сохранить хеш
		if($res=DB::getInstance()->saveRegSlugHesh($user,$hesh))return $res;
	}
	protected function mailToUser($user,$hesh){
		//отправить e-mail с хэшем для подтверждения
		
		$slug_hesh=RegistrationDataStorage::getHesh($user->slug,1,1);
		$headers='From:Интернет магазин '.$_SERVER['HTTP_HOST'].' <'.MAIL.'>'."\r\n";
        $headers.='Content-type:text/html;charset=utf-8;'."\r\n";
        $ref='http://'.$_SERVER['HTTP_HOST'].'/confirm/'.$hesh.'/'.$slug_hesh;
        $msg='Для подтверждения регистрации на сайте '.$_SERVER['HTTP_HOST'].' нажмите на кнопке '."<a href='$ref'>КНОПКА</a>";
        echo('Отправка e-mail.<br>Кому: '.$user->mail.'<br>От: '.MAIL.'<br>Текст: '.$msg);
		//if(!mail($user->mail,'Регистрация на сайте '.$_SERVER['HTTP_HOST'],$msg,$headers))return('Не удалось отправить майл заказчику.');
	}
	protected function redirect($m,$u){
		//parent::redirect('Вы успешно зарегистрированы. На указанный Вами e-mail отправлено письмо, содержащее ссылку для подтверждения.','/registercomplete');
	}
	protected function save($user){
		//сохраняет профиль и рег.данные
		//возвращает false в cл.успеха или текст ошибки
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