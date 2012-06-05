<?php
namespace Tests\Database\SQL\Parts\Expression;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class TableExpresion extends Unit implements IUnitTest {
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