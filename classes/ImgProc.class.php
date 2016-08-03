<?php
class ImgProc{
    protected static function checkFileField($field_name){
        //Проверяет полученные данные в $_FILES
        //Возвращает false/error
        if($_FILES[$field_name]['error']!=0){
            return"Ошибка загрузки ".$_FILES[$field_name]['error'];
        }
        if($_FILES[$field_name]['size']>Globals\MAX_IMAGEFILE_SIZE){
            return"Файл слишком большой";
        }
        if($_FILES[$field_name]['type']!='image/jpeg'){
            return"Неверный тип файла";
        }        
        return false;
    }
    protected static function cutImage($path,$new_width,$new_height,$source_img,&$res_img){
        // Обрезает и сохраняет image
        // returns false/error and res_img for scaling
        list($width,$height)=getimagesize($path);
        if($width>$height){
            $w_big=$height;
            $h_big=$height;
        }else{
            $w_big=$width;
            $h_big=$width;
        }
        $x_s=($width-$w_big)/2;
        $y_s=($height-$h_big)/2;
        if(false===$res_img=@imagecreatetruecolor($new_width,$new_height))return'не удалось обработать изображение';
        if(!imagecopyresampled($res_img,$source_img,0,0,$x_s,$y_s,$new_width,$new_height,$w_big,$h_big))return'Не удалось обработать большое изображение';
        //Cохранить Большое изображение
        if(!imagejpeg($res_img,$path,100))return'Не удалось сохранить большое изображение';
        return false;
    }    
    protected static function scaleImage($source_path,$new_path,$new_width,$new_height,$source_img){
        // Масштабирует и сохраняет image
        // returns false/error
        list($width,$height)=getimagesize($source_path);
        if(false===$new_img=@imagecreatetruecolor($new_width,$new_height))return'не удалось смасштаборовать изображение';
        if(!imagecopyresampled($new_img,$source_img,0,0,0,0,$new_width,$new_height,$width,$height))return'Не удалось обработать малое изображение';
        if(!imagejpeg($new_img,$new_path,100))return'Не удалось сохранить малое изображение';
        return false;
    }
    protected static function processImages($field_name,$big_path,$mini_path,$big_width,$big_height,$mini_width,$mini_height){
        //Вызывает методы обрезки и масштабирования изобр.
        //returns false/error
        if(false===@move_uploaded_file($_FILES[$field_name]['tmp_name'],$big_path))return'Не удалось переместить файл изображения';
        if(false===$source_img=@imagecreatefromjpeg($big_path))return'Файл изображения повреждён';
        //Обрезать и сохранить Большое изображение
        $res_img=false;
        if($err=self::cutImage($big_path,$big_width,$big_height,$source_img,$res_img))return $err;
        //Масштабировать и сохранить Малое изображение
        if($err=self::scaleImage($big_path,$mini_path,$mini_width,$mini_height,$res_img))return $err;
        return false;
    }
    public static function processGoodFoto($field_name,$file_name){
        //Обрабатывает файл изображения Товара.
        //Возвращает NULL/error
        if($err=self::checkFileField($field_name))return $err;
        if(false===@imagecreatefromjpeg($_FILES[$field_name]['tmp_name']))return'Файл изображения повреждён';
        $big_path=GOOD_FOTO_BIG_PATH.$file_name;
        $mini_path=GOOD_FOTO_MINI_PATH.$file_name;
        if($err=self::processImages($field_name,$big_path,$mini_path,Globals\GOOD_FOTO_BIG_WIDTH,Globals\GOOD_FOTO_BIG_HEIGHT,Globals\GOOD_FOTO_MINI_WIDTH,Globals\GOOD_FOTO_MINI_HEIGHT))return $err;
    }
    public static function processLoadedImage($file_name){
        //Обрабатывает полученный файл изображения:
        //-обрезает Большое фото
        //-сздает миниатюру
        //Возвращает true/false
        //die(IMG_BIG_PATH);
        if($_FILES[Globals\FILE_FIELD_NAME]['error']!=0){
            echo"Ошибка загрузки ".$_FILES[Globals\FILE_FIELD_NAME]['error'];
            return false;
        }
        if($_FILES[Globals\FILE_FIELD_NAME]['size']>Globals\MAX_IMAGEFILE_SIZE){
            echo"Файл слишком большой";
            return false;
        }
        if($_FILES[Globals\FILE_FIELD_NAME]['type']!='image/jpeg'){
            echo"Неверный тип файла";
            return false;
        }
        $path=IMG_BIG_PATH.$file_name;
        $mini_path=IMG_MINI_PATH.$file_name;
        move_uploaded_file($_FILES[Globals\FILE_FIELD_NAME]['tmp_name'],$path);
        list($width,$height)=getimagesize($path);
        //
        if(false===$source_img=@imagecreatefromjpeg($path))die('Файл изображения повреждён');
        // if($width< MAXI_WIDTH OR $height< MAXI_HEIGHT){
        //     echo'Изображение слишком мало';
        //     return false;
        // }
        //Обрезать Большое изображение
        if($width>$height){
            $w_big=$height;
            $h_big=$height;
        }else{
            $w_big=$width;
            $h_big=$width;
        }
        $x_s=($width-$w_big)/2;
        $y_s=($height-$h_big)/2;
        $big_img=imagecreatetruecolor(Globals\MAXI_WIDTH,Globals\MAXI_HEIGHT);
        imagecopyresampled($big_img,$source_img,0,0,$x_s,$y_s,Globals\MAXI_WIDTH,Globals\MAXI_HEIGHT,$w_big,$h_big);
        //imagecopy($big_img, $source_img, 0, 0, $x_s, $y_s, $w_big, $h_big);
        //Cохранить Большое изображение
        imagejpeg($big_img,$path,100);
        //Обрезать Малое изображение
        list($width,$height)=getimagesize($path);
        $mini_img=imagecreatetruecolor(Globals\MINI_WIDTH,Globals\MINI_HEIGHT);
        imagecopyresampled($mini_img,$big_img,0,0,0,0,Globals\MINI_WIDTH,Globals\MINI_HEIGHT,$width,$height);
        //Cохранить Малое изображение
        imagejpeg($mini_img,$mini_path,100);
        return true;
    }
    public static function deleteFotos($fn){
        // Deletes image files
        $big_path=GOOD_FOTO_BIG_PATH.$fn;
        $mini_path=GOOD_FOTO_MINI_PATH.$fn;
        if(!(unlink($big_path)&unlink($mini_path)))return 'Не удалось удалить файл изображения';
    }
}