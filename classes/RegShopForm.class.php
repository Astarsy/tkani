<?php
class RegShopForm{
    //Класс Формы Регистрации Магазина
    //Метод validate создаст свойтва класса с очищенными значениями соотв. полей формы. Массив $_err_fields будет содержать инвалидные поля в виде 'field_name'=>'err' для упрощения присвоения класса ошибки в форме, а массив $err_msgs- текст некоторых ошибок, если о них необходимо сообщить
    protected $_used_fields=array();
    public $_err_fields=array();
    public $err_msgs=array();
    public function __construct($fields){
        //принимает проверяемые поля=>обязателльность true/false
        $this->_used_fields=$fields;
    }
    public function validate(){
        //проверить, что получены все поля и проверить пoля
        $rc=new ReflectionClass($this);        
        foreach($this->_used_fields as $f_n=>$v){
            if(!isset($_POST[$f_n]))die('не найдено поле '.$f_n);//exit(header('Location:/error'));
            //вызвать соотв валидатор namefieldValidator
            if($rc->hasMethod($f_n.'Validator'))$validator=$rc->getMethod($f_n.'Validator');
            else $validator=$rc->getMethod('defaultValidator');
            $this->{$f_n}=$validator->invoke($this,$f_n,$rc);
        }
    }
//TODO: Добавить валидатор для pub_phone
    public function paymentValidator($f_n,$rc){
        //орабатывает поле- массив, создаёт новое свойство в текущем экземпляре со значением в виде массива полученных значений.
        $arr_n=array();
        foreach($_POST[$f_n] as $k=>$v){
            $arr_n[Globals\clearStr($k)]=Globals\clearStr($v,400);
        }
        if(''==implode('',$arr_n)){
            $this->_err_fields[$f_n]='err';
            $this->err_msgs[$f_n]='Необходимо выбрать хотя бы одно значение.';
        }
        return $arr_n;
    }
    public function shipingValidator($n,$rc){
        return $this->paymentValidator($n,$rc);
    }
    public function defaultValidator($n,$rc){
        // Default validator- String Validator, returns cleared string
        $c_str=Globals\clearStr($_POST[$n]);
        if($c_str==''&&$this->_used_fields[$n]==true){
            $this->_err_fields[$n]='err';
        }
        return $c_str;
    }
    public function titleValidator($n,$rc){
        //Дополняет проверку запросом на уникальность
        $c_n=$this->defaultValidator($n,$rc);
        if(false!==DB::getInstance()->getShopByTitle($c_n)){
            $this->_err_fields[$n]='err';
            $this->err_msgs[$n]='Магазин с таким названием уже зарегистрирован. Пожалуйста, выберите другое название.';
        }
        return $c_n;
    }
}