<?php
namespace Basic\Arr\Object;

class SortedCollectionObject extends CollectionObject {
	private $function;
	
	function __construct($function){
		$this->function = $function;
	}
	
	function Set($k,$v){
		parent::Set($k,$v);
		usort($this->data, $this->function);
	}
}