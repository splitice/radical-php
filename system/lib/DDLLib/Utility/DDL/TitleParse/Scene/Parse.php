<?php
namespace DDL\TitleParse\Scene;

class Parse {
	static function NS(){
		//Build namespace
		$ns = explode('\\',__CLASS__);
		array_pop($ns);
		$ns[] = 'Types';
		$ns = implode('\\',$ns);
		return $ns;
	}
	static function Load($s,$rls){
		$s = str_replace(array('/','\\'),'_',$s);
		$s = strtoupper($s);
		
		$ns = self::NS();
		
		$class = $ns.'\\'.$s;
		
		return new $class($rls);
	}
	static function Classes(){
		$ret = array();
		$ns = self::NS();
		foreach(glob(__DIR__.DIRECTORY_SEPARATOR.'Types'.DIRECTORY_SEPARATOR.'*.php') as $file){
			$class = basename($file,'.php');
			$class = $ns.'\\'.$class;
			$ret[] = $class;
		}
		return $ret;
	}
	/*
	 * Returns false - Did not match expression
	 * Returns true - Matched expression
	 */
	static function ExprMatch($type,$class){
		if(is_string($type)){
			$class = ltrim($class,'_');
			
			if($class == $type){//Direct match [Case sensitive]
				return true;
			}
			if($type{strlen($type)-1} == '_'){//Partial Check
				if(strtoupper(substr($class,0,strlen($type))) == strtoupper($type)){
					return true;
				}
			}
			if(ctype_lower($type)){//is native type
				if($class::getNType() == $type){
					return true;
				}
			}
		}
		return false;
	}
	static function ParseRelease($release,$type = false){
		//Itterate and check
		foreach(static::Classes() as $class){
			$type_temp = $type;
			if($type_temp == false || $type_temp = static::ExprMatch($type,$class)){
				if(call_user_func(array($class,'is'),$release)){
					return new $class($release);
				}
			}
		}
	}
}