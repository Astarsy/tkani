<?php
class ShopDB{
    // Провайдер данных для Главной части.
    public function __construct(){
        $this->_pdo=Globals\getPDOInstance();
    }
    public function getGoodById($uid){
        //Возвращяет good as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT goods.id,goods.slug,shops.title as shop,caths.name as cath,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN shops ON shops.id=goods.shop_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN caths ON caths.id=goods.cath_id WHERE goods.id=:uid");
            $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getGoods($order='d_date',$ofset=0,$limit=4){
        // Возвращяет массив объектов новых товаров
        $sql="SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id ORDER BY $order DESC LIMIT $ofset,$limit";
        try{
            $stmt=$this->_pdo->query($sql);
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