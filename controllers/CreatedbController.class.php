<?php
class CreatedbController{
    public function Method(){
        // !!! ОТЛАДКА создаёт НОВУЮ БД по запосам из файла create.sql
        header('Content-Type:text/plain;');
        echo'Создание НОВОЙ БД '.Globals\DB_NAME;
        if(DB::getInstance()->createTestDB())echo"\r\n->Ok";
        exit;
    }
}