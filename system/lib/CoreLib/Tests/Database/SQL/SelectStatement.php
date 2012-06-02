<?php
namespace Tests\Database\SQL;

use Model\Database\SQL as Target;
use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class SelectStatement extends Unit implements IUnitTest {
	function test1Compound(){
		$select = new Target\SelectStatement('table');
		$this->assertEqual('SELECT * FROM table', $select->toSQL(),'FROM test');
		
		$select->where('ab=cd');
		$this->assertEqual('SELECT * FROM table WHERE ab=cd', $select->toSQL(),'WHERE test');
		
		$select->order_by('ef DESC');
		$this->assertEqual('SELECT * FROM table WHERE ab=cd ORDER BY ef DESC', $select->toSQL(),'ORDER BY test');
		
		$select->limit(10, 1);
		$this->assertEqual('SELECT * FROM table WHERE ab=cd ORDER BY ef DESC LIMIT 10,1', $select->toSQL(),'LIMIT test');
	}
	
	function test2Where(){
		
	}
}