<?php
class Msg{
	public static function encode($txt){
		return urlencode($txt);
	}
	public static function decode($txt){
		return urldecode($txt);
	}
    public static function encodeSecret($txt){
        return urlencode(base64_encode($txt));
    }
    public static function decodeSecret($txt){
        return base64_decode(urldecode($txt));
    }
    public static function sendMail($mail,$msg){
        $headers='From:Интернет магазин '.$_SERVER['HTTP_HOST'].' <'.MAIL.'>'."\r\n";
        $headers.='Content-type:text/html;charset=utf-8;'."\r\n";
        $subj='Интернет магазин '.$_SERVER['HTTP_HOST'];
        $headers='From:Интернет магазин '.$_SERVER['HTTP_HOST'].' <'.MAIL.'>'."\r\n";
        $headers.='Content-type:text/html;charset=utf-8;'."\r\n";
        die('Отправка e-mail.<br>Кому: '.$mail.'<br>От: '.MAIL.'<br>Текст: '.$msg);
        if(!mail($mail,$subj,$msg,$headers))return('Не удалось отправить майл заказчику.');
    }
}