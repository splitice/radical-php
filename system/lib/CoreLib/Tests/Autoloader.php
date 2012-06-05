<?php
namespace Tests;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class Autoloader extends Unit implements IUnitTest {
	function testResolve(){
		$class = 'ClassLoader';
		
		$path = \Autoloader::resolve($class);
		
		$this->assertTrue(is_string($path),'Autoloader resolved path is a string');
		$this->assertTrue(file_exists($path),'Autoloader resolved path does not exist');
	}
}