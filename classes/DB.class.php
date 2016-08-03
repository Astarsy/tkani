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
    public function getAllPaymentNames(){
        //Ret all payment methods as numeric array
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id,name FROM payments");
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $res=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM))$res[(int)($r[0])]=$r[1];
        return $res;
    }
    public function getAllOwnerFormNames(){
        //Ret all owner forms as numeric array
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id,name FROM owner_forms");
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $res=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM))$res[(int)($r[0])]=$r[1];
        return $res;
    }
    public function getAllShipingNames(){
        //Ret all ship methods as numeric array
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id,name FROM shipings");
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $res=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM))$res[(int)($r[0])]=$r[1];
        return $res;
    }
    public function getSalerRequestById($id){
        //Ret a saler request as ASSOC_ARRAY
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id,user_id,reg_time,add_payment,add_shiping,reject_reason FROM saler_requests WHERE id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){die($e);}
    }
    public function getAllSalerRequests(){
        //Ret all saler requests as ASSOC_ARRAY
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT saler_requests.id,user_id,users.name,reg_time,add_payment,add_shiping,reject_reason FROM saler_requests LEFT JOIN users ON users.id=user_id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){die($e);}
    }
    public function getUserSalerRequestTime($user){
        //Возвращает время подачи заявки на открытие Магазина у данного П-ля или false
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT reg_time FROM saler_requests WHERE user_id=:u_id");
            $stmt->bindParam(':u_id',$user->id,PDO::PARAM_INT);
            $stmt->execute();
            return (int)($stmt->fetch(PDO::FETCH_NUM)[0]);
        }catch(PDOException $e){die($e);}
    }
    public function getShopsOfUser($user){
        //Возвращяет массив Магазинов П-ля как массивы
        try{
            $stmt=$this->_pdo->prepare("SELECT id,slug,open_time,respons_person,title,logo,owner_form,descr,pub_phone,pub_address,addition_info FROM shops WHERE respons_person=:u_id");
            $stmt->bindParam(':u_id', $user->id, PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getShipingsOfShop($s_id){
        //Returns all Payments of the shop as numeric array
        try{
            $stmt=$this->_pdo->prepare("SELECT name FROM shipings WHERE id IN(SELECT shiping_id FROM shipings_of_shops WHERE shop_id=:s_id)");
            $stmt->bindParam(':s_id',$s_id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $arr=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM)[0])$arr[]=$r;
        return $arr;
    }
    public function getPaymentsOfShop($s_id){
        //Returns all Payments of the shop as numeric array
        try{
            $stmt=$this->_pdo->prepare("SELECT name FROM payments WHERE id IN(SELECT payment_id FROM payments_of_shops WHERE shop_id=:s_id)");
            $stmt->bindParam(':s_id',$s_id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $arr=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM)[0])$arr[]=$r;
        return $arr;
    }
    public function getShopOfUser($s_id,$user){
        //Returns the shop of the user as an array
        try{
            $stmt=$this->_pdo->prepare("SELECT id,slug,open_time,respons_person,title,logo,(SELECT name FROM owner_forms WHERE id=shops.owner_form) as owner_form,descr,pub_phone,pub_address,addition_info FROM shops WHERE respons_person=:u_id AND id=:s_id");
            $stmt->bindParam(':u_id',$user->id,PDO::PARAM_INT);
            $stmt->bindParam(':s_id',$s_id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUnactiveShopOfUser($user){
        //Возвращает неактивный магазин как массив
        try{
            $stmt=$this->_pdo->prepare("SELECT id,slug,open_time,respons_person,title,logo,(SELECT name FROM owner_forms WHERE id=shops.owner_form) as owner_form,descr,pub_phone,pub_address,addition_info FROM shops WHERE respons_person=:u_id AND open_time IS NULL");
            $stmt->bindParam(':u_id',$user['id'],PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // public function getShopsOfUserById($u_id){
    //     //Returns an array of arrays of shops
    //     try{
    //         $stmt=$this->_pdo->prepare("SELECT id,slug,title,logo,respons_person FROM shops WHERE respons_person=:u_id");
    //         $stmt->bindParam(':u_id',$u_id,PDO::PARAM_INT);
    //         $stmt->execute();
    //     }catch(PDOException $e){
    //         die($e);
    //     }
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    public function getUserByMail($mail,$fetch=PDO::FETCH_OBJ){
        // Returms Object of user or false
        //Adds a shops fied, conteints count of shops
        try{
            $stmt=$this->_pdo->prepare("SELECT id,slug,name,mail,alt_mail,gender,mobile,tel,fax,zip,street,city,country,job_title,active,(SELECT COUNT(id) FROM shops WHERE respons_person IN(SELECT id FROM users WHERE mail=:mail) AND open_time IS NOT NULL)as shops_count FROM users WHERE mail=:mail");
            $stmt->bindParam(':mail',$mail,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
            return false;
        }
        return $stmt->fetch($fetch);
    }
    public function getUserByMailFull($mail,$fetch=PDO::FETCH_OBJ){
        // Returms Object of user or false
        //дополнительно заполняет поля внешних ключей
        try{
            $stmt=$this->_pdo->prepare("SELECT users.id,users.slug,users.name,mail,alt_mail,gender,mobile,tel,fax,zip,street,city,countries.name as country,job_title,active FROM users LEFT JOIN countries ON countries.id=users.country WHERE mail=:mail");
            $stmt->bindParam(':mail',$mail,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
            return false;
        }
        return $stmt->fetch($fetch);
    }
    public function getUserByIdFull($id){
        // Returms a user as Assoc array
        //дополнительно заполняет поля внешних ключей
        try{
            $stmt=$this->_pdo->prepare("SELECT users.id,users.slug,users.name,mail,alt_mail,gender,mobile,tel,fax,zip,street,city,countries.name as country,job_title,active FROM users LEFT JOIN countries ON countries.id=users.country WHERE users.id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){die($e);}
    }
    protected function getSubjPermByName($subj){
        //Returns a permition code for subject
        //if there arn't- false
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT code FROM subjects WHERE name=:name");
            $stmt->bindParam(':name',$subj,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $arr=$stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($arr['code']))return (int)($arr['code']);
        else return false;
    }
    protected function isUserExistsById($u_id){
        //Returns true/false if user are registered,
        //excludes a 'guest' user
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id FROM users WHERE id=:id AND name!='guest' AND active=true");
            $stmt->bindParam(':id',$u_id,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return !empty($stmt->fetch(PDO::FETCH_ASSOC));
    }
    protected function isUserSalerById($u_id){
        //Returns true/false if user are saler
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id FROM shops WHERE respons_person=(SELECT id FROM users WHERE id=:id AND active=true)");
            $stmt->bindParam(':id',$u_id,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return !empty($stmt->fetch(PDO::FETCH_ASSOC));
    }
    protected function isUserAdminById($u_id){
        //Returns true/false if user are Admin
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT id FROM admins WHERE user_id=(SELECT id FROM users WHERE id=:id AND active=true)");
            $stmt->bindParam(':id',$u_id,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return !empty($stmt->fetch(PDO::FETCH_ASSOC));
    }
    public function getPermitions($u_id,$subj){
        //возвращает true/false
        $subj_perm=$this->getSubjPermByName($subj);
        if($subj_perm===false)return false;
        if($subj_perm===0)return true;
        if($subj_perm===1&&$this->isUserExistsById($u_id))return true;
        if($subj_perm<=3&&$this->isUserSalerById($u_id))return true;
        if($subj_perm<=7&&$this->isUserAdminById($u_id))return true;
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
    public function getAllSubjects(){
        //Возвращяет массив всех Subjects
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT name FROM subjects");
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $arr=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM)[0])$arr[]=$r;
        return $arr;

    }
    public function saveRegSlugHesh($user,$hesh){
        //Создаёт запись в reg_heshes
        //returns fasle/error
        $r_t=time();
        try{
            $stmt=$this->_pdo->prepare(
            "INSERT INTO reg_heshes(hesh,user_slug,reg_time)
                VALUES(:hesh,:slug,:r_t)");
            $stmt->bindParam(':hesh',$hesh,PDO::PARAM_STR);
            $stmt->bindParam(':slug',$user->slug,PDO::PARAM_INT);
            $stmt->bindParam(':r_t',$r_t,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){return $e;}
    }
    public function saveUser($user){
        //Создаёт или обновляет профиль п-ля
        if(empty($this->getUserByMail($user->mail)))return $this->insertUser($user);
        else return $this->updateUser($user);
    }
    public function insertUser($user){
        //Создаёт профиль п-ля returns true/false
        try{
            $stmt=$this->_pdo->prepare(
            "INSERT
                INTO users(slug,name,mail,alt_mail,mobile,tel,fax,zip,street,city,country,job_title,active)
                VALUES(:slug,:name,:mail,:alt_mail,:mobile,:tel,:fax,:zip,:street,:city,(SELECT id FROM countries WHERE name=:country),:job_title,false)");
            $stmt->bindParam(':slug', $user->slug, PDO::PARAM_STR);
            $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':alt_mail', $user->alt_mail, PDO::PARAM_STR);
            $stmt->bindParam(':mobile', $user->mobile, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $user->tel, PDO::PARAM_STR);
            $stmt->bindParam(':fax', $user->fax, PDO::PARAM_STR);
            $stmt->bindParam(':zip', $user->zip, PDO::PARAM_STR);
            $stmt->bindParam(':street', $user->street, PDO::PARAM_STR);
            $stmt->bindParam(':city', $user->city, PDO::PARAM_STR);
            $stmt->bindParam(':country', $user->country, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':job_title', $user->job_title, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){
            //die('<br>Исключение '.$e->getCode().'<br>'.$e);
            return false;
        }
        return true;
    }
    protected function updateUser($user){
        //Обновляет профиль п-ля returns true/false
        try{
            $stmt=$this->_pdo->prepare(
            "UPDATE users SET
                    name=:name,
                    alt_mail=:alt_mail,
                    mobile=:mobile,
                    tel=:tel,
                    fax=:fax,
                    zip=:zip,
                    street=:street,
                    city=:city,
                    country=(SELECT id FROM countries WHERE name=:country),
                    job_title=:job_title
                WHERE mail=:mail");
            $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
            $stmt->bindParam(':alt_mail', $user->alt_mail, PDO::PARAM_STR);
            $stmt->bindParam(':mobile', $user->mobile, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $user->tel, PDO::PARAM_STR);
            $stmt->bindParam(':fax', $user->fax, PDO::PARAM_STR);
            $stmt->bindParam(':zip', $user->zip, PDO::PARAM_STR);
            $stmt->bindParam(':street', $user->street, PDO::PARAM_STR);
            $stmt->bindParam(':city', $user->city, PDO::PARAM_STR);
            $stmt->bindParam(':country', $user->country, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $user->mail, PDO::PARAM_STR);
            $stmt->bindParam(':job_title', $user->job_title, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die('<br>Исключение '.$e->getCode().'<br>'.$e);return false;}
        return true;
    }
    public function setActiveByMail($mail,$active){
        //Устонавливает флаг актовности по mail
        //возвращает false или сообщение об ошибке
        try{
            $stmt=$this->_pdo->prepare("UPDATE users SET active=:active WHERE mail=:mail");
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->bindParam(':active', $active, PDO::PARAM_BOOL);
            $stmt->execute();
        }catch(PDOException $e){
            return $e;
        }
        return false;
    }
    public function activateUser($hesh,$slug_hesh){
        //Активация п-ля
        //алгоритм проверки:
        //  если есть такой хеш и хеш slug-ов свпадает- Ок
        //возвращает false или сообщение об ошибке
        if(!($user_slug=$this->getUserSlugByRegHesh($hesh)))return 'Пользователь не найден.';
        $saved_slug_hesh=RegistrationDataStorage::getHesh($user_slug,1,1);
        if($saved_slug_hesh!=$slug_hesh)return 'Неверный код активации.';
        try{
            $del_stmt=$this->_pdo->prepare(
            "DELETE FROM reg_heshes WHERE user_slug=:slug");
            $del_stmt->bindParam(':slug', $user_slug, PDO::PARAM_STR);
            $up_stmt=$this->_pdo->prepare("UPDATE users SET active=true WHERE slug=:slug");
            $up_stmt->bindParam(':slug', $user_slug, PDO::PARAM_STR);
            $this->_pdo->beginTransaction();
            $del_stmt->execute();
            $up_stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }
        return false;
    }
    protected function getUserSlugByRegHesh($reg_hesh){
        //возвращает slug по reg_hesh или false
        try{
            $stmt=$this->_pdo->prepare("SELECT user_slug FROM reg_heshes WHERE hesh=:reg_hesh");
            $stmt->bindParam(':reg_hesh', $reg_hesh, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
            return false;
        }
        if($res=$stmt->fetch(PDO::FETCH_OBJ))return $res->user_slug;
        else return false;
    }
    public function getShopByTitle($name){
        //Возвращает в виде массива Магазин или false
        try{
            $stmt=$this->_pdo->prepare("SELECT id,slug,open_time,respons_person,title,logo,owner_form,descr,pub_phone,pub_address,addition_info FROM shops WHERE title=:n");
            $stmt->bindParam(':n', $name, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
            return false;
        }
        $res=$stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($res))return false;
        else return $res;
    }
    public function saveShop($user,$shop){
        //Saves a changes of the shop within the one transaction
        try{
            $this->_pdo->beginTransaction();
    // - update the shop table
            $stmt=$this->_pdo->prepare("UPDATE shops SET title=:t,logo=:l,owner_form=(SELECT id FROM owner_forms WHERE name=:of),descr=:d,pub_phone=:pp,pub_address=:pa,addition_info=:ai WHERE id=:id");
            $stmt->bindParam(':id',$shop->id,PDO::PARAM_INT);
            $stmt->bindParam(':t',$shop->title,PDO::PARAM_STR);
            $stmt->bindParam(':l',$shop->logo,PDO::PARAM_STR);
            $stmt->bindParam(':of',$shop->owner_form,PDO::PARAM_STR);
            $stmt->bindParam(':d',$shop->desc,PDO::PARAM_STR);
            $stmt->bindParam(':pp',$shop->pub_phone,PDO::PARAM_STR);
            $stmt->bindParam(':pa',$shop->pub_address,PDO::PARAM_STR);
            $stmt->bindParam(':ai',$shop->addition_info,PDO::PARAM_STR);
            $stmt->execute();

    // - update the payments_of_shops table
            $arr=array();
            foreach($shop->payment as $pm)$arr[]=$this->_pdo->quote($pm);
            $p_ns=implode(',',$arr);
    //  - delete payments
            $stmt=$this->_pdo->prepare("DELETE FROM payments_of_shops WHERE shop_id=:id");
            $stmt->bindParam(':id',$shop->id,PDO::PARAM_INT);
            $stmt->execute();
    //  - insert payments
            $stmt=$this->_pdo->prepare("INSERT INTO payments_of_shops(shop_id,payment_id)
            SELECT :id,id FROM payments WHERE name IN($p_ns)");
            $stmt->bindParam(':id',$shop->id,PDO::PARAM_INT);
            $stmt->execute();

    // - update the shipings_of_shops table
            $arr=array();
            foreach($shop->shiping as $sm)$arr[]=$this->_pdo->quote($sm);
            $s_ns=implode(',',$arr);
    //  - delete shipings
            $stmt=$this->_pdo->prepare("DELETE FROM shipings_of_shops WHERE shop_id=:id");
            $stmt->bindParam(':id',$shop->id,PDO::PARAM_INT);
            $stmt->execute();
    //  - insert shipings
            $stmt=$this->_pdo->prepare("INSERT INTO shipings_of_shops(shop_id,shiping_id)
            SELECT :id,id FROM shipings WHERE name IN($s_ns)");
            $stmt->bindParam(':id',$shop->id,PDO::PARAM_STR);
            $stmt->execute();

            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            die($e);
        }
        return false;
    }
    public function processSalerRequest($user,$shop){
        //Обрабатывает запрос на создание Магазина, в рамках одной транзакции, Возвращает сообщение с причиной отказа или false
    //1-проверить отсутствие заявок;
        if(0!==$this->getUserSalerRequestTime($user))return 'Заявка подана.';
        if(false!==$this->getShopByTitle($shop->title))return 'Магазин с таким именем уже есть.';
        if(isset($shop->payment['addition']))$p=$shop->payment['addition'];
        else $p=NULL;
        if(isset($shop->shiping['addition']))$s=$shop->shiping['addition'];
        else $s=NULL;
        try{        
    //2-создать запись в Заявках;
            $this->_pdo->beginTransaction();
            $stmt=$this->_pdo->prepare("INSERT INTO saler_requests(user_id,reg_time,add_payment,add_shiping)
            VALUES(:u_id,:t,:p,:s)");
            $stmt->bindParam(':t',(time()),PDO::PARAM_INT);
            $stmt->bindParam(':u_id',$user->id,PDO::PARAM_INT);
            $stmt->bindParam(':p', $p, PDO::PARAM_STR);
            $stmt->bindParam(':s', $s, PDO::PARAM_STR);
            $stmt->execute();

    //3-создать неактивный магазин;
            $stmt=$this->_pdo->prepare("INSERT INTO shops(slug,create_time,open_time,respons_person,title,logo,owner_form,descr,pub_phone,pub_address,addition_info)
            VALUES(:sl,:ct,NULL,:rp,:t,NULL,(SELECT id FROM owner_forms WHERE name=:of),:d,:pp,:pa,:ai)");
            $time=time();
            $sl='sh_'.$time;
            $stmt->bindParam(':sl',$sl,PDO::PARAM_STR);
            $stmt->bindParam(':ct',$time,PDO::PARAM_INT);
            $stmt->bindParam(':rp',$user->id,PDO::PARAM_STR);
            $stmt->bindParam(':t',$shop->title,PDO::PARAM_STR);
            $stmt->bindParam(':of',$shop->owner_form,PDO::PARAM_STR);
            $stmt->bindParam(':d',$shop->desc,PDO::PARAM_STR);
            $stmt->bindParam(':pp',$shop->pub_phone,PDO::PARAM_STR);
            $stmt->bindParam(':pa',$shop->pub_address,PDO::PARAM_STR);
            $stmt->bindParam(':ai',$shop->addition_info,PDO::PARAM_STR);
            $stmt->execute();

    //4-создать записи о сп-ах оплаты
            $arr=array();
            foreach($shop->payment as $pm)$arr[]=$this->_pdo->quote($pm);
            $p_ns=implode(',',$arr);
            $stmt=$this->_pdo->prepare("INSERT INTO payments_of_shops(shop_id,payment_id)
            SELECT (SELECT id FROM shops WHERE slug=:s_sl),id FROM payments WHERE name IN($p_ns)");
            $stmt->bindParam(':s_sl',$sl,PDO::PARAM_STR);
            $stmt->execute();

    //5-создать записи о сп-ах доставки
            $arr=array();
            foreach($shop->shiping as $sm)$arr[]=$this->_pdo->quote($sm);
            $s_ns=implode(',',$arr);
            $stmt=$this->_pdo->prepare("INSERT INTO shipings_of_shops(shop_id,shiping_id)
            SELECT (SELECT id FROM shops WHERE slug=:s_sl),id FROM shipings WHERE name IN($s_ns)");
            $stmt->bindParam(':s_sl',$sl,PDO::PARAM_STR);
            $stmt->execute();

            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }
        return false;
    }
    public function rejectSalerRequestById($id,$reason){
        //Отклоняет заявку по причине, Удаляет Магазин
        //Возвращает текст ошибки/false
        try{
            //Пометить заявку Указанием причины отклонения
            $this->_pdo->beginTransaction();
            $stmt=$this->_pdo->prepare("UPDATE saler_requests SET reject_reason=:reason WHERE id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->bindParam(':reason',$reason,PDO::PARAM_STR);
            $stmt->execute();
            //Удалить Магазин
            $stmt=$this->_pdo->prepare("DELETE FROM shops WHERE respons_person=(SELECT user_id FROM saler_requests WHERE id=:id)");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }
        return false;
    }
    public function confirmSalerRequest($r_id){
        //Одобрение заявки на открытие магазина.
        //Возвращает текст ошибки/false
        try{
            $this->_pdo->beginTransaction();
            //Открыть Магазин
            $stmt=$this->_pdo->prepare("UPDATE shops SET open_time=:t WHERE respons_person=(SELECT user_id FROM saler_requests WHERE id=:r_id)");
            $stmt->bindParam(':t',(time()),PDO::PARAM_INT);
            $stmt->bindParam(':r_id',$r_id,PDO::PARAM_INT);
            $stmt->execute();
            //Удалить заявку
            $stmt=$this->_pdo->prepare("DELETE FROM saler_requests WHERE id=:r_id");
            $stmt->bindParam(':r_id',$r_id,PDO::PARAM_INT);
            $stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }
        return false;
    }

// Товарная часть приложения

    public function getGoodsOfShopOfUserById($uid){
        // Returns array of goods of the user
        try{
            $stmt=$this->_pdo->prepare("SELECT goods.id,slug,shop_id,d_date,goods.name,price,descr,options.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN options ON options.id=manuf WHERE shop_id=(SELECT id FROM shops WHERE respons_person=:uid)");
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function deleteGoodOfUserShop($gid,$uid){
        // Deletes thr good by id for user by id
        // Returns NULL/error
        try{
            $this->_pdo->beginTransaction();
            // delete foto
            // $stmt=$this->_pdo->prepare("DELETE FROM fotos WHERE id=(SELECT main_foto_id FROM goods WHERE id=:gid AND shop_id=(SELECT id FROM shops WHERE respons_person=:uid))");
            // $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            // $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            // $stmt->execute();
            // delete the good
            $stmt=$this->_pdo->prepare(
            "DELETE FROM goods WHERE id=:gid AND shop_id=(SELECT id FROM shops WHERE respons_person=:uid)");
            $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }        
    }
    public function createGood($form,$u_id){
        // Creates new good from form data, returns a slug of the good or false if failure
        $slug='g_'.time();
        try{
            $this->_pdo->beginTransaction();
            // insert new foto if it'd uploaded
            if(NULL!==$f_n=$form->getFieldValue('foto')){
                $stmt=$this->_pdo->prepare("INSERT INTO fotos SET file=:f");
                $stmt->bindParam(':f',$form->getFieldValue('foto'),PDO::PARAM_STR);
                $stmt->execute();
            }
            // insert new good
            $stmt=$this->_pdo->prepare(
            "INSERT INTO goods(slug,shop_id,d_date,name,price,descr,manuf,consist,width,main_foto_id) VALUES(:s,(SELECT id FROM shops WHERE respons_person=:uid),:d,:n,:p,:de,(SELECT id FROM options WHERE name=:m),:c,:w,(SELECT id FROM fotos WHERE file=:f))");
            $stmt->bindParam(':s',$slug,PDO::PARAM_STR);
            $stmt->bindParam(':uid',$u_id,PDO::PARAM_INT);
            $now=time();
            $stmt->bindParam(':d',$now,PDO::PARAM_INT);
            $n=$form->getFieldValue('name');
            $stmt->bindParam(':n',$n,PDO::PARAM_STR);
            $p=$form->getFieldValue('price');
            $stmt->bindParam(':p',$p,PDO::PARAM_INT);
            $de=$form->getFieldValue('descr');
            $stmt->bindParam(':de',$de,PDO::PARAM_STR);
            $m=$form->getFieldValue('manuf');
            $stmt->bindParam(':m',$m,PDO::PARAM_STR);
            $c=$form->getFieldValue('consist');
            $stmt->bindParam(':c',$c,PDO::PARAM_STR);
            $w=$form->getFieldValue('width');
            $stmt->bindParam(':w',$w,PDO::PARAM_INT);
            $f=$form->getFieldValue('foto');
            $stmt->bindParam(':f',$f,PDO::PARAM_STR);
            $stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return false;
        }
        return $slug;
    }
    public function saveGood($form,$uid,$gid){
        // Saves changes for good to the DB
        // Returns NULL/error
        try{
            $this->_pdo->beginTransaction();
            // update foto
            $stmt=$this->_pdo->prepare("UPDATE fotos SET file=:f WHERE id=(SELECT main_foto_id FROM goods WHERE id=:gid AND shop_id=(SELECT id FROM shops WHERE respons_person=:uid))");
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            $fn=$form->getFieldValue('foto');
            $stmt->bindParam(':f',$fn,PDO::PARAM_STR);
            $stmt->execute();
            // update the good
            $stmt=$this->_pdo->prepare(
            "UPDATE goods SET name=:n,price=:p,descr=:de,manuf=(SELECT id FROM options WHERE name=:m),consist=:c,width=:w,main_foto_id=(SELECT id FROM fotos WHERE file=:f) WHERE id=:gid AND shop_id=(SELECT id FROM shops WHERE respons_person=:uid)");
            $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $n=$form->getFieldValue('name');
            $stmt->bindParam(':n',$n,PDO::PARAM_STR);
            $p=$form->getFieldValue('price');
            $stmt->bindParam(':p',$p,PDO::PARAM_INT);
            $de=$form->getFieldValue('descr');
            $stmt->bindParam(':de',$de,PDO::PARAM_STR);
            $m=$form->getFieldValue('manuf');
            $stmt->bindParam(':m',$m,PDO::PARAM_STR);
            $c=$form->getFieldValue('consist');
            $stmt->bindParam(':c',$c,PDO::PARAM_STR);
            $w=$form->getFieldValue('width');
            $stmt->bindParam(':w',$w,PDO::PARAM_INT);
            $f=$form->getFieldValue('foto');
            $stmt->bindParam(':f',$f,PDO::PARAM_STR);
            $stmt->execute();
            $this->_pdo->commit();
        }catch(PDOException $e){
            $this->_pdo->rollBack();
            return $e;
        }
    }
    public function getGoodBySlug($slug){
        //Возвращяет good as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT goods.id,slug,shop_id,d_date,goods.name,price,descr,options.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN options ON options.id=goods.manuf LEFT JOIN fotos ON fotos.id=goods.main_foto_id WHERE slug=:s");
            $stmt->bindParam(':s',$slug,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getFieldsOfForm($name){
        //returns fields for form as array of objects
        try{
            $stmt=$this->_pdo->prepare("SELECT type,name,title,required FROM fields WHERE form=(SELECT id FROM forms WHERE name=:n)");
            $stmt->bindParam(':n',$name,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    protected function getOptionsOfField($name){
        //returns options-items for the field as array
        try{
            $stmt=$this->_pdo->prepare("SELECT name FROM options WHERE field=(SELECT id FROM fields WHERE name=:n)");
            $stmt->bindParam(':n',$name,PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        $res=array();
        while($r=$stmt->fetch(PDO::FETCH_NUM))$res[]=$r[0];
        return $res;
    }
    public function fieldFactory($template){
        //Returns instance of an apropriate Field object
        $c_n=ucfirst($template->name).'Field';
        if(!class_exists($c_n)){
            $c_n=ucfirst($template->type).'Field';
            if(!class_exists($c_n))die('Нет класса поля '.$c_n);
        }
        $rc=new ReflectionClass($c_n);
        $field=$rc->newInstance($template);
        $field->options=$this->getOptionsOfField($template->name);
        return $field;
    }
    public function formFactory($name){
        // Factory of forms. Returns instance of Form
        if(!class_exists($name))die('Нет класса формы '.$name);
        $rc=new ReflectionClass($name);
        $form=$rc->newInstance($this);
        return $form;
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