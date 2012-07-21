<?php
namespace Tests\Basic\DateTime;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class Date extends Timestamp {
	function testToFromSQL(){
		$date = '2012-04-01';
		
		$dt = \Basic\DateTime\Date::fromSQL($date);
		$this->assertEqual(strtotime($date),(string)$dt, 'Date fromSQL');
		$this->assertEqual($date,$dt->toSQL(), 'Date toSQL');
	}
}