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
        //Возвращяет массив массивов элементов в корзине или NULL
        if(!$this->_rows)return;
        $slug_arr=array();
        foreach($this->_rows as $row){
            $slug_arr[]=Globals\clearStr(explode(':',$row)[0],30);
        }
        $slugs_str='"'.implode('","',$slug_arr).'"';
        $goods=$this->_db->getGoodsBySlugs($slugs_str);
        $items=array();
        foreach($slug_arr as $slug){
            if($good=$this->getGoodBySlug($slug,$goods))$items[]=$good;
        }
        return $items;
    }
    private function getGoodBySlug($s,$goods){
        foreach($goods as $good){
            if($good->slug==$s)return $good;
        }
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