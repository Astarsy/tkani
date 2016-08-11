<?php
class Search{
    // Текстовый писк
    public function __construct(){
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /search
        return 'search/content.twig.html';
    }
    public function getIcon(){
        //возвращает имя шаблона для отображения в меню
        return 'search/icon.twig.html';
    }
}