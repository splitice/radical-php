<?php
namespace Tests\Basic\DateTime;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Timestamp extends Unit implements IUnitTest {
	private $standard_formats = array(
		"F j, Y, g:i a",                 // March 10, 2001, 5:16 pm
		"m.d.y",                         // 03.10.01
		"j, n, Y",                       // 10, 3, 2001
		"Ymd",                           // 20010310
		'h-i-s, j-m-y, it is w Day',     // 05-16-18, 10-03-01, 1631 1618 6 Satpm01
		'\i\t \i\s \t\h\e jS \d\a\y.',   // it is the 10th day.
		"D M j G:i:s T Y",               // Sat Mar 10 17:16:18 MST 2001
		'H:m:s \m \i\s\ \m\o\n\t\h',     // 17:03:18 m is month
		"H:i:s"                         // 17:16:18
	);
	function testFormat()
	{
		$time = time();
		$dt = new \Basic\DateTime\Timestamp($time);

		foreach($this->standard_formats as $format){
			$this->assertEqual(date($format,$time), $dt->toFormat($format), 'Format "'.$format.'" matched');
		}
	}
	
	function testAgo()
	{
		$dt = new \Basic\DateTime\Timestamp(time());
		$this->assertEqual('0 seconds ago',$dt->toAgo(),'Ago Test Now');
		
		$dt = new \Basic\DateTime\Timestamp(time()-1);
		$this->assertEqual('1 second ago',$dt->toAgo(),'Ago 1s Test');
		
		$dt = new \Basic\DateTime\Timestamp(time()-2);
		$this->assertEqual('2 seconds ago',$dt->toAgo(),'Ago 2s Test');
		
		$dt = new \Basic\DateTime\Timestamp(time()-11);
		$this->assertEqual('11 seconds ago',$dt->toAgo(),'Ago 11s Test');
		
		$dt = new \Basic\DateTime\Timestamp(time()-11000);
		$this->assertEqual('3 hours ago',$dt->toAgo(),'Ago 3h Test');
	}
}