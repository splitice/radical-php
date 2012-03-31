<?php
namespace CLI\Threading;

abstract class ThreadedObject {
	private $shm;
	
	function __construct(){
		$this->shm = new SharedMemory();
	}
	
	function __get($name){
		return $this->shm->$name;
	}
	
	function __set($name,$value){
		$this->shm->$name = $value;
	}
}