<?php
class RegisterForm extends CabinetForm{
	public function __construct($user,$arr){
		parent::__construct($user,$arr);
		$user->password='';
	}
	protected function processOver($user){
		//вызывается только при удачном сохрании перед
		//перенаправлением
		//возвращает false/error
		$hesh=RegistrationDataStorage::getHesh(openssl_random_pseudo_bytes(5),1,1);
		//сохранить хеш
		if($res=DB::getInstance()->saveRegSlugHesh($user,$hesh))return $res;
		//отправить e-mail с хэшем для подтверждения
		if($res=$this->mailToUser($user,$hesh))return $res;
	}
	protected function mailToUser($user,$hesh){
		//отправяет e-mail с хэшем для подтверждения
		//возвращает false/error
		$slug_hesh=RegistrationDataStorage::getHesh($user->slug,1,1);
        $ref='http://'.$_SERVER['HTTP_HOST'].'/cabinet/confirm/'.$hesh.'/'.$slug_hesh;
        $msg='Для подтверждения регистрации на сайте '.$_SERVER['HTTP_HOST'].' нажмите на кнопке '."<a href='$ref'>КНОПКА</a>";
        return Msg::sendMail($user->mail,$msg);
	}
	protected function redirect($t,$m,$u){
		parent::redirect('Успешная регистрация','Вы успешно зарегистрированы. На указанный Вами e-mail отправлено письмо, содержащее ссылку для подтверждения электронного адреса.','/msg');
	}
	protected function save($user){
		//сохраняет профиль и рег.данные
		//возвращает false/error
		if($res=$this->createProfile($user))return $res;
		if($res=$this->createRegData($user))return $res;
	}
	protected function createRegData($user){
		//creates a new user register date
		//returns false/error
		if(!RegistrationDataStorage::saveUserRegistrationData($user->mail,$user->password))return'Регистрационные данные не сохранены';
		return false;
	}
	protected function createProfile($user){
		//creates a new user profile
		//returns false/error
		$db=DB::getInstance();
		if($db->getUserByMail($user->mail))return 'Не удалось сохранить профиль. Пожалуйста, обратись в службу технической поддержки.';
		if(!$db->insertUser($user))return 'Не удалось сохранить профиль т.к. произошла ошибка.';
		return false;
	}
	public function passwordValidator($n){
		if(!(isset($_POST['password'])&&isset($_POST['password2'])))die('Нет полей паролей');
		$p1=Globals\clearPassword($_POST['password']);
		$p2=Globals\clearPassword($_POST['password2']);
		if(empty($p1)||empty($p2)){
			$this->_classes[$n]='err';
			$this->_msgs[$n]='Не заполнены поля паролей.';
		}elseif($p1!==$p2){
			$this->_classes[$n]='err';
			$this->_msgs[$n]='Пароли не совпадают.';
		}
		return $p1;
	}
	public function mobileValidator($n){
		$c_ph=Globals\clearPhone($_POST[$n]);
		if($c_ph==false&&$this->_used_fields[$n]){
			$this->_classes[$n]='err';
			$this->_msgs[$n]='Необходимо указать номер мобильного телефона.';
		}
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
			$this->_msgs[$n]='Указанный e-mail уже используется.';
		}
		return $c_mail;
	}
	public function alt_mailValidator($n){
		return $this->mailValidator($n);
	}
}