<?php
class ValidableField{
    // Абстрактный базовый класс Поля Формы
    protected $_type;
    protected $_name;// имя поля в html-форме
    protected $_title;
    protected $_value;// значение после извлечения из Поста и валидации
    protected $_required;// является ли обязетельным к заполнению
    protected $_err_msg;// текст сообщения об ошибке

    public function  __construct($template){
        $this->_type=(string)$template->type;
        $this->_name=(string)$template->name;
        $this->_title=(string)$template->title;
        $this->_required=(bool)$template->required;
    }
    public function validator(){
        // Base validator
        if(!isset($_POST[$this->_name])){
            // отсутсвует ожидаемое поле
            $this->_err_msg='отсутсвует ожидаемое поле '.$this->_name;
        }
    }
    public function __toString(){
        if(!$this->_err_msg){
            $cl='';
            $msg='';
        }else{
            $cl="class='err'";
            $msg="<div class='field_err_msg'>$this->_err_msg</div>";
        }
        return "<label $cl>$this->_title<input type='$this->_type' name='$this->_name' value='$this->_value'></label>$msg
";
    }public function save(){
        //Перегрузить для сохранения состояния
    }
    public function getName(){
        return $this->_name;
    }
    public function getValue(){
        return $this->_value;
    }
    public function getErrMsg(){
        return $this->_err_msg;
    }
}