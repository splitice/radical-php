<?php
namespace Tests\Basic;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Arr extends Unit implements IUnitTest {
	function testIsAssoc()
	{
		$this->assertTrue(\Basic\Arr::is_assoc(array('abc'=>0,'cde'=>1)),'Assoc Test 1');
		$this->assertTrue(\Basic\Arr::is_assoc(array('abc'=>0,'cde'=>1,2=>3)),'Assoc Test 2');
		$this->assertFalse(\Basic\Arr::is_assoc(array(0,1,2)),'Assoc Test 3');
		$this->assertTrue(\Basic\Arr::is_assoc(array(1,2=>3)),'Assoc Test 4');
	}
} // End arr