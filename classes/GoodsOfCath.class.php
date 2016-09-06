<?php
class GoodsOfCath extends Goods{
    // Секция Новые товары в данной Группе
    protected $_items;
    public function __construct($db,$id,$sort,$page,$count){
        $this->_items=$db->getGoodsOfCath($id,$sort,$page,$count);
    }
}