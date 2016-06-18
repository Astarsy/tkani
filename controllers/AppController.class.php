<?php 
class AppController{
	static protected $_instance;
	protected $_content,$_controller,$_method,$_args;

	static public function getInstance(){
		if(!(self::$_instance instanceof self))self::$_instance=new self();
		return self::$_instance;
	}

	protected function __construct(){
		$request=Globals\clearStr($_SERVER['REQUEST_URI']);
        $strs=explode('/',$request);
		array_shift($strs);

		$c_n=isset($strs[0])?ucfirst($strs[0].'Controller'):'DefaultController';
		if(class_exists($c_n)){
			$rc=new ReflectionClass($c_n);
			array_shift($strs);
		}else $rc=new ReflectionClass('DefaultController');
		$m_n=isset($strs[0])?$strs[0].'Method':'Method';
		if($rc->hasMethod($m_n)){
			$this->_method=$rc->getMethod($m_n);
			array_shift($strs);
		}else{
			$rc=new ReflectionClass('DefaultController');
			$this->_method=$rc->getMethod('errorMethod');
		}
		$this->_controller=$rc->newInstance();
        $ks=$vs=array();
        if(!empty($strs[0])){
            for($i=0,$c=count($strs);$i<$c;$i++){
            if($strs[$i]!='')$ks[]=$strs[$i];
            else continue;
            $i++;
            if($i>=count($strs))break;
            $vs[]=$strs[$i];
            }
            if(count($ks)>count($vs))$vs[]='';
            $this->_args=array_combine($ks,$vs);
        }	    
	}

	public function render($t_n,$arg_arr){
		$loader=new Twig_Loader_Filesystem('templates');
		if(Globals\DEBUG)$twig=new Twig_Environment($loader,array('debug'=>true));
		else $twig=new Twig_Environment($loader);
		return $twig->render($t_n,$arg_arr);
	}

	public function getContent(){
		$this->_method->invoke($this->_controller);
		return $this->_content;
	}
	public function setContent($c){
		$this->_content=$c;
	}

    public function getArgs(){
        return $this->_args;
    }
}