<?php
class EditGoodForm extends AddGoodForm{
    public function __construct($db){
        parent::__construct($db);
    }
    public function init($good){
        // Инициализация полей значениями товара
        foreach($this->_fields as $field){
            $field->setValue($good->{$field->getName()});
        }
    }
}