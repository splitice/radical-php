<?php
namespace Database\SQL\Parts;

use Database\IToSQL;

class Where extends Internal\PartBase {
	private $parts = array();
	
	function __construct($parts){
		$this->parts = $parts;
	}
	function Add($key,$part){
		$this->parts[$key] = $part;
	}
	function toSQL($where = false){
		if(is_string($this->parts)){
			$sql = '';
			if($where)
				$sql = 'WHERE ';
			$sql .= $this->parts;
			return $sql;
		}
		
		//Do for array
		$db = \DB::getInstance();
	
		$ret = array();
		foreach($this->parts as $k=>$v){
			$rr = '`'.$k.'`';
			if($v instanceof IToSQL){
				$rr .= $v->toSQL();
			}else{
				$rr .= '='.$db->Escape($v);
			}
			$ret[] = $rr;
		}
		
		//Build SQL
		$sql = '';
		if($where)
			$sql = 'WHERE ';
		$sql .= implode(' AND ',$ret);
		
		return $sql;
	}
}