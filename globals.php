<?php
namespace Globals{
	const DB_NAME='gladkovdb';
	const DB_USER='gladkov';
	const DB_PASS='lk08d16ex4';

	function clearStr($str){
		return trim(strip_tags($str));
	}

	function clearInt($i){
	    return (int)$i;
	}

	function clearUInt($i){
	    return abs(self::clearInt($i));
	}

	function ucfirst($str,$coding='UTF-8'){
	    return mb_strtoupper(mb_substr($str,0,1,$coding),$coding).mb_substr($str,1,mb_strlen($str),$coding);
	}  
	  
	function clearMail($mail){
	    //Очищает и возвращает Mail или false
	    $res=substr(trim(strip_tags($mail)),0,100);
	    if(preg_match('/^.{1,30}@{1}.{1,20}\.{1}.{1,5}$/',$res))return $res;
	    else return false;
	}
}