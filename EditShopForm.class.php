<?php
class EditShopForm extends RegShopForm{
    public function __construct($fields,$user,$shop_id){
        //принимает проверяемые поля=>обязателльность true/false, id of the user and id of the shop
        parent::__construct($fields);

        //Созаём поля из запроса в БД соотв. Магазина
        $db=DB::getInstance();
        if(!($shop=$db->getShopOfUser($shop_id,$user)))die('Ошибка при запросе магазина');
        foreach($shop as $k=>$v){
            $this->{$k}=$v;
        }
        $this->payment=$db->getPaymentsOfShop($shop_id);
        $this->shiping=$db->getShipingsOfShop($shop_id);
    }
}