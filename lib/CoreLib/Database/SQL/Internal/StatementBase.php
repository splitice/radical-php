<?php
namespace Database\SQL\Internal;

use Database\SQL\IStatement;

abstract class StatementBase implements IStatement {
	function Execute(){
		$sql = $this->toSQL();
		return \DB::Q($sql);
	}
	function Query(){
		return $this->Execute();
	}
	function __toString(){
		return $this->toSQL();
	}
	function mergeTo(IStatement $mergeIn){
		return $mergeIn->_mergeSet(get_object_vars($this));
	}
	function _mergeSet(array $in){
		$a = get_object_vars($this);
		foreach($a as $k=>$v){
			if(isset($in[$k])){
				$this->$k = $in[$k];
			}
		}
		//$this->sql = null;
		return $this;
	}
}