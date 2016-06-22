<?php
class RegistrationDataStorage{
	// Отвечает за хранение паролей пользователей
    public static function getUserRegistrationData($mail){
        //Получает из хранилища данные п-ля
        //возвращает массив строк mail,pass_hash,salt,iters
        //либо false        
        //В данной версии учетные записи хранятся в файле
        if($mail==='')return false;
        if(!is_file(USERS_FILENAME)){
            echo'Файл не найден '.USERS_FILENAME;// ОТЛАДКА!!!
            exit;
        }
        $users=file(USERS_FILENAME);
        foreach($users as $user){
            $strs=explode(':',$user);
            if($strs[0]==$mail)return $strs;
        }
        return false;
    }
    public static function saveUserRegistrationData($m,$p){
        //Сохраняет новую ючетную запись в хранилище
        //В данной версии учетные записи хранятся в файле
        if(self::getUserRegistrationData($m))return false;
        $s=rand(0,getrandMax());
        $i=rand(2,20);
        $h=self::getHesh($p,$s,$i);
        $str="$m:$h:$s:$i\n";
        if(file_put_contents(USERS_FILENAME,$str,FILE_APPEND))return true;
        return false;
    }
    public static function getHesh($p,$s,$its){
        $str=$p;
        for($i=0;$i<$its;$i++)$str=sha1($str.$s);
        return $str;
    }
}