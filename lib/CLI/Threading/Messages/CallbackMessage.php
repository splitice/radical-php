<?php
namespace CLI\Threading\Messages;

class CallbackMessage extends MessageBase implements IExecuteMessage {
	private $callback;

	function __construct($callback){
		$this->callback = $callback;
		parent::__construct();
	}

	function Execute(){
		call_user_func($this->callback,$this);
	}
}