<?php
namespace Tests;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Autoloader extends Unit implements IUnitTest {
	function testResolve(){
		$class = 'ClassLoader';
		
		$path = \Autoloader::$instance->resolve($class);
		
		$this->assertTrue(is_string($path),'Autoloader resolved path is a string');
		$this->assertTrue(file_exists($path),'Autoloader resolved path does not exist');
	}
}