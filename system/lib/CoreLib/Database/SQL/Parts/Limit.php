<?php
namespace Database\SQL\Parts;

class Limit extends Internal\PartBase {
	private $from;
	private $number;
	
	function __construct($from = null,$number = null){
		if($number === null){
			if(is_array($from)){
				$this->from = isset($from[0])?$from[0]:null;
				$this->number = isset($number[0])?$number[0]:null;
				
				//Argument inverter
				if($this->number === null && $this->from){
					$this->from = $this->number;
					$this->number = null;
				}
			}else{
				$this->number = $from;
			}
		}else{
			$this->from = $from;
			$this->number = $number;
		}
	}
	
	function from($set=null){
		if($set === null){
			return $this->from;
		}
		$this->from = $set;
		return $this;
	}
	
	function number($set=null){
		if($set === null){
			return $this->number;
		}
		$this->number = $set;
		return $this;
	}
	
	
	function toSQL(){
		//Null statement
		if($this->number === null) return;
		
		//Start
		$ret = 'LIMIT ';
		
		//Offset
		if($this->from !== null){
			$ret .= $this->from.', ';
		}
		
		//Limit
		$ret .= $this->number;
		
		return $ret;
	}
}