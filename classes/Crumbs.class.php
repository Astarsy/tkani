<?php
class Crumbs{
    protected $_items=array();
    public function __construct(){
        $this->_items[]=array('Главная','http://'.$_SERVER['HTTP_HOST']);
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице
        return 'default/crumbs.twig.html';
    }
    public function setLocation($args){
        //Формирует массив элементов
        $this->_items=array_merge($this->_items,$args);
    }
    public function getItems(){
        //Возвращает массив эл-в
        return $this->_items;
    }
}