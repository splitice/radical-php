<?php
namespace Database;

use Database\Model\TableReferenceInstance;

class API {
	protected $table;
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
	}
	function Select($where){
		$class = $this->table->getClass();
		if(is_array($where)){
			return $class::getAll($where);
		}else{
			return $class::fromId($where);
		}
	}
	function Insert($data){
		$class = $this->table->getClass();
		return $class::create($data,true);
	}
	function Delete($where){
		$obj = $this->_get($where);
		if($obj){
			foreach($obj as $o){
				$o->Delete();
			}
			return true;
		}
		return false;
	}
	private function _isDBArray($where){
		$ret = true;
		foreach($where as $v){
			if(!($v instanceof Model\Table)){
				$ret = false;
			}
		}
		return $ret;
	}
	private function _get($where){
		if($this->_isDBArray($where)){
			$obj = $where;
		}else{
			$obj = $this->Select($where);
		}
		return $obj;
	}
	function Update($where,$set){
		$obj = $this->_get($where);
		if($obj){
			foreach($obj as $o){
				foreach($set as $k=>$v){
					$o->setSQLField($k,$v);
				}
				$o->Update();
			}
			return true;
		}
		return false;
	}
}