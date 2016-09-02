<?php
class ShopDB{
    // Провайдер данных для Главной части.
    public function __construct(){
        $this->_pdo=Globals\getPDOInstance();
    }
    public function getNewsBarItems($length=88){
        $sql="SELECT id,news_date,title,LEFT(content,$length)as content FROM news ORDER BY news_date DESC LIMIT 10";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBasketItems($rows){
        //Возвращает массив массивов элементов корзины
        $slugs_arr=array();
        foreach($rows as $row){
            $ids_arr[]=$this->_pdo->quote(Globals\clearStr(explode(':',$row)[0],30));
        }
        $slugs_str=implode(',', $ids_arr);
        $sql="SELECT goods.id,goods.slug,shops.title as shop,caths.name as cath,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN shops ON shops.id=goods.shop_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN caths ON caths.id=goods.cath_id WHERE goods.slug IN($slugs_str)";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCathById($id){
        //Возвращяет cath as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT caths.id,name,fotos.file as foto FROM caths LEFT JOIN fotos ON caths.foto_id=fotos.id WHERE caths.id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getGroupById($id){
        //Возвращяет group as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT groups.id,name,fotos.file as foto FROM groups LEFT JOIN fotos ON groups.foto_id=fotos.id WHERE groups.id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getGoodById($uid){
        //Возвращяет good as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT goods.id,goods.slug,shops.title as shop,caths.id as cath_id,caths.name as cath,groups.id as group_id,groups.name as group_name,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN shops ON shops.id=goods.shop_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN caths ON caths.id=goods.cath_id LEFT JOIN groups ON groups.id=caths.group_id WHERE goods.id=:uid");
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getCathsOfGroup($id){
        // Возвращяет массив объектов категорий в Группе
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT caths.id,name,fotos.file as foto FROM caths LEFT JOIN fotos ON fotos.id=caths.foto_id WHERE caths.group_id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        if(empty($arr=$stmt->fetchAll(PDO::FETCH_OBJ)))return array();
        return $arr;
    }
    public function getGoods($order,$ofset,$limit){
        // Возвращяет массив объектов товаров
        $sql="SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id ORDER BY $order DESC LIMIT $ofset,$limit";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getGoodsOfGroup($gid,$order='d_date',$ofset=0,$limit=4){
        // Возвращяет массив объектов товаров Группы
        $stmt=$this->_pdo->prepare("SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id WHERE goods.cath_id IN(SELECT id FROM caths WHERE group_id=:gid)ORDER BY $order DESC LIMIT $ofset,$limit");
        try{
            $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getGoodsOfCath($cid,$order='d_date',$ofset=0,$limit=4){
        // Возвращяет массив объектов товаров Категории
        $stmt=$this->_pdo->prepare("SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id WHERE goods.cath_id=:cid ORDER BY $order DESC LIMIT $ofset,$limit");
        try{
            $stmt->bindParam(':cid',$cid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getLeftMenuItems(){
        // Возвращает массив эл-в LeftMenu
        $res=$this->getNotEmptyGroups();
        $sql="SELECT DISTINCT cath_id as id,caths.name,group_id,caths.foto_id FROM goods LEFT JOIN caths ON caths.id=goods.cath_id WHERE group_id IS NULL";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return array_merge($res,$stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function getNotEmptyGroups(){
        // Возвращает непустые groups с непустыми caths
        $sql="SELECT DISTINCT group_id as id,groups.name,groups.foto_id FROM caths INNER JOIN groups ON groups.id=caths.group_id WHERE caths.id IN (SELECT cath_id FROM goods)";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getNotEmptyCaths(){
        // Возвращает непустые caths
        $sql="SELECT DISTINCT cath_id as id,caths.name,caths.foto_id FROM goods LEFT JOIN caths ON caths.id=goods.cath_id";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}