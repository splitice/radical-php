<?php
namespace CLI\Threading;

use CLI\Thread;

declare(ticks=1);

class MessageHandler {
	static $init;
	
	function __construct(){
		$tid = getmypid();

		//Only init once
		if(self::$init == $tid) return;
		
		//Store Id as a double init check
		self::$init = $tid;
		
		pcntl_signal(SIGUSR1, array($this,"Tick"), true);
	}
	function Tick(){
		if(Thread::$self){
			foreach(Thread::$self->children as $c){
				$c->communication->HandleReceive();
			}
		}
	}
}