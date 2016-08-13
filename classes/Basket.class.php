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
    public function getAddBlock(){
        return 'basket/add_block.twig.html';
    }
    public function getTotal(){
        // Возвращяет текущее состояние ИТОГО в корзине
        return (int)1000;
    }
}