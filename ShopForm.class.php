<?php
class ShopForm{
    //Класс Магазина Продавца
    public function __construct($u_id,$s_id){
        if(!($this->shop=DB::getInstance()->getShopOfUser($u_id,$s_id)))return false;
    }
}