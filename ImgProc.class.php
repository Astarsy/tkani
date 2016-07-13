<?php
define('IMG_MINI_PATH',$_SERVER['DOCUMENT_ROOT'].'/'.'logos/mini/');
define('IMG_BIG_PATH',$_SERVER['DOCUMENT_ROOT'].'/'.'logos/big/');
const FILE_FIELD_NAME='user_file';
const MINI_WIDTH=400; const MINI_HEIGHT=115;
const MAXI_WIDTH=728; const MAXI_HEIGHT=210;
const MAX_IMAGEFILE_SIZE=30000000;

class ImgProc{
    public static function processLoadedImage($file_name){
        //Обрабатывает полученный файл изображения:
        //-обрезает Большое фото
        //-сздает миниатюру
        //Возвращает true/false
        //die(IMG_BIG_PATH);
        if($_FILES[FILE_FIELD_NAME]['error']!=0){
            echo"Ошибка загрузки ".$_FILES[FILE_FIELD_NAME]['error'];
            return false;
        }
        if($_FILES[FILE_FIELD_NAME]['size']>MAX_IMAGEFILE_SIZE){
            echo"Файл слишком большой";
            return false;
        }
        if($_FILES[FILE_FIELD_NAME]['type']!='image/jpeg'){
            echo"Неверный тип файла";
            return false;
        }
        $path=IMG_BIG_PATH.$file_name;
        $mini_path=IMG_MINI_PATH.$file_name;
        move_uploaded_file($_FILES[FILE_FIELD_NAME]['tmp_name'],$path);
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
        $big_img=imagecreatetruecolor(MAXI_WIDTH,MAXI_HEIGHT);
        imagecopyresampled($big_img,$source_img,0,0,$x_s,$y_s,MAXI_WIDTH,MAXI_HEIGHT,$w_big,$h_big);
        //imagecopy($big_img, $source_img, 0, 0, $x_s, $y_s, $w_big, $h_big);
        //Cохранить Большое изображение
        imagejpeg($big_img,$path,100);
        //Обрезать Малое изображение
        list($width,$height)=getimagesize($path);
        $mini_img=imagecreatetruecolor(MINI_WIDTH,MINI_HEIGHT);
        imagecopyresampled($mini_img,$big_img,0,0,0,0,MINI_WIDTH,MINI_HEIGHT,$width,$height);
        //Cохранить Малое изображение
        imagejpeg($mini_img,$mini_path,100);
        return true;
    }
}