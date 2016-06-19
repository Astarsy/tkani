<?php
class DB{
    protected static $_instance=null;
    protected $_pdo;
    private function __construct(){
        $this->_pdo=new PDO(
            'mysql:host=localhost;dbname='.Globals\DB_NAME,
            Globals\DB_USER,
            Globals\DB_PASS);
        if(Globals\DEBUG)$this->_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    private function __clone(){}
    public function __destruct(){
        unset($this->_pdo);
    }
    public static function getInstance(){
        if(self::$_instance==null){
            self::$_instance=new DB();
        }
        return self::$_instance;
    }
    public function getUserByMail($mail){
        // Returms Object of user or false
        $mail=$this->_pdo->quote($mail);
        $sql="SELECT id,slug,name,mail,alt_mail,gender,mobile,tel,fax,zip,street,city,country,job_title FROM users WHERE mail=$mail";
        try{
            $res=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $res->fetch(PDO::FETCH_OBJ);
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
    public function createTestDB(){
        // Creates a test database
        $file=file_get_contents('create.sql');
        $sqlarr=explode(";",$file);
        foreach($sqlarr as $sql){
            if(empty($sql))continue;
            try{
                echo $sql;
                $this->_pdo->query($sql);
            }catch(PDOException $e){
                echo $e;
                exit;
            }
        }
    }
}