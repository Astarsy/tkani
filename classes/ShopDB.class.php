<?php
class ShopDB{
    // Провайдер данных для Главной части.
    public function __construct(){
        $this->_pdo=Globals\getPDOInstance();
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