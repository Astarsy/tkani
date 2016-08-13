<?php
class RecomendedGoods{
    // Секция Новые товары Главной Витрины
    protected $_items;
    public function __construct($db){
        $this->_items=$db->getGoods($order='RAND()');
    }
    public function getItems(){
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'default/recomended_goods.twig.html';
    }
}