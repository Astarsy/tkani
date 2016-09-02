<?php
class GoodsOfCath extends Goods{
    // Секция Новые товары в данной Группе
    protected $_items;
    public function __construct($db,$id,$order='d_date',$ofset=0,$limit=4){
        $this->_items=$db->getGoodsOfCath($id,$order,$ofset,$limit);
    }
}