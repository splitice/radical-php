<?php
namespace DDL\TitleParse\Scene\Types\Internal;

abstract class SceneBase extends BasicBase {
	const DELIMITER = '.';
	
	protected $group = '';
	protected $internal = false;
	protected $proper = false;
	
	function __construct($string){
		//parent::__construct($string);
		
		$string = trim($string);
		
		$this->rls = $string;
		
		if(strpos($string,' ')){
			$this->isValid(false);
		}else{
			$pos = strrpos($string,'-');
			$this->group = substr($string,$pos+1);
			if(strtolower(substr($this->group,-4))=='_int'){
				$this->internal = true;
				$this->group = substr($this->group,0,-4);
			}
			
			$string = substr($string,0,$pos);
			$this->parts = $this->Split($string);
			
			foreach($this->parts as $k=>$v){
				if($v == 'PROPER'){
					$this->proper = true;
					unset($this->parts[$k]);
					$this->parts = array_values($this->parts);
				}elseif(strtoupper($v) == 'INTERNAL'){
					$this->proper = true;
					unset($this->parts[$k]);
					$this->parts = array_values($this->parts);
				}
			}
			
			$this->Parse();
		}
	}
	protected function Split($string){
		$string = str_replace('_','.',$string);//BAD group
		return parent::Split($string);
	}
	public function isImageset(){
		return false;
	}
}