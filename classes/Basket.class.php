<?php
class Basket{
    // Корзина пользователя.
    // Хранение- в куках.
    public function __construct(){
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /baket
        return 'basket/basket.twig.html';
    }
    public function getIcon(){
        //возвращает имя шаблона для отображения в меню
        return 'basket/icon.twig.html';
    }
}