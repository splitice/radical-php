<?php
namespace Database\SQL\Parts;

class Between extends Internal\PartBase {
	private $a;
	private $b;
	
	function __construct($a,$b){
		$this->a = $a;
		$this->b = $b;
	}
	
	function toSQL(){
		$db = \DB::getInstance();
		return ' BETWEEN '.$db->Escape($this->a).' AND '.$db->Escape($this->b);
	}
}