<?php
namespace Cast;

class Boolean extends Internal\CastBase implements ICast {
	protected $yes;
	protected $no;
	
	function __construct($yes = null, $no = null){
		$this->yes = $yes;
		$this->no = $no;
	}
	function Cast($value){
		if($this->yes){
			return ($value == $this->yes);
		}
		if($this->no){
			return !($value == $this->no);
		}
		return (bool)$value;
	}
}