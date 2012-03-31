<?php
namespace CLI\Threading\Messages;

class EvalMessage extends MessageBase implements IExecuteMessage {
	private $callback;

	function __construct($callback){
		$this->callback = $callback;
		parent::__construct();
	}

	function Execute(){
		eval($this->callback);
	}
}