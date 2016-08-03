<?php
class TextField extends ValidableField{
    // Поле с валидатором для Строки
    public function validator(){
        parent::validator();//проверит наличие поля
        if($this->_err_msg)return;
        // String validator
        $val=Globals\clearStr($_POST[$this->_name]);
        if($val==''&&$this->_required==true){
            // отсутстует обязательное значение
            $this->_err_msg='необходимо заполнить данное поле';
            return;
        }
        $this->_value=$val;
    }
    public function setValue($v){
        $this->_value=Globals\clearStr($v);
    }
}