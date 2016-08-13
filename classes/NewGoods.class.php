<?php
class NewGoods{
    // Секция Новые товары Главной Витрины
    protected $_items;
    public function __construct($db){
        $this->_items=$db->getGoods();
    }
    public function getItems(){
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'default/new_goods.twig.html';
    }
}