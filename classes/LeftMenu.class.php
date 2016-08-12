<?php
class LeftMenu{
    // Текстовый писк
    protected $_items;//ассоц. массив элементов меню
    public function __construct($db){
        $this->_items=$db->getLeftMenuItems();
    }
    public function getItems(){
        //var_dump($this->_items);
        return $this->_items;
    }
    public function __toString(){
        //возвращает имя файла шаблона для отображения на странице /search
        return 'left_menu.twig.html';
    }
    public function setHere($name){
        // Устанавливает флаг навигации по имени item-a.
        // Возвращает NULL/ERROR
        $i=0;
        foreach($this->_items as $item){
            if($item['name']==$name){
                $this->_items[$i]['here']=true;
                return;
            }
            $i++;
        }
        return 'не найден эл-т меню';
    }
}