<?php
class NewGoodsOfGroup extends Goods{
    // Секция Новые товары в данной Группе
    protected $_items;
    public function __construct($db,$id){
        $this->_items=$db->getGoodsOfGroup($id);
    }
}