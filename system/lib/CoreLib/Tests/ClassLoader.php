<?php
namespace Tests;

use Core\Debug\Test\IUnitTest;
use Core\Debug\Test\Unit;

class ClassLoader extends Unit implements IUnitTest {
	function testToPath(){
		$class = 'ABC\\ClassLoader';
		
		$path = \Core\Libraries::toPath($class,false);
		
		$this->assertTrue(is_string($path),'Classloader path is a string');
		$this->assertEqual($path, 'ABC'.DIRECTORY_SEPARATOR.'ClassLoader' ,'Classloader path is transformed correctly');
		
		$class = 'ClassLoader';
		$path = \Core\Libraries::toPath($class,true);
		$this->assertTrue(file_exists($path),'Class Loader full path resolves correctly');
	}
	function testToClass(){
		$class = 'ABC\\ClassLoader';
		$path = 'ABC'.DIRECTORY_SEPARATOR.'ClassLoader';
		
		$class2 = \Core\Libraries::toClass($path);
	
		$this->assertTrue(is_string($path),'Classloader path is a string');
		$this->assertEqual($class, $class2 ,'Classloader path is transformed correctly');
	}
	
	function testGetNSExpression(){
		$expr = '*';
		$classes = \Core\Libraries::getNSExpression($expr);
	
		$this->assertTrue(is_array($classes),'Classloader is an array');
		$this->assertTrue(in_array('ClassLoader',$classes),'Classloader is in array, expr matches');
	}
}