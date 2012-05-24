<?php
namespace CLI\Threading;

abstract class ActiveObject {
	protected $thread;
	function Run($complete = null) {
		$this->thread = new Thread ();
		if ($this->thread->isThis ()) {
			$this->PerformWork ();
			if ($complete === null)
				exit ();
			$complete ( $this );
		}
		return $this->thread;
	}
	abstract function Work();
	static function Start() {
		$rc = new \ReflectionClass ( get_called_class () );
		$object = $rc->newInstanceArgs ( func_get_args () );
		return $object->Run ();
	}
}