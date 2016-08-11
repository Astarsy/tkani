<?php
class ShopDB{
    public function __construct(){
        $this->_pdo=Globals\getPDOInstance();
    }
}