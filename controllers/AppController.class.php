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

		$c_n=ucfirst($strs[0].'Controller');
		if($strs[0]=='')$this->createDefaults();
		else{
	        if(!class_exists($c_n))$this->createError();
	        else{
	            $rc=new ReflectionClass($c_n);
	            array_shift($strs);
	            if(empty($strs))$this->createError();
	            else{
		            $m_n=$strs[0].'Method';
		            if(!$rc->hasMethod($m_n))$this->createError();
		            else{
		            	$this->_method=$rc->getMethod($strs[0].'Method');
		            	$this->_controller=$rc->newInstance();
		            	array_shift($strs);	            
		            }
		        }
	        }
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
	}

	protected function createDefaults(){
		$rc=new ReflectionClass('DefaultController');
        $this->_controller=$rc->newInstance();
        $this->_method=$rc->getMethod('defaultMethod');
	}

	protected function createError(){
		$rc=new ReflectionClass('DefaultController');
        $this->_controller=$rc->newInstance();
        $this->_method=$rc->getMethod('errorMethod');
	}

	public function render($t_n){
		$loader=new Twig_Loader_Filesystem('templates');
		$twig=new Twig_Environment($loader);
		return $twig->render($t_n);
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