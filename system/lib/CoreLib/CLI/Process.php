<?php
namespace CLI;

class Process {
	static function Title($title){
		Thread::$self->setName($title);
	}
}