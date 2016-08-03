<?php
class ImgField extends ValidableField{
    // Поле Изображение
    public function validator(){
        //Этот тип input не создаёт поле!
        //parent::validator();//проверит наличие поля
        //if($this->_err_msg)return;
        if(empty($_FILES[$this->_name]['name'])&&$this->_required==true&&empty($_POST['old_foto'])){
            // отсутстует обязательное значение
            $this->_err_msg='необходимо выбрать изображение';
        }      
    }
    public function save(){
        //Сохраняет состояние
        if(!empty($_FILES[$this->_name]['name'])){
            // есть загруженный файл
            $foto_name='f_'.time().'.jpg';
            if(NULL===$this->_err_msg=ImgProc::processGoodFoto($this->_name,$foto_name)){
                // image was saved succesfully
                $this->_value=$foto_name;
                return;
            }else{
                // error while saving image
                $this->_err_msg='ошибка при сохранении изображения';
                return;
            }
        }else{
            // нет загруженного файла
            if(!empty($_POST['old_foto'])){
                // но есть старое имя файла изображения
                $this->_value=Globals\clearStr($_POST['old_foto']);
            }else{
                // и нет старого имени файла изображения
                if($this->_required==true){
                    // и это обязательное значение
                    $this->_err_msg='необходимо выбрать изображение';
                    return;
                }
            }
        }
    }
    public function __toString(){
        if($this->_value)$img="<img src='/fotos/mini/$this->_value'><input type='hidden' name='old_foto' value='{$this->_value}'>";
        else $img='';
        if(!$this->_err_msg)$cl=$msg='';
        else{
            $cl="class='err'";
            $msg="<div class='field_err_msg'>$this->_err_msg</div>";
        }
        $res="<label $cl>$this->_title<input type='file' name='$this->_name' value='{$this->_value}'></label>$msg$img";
        return $res;
    }
    public function setValue($v){
        $this->_value=Globals\clearStr($v);
    }
}