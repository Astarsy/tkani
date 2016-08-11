<?php
class NewGoods{
    // Секция Новые товары Главной Витрины
    public function __construct(){
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'new_goods.twig.html';
    }
}