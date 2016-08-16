<?php
class Basket{
    // Корзина пользователя.
    // Хранение- в куках.
    protected $_db;
    protected $_rows;//строки art:length
    public function __construct($db){
        $this->_db=$db;
        if(!isset($_COOKIE['basket'])){
            $_COOKIE['basket']='';
            setcookie('basket','',0,'/');
        }
        $this->_rows=explode('|',($_COOKIE['basket']));
    }
    public function getItems(){
        //Возвращяет массив массивов элементов в корзине
        $items=$this->_db->getBasketItems($this->_rows);
        return $items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'basket/basket.twig.html';
    }
    public function getIcon(){
        //возвращает имя шаблона для отображения в меню
        return 'basket/icon.twig.html';
    }
    public function getContent(){
        //возвращает имя шаблона для отображения содержимого корзины
        return 'basket/content.twig.html';
    }
    public function getAddBlock(){
        //возвращает имя шаблона блока add_block
        return 'basket/add_block.twig.html';
    }
    public function getIncDecBlock(){
        //возвращает имя шаблона блока add_block
        return 'basket/inc_dec_block.twig.html';
    }
}