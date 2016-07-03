<?php
class RegShopForm{
    //Класс Формы Регистрации Магазина
    protected $_used_fields=array();
    public $_err_fields=array();
    public function __construct($fields){
        //принимает проверяемые поля
        $this->_used_fields=$fields;
    }
    public function validate(){
        //проверить, что получены все поля и проверить пoля
        $rc=new ReflectionClass($this);        
        foreach($this->_used_fields as $f_n=>$v){
            if(!isset($_POST[$f_n]))die($f_n);//exit(header('Location:/error'));
            //вызвать соотв валидатор namefieldValidator
            if($rc->hasMethod($f_n.'Validator'))$validator=$rc->getMethod($f_n.'Validator');
            else $validator=$rc->getMethod('defaultValidator');
            $validator->invoke($this,$f_n,$rc);
        }
    }
    public function paymentValidator($f_n,$rc){
        //орабатывает поле- массив, создаёт новое свойство в текущем экземпляре со значением в виде массива полученных значений.
        $arr_n=$f_n.'_selected';
        $this->{$arr_n}=array();
        foreach($_POST[$f_n] as $k=>$v){
            $this->{$arr_n}[Globals\clearStr($k)]=Globals\clearStr($v);
        }
    }
    public function shipingValidator($n,$rc){
        return $this->paymentValidator($n,$rc);
    }
    public function defaultValidator($n,$rc){
        // Default validator- String Validator
        $c_str=Globals\clearStr($_POST[$n]);
        if($c_str==''){
            $this->_err_fields[$n]='err';
            return false;
        }
        return $c_str;
    }
    // public function __get($n){
    //     return $this->{$n};
    // }
}