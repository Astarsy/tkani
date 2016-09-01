<?php
class RecomendedGoodsOfGroup extends RecomendedGoods{
    // Секция Новые товары в данной Группе
    protected $_items;
    public function __construct($db,$id){
        $this->_items=$db->getGoodsOfGroup($id,$order='RAND()');
    }
}