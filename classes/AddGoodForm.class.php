<?php
class AddGoodForm extends ValidableForm{
    public function __construct($db){
        $name=get_class();
        $field_templates=$db->getFieldsOfForm($name);
        $fields=array();
        foreach($field_templates as $t){
            $fields[]=$db->fieldFactory($t);
        }
        parent::__construct($fields);
    }
}