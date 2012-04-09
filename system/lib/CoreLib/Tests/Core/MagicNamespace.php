<?php
namespace Tests\Core;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class MagicNamespace extends Unit implements IUnitTest {
	function testTheory(){
		$mn = new \Core\MagicNamespace('ClassLoader', 'CoreLib');
		
		$this->assertEqual($mn->getMagicClass(), '_\\CoreLib\\ClassLoader','Resolving to the correct path');
		
		$this->assertTrue(class_exists($mn->getMagicClass()),'Autoloading Magic Namespace #1');
		$this->assertTrue(class_exists($mn->getMagicClass()),'Cached Magic Namespace #2');
	}
}