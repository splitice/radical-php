<?php
namespace Tests\Database\SQL\Parts;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class Limit extends Unit implements IUnitTest {
	function testScalar(){
		$limit = new \Model\Database\SQL\Parts\Limit(1,2);
		$this->assertEqual('LIMIT 1,2', (string)$limit,'test 1');
		
		$limit = new \Model\Database\SQL\Parts\Limit(1);
		$this->assertEqual('LIMIT 1', (string)$limit,'test 2');
		
		$limit->number(10);
		$this->assertEqual('LIMIT 10', (string)$limit,'test 3');
		
		$limit->from(2);
		$this->assertEqual('LIMIT 2,10', (string)$limit,'test 4');
	}
	function testArray(){
		$limit = new \Model\Database\SQL\Parts\Limit(array(1,2));
		$this->assertEqual('LIMIT 1,2', (string)$limit,'test 1');
	
		$limit = new \Model\Database\SQL\Parts\Limit(array(1));
		$this->assertEqual('LIMIT 1', (string)$limit,'test 2');
	
		$limit->number(10);
		$this->assertEqual('LIMIT 10', (string)$limit,'test 3');
	
		$limit->from(2);
		$this->assertEqual('LIMIT 2,10', (string)$limit,'test 4');
	}
	function testEmpty(){
		$limit = new \Model\Database\SQL\Parts\Limit();
		$this->assertEqual('', (string)$limit,'test 1');
	}
}