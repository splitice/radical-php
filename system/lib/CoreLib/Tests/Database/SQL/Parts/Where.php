<?php
namespace Tests\Database\SQL\Parts;

use Database\SQL\Parts\Expression\Between;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Where extends Unit implements IUnitTest {
	const PART_NAME = 'WHERE';
	
	protected function _class(){
		return '\\Database\\SQL\\Parts\\Where';
	}
	
	function testAssoc(){
		$class = $this->_class();
		
		$assoc = array('user_id'=>1);
		$where = new $class($assoc);
		$this->assertEqual(static::PART_NAME.' user_id=1', (string)$where,'Associative array test 1');
		
		$assoc = array('user_id'=>'test');
		$where = new $class($assoc);
		$this->assertEqual(static::PART_NAME.' user_id="test"', (string)$where,'Associative array test 2');
		
		$assoc['test'] = 1;
		$this->assertEqual(static::PART_NAME.' user_id="test" AND test=1', (string)$where,'Associative array test 3');
		
		$where = new $class();
		$this->assertEqual('', (string)$where,'Associative array test 4');
		
		$assoc['test'] = 1;
		$this->assertEqual(static::PART_NAME.' test=1', (string)$where,'Associative array test 4');
		
		$assoc = array(array('user_id'=>1),array('post_id'=>2));
		$where = new $class($assoc);
		$this->assertEqual(static::PART_NAME.' user_id=1 AND post_id=2', (string)$where,'Associative array test 5');
		
		$assoc = array('user_id'=>'test');
		$assoc['bet'] = new Between(1, 3);
		$where = new $class($assoc);
		$this->assertEqual(static::PART_NAME.' user_id=1 AND post_id=2 AND bet BETWEEN 1 AND 3', (string)$where,'Associative array test 6');
	}
}