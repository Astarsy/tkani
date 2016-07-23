<?php
class RegistrationDataStorage{
	// Отвечает за хранение паролей пользователей
    public static function getUserRegistrationData($mail){
        //Получает из хранилища данные п-ля
        //возвращает массив строк mail,pass_hash,salt,iters
        //либо false        
        //В данной версии учетные записи хранятся в файле
        if($mail==='')return false;
        if(!is_file(USERS_FILENAME))die('Файл не найден '.USERS_FILENAME);
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
    public static function changeUserRegistrationData($mail,$new_passwd){
        //Changes a password for given mail, returns true/false
        if($mail==='')return false;
        if(!is_file(USERS_FILENAME))die('Файл не найден '.USERS_FILENAME);
        $users=file(USERS_FILENAME);
        // echo'<br><pre>';var_dump($users);
        $i=0;
        foreach($users as $user){
            $strs=explode(':',$user);
            if($strs[0]==$mail){
                $h=self::getHesh($new_passwd,$strs[2],$strs[3]);
                $users[$i]="$mail:$h:$strs[2]:$strs[3]";
                // echo'<br>';die(var_dump($users));
                if(file_put_contents(USERS_FILENAME,$users))return true;
            }
            $i++;
        }
        return false;
    }
    public static function getHesh($p,$s,$its){
        $str=$p;
        for($i=0;$i<$its;$i++)$str=sha1($str.$s);
        return $str;
    }
}