<?php
namespace Database\SQL\Parts;

use Database\IToSQL;

class Where extends Internal\PartBase {
	const SEPPERATOR = 'AND';
	private $parts = array();
	
	function __construct($parts = array()){
		foreach($parts as $k=>$v){
			if(is_array($v)){
				foreach($v as $vk=>$vv){
					$this->Add(array($k,$vk),$vv);
				}
			}else{
				$this->Add($k,$v);
			}
		}
	}
	function Add($key,$part){
		if(!is_array($key)){
			$key = array('',$key);
		}
		$this->parts[$key[0]][$key[1]] = $part;
	}
	function toSQL($where = false){
		if(is_string($this->parts)){
			$sql = '';
			if($where && count($this->parts))
				$sql = 'WHERE ';
			$sql .= $this->parts;
			return $sql;
		}
		
		//Do for array
		$db = \DB::getInstance();
	
		$ret = array();
		foreach($this->parts as $alias=>$p){
			$rr = '';
			if($alias){
				$rr = '`'.$alias.'`.';
			}
			foreach($p as $k=>$v){
				$rri = $rr. '`'.$k.'`';
				if($v instanceof IToSQL){
					$rri .= $v->toSQL();
				}else{
					$rri .= '='.$db->Escape($v);
				}
				$ret[] = $rri;
			}
		}
		
		//Build SQL
		$sql = '';
		if($where)
			$sql = 'WHERE ';
		$sql .= implode(' '.static::SEPPERATOR.' ',$ret);
		
		return $sql;
	}
}