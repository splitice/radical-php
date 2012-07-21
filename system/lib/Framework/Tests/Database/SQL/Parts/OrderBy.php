<?php
namespace Tests\Database\SQL\Parts;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class OrderBy extends Unit implements IUnitTest {
	function testOrder(){
		$order = new \Model\Database\SQL\Parts\OrderBy(array(1,2));
		$this->assertEqual('ORDER BY 1,2', (string)$order,'test 1');
		
		$order = new \Model\Database\SQL\Parts\OrderBy(1);
		$this->assertEqual('ORDER BY 1', (string)$order,'test 2');
	}
	function testEmpty(){
		$order = new \Model\Database\SQL\Parts\OrderBy();
		$this->assertEqual('', (string)$order,'test 1');
	}
}