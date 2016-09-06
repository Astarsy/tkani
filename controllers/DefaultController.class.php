<?php
class DefaultController{
    // Контроллер поумолчанию, выводит основной контент.
    // Не наследует BaseController, т.к. не нужна проверка прав.
    // Использует собственный провайдер БД- ShopDB
    public function __construct(){
        $this->_db=new ShopDB();
        $this->logger=new Logger();
        $this->basket=new Basket($this->_db);
        $this->search=new Search();
        $this->crumbs=new Crumbs();
        $this->host=$_SERVER['SERVER_NAME'];
    }
    public function Method(){
        // Главная Витрина
        $fc=AppController::getInstance();
        $this->left_menu=new LeftMenu($this->_db);
        $this->news_bar=new NewsBar($this->_db);
        $this->title=$_SERVER['HTTP_HOST'].'-Ткани';
        $this->all_goods=array();
        $this->all_goods['Новые']=new Goods($this->_db);
        $this->all_goods['Рекомендуем обратить внимание']=new Goods($this->_db,'RAND()');
        $fc->setContent($fc->render('default/index.twig.html',array('this'=>$this,)));
    }
    public function groupMethod(){
        // gladkov.loc/group/3
        // отобразить кнопки выбора категории
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        $gid=Globals\clearUInt($args[0]);
        if(!$this->group=$this->_db->getGroupById($gid))exit(header('Location:/error'));
        $this->title=$this->group->name;
        $this->caths=new CathsOfGroup($this->_db,$gid);
        $this->crumbs->setLocation(array(
                array($this->group->name,'/group/'.$this->group->id)));
        $this->left_menu=new LeftMenu($this->_db);
        $this->news_bar=new NewsBar($this->_db); 
        $this->all_goods=array();
        $this->all_goods['Новые '.$this->group->name]=new GoodsOfGroup($this->_db,$gid,0,0,4);
        $this->all_goods['Рекомендуем обратить внимание']=new GoodsOfGroup($this->_db,$gid,3,0,4);
        $this->left_menu->setHere($this->group->name);
        $fc->setContent($fc->render('default/show_group.twig.html',array('this'=>$this,)));
    }
    public function cathMethod(){
        // отобазить все товары в категории
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        $cid=Globals\clearUInt($args[0]);
        if(!$this->cath=$this->_db->getCathById($cid))exit(header('Location:/error'));
        $br='/cath/'.$cid;
        if(isset($args[1])&&isset($args[2]))
            $this->sorter=new Sorter($args[1],$args[2],$this->cath->count,$br);
        else 
            $this->sorter=new Sorter(0,0,$this->cath->count,$br);
        $this->title=$this->cath->name;
        $this->crumbs->setLocation(array(
                array($this->cath->group_name,'/group/'.$this->cath->group_id),
                array($this->cath->name,'/cath/'.$this->cath->id)));
        $this->all_goods=array();
        $this->all_goods['']=new GoodsOfCath(
            $this->_db,
            $cid,
            $this->sorter->getSortOrder(),
            $this->sorter->getCurPage(),
            Globals\GOODS_ON_PAGE);
        $fc->setContent($fc->render('default/show_goods.twig.html',array('this'=>$this,)));
    }
    public function allMethod(){
        // отобразить все товары в Группе
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        $gid=Globals\clearUInt($args[0]);
        if(!$this->group=$this->_db->getGroupById($gid))exit(header('Location:/error'));
        $br='/all/'.$gid;
        if(isset($args[1])&&isset($args[2]))
            $this->sorter=new Sorter($args[1],$args[2],$this->group->count,$br);
        else 
            $this->sorter=new Sorter(0,0,$this->group->count,$br);
        $this->title=$this->group->name;
        $this->crumbs->setLocation(array(
                array($this->group->name,'/group/'.$this->group->id)));
        $this->all_goods=array();
        $this->all_goods['']=new GoodsOfGroup(
            $this->_db,
            $gid,
            $this->sorter->getSortOrder(),
            $this->sorter->getCurPage(),
            Globals\GOODS_ON_PAGE);
        $fc->setContent($fc->render('default/show_goods.twig.html',array('this'=>$this,)));
    }
    public function goodMethod(){
        // gladkov.loc/good/3
        // Большая карта товара
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        if(!$item=$this->_db->getGoodById(Globals\clearUInt($args[0])))exit(header('Location:/error'));
        $this->crumbs->setLocation(array(
                array($item->group_name,'/group/'.$item->group_id),
                array($item->cath,'/cath/'.$item->cath_id),
                array($item->name,'/good/'.$item->id)));
        $fc->setContent($fc->render('default/show_good.twig.html',array('this'=>$this,'item'=>$item,)));
    }
    public function nextMethod(){
        // перенаправить на Больш.карт. следующего товара
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        $id=$this->_db->getNextGoodId(Globals\clearUInt($args[0]));
        exit(header("Location:/good/$id"));
    }
    public function prevMethod(){
        // перенаправить на Больш.карт. предыдущего товара
        $fc=AppController::getInstance();
        $args=$fc->getArgsNum();
        if(!isset($args[0]))exit(header('Location:/error'));
        $id=$this->_db->getPrevGoodId(Globals\clearUInt($args[0]));
        exit(header("Location:/good/$id"));
    }
    public function basketMethod(){
        // gladkov.loc/basket
        $fc=AppController::getInstance();
        $this->crumbs->setLocation(array(
                array('Корзина','')));
        $fc->setContent($fc->render('default/basket.twig.html',array('this'=>$this,)));
    }
    public function errorMethod(){
        // Выводит страницу ошибки. Сюда идет переадресация из многих обработчиков ошибок
        $fc=AppController::getInstance();
        $fc->setContent($fc->render('error.twig.html'));
    }
    public function msgMethod(){
        //выводит сообщение формата:
        //glagkov.loc/msg/title_text/message_text
        $fc=AppController::getInstance();
        if(isset(array_keys($fc->getArgs())[0]))$title=array_keys($fc->getArgs())[0];
        else $title='Сообщение';
        if(isset($fc->getArgs()[$title]))$msg=Msg::decode($fc->getArgs()[$title]);
        else $msg='Отсутствует текст сообщения...';
        $title=Msg::decode($title);
        $fc->setContent($fc->render('msg.twig.html',array(
            'title'=>$title,
            'msg'=>$msg,
            )));   
    }
}