<?php
namespace CLI;

use CLI\Threading\Thread;

class Process {
	static function Title($title){
		self::getThread()->setName($title);
	}
	static function getThread(){
		return Threading\Thread::current();
	}
	static function getProcess(){
		
	}
}