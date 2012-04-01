<?php
namespace Tests\Basic\DateTime;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class DateTime extends Timestamp {
	function testToFromSQL(){
		$date = '2012-04-01 00:00:00';
		
		$dt = \Basic\DateTime\DateTime::fromSQL($date);
		$this->assertEqual(strtotime($date),(string)$dt, 'DateTime fromSQL');
		$this->assertEqual($date,$dt->toSQL(), 'DateTime toSQL');
	}
}