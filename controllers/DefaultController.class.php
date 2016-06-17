<?php
class DefaultController{
    //Контроллер поумолчанию, выводит основной контент
    protected static $_pdo;
    public static function getPDO(){
        if(!isset(self::$_pdo)){            
            self::$_pdo=new PDO(
                'mysql:host=localhost;charset=utf8;dbname='.Globals\DB_NAME,
                Globals\DB_USER,
                Globals\DB_PASS);
            self::$_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return self::$_pdo;
    }
    public function errorMethod(){
        // запрос несуществующего метода Любого контроллера
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('error.twig.html'));
    }
    public function Method(){
        // gladkov.loc
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('index.twig.html'));
    }
    public function goodMethod(){
        // gladkov.loc/good/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_good.twig.html'));
    }
    public function cathMethod(){
        // gladkov.loc/cath/3
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('show_cath.twig.html'));
    }
    // !!! ОТЛАДКА
    public function createdbMethod(){
        // !!! ОТЛАДКА создаёт НОВУЮ БД по запосам из файла create.sql
        echo'Создание НОВОЙ БД '.Globals\DB_NAME;
        $db=self::getPDO();
        $file=file_get_contents('create.sql');
        $sqlarr=explode(";",$file);

        foreach($sqlarr as $sql){
            if(empty($sql))continue;
            try{
                $db->query($sql);
            }catch(PDOException $e){
                echo $e;
                exit;
            }
        }
        echo'->Ok';
        exit;
    }
}