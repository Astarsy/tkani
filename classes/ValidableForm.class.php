<?php
class ValidableForm{
    //Общий абстрактный базвый класс форм
    protected $_fields=array();// объекты Field
    protected $_err_msg;// текст сообщения об ошибке

    public function __construct($fields){
        $this->_fields=$fields;
    }
    public function validate(){  
        foreach($this->_fields as $field){
            $field->validator();
            if($field->getErrMsg()){
                $this->_err_msg='Пожалуйста, правильно заполните форму.';
            }
        }
    }
    public function save(){  
        foreach($this->_fields as $field){
            $field->save();
            if(NULL!==$err=$field->getErrMsg()){
                $this->_err_msg=$err;
                return;
            }
        }
    }
    public function __toString(){
        //выводит содержимое МЕЖДУ тегов form
        if(!$this->_err_msg)$res='';
        else $res="<div class='form_err_msg'>$this->_err_msg</div>";
        foreach($this->_fields as $field){
            $res.=$field;
        }
        return $res;
    }
    public function getErrMsg(){
        return $this->_err_msg;
    }
    public function getFieldValue($name){
        // Returns a value of the field by name
        foreach($this->_fields as $f){
            if($f->getName()==$name)return $f->getValue();
        }
        return NULL;
    }
}