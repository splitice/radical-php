<?php
namespace DDL\Hosts\API\Internal\FSapi;

class MultiContentContainer{
	private $status;
	
	function __construct($status){
		$this->status = $status;
	}
	function hasContent(){
		return true;
	}
	function getContent(){
		return $this->status;
	}
}