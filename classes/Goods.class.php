<?php
class Goods{
    // Блок Товары для отображения на рвзных страницах
    protected $_items;
    public function __construct($db,$order='d_date',$ofset=0,$limit=4){
        $this->_items=$db->getGoods($order,$ofset,$limit);
    }
    public function getItems(){
        return $this->_items;
    }
    public function __toString(){
        //создаёт и возвращает имя файла шаблона из имени класса в нижнем регистре: default/classname.twig.html
        // $twig_themplate_name='default/'.strtolower(get_class($this)).'.twig.html';
        // return $twig_themplate_name;
        return 'default/goods.twig.html';
    }
}