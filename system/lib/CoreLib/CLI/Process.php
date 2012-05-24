<?php
namespace CLI;

use CLI\Threading\Thread;

class Process {
	static function Title($title){
		Thread::current()->setName($title);
	}
}