<?php
namespace CLI;

use CLI\Threading\Thread;

class Process {
	static function title($title){
		self::getThread()->setName($title);
	}
	static function getThread(){
		return Threading\Thread::current();
	}
	static function getProcess(){
		
	}
}