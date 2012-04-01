<?php
namespace Tests;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class ClassLoader extends Unit implements IUnitTest {
	function testToPath(){
		$class = 'ABC\\ClassLoader';
		
		$path = \ClassLoader::toPath($class,false);
		
		$this->assertTrue(is_string($path),'Classloader path is a string');
		$this->assertEqual($path, 'ABC'.DIRECTORY_SEPARATOR.'ClassLoader' ,'Classloader path is transformed correctly');
		
		$class = 'ClassLoader';
		$path = \ClassLoader::toPath($class,true);
		$this->assertTrue(file_exists($path),'Class Loader full path resolves correctly');
	}
	function testToClass(){
		$class = 'ABC\\ClassLoader';
		$path = 'ABC'.DIRECTORY_SEPARATOR.'ClassLoader';
		
		$class2 = \ClassLoader::toClass($path);
	
		$this->assertTrue(is_string($path),'Classloader path is a string');
		$this->assertEqual($class, $class2 ,'Classloader path is transformed correctly');
	}
	
	function testGetNSExpression(){
		$expr = '*';
		$classes = \ClassLoader::getNSExpression($expr);
	
		$this->assertTrue(is_array($classes),'Classloader is an array');
		$this->assertTrue(in_array('ClassLoader',$classes),'Classloader is in array, expr matches');
	}
}