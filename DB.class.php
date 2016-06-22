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
    public function getPermition($um,$obj){
        //возвращает code если у $un есть права на $obj
        //объекты доступные п-лю gues доступны всем
        //права данного п-ля и guest суммируются
        $subj=$this->_pdo->quote(get_class($obj));
        $um=$this->_pdo->quote($um);
        $sql="SELECT code FROM permitions WHERE user_id IN(SELECT id FROM users WHERE mail IN ($um,''))AND subject_id=(SELECT id FROM subjects WHERE name=$subj);";
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
        $arr[]='';
        while($r=$res->fetch(PDO::FETCH_NUM)[0])$arr[]=$r;
        return $arr;
    }
    public function saveUser($user){
        //Создаёт или обновляет профиль п-ля
        if(empty($this->getUserByMail($user->mail)))return $this->insertUser($user);
        else return $this->updateUser($user);
    }
    protected function insertUser($user){
        //Создаёт профиль п-ля
        try{
            $stmt=$this->_pdo->prepare(
            "INSERT
                INTO users(
                    slug,
                    name,
                    mail,
                    mobile,
                    zip,
                    street,
                    city,
                    country,
                    job_title
                )VALUES(
                    :slug,
                    :name,
                    :mail,
                    :mobile,
                    :zip,
                    :street,
                    :city,
                    (SELECT id FROM countries WHERE name=:country),
                    :job_title)");
            $stmt->bindParam(':slug', $user->slug, PDO::PARAM_STR);
            $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':mobile', $user->mobile, PDO::PARAM_STR);
            $stmt->bindParam(':zip', $user->zip, PDO::PARAM_STR);
            $stmt->bindParam(':street', $user->street, PDO::PARAM_STR);
            $stmt->bindParam(':city', $user->city, PDO::PARAM_STR);
            $stmt->bindParam(':country', $user->country, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':job_title', $user->job_title, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die('<br>Исключение '.$e->getCode().'<br>'.$e);}
        return true;
    }
    protected function updateUser($user){
        //Обновляет профиль п-ля
        try{
            $stmt=$this->_pdo->prepare(
            "UPDATE users SET
                    name=:name,
                    mobile=:mobile,
                    zip=:zip,
                    street=:street,
                    city=:city,
                    country=(SELECT id FROM countries WHERE name=:country),
                    job_title=:job_title
                WHERE mail=:mail");
            $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
            $stmt->bindParam(':mobile', $user->mobile, PDO::PARAM_STR);
            $stmt->bindParam(':zip', $user->zip, PDO::PARAM_STR);
            $stmt->bindParam(':street', $user->street, PDO::PARAM_STR);
            $stmt->bindParam(':city', $user->city, PDO::PARAM_STR);
            $stmt->bindParam(':country', $user->country, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':job_title', $user->job_title, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die('<br>Исключение '.$e->getCode().'<br>'.$e);}
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