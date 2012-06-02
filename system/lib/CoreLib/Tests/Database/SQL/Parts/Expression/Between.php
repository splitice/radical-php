<?php
namespace Tests\Database\SQL\Parts\Expression;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Between extends Unit implements IUnitTest {
	function testBasic(){
		$between = new \Model\Database\SQL\Parts\Expression\Between(1,2);
		$this->assertEqual('BETWEEN 1 AND 2', (string)$between);
	}
}