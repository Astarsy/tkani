<?php
class Sorter{
    // Порядок сортировки и номер текущей страницы
    public function __construct($p=0,$s=0,$c){
        //принимает №тек.стр и №способа сотр.,число товаров
        $this->_orders=array(0=>'Новые','Дешевле','Дороже',);
        $this->_page=Globals\clearUInt($p);//номер текущей страницы
        $this->_page_count=ceil(Globals\clearUInt($c)/Globals\GOODS_ON_PAGE);
        $this->_sort=Globals\clearUInt($s);//0-новые,1-дешевле,2-дороже
    }
    public function getCurPage(){
        return $this->_page;
    }
    public function getSortOrder(){
        return $this->_sort;
    }
    public function getOrders(){
        return $this->_orders;
    }
    public function getPageCount(){
        return $this->_page_count;
    }
    public function getBaseRef(){
        return '/cath/1';
    }
    public function printSorter(){
        //возвращает имя шаблона блока
        return 'sorter/sorter.twig.html';
    }
    public function printPagenator(){
        //возвращает имя шаблона блока
        return 'sorter/paginator.twig.html';
    }
}