<?php
class CathsOfGroup{
    // Секция Категории в Группе
    protected $_items;
    public function __construct($db,$gid){
        $this->_items=$db->getCathsOfGroup($gid);
    }
    public function getItems(){
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице
        return 'default/caths_of_group.twig.html';
    }
}