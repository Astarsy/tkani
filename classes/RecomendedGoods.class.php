<?php
class RecomendedGoods{
    // Секция Новые товары Главной Витрины
    public function __construct(){
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'recomended_goods.twig.html';
    }
}