<?php
namespace CLI\Threading\Messages;
use CLI\Thread;

class Output extends MessageBase implements IExecuteMessage {
	private $string;

	function __construct($string){
		$this->string = $string;
		parent::__construct();
	}

	function Execute(){
		echo $this->string;
		if(ob_get_level()) ob_flush();
	}
	
	static function obHandler($message){
		if(!empty($message)){
			$t = new static($message);
			$t->Send(Thread::$self->parent);
		}
		return '';
	}
}