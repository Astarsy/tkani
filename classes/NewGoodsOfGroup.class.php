<?php
class NewGoodsOfGroup extends NewGoods{
    // Секция Новые товары в данной Группе
    protected $_items;
    public function __construct($db,$id){
        $this->_items=$db->getGoodsOfGroup($id);
    }
}