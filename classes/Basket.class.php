<?php
class Basket{
    // Корзина пользователя.
    // Хранение- в куках.
    protected $_rows;//строки art:length
    public function __construct($db){
        if(!isset($_COOKIE['basket']))$_COOKIE['basket']='';
        $this->rows=explode('|',($_COOKIE['basket']));
        var_dump($_COOKIE['basket']);
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'basket/basket.twig.html';
    }
    public function getIcon(){
        //возвращает имя шаблона для отображения в меню
        return 'basket/icon.twig.html';
    }
    public function getAddBlock(){
        return 'basket/add_block.twig.html';
    }
}