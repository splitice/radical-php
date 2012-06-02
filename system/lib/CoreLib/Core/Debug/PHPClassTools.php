<?php
namespace Debug;

class PHPClassTools {
	protected $tokens;
	protected $alias;
	
	function __construct($file){
		$data = file_get_contents($file);
		$this->tokens = token_get_all($data);
		$this->alias = $this->getAliases();
	}
	function getAliases(){
		$current_class = $current_as = '';
		$is = false;
		$as = false;
		$classes = array();
		foreach($this->tokens as $tt){
			if($as){
				if($tt[0] == T_NS_SEPARATOR || $tt[0] == T_STRING){
					$current_as .= $tt[1];
				}elseif($tt[0] != T_WHITESPACE){
					if($current_class){
						$classes[$current_class] = $current_as;
					}
					$current_as = '';
					$as = false;
				}
			}else{
				if($tt[0] == T_USE){
					$current_class = '';
					$is = true;
				}else if($tt[0] == T_NS_SEPARATOR || $tt[0] == T_STRING){
					$current_class .= $tt[1];
				}elseif($tt[0] == ';' || $tt[0] == T_AS){
					if($is && $current_class){
						$classes[$current_class] = $current_class;
					}
					$is = false;
				}
				if($tt[0] == T_AS){
					$as = true;
				}
			}
		}
		foreach($classes as $class=>$alias){
			$classes[$class] = array_pop(explode('\\',$alias));
		}
		$classes = array_flip($classes);
		return $classes;
	}
	
	function getDependencies(){
		//die(var_dump(array($this->getImplements(),$this->getExtends(),$this->getCalls(),$this->getNew())));
		$interface = $this->getArgumentInterface();
		$extra = array();
		/*foreach($interface as $i){
			$provider = 'interface.'.str_replace('\\','.',ltrim($i,'\\'));
			$temp = \Core\Provider::Find($provider);
			if(!is_array($temp)) $temp = array($temp);
			$extra = array_merge($extra,$temp);
		}*/
		$a = array_unique(array_merge($this->getImplements(),$this->getExtends(),$this->getCalls(),$this->getNew(),$interface,$extra));
		$a = array_filter($a);
		return $a;
	}
	private function relativeNS($class){
		if($class == 'self' || $class == 'static' || $class == 'parent' || $class == '__construct'){
			return;
		}
		if($class{0} == '\\'){
			return $class;
		}
		if($class){
			if(isset($this->alias[$class])){
				return '\\'.ltrim($this->alias[$class],'\\');
			}
			$first = array_shift(explode('\\',$class));
			if(isset($this->alias[$first])){
				return '\\'.ltrim($this->alias[$first],'\\').'\\'.implode('\\',array_slice(explode('\\',$class),1));
			}
			
			$path = ltrim($this->getNamespace()).'\\';
			$path .= $class;
			$path = '\\'.ltrim($path,'\\');
			return $path;
		}
	}
	function getNamespace(){
		$ns = $this->_getPre(T_NAMESPACE,false);
		if(!$ns){
			return '';
		}
		return $ns[0];
	}
	function getImplements(){
		return $this->_getPre(T_IMPLEMENTS);
	}
	function getExtends(){
		return $this->_getPre(T_EXTENDS);
	}
	function getNew(){
		return $this->_getPre(T_NEW);
	}
	function getCalls(){
		$classes = array();
		$current_class = '';
		foreach($this->tokens as $tt){
			if($tt[0] == T_NS_SEPARATOR || $tt[0] == T_STRING){
				$current_class .= $tt[1];
			}elseif($tt[0] == T_DOUBLE_COLON){
				if($current_class){
					$classes[] = $this->relativeNS($current_class);
				}
				$current_class = '';
			}else{
				$current_class = '';
			}
		}
		return $classes;
	}
	private function _getPre($token,$needs_resolve = true){
		$current_class = '';
		$is = false;
		$classes = array();
		foreach($this->tokens as $tt){
			if($tt[0] == $token){
				$current_class = '';
				$is = true;
			}else if($tt[0] == T_NS_SEPARATOR || $tt[0] == T_STRING){
				$current_class .= $tt[1];
			}elseif($tt[0] != T_WHITESPACE){
				if($is && $current_class){
					if($needs_resolve){
						$current_class = $this->relativeNS($current_class);
					}
					$classes[] = $current_class;
					
				}
				$is = false;
				$current_class = '';
			}
		}
		return $classes;
	}
	function getArgumentInterface(){
		$classes = array();
		foreach($this->tokens as $k1=>$tt){
			if($tt[0] == T_FUNCTION){
				foreach(array_slice($this->tokens,$k1,null,true) as $k2=>$tt2){
					if($tt2[0] == '('){
						$args = '';
						foreach(array_slice($this->tokens,$k2+1) as $k2=>$tt2){
							if($tt2[0] == ')')
								break(1);
							if(!isset($tt2[1])){
								$tt2[1] = $tt2[0];
							}
							$args .= $tt2[1];
						}
						$args = explode(',',$args);
						foreach($args as $a){
							$a = explode(' ',trim(array_shift(explode('=',$a))));
							if(count($a) >= 2){
								$classes[$this->relativeNS($a[0])] = true;
							}
						}
						break;
					}
				}
			}
		}
		return array_keys($classes);
	}
}