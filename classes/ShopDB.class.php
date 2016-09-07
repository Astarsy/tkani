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
    public function getGoodsBySlugs($slugs){
        //Возвращает массив товаров по строке слугов
        $sql="SELECT goods.id,goods.slug,shops.title as shop,caths.name as cath,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN shops ON shops.id=goods.shop_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN caths ON caths.id=goods.cath_id WHERE goods.slug IN($slugs)";
        try{
            $stmt=$this->_pdo->query($sql);
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getCathById($id){
        //Возвращяет cath as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT caths.id,caths.name,fotos.file as foto,groups.id as group_id,groups.name as group_name,(SELECT COUNT(id) FROM goods WHERE cath_id=:id) as count FROM caths LEFT JOIN groups ON caths.group_id=groups.id LEFT JOIN fotos ON caths.foto_id=fotos.id WHERE caths.id=:id");
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){die($e);}
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function getGroupById($id){
        //Возвращяет group as object
        try{
            $stmt=$this->_pdo->prepare(
            "SELECT groups.id,name,fotos.file as foto,(SELECT COUNT(id) FROM goods WHERE cath_id IN(SELECT cath_id FROM caths WHERE group_id=:id)) as count FROM groups LEFT JOIN fotos ON groups.foto_id=fotos.id WHERE groups.id=:id");
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
    public function getGoodsOfGroup($gid,$sort,$page,$count){
        // Возвращяет массив объектов товаров Группы
        $order_str=$this->createSortOrder($sort,$page,$count);
        $stmt=$this->_pdo->prepare("SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id WHERE goods.cath_id IN(SELECT id FROM caths WHERE group_id=:gid) $order_str");
        try{
            $stmt->bindParam(':gid',$gid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getGoodsOfCath($cid,$sort,$page,$count){
        // Возвращяет массив объектов товаров Категории
        $order_str=$this->createSortOrder($sort,$page,$count);
        $stmt=$this->_pdo->prepare("SELECT goods.id,goods.slug,shops.title as shop,cath_id,d_date,goods.name,price,goods.descr,manufs.name as manuf,consist,width,fotos.file as foto FROM goods LEFT JOIN fotos ON fotos.id=goods.main_foto_id LEFT JOIN manufs ON manufs.id=goods.manuf LEFT JOIN shops ON shops.id=goods.shop_id WHERE goods.cath_id=:cid $order_str");
        try{
            $stmt->bindParam(':cid',$cid,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    protected function createSortOrder($sort,$page,$count){
        switch($sort){
            case 1:
            // дешевле
                $by='price';
                $or='ASC';
                break;
            case 2:
            // дороже
                $by='price';
                $or='DESC';
                break;
            case 3:
            // дороже
                $by='RAND()';
                $or='';
                break;
            default:
            // новые
                $by='d_date';
                $or='DESC';
        }
        $of=$page*$count;
        $sql="ORDER BY $by $or LIMIT $of,".$count;
        return $sql;
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
    public function getNextGoodId($id){
        // Возвращяет id следующего в группе или категории товара
        $goods=$this->getGoodsOfCathOrGroupById($id);
        $cur_ind=0;
        foreach($goods as $ind=>$good){
            if($good[0]==$id)$cur_ind=$ind;
        }
        if(isset($goods[$cur_ind+1]))$id=$goods[$cur_ind+1][0];
        else $id=$goods[0][0];
        return $id;
    }
    public function getPrevGoodId($id){
        // Возвращяет id предыдущего в группе или категории товара
        $goods=$this->getGoodsOfCathOrGroupById($id);
        $cur_ind=0;
        foreach($goods as $ind=>$good){
            if($good[0]==$id)$cur_ind=$ind;
        }
        //var_dump($arr);
        if(isset($goods[$cur_ind-1]))$id=$goods[$cur_ind-1][0];
        else $id=$goods[count($goods)-1][0];
        return $id;
    }
    protected function getGoodsOfCathOrGroupById($id){
        // Служ. Выбирает все товары в Категории и Группе
        $stmt=$this->_pdo->prepare("SELECT id,d_date FROM goods WHERE cath_id IN(SELECT id FROM caths WHERE group_id=(SELECT group_id FROM caths WHERE id=(SELECT cath_id FROM goods WHERE id=:id))) UNION SELECT id,d_date FROM goods WHERE id=:id ORDER BY d_date ASC;");
        try{
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
        }catch(PDOException $e){
            die($e);
        }
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }
}