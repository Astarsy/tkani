<?php
class DB{
    protected $_pdo;
    public function __construct(){
        $this->_pdo=new PDO(
            'mysql:host=localhost;dbname='.Globals\DB_NAME,
            Globals\DB_USER,
            Globals\DB_PASS);
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    public function getPDO(){
        return $this->_pdo;
    }
    public function getPermition($un,$obj){
        //возвращает code если у $un есть права на $obj
        $subj=$this->_pdo->quote(get_class($obj));
        $un=$this->_pdo->quote($un);
        $sql="SELECT code FROM permitions WHERE user_id=(SELECT id FROM users WHERE name=$un)AND subject_id=(SELECT id FROM subjects WHERE name=$subj);";
        try{
            //echo $sql;
            $res=$this->_pdo->query($sql);
        }catch(PDOException $e){
            echo $e;
            exit;
        }
        //var_dump($res);
        if(!empty($res))return $res->fetch(PDO::FETCH_NUM)[0];
        return false;
    }
}