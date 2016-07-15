<?php
class ValidableForm{
    //Общий абстрактный базвый класс форм
    protected $_fields=array();
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
            if($field->getErrMsg()){
                $this->_err_msg='Не удалось сохранить данные формы.';
            }
        }
    }
    public function __toString(){
        //выводит содержимое МЕЖДУ тегов form
        $res='';
        foreach($this->_fields as $field){
            $res.=$field;
        }
        return $res;
    }
    public function getErrMsg(){
        return $this->_err_msg;
    }
}