<?php
class LeftMenu{
    // Текстовый писк
    public function __construct(){
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /search
        return 'left_menu.twig.html';
    }
}