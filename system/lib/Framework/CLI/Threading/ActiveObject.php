<?php
namespace CLI\Threading;

abstract class ActiveObject {
	protected $thread;
	function run($complete = null) {
		$this->thread = new Thread ();
		if ($this->thread->isThis ()) {
			$this->PerformWork ();
			if ($complete === null)
				exit ();
			$complete ( $this );
		}
		return $this->thread;
	}
	abstract function work();
	static function start() {
		$rc = new \ReflectionClass ( get_called_class () );
		$object = $rc->newInstanceArgs ( func_get_args () );
		return $object->Run ();
	}
}