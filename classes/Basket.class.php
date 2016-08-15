<?php
class Basket{
    // Корзина пользователя.
    // Хранение- в куках.
    protected $_rows;//строки art:length
    public function __construct($db){
        if(!isset($_COOKIE['basket'])){
            $_COOKIE['basket']='';
            setcookie('basket','',0,'/');
        }
        $this->rows=explode('|',($_COOKIE['basket']));
        var_dump($_COOKIE['basket']);
    }
    public function getItems(){
        //Возвращяет массив массивов элементов в корзине
        $arr=array(
            );
        $arr[]=array('name'=>'good1','price'=>'1000');
        $arr[]=array('name'=>'good2','price'=>'2000');
        return $arr;
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
        //возвращает имя шаблона блока inc/dec
        return 'basket/add_block.twig.html';
    }
}