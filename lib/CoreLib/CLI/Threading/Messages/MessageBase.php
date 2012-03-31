<?php
namespace CLI\Threading\Messages;
use CLI\Thread;

abstract class MessageBase {
	protected $thread;
	
	function __construct(){
		$this->thread = \CLI\Thread::$self->getId();
	}
	function getThreadId(){
		return $this->thread;
	}
	function Send(Thread $thread){
		$thread->communication->Send($this);
		while(!posix_kill($thread->getId(),SIGUSR1)){
			usleep(10);
		}
	}
}