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
    public function getUserByName($name){
        // Returms Object of user or false
        $name=$this->_pdo->quote($name);
        $sql="SELECT slug,name,mail,alt_mail,gender,mobile,tel,fax,zip,street,city,country,job_title FROM users WHERE name=$name";
        try{
            $res=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $res->fetch(PDO::FETCH_OBJ);
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
        //объекты доступные п-лю gues доступны всем
        //права данного п-ля и guest суммируются
        $subj=$this->_pdo->quote(get_class($obj));
        $un=$this->_pdo->quote($un);
        $sql="SELECT code FROM permitions WHERE user_id IN(SELECT id FROM users WHERE name IN ($un,'guest'))AND subject_id=(SELECT id FROM subjects WHERE name=$subj);";
        try{
            //echo $sql;
            $res=$this->_pdo->query($sql);
        }catch(PDOException $e){
            echo $e;
            exit;
        }
        $arr=$res->fetchall(PDO::FETCH_NUM);
        //var_dump($arr);
        if(!empty($arr)){
            $permit=0;
            foreach($arr as $row){
                $permit|=$row[0];
            }
            return $permit;
        }
        return false;
    }
    public function getCountries(){
        //Возвращает массив всех стран
        $sql="SELECT name FROM countries";
        try{
            $res=$this->_pdo->query($sql);
        }catch(PDOException $e){die($e);}
        $arr=array();
        while($r=$res->fetch(PDO::FETCH_NUM)[0])$arr[]=$r;
        return $arr;
    }
    public function createUser($user){
        $user->slug=$this->_pdo->quote($user->slug);
        $user->name=$this->_pdo->quote($user->name);
        $user->mail=$this->_pdo->quote($user->mail);
        $user->gender=(bool)$user->gender;
        $user->mobile=$this->_pdo->quote($user->mobile);
        $user->alt_mail=$this->_pdo->quote($user->alt_mail);
        $user->zip=$this->_pdo->quote($user->zip);
        $user->street=$this->_pdo->quote($user->street);
        $user->city=$this->_pdo->quote($user->city);
        $user->country=$this->_pdo->quote($user->country);
        $user->job_title=$this->_pdo->quote($user->job_title);
        $sql="INSERT
                INTO users(
                    slug,
                    name,
                    mail,
                    mobile,
                    zip,
                    street,
                    city,
                    country
                )VALUES(
                    $user->slug,
                    $user->name,
                    $user->mail,
                    $user->mobile,
                    $user->zip,
                    $user->street,
                    $user->city,
                    (SELECT id FROM countries WHERE name=$user->country))";
        try{
            //die('<br>'.$sql);
            $this->_pdo->exec($sql);
        }catch(PDOException $e){die('<br>Исключение '.$e->getCode());}
        return true;
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
        return true;
    }
}