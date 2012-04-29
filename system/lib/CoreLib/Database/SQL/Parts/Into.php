<?php
namespace Database\SQL\Parts;

class Into extends Internal\PartBase {
	private $table;
	
	function __construct($table){
		$this->table = $table;
	}
	
	protected function table($set=null){
		if($set === null){
			return $this->table;
		}
		$this->table = $set;
		return $this;
	}
	
	function toSQL(){
		return 'INTO '.$this->table;
	}
}