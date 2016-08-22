<?php
class NewsBar{
    // Блок новостей
    protected $_items;//ассоц. массив элементов меню
    public function __construct($db){
        $this->_items=$db->getNewsBarItems();
    }
    public function getItems(){
        //var_dump($this->_items);
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /search
        return 'default/news_bar.twig.html';
    }
}