<?php
namespace Tests\Database\SQL\Parts;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Into extends Unit implements IUnitTest {
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