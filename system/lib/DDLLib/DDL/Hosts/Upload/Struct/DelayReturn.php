<?php
namespace DDL\Hosts\Upload\Struct;

class DelayReturn {
	public $multi;
	public $page;
	private $time;
	
	function __construct($page,$multi,$delay=10){
		$this->multi = $multi;
		$this->page = $page;
		$this->time = time()+$delay;
	}
	
	function isTime(){
		if($this->time <= time()){
			return true;
		}
		return false;
	}
	function Call(){
		$this->multi->Callback($this->page);
	}
}