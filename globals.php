<?php
namespace Globals{
	const DEBUG=true;
	define('MAIL','reg@'.$_SERVER['HTTP_HOST']);
	const DB_NAME='gladkovdb';
	const DB_USER='gladkov';
	const DB_PASS='lk08d16ex4';
	const USER_SESNAME='user_data';
	define('USERS_FILENAME','.htpasswd');//учетные данные поль-лей
    
	function clearStr($str,$len=200){
		return (string)substr(trim(strip_tags($str)),0,$len);
	}

	function clearInt($i){
	    return (int)$i;
	}

	function clearUInt($i){
	    return abs(clearInt($i));
	}

	function ucfirst($str,$coding='UTF-8'){
	    return mb_strtoupper(mb_substr($str,0,1,$coding),$coding).mb_substr($str,1,mb_strlen($str),$coding);
	}	  
	function clearMail($mail){
	    //Очищает и возвращает Mail или false
	    $res=clearStr($mail,55);
	    if(preg_match('/^.{1,30}@{1}.{1,20}\.{1}.{1,3}$/',$res))return $res;
	    else return false;
	}
    function clearPassword($pw){
        //Очищает и возвращает Password
        return substr(trim(strip_tags($pw)),0,100);
    }
    function clearPhone($p){
        //Возвращает очищенный phone или false
        $p=clearStr($p,20);
        //'/^\+7\(\d{3,4}\)\d{2,3}-\d{2}-\d{2}$/'
        if(preg_match('/^([\d,?\(,?\),? ,\+,-]){6,30}$/',$p))return $p;
        return false;
    }
}