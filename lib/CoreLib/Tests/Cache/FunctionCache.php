<?php
namespace Tests\Cache;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class FunctionCache extends Unit implements IUnitTest {
	public $a = 0;
	private function _testCache(){
		$self = $this;
		return \Cache\FunctionCache::Call($function = function() use($self){
			$self->a++;
			return 'true';
		},100,__FILE__,4,'Memory');
	}
	function testCache(){		
		$cache = \Cache\FunctionCache::Get('Memory');
		$cache->Delete(__FILE__.':4');
		
		$result1 = $this->_testCache();
		$this->assertEqual('true', $result1, 'Function itteration 1 return value');
		$result2 = $this->_testCache();
		$this->assertEqual('true', $result2, 'Function itteration 2 return value');
		
		$this->assertEqual(1, $this->a, 'Function Cached');
	}
}