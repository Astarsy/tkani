<?php
class LeftMenu{
    // Текстовый писк
    public function __construct(){
        $db=new ShopDB();
        $this->_items=$db->getLeftMenuItems();
    }
    public function getItems(){
    //    var_dump($this->_items);
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /search
        return 'left_menu.twig.html';
    }
}