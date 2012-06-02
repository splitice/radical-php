<?php
namespace Database\SQL\Parts\Expression;

use Database\SQL\Parts\Internal;

class TableExpression extends Internal\PartBase {
	private $table;
	private $field;
	
	function __construct($field,$table = null){
		$this->table = $table;
		$this->field = $field;
	}
	
	protected function table($set=null){
		if($set === null){
			return $this->table;
		}
		$this->table = $set;
		return $this;
	}
	
	protected function field($set=null){
		if($set === null){
			return $this->field;
		}
		$this->field = $set;
		return $this;
	}
	
	function toSQL(){
		$ret = '';
		if($this->table !== null) $ret = $this->table.'.';
		$ret .= $this->field;
		return $ret;
	}
}