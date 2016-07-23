<?php
class SelectField extends TextField{
    // Поле Select, перегружает метод отображения HTML
    public function __toString(){
        if(!$this->_err_msg){
            $cl='';
            $msg='';
        }else{
            $cl="class='err'";
            $msg="<div class='field_err_msg'>$this->_err_msg</div>";
        }
        $options_str='';
        foreach($this->options as $k=>$v){
            $options_str.="<option>$v</option>";
        }
        $res="<label $cl>$this->_title<select type='$this->_type' name='$this->_name'>$options_str</select></label>$msg
";
        return $res;
    }
}